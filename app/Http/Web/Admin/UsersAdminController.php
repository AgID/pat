<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\File;
use Helpers\Obfuscate;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\UserValidator;
use Model\AclProfilesModel;
use Model\InstitutionsModel;
use Model\RelUsersAclProfilesModel;
use Model\UsersModel;
use System\Arr;
use System\Email;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Password;
use System\Random;
use System\Registry;
use System\Token;
use System\Uploads;
use System\View;

/**
 *
 * Controller Utenti
 *
 */
class UsersAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
        helper(['checkPassword', 'form', 'url', 'app', 'string']);
    }

    /**
     * @description Renderizza la pagina index degli Utenti
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/user.html
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $this->breadcrumb->push('Utente', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Gestione Utenti';
        $data['subTitleSection'] = 'GESTIONE DEGLI UTENTI E DEGLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';

        $data['formAction'] = '/admin/user';
        $data['formSettings'] = [
            'name' => 'form_user',
            'id' => 'form_user',
            'class' => 'form_user'
        ];

        render('users/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/user/list.html
     * @method AJAX
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $response = [];

        //Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true) {

            $data = [];

            $records = UsersModel::select('users.*')
                ->with('profiles:id,name')
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'users.institution_id', '=', 'i.id', 'left outer')
                ->orderBy('name')
                ->get();

            $records = optional($records)
                ->toArray();

            $response['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    $isLoggedUser = (int)authPatOs()->id() === (int)$record['id'];

                    if (!empty($record['profiles']) && is_array($record['profiles'])) {

                        $tmpProfiles = Arr::pluck($record['profiles'], 'name');
                        $profiles = str_replace(',', '', implode(
                            ',',
                            array_map(
                                function ($profile) {
                                    return ('<small class="badge badge-primary mb-1">' . $profile . '</small><br>');
                                },
                                $tmpProfiles
                            )
                        ));
                    } else {

                        $profiles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    $buttonAction = '<div><i class="text-muted fas fa-ban"></i></div>';

                    if (authPatOs()->id() !== (int)$record['id'] && (empty($record['technical_user']) || isSuperAdmin())) {

                        //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                        $buttonAction = (!$isLoggedUser) ? ButtonAction::create([
                            'edit' => $this->acl->getUpdate(),
                            'delete' => $this->acl->getDelete(),
                        ])
                            ->addEdit('admin/user/edit/' . $record['id'], $record['id'])
                            ->addDelete('admin/user/delete/' . $record['id'], $record['id'])
                            ->render() : '<i class="fas fa-ban text-muted"></i>';
                    }

                    $dataToggle = ($record['active'] === 0)
                        ? 'Non attivo'
                        : 'Attivo';

                    $colorGrey = 'grey';

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($isLoggedUser || (!empty($record['technical_user']) && !isSuperAdmin()))
                        ? '<i class="fas fa-ban text-muted"></i>'
                        : ButtonAction::checkList('item[]', $record['id']);

                    $setTempData[] = '<i data-toggle="tooltip" data-placement="top" data-original-title="Utente ' . $dataToggle . '" 
                    style="color: ' . $colorGrey . '"></i>&nbsp;' . checkDecrypt($record['name']);

                    $setTempData[] = checkDecrypt($record['username']);
                    $setTempData[] = $profiles;
                    $setTempData[] = checkDecrypt($record['email']);

                    //Se è un SuperAdmin mostro la colonna dell'Ente
                    if (isSuperAdmin(true)) {
                        $setTempData[] = !empty($record['institution']['full_name_institution'])
                            ? $record['institution']['full_name_institution']
                            : 'Super Admin';
                    }

                    $setTempData[] = $buttonAction;

                    // Append TempData
                    if ($isLoggedUser) {
                        array_unshift($data, $setTempData);
                    } else {
                        $data[] = $setTempData;
                    }
                }

                $response['aaData'] = $data;
            }

            echo json_encode($response);

        }
    }

    /**
     * @description Funzione per la creazione di un nuovo utente
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/user/create.html
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $this->breadcrumb->push('Utente', 'admin/user');
        $this->breadcrumb->push('Nuovo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Gestione Utenti';
        $data['subTitleSection'] = 'GESTIONE DEGLI UTENTI E DEGLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';

        $data['formAction'] = '/admin/user/store';
        $data['formSettings'] = [
            'name' => 'form_user',
            'id' => 'form_user',
            'class' => 'form_user'
        ];

        $data['_storageType'] = 'insert';

        $data['profilesIds'] = null;

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        $data['institution_id'] = $institutionId;
        // Profilo completo utente
        $data['identity'] = authPatOs()->getIdentity();

        //Recupero i profili ACL
        $acl = AclProfilesModel::select(['id', 'name'])
            ->where(function ($query) use ($institutionId) {
                $query->where('acl_profiles.institution_id', '=', $institutionId);
                $query->orWhereNull('institution_id');
            });

        $acl = $acl->get()->toArray();

        $data['acl'] = Arr::pluck($acl, 'name', 'id');

        render('users/form_store', $data, 'admin');
    }

    /**
     * @description Funzione per il blocco/sblocco di utenti
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /user/lock-unlock.html
     */
    public function lockUnlock(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Validatore per le operazioni multiple
        $validator = new UserValidator();
        $validate = $validator->multipleSelection();

        $getIdentity = authPatOs()->getIdentity(['id', 'name']);

        if ($validate['is_success'] === true) {

            sessionSetNotify('Operazione avvenuta con successo');

            $active = Input::get('action') === 'lock' ? 0 : 1;
            $action = Input::get('action') === 'lock' ? 'Blocco' : 'Sblocco';

            //Recupero gli utenti selezionati da attivare/disattivare
            $users = Registry::get('__ids__multi_select_profile__');

            // Dati per registrazione ActivityLogs
            $ids = Arr::pluck($users, 'id');
            $fullNames = Arr::pluck($users, 'name');

            //Update utenti
            UsersModel::whereIn('id', $ids)
                ->update(['active' => $active]);

            // Storage Activity log
            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => $action . ' Utenti',
                'description' => $action . ' Utenti "' . implode(',', $fullNames) . '" con ID ' . implode(',', $ids) . ' da parte dell\'utente ' . $getIdentity['name']
                    . ' con ID ' . $getIdentity['id'] . ' riuscita con successo',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'area' => 'users',
                'object_id' => 54,
                'platform' => 'all'
            ]);
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/user');
    }

    /**
     * @description Funzione per l'update di un utente
     *
     * @return void
     * @throws Exception
     * @url /admin/user/update.html
     * @method POST
     */
    public function update(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        $hasError = false;
        $doUpload = null;
        $json = new JsonResponse();
        $code = $json->success();
        $passwordWS = null;

        //Validatore form
        $validator = new UserValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {

            $userId = (int)strip_tags(Input::post('id', true));

            // Recupero l'utente attuale prima di modificarlo e lo salvo nel versioning
            $user = UsersModel::with('profiles:id')->find($userId);

            //Controllo sul file da aggiornare
            if (filesUploaded('profile_image') === true) {

                $doUpload = $this->doUpload();
                $hasError = (bool)$doUpload['success'];

                if (!$hasError) {

                    //Se esiste elimino il vecchio file dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir() . '/assets/images/' . $user->profile_image)) {

                        File::delete(MEDIA_PATH . instituteDir() . '/assets/images/' . $user->profile_image);
                    }
                }
            }

            if (!$hasError) {

                $data['name'] = checkEncrypt(strip_tags((trim(Input::post('name', true)))));
//                $data['username'] = checkEncrypt(strip_tags(Input::post('username', true)));
                $data['email'] = checkEncrypt(strip_tags(strtolower(trim(Input::post('email', true)))));
                $data['phone'] = checkEncrypt(strip_tags(Input::post('phone', true)));
                $data['profile_image'] = !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : $user->profile_image;

                if (getAclModifyProfile()) {

                    $data['password_expiration_days'] = setDefaultData(strip_tags(Input::post('password_expiration_days', true)), 0, ['', null, 0, false]);
                    $data['prevent_password_repetition'] = setDefaultData(strip_tags(Input::post('prevent_password_repetition', true)), 0, ['', null, 0, false]);
                    // $data['prevent_password_repetition_6_months'] = setDefaultData(Input::post('prevent_password_repetition_6_months'), 0, ['', null, 0, false]);
                    $data['prevent_password_change_day'] = setDefaultData(strip_tags(Input::post('prevent_password_change_day', true)), 0, ['', null, 0, false]);
                    $data['deactivate_account_no_use'] = setDefaultData(strip_tags(Input::post('deactivate_account_no_use', true)), 0, ['', null, 0, false]);
                    $data['filter_owner_record'] = setDefaultData(strip_tags(Input::post('filter_owner_record', true)), 0, ['', null, 0, false]);

                }

                $data['notes'] = Input::post('notes', true);


                //Controllo se è stata modificata o meno la password
                if (!empty(Input::post('password'))) {

                    $data['password'] = Password::hash(Input::post('password'));
                }

                // Update Utente
                UsersModel::where('id', '=', $userId)->updateWithLogs($user, $data);

                //Elimino i profili acl associati all'utente prima di reinserirli aggiornati
                RelUsersAclProfilesModel::where('user_id', $userId)
                    ->delete();

                //Controllo se nell'Input ci sono profili ACL
                if (Input::post('profiles', true)) {

                    $dataProfiles = [];
                    foreach (Input::post('profiles') as $profile) {
                        $dataProfiles[] = [
                            'user_id' => (int)Input::post('id'),
                            'acl_profile_id' => (int)strip_tags($profile),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }

                    //Update Profili Acl dell'utente
                    RelUsersAclProfilesModel::insert($dataProfiles);

                }

                // Regenerate Session. La modifica è avvenuta tramite il profilo utente.
                if (authPatOs()->id() === $userId) {

                    // Rigenero la sessione.
                    authPatOs()->regenerateSession($user->toArray());
                    authPatOs()->addStorage(authPatOs()->getStorage());

                    // Lista dei profili associati
                    $profileIds = RelUsersAclProfilesModel::where('user_id', '=', $userId)
                        ->pluck('acl_profile_id')->toArray();

                    $storage['profiles'] = serialize($profileIds);
                    authPatOs()->addStorage($storage);

                }

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_edit', null, 'patos'), 'Utente '));

                if ($passwordWS !== null) {
                    $json->set('is_modal', 1);
                    $json->set('username', strip_tags(Input::post('username', true)));
                    $json->set('password', $passwordWS);
                }

            } else {

                $code = $json->bad();
                $json->error('error', $doUpload['data']);
            }
        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Funzione per l'upload dei file
     * @return array
     */
    private function doUpload(): array
    {
        $data = [];

        $upload = new Uploads();
        $config['upload_path'] = './media/' . instituteDir() . '/assets/images/';
        $config['allowed_types'] = 'png|jpeg|gif|jpg';
        $config['encrypt_name'] = true;
        $config['file_ext_tolower'] = true;
        $config['max_size'] = 5042;
        $config['max_width'] = 1024;
        $config['max_height'] = 1024;
        $config['min_width'] = 50;
        $config['min_height'] = 50;
        $config['max_filename'] = 50;
        $config['remove_spaces'] = true;

        $upload->initialize($config);

        if ($upload->doUpload('profile_image')) {

            $data['success'] = false;
            $data['data'] = $upload->data();
        } else {

            $data['success'] = true;
            $data['data'] = $upload->displayErrors();
        }

        return $data;
    }

    /**
     * @description Funzione per l'eliminazione di un utente
     *
     * @return void
     * @throws Exception
     * @url /admin/user/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        //Validatore che verifica se l'utente da eliminare esiste
        $validator = new UserValidator();
        $validate = $validator->validateUriSegmentId();

        if (!$validate['is_success']) {

            redirect('admin/user', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        //Recupero l'utente da eliminare dal Registro
        $user = Registry::get('user');

        $profileImage = $user->profile_image;

        // Elimino i dati anche nella tabella di relazione con i profili acl
        RelUsersAclProfilesModel::where('user_id', uri()->segment(4, 0))
            ->delete();

        //Elimino l'utente settando deleted = 1
        $model = new UsersModel();
        $fields = $model->getFillable();
        $keepFields = ['institution_id', 'id', 'name', 'username', 'password'];
        foreach ($fields as $field) {
            if (!in_array($field, $keepFields)) {
                $user->$field = null;
            }
        }

        $user->deleted = 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->active = 0;
        $user->save();

        // Dati per registrazione ActivityLog
        $getIdentity = authPatOs()->getIdentity(['id', 'name']);

        // Storage Activity log
        ActivityLog::create([
            'user_id' => $getIdentity['id'],
            'action' => 'Eliminazione Utente',
            'description' => 'Eliminazione Utente con ID (' . $user->id . ')',
            'request_post' => [
                'post' => @$_POST,
                'get' => Input::get(),
                'server' => Input::server(),
            ],
            'action_type' => 'deleteUserInstance',
            'object_id' => 54,
            'record_id' => $user->id,
            'area' => 'user',
            'platform' => 'all'
        ]);

        //Elimino l'eventuale immagine del profilo dell'utente dal file system
        removePhoto($profileImage);

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        sessionSetNotify(sprintf(__('success_delete', null, 'patos'), 'Utente '));

        redirect('admin/user');
    }

    /**
     * @description Funzione per il salvataggio di un nuovo utente
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/user/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $hasError = false;
        $doUpload = null;
        $json = new JsonResponse();
        $code = $json->success();
        $randomAlpha = null;
        $passwordWS = null;

        //Validatore form
        $validator = new UserValidator();
        $check = $validator->check();

        if ($check['is_success']) {

            $institutionId = checkAlternativeInstitutionId();

            //Controllo sul file da caricare
            if (filesUploaded('profile_image') === true) {

                $doUpload = $this->doUpload();
                $hasError = (bool)$doUpload['success'];
            }

            if ($hasError === false) {

                $data['institution_id'] = $institutionId;
                $data['name'] = checkEncrypt(trim(Input::post('name', true)));
                $data['username'] = checkEncrypt(strtolower(trim(Input::post('username', true))));
                $data['password'] = Password::hash(trim(Input::post('password', true)));
                $data['email'] = checkEncrypt(strtolower(trim(Input::post('email', true))));
                $data['phone'] = checkEncrypt(trim(Input::post('phone', true)));
                $data['fiscal_code'] = checkEncrypt(Input::post('fiscal_code', true));
                $data['profile_image'] = !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : null;
                $data['registration_type'] = Input::post('registration_type', true);
                $data['password_expiration_days'] = Input::post('password_expiration_days', true);
                $data['prevent_password_repetition'] = setDefaultData(Input::post('prevent_password_repetition', true), 0, [null]);
                // $data['prevent_password_repetition_6_months'] = Input::post('prevent_password_repetition_6_months', true);
                $data['prevent_password_change_day'] = Input::post('prevent_password_change_day', true);
                $data['deactivate_account_no_use'] = setDefaultData(Input::post('deactivate_account_no_use', true), 0, [null]);
                $data['filter_owner_record'] = Input::post('filter_owner_record', true);
                $data['notes'] = Input::post('notes', true);
                $data['registration_date'] = date('Y-m-d H:i:s');
                $data['deleted'] = 0;
                $data['active'] = 1;
                $data['last_visit'] = date('Y-m-d H:i:s');

                // Registra utente attivo con mail di notifica oppure
                if (Input::post('registration_type') == 1) {
                    $randomAlpha = Random::numeric();
                }

                // Store dell'utente
                $insert = UsersModel::createWithLogs($data);

                // Creo la directory associata all'utente per l'upload dei file personali
                createDirByUserId($insert->id);

                if (Input::post('profiles')) {

                    $dataProfiles = [];

                    foreach (Input::post('profiles') as $profile) {

                        $dataProfiles[] = [
                            'user_id' => $insert->id,
                            'acl_profile_id' => $profile,
                            //'last_visit' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }

                    // Storage dei profili associati all'utente
                    RelUsersAclProfilesModel::insert($dataProfiles);
                }

                // Registration type - send email
                if (Input::post('registration_type') == 1) {

                    $pathView = '';
                    $dataActivation = [];

                    $institution = InstitutionsModel::where('id', '=', $institutionId)->first();

                    /*$dataActivation['logo'] = !empty($institution->simple_logo_file) && !empty($institution->short_institution_name)
                        ? baseUrl('media/' . $institution->short_institution_name .'assets/images/' . $institution->simple_logo_file)
                        : baseUrl('assets/newsletter/img/logo-pat.png');*/

                    if ((int)Input::post('registration_type') === 1) {

                        $pathView = 'email/activation_user/notify_no_link_activation';
                    }

                    if ((int)Input::post('registration_type') === 2) {

                        $pathView = 'email/activation_user/notify_with_link_activation';
                    }

                    $dataActivation['fullName'] = strip_tags(Input::post('name', true));

                    $dataActivation['url'] = siteUrl('auth');
                    $dataActivation['username'] = strip_tags(Input::post('username', true));
                    $dataActivation['email'] = strip_tags(Input::post('email', true));
                    $dataActivation['password'] = strip_tags(Input::post('password', true));

                    $dataActivation['institutionalLink'] = siteUrl();
                    $dataActivation['activationLink'] = siteUrl('user-activation/' . $randomAlpha . '?ak=' . Obfuscate::encode($insert->id));

                    // Build template mail
                    $template = view::create($pathView, $dataActivation)
                        ->partial('header', 'email/header')
                        ->partial('footer', 'email/footer')
                        ->render();

                    if (checkAlternativeInstitutionId() !== PatOsInstituteId() &&
                        isSuperAdmin(true) &&
                        $institution['show_smtp_auth'] === 1
                    ) {

                        $configMail = [
                            'smtp_username' => $institution['smtp_user'],
                            'smtp_password' => $institution['smtp_pass'],
                            'smtp_host' => $institution['smtp_host'],
                            'smtp_port' => $institution['smtp_port'],
                            'smtp_security' => $institution['smtp_security'],
                            'smtp_auth' => $institution['smtp_auth']
                        ];
                        $configs = patOsConfigMail(true, $configMail);

                    } else {
                        $configs = loadConfigMail();
                    }

                    $subject = sprintf(
                        __('notify_email_subject_activation_user', null, 'general'),
                        strip_tags(Input::post('name', true)),
                        $institution['full_name_institution']
                    );

                    // Send email
                    $email = new Email($configs);
                    $send = $email->from($configs['smtp_user'])
                        ->to(strip_tags(Input::post('email', true)))
                        ->set_newline("\r\n")
                        ->subject($subject)
                        ->message($template)
                        ->send();

                    if ($send !== true) {

                        Log::danger($email->print_debugger());
                    }
                }

                $json->set('message', sprintf(__('success_save', null, 'patos'), 'Utente '));

                if ($passwordWS !== null) {
                    $json->set('is_modal', 1);
                    $json->set('username', strip_tags(Input::post('username', true)));
                    $json->set('password', $passwordWS);
                }

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            } else {

                $code = $json->bad();
                $json->error('error', $doUpload['data']);
            }
        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Funzione di blocco/sblocco di un singolo utente
     *
     * @return void
     * @throws Exception
     * @url /admin/user/active/:num.html
     * @method GET
     */
    public function activationSingle(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Controllo se esiste l'utente da bloccare/sbloccare
        $result = UsersModel::find(uri()->segment(4));

        if (empty($result)) {

            $message = __('error_activate_user', null, 'patos');
            $notifyType = 'danger';
        } else {

            $result->active = ($result->active === 1) ? 0 : 1;
            $result->save();

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);
            $action = $result->active === 1 ? 'Sblocco' : 'Blocco';

            // Storage Activity log
            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => $action . ' Utente',
                'description' => $action . ' Utente con ID (' . $result->id . ')',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'action_type' => 'updateUserInstance',
                'object_id' => 54,
                'record_id' => $result->id,
                'area' => 'user',
                'platform' => 'all'
            ]);
            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            $message = __('activate_user_message', null, 'patos');
            $notifyType = 'success';
        }

        sessionSetNotify($message, $notifyType);
        redirect('admin/user');
        exit();
    }

    /**
     * @description Funzione per la modifica di un utente
     *
     * @return void
     * @throws Exception
     * @url /admin/user/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new UserValidator();
        $validate = $validator->validateUriSegmentId();

        if (!$validate['is_success']) {

            redirect('admin/user', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        $user = UsersModel::where('id', uri()->segment(4, 0))
            ->with(['profiles' => function ($query) {
                $query->select(['acl_profile_id', 'user_id']);
            }])->first();

        if ($user !== null) {
            showError('Attenzione', 'utente non trovato');
        }

        $user = $user->toArray();

        $this->breadcrumb->push('Utente', 'admin/user');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Gestione Utenti';
        $data['subTitleSection'] = 'GESTIONE DEGLI UTENTI E DEGLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';

        $data['formAction'] = '/admin/user/update';
        $data['formSettings'] = [
            'name' => 'form_user',
            'id' => 'form_user',
            'class' => 'form_user'
        ];
        $data['_storageType'] = 'update';
        $data['user'] = $user;

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $user['institution_id'];

        // Profilo in sessione
        $data['profilesIds'] = Arr::pluck($user['profiles'], 'acl_profile_id');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        //Recupero i profili ACL
        $acl = AclProfilesModel::select(['id', 'name'])
            ->where(function ($query) use ($institutionId) {
                $query->where('acl_profiles.institution_id', '=', $institutionId);
                $query->orWhereNull('institution_id');
            });

        $acl = $acl->get()->toArray();

        $data['acl'] = Arr::pluck($acl, 'name', 'id');

        // Profilo completo utente
        $data['identity'] = authPatOs()->getIdentity();

        render('users/form_store', $data, 'admin');
    }

    /**
     * @description Funzione per l'eliminazione multipla degli utenti selezionati
     *
     * @return void
     * @throws Exception
     * @url /admin/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        //Validatore per le operazioni multiple
        $validator = new UserValidator();
        $validate = $validator->multipleSelection(false);

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli utenti selezionati da eliminare
            $users = Registry::get('__ids__multi_select_profile__');

            //Elimino l'utente settando deleted = 1
            $model = new UsersModel();
            $fields = $model->getFillable();
            $keepFields = ['institution_id', 'id', 'name'];

            //Elimino gli elementi
            foreach ($users as $user) {

                $profileImage = $user->profile_image;

                foreach ($fields as $field) {
                    if (!in_array($field, $keepFields)) {
                        $user->$field = null;
                    }
                }

                $user->deleted = 1;
                $user->active = 0;
                $user->save();

                // Dati per registrazione ActivityLog
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                // Storage Activity log
                ActivityLog::create([
                    'user_id' => $getIdentity['id'],
                    'action' => 'Eliminazione Utente',
                    'description' => 'Eliminazione Utente con ID (' . $user->id . ')',
                    'request_post' => [
                        'post' => @$_POST,
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                    'action_type' => 'deleteUserInstance',
                    'object_id' => 54,
                    'record_id' => $user->id,
                    'area' => 'user',
                    'platform' => 'all'
                ]);

                //Elimino la cartella dell'utente dall'archivio file
                //deleteUserFolder($user->id);

                //Elimino l'eventuale immagine del profilo dell'utente dal file system
                removePhoto($profileImage);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/user');
    }
}
