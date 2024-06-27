<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\ContestModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i bandi di concorso
 */
class OpenDataContestsFrontController extends BaseFrontController
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
     * Metodo download open data "Concorsi attivi",
     * nella sezione dei Bandi di concorso
     * Id sezione 75
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexActive(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getDataWithPageId('concorso', 75);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests,$resultSection);
        }
    }

    /**
     * Metodo download open data "Concorsi scaduti",
     * nella sezione dei Bandi di concorso
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexExpired(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso scaduti
        $queryContents = $this->getDataWithPageId('concorso', 76);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests,$resultSection);
        }
    }

    /**
     * Metodo download open data "Avvisi", nella sezione dei Bandi di concorso
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexAlert(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi (avvisi)
        $queryContents = $this->getDataWithPageId('avviso');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests,$resultSection);
        }
    }

    /**
     * Metodo download open data "Esiti", nella sezione dei Bandi di concorso
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexResult(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi (esito)
        $queryContents = $this->getDataWithPageId('concorso');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests,$resultSection);
        }
    }


    /**
     * Funzione che ritorna i dati per generare gli open data
     *
     * @param $type   {la tipologia per cui filtrare i dati}
     * @param $pageId {id della pagina}
     * @return mixed
     * @throws Exception
     */
    private function getDataWithPageId($type = null, $pageId = null): mixed
    {
        // Recupero i bandi di concorso da mostrare
        return ContestModel::where('typology', $type)
            ->where('activation_date', '<', date("Y-m-d H:i:s", strtotime('tomorrow')))
            ->where(
                function ($query) use ($pageId) {
                    if($pageId == 75){
                        $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))
                            ->orWhereNull('expiration_date');
                    }elseif ($pageId == 76){
                        $query->where('expiration_date', '<', date('Y-m-d H:i:s'));
                    }
                })
            ->with('assignments:id,name,object')
            ->with('relative_measure:id,object')
            ->with('office:id,structure_name')
            ->with('related_contest:id,object')
            ->with('attachs')
            ->orderBy('activation_date', 'DESC')
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

        $fileName = 'Bandi di concorso - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Tipo',
            'Oggetto',
            'Bando di Concorso relativo',
            'Sede Prova - Provincia',
            'Sede Prova - Comune',
            'Sede Prova - Indirizzo',
            'Ufficio di riferimento',
            'Calendario delle Prove',
            'Criteri di valutazione',
            'Tracce prove scritte',
            'Data di pubblicazione',
            'Data di scadenza',
            'Data di termine del concorso',
            'Orario scadenza',
            'Numero dipendenti assunti',
            'Eventuale spesa prevista',
            'Spese effettuate',
            'Provvedimento',
            'Commissione giudicatrice',
            'Maggiori informazioni sul bando',
            'Url origine ' . $resultSection['name'],
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

            // Riferimento url
            $officeUrl = !empty($r['office'])
                ? siteUrl('page/40/details/' . $r['office']['id'] . '/' . urlTitle($r['office']['structure_name']))
                : null;


            //Commissione giudicatrice
            $commissions = '';
            if (!empty($r['assignments'])) {
                foreach ($r['assignments'] as $comm) {
                    $commissions .= $comm['object'] . "\n";
                }
            }

            $data = [
                // Tipo
                !empty($r['typology']) ? $r['typology'] : null,
                // Oggetto
                !empty($r['object']) ? $r['object'] : null,
                //Concorso o avviso relativo
                !empty($r['related_contest']['object']) ? $r['related_contest']['object'] : null,
                // Sede Prova - Provincia
                !empty($r['province_office']) ? $r['province_office'] : null,
                // Sede Prova - Comune
                !empty($r['city_office']) ? $r['city_office'] : null,
                // Sede Prova - Indirizzo
                !empty($r['office_address']) ? $r['office_address'] : null,
                // Ufficio di riferimento
                !empty($r['office']) ? $r['office']['structure_name'] . ' - ' . $officeUrl : null,
                // Calendario delle Prove
                !empty($r['test_calendar']) ? S::chartsEntityDecode(S::stripTags($r['test_calendar'])) : null,
                // Criteri di valutazione
                !empty($r['evaluation_criteria']) ? S::chartsEntityDecode(S::stripTags($r['evaluation_criteria'])) : null,
                // Tracce prove scritte
                !empty($r['traces_written_tests']) ? S::chartsEntityDecode(S::stripTags($r['traces_written_tests'])) : null,
                // Data di pubblicazione
                !empty($r['activation_date']) ? convertDateForCsv($r['activation_date']) : null,
                // Data di scadenza
                !empty($r['expiration_date']) ? convertDateForCsv($r['expiration_date']) : null,
                // Data di termine del concorso
                !empty($r['expiration_contest_date']) ? convertDateForCsv($r['expiration_contest_date']) : null,
                // Orario scadenza
                !empty($r['expiration_time']) ? $r['expiration_time'] : null,
                // Numero dipendenti assunti
                !empty($r['hired_employees']) ? $r['hired_employees'] :null ,
                // Eventuale spesa prevista
                !empty($r['expected_expenditure']) ? ('€ ' . S::currency($r['expected_expenditure'], 2, ',', '.')) : null,
                // Spese effettuate
                !empty($r['expenditures_made']) ? ('€ ' . S::currency($r['expenditures_made'], 2, ',', '.')) : null,
                //Provvedimento
                !empty($r['relative_measure']['object']) ? $r['relative_measure']['object'] : null,
                //Commissione giudicatrice
                !empty($commissions) ? trim($commissions) : null,
                //Maggiori informazioni sul bando
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($resultSection['name'])),
                // Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
