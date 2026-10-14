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

            setFileDownloadCookie();

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }


    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Relazioni degli organi di revisione amministrativa
     * e contabile al bilancio di previsione o budget e alle relative variazioni
     * ID sezione 606
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexBudgetForecastReports(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('606');

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Relazioni degli organi di revisione amministrativa
     * e contabile al conto consuntivo o al bilancio di esercizio
     * ID sezione 607
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexFinancialStatementReports(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('607');

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per i Controlli e rilievi,
     * nella sezione Controlli e rilievi sull'amministrazione->Rilievi - recepiti e non - della Corte dei conti
     * riguardanti l'organizzazione e l'attività delle amministrazioni stesse e dei loro uffici
     * ID sezione 608
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexReviewOrganizationsOrganisms(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('608');

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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
        return ReliefChecksModel::filter(
            (int)Input::get('str', true),
            (Input::get('obj', true)),
            (int)Input::get('year', true)
        )
            ->with('office:id,structure_name')
            ->with('attachs')
            ->when($type, function ($query, $type) {
                $query->whereHas('public_in_filter', function ($query) use ($type) {
                    $query->where('rel_relief_check_public_in.public_in_id', '=', $type);
                });
            })
            ->orderBy('date', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
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
        setFileDownloadCookieCSV($fileName);
        $output = fopen("php://output", 'w');

        $headers = array(
            'Oggetto',
            'Data',
            'Oggetto del rilievo',
            'Ufficio',
            'Descrizione',
            'url origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        $reliefTaken = [1 => 'Recepito', 2 => 'Non recepito', 3 => 'Parzialmente recepito'];
        $actObject = [ 1 => 'Organizzazione', 2 => 'Attività'];

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

            $actObjectTxt = "";
            if(!empty($r['act_object'])){
                $opt = explode(",", $r['act_object']);
                foreach ($opt as $op){
                    $actObjectTxt .= $actObject[(int)$op].", ";
                }
                $actObjectTxt = rtrim($actObjectTxt, ', ');
            }

            // Genero l'indice dell'array
            $data = [
                !empty($r['object']) ? $r['object'] : null,
                !empty($r['date']) ? date('d/m/Y', strtotime($r['date'])) : null,
                $actObjectTxt,
                trim($structure),
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['object'])),
                trim($attachLists)
            ];
            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
