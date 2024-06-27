<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per le strutture organizzative
 */
class OpenDataStructuresFrontController extends BaseFrontController
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
     * Metodo chiamato per il download degli open data per "Strutture organizzative",
     * nella sezione Articolazione degli uffici, Telefono e posta elettronica
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

        // Recupero le strutture
        $queryContents = $this->getData();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo chiamato per la pagina "Posta elettronica certificata"
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCertifiedMail(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero le strutture
        $queryContents = $this->getData(true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);
        }
    }

    /**
     * Metodo che restituisce le strutture di cui esportare gli open data
     * @param bool $certified {indica se si deve inserire il controllo sul campo posta certificata nella query}
     * @return mixed
     */
    private function getData(bool $certified = false): mixed
    {

        // Recupero le strutture
        $query = StructuresModel::where('articulation', 1)
            ->where(function ($query) {
                $query->where('archived', '!=', 1)
                    ->orWhereNull('archived');
            });

        // Se sono nella sezione Posta elettronica certificata controllo il campo relativo
        if ($certified) {
            $query->whereNotNull('certified_email');
        }

        $query->with('structure_of_belonging:id,structure_name')
            ->with('responsibles:id,full_name')
            ->with('to_contact:id,full_name')
            ->with('attachs')
            ->orderBy('structure_name', 'ASC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'));

        return $query->get();
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

        $fileName = 'Strutture organizzative - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Nome struttura',
            'Struttura di appartenenza',
            'Responsabili',
            'Personale da contattare',
            'Posta elettronica',
            'Email certificate',
            'Telefono',
            'Fax',
            'Descrizione attività',
            'Orari al pubblico',
            'url origine struttura organizzativa',
            'allegati'
        );
        fputcsv($output, $headers, ';', '"');


        foreach ($resultContests as $r) {

            // Concateno i responsabili
            $responsiblesLists = '';
            if (!empty($r['responsibles'])) {

                foreach ($r['responsibles'] as $resp) {
                    if(!empty($resp['full_name'])){
                        $responsiblesLists .= $resp['full_name'] . ' - [Url]: '
                            . siteUrl('page/58/details/' . $resp['id'] . '/' . urlTitle($resp['full_name'])) . "\n";
                    }
                }
            }

            // Concateno il personale da contattare
            $toContactLists = '';
            if (!empty($r['to_contact'])) {

                foreach ($r['to_contact'] as $contact) {

                    if(!empty($contact['full_name'])){
                        $toContactLists .= $contact['full_name'] . ' - [Url]: '
                            . siteUrl('page/58/details/' . $contact['id'] . '/' . urlTitle($contact['full_name'])) . "\n";
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
                //Tipo
                !empty($r['structure_name']) ? $r['structure_name'] : null,
                //Struttura di appartenenza
                !empty($r['structure_of_belonging']) ? $r['structure_of_belonging']['structure_name'] : null,
                //Responsabili della struttura
                trim($responsiblesLists),
                //Personale da contattare
                trim($toContactLists),
                //Posta elettronica
                !empty($r['reference_email']) ? $r['reference_email'] : null,
                //Email certificata
                !empty($r['certified_email']) ? $r['certified_email'] : null,
                //Telefono
                !empty($r['phone']) ? $r['phone'] : null,
                //Fax
                !empty($r['fax']) ? $r['fax'] : null,
                //Descrizione delle attività
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) :null,
                //Orari al pubblico
                !empty($r['timetables']) ? $r['timetables'] : null,
                //Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['structure_name'])),
                //Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
