<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\ModulesRegulationsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per la Modulistica
 */
class OpenDataModulisticsFrontController extends BaseFrontController
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
     * Metodo download open data per la Modulistica,
     * nella sezione Attività e procedimenti->Dichiarazioni sostitutive e acquisizione d'ufficio dei dati
     * ID sezione 101
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexSubstituteDeclarations(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = ModulesRegulationsModel::where('typology', 'dichiarazione sostitutiva')
            ->with('proceedings:id,name')
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
     * Metodo download open data per la Modulistica,
     * nella sezione Disposizioni Generali->Atti generali->Modulistica
     * ID sezione 30
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexModulistics(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = ModulesRegulationsModel::where('typology', 'modulo')
            ->with('proceedings:id,name')
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

        $fileName = 'Modulistica - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        // Intestazione indice dei dati...
        $headers = array(
            'Titolo',
            'Procedimenti associati',
            'Descrizione',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno i procedimenti..
            $proceedingsList = '';
            if (!empty($r['proceedings'])) {

                foreach ($r['proceedings'] as $proceeding) {

                    if(!empty($proceeding['name'])){
                        $proceedingsList .= $proceeding['name'] . ' - [Url]: '
                            . siteUrl('page/98/details/' . $proceeding['id'] . '/' . urlTitle($proceeding['name'])) . "\n";
                    }
                }
            }

            // Concateno gli allegati..
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
                !empty($r['title']) ? $r['title'] : null,
                // Procedimenti
                trim($proceedingsList),
                // Descrizione
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['title'])),
                // Allegati
                trim($attachLists)
            ];
            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
