<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\PoliticOrganValidator;
use Model\RelSectionsFoPoliticOrganPersonnel;
use Model\RelSectionsFoPoliticOrganStructure;
use Model\SectionFoPoliticOrganModel;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Organi politici
 *
 */
class PoliticOrganAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @description Renderizza la pagina index dei Organi politici
     * @return void
     * @throws Exception
     * @url /admin/politic-organ.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Organi politici', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Organi politici';
        $data['subTitleSection'] = 'GESTIONE DEI ORGANI POLITICI';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/politic-organ';
        $data['formSettings'] = [
            'name' => 'form_politic-organ',
            'id' => 'form_politic-organ',
            'class' => 'form_politic-organ',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('politic_organ/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/politic-organ/list.html
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        //Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();
        $response = [];

        //Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                2 => 'updated_at'
            ];

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'updated_at');

            //Li prendo direttamente da section_fo, così all'attivazione non ho necessità di censirle
            $sections = getSections(605);
            $totalRecords = count($sections);
            $tmpSections = [];
            foreach ($sections as $s){
                if($s['id'] != 244 && $s['id'] != 245){ //commissioni e gruppi consiliari sono escluse -> gestite con l'archivio dedicato
                    $tmpSections[$s['id']] = $s;
                }
            }
            $sections = $tmpSections;
            unset($tmpSections);

            $response['draw'] = intval($dataTable['draw']);
            $response['iTotalRecords'] = ($totalRecords);
            $response['iTotalDisplayRecords'] = ($totalRecords);
            $response['aaData'] = [];

            $records = SectionFoPoliticOrganModel::select(['section_fo_politic_organ.*', 'i.full_name_institution'])
            ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'section_fo_politic_organ.institution_id', '=', 'i.id', 'left outer')
                ->get();
            $records = $records->isEmpty() ? [] : $records->toArray();

            if(!empty($records)){
                $tmpRecords = [];
                foreach ($records as $r){
                    $tmpRecords[$r['section_fo_id']] = $r;
                }
                $records = $tmpRecords;
                unset($tmpRecords);
            }

            if(!empty($sections)){

                $getIdentity = authPatOs()->getIdentity(['id']);
                $checkVl = false;
                $manageableStatesVl = [];
                $usInfo = null;

                foreach ($sections as $section){

                    $rowDenomination = !empty($section['label']) ? $section['label'] : $section['name']; //va presa sempre da section_fo

                    $vlPermits = false;
                    $vlOwnerPermits = false;

                    $updatePermits = match (true){
                        $checkVl => (($this->acl->getUpdate() && $vlPermits) || $vlOwnerPermits),
                        default => $this->acl->getUpdate()
                    };

                    if($updatePermits){
                        $buttonAction = ButtonAction::create([
                            'edit' => true,
                        ])->addEdit('admin/politic-organ/edit/' . $section['id'], $section['id']);

                        if(!empty($records[$section['id']])){
                            $buttonAction = $buttonAction->addCustomButton('s_r_l',siteUrl('admin/politic-organ/show-record-log/'.$section['id']), $section['id'], '111', 'show-record-log', 'view');
                        }

                        $buttonAction = $buttonAction->render();
                    }else{
                        $buttonAction = '';
                    }

                    $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">Da modificare</small>';
                    $competenze = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">Non presenti</small>';

                    if (!empty($records[$section['id']])) {
                        $updateAt = '<small class="badge badge-info"> <i class="far fa-clock"></i> ' . date('d-m-Y H:i:s', strtotime($records[$section['id']]['updated_at'])) . '</small>';

                        if(!empty($records[$section['id']]['skills'])){

                            $competenze = strip_tags($records[$section['id']]['skills']);
                            $competenze = mb_convert_encoding($competenze, 'UTF-8', 'UTF-8');
                            $competenze = mb_strimwidth($competenze, 0, 120, ' …', 'UTF-8'); // niente ellissi se <120
                        }
                    }

                    $checkBox = ButtonAction::checkList('item[]', $section['id']);

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = $checkBox;
                    $setTempData[] = "<a href='".baseUrl('/page/'.$section['id'].'/'.strtolower($rowDenomination).'.html')."'>".$rowDenomination."</a>";
                    $setTempData[] = $competenze;
                    $setTempData[] = $updateAt;

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;

                }

                $response['aaData'] = $data;
            }
            jsonOut($response);
        }
    }


    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        // Validatore che verifica se l'elemento da modificare esiste
        $validator = new PoliticOrganValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/politic-organ');
            exit();
        }

        $data = [];
        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $politicOrgan = Registry::get('politic_organ');

        if(!empty($politicOrgan) && is_object($politicOrgan)){
            $politicOrgan =  $politicOrgan->toArray();
        }elseif(empty($politicOrgan)){
            $politicOrgan = [];
        }

        $this->breadcrumb->push('Organi politici', 'admin/politic-organ');
        $this->breadcrumb->push('Modifica', '/');

        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Organi politici';
        $data['subTitleSection'] = 'GESTIONE DEGLI ORGANI POLITICI';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        // Setto il tipo di storage
        $data['_storageType'] = 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] =  '/admin/politic-organ/update';
        $data['formSettings'] = [
            'name' => 'form_politic_organ',
            'id' => 'form_politic_organ',
            'class' => 'form_politic_organ'
        ];

        $data['politicOrgan'] = $politicOrgan;

        // ID dell'ente corrente.
        $data['institution_id'] = checkAlternativeInstitutionId();

        render('politic_organ/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un tasso di assenza
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/politic-organ/update.html
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new PoliticOrganValidator();
        $check = $validator->check('update');

        // Controllo se la validazione è andata a buon fine
        if ($check['is_success']) {

            //Recupero la riga se esiste
            $sectionID = (int)Input::post('section_fo_id', true);
            $politicOrgan = SectionFoPoliticOrganModel::where('section_fo_id', $sectionID)->first();
            $insert = empty($politicOrgan);
            $institutionID = checkAlternativeInstitutionId();
            $now = date("Y-m-d H:i:s");

            // Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, true);

            //Salvo la riga
            $data = [];
            $data['denomination'] = trim(strip_tags(Input::post('denomination', true))) ?? '';
            $data['skills'] = Input::post('skills', true);
            $data['absence_office_reason'] = Input::post('absence_office_reason', true);
            $data['show_personnel'] = setDefaultData(strip_tags(Input::post('show_personnel', true)), 0, ['', null]);

            if($insert){
                $data['institution_id'] = $institutionID;
                $data['section_fo_id'] = $sectionID;
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                SectionFoPoliticOrganModel::insert($data);

            }else{
                $politicOrgan->update($data);
            }


            //Livello dirigenziale
            $managementStructures = !empty(Input::post('management_structure')) ? explode(",", Input::post('management_structure')) : [];
            $toInsertManagement = array_map(function($str) use($institutionID,$sectionID,$now){
                return [
                    'institution_id' => $institutionID,
                    'section_fo_id' => $sectionID,
                    'typology' => 'management_structure',
                    'object_structures_id' => (int)$str,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }, $managementStructures);

            RelSectionsFoPoliticOrganStructure::where('section_fo_id', $sectionID)->where('typology', 'management_structure')->delete();
            RelSectionsFoPoliticOrganStructure::insert($toInsertManagement);

            //Livello non dirigenziale
            $nonManagementStructures = !empty(Input::post('management_structure')) ? explode(",", Input::post('non_management_structure')) : [];
            $toInsertNonManagement = array_map(function($str) use($institutionID,$sectionID,$now){
                return [
                    'institution_id' => $institutionID,
                    'section_fo_id' => $sectionID,
                    'typology' => 'non_management_structure',
                    'object_structures_id' => (int)$str,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }, $nonManagementStructures);
            RelSectionsFoPoliticOrganStructure::where('section_fo_id', $sectionID)->where('typology', 'non_management_structure')->delete();
            RelSectionsFoPoliticOrganStructure::insert($toInsertNonManagement);


            //PERSONALE
            //viene modificato solo se passo "mosta personale a 1"
            if(Input::post('show_personnel', true) == 1){

                $personnel = explode(",", Input::post('personnel'));
                $toInsertPersonnel = array_map(function($pers) use($institutionID,$sectionID,$now){
                    return [
                        'institution_id' => $institutionID,
                        'section_fo_id' => $sectionID,
                        'object_personnel_id' => (int)$pers,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }, $personnel);
                RelSectionsFoPoliticOrganPersonnel::where('section_fo_id', $sectionID)->delete();
                RelSectionsFoPoliticOrganPersonnel::insert($toInsertPersonnel);
            }

            ActivityLog::create([
                'user_id' => authPatOs()->getIdentity(['id'])['id'],
                'action' => 'Modifica organo politico',
                'description' => 'Modifica organo politico con ID (' . $sectionID . ')',
                'action_type' => 'updateObjectInstance',
                'object_id' => 111,
                'record_id' => $sectionID,
                'area' => 'object'
            ]);


            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

            $json->set('message', __('success_update_operation', null, 'patos'));

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }
}
