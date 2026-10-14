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
use Model\SectionFoPoliticOrganModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per il personale
 */
class OpenDataPersonnelFrontController extends BaseFrontController
{

    protected array $validation;

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
            ->with('political_organ')
            ->join('rel_personnel_public_in as public_in', 'public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('public_in.public_in_id', 238)
            ->orderBy('public_in.updated_at', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
            ->get();

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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
            ->with('political_organ')
            ->join('rel_personnel_public_in as public_in', 'public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('public_in.public_in_id', 239)
            ->orderBy('public_in.updated_at', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
            ->get();

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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
            ->with('political_organ')
            ->join('rel_personnel_public_in as public_in', 'public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('public_in.public_in_id', 242)
            ->orderBy('public_in.updated_at', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
            ->get();

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

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

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }
    }

    public function indexPositionHolders(){


        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        $queryContents = PersonnelModel::select(['object_personnel.*']) //non rimuovere
            ->where(function ($query) {
            })
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
            ->filter(
                (Input::get('n', true)),
            )
            ->where(function ($query){
                //tutti coloro che sono associati a un organo politico o che hanno pubblica in questa pagina.
                $query->whereHas('political_organ')
                    ->orWhere('rel_personnel_public_in.public_in_id', '246');
            })
            ->with('political_organ')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('role:id,name')
            ->groupBy('object_personnel.id')
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->get();

        if (!empty($querySection) && !$queryContents->isEmpty()) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            // Creo il csv
            $this->generateCsv($resultContests, $resultSection);

        }


    }


    public function politicsPage()
    {
        $currentPageId = (int)uri()->segment(3, 0);

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', $currentPageId)
            ->first();

        $politicOrganInfo = SectionFoPoliticOrganModel::where('section_fo_id', $currentPageId)->first();

        if (!empty($querySection) && !empty($politicOrganInfo)) {

            $relationInfo = SectionsFoModel::select(['id'])->where('id', $currentPageId )
                ->with('management_structure:id,structure_name,archived')
                ->with('non_management_structure:id,structure_name,archived')
                ->with('politic_personnel.role')
                ->first()
                ->toArray();

            $resultSection = $querySection->toArray();

            // Creo il csv
            $this->generatePoliticsPageCsv($politicOrganInfo, $resultSection,$relationInfo);

        }
    }

    /**
     * Metodo che restituisce i dati del personale per generare gli open data
     * @param null $publicIn      {ID sezioni}
     * @param bool $list          {indica se filtrare i record in base all'archiviazione e al campo utilizza negli elenchi del personale}
     * @param bool $notIndefinite {indica se filtrare i record per contratto a tempo non indeterminato}
     * @return mixed
     */
    protected function getDataForPublicIn($publicIn = null, bool $list = false, bool $notIndefinite = false): mixed
    {

        // Recupero il personale per gli open data da generare
        return PersonnelModel::where(function ($query) {
            })
            // Filtro in base al pubblica in
            ->when($publicIn, function ($query, $publicIn) {
                $query->whereHas('public_in_filter', function ($query) use ($publicIn) {
                    $query->where('rel_personnel_public_in.public_in_id', '=', $publicIn);
                });
            })
            ->when($list, function ($query) {
                $query->where('personnel_lists', 1)
                    ->where(function ($query) {
                    });
            })
            ->when($notIndefinite, function ($query) {
                $query->where('determined_term', 1);
            })
            ->filter(
                (Input::get('n', true)),
            )
            ->with('role:id,name')
            ->with('referent_structures:id,structure_name')
            ->with('assignments:id,name,object')
            ->with('political_organ')
            ->with('attachs')
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
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
    protected function generateCsv($resultContests = null, $resultSection = null): void
    {

        $fileName = 'Referenti - ' . $resultSection['name'] . '.csv';
//        header('Set-Cookie: fileDownload=true; path=/; SameSite=None; Secure');
//        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
//        header('Content-Type: text/csv; charset= utf-8');
//        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        setFileDownloadCookieCSV($fileName);
        $output = fopen("php://output", 'w');

        $headers = array(
            'Titolo',
            'Nome completo referente (cognome - nome)',
            'Ruolo',
            'Incarichi associati',
            'Strutture organizzative di appartenenza',
            'Incarico di stampo politico',
            'Organo politico amministrativo o di gestione',
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
                $politicalOrgansLists = implode(",",array_map(fn($item) => $this->getSectionName($item['section_fo_id']), $r['political_organ']));
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


    protected function getSectionName($sectionID = 0)
    {

        if(empty($sectionID)){
            return "";
        }

        $name = SectionsFoModel::select(['id', 'name'])->where('id', $sectionID)->first();

        if(!empty($name)){
            return $name->toArray()['name'];
        }
        return "";

    }

    private function generatePoliticsPageCsv($politicOrganInfo, $resultSection, $relationInfo)
    {

        $fileName = 'Organo.csv';

        setFileDownloadCookieCSV($fileName);
        $output = fopen("php://output", 'w');

        $headers = array(
            'Denominazione',
            'Competenze',
            'Uffici dirigenziali',
            'Uffici non dirigenziali',
            'Motivazione assenza uffici',
            'Url origine',
        );
        fputcsv($output, $headers, ';', '"');

        $politicOrganInfo = $politicOrganInfo->toArray();

        $management_structure = '';
        if(!empty($relationInfo['management_structure'])){
            foreach ($relationInfo['management_structure'] as $str){

                $link = !empty($str['archived']) ?
                    escapeXss($str['structure_name'])." [Elemento archiviato]" :
                    '<a target="_blank" href="' . siteUrl('page/40/details/' . $str['id'] . '/' . urlTitle($str['structure_name'])) . '">'.escapeXss($str['structure_name']).'</a>';

                $management_structure.= $link.", ";
            }

            $management_structure = rtrim($management_structure, ", ");

        }

        $non_management_structure = '';
        if(!empty($relationInfo['non_management_structure'])){
            foreach ($relationInfo['non_management_structure'] as $str){

                $link = !empty($str['archived']) ?
                    escapeXss($str['structure_name'])." [Elemento archiviato]" :
                    '<a target="_blank" href="' . siteUrl('page/40/details/' . $str['id'] . '/' . urlTitle($str['structure_name'])) . '">'.escapeXss($str['structure_name']).'</a>';

                $non_management_structure.= $link.", ";
            }
            $non_management_structure = rtrim($non_management_structure, ", ");
        }

        // Genero l'indice dell'array
        $data = [
            // Titolo accademico
            !empty($politicOrganInfo['denomination']) ? $politicOrganInfo['denomination'] : null,
            // Nome completo
            !empty($politicOrganInfo['skills']) ? S::chartsEntityDecode(S::stripTags($politicOrganInfo['skills'])) : null,
            $management_structure,
            $non_management_structure,
            //Motivazione assenza uffici
            !empty($politicOrganInfo['absence_office_reason']) ? S::stripTags($politicOrganInfo['absence_office_reason']) : null,
            // Url origine dato
            siteUrl('page/' . $resultSection['id'] . '/organo.html'),

        ];
        fputcsv($output, $data, ';', '"');

        fclose($output);
    }
}
