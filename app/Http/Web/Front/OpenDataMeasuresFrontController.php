<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\MeasuresModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i Provvedimenti
 */
class OpenDataMeasuresFrontController extends BaseFrontController
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
     * Metodo download open data per i Provvedimenti,
     * nella sezione Provvedimenti->Provvedimenti organi indirizzo politico
     * ID sezione 103
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPolitical(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData(14);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }


    /**
     * Metodo download open data per i Provvedimenti,
     * nella sezione Provvedimenti->Provvedimenti dirigenti amministrativi
     * ID sezione 106
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexAdministrative(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData(13);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Funzione che ritorna i dati per generare gli open data
     *
     * @param $type {la tipologia per cui filtrare i dati}
     * @return mixed
     * @throws Exception
     */
    private function getData($type = null): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        return MeasuresModel::where('type', $type)
            ->with('structures:id,structure_name')
            ->with('personnel:id,full_name')
            ->with('relative_procedure_contraent:id,object')
            ->with('attachs')
            ->orderBy('date', 'DESC')
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

        $fileName = 'Provvedimenti - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Numero del provvedimento',
            'Oggetto',
            'Tipologia',
            'Struttura responsabile',
            'Funzionario responsabile',
            'Data',
            'Scelta del contraente',
            'Scelta del contraente per l\'affidamento di lavori, forniture e servizi',
            'Note',
            'url origine ' . $resultSection['name'],
            'allegati'
        );
        fputcsv($output, $headers, ';', '"');

        $measureTypologies = config('measureTypologies', null, 'app');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            $structuresLists = '';
            if (!empty($r['structures'])) {
                foreach ($r['structures'] as $structure) {
                    if(!empty($structure['structure_name'])){
                        $structuresLists .= $structure['structure_name'] . ' - [Url]: '
                            . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . "\n";
                    }
                }
            }


            $responsiblesLists = '';
            if (!empty($r['personnel'])) {

                foreach ($r['personnel'] as $responsible) {
                    if(!empty($responsible['full_name'])){
                        $responsiblesLists .= $responsible['full_name'] . ' - [Url]: ' . siteUrl('page/4/details/' . $responsible['id']
                            . '/' . urlTitle($responsible['full_name'])) . "\n";
                    }
                }
            }

            // Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {

                foreach ($r['attachs'] as $attach) {
                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";
                }
            }

            // Genero l'indice dell'array
            $data = [
                // Numero del provvedimento
                !empty($r['number']) ? $r['number'] : null,
                // Oggetto
                !empty($r['object']) ? $r['object'] : null,
                // Tipologia
                !empty($r['type']) && array_key_exists($r['type'], $measureTypologies) ? $measureTypologies[$r['type']] : null,
                // Struttura responsabile
                !empty($structuresLists) ? trim($structuresLists) : null,
                // Funzionario responsabile
                !empty($responsiblesLists) ? trim($responsiblesLists) : null,
                // Data
                !empty($r['date']) ? date('d-m-Y', strtotime($r['date'])) : null,
                // Scelta del contraente
                !empty($r['choice_of_contractor']) ? S::chartsEntityDecode(S::stripTags($r['choice_of_contractor'])) : null,
                // Scelta del contraente per l'affidamento di lavori, forniture e servizi
                !empty($r['relative_procedure_contraent']) ? $r['relative_procedure_contraent']['object'] : null,
                // Note
                !empty($r['notes']) ? $r['notes'] :null,
                // Url origine record
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                // Allegati
                trim($attachLists)
            ];
            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }

}