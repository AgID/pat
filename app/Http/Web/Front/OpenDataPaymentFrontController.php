<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\PaymentModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i tassi di assenza
 */
class OpenDataPaymentFrontController extends BaseFrontController
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
        $queryContents = PaymentModel::with('structure:id,structure_name')
            ->filter(
                (int)Input::get('year', true),
                (int)Input::get('category', true),
                Input::get('typology', true),
                Input::get('trimester', true),
                Input::get('supplier', true),
            )
            ->with('attachs')
            ->orderBy('year', 'DESC')
            ->orderBy('trimester', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
            ->get();

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $details = false;

            $this->generateCsv($resultContests, $resultSection, $details);

        }

    }


    /**
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection  {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    private function generateCsv($resultContests = null, $resultSection = null, $details = true): void
    {

        //tassi di assenza
        $fileName = $resultSection['name'] . '.csv';

        setFileDownloadCookieCSV($fileName);
        $output = fopen("php://output", 'w');

        $headers = array(
            1 => 'Oggetto',
            2 => 'Beneficiario',
            3 => 'Anno',
            4 => 'Trimestre',
            5 => 'Importo',
            6 => 'Categoria',
            7 => 'Tipologia',
            8 => 'Note',
            9 => 'url origine ' . $resultSection['name'],
            10 => 'Allegati'
        );

        if(!$details){
            unset($headers[1]);
            unset($headers[8]);
            unset($headers[9]);
            unset($headers[10]);
        }

        fputcsv($output, $headers, ';', '"');

        $typologies = config('paymentTypologyCorrenti', [], 'app') + config('paymentTypologyCapitale', [], 'app');

        $categories = [ 1 => 'Uscite correnti', 2=>'Uscite in conto capitale'];

        $paymentSupplier = config('paymentSupplierTypology', [], 'app');

        foreach ($resultContests as $r) {
            //Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {
                foreach ($r['attachs'] as $attach) {
                    $attachLists .= '(' . $attach['label'] . ') - Url: ' . siteUrl('/download/' . $attach['id']) . "\n";
                }
            }

            $data = [
                1 =>  !empty($r['object']) ? $r['object'] : null,
                2 =>  ($paymentSupplier[$r['supplier']] ?? ''),
                3 =>  !empty($r['year']) ? $r['year'] : null,
                4 =>  !empty($r['trimester']) ? (string)$r['trimester'] : null,
                5 =>  !empty($r['compensation']) ? S::currency($r['compensation'], 2, ',', '.')  : null,
                6 =>  !empty($r['expense_category']) ? $categories[$r['expense_category']] : null,
                7 =>  !empty($r['expense_typology']) ? $typologies[$r['expense_typology']] : null,
                8 => !empty($r['notes']) ? $r['notes'] : null,
                9 => siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/pagamento.html'),
                10 => trim($attachLists)
            ];

            if(!$details){
                unset($data[1]);
                unset($data[8]);
                unset($data[9]);
                unset($data[10]);
            }

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
