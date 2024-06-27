<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\RealEstateAssetModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per gli immobili
 */
class OpenDataRealEstateAssetFrontController extends BaseFrontController
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
     * Metodo download open data per gli Immobili,
     * nella sezione Beni immobili e gestione patrimonio->Patrimonio immobiliare
     * ID sezione 133
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
        $queryContents = RealEstateAssetModel::with('offices:id,structure_name')
            ->with('attachs')
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

        $fileName = 'Immobili - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Nome',
            'Indirizzo',
            'Ufficio utilizzatore',
            'Superficie lorda',
            'Superficie scoperta',
            'Foglio',
            'Particella',
            'Subalterno',
            'Descrizione e note',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno le strutture
            $structuresList = '';
            if (!empty($r['offices'])) {

                foreach ($r['offices'] as $office) {
                    if(!empty($office['structure_name'])){
                        $structuresList .= $office['structure_name'] . ' - [Url]: '
                            . siteUrl('page/40/details/' . $office['id'] . '/' . urlTitle($office['structure_name'])) . "\n";
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
                // Nome
                !empty($r['name']) ? $r['name'] : null,
                // Tipologia
                !empty($r['address']) ? $r['address'] : null,
                // Uffici utilizzatori
                trim($structuresList),
                // Superficie lorda
                !empty($r['gross_surface']) ? $r['gross_surface'] . ' mq' : null,
                // Superficie scoperta
                !empty($r['discovered_surface']) ? $r['discovered_surface'] . ' mq' : null,
                // Foglio
                !empty($r['sheet']) ? $r['sheet'] : null,
                // Particella
                !empty($r['particle']) ? $r['particle'] : null,
                // Subalterno
                !empty($r['subaltern']) ? $r['subaltern'] : null,
                // Descrizione e note
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
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
