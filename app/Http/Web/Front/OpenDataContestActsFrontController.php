<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\ContestsActsModel;
use Model\SectionsFoModel;
use System\Arr;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i Bandi di gara e contratti
 */
class OpenDataContestActsFrontController extends BaseFrontController
{
    private array $validation;

    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');

        // Chiamata validatore dai parametri di input valido per tutta la classe.
        $this->validation = (new OpenDataDate())->validateDateAndDiff();

        if (!$this->validation['is_success']) {

            $response = new Response();
            $response->setHeader('Content-Type', 'application/json');
            $response->setStatus($response::BAD);
            $response->body($this->validation['errors']);
            echo $response->send(true);
            die();

        } else {

            header('Cache-Control: max-age=60, must-revalidate');
            header('Set-Cookie: fileDownload=true; path=/');

        }

    }

    /**
     * Metodo download open data per i Bandi di gara,
     * nella sezione Pagamenti dell'amministrazione->Dati sui pagamenti->Pagamenti di Bandi di gara e contratti
     * ID sezione 157
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPaymentsContestsActs(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = ContestsActsModel::select(['object_contests_acts.*'])
            ->where('object_contests_acts.typology', '=', 'liquidation')
            ->where('object_contests_acts.amount_liquidated', '!=', '')
            ->orWhereNotNull('object_contests_acts.amount_liquidated')
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object', 'cig']);
                $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_procedure_id', 'relative_notice_id', 'object_structures_id']);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
                $query->with(['relative_notice' => function ($query) {
                    $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_notice_id', 'object_structures_id']);
                    $query->with(['structure' => function ($query) {
                        $query->select(['id', 'structure_name']);
                    }]);
                }]);
            }])
            ->with('relative_procedure_awardees:id,name')
            ->with('attachs')
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $fileName = 'Bandi di gara e contratti - ' . $resultSection['name'] . '.csv';
            header('Set-Cookie: fileDownload=true; path=/');
            header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
            header('Content-Type: text/csv; charset= utf-8');
            header('Content-Disposition: attachment; filename="' . $fileName.'"');
            $output = fopen("php://output", 'w');


            $headers = array(
                'Tipo',
                'Struttura organizzativa proponente',
                'Valore Importo liquidato',
                'Data di pubblicazione',
                'Codice CIG',
                'Bando di gara relativo (se il presente è avviso o esito)',
                'Oggetto',
                'Maggiori Dettagli',
                'Aggiudicatari della gara',
                'url origine ' . $resultSection['name'],
                'Allegati'
            );
            fputcsv($output, $headers, ';', '"');

            // Creazione delle celle per i dati in formato CSV
            foreach ($resultContests as $r) {

                $tmpStructure = '';
                if (!empty($r['relative_procedure']['structure']) || !empty($r['relative_procedure']['relative_notice']['structure'])) {
                    $tmpStructure = !empty($r['relative_procedure']['structure'])
                        ? $r['relative_procedure']['structure']
                        : $r['relative_procedure']['relative_notice']['structure'];
                }
                $tmpCig = '';
                if (!empty($r['relative_procedure']['cig']) || !empty($r['relative_procedure']['relative_notice']['cig'])) {
                    $tmpCig = !empty($r['relative_procedure']['cig'])
                        ? $r['relative_procedure']['cig']
                        : $r['relative_procedure']['relative_notice']['cig'];
                }
                $tmpRelativeProcedure = '';
                if (!empty($r['relative_procedure']) || !empty($r['relative_notice'])) {
                    $tmpRelativeProcedure = !empty($r['relative_procedure'])
                        ? $r['relative_procedure']
                        : $r['relative_notice'];
                }

                // Concateno gli allegati
                $awardeesLists = '';
                if (!empty($r['relative_procedure_awardees'])) {

                    foreach ($r['relative_procedure_awardees'] as $awardee) {

                        $awardeesLists .= !empty($awardee['name'])  ? $awardee['name'] : 'N.D.';
                        $awardeesLists .= "\n";

                    }

                }

                //Concateno gli allegati
                $attachLists = '';
                if (!empty($r['attachs'])) {

                    foreach ($r['attachs'] as $attach) {

                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";

                    }
                }

                // Genero l'indice dell'array
                $data = [
                    // Tipo
                    !empty($r['type']) ? $r['type'] : null,
                    // Struttura organizzativa proponente
                    !empty($tmpStructure) ? $tmpStructure['structure_name'] : null,
                    // Valore importo liquidato
                    !empty($r['amount_liquidated']) ? '€ ' . S::currency($r['amount_liquidated'], 2, ',', '.') : null,
                    // Data di pubblicazione
                    !empty($r['activation_date']) ? date('d-m-Y', strtotime($r['activation_date'])) : null,
                    // Codice cig
                    !empty($tmpCig) ? $tmpCig : null,
                    // Bando relativo
                    !empty($tmpRelativeProcedure) ? $tmpRelativeProcedure['object'] : null,
                    // Oggetto
                    !empty($r['object']) ? $r['object'] : null,
                    // Maggiori Dettagli
                    !empty($r['details']) ? S::chartsEntityDecode(S::stripTags($r['details'])) : null,
                    // Aggiudicatari
                    !empty($awardeesLists) ? trim($awardeesLists) : null,
                    // Url origine dato
                    siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                    // Allegati
                    trim($attachLists)
                ];
                fputcsv($output, $data, ';', '"');
            }
            fclose($output);
        }
    }

    /**
     * Metodo download open data per i Bandi di gara,
     * nella sezione Bandi di gara e contratti->Dati previsti dall'articolo 1, comma 32, della legge 6 novembre 2012, n. 190. Informazioni sulle singole procedure
     * ID sezione 110
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexIndividualProceduresTabularFormat(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = ContestsActsModel::select(['object_contests_acts.*'])
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                    ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                    ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                    ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
                })
            ->with('structure:id,structure_name')
            ->with('contraent_choice:id,name')
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'expiration_date', 'relative_notice_id', 'contraent_choice', 'object_structures_id']);
                $query->with('structure:id,structure_name');
                $query->with('contraent_choice:id,name');
            }])
            ->with('relative_liquidation:relative_procedure_id,id,object,anac_year,amount_liquidated')
            ->with('relative_lots:relative_notice_id,id,object,asta_base_value,cig')
            ->with('requirement:id,denomination,code')
            ->with('proceedings:id,object')
            ->with('participants:id,name')
            ->with('awardees:id,name')
            ->with('attachs')
            ->orderBy('activation_date', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Bandi di gara,
     * nella sezione Bandi di gara e contratti->Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari
     * distintamente per ogni procedura
     * ID sezione 112
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexActsOfContractingAuthorities(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = ContestsActsModel::select(['object_contests_acts.*'])
            ->where('object_contests_acts.typology', '!=', 'liquidation')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('tomorrow')))
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['contraent_choice' => function ($query) {
                $query->select(['id', 'name']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'expiration_date', 'relative_notice_id', 'contraent_choice', 'object_structures_id']);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
            }])
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object', 'asta_base_value', 'cig']);
            }])
            ->with('requirement:id,denomination,code')
            ->with('proceedings:id,object')
            ->with('participants:id,name')
            ->with('awardees:id,name')
            ->with('attachs')
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Bandi di gara
     * ID sezione 257
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPreinformationNotices(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        $currentPageId = 257;

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $queryContents = ContestsActsModel::select(['object_contests_acts.*'])
            ->where('object_contests_acts.typology', 'alert')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with('attachs')
            ->orderBy('object_contests_acts.expiration_date', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection, true);
        }
    }

    /**
     * Metodo download open data per i bandi di gara,
     * nella sezione Atti relativi alle procedure ... -> Avvisi e Bandi
     * ID sezione 524
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexNoticesAndAdvertisements(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId(524, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i bandi di gara,
     * nella sezione Atti relativi alle procedure ... -> Concessioni e partenariato pubblico privato
     * ID sezione 525
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexConcessionsAndPartnership(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId(525, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i bandi di gara,
     * nella sezione Atti relativi alle procedure ... -> Procedure negoziate afferenti agli investimenti pubblici finanziati, in tutto o in parte,
     * con le risorse previste dal PNRR e dal PNC e dai programmi cofinanziati dai fondi strutturali dell'Unione europea",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 526
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPnrAndPncAndEuropeanFinancing(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId(526);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Metodo chiamato per la pagina "Affidamenti diretti di lavori, servizi e forniture di somma urgenza e di protezione civile",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 532
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexDirectFoster(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId(532, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Metodo chiamato per la pagina "Affidamenti in house",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 533
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexInHouseContracting(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId(533, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Metodo chiamato per la pagina "Delibera a contrarre",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 528
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexDeliberation(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId(528, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Metodo che restituisce gli elementi in base al pubblica in,
     *
     * @param null  $currentPageId {id della sezione fo}
     * @param array $typology      {tipo di elementi da restituire(solo se ha pubblica_in)}
     * @return mixed
     */
    private function getDataFromPageId($currentPageId = null, bool $typology = false): mixed
    {
        $currentPageId = (int)$currentPageId;
        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::select(['object_contests_acts.*'])
            ->where(function ($query) use ($currentPageId) {
                if (in_array($currentPageId, [525, 526])) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                } elseif ($currentPageId == 532) {
                    $query->where('object_contests_acts.decree_163' , 1)
                    ->where(function ($query) {
                        $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                            ->orWhereNull('object_contests_acts.expiration_date');
                    });
                } elseif ($currentPageId == 533) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date')
                        ->whereHas('contraent_choice', function ($query) {
                            $query->where('id', '=', 14);
                        });

                }
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(function ($query) use ($currentPageId, $typology) {
                if(!in_array($currentPageId, [532,533,528])){
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    });
                }
                if ($typology) {
                    if(in_array($currentPageId, [524, 525])){
                        $query->orWhere(function ($query) {
                            $query->where('object_contests_acts.typology', 'alert')
                                ->orWhere('object_contests_acts.typology', 'notice');
                        });
                    }elseif(in_array($currentPageId, [532,533])){
                        $query->where('object_contests_acts.typology', 'foster');
                    }elseif ($currentPageId == 528){
                        $query->where('object_contests_acts.typology', 'deliberation');
                    }
                }
            })
            ->with('structure:id,structure_name')
            ->with('contraent_choice:id,name')
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'expiration_date', 'relative_notice_id', 'contraent_choice', 'object_structures_id']);
                $query->with('structure:id,structure_name');
                $query->with('contraent_choice:id,name');
            }])
            ->with('relative_liquidation:relative_procedure_id,id,object,anac_year,amount_liquidated')
            ->with('relative_lots:relative_notice_id,id,object,asta_base_value,cig')
            ->with('requirement:id,denomination,code')
            ->with('proceedings:id,object')
            ->with('participants:id,name')
            ->with('awardees:id,name')
            ->with('attachs');

        if(in_array($currentPageId, [524])){
            $contestActs->orderBy('object_contests_acts.activation_date', 'DESC')
                ->groupBy('object_contests_acts.id');
        }elseif (in_array($currentPageId, [525,526])){
            $contestActs->groupBy('object_contests_acts.id');
        }elseif (in_array($currentPageId, [532,528])){
            $contestActs->groupBy('object_contests_acts.id');
        }

        return $contestActs->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();
    }


    /**
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection  {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    private function generateCsv($resultContests = null, $resultSection = null): void
    {

        $fileName = 'Bandi di gara e contratti - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Tipo',
            'Contratto',
            'Oggetto',
            'Denominazione dell\'Amministrazione aggiudicatrice',
            'Codice fiscale dell\'Amministrazione aggiudicatrice',
            'Tipo di Amministrazione',
            'Sede di gara - Provincia',
            'Sede di gara - Comune',
            'Sede di gara - Indirizzo',
            'Struttura organizzativa proponente',
            'Senza importo',
            'Valore Importo a base asta',
            'Valore Importo di aggiudicazione',
            'Valore Importo liquidato',
            'Data di pubblicazione',
            'Data di scadenza del bando',
            'Data di effettivo inizio dei lavori o forniture',
            'Data di ultimazione dei lavori o forniture',
            'Data GUUE',
            'Data GURI',
            'Data pubblicazione Stazione Appaltante',
            'Requisiti di qualificazione',
            'Codice CPV',
            'Codice SCP',
            'URL di Pubblicazione su www.serviziocontrattipubblici.it',
            'Codice CIG',
            'Bando di gara relativo (se il presente è avviso o esito)',
            'Altre procedure',
            'Maggiori Dettagli',
            'Procedura di scelta del contraente',
            'Partecipanti alla gara',
            'Aggiudicatari della gara',
            'url origine ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        $contract = ['' => '', 1 => 'Lavori', 2 => 'Servizi', 3 => 'Forniture'];
        $administrationType = ['' => '', 1 => 'Regioni', 2 => 'Provincie', 3 => 'Comuni', 4 => 'Università', 5 => 'Ministeri', 6 => 'Organi istituzionali'];

        foreach ($resultContests as $r) {

            if (!empty($r['relative_notice']['structure']) || !empty($r['structure'])) {
                $tmpStructure = !empty($r['relative_notice']['structure'])
                    ? $r['relative_notice']['structure']
                    : $r['structure'];
            }

            if ($r['typology'] == 'result') {
                $tmpCig = !empty($r['relative_notice']['cig']) ? $r['relative_notice']['cig'] : null;
            } elseif ($r['typology'] == 'notice') {
                $tmpCig = !empty($r['relative_lots']) ? implode(', ', Arr::pluck($r['relative_lots'], 'cig')) : null;
            } else {
                $tmpCig = !empty($r['cig']) ? $r['cig'] : null;
            }

            $tmpRelativeProcedure = null;
            if (!empty($r['relative_procedure']['object'])) {
                $tmpRelativeProcedure = $r['relative_procedure']['object'];
            }

            $otherProceduresList = '';
            if (!empty($r['proceedings'])) {
                foreach ($r['proceedings'] as $proceeding) {

                    if (!empty($proceeding['object']) && !empty($proceeding['id'])) {
                        $otherProceduresList .= $proceeding['object'] . ' - [Url]: ' . siteUrl('page/10/details/' . $proceeding['id'] . '/' . urlTitle($proceeding['object'])) . "\n";
                    }

                }
            }

            // Concateno gli aggiudicatari della gara
            $awardeesLists = '';
            if (!empty($r['awardees'])) {

                foreach ($r['awardees'] as $awardee) {

                    if(!empty($awardee['name'])){
                        $awardeesLists .= $awardee['name'] . "\n";
                    }else{
                        $awardeesLists .= 'N.D.' . "\n";
                    }

                }
            }

            // Concateno i partecipanti alla gara
            $participantsList = '';
            if (!empty($r['participants'])) {

                foreach ($r['participants'] as $participant) {

                    if(!empty($participant['name'])){
                        $participantsList .= $participant['name'] . "\n";
                    }else{
                        $participantsList .= 'N.D.' . "\n";
                    }
                }
            }

            $requirementsList = '';
            if (!empty($r['requirement']['code']) && !empty($r['requirement']['denomination'])) {
                $requirementsList = $r['requirement']['code'] . ' - ' . $r['requirement']['denomination'] . "\n";
            }

            $contraentChoice = '';
            if (!empty($r['contraent_choice']['name'])) {
                $contraentChoice = $r['contraent_choice']['name'];
            } elseif (!empty($r['relative_notice']['contraent_choice']['name'])) {
                $contraentChoice = $r['relative_notice']['contraent_choice']['name'];
            }

            //Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {

                foreach ($r['attachs'] as $attach) {

                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";

                }
            }

            $liquidationValueSum = '';
            if (!empty($r['relative_liquidation'])) {
                $liquidationValueSum = collect(Arr::pluck($r['relative_liquidation'], 'amount_liquidated'))
                    ->map(function ($items) {
                        return (float)S::currency($items, 2, null, null, false);
                    })->sum();
                if(!empty($liquidationValueSum)){
                    $liquidationValueSum = S::currency((string)$liquidationValueSum, 2, ',', '.');
                }else{
                    $liquidationValueSum = null;
                }
            }

            $astaValueSum = '';
            if (!empty($r['relative_lots'])) {
                $astaValueSum = collect(Arr::pluck($r['relative_lots'], 'asta_base_value'))
                    ->map(function ($items) {
                        return (float)S::currency($items, 2, null, null, false);
                    })->sum();
                if(!empty($astaValueSum)){
                    $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');
                }else{
                    $astaValueSum = null;
                }
            } else {
                $astaValueSum = !empty($r['asta_base_value']) ? $r['asta_base_value'] : null;
            }


            $data = [
                // Tipo
                !empty($r['type']) ? $r['type'] : null,
                // Contratto
                !empty($r['contract']) ? $contract[$r['contract']] : null,
                // Oggetto
                !empty($r['object']) ? $r['object'] : null,
                // Denominazione dell'Amministrazione aggiudicatrice
                !empty($r['adjudicator_name']) ? $r['adjudicator_name'] : null,
                // Codice fiscale dell'Amministrazione aggiudicatrice
                !empty($r['adjudicator_data']) ? $r['adjudicator_data'] : null,
                // Tipo di Amministrazione
                !empty($r['administration_type']) ? $administrationType[$r['administration_type']] : null,
                // Sede di gara - Provincia
                !empty($r['province_office']) ? $r['province_office'] : null,
                // Sede di gara - Comune
                !empty($r['municipality_office']) ? $r['municipality_office'] : null,
                // Sede di gara - Indirizzo
                !empty($r['office_address']) ? $r['office_address'] : null,
                // Struttura organizzativa proponente
                !empty($tmpStructure) ? $tmpStructure['structure_name'] . ' - [Url]: ' . siteUrl('page/40/details/' . $tmpStructure['id'] . '/' . urlTitle($tmpStructure['structure_name'])) : null,
                // Senza importo
                !empty($r['no_amount']) ? 'Si' : 'No',
                // Valore Importo a base asta
                !empty($astaValueSum) ? '€ ' . S::currency($astaValueSum, 2, ',', '.') : null,
                // Valore Importo di aggiudicazione
                !empty($r['award_amount_value']) ? '€ ' . S::currency($r['award_amount_value'], 2, ',', '.') : null,
                // Valore importo liquidato
                !empty($liquidationValueSum) ? '€ ' . S::currency($liquidationValueSum, 2, ',', '.') : null,
                // Data di pubblicazione
                !empty($r['activation_date']) ? convertDateForCsv($r['activation_date']) : null,
                // Data di scadenza del bando
                !empty($r['expiration_date']) ? convertDateForCsv($r['expiration_date']) : null,
                // Data di effettivo inizio dei lavori, servizi o forniture
                !empty($r['work_start_date']) ? convertDateForCsv($r['work_start_date']) : null,
                // Data di ultimazione dei lavori o forniture
                !empty($r['work_end_date']) ? convertDateForCsv($r['work_end_date']) : null,
                // Data GUUE
                !empty($r['guue_date']) ? convertDateForCsv($r['guue_date']) : null,
                // Data GURI
                !empty($r['guri_date']) ? convertDateForCsv($r['guri_date']) : null,
                // Data pubblicazione Stazione Appaltante
                !empty($r['contracting_stations_publication_date']) ? convertDateForCsv($r['contracting_stations_publication_date']) : null,
                // Requisiti di qualificazione
                !empty($requirementsList) ? trim($requirementsList) : null,
                // Codice CPV
                !empty($r['cpv_code_id']) ? $r['cpv_code_id'] : null,
                // Codice SCP
                !empty($r['codice_scp']) ? $r['codice_scp'] : null,
                // URL di Pubblicazione su www.serviziocontrattipubblici.it
                !empty($r['url_scp']) ? $r['url_scp'] : null,
                // Codice cig
                !empty($tmpCig) ? $tmpCig : null,
                // Bando di gara relativo (se il presente è avviso o esito)
                !empty($tmpRelativeProcedure) ? $tmpRelativeProcedure : null,
                // Altre procedure
                !empty($otherProceduresList)  ? trim($otherProceduresList) : null,
                // Maggiori Dettagli
                !empty($r['details']) ? S::chartsEntityDecode(S::stripTags($r['details'])) : null,
                // Scelta del contraente
                !empty($contraentChoice)  ? trim($contraentChoice) : null,
                // Partecipanti alla gara
                !empty($participantsList)  ? trim($participantsList) : null,
                // Aggiudicatari
                !empty($awardeesLists)  ? trim($awardeesLists) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                // Allegati
                !empty($attachLists)  ? trim($attachLists) : null
            ];
            fputcsv($output, $data, ';', '"');
        }

        fclose($output);

    }
}
