<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\ChargesModel;
use Model\SectionsFoModel;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per gli Oneri informativi
 */
class OpenDataChargesFrontController extends BaseFrontController
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
     * Metodo download open data per gli Oneri informativi,
     * nella sezione Disposizioni Generali->Oneri informativi per cittadini e imprese->Scadenzario obblighi amministrativi
     * ID sezione 32
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexSchedule(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('obbligo');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Metodo download open data per gli Oneri informativi,
     * nella sezione Disposizioni Generali->Oneri informativi per cittadini e imprese->Oneri informativi per cittadini e imprese
     * ID sezione 33
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexForCitizensAndCompanies(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('onere');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }


    /**
     * Funzione che ritorna i dati per la generazione degli open data
     *
     * @param $type {la tipologia per cui filtrare i dati}
     * @return mixed
     * @throws Exception
     */
    private function getData($type = null): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        return ChargesModel::where('type', $type)
            ->with('attachs')
            ->with('proceedings:id,name')
            ->with('measures:id,object,type')
            ->with('regulations:id,title')
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

        $fileName = 'Oneri Informativi - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Per i cittadini',
            'Per imprese',
            'Denominazione',
            'Data di scadenza',
            'Descrizione',
            'Procedimenti associati',
            'Provvedimenti associati',
            'Regolamenti o altra documentazione associata',
            'Maggiori informazioni',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno i procedimenti
            $proceedingsList = '';
            if (!empty($r['proceedings'])) {
                foreach ($r['proceedings'] as $proceeding) {

                    $proceedingsList .= $proceeding['name'] . ' - [Url]: '
                        . siteUrl('page/98/details/' . $proceeding['id'] . '/' . urlTitle($proceeding['name'])) . "\n";

                }
            }

            // Concateno i provvedimenti
            $measuresList = '';
            if (!empty($r['measures'])) {

                foreach ($r['measures'] as $measure) {

                    $measuresList .= $measure['object'] . ' - [Url]: '
                        . siteUrl('page/9/details/' . $measure['id'] . '/' . urlTitle($measure['object'])) . "\n";

                }
            }

            // Concateno i regolamenti
            $regulationsList = '';
            if (!empty($r['regulations'])) {

                foreach ($r['regulations'] as $regulation) {

                    $regulationsList .= $regulation['title'] . ' - [Url]: '
                        . siteUrl('page/29/details/' . $regulation['id'] . '/' . urlTitle($regulation['title'])) . "\n";

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

            $data = [
                // Per Cittadini
                !empty($r['citizen']) ? 'Si' : null,
                // Per Imprese
                !empty($r['companies']) ? 'Si' : null,
                // Denominazione
                !empty($r['title']) ? $r['title'] : null,
                // Data di scadenza
                !empty($r['expiration_date']) ? date('d-m-Y', strtotime($r['expiration_date'])) : null,
                // Descrizione
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Procedimenti
                trim($proceedingsList),
                // Provvedimenti
                trim($measuresList),
                // Regolamenti
                trim($regulationsList),
                // Maggiori informazioni
                !empty($r['title']) ? $r['title'] : null,
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
