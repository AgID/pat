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
use Model\NoticesActsModel;
use Model\SectionsFoModel;
use System\Arr;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per gli atti delle amministrazioni
 */
class OpenDataNoticesActsFrontController extends BaseFrontController
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
     * Metodo download open data per gli atti delle amministrazioni
     * ID sezione 114,115,116,529,530,531
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexNoticesActs(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataFromPageId((int)uri()->segment(3, 0));

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Metodo download open data
     * ID sezione 117 e 527 (pagine a doppia pubblicazione)
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexFinancialManagementReports(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        $contest = Input::get('issueType') == 'contest-act';

        if ($contest) {

            //Resoconti della gestione finanziaria dei contratti al termine della loro esecuzione
            if ((int)uri()->segment(3, 0) == 117) {
                $queryContents = ContestsActsModel::select(['object_contests_acts.*'])
                    ->whereIn('object_contests_acts.typology', ['result', 'foster'])
                    ->where(
                        function ($query) {
                            $query->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                                ->orWhereNull('object_contests_acts.expiration_date');
                        });
            } else {
                //Fase esecutiva 527
                $queryContents = ContestsActsModel::select(['object_contests_acts.*'])
                    ->where('object_contests_acts.typology', 'foster')
                    ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                    ->where(
                        function ($query) {
                            $query->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                                ->orWhereNull('object_contests_acts.expiration_date');
                        }
                    )
                    ->whereHas('public_in_section', function ($query) {
                        $query->where('public_in_id', 527);
                    })
                    ->with(['relative_liquidation' => function ($query) {
                        $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
                    }])
                    ->with(['relative_notice' => function ($query) {
                        $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id', 'contraent_choice']);
                        $query->with(['contraent_choice' => function ($query) {
                            $query->select(['id', 'name']);
                        }]);
                    }]);

            }

            $queryContents = $queryContents->with('structure:id,structure_name')
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
                ->orderBy('expiration_date', 'DESC')
                ->skip(Input::get('skip'))
                ->take(Input::get('take'))
                ->get();

        } else {
            //notice-act
            $queryContents = $this->getDataFromPageId((int)uri()->segment(3, 0));
        }

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            if ($contest) {
                $this->generateCsvContest($resultContests, $resultSection);
            } else {
                $this->generateCsv($resultContests, $resultSection);
            }

        }

    }


    /**
     * Funzione che ritorna i dati per la generazione degli open data
     *
     * @param $sectionId {id della sezione}
     * @return mixed
     * @throws Exception
     */
    private function getDataFromPageId($sectionId = null): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        return NoticesActsModel::whereHas('public_in_section', function ($query) use ($sectionId) {
            $query->where('public_in_id', $sectionId);
        })
            ->with('assignments:id,object')
            ->with('relative_contest_act:id,type,object')
            ->with('attachs')
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();
    }


    /**
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    private function generateCsv($resultContests = null, $resultSection = null): void
    {

        $fileName = 'Bandi di gara e contratti - atti delle amministrazioni - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Oggetto',
            'Data',
            'Procedure relative',
            'Note',
            'Commissione giudicatrice',
            'Cup',
            'Importo totale del finanziamento',
            'Fonti Finanziarie',
            'Stato di attuazione finanziario e procedurale',
            'Data avvio progetti',
            'Url Origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        foreach ($resultContests as $r) {

            //Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {
                foreach ($r['attachs'] as $attach) {
                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";
                }
            }

            //Procedura relativa
            $relativeProcedures = '';
            if (!empty($r['relative_contest_act'])) {
                foreach ($r['relative_contest_act'] as $procedure) {
                    $relativeProcedures .= $procedure['object'] . "\n";
                }
            }

            //Commissione giudicatrice
            $commissions = '';
            if (!empty($r['assignments'])) {
                foreach ($r['assignments'] as $comm) {
                    $commissions .= $comm['object'] . "\n";
                }
            }

            $data = [
                //Oggetto
                !empty($r['object']) ? $r['object'] : null,
                //Data
                !empty($r['date']) ? convertDateForCsv($r['date']) : null,
                //Procedure relative
                !empty($relativeProcedures) ? trim($relativeProcedures) : null,
                //Note
                !empty($r['details']) ? S::chartsEntityDecode(S::stripTags($r['details'])) : null,
                //Commissione giudicatrice
                !empty($commissions) ? trim($commissions) : null,
                //Cup
                !empty($r['cup']) ? $r['cup'] : null,
                //Importo totale del finanziamento
                !empty($r['total_fin_amount']) ? $r['total_fin_amount'] : null,
                //Fonti Finanziarie
                !empty($r['financial_sources']) ? $r['financial_sources'] : null,
                //Stato di attuazione finanziario e procedurale
                !empty($r['implementation_state']) ? $r['implementation_state'] : null,
                //Data avvio progetti
                !empty($r['projects_start_date']) ? convertDateForCsv($r['projects_start_date']) : null,
                //Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                //Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }

    /**
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    private function generateCsvContest($resultContests = null, $resultSection = null): void
    {

        $fileName = 'Bandi di gara e contratti - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
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

                    if (!empty($awardee['name'])) {
                        $awardeesLists .= $awardee['name'] . "\n";
                    } else {
                        $awardeesLists .= 'N.D.' . "\n";
                    }

                }
            }

            // Concateno i partecipanti alla gara
            $participantsList = '';
            if (!empty($r['participants'])) {

                foreach ($r['participants'] as $participant) {

                    if (!empty($participant['name'])) {
                        $participantsList .= $participant['name'] . "\n";
                    } else {
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
                if (!empty($liquidationValueSum)) {
                    $liquidationValueSum = S::currency((string)$liquidationValueSum, 2, ',', '.');
                } else {
                    $liquidationValueSum = null;
                }
            }

            $astaValueSum = '';
            if (!empty($r['relative_lots'])) {
                $astaValueSum = collect(Arr::pluck($r['relative_lots'], 'asta_base_value'))
                    ->map(function ($items) {
                        return (float)S::currency($items, 2, null, null, false);
                    })->sum();
                if (!empty($astaValueSum)) {
                    $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');
                } else {
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
                !empty($otherProceduresList) ? trim($otherProceduresList) : null,
                // Maggiori Dettagli
                !empty($r['details']) ? S::chartsEntityDecode(S::stripTags($r['details'])) : null,
                // Scelta del contraente
                !empty($contraentChoice) ? trim($contraentChoice) : null,
                // Partecipanti alla gara
                !empty($participantsList) ? trim($participantsList) : null,
                // Aggiudicatari
                !empty($awardeesLists) ? trim($awardeesLists) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                // Allegati
                !empty($attachLists) ? trim($attachLists) : null
            ];
            fputcsv($output, $data, ';', '"');
        }

        fclose($output);

    }
}
