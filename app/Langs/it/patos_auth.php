<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [

    /**
     * Label condiviso con altre schermate
     */
    'anchor_back_auth' => 'Autenticazione',

    /**
     * @route /auth.html
     * @path admin/auth/auth.blade.php
     */
    'username_or_email_not_valid' => 'Username o password errati',
    'token_not_not_valid' => 'Token non valido. riprovare l\'autenticazione',
    'user_disabled' => 'Utente disabilitato per mancato utilizzo.',
    'head_auth_user' => 'Autenticazione utente',
    'placeholder_auth_username' => 'Email o Username',
    'placeholder_auth_password' => 'Password',
    'anchor_lost_password' => 'Password dimenticata?',
    'btn_auth_submit' => 'Accedi',

    /**
     * @route /lost-password.html
     * @path admin/auth/lostpassword.blade.php
     */
    'head_auth_lost_password' => 'Recupero password',
    'placeholder_lost_password_email' => 'Email',
    'btn_get_new_password' => 'Ottieni nuova password',
    'notify_email_not_found' => 'Email non trovata o non attiva',
    'notify_email_subject' => '%s - Procedura di recupero password avviata.',

    /**
     * @route /recovery/password.html
     * @path  admin/auth/recovery_password.blade.php
     */
    'head_recovery_password' => 'Recupera password',
    'msg_recovery_password' => 'Inserisci la nuova password per completare la procedura di recupero.',
    'placeholder_password' => 'Password',
    'placeholder_repeat_password' => ' Ripeti Password',
    'btn_set_new_password' => 'Salva Password',
    'notify_error_head' => 'Attenzione',
    'notify_error_body' => 'Token non valido oppure la procedura di recupero password &egrave; scaduta.',

    /**
     * @route /auth.html
     * @path Http/Web/Auth/AuthAdminController.php
     */
    'auth_blocked_title' => 'Accesso utente - bloccato',
    'auth_blocked_description' => 'Accesso utente bloccato "%s" nel giorno %s alle ore %s per superamento tentativi di accesso.',
    'auth_success_title' => 'Accesso utente - riuscito',
    'auth_success_description' => 'Accesso utente "%s" riuscito nel giorno %s alle ore %s.',
    'auth_failed_title' => 'Accesso utente - fallito',
    'auth_failed_description' => 'Accesso utente "%s" fallito nel giorno %s alle ore %s.',
    'auth_failed_block_15' => 'Hai effettuato troppi tentativi di accesso. Sei stato bloccato per 15 minuti',

    /**
     * @route /logout.html
     * @path Http/Web/Auth/LogoutAdminController.php
     */
    'auth_logout_title' => 'Logout utente',
    'auth_logout_description' => 'Logout "%s" nel giorno %s alle ore %s.',

    /**
     * @route /lost-password.html
     * @path Http/Web/Auth/LostPasswordAdminController.php
     */
    'auth_request_recovery_success_title' => 'Richiesta di recupero password - riuscito',
    'auth_request_recovery_success_description' => 'L\'utente "%s" in data %s alle ore %s ha effettuato con successo una richiesta di recupero della password.',
    'auth_request_recovery_failed_title' => 'Richiesta di recupero password - fallita',
    'auth_request_recovery_failed_description' => 'L\'utente "%s" in data %s alle ore %s ha effettuato una richiesta fallita di recupero della password.',
    'auth_password_reset_failed_title' => 'Richiesta di recupero password - utente non trovato',
    'auth_password_reset_failed_description' => 'In data %s alle ore %s, la richiesta di recupero password non è andata a buon fine, perché l\'applicativo non è riuscito a trovate un utente.',
    'auth_password_reset_expire_title' => 'Richiesta di recupero password - tempo scaduto',
    'auth_password_reset_expire_description' => 'In data %s alle ore %s, non è stato possibile resettare la password per l\'utenza "%s". Tale richiesta ha superato il tempo limite delle impostato a 24 ore.',
    'auth_password_reset_success_title' => 'Richiesta di recupero password - completata',
    'auth_password_reset_success_description' => 'In data %s alle ore %s, l\'utenza "%s" ha completato con successo la procedura di recupero password.',
    'temporary_error' => 'Errore temporaneo. Riprova pi&ugrave; tardi oppure contatta il servizio assistenza',
    'invalid_user_id' => 'Identificativo utente non valido',
    'invalid_institution_id' => 'Identificativo ente non valido',
    'recovery_password_error' => 'Errore nella procedura di recupero della password',
    'user_not_found' => 'Utente non trovato',
    'expired_recovery_procedure' => 'Procedura di recovery password scaduta',
    'invalid_token' => 'Token non valido',
    'recovery_password_complete' => 'Procedura recupero password completata - '
];