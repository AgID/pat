<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Auth;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\AuthPatOS;
use Helpers\FileSystem\Dir;
use Jenssegers\Agent\Agent;
use Model\AttemptsModel;
use Model\PasswordHistoryModel;
use Model\RelUsersAclProfilesModel as Profile;
use Model\ConcurrentSessModel;
use System\Arr;
use System\Auth;
use System\BaseController;
use System\Input;
use System\Token;
use System\Validator;
use System\Action;

class AuthAdminController extends BaseController
{

    /**
     * Costruttore
     */
    public function __construct()
    {
        parent::__construct();
        helper(['form', 'url', 'string', 'app']);
    }

    /**
     * @throws Exception
     * @url /auth.html
     * @method GET
     * @return void
     */
    public function index(): void
    {
        $data = [];

        $uriToken = random_string('unique', 32);
        session()->setFlash('auth_uri_token', $uriToken);

        $data['token'] = $uriToken;
        $data['errors'] = session()->getFlash('auto_error');
        $data['logout'] = session()->getFlash('logout_user_ok');
        $data['recoverySuccess'] = session()->getFlash('recovery_password_ok');
        $data['registerAccountSuccess'] = session()->getFlash('register_account_success');
        $data['activeAccount'] = session()->getFlash('active_account_mail');

        // Render Auth
        render('auth/auth', $data, 'admin');
    }

