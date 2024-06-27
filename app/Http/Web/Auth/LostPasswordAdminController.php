<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Auth;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Carbon\Carbon;
use Exception;
use Helpers\ActivityLog;
use Helpers\Obfuscate;
use Model\RecoveryPassword;
use Model\UsersModel;
use System\BaseController;
use System\Config;
use System\Email;
use System\Input;
use System\Log;
use System\Password;
use System\Token;
use System\Validator;
use System\View;

class LostPasswordAdminController extends BaseController
{
    /**
     * @var array|mixed|null
     */
    private $lastVisited;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper(['form', 'url', 'string', 'checkPassword']);

        $config = new Config();
        $config->load(APP_PATH . 'Config/custom.php');
        Obfuscate::setHash($config->get('obfuscate_key'));

        $configs = new \Maer\Config\Config();
        $configs->load(APP_PATH . 'Config/auth_pat_os.php');
        $this->lastVisited = $configs->get('last_visit');
    }

    /**
     * @url /lost-password.html
     * @method GET
     * @throws Exception
     * @return void
     */
    public function index(): void
    {
        $uriToken = random_string('unique', 32);
        session()->setFlash('lost_password_uri_token', $uriToken);

        $data['token'] = $uriToken;
        $data['errors'] = session()->getFlash('lost_password_error');

        render('auth/lostpassword', $data, 'admin');
    }

    /**
     * @url /lost-password.html
     * @method POST
     * @throws Exception
     * @return void
     */
    public function store(): void
    {
        $hasErrors = false;
        $setErrors = [];
        $redirect = 'lost-password-success';
        $queryStringParams = '';

        if (Token::verify() && Input::get('t', true) === session()->getFlash('lost_password_uri_token')) {

            $validator = new Validator();
            $validator->label('e-mail')
                ->value(Input::post('email'))
                ->required()
                ->isEmail()
                ->add(function() {
                    $result = UsersModel::where(function ($query) {

                        $query->where('email', '=', Input::post('email', true))
                            ->orWhere('email', '=', checkEncrypt(Input::post('email', true)));
                    })
                        ->where('deleted', '=', 0)
                        ->where('active', '=', 1)
                        ->where('super_admin', '=', 0)
                        ->where('institution_id', '=', PatOsInstituteId())
                        ->first();

                    if (empty($result)) {
                        return [
                            'error' => 'L\'indirizzo email inserito non &egrave; stato trovato'
                        ];
                    }

                    return null;
                })
                ->end();

            if (!$validator->isSuccess()) {
                $hasErrors = true;
                $setErrors = $validator->getErrors();
                $redirect = 'lost-password';
            }

            if (!$hasErrors) {

                $result = UsersModel::where(function ($query) {
                    
                    $query->where('email', '=', Input::post('email', true))
                          ->orWhere('email', '=', checkEncrypt(Input::post('email', true)));
                })
                    ->where('deleted', '=', 0)
                    ->where('active', '=', 1)
                    ->where('super_admin', '=', 0)
                    ->where('institution_id', '=', PatOsInstituteId())
                    ->first();

                $check = false;
                // var_dump($result->toSql());
                // var_dump($result->getBindings());
                
                if (!empty($result) && is_object($result)) {

                    $user = $result->toArray();
                    $lastActivity = (strtotime($user[$this->lastVisited]) + ((int)$user['deactivate_account_no_use'] * 24 * 60 * 60));

                    $emailEncrypt = checkDecrypt($user['email']);

                    // Controllo che la data dell'ultima visita sia inferiore alla data attuale per vedere se l'utente è attivo o meno
                    if ($lastActivity > time()) {

                        $check = true;

                        $token = random_string('unique', 32);
                        $queryStringParams = '?e=' . $emailEncrypt;

                        RecoveryPassword::create([
                            'user_id' => $user['id'],
                            'institution_id' => PatOsInstituteId(),
                            'token' => $token
                        ]);

                        // Registro nelle attività dei logs
                        ActivityLog::create([
                            'action' => __('auth_request_recovery_success_title', null, 'patos_auth'),
                            'description' => sprintf(__('auth_request_recovery_success_description', null, 'patos_auth'), (strip_tags((string)Input::post('email', true))), date('d-m-Y'), date('H:i:s')),
                            'request_post' => Input::post(),
                        ]);

                        $link = [
                            'uid' => Obfuscate::encode($user['id']),
                            'token' => $token,
                            'iid' => Obfuscate::encode(PatOsInstituteId()),
                        ];

                        $data = [];
                        $data['email'] = $emailEncrypt;
                        $data['link'] = siteUrl('recovery/password') . '?' . http_build_query($link);

                        $template = View::create('email/lost_password/index', $data)
                            ->partial('header', 'email/header')
                            ->partial('footer', 'email/footer')
                            ->render();

                        $configs = patOsConfigMail(true);

                        $email = new Email($configs);
                        $subject = sprintf(__('notify_email_subject', null, 'patos_auth'), siteUrl());
                        $send = $email->from($configs['smtp_user'])
                            ->to($emailEncrypt)
                            ->set_newline("\r\n")
                            ->subject($subject)
                            ->message($template)
                            ->send();

                        if ($send === true) {

                            session()->setFlash('lost_password_success', true);
                        } else {

                            $hasErrors = true;
                            $setErrors = [__('temporary_error', null, 'patos_auth')];
                        }
                    }
                }

                // Se l'utente non è attivo o la mail inserita non viene trovata, viene mostrato il messaggio di errore
                if (!$check) {

                    // Registro nelle attività dei logs
                    ActivityLog::create([
                        'action' => __('auth_request_recovery_failed_title', null, 'patos_auth'),
                        'description' => sprintf(__('auth_request_recovery_failed_description', null, 'patos_auth'), strip_tags((string)Input::post('email',true)), date('d-m-Y'), date('H:i:s')),
                        'request_post' => Input::post(),
                    ]);

                    // $hasErrors = true;
                    //$setErrors = [__('notify_email_not_found', null, 'patos_auth')];
                }
            }
        }

        if ($hasErrors === true) {

            session()->setFlash('lost_password_error', $setErrors);
            // $redirect = 'lost-password';
        }

        redirect($redirect . $queryStringParams);
    }

    /**
     * @url /lost-password-success.html
     * @method GET
     * @throws Exception
     * @return void
     */
    public function lostPasswordSuccess(): void
    {
        $validator = new Validator();
        $validator->label('email')
            ->value(checkDecrypt(Input::get('e',true)))
            ->isEmail();

        if (!$validator->isSuccess()) {
            show404();
        }

        $data = [];
        $data['email'] = checkDecrypt(Input::get('e',true));

        render('auth/lostpassword_success', $data, 'admin');
    }


    /**
     * @throws Exception
     * @url /recovery/password.html
     * @method GET
     * @return void
     */
    public function edit(): void
    {
        $data = [];
        $hasError = false;

        $validator = new Validator();

        // Validatore user_id
        $validator->label('uid')
            ->value(Input::get('uid'))
            ->required()
            ->add(function () {
                $uId = !empty(Input::get('uid')) ? Obfuscate::decode(Input::get('uid')) : 0;
                if ((bool)preg_match('/[0-9]/', $uId) !== true || $uId == 0) {
                    return ['error' => 1];
                }
                return null;
            }, __('invalid_user_id', null, 'patos_auth'));

        // Validatore token
        $validator->label('token')
            ->value(Input::get('token'))
            ->required()
            ->isAlphaNum()
            ->exactLength(32);

        // Validatore institution_id
        $validator->label('iid')
            ->value(Input::get('iid'))
            ->required()
            ->add(function () {
                $iId = !empty(Input::get('iid')) ? Obfuscate::decode(Input::get('iid')) : 0;
                if ((bool)preg_match('/[0-9]/', $iId) !== true || $iId == 0) {
                    return ['error' => 1];
                }
                return null;
            }, __('invalid_institution_id', null, 'patos_auth'));

        if ($validator->isSuccess() === false) {
            echo showError(__('recovery_password_error', null, 'patos_auth'));
            die();
        }

        $userId = Obfuscate::decode(Input::get('uid'));
        $instituteId = Obfuscate::decode(Input::get('iid'));

        $resultExpire = RecoveryPassword::whereHas('user', function ($query) use ($userId) {
            $query->where('id', '=', $userId);
        })->where('token', Input::get('token',true))
            ->where('institution_id', '=', $instituteId)
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();

        $recovery = !empty($resultExpire) ? $resultExpire->toArray() : false;
        $getUserId = !empty($resultExpire->user->id) ? $resultExpire->user->id : false;

        if (empty($recovery) && empty($getUserId)) {

            $hasError = true;
        } else {

            $limitExpire = (Carbon::parse($recovery['created_at'])->unix() + (60 * 60 * 24));
            $now = Carbon::now()->timestamp;

            if ($now > $limitExpire) {

                $hasError = true;
            }
        }

        $uriToken = random_string('unique', 32);
        session()->setFlash('recovery_password_uri_token', $uriToken);

        $data['hasError'] = $hasError;
        $data['uriToken'] = $uriToken;
        $data['token'] = Input::get('token', true);
        $data['uid'] = Input::get('uid',true);
        $data['iid'] = Input::get('iid',true);

        $data['errors'] = session()->getFlash('recovery_password_errors');

        render('auth/recovery_password', $data, 'admin');
    }

    /**
     * @throws Exception
     * @url /recovery/password.html
     * @method POST
     * @return void
     */
    public function update(): void
    {
        $validator = new Validator();
        $hasErrors = false;
        $setErrors = null;
        $redirect = 'auth';

        $link = [
            'uid' => !empty(Input::post('uid')) ? Input::post('uid', true) : '',
            'token' => !empty(Input::post('token')) ? Input::post('token', true) : '',
            'iid' => !empty(Input::post('iid')) ? Input::post('iid',true) : '',
        ];

        if (Token::verify() && Input::get('t', true) === session()->getFlash('recovery_password_uri_token')) {

            // Validatore user_id
            $validator->label('uid')
                ->value(Input::post('uid'))
                ->required()
                ->add(function () {
                    $uId = !empty(Input::post('uid')) ? Obfuscate::decode(Input::post('uid')) : 0;
                    if ((bool)preg_match('/[0-9]/', $uId) !== true || $uId == 0) {
                        return ['error' => 1];
                    }
                    return null;
                }, __('invalid_user_id', null, 'patos_auth'));

            // Validatore token
            $validator->label('token')
                ->value(Input::post('token'))
                ->required()
                ->isAlphaNum()
                ->exactLength(32);

            // Validatore institution_id
            $validator->label('iid')
                ->value(Input::post('iid'))
                ->required()
                ->add(function () {
                    $iId = !empty(Input::post('iid')) ? Obfuscate::decode(Input::post('iid')) : 0;
                    if ((bool)preg_match('/[0-9]/', $iId) !== true || $iId == 0) {
                        return ['error' => 1];
                    }
                    return null;
                }, __('invalid_institution_id', null, 'patos_auth'));

            $validator->label('Password')
                ->value(Input::post('password'))
                ->required()
                ->add(
                    function () {
                        return checkPassword(Input::post('password'));
                    },
                    __('check_password_error', null, 'patos')
                );

            $validator->label('Ripeti Password')
                ->value(Input::post('re_password'))
                ->required()
                ->isMatches(Input::post('password'), 'Password');

            if ($validator->isSuccess() === false) {
                $hasErrors = true;
                $setErrors = $validator->getErrors();
                $redirect = 'recovery/password?' . http_build_query($link);
            }

            if ($hasErrors === false) {

                $userId = Obfuscate::decode(Input::post('uid'));
                $instituteId = Obfuscate::decode(Input::post('iid'));

                $resultExpire = RecoveryPassword::whereHas('user', function ($query) use ($userId) {
                    $query->where('id', '=', $userId);
                })->where('token', (Input::post('token', true)))
                    ->where('institution_id', '=', $instituteId)
                    ->where('user_id', '=', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->first();

                $recovery = !empty($resultExpire) ? $resultExpire->toArray() : false;
                $getUserId = !empty($resultExpire->user->id) ? $resultExpire->user->id : false;

                if (empty($recovery) && empty($getUserId)) {

                    $hasErrors = true;
                    $setErrors = [__('user_not_found', null, 'patos_auth')];

                    // Registro nelle attività dei logs
                    ActivityLog::create([
                        'action' => __('auth_password_reset_failed_title', null, 'patos_auth'),
                        'description' => sprintf(__('auth_password_reset_failed_description', null, 'patos_auth'), date('d-m-Y'), date('H:i:s')),
                        'request_get' => Input::get(),
                        'request_post' => Input::post()
                    ]);
                } else {

                    $limitExpire = (Carbon::parse($recovery['created_at'])->unix() + (60 * 60 * 24));
                    $now = Carbon::now()->timestamp;
                    $userEmail = checkDecrypt($resultExpire->user->email);

                    if ($now > $limitExpire) {

                        $hasErrors = true;
                        $setErrors = [__('expired_recovery_procedure', null, 'patos_auth')];

                        // Registro nelle attività dei logs
                        ActivityLog::create([
                            'action' => __('auth_password_reset_expire_title', null, 'patos_auth'),
                            'description' => sprintf(__('auth_password_reset_expire_description', null, 'patos_auth'), date('d-m-Y'), date('H:i:s'), $userEmail),
                            'request_post' => Input::post()
                        ]);
                    } else {

                        // Update della nuova password utente
                        UsersModel::where('id', '=', $getUserId)->update([
                            'password' => Password::hash(Input::post('password',true))
                        ]);

                        // Elimino nella tabella il token del recupero password associato all'ID dell'utente
                        RecoveryPassword::where('user_id', '=', $getUserId)->delete();

                        // Settare le sessioni
                        session()->setFlash(
                            'recovery_password_ok',
                            '<strong>Procedura di recupero password completata</strong>. <br />Inserisci le nuove credenziali per accedere nell\'area riservata'
                        );

                        // Registro nelle attività dei logs
                        ActivityLog::create([
                            'action' => __('auth_password_reset_success_title', null, 'patos_auth'),
                            'description' => sprintf(__('auth_password_reset_success_description', null, 'patos_auth'), date('d-m-Y'), date('H:i:s'), $userEmail),
                            'request_post' => Input::post()
                        ]);

                        // Inviare email di notifica procedura recupero password completata
                        $data = [];
                        $data['email'] = $userEmail;

                        $template = View::create('email/lost_password/success_recovery_password', $data)
                            ->partial('header', 'email/header')
                            ->partial('footer', 'email/footer')
                            ->render();

                        $configs = patOsConfigMail(true);

                        $email = new Email($configs);
                        $subject = sprintf(__('notify_email_subject', null, 'patos_auth'), siteUrl());
                        $send = $email->from($configs['smtp_user'])
                            ->to($userEmail)
                            ->set_newline("\r\n")
                            ->subject(__('recovery_password_complete', null, 'patos_auth') . siteUrl())
                            ->message($template)
                            ->send();

                        if ($send !== true) {

                            Log::danger($email->print_debugger());
                        }
                    }
                }
            }
        } else {

            $hasErrors = true;
            $setErrors = ['token' => __('invalid_token', null, 'patos_auth')];
            $redirect = 'recovery/password?' . http_build_query($link);
        }

        if ($hasErrors === true) {
            session()->setFlash('recovery_password_errors', $setErrors);
        }

        redirect($redirect);
    }
}
