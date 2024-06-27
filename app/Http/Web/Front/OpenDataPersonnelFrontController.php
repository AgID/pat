<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\PersonnelModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per il personale
 */
class OpenDataPersonnelFrontController extends BaseFrontController
{

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
     * Metodo download open data per il Personale,
     * nella sezione del Sindaco
     * ID sezione 238
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexMayor(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del sindaco per generare gli open data
        $queryContents = PersonnelModel::select(['object_personnel.*'])
            ->with('role:id,name')
            ->with('referent_structures:id,structure_name')
            ->with('assignments:id,object,name')
            ->with('measures:id,object')
            ->with('attachs')
            ->with('political_organ:id,object_personnel_id,political_organ_id')
            ->join('rel_personnel_public_in as public_in', 'public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('public_in.public_in_id', 238)
            ->orderBy('public_in.updated_at', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);
        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione del Vicesindaco
     * ID sezione 239
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexDeputyMayor(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del vicesindaco per generare gli open data
        $queryContents = PersonnelModel::select(['object_personnel.*', 'public_in.id as pId'])
            ->with('role:id,name')
            ->with('referent_structures:id,structure_name')
            ->with('assignments:id,name,object')
            ->with('measures:id,object')
            ->with('attachs')
            ->with('political_organ:id,object_personnel_id,political_organ_id')
            ->join('rel_personnel_public_in as public_in', 'public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('public_in.public_in_id', 239)
            ->orderBy('public_in.updated_at', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione del Presidente del consiglio comunale
     * ID sezione 242
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPresidentCityCouncil(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del presidente del consiglio comunale per generare gli open data
        $queryContents = PersonnelModel::select(['object_personnel.*', 'public_in.id as pId'])
            ->with('role:id,name')
            ->with('referent_structures:id,structure_name')
            ->with('assignments:id,name,object')
            ->with('measures:id,object')
            ->with('attachs')
            ->with('political_organ:id,object_personnel_id,political_organ_id')
            ->join('rel_personnel_public_in as public_in', 'public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('public_in.public_in_id', 242)
            ->orderBy('public_in.updated_at', 'DESC')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Dotazione organica
     * ID sezione 64
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexStaffing(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale per generare gli open data
        $queryContents = $this->getDataForPublicIn(null, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Personale non a tempo indeterminato
     * ID sezione 65
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexNotIndefinite(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale non a tempo indeterminato per generare gli open data
        $queryContents = $this->getDataForPublicIn(null, true, true);

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Titolari di incarichi dirigenziali (dirigenti non generali)
     * ID sezione 60
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexManagerialPositions(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('60');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Posizioni organizzative
     * ID sezione 63
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexOrganisationalPositions(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('63');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Titolari di incarichi dirigenziali amministrativi di vertice
     * ID sezione 58
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexTopPositions(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('58');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Organizzazione->Titolari di incarichi politici, di amministrazione, di direzione o di governo->
     * Giunta e assessori
     * ID sezione 240
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexCouncilAndCouncillors(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('240');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Organizzazione->Titolari di incarichi politici, di amministrazione, di direzione o di governo->Consiglio Comunale
     * ID sezione 241
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexCityCouncil(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('241');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Organizzazione->Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * ID sezione 246
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexHoldersOfAdministrativePositions(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('246');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Personale->Titolari di incarichi dirigenziali amministrativi di vertice->Segretario Generale
     * ID sezione 59
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexGeneralSecretary(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('59');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo download open data per il Personale,
     * nella sezione Organizzazione->Titolari di incarichi politici, di amministrazione, di direzione o di governo->Direzione Generale
     * ID sezione 243
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexGeneralManagement(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i dati del personale, in base al pubblica in, per generare gli open data
        $queryContents = $this->getDataForPublicIn('243');

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo che restituisce i dati del personale per generare gli open data
     * @param null $publicIn      {ID sezioni}
     * @param bool $list          {indica se filtrare i record in base all'archiviazione e al campo utilizza negli elenchi del personale}
     * @param bool $notIndefinite {indica se filtrare i record per contratto a tempo non indeterminato}
     * @return mixed
     */
    private function getDataForPublicIn($publicIn = null, bool $list = false, bool $notIndefinite = false): mixed
    {

        // Recupero il personale per gli open data da generare
        return PersonnelModel::where('archived', '!=', 1)
            // Filtro in base al pubblica in
            ->when($publicIn, function ($query, $publicIn) {
                $query->whereHas('public_in_filter', function ($query) use ($publicIn) {
                    $query->where('rel_personnel_public_in.public_in_id', '=', $publicIn);
                });
            })
            ->when($list, function ($query) {
                $query->where('personnel_lists', 1)
                    ->where('archived', '!=', 1);
            })
            ->when($notIndefinite, function ($query) {
                $query->where('determined_term', 1);
            })
            ->with('role:id,name')
            ->with('referent_structures:id,structure_name')
            ->with('assignments:id,name,object')
            ->with('political_organ:id,object_personnel_id,political_organ_id')
            ->with('attachs')
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
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

        $fileName = 'Referenti - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        $headers = array(
            'Titolo',
            'Nome completo referente (cognome - nome)',
            'Ruolo',
            'Incarichi associati',
            'Strutture organizzative di appartenenza',
            'Incarico di stampo politico',
            'Organo politico-amministrativo',
            'Atto di nomina o proclamazione',
            'Telefono fisso',
            'Telefono mobile',
            'Fax',
            'Email',
            'Email certificata',
            'Note',
            'Compensi',
            'Importi di viaggi di servizi e missioni',
            'Altri incarichi con oneri a carico della finanza pubblica e relativi compensi',
            'In carica da',
            'In carica fino a',
            'url origine ' . $resultSection['name'],
            'allegati'
        );
        fputcsv($output, $headers, ';', '"');

        foreach ($resultContests as $r) {
            // Concateno gli incarichi

            $assignmentsLists = '';
            if (!empty($r['assignments'])) {
                foreach ($r['assignments'] as $assignment) {
                    if(!empty($assignment['name'])){
                        $assignmentsLists .= $assignment['name'] . ' - [Url]: '
                            . siteUrl('page/3/details/' . $assignment['id'] . '/' . urlTitle($assignment['name'])) . "\n";
                    }
                }
            }

            // Concateno le strutture
            $structuresLists = '';
            if (!empty($r['referent_structures'])) {

                foreach ($r['referent_structures'] as $structure) {
                    if(!empty($structure['structure_name'])){
                        $structuresLists .= $structure['structure_name'] . ' - [Url]: '
                            . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . "\n";
                    }
                }
            }

            // Concateno gli organi politici
            $politicalOrgansLists = '';
            if (!empty($r['political_organ'])) {

                $organs = config('politicalAdministrative', null, 'app');

                foreach ($r['political_organ'] as $politicalOrgan) {
                    if(array_key_exists($politicalOrgan['political_organ_id'], $organs)) {
                        $politicalOrgansLists .= $organs[$politicalOrgan['political_organ_id']] . "\n";
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
                // Titolo accademico
                !empty($r['title']) ? $r['title'] : null,
                // Nome completo
                !empty($r['full_name']) ? $r['full_name'] : null,
                // Ruolo
                !empty($r['role']['name']) ? $r['role']['name'] : null,
                // Incarichi associati
                !empty ($assignmentsLists) ? trim($assignmentsLists) : null,
                // Strutture di appartenenza
                !empty ($structuresLists)  ? trim($structuresLists) : null,
                // Incarico di stampo politico
                !empty($r['political_role']) ? $r['political_role'] : null,
                //  Organo politico-amministrativo
                !empty($politicalOrgansLists) ? trim($politicalOrgansLists) : null,
                //  Atto di nomina o proclamazione
                !empty($r['extremes_of_conference']) ? $r['extremes_of_conference'] : null,
                // Telefono fisso
                !empty($r['phone']) ? $r['phone'] : null,
                // Telefono mobile
                !empty($r['mobile_phone']) ? $r['mobile_phone'] : null,
                // Fax
                !empty($r['fax']) ? $r['fax'] : null,
                // Email
                !empty($r['email']) ? $r['email'] : null,
                // Email certificata
                !empty($r['certified_email']) ? $r['certified_email'] : null,
                // Note
                !empty($r['notes']) ? S::chartsEntityDecode(S::stripTags($r['notes'])) : null,
                //  Compensi
                !empty($r['compensations']) ? S::chartsEntityDecode(S::stripTags($r['compensations'])) : null,
                // Importi di viaggi di servizi e missioni
                !empty($r['trips_import']) ? S::chartsEntityDecode(S::stripTags($r['trips_import'])) : null,
                // Altri incarichi con oneri a carico della finanza pubblica e relativi compensi
                !empty($r['other_assignments']) ? S::chartsEntityDecode(S::stripTags($r['other_assignments'])) : null,
                // In carica dal
                !empty($r['in_office_since']) ? convertDateForCsv($r['in_office_since']) : null,
                // In carica fino al
                !empty($r['in_office_until']) ? convertDateForCsv($r['in_office_until']) : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['full_name'])),
                // Allegati
                !empty ($attachLists) ? trim($attachLists) : null,
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
