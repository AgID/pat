<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\ReliefChecksModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i Controlli e rilievi
 */
class OpenDataReliefChecksController extends BaseFrontController
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
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Organismi indipendenti di valutazione, nuclei di valutazione
     * o altri organismi con funzioni analoghe
     * ID sezione 139
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexIndependentOrganisms(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('139');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Organi di revisione amministrativa e contabile
     * ID sezione 144
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexReviewOrganisms(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('144');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Corte dei conti
     * ID sezione 145
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexCourtOfAuditors(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('145');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Attestazione dell'OIV o di altra struttura analoga
     * nell'assolvimento degli obblighi di pubblicazione
     * ID sezione 140
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexOIVCertification(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('140');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Documento dell'OIV di validazione della Relazione
     * sulla Performance
     * ID sezione 141
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexOIVDocument(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('141');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Relazione dell'OIV sul funzionamento complessivo del
     * Sistema di valutazione trasparenza e integrità dei controlli interni
     * ID sezione 142
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexOIReport(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('142');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Altri atti degli organismi indipendenti di valutazione,
     * nuclei di valutazione o altri organismi con funzioni analoghe
     * ID sezione 143
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexOtherActs(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('143');

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
        return ReliefChecksModel::with('office:id,structure_name')
            ->with('attachs')
            ->when($type, function ($query, $type) {
                $query->whereHas('public_in_filter', function ($query) use ($type) {
                    $query->where('rel_relief_check_public_in.public_in_id', '=', $type);
                });
            })
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

        $fileName = 'Controlli e rilievi - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Oggetto',
            'Data',
            'Ufficio',
            'Descrizione',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            $structure = !empty($r['office']['structure_name'])
                ? $r['office']['structure_name'] . ' - [Url]: ' . siteUrl('page/40/details/' . $r['office']['id'] . '/' . urlTitle($r['office']['structure_name']))
                : '';

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
                // Tipo canone
                !empty($r['object']) ? $r['object'] : null,
                // Data
                !empty($r['date']) ? date('d-m-Y', strtotime($r['date'])) : null,
                // Ufficio referente per il contratto
                trim($structure),
                // Descrizione
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                // Allegati
                trim($attachLists)
            ];
            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
