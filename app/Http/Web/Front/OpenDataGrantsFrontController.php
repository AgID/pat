<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\GrantsModel;
use Model\SectionsFoModel;
use System\Arr;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per le sovvenzioni
 */
class OpenDataGrantsFrontController extends BaseFrontController
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
     * Metodo download open data per le Sovvenzioni,
     * nella sezione Sovvenzioni, Contributi, Sussidi, Vantaggi economici->Atti di concessione
     * ID sezione 126
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexConcessionActs(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('grant');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per le Sovvenzioni,
     * nella sezione Sovvenzioni, Contributi, Sussidi, Vantaggi economici->Elenco soggetti beneficiari
     * ID sezione 127
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexBeneficiary(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('grant');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per le Sovvenzioni,
     * nella sezione Pagamenti dell'amministrazione->Dati sui pagamenti->Pagamenti di Sovvenzioni, contributi, sussidi,
     * vantaggi economici
     * ID sezione 127
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexLiquidation(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('liquidation');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $fileName = ' Sovvenzioni - ' . $resultSection['name'] . '.csv';
            header('Set-Cookie: fileDownload=true; path=/');
            header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
            header('Content-Type: text/csv; charset= utf-8');
            header('Content-Disposition: attachment; filename="' . $fileName.'"');
            $output = fopen("php://output", 'w');

            $headers = array(
                'Tipologia',
                'Oggetto',
                'Struttura organizzativa responsabile',
                'Dirigente o funzionario responsabile',
                'Data di riferimento',
                'Importo erogato',
                'Data importo erogato',
                'Note',
                'Procedura relativa',
                'url origine ' . $resultSection['name'],
                'Allegati'
            );
            fputcsv($output, $headers, ';', '"');

            // Creazione delle celle per i dati in formato CSV
            foreach ($resultContests as $r) {

                $structure = !empty($r['relative_grant']['structure']) ? $r['relative_grant']['structure'] : null;


                // Concateno i responsabili
                $personnelList = '';
                if (!empty($r['relative_grant']['personnel'])) {
                    foreach ($r['relative_grant']['personnel'] as $p) {

                        if(!empty($p['full_name'])){
                            $personnelList .= '(' . $p['full_name'] . ') - Url: '
                                . siteUrl('page/4/details/' . $p['id'] . '/' . urlTitle($p['full_name'])) . "\n";
                        }
                    }
                }

                $relativeGrant = !empty($r['relativeGrant']) ? $r['relativeGrant'] : null;

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
                    // Tipologia
                    !empty($r['typology']) ? $r['typology'] : null,
                    // Oggetto
                    !empty($r['object']) ? $r['object'] : null,
                    // Struttura organizzativa responsabile
                    !empty($structure['structure_name']) ? $structure['structure_name'] . '- [Url]: ' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) : null,
                    // Dirigente o funzionario responsabile
                    !empty($personnelList) ? trim($personnelList): null,
                    // Data di riferimento
                    !empty($r['reference_date']) ? convertDateForCsv($r['reference_date']) : null,
                    // Importo erogato(somme liquidate)
                    !empty($r['compensation_paid']) ? '€ ' . S::currency($r['compensation_paid'], 2, ',', '.') : null,
                    // Data importo erogato
                    !empty($r['compensation_paid_date']) ? $r['compensation_paid_date'] : null,
                    // Note
                    !empty($r['notes']) ? S::chartsEntityDecode(S::stripTags($r['notes'])) : null,
                    // Procedura relativa
                    !empty($relativeGrant['object']) ? $relativeGrant['object'] . ' - [Url]: ' . siteUrl('page/126/details/' . $relativeGrant['id'] . '/' . urlTitle($relativeGrant['object'])) : null,
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
     * Funzione che ritorna i dati per la generazione degli open data
     *
     * @param null $type Indica la tipologia (Sovvenzione/Liquidazione)
     * @return mixed
     */
    private function getData($type = null): mixed
    {
        // Recupero i provvedimenti da mostrare
        return GrantsModel::where('object_grants.type', $type)
            ->with(['relative_grant' => function ($query) {
                $query->select(['object_grants.id', 'object', 'beneficiary_name', 'concession_act_date', 'fiscal_data', 'privacy', 'object_structures_id', 'notes', 'fiscal_data', 'fiscal_data_not_available'])
                    ->with('structure:id,structure_name');
                $query->with('personnel:id,full_name');
            }])
            ->with('normatives:id,name')
            ->with('regulation:id,title')
            ->with('structure:id,structure_name')
            ->with('personnel:id,full_name')
            ->with('relative_liquidation:id,grant_id,compensation_paid,compensation_paid_date,reference_date')
            ->with('attachs')
            ->orderBy('object_grants.concession_act_date', 'DESC')
            ->orderBy('object_grants.reference_date', 'DESC')
            ->orderBy('object_grants.created_at', 'DESC')
            ->skip(Input::get('skip'))
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
        $fileName = 'Sovvenzioni - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');


        $headers = array(
            'Tipologia',
            'Oggetto',
            'Struttura organizzativa responsabile',
            'Dirigente o funzionario responsabile',
            'Data di riferimento',
            'Data inizio',
            'Data fine',
            'Importo',
            'Importo erogato',
            'Data importo erogato',
            'Norma o titolo alla base dell\'attribuzione',
            'Regolamento alla base dell\'attribuzione',
            'Note',
            'Modalità seguita per l\'individuazione',
            'url origine ' . $resultSection['name'],
            'Allegati'
        );

        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            $structure = !empty($r['structure']) ? $r['structure'] : null;

            $liquidationValueSum = null;
            if (!empty($r['relative_liquidation'])) {
                $liquidationValueSum = collect(Arr::pluck($r['relative_liquidation'], 'compensation_paid'))
                    ->map(function ($items) {
                        return (float)S::currency($items, 2, null, null, false);
                    })->sum();
                $liquidationValueSum = S::currency((string)$liquidationValueSum, 2, ',', '.');
                $liquidationDate = !empty($r['relative_liquidation'][0]['reference_date']) ? $r['relative_liquidation'][0]['reference_date'] : null;
            }

            // Concateno i responsabili
            $personnelList = '';
            if (!empty($r['personnel'])) {

                foreach ($r['personnel'] as $p) {
                    if(!empty($p['full_name'])){
                        $personnelList .= '(' . $p['full_name'] . ') - Url: ' . siteUrl('page/4/details/' . $p['id'] . '/' . urlTitle($p['full_name'])) . "\n";
                    }
                }
            }

            // Concateno le normative
            $normativesList = '';
            if (!empty($r['normatives'])) {

                foreach ($r['normatives'] as $normative) {

                    if(!empty($normative['name'])){
                        $normativesList .= '(' . $normative['name'] . ') - Url: '
                            . siteUrl('page/24/details/' . $normative['id'] . '/' . urlTitle($normative['name'])) . "\n";
                    }
                }
            }

            // Concateno gli allegati
            $attachLists = '';
            $attachLists = '';
            if (!empty($r['attachs'])) {

                foreach ($r['attachs'] as $attach) {

                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";
                }
            }

            // Genero l'indice dell'array
            $data = [
                // Tipologia
                !empty($r['typology']) ? $r['typology'] : null,
                // Oggetto
                !empty($r['object']) ? $r['object'] : null,
                // Struttura organizzativa responsabile
                !empty($structure['structure_name']) ? $structure['structure_name'] . '- [Url]: ' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) : null,
                // Dirigente o funzionario responsabile
                !empty($personnelList) ? trim($personnelList): null,
                // Data di riferimento
                !empty($r['concession_act_date']) ? date('d-m-Y', strtotime($r['concession_act_date'])) : null,
                // Data inizio
                !empty($r['start_date']) ? date('d-m-Y', strtotime($r['start_date'])) : null,
                // Data fine
                !empty($r['end_date']) ? date('d-m-Y', strtotime($r['end_date'])) : null,
                // Importo
                !empty($r['concession_amount']) ? '€ ' . S::currency($r['concession_amount'], 2, ',', '.') : null,
                // Importo erogato(somme liquidate)
                !empty($liquidationValueSum) ? '€ ' . S::currency($liquidationValueSum, 2, ',', '.') : null,
                // Data importo erogato
                !empty($liquidationDate) ? date('d-m-Y', strtotime($liquidationDate)) : null,
                // Norma o titolo alla base dell'attribuzione
                !empty($normativesList) ? trim($normativesList): null,
                // Regolamento alla base dell'attribuzione
                !empty($r['regulation']) ? $r['regulation']['title'] . ' - [Url]: ' . siteUrl('page/24/details/' . $r['regulation']['id'] . '/' . urlTitle($r['regulation']['title'])) : null,
                // Note
                !empty($r['notes']) ? S::chartsEntityDecode(S::stripTags($r['notes'])) : null ,
                // Modalità seguita per l'individuazione
                !empty($r['detection_mode']) ? S::chartsEntityDecode(S::stripTags($r['detection_mode'])) : null,
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
