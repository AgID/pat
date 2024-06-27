<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\Validators\OpenDataDate;
use Model\AbsenceRatesModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i tassi di assenza
 */
class OpenDataAbsenceRatesFrontController extends BaseFrontController
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
     * Metodo download open data per i Tassi di assenza,
     * nella sezione Personale->Tassi di assenza
     * ID sezione 66
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     * @noinspection PhpUndefinedVariableInspection
     */
    public function index(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = AbsenceRatesModel::with('attachs')
            ->with('structure:id,structure_name')
            ->with('attachs')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
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
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection  {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    private function generateCsv($resultContests = null, $resultSection = null): void
    {

        //tassi di assenza
        $fileName = $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Struttura',
            'Periodo',
            'Anno',
            'Percentuale presenza',
            'Percentuale assenza totale',
            'url origine ' . $resultSection['name'],
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

            $structure = !empty($r['structure']['structure_name'])
                ? $r['structure']['structure_name'].' - '.siteUrl('page/40/details/' .$r['structure']['id']. '/' .urlTitle($r['structure']['structure_name']))
                : null;

            // Mesi
            $monthsLists = !empty($r['month']) ? explode(',', $r['month']) : null;
            if(!empty($monthsLists)) {
                $tmpMonth = [];
                $periods = config('absenceRatesPeriod', null, 'app');
                foreach ($monthsLists as $month){
                    $tmpMonth [] = $periods[$month];
                }
                $monthsLists = implode(',', $tmpMonth);
            }

            $data = [
                // Struttura
                $structure,
                // Periodo
                !empty($monthsLists)  ? $monthsLists : null,
                // Anno
                !empty($r['year']) ? $r['year'] : null,
                // Percentuale presenza
                !empty($r['presence_percentage']) ? $r['presence_percentage'] : null,
                // Percentuale assenza totale
                !empty($r['total_absence']) ? $r['total_absence'] : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/tasso-di-assenza'),
                // Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
