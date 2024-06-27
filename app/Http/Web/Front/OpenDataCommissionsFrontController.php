<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\CommissionsModel;
use Model\SectionsFoModel;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per le commissioni
 */
class OpenDataCommissionsFrontController extends BaseFrontController
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
     * Metodo download open data per le Commissioni,
     * nella sezione Titolari di incarichi politici, di amministrazione, di direzione o di governo->Commissioni
     * ID sezione 245
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexCommission(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('commissione');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }

    }

    /**
     * Metodo download open data per le Gruppi consiliari,
     * nella sezione Titolari di incarichi politici, di amministrazione, di direzione o di governo->Commissioni
     * ID sezione 244
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexGroup(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = $this->getData('gruppo consiliare');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }


    /**
     * Funzione che ritorna i dati per generare gli open data
     *
     * @param $type {la tipologia per cui filtrare i dati}
     * @return mixed
     * @throws Exception
     */
    private function getData($type = null): mixed
    {
        // Recupero le commissioni da scaricare come OpenData
        return CommissionsModel::where('typology', $type)
            ->where('archived', '!=', 1)
            ->with('president:id,full_name')
            ->with('secretaries:id,full_name,archived')
            ->with('members:id,full_name,archived')
            ->with('attachs')
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

        $fileName = 'Commissioni e gruppi consiliari - ' . $resultSection['name'] . '.csv';
//        header('Set-Cookie: fileDownload=true; path=/; SameSite=None; Secure');
//        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
//        header('Content-Type: text/csv; charset= utf-8');
//        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        setFileDownloadCookieCSV($fileName);
        $output = fopen("php://output", 'w');

        // Intestazione indice dei dati...
        $headers = array(
            'Nome',
            'Tipologia',
            'Presidente o capogruppo',
            'Segretari',
            'Descrizione',
            'Membri',
            'Immagine associata',
            'Telefono',
            'Fax',
            'Indirizzo',
            'Email',
            'Url origine ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        $patInfo = patOsInstituteInfo(['short_institution_name']);

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            $president = !empty($r['president'])
                ? $r['president']['full_name'] . ' - ' . siteUrl('page/4/details/' . $r['president']['id'] . '/' . urlTitle($r['president']['full_name']))
                : null;

            // Concateno gli incarichi
            $secretariesLists = '';
            if (!empty($r['secretaries'])) {
                foreach ($r['secretaries'] as $secretary) {

                    $secretariesLists .= $secretary['full_name'] . ' - [Url]: '
                        . siteUrl('page/4/details/' . $secretary['id'] . '/' . urlTitle($secretary['full_name'])) . "\n";

                }
            }

            // Concateno le strutture
            $membersLists = '';
            if (!empty($r['members'])) {
                foreach ($r['members'] as $member) {

                    $membersLists .= $member['full_name'] . ' - [Url]: '
                        . siteUrl('page/4/details/' . $member['id'] . '/' . urlTitle($member['full_name'])) . "\n";

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
                // Nome commissione
                !empty($r['name']) ? $r['name'] : null,
                // Tipologia
                !empty($r['typology']) ? $r['typology'] : null,
                // Presidente o capogruppo della commissione
                $president,
                // Segretari
                trim($secretariesLists),
                // Descrizione
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])) : null,
                // Membri
                trim($membersLists),
                //  Immagine associata
                baseUrl('media/' . instituteDir($patInfo['short_institution_name']) . '/assets/images/' . $r['image']),
                //  Telefono
                !empty($r['phone']) ? $r['phone'] : null,
                // Fax
                !empty($r['fax']) ? $r['fax'] : null,
                // Indirizzo
                !empty($r['address']) ? $r['address'] : null,
                // Email
                !empty($r['email']) ? $r['email'] : null,
                // Url origine record
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['name'])),
                // Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
