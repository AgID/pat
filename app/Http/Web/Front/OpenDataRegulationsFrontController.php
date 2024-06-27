<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\RegulationsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i Regolamenti
 */
class OpenDataRegulationsFrontController extends BaseFrontController
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
     * Metodo download open data per i Regolamenti,
     * nella sezione Disposizioni Generali->Atti generali->Atti amministrativi generali
     * ID sezione 25
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexGeneralAdministrativeActs(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('25');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }


    /**
     * Metodo download open data per i Regolamenti,
     * nella sezione Disposizioni Generali->Atti generali->Documenti di programmazione strategico-gestionale
     * ID sezione 26
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexDocumentsStrategicManagement(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('26');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }


    /**
     * Metodo download open data per i Regolamenti,
     * nella sezione Disposizioni Generali->Atti generali->Statuti e leggi regionali
     * ID sezione 27
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexStatutesRegionalLaws(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('27');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }



    /**
     * Metodo download open data per i Regolamenti,
     * nella sezione Disposizioni Generali->Codice disciplinare e codice di condotta
     * ID sezione 28
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexDisciplinaryAndConductCode(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('28');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }
    /**
     * Metodo download open data per i Regolamenti,
     * nella sezione Disposizioni Generali->Atti generali->Regolamenti
     * ID sezione 29
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexRegulations(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('29');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Funzione che ritorna i dati per la generazione degli open data
     *
     * @param $section {le sezioni pubblica in per cui filtrare i dati}
     * @return mixed
     * @throws Exception
     */
    private function getData($section = null): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        return RegulationsModel::with('proceedings:id,name')
            ->with('structures:id,structure_name')
            ->with('attachs')
            ->when($section, function ($query, $section) {
                $query->whereHas('public_in_filter', function ($query) use ($section) {
                    $query->where('rel_regulations_public_in.public_in_id', '=', $section);
                });
            })
            ->orderBy('order', 'ASC')
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

        $fileName = 'Regolamenti - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Titolo',
            'Numero',
            'Protocollo',
            'Data emissione',
            'Strutture organizzative associate',
            'Procedimenti associati',
            'Descrizione',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno le strutture
            $structuresList = '';
            if (!empty($r['structures'])) {

                foreach ($r['structures'] as $structure) {
                    if(!empty($structure['structure_name'])){
                        $structuresList .= $structure['structure_name'] . ' - [Url]: '
                            . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . "\n";
                    }
                }
            }

            // Concateno i procedimenti
            $proceedingsList = '';
            if (!empty($r['proceedings'])) {

                foreach ($r['proceedings'] as $proceeding) {

                    if(!empty($proceeding['name'])){
                        $proceedingsList .= $proceeding['name'] . ' - [Url]: '
                            . siteUrl('page/98/details/' . $proceeding['id'] . '/' . urlTitle($proceeding['name'])) . "\n";
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
                !empty($r['title']) ? $r['title'] : null,
                // Numero
                !empty($r['number']) ? $r['number'] : null,
                // Protocollo
                !empty($r['protocol']) ? $r['protocol'] : null,
                // Data emissione
                !empty($r['issue_date']) ? date('d-m-Y', strtotime($r['issue_date'])) : null,
                // Strutture Correlati
                trim($structuresList),
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
