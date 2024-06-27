<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\CompanyModel;
use Model\SectionsFoModel;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per gli enti controllati
 */
class OpenDataCompanyFrontController extends BaseFrontController
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
     * Metodo download open data per gli Enti controllati,
     * nella sezione Enti controllati->Enti pubblici vigilati
     * ID sezione 89
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPublicEntities(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('ente pubblico vigilato');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);
        }
    }

    /**
     * Metodo download open data per gli Enti controllati,
     * nella sezione Enti controllati->Società partecipate
     * ID sezione 91
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexParticipatedCompanies(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('societa partecipata');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);
        }
    }

    /**
     * Metodo download open data per gli Enti controllati,
     * nella sezione Enti controllati->Enti di diritto privato controllati
     * ID sezione 93
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexControlledPrivateEntities(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('ente di diritto privato controllato');

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

        $fileName = 'Enti controllati - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Ragione sociale',
            'Tipologia',
            'Descrizione attività',
            'Misura di partecipazione',
            'Durata dell\'impegno',
            'Oneri complessivi per anno',
            'Rappresentanti negli organi di governo',
            'Incarichi amministrativi e relativo trattamento economico',
            'Indirizzo portale web',
            'Risultati di bilancio (ultimi 3 anni)',
            'Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell\'incarico',
            'Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell\'incarico',
            'url origine ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');


        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno i rappresentanti
            $representativesLists = '';
            if (!empty($r['representatives'])) {
                foreach ($r['representatives'] as $representative) {

                    $representativesLists .= $representative['full_name'] . ' - [Url]: '
                        . siteUrl('page/40/details/' . $representative['id'] . '/' . urlTitle($representative['full_name'])) . "\n";

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
                // Ragione sociale
                !empty($r['company_name']) ? $r['company_name'] : null,
                // Tipologia
                !empty($r['typology']) ? $r['typology'] : null,
                // Descrizione attività
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Misura di partecipazione
                !empty($r['participation_measure']) ? $r['participation_measure'] : null,
                // Durata dell'impegno
                !empty($r['duration']) ? $r['duration'] : null,
                // Oneri complessivi per anno
                !empty($r['year_charges']) ? S::chartsEntityDecode(S::stripTags($r['year_charges'])) : null,
                // Rappresentanti negli organi di governo
                trim($representativesLists),
                // Incarichi amministrativi e relativo trattamento economico
                !empty($r['treatment_assignments']) ? S::chartsEntityDecode(S::stripTags($r['treatment_assignments'])) : null,
                // Indirizzo portale web
                !empty($r['website_url']) ? $r['website_url'] : null,
                // Risultati di bilancio (ultimi 3 anni)
                !empty($r['balance']) ? S::chartsEntityDecode(S::stripTags($r['balance'])) :null,
                // Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell'incarico
                !empty($r['inconferability_dec_link']) ? $r['inconferability_dec_link'] : null,
                // Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell'incarico
                !empty($r['incompatibility_dec_link']) ? $r['incompatibility_dec_link'] : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['company_name'])),
                // Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
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
        return CompanyModel::where('archived', '!=', 1)
            ->where('typology', $type)
            ->with('attachs')
            ->with('representatives:id,full_name')
            ->get();
    }
}
