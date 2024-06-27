<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\PasswordHistoryModel;
use Model\UsersModel;
use System\Input;
use System\Password;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Utente (users)
 */
class UserValidator
{
    public Validator $validator;

    /**
     * @description Costruttore
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste un utente con quell'ID per l'ente
     *
     * @param string $mode Indica se si sta modificando il proprio profilo o un altro utente
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId(string $mode = 'user'): array
    {
        $id = ($mode === 'user') ? uri()->segment(4, 0) : authPatOs()->id();

        $this->validator->label('ID utente')
            ->value($id)
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () {

                $args = func_get_args();
                //Recupero l'utente con l'id passato in input
                $check = UsersModel::find((int)$args[1]);

                //Se non esiste l'utente con questo id, mostro un messaggio di errore
                if ($check == null) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'utente ')
                    ];
                }

                //Se esiste l'utente lo salvo nel registro
                Registry::set('user', $check);

                return null;
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert'): array
    {
        return $this->validate($mode);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    protected function validate(string $mode = 'insert'): array
    {
        // Profilo completo utente
        $identity = authPatOs()->getIdentity();

        $this->validator->label('Nome')
            ->value(trim(Input::post('name')))
            ->required()
            ->betweenString(3, 45)
            ->end();

        // Validator insert
        if ($mode === 'insert') {

            $this->validator->label('Username')
                ->value(Input::post('username'))
                ->required()
                ->betweenString(3, 45)
                ->add(function () {
                    if (!filter_var(Input::post('username'), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => "/^[a-zA-Z0-9_!?.@=$&]+$/"]])) {
                        return [
                            'error' => 1
                        ];
                    }
                    return null;
                }, 'Lo username pu&ograve; contenere caratteri alfanumerici e i seguenti caratteri speciali: !?.@=$& ')
                ->add(function () {
                    //Controllo se già esiste un utente con lo stesso username per l'ente
                    $usernameExist = UsersModel::where('username', checkEncrypt(strtolower(Input::post('username'))))->first();
                    if (!empty($usernameExist)) {
                        return ['error' => __('username_exist', null, 'patos')];
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Password')
                ->value(Input::post('password'))
                ->required()
                ->add(function () {
                    return checkPassword(Input::post('password'));
                })
                ->isMatches(Input::post('re_password'), 'Conferma Password')
                ->end();

            $this->validator->label('Email')
                ->value(Input::post('email'))
                ->required()
                ->isEmail()
                ->isMatches(Input::post('re_email'), 'Conferma Email')
                ->add(function () {
                    $emailExist = UsersModel::where('email', checkEncrypt(strtolower(Input::post('email'))))->first();
                    if (!empty($emailExist)) {
                        return ['error' => __('email_exist', null, 'patos')];
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Codice Fiscale')
                ->value(Input::post('fiscal_code'))
                ->add(function () {
                    $fiscalCodeCheck = new FiscalCodeRule(Input::post('fiscal_code'));
                    if (!$fiscalCodeCheck->isValidate()) {
                        return ['error' => __('fiscal_code_error', null, 'patos')];
                    } else {
                        $fiscalCodeExist = UsersModel::where('fiscal_code', Input::post('fiscal_code'))->first();
                        if (!empty($fiscalCodeExist)) {
                            return ['error' => __('fiscal_code_exist', null, 'patos')];
                        }
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Tipo di registrazione')
                ->value(Input::post('registration_type'))
                ->required()
                ->in('0,1,2')
                ->end();

            $this->validator->label('Validità della password')
                ->value(Input::post('password_expiration_days'))
                ->required()
                ->in('25,50,100,180,365')
                ->end();
        }

        // Validator update
        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $user = UsersModel::where('id', Input::post('id'))->first();
                    if (empty($user)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Utente')];
                    }
                })
                ->end();


            $this->validator->label('Password')
                ->value(Input::post('password'))
                ->isMatches(Input::post('re_password'), 'Conferma Password')
                ->add(function () {

                    $checkPassword = checkPassword(Input::post('password'));

                    if ($checkPassword != null) {
                        return $checkPassword;
                    }

                    // Diverso da Super Admin.
                    if (!isSuperAdmin()) {

                        if (Input::post('id') == null) {
                            return ['error' => __('not_exist_user', null, 'patos')];
                        }

                        $passwordHistory = PasswordHistoryModel::where('user_id', Input::post('id'))
                            ->limit(5)
                            ->orderBy('id', 'DESC')
                            ->get()
                            ->toArray();

                        if (empty($passwordHistory)) {
                            return null;
                        }

                        $user = UsersModel::select(['prevent_password_change_day', 'prevent_password_repetition'])
                            ->where('id', Input::post('id'))
                            ->first()
                            ->toArray();

                        if (empty($user)) {
                            return [
                                'error' => __('not_exist_user', null, 'patos')
                            ];
                        }

                        $currentDay = date('Y-m-d');
                        foreach ($passwordHistory as $ph) {

                            if ($user['prevent_password_change_day'] === 1) {
                                if ($currentDay === date('Y-m-d', strtotime($ph['created_at']))) {
                                    return [
                                        'error' => __('password_change_day', null, 'patos')
                                    ];
                                }
                            }

                            if ($user['prevent_password_repetition'] === 1) {
                                if (Password::verify(Input::post('password'), $ph['password']) === true) {
                                    return [
                                        'error' => __('last_5_password', null, 'patos')
                                    ];
                                }
                            }
                        }
                    }

                    return null;
                })
                ->end();

            $this->validator->label('Email')
                ->value(Input::post('email'))
                ->isEmail()
                ->isMatches(Input::post('re_email'), 'Conferma Email')
                ->add(function () {
                    $emailExist = UsersModel::where('id', '!=', Input::post('id'))
                        ->where('email', Input::post('email'))->first();
                    if (!empty($emailExist)) {
                        return ['error' => __('email_exist', null, 'patos')];
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Validità della password')
                ->value(Input::post('password_expiration_days'))
                ->in('25,50,100,180,365')
                ->end();
        }

        $this->validator->label('Cellulare')
            ->value(Input::post('phone'))
            ->maxLength(17, __('max_phone_length', null, 'patos'))
            ->minLength(3, __('min_phone_length', null, 'patos'))
            ->regex('/^([0-9\(\)\.\-\+\ ]){3,17}$/', __('phone_error', null, 'patos'))
            ->end();

        $this->validator->label('Impedisci ripetizione delle ultime 5 password')
            ->value(Input::post('prevent_password_repetition'))
            ->in('1,2')
            ->end();

        $this->validator->label('Impedisci utilizzo di una password usata negli utlimi 6 mesi')
            ->value(Input::post('prevent_password_repetition_6_months'))
            ->in('1,2')
            ->end();

        $this->validator->label('Blocco utente dopo ripetuti accessi')
            ->value(Input::post('wrong_password_lock'))
            ->in('3,5,10')
            ->end();

        $this->validator->label('Inattività dopo il blocco per accessi errati')
            ->value(Input::post('wrong_password_lock_time'))
            ->in('5,15,30,60')
            ->end();

        $this->validator->label('Impedisci il cambio password più di una volta al giorno')
            ->value(Input::post('prevent_password_change_day'))
            ->in('1,2')
            ->end();

        $this->validator->label('Disattivazione account per mancato utilizzo')
            ->value(Input::post('account_deactivation_for_non_use'))
            ->in('25,50,100,180,365')
            ->end();

        $this->validator->label('Visualizza i soli elementi dell\'utente')
            ->value(Input::post('filter_owner_record'))
            ->in('1,2')
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('notes'))
            ->maxLength(500)
            ->end();

        if (filesUploaded('profile_image')) {
            $this->validator->label('Immagine profilo')
                ->file(Input::files('profile_image'))
                ->required()
                ->allowed(['png', 'jpeg', 'jpg', 'gif'])
                ->maxSize('5MB')
                ->minSize('1KB')
                ->allowedDimensions('50', '1024', '50', '1024')
                ->end();
        }

        if (Input::post('profiles')) {
            foreach (Input::post('profiles') as $profile) {
                $this->validator->label('Profilo ' . $profile)
                    ->value($profile)
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo per la validazione della password
     *
     * @return array
     * @throws Exception
     */
    public function validatePasswordReset(): array
    {
        $this->validator->label('Password')
            ->value(Input::post('password'))
            ->isMatches(Input::post('re_password'), 'Conferma Password')
            ->add(function () {

                $checkPassword = checkPassword(Input::post('password'));

                if ($checkPassword != null) {
                    return $checkPassword;
                }

                // Diverso da Super Admin.
                if (!isSuperAdmin()) {

                    if (Input::post('id') == null) {
                        return ['error' => __('not_exist_user', null, 'patos')];
                    }

                    $passwordHistory = PasswordHistoryModel::where('user_id', Input::post('id'))
                        ->limit(5)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->toArray();

                    if (empty($passwordHistory)) {
                        return null;
                    }

                    $user = UsersModel::select(['prevent_password_change_day', 'prevent_password_repetition'])
                        ->where('id', Input::post('id'))
                        ->first()
                        ->toArray();

                    if (empty($user)) {
                        return [
                            'error' => __('not_exist_user', null, 'patos')
                        ];
                    }

                    $currentDay = date('Y-m-d');
                    foreach ($passwordHistory as $ph) {

                        if ($user['prevent_password_change_day'] === 1) {
                            if ($currentDay === date('Y-m-d', strtotime($ph['created_at']))) {
                                return [
                                    'error' => __('password_change_day', null, 'patos')
                                ];
                            }
                        }

                        if ($user['prevent_password_repetition'] === 1) {
                            if (Password::verify(Input::post('password'), $ph['password']) === true) {
                                return [
                                    'error' => __('last_5_password', null, 'patos')
                                ];
                            }
                        }
                    }
                }
                return null;
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validatore per le operazioni multiple(Delete, Lock/Unlock)
     *
     * @param bool $lockUnlock Indica se si tratta di un operazione di blocco/sblocco utenti
     * @return array
     * @throws Exception
     */
    public function multipleSelection(bool $lockUnlock = true): array
    {
        $this->validator->label('Identificativi elementi');
        $this->validator->value(Input::get('ids'));
        $this->validator->required();
        $this->validator->regex('/^[0-9,]+$/', __('multiple_selection_errors', null, 'patos'));
        $this->validator->add(function () {

            $ids = explode(',', Input::get('ids'));
            $isError = false;

            if (!empty($ids) && is_array($ids)) {
                $isError = true;
            }

            if ($isError) {

                $users = UsersModel::select(['*'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($users === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $users);
                }
            }

            if (!$isError) {

                return ['error' => __('no_permits', null, 'patos')];
            }

            return null;
        });

        $this->validator->end();

        if ($lockUnlock) {
            $this->validator->label('azione');
            $this->validator->value(Input::get('action'));
            $this->validator->required();
            $this->validator->in('lock,unlock');
            $this->validator->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
