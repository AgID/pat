<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\InterventionsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per gli Interventi
 */
class OpenDataInterventionsFrontController extends BaseFrontController
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
     * Metodo download open data per gli Interventi,
     * nella sezione Interventi straordinari e di emergenza
     * ID sezione 187
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = InterventionsModel::with('attachs')
            ->with('measures:id,object')
            ->with('regulations:id,title')
            ->orderBy('created_at', 'DESC')
            ->orderBY('updated_at', 'DESC')
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
        $fileName = 'Interventi - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Nome Intervento',
            'Descrizione',
            'Provvedimenti Correlati',
            'Regolamenti',
            'Norme derogate e Motivazioni',
            'Termini temporali per i provvedimenti straordinari',
            'Costo interventi stimato',
            'Costo interventi effettivo',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );

        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            $measuresList = '';
            if (!empty($r['measures'])) {
                foreach ($r['measures'] as $measure) {

                    if(!empty($measure['object'])){
                        $measuresList .= $measure['object'] . ' - [Url]: '
                            . siteUrl('page/9/details/' . $measure['id'] . '/' . urlTitle($measure['object'])) . "\n";
                    }
                }
            }

            $regulationsList = '';
            if (!empty($r['regulations'])) {

                foreach ($r['regulations'] as $regulation) {

                    if(!empty($regulation['title'])){
                        $regulationsList .= $regulation['title'] . ' - [Url]: '
                            . siteUrl('page/29/details/' . $regulation['id'] . '/' . urlTitle($regulation['title'])) . "\n";
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
                // Nome intervento
                !empty($r['name']) ? $r['name'] : null,
                // Descrizione
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Provvedimenti Correlati
                !empty($measuresList) ? trim($measuresList) : null,
                // Regolamenti
                !empty($regulationsList) ? trim($regulationsList) : null,
                // Norme derogate e Motivazioni
                !empty($r['derogations']) ? S::chartsEntityDecode(S::stripTags($r['derogations'])) : null,
                // Termini temporali per i provvedimenti straordinari
                !empty($r['time_limits']) ? convertDateForCsv($r['time_limits']) : null,
                // Costo interventi stimato
                !empty($r['estimated_cost']) ? '€ ' . S::currency($r['estimated_cost'], 2, ',', '.') : null,
                // Costo interventi effettivo
                !empty($r['effective_cost']) ? '€ ' . S::currency($r['effective_cost'], 2, ',', '.') : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['name'])),
                // Allegati
                trim($attachLists)
            ];
            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
