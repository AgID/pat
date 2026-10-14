<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\NormativesModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per le Normative
 */
class OpenDataNormativeFrontController extends BaseFrontController
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
     * Metodo download open data per le normative,
     * nella sezione Sovvenzioni, Contributi, Sussidi, Vantaggi economici -> Criteri e modalità
     * ID sezione 125
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexCriteriaAndModalities(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData([2]);

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests,$resultSection);

        }
    }


    /**
     * Metodo download open data per le normative,
     * nella sezione Disposizioni Generali->Atti generali->Riferimenti normativi su organizzazione e attività
     * ID sezione 24
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexReferencesOnOrganizationAndActivities(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData([1,4,50,51,52]);

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests,$resultSection);

        }
    }

    /**
     * Funzione che ritorna i dati per la generazione degli open data
     *
     * @param $topic {tipo di normativa}
     * @return mixed
     * @throws Exception
     */
    private function getData(array $topic = []): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        return NormativesModel::whereIn('normative_topic', $topic)
            ->filter(
                (Input::get('norm', true)),
                (int)Input::get('t', true),
                (int)Input::get('n', true),
                Input::get('s', true),
                Input::get('e', true),
                (int)Input::get('u', true)
            )
            ->with('attachs')
            ->with('structures:id,structure_name')
            ->orderBy('issue_date', 'DESC')
            ->orderBy('name', 'ASC')
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

        $fileName = 'Normative - ' . $resultSection['name'] . '.csv';
//        header('Set-Cookie: fileDownload=true; path=/; SameSite=None; Secure');
//        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
//        header('Content-Type: text/csv; charset= utf-8');
//        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        setFileDownloadCookieCSV($fileName);
        $output = fopen("php://output", 'w');

        $headers = array(
            'Tipologia Atto',
            'Numero',
            'Protocollo',
            'Data Promulgazione',
            'Titolo della Norma',
            'Argomento della Normativa',
            'Link alla Normativa',
            'Valida per le strutture',
            'Descrizione',
            'Url Origine - ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        $typology = [null => ''] + config('normativeTypologies', [], 'app');

        $topic = [1 => 'Organizzazione dell\'Ente',
            2 => 'Sovvenzioni e contributi', 3 => 'Altro', 4=> 'Attività'];
        //Argomenti della normativa

        foreach ($resultContests as $r) {
            //Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {

                foreach ($r['attachs'] as $attach) {

                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";

                }
            }

            $structure = '';
            if(!empty($r['structures'])){
                foreach ($r['structures'] as $str) {
                    $structure.= $str['structure_name']."\n";
                }
            }

            $data = [
                //Tipologia Atto
                !empty($r['act_type']) ? $typology[$r['act_type']] : null,
                //Numero
                !empty($r['number']) ? $r['number'] : null,
                //Protocollo
                !empty($r['protocol']) ? $r['protocol'] : null,
                //Data promulgazione
                !empty($r['issue_date']) ? convertDateForCsv($r['issue_date']) : null,
                //Titolo della Norma
                !empty($r['name']) ? $r['name'] : null,
                // Argomento della Normativa
                !empty($r['normative_topic']) ? $topic[$r['normative_topic']] : null,
                // Link alla normativa
                !empty($r['normative_link']) ? $r['normative_link'] : null,
                //Valida per le strutture
                $structure,
                //Descrizione
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) :  null,
                //Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['name'])),
                //Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }

}
