<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\GeneralActsDocumentsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per Procedure Banca Dati Nazionale Contratti Pubblici
 */
class OpenDataGeneralActsDocumentsFrontController extends BaseFrontController
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

            setFileDownloadCookie();

        }
    }

    /**
     * Metodo download open data per le Procedure Banca Dati Nazionale Contratti Pubblici,
     * nella sezione Bandi di Gara e Contratti
     * ID sezione 66
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

        if (!empty($querySection)) {

            $resultSection = $querySection->toArray();

            // Recupero i bandi di concorso attivi
            $queryContents = $this->getData($resultSection['id']);

            $resultContests = $queryContents->toArray();

            if(!empty($queryContents)) {
                $this->generateCsv($resultContests, $resultSection);
            }
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
        //Procedure di gara
        $fileName = $resultSection['name'] . '.csv';
        setFileDownloadCookieCSV($fileName);

        $output = fopen("php://output", 'w');

        //Setto le colonne
        $headers = array(
            'OGGETTO',
            'DATA DOCUMENTO',
            'LINK ESTERNO',
            'NOTE'
        );

        if($resultSection['id'] == 583) {
            $headers [] = 'TIPOLOGIA';
        } elseif ($resultSection['id'] == 586) {
            $headers [] = 'CUP';
            $headers [] = 'DATA AVVIO';
            $headers [] = 'IMPORTO FINANZIAMENTO';
            $headers [] = 'FONTI FINANZIARIE';
            $headers [] = 'STATO DI ATTUAZIONE PROCEDURALE';
        }

        $headers [] = 'ALLEGATI';
        $headers [] = 'ULTIMA MODIFICA';

        fputcsv($output, $headers, ';', '"');

        if (!empty($resultContests)) {

            $typologies = [
                'lavori' => 'Lavori pubblici, per assenza di lavori',
                'acquisti' => 'Acquisti di forniture e servizi, per assenza di acquisti di forniture e servizi',
            ];

            foreach ($resultContests as $record) {
                $actDate = !empty($record['document_date']) ? (date('d-m-Y', strtotime($record['document_date']))) : '';

                $startDate = !empty($record['start_date']) ? date('d-m-Y', strtotime($record['start_date'])) : '';

                $updateAt = !empty($record['updated_at']) ? date('d-m-Y', strtotime($record['updated_at'])) : '';

                $attachLists = '';
                if (!empty($record['attachs'])) {

                    foreach ($record['attachs'] as $attach) {

                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";

                    }
                }

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    !empty($record['object']) ? escapeXss($record['object']) : '',
                    $actDate,
                    !empty($record['external_link']) ? escapeXss($record['external_link']) : '',
                    !empty($record['notes']) ? $record['notes'] : ''
                ];

                if($resultSection['id'] == 583) {
                    $data [] = !empty($record['typology']) ? $typologies[$record['typology']] ?? '' : '';
                } elseif ($resultSection['id'] == 586) {
                    $data [] = !empty($record['cup']) ? escapeXss($record['cup']) : '';
                    $data [] = $startDate;
                    $data [] = !empty($record['financing_amount']) ? '€ ' . escapeXss(S::currency($record['financing_amount'], 2, ',', '.')) : '';
                    $data [] = !empty($record['financial_sources']) ? escapeXss($record['financial_sources']) : '';
                    $data [] = !empty($record['procedural_implementation_status']) ? escapeXss($record['procedural_implementation_status']) : '';
                }

                $data [] = trim($attachLists);
                $data [] = $updateAt;

                fputcsv($output, $data, ';', '"');
            }
            fclose($output);
        }
    }

    /**
     * Funzione che ritorna i dati per la generazione degli open data
     *
     * @param $section {le sezioni pubblica in per cui filtrare i dati}
     * @return mixed
     * @throws Exception
     */
    private function getData(int $section = null): mixed
    {
        // Recupero gli atti
        return GeneralActsDocumentsModel::whereHas('public_in_section', function ($query) use ($section) {
            $query->where('public_in_id', $section);
        })
            ->with('attachs')
            ->orderBy('document_date', 'DESC')
            ->orderBy('object', 'ASC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();
    }
}
