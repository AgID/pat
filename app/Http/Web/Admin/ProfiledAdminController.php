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
use Helpers\Security\Acl;
use Helpers\Validators\UserValidator;
use Model\PasswordHistoryModel;
use Model\RelUsersAclProfilesModel;
use Model\UsersModel;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Password;
use System\Token;
use System\Uploads;

/**
 *
 * Controller Profilo Utente
 *
 */
class ProfiledAdminController extends BaseAuthController
{

    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('not_acl');
        helper('checkPassword');
        $this->acl = new Acl();
    }

    /**
     * @description Renderizza la pagina del Profilo Utente
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/profile.html
     */
    public function index(): void
    {
        //Validatore
        $validator = new UserValidator();
        $validate = $validator->validateUriSegmentId('profile');

        if (!$validate['is_success']) {

            redirect('admin/dashboard', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        $user = UsersModel::where('id', authPatOs()->id())
            ->with('profiles:id');

        $user = $user->first();

        $user = $user->toArray();

        $this->breadcrumb->push('Profilo', 'admin/user');
        // $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Profilo';
        $data['subTitleSection'] = 'GESTIONE PROFILO UTENTE';
        $data['sectionIcon'] = '<i class="far fa-user-circle fa-3x"></i>';

        $data['formAction'] = '/admin/profile/update';

        $data['formSettings'] = [
            'name' => 'form_profile',
            'id' => 'form_profile',
            'class' => 'form_profile'
        ];
        $data['_storageType'] = 'update';

        $data['user'] = $user;

        $data['profilesIds'] = Arr::pluck($user['profiles'], 'id');

        if (isset($user['scp'])) {
            $data['scp'] = $user['scp'];
        }

        render('profile/index', $data, 'admin');
    }

    /**
     * @description Funzione chiamata per la vista di reset della password quando è scaduta
     *
     * @return void
     * @throws Exception
     * @url /admin/force/password.html
     * @method GET
     */
    public function forcePassword(): void
    {
        $user = UsersModel::where('id', authPatOs()->id())
            ->with('profiles:id')
            ->first();

        $user = $user->toArray();

        $this->breadcrumb->push('Profilo', 'admin/user');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Profilo';
        $data['subTitleSection'] = 'GESTIONE PROFILO UTENTE';
        $data['sectionIcon'] = '<i class="far fa-user-circle fa-3x"></i>';

        $data['formAction'] = '/admin/profile/update/password';

        $data['formSettings'] = [
            'name' => 'form_profile',
            'id' => 'form_profile',
            'class' => 'form_profile'
        ];
        $data['_storageType'] = 'update';

        $data['user'] = $user;

        render('profile/force_change_password', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update della password quando viene reimpostata perché scaduta
     *
     * @return void
     * @throws Exception
     * @url /admin/profile/update/password.html
     * @method POST
     */
    public function updatePassword(): void
    {
        $json = new JsonResponse();
        $code = $json->success();
        $data = [];

        //Validatore form
        $validator = new UserValidator();
        $check = $validator->validatePasswordReset();

        if ($check['is_success']) {

            // Recupero il profilo utente attuale prima di modificarlo e lo salvo nel versioning
            $user = UsersModel::find(authPatOs()->id());

            //Controllo se è stata modificata o meno la password
            if (!empty(Input::post('password'))) {
                $data['password'] = Password::hash(strip_tags((string)Input::post('password', true)));
            }

            // Update del profilo utente
            UsersModel::where('id', '=', authPatOs()->id())->update($data);

            // Salvo la vecchia password
            PasswordHistoryModel::create([
                'user_id' => authPatOs()->id(),
                'password' => $user->password,
            ]);

            // Regenerate Session. La modifica è avvenuta tramite il profilo utente.
            // Rigenero la sessione.
            $userAfterUpdate = UsersModel::find(authPatOs()->id());
            authPatOs()->regenerateSession($userAfterUpdate->toArray());
            authPatOs()->addStorage(authPatOs()->getStorage());

            // Lista dei profili associati
            $profileIds = RelUsersAclProfilesModel::where('user_id', '=', authPatOs()->id())
                ->pluck('acl_profile_id')->toArray();

            $storage['profiles'] = serialize($profileIds);
            $storage['expire_password'] = false;

            authPatOs()->addStorage($storage);

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            // Storage Activity log
            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => 'Modifica profilo utente',
                'description' => 'Modifica profilo utente con ID (' . authPatOs()->id() . ')',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
            ]);
            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            $json->set('message', sprintf(__('success_edit', null, 'patos'), 'Profilo '));
        } else {
            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Funzione che effettua l'update del profilo utente
     *
     * @return void
     * @throws Exception
     * @url /admin/profile/update.html
     * @method POST
     */
    public function update(): void
    {
        $hasError = false;
        $doUpload = null;
        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new UserValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {

            // Recupero il profilo utente attuale prima di modificarlo e lo salvo nel versioning
            $user = UsersModel::with('profiles')->find(authPatOs()->id());

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

            if ($hasError === false) {

                $data['name'] = checkEncrypt(strip_tags((string)Input::post('name', true)));
                $data['email'] = checkEncrypt(strip_tags((string)Input::post('email', true)));
                $data['fiscal_code'] = checkEncrypt(strip_tags((string)Input::post('fiscal_code', true)));
                $data['phone'] = checkEncrypt(strip_tags((string)Input::post('phone', true)));
                $data['profile_image'] = !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : $user->profile_image;

                // Verifico se l'utente è superAdmin o se ha i permessi di modifica avanzata del profilo
                if (isSuperAdmin(true) || $this->acl->getModifyProfile()) {

                    $data['registration_type'] = strip_tags((string)Input::post('registration_type', true));
                    $data['password_expiration_days'] = strip_tags((string)Input::post('password_expiration_days', true));
                    $data['prevent_password_repetition'] = setDefaultData(strip_tags((string)Input::post('prevent_password_repetition', true)), 0, ['', null, 0, false]);
                    $data['prevent_password_change_day'] = setDefaultData(strip_tags((string)Input::post('prevent_password_change_day', true)), 0, ['', null, 0, false]);
                    $data['deactivate_account_no_use'] = setDefaultData(strip_tags((string)Input::post('deactivate_account_no_use', true)), 0, ['', null, 0, false]);
                    $data['filter_owner_record'] = setDefaultData(strip_tags((string)Input::post('filter_owner_record', true)), 0, ['', null, 0, false]);
                }

                $data['notes'] = Input::post('notes', true);

                //Controllo se è stata modificata o meno la password
                if (!empty(Input::post('password'))) {

                    $data['password'] = Password::hash(strip_tags((string)Input::post('password', true)));
                }

                //Update del profilo utente
                UsersModel::where('id', '=', authPatOs()->id())->update($data);

                if (isSuperAdmin(true) || $this->acl->getModifyProfile()) {

                    //Elimino i dati nelle tabelle di relazione prima di reinserirli aggiornati
                    RelUsersAclProfilesModel::where('user_id', authPatOs()->id())
                        ->delete();

                    if (Input::post('profiles')) {

                        $dataProfiles = [];

                        foreach (Input::post('profiles') as $profile) {
                            $dataProfiles[] = [
                                'user_id' => authPatOs()->id(),
                                'acl_profile_id' => strip_tags((int)$profile),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];
                        }

                        // Update nelle tabelle di relazione
                        RelUsersAclProfilesModel::insert($dataProfiles);
                    }
                }

                // Salvo la vecchia password
                PasswordHistoryModel::create([
                    'user_id' => authPatOs()->id(),
                    'password' => $user->password,
                ]);

                // Regenerate Session. La modifica è avvenuta tramite il profilo utente.
                // Rigenero la sessione.
                $userAfterUpdate = UsersModel::find(authPatOs()->id());
                authPatOs()->regenerateSession($userAfterUpdate->toArray());
                authPatOs()->addStorage(authPatOs()->getStorage());

                // Lista dei profili associati
                $profileIds = RelUsersAclProfilesModel::where('user_id', '=', authPatOs()->id())
                    ->pluck('acl_profile_id')->toArray();

                $storage['profiles'] = serialize($profileIds);
                $storage['expire_password'] = false;

                authPatOs()->addStorage($storage);

                // Dati per registrazione ActivityLog
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                // Storage Activity log
                ActivityLog::create([
                    'user_id' => $getIdentity['id'],
                    'action' => 'Modifica profilo utente',
                    'description' => 'Modifica profilo utente con ID (' . authPatOs()->id() . ')',
                    'request_post' => [
                        'post' => @$_POST,
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                    'action_type' => 'updateUserInstance',
                    'object_id' => 54,
                    'record_id' => authPatOs()->id(),
                    'area' => 'user'
                ]);

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_edit', null, 'patos'), 'Profilo '));
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
     *
     * @return array
     * @throws Exception
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
}
