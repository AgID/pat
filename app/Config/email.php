<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [

    /**
     * Configurazione invio Email di default
     */
    'default' => [

        /**
         * @Description: Setta L'user Agent
         * @Options: None
         * @DefaultValue: Eamil
         */
        'useragent' => 'Pat Open Source',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Il protocollo di invio della posta.
         * @Options: mail, sendmail, or smtp
         * @DefaultValue: mail
         */
        'protocol' => _env('MAIL_SMTP_PROTOCOL'),


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Il percorso del server a Sendmail.
         * @Options: None
         * @DefaultValue: /usr/sbin/sendmail
         */
        // 'mailpath' => '',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Indirizzo server SMTP.
         * @Options: None
         * @DefaultValue:
         */
        'smtp_host' => _env('MAIL_SMTP_HOST'),


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: SMTP Username.
         * @Options: None
         * @DefaultValue:
         */
        'smtp_user' => _env('MAIL_USERNAME'),


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: SMTP Password.
         * @Options: None
         * @DefaultValue:
         */
        'smtp_pass' => _env('MAIL_PASSWORD'),


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: SMTP Port.
         * @Options: None
         * @DefaultValue:
         */
        'smtp_port' => _env('MAIL_PORT'),


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: SMTP Timeout (in secondi).
         * @Options: None
         * @DefaultValue:
         */
        'smtp_timeout' => 5,


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Abilita connessioni SMTP persistenti.
         * @Options: TRUE oppure FALSE (boolean)
         * @DefaultValue: FALSE
         */
        // 'smtp_keepalive' => '',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: SMTP Encryption
         * @Options: tls or ssl
         * @DefaultValue: No Default
         */
        'smtp_crypto' => _env('MAIL_SMTP_CRYPTO'),


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Abilita word-wrap.
         * @Options: TRUE oppure FALSE
         * @DefaultValue: TRUE
         */
        'wordwrap' => true,


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Conteggio caratteri prima di  concludere la riga.
         * @Options: numero
         * @DefaultValue: 76
         */
        'wrapchars' => 76,


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Tipo di posta. Se invii un'e-mail HTML, devi inviarla come una pagina web completa. Assicurati
         * di non avere collegamenti relativi o percorsi di immagini relativi, altrimenti non funzioneranno
         * @Options: text oppure html
         * @DefaultValue: text
         */
        'mailtype' => 'html',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Character set (utf-8, iso-8859-1, etc.).
         * @Options: --
         * @DefaultValue: utf-8
         */
        // 'charset' => '',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Se convalidare l'indirizzo e-mail.
         * @Options: TRUE oppure FALSE
         * @DefaultValue: FALSE
         */
        // 'validate' => '',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Priorità email. 1 = più alto. 5 = più basso. 3 = normale.
         * @Options: 1, 2, 3, 4, 5
         * @DefaultValue: 3
         */
        'priority' => 3,


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Carattere di nuova riga. (Usa "\ r \ n" per conformarti a RFC 822).
         * @Options: “\r\n” oppure “\n” oppure “\r”
         * @DefaultValue: \n
         */
        'crlf' => '\r\n',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description:  Carattere di nuova riga. (Usa "\ r \ n" per conformarti a RFC 822)
         * @Options:  “\r\n” oppure “\n” oppure “\r”
         * @DefaultValue: \n
         */
        'newline' => '\r\n',


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Abilita modalità batch BCC.
         * @Options: TRUE oppure FALSE
         * @DefaultValue: FALSE
         */
        'bcc_batch_mode' => false,


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Numero di messaggi di posta elettronica in ogni batch BCC.
         * @Options: None
         * @DefaultValue: 200
         */
        'bcc_batch_size' => 200,


        //--------------------------------------------------------------------------------------------------------------


        /**
         * @Description: Abilita notifica messaggio dal server
         * @Options: TRUE oppure FALSE
         * @DefaultValue: FALSE
         */
        // 'dsn' => ''
    ],


    /**
     * Configurazione invio email customizzato
     */
//    'custom' => [
//
//    ]
];
