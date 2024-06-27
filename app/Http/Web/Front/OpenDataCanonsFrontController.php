<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\LeaseCanonsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i canoni di locazione
 */
class OpenDataCanonsFrontController extends BaseFrontController
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
     * Metodo download open data per i Canoni di locazione,
     * nella sezione Beni immobili e gestione patrimonio -> Canoni di locazione o affitto->Canoni di locazione
     * o affitto percepiti
     * ID sezione 136
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPerceived(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData(2);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Canoni di locazione,
     * nella sezione Beni immobili e gestione patrimonio->Canoni di locazione o affitto->Canoni di locazione o affitto versati
     * ID sezione 137
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPaid(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData(1);

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

        //Modifico il formato dell'importo per renderlo conforme a quello del db
        if (Input::get('am') != null) {
            $formattedAmount = str_replace('.', '', Input::get('am'));
            $formattedAmount = str_replace(',', '.', $formattedAmount);
        } else {
            $formattedAmount = null;
        }
        // Recupero i canoni di locazione percepiti da mostrare
        return LeaseCanonsModel::where('canon_type', $type)
            ->with('properties:id,name')
            ->with('structure:id,structure_name')
            ->with('attachs')
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

        $fileName = 'Canoni di locazione - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Tipo canone',
            'Informazioni sul beneficiario',
            'Partita IVA/codice fiscale beneficiario',
            'Importo',
            'Estremi del contratto',
            'Immobile',
            'Ufficio referente per il contratto',
            'Data inizio',
            'Data fine',
            'Note',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');


        $canonType = [1 => 'Canoni di locazione o di affitto versati', 2 => 'Canoni di locazione o di affitto percepiti'];

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno gli immobili
            $realEstateAssetsList = '';
            if (!empty($r['properties'])) {

                foreach ($r['properties'] as $property) {
                    $realEstateAssetsList .= $property['name'] . ' - [Url]: '
                        . siteUrl('page/133/details/' . $property['id'] . '/' . urlTitle($property['name'])) . "\n";
                }
            }

            $structure = !empty($r['structure'])
                ? $r['structure']['structure_name'] . ' - [Url]: ' . siteUrl('page/40/details/' . $r['structure']['id'] . '/' . urlTitle($r['structure']['structure_name']))
                : '';

            //Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {

                foreach ($r['attachs'] as $attach) {

                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";

                }
            }

            $data = [
                // Tipo canone
                !empty($r['canon_type']) ? $canonType[$r['canon_type']] : null,
                // Informazioni sul beneficiario
                !empty($r['beneficiary']) ? $r['beneficiary'] : null,
                // Partita IVA/codice fiscale beneficiario
                !empty($r['fiscal_code']) ? $r['fiscal_code'] : null,
                // Importo
                !empty($r['amount']) ? '€ ' . S::currency($r['amount'], 2, ',', '.') : null,
                // Estremi del contratto
                !empty($r['contract_statements']) ? $r['contract_statements'] : null,
                // Immobile
                trim($realEstateAssetsList),
                // Ufficio referente per il contratto
                trim($structure),
                // Data inizio
                !empty($r['start_date']) ? date('d-m-Y', strtotime($r['start_date'])) : null,
                // Data fine
                !empty($r['end_date']) ? date('d-m-Y', strtotime($r['end_date'])) : null,
                // Descrizione
                !empty($r['notes']) ? S::chartsEntityDecode(S::stripTags($r['notes'])) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($canonType[$r['canon_type']])),
                // Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