    /**
     * @throws Exception
     * @url /auth.html
     * @method POST
     * @return void
     */
    public function login(): void
    {
        $hasErrors = false;
        $setErrors = [];
        $redirect = 'auth';
        $location = 'auto';
        $registerAttempts = false;
        $isSystemProfile = 0;
        $versioningProfile = 0;
        $archivingProfile = 0;
        $lockUserProfile = 0;
        $advancedProfile = 0;
        $isAdmin = 0;
        $exportCsv = 0;
        $fileArchive = 1;

        $agent = new Agent();

        // Verifica - Cross-site request forgery
        if (Token::verify() && Input::get('t', true) === session()->getFlash('auth_uri_token')) {

            // Verifica i tentativi di accesso.
            $attempts = AttemptsModel::where('ip', Input::ipAddress())
                ->where('created_at', '>=', date('Y-m-d H:i:s', time() - 60 * 15))
                ->orderBy('created_at', 'DESC')
                ->count();

            // l'utente bloccato ?
            if ($attempts >= 5) {

                $registerAttempts = true;

                // Registro nelle attività dei logs
                ActivityLog::create([
                    'action' => __('auth_blocked_title', null, 'patos_auth'),
                    'description' => sprintf(__('auth_blocked_description', null, 'patos_auth'), strip_tags((string)Input::post('username', true)), date('d-m-Y'), date('H:i:s')),
                    'request_post' => Input::post(['username'], true),
                    'object_id' => 54
                ]);

                $hasErrors = true;
                $setErrors = ['attemps' => __('auth_failed_block_15', null, 'patos_auth')];
            }

            // Validatore form
            $validator = new Validator();

            // Validator username
            $validator->label('E-mail o username')
                ->value(Input::post('username'))
                ->required()
                ->minLength(2)
                ->maxLength(32)
                ->end();

            // Validator password
            $validator->label('Password')
                ->value(Input::post('password'))
                ->required()
                ->minLength(2)
                ->maxLength(32)
                ->end();

            $validator->label('Browser detect')
                ->value(Input::post('dpm'))
                ->required()
                ->in('true,false')
                ->end();


            // Analizza validatore
            if (!$validator->isSuccess()) {

                // Non stampo il motivo reale dell'errore prodotto dal validator,
                // ma solo la notifica delle credenziali non valide.
                $hasErrors = true;
                $msgError = __('username_or_email_not_valid', null, 'patos_auth');
                $setErrors = [$msgError];
                $redirect = 'auth';
            }


            // Validazione OK
            if (!$hasErrors) {

                // Custom PAT OS Auth
                $auth = new Auth(AuthPatOS::class);

                // Request Input
                $username = trim(Input::post('username', true));
                $password = trim(Input::post('password', true));

                // Auth Pat OS
                $isAuth = $auth->authenticate($username, $password);

                // Login OK
                if ((bool)$isAuth === true) {

                    // Elimino i tentativi di accesso falliti
                    AttemptsModel::where('ip', Input::ipAddress())->delete();

                    // Lista dei profili associati
                    $profiles = Profile::select(['id', 'acl_profile_id'])
                        ->where('user_id', '=', $auth->id())
                        ->with(['profile' => function ($query) {
                            $query->select([
                                'id',
                                'is_admin',
                                'is_system',
                                'versioning',
                                'archiving',
                                'lock_user',
                                'advanced',
                                'export_csv',
                                'editor_wishing',
                                'file_archive'
                            ]);
                        }])
                        ->get()
                        ->toArray();

                    // IDs Profili.
                    $profileIds = Arr::pluck($profiles, 'acl_profile_id');

                    // Verifica scadenza validità password
                    $passwordHistory = PasswordHistoryModel::select('created_at')
                        ->where('user_id', $auth->id())
                        ->orderBy('id', 'DESC')
                        ->first();

                    $setExpirePassword = false;
                    $getIdentity = $auth->getIdentity(['last_visit', 'force_change_password']);

                    if (empty($passwordHistory) || $getIdentity['force_change_password'] === 1) {

                        $setExpirePassword = true;
                    } else {

                        $getIdentity = $auth->getIdentity(['password_expiration_days']);
                        $expirationDayToSeconds = $getIdentity['password_expiration_days'] * 24 * 60 * 60;
                        $dataChangePassword = strtotime($passwordHistory['created_at']) + $expirationDayToSeconds;
                        $expirePassword = time();

                        if ($expirePassword > $dataChangePassword) {
                            $setExpirePassword = true;
                        }
                    }

                    // SET storage profilo singola voce, NON Profilo ACL per il CRUD.
                    foreach ($profiles as $profile) {

                        if (!empty($profile['profile'])) {

                            if ($profile['profile']['is_system'] > 0) {
                                $isSystemProfile = $profile['profile']['is_system'];
                            }

                            if ($profile['profile']['versioning'] > 0) {
                                $versioningProfile = $profile['profile']['versioning'];
                            }

                            if ($profile['profile']['archiving'] > 0) {
                                $archivingProfile = $profile['profile']['archiving'];
                            }

                            if ($profile['profile']['lock_user'] > 0) {
                                $lockUserProfile = $profile['profile']['lock_user'];
                            }

                            if ($profile['profile']['advanced'] > 0) {
                                $advancedProfile = $profile['profile']['advanced'];
                            }

                            if ($profile['profile']['export_csv'] > 0) {
                                $exportCsv = $profile['profile']['export_csv'];
                            }

                            if ($profile['profile']['file_archive'] >= 1 && $profile['profile']['file_archive'] > $fileArchive) {
                                $fileArchive = $profile['profile']['file_archive'];
                            }

                            if (strlen($profile['profile']['editor_wishing']) >= 1) {
                                $editorWishing = $profile['profile']['editor_wishing'];
                            }

                            if ($profile['profile']['is_admin'] > 0) {
                                $isAdmin = $profile['profile']['is_admin'];
                            }
                        }
                    }

                    // Storage Profilo e dati aggiuntivi in sessione.
                    $storage = [];
                    $storage['last_date_access'] = date('d-m-Y');
                    $storage['last_hour_access'] = date('H:i');
                    $storage['institute_id'] = PatOsInstituteId();
                    $storage['profiles'] = !isSuperAdmin() ? serialize($profileIds) : null;
                    $storage['expire_password'] = !isSuperAdmin() ? $setExpirePassword : false;
                    $storage['is_system'] = !isSuperAdmin() ? $isSystemProfile : null;
                    $storage['versioning'] = !isSuperAdmin() ? $versioningProfile : null;
                    $storage['archiving'] = !isSuperAdmin() ? $archivingProfile : null;
                    $storage['lock_user'] = !isSuperAdmin() ? $lockUserProfile : null;
                    $storage['advanced'] = !isSuperAdmin() ? $advancedProfile : null;
                    $storage['editor_wishing'] = !empty($editorWishing) ? $editorWishing : 'base';
                    $storage['export_csv'] = !isSuperAdmin() ? $exportCsv : null;
                    $storage['file_archive'] = !isSuperAdmin() ? $fileArchive : null;
                    $storage['is_admin'] = !isSuperAdmin() ? $isAdmin : null;
                    $storage['browser_private_mode'] = Input::post('dpm') === 'true' ? true : false;
                    $storage['sess_id'] = md5(uniqid(mt_rand() . microtime(true)));

                    // Setto in sessione il primo accesso.
                    session()->setFlash('admin_first_access', (isSuperAdmin() === true));

                    // Se non esiste creo la directory associata all'utente per l'upload dei file personali
                    if (!isSuperAdmin()) {

                        $dir = MEDIA_PATH . instituteDir() . '/file_archive/' . config('prefix_user_dir', null, 'app') . $auth->id();
                        if (!Dir::exists($dir)) {

                            createDirByUserId($auth->id());
                        }
                    }

                    // Registro nelle attività dei logs..
                    ActivityLog::create([
                        'user_id' => $auth->id(),
                        'action' => __('auth_success_title', null, 'patos_auth'),
                        'description' => sprintf(__('auth_success_description', null, 'patos_auth'), strip_tags((string)Input::post('username', true)), date('d-m-Y'), date('H:i:s')),
                        'request_post' => Input::post(['username']),
                        'object_id' => 54
                    ]);

                    $optionStoragePlugin = null;
                    if (\System\Registry::exist('option_storage_plugin')) {
                        $optionStoragePlugin = \System\Registry::get('option_storage_plugin');
                    }

                    if (is_array($optionStoragePlugin)) {

                        // Converto i due array in collezioni
                        $storageCollection = collect($storage);
                        $optionStoragePluginCollection = collect($optionStoragePlugin);

                        // Unisco le due collezioni
                        $mergedCollection = $storageCollection->merge($optionStoragePluginCollection);

                        // Converto la collezione unificata in un array
                        $storage = $mergedCollection->toArray();

                    }

                    // Add Storage...
                    $auth->addStorage($storage);

                    // Redirect Pannello di controllo
                    $redirect = 'admin/dashboard';
                    $location = 'refresh';
                } else {

                    // Registro nelle attività dei logs..
                    ActivityLog::create([
                        'user_id' => $auth->id(),
                        'action' => __('auth_failed_title', null, 'patos_auth'),
                        'description' => sprintf(__('auth_failed_description', null, 'patos_auth'), strip_tags((string)Input::post('username', true)), date('d-m-Y'), date('H:i:s')),
                        'request_post' => Input::post(['username']),
                        'object_id' => 54
                    ]);

                    // Username non valido..
                    $hasErrors = true;
                    $msgError = __('username_or_email_not_valid', null, 'patos_auth');
                    $setErrors = [$auth->getErrorAuth()];
                    $redirect = 'auth';
                }
            }

            // Register IP - Brute Force Block
            if ($hasErrors === true && $registerAttempts === true) {

                AttemptsModel::create([
                    'ip' => Input::ipAddress(),
                    'client_info' => $agent->getUserAgent()
                ]);

            } else {

                if (!empty($storage) && is_array($storage) && !empty($storage['sess_id'])) {

                    ConcurrentSessModel::create([
                        'user_id' => $auth->id(),
                        'institution_id' => PatOsInstituteId(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                        'device' => $agent->device(),
                        'ip' => Input::ipAddress(),
                        'browser_private_mode' => Input::post('dpm') == 'true' ? 1 : 0,
                        'sess_id' => $storage['sess_id'],
                        'is_super_admin' => (isSuperAdmin() === true) ? 1 : 0,
                    ]);

                }
            }

        } else {

            // Sessione token scaduta.
            $setErrors = [__('token_not_not_valid', null, 'patos_auth')];
        }

        // Fash Data eventuali errori
        session()->setFlash('auto_error', $setErrors);

        ob_clean();

        // redirect utente..
        redirect($redirect, $location);
        exit();
    }
}
