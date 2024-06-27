<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use System\Registry;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


return [

    /**
     * Definisco la cartella del template
     */
    'theme' => 'default',


    //------------------------------------------------------------------------------------------------------------------


    /**
     * URI Protocol:
     *
     * Recupera la stringa dell'url
     *
     * PARAMETRI:
     *  'REQUEST_URI'  : $_SERVER['REQUEST_URI']
     *  'QUERY_STRING' : $_SERVER['QUERY_STRING']
     *  'PATH_INFO'    : $_SERVER['PATH_INFO']
     *
     *  ATTENZIONE: impostato su "PATH_INFO", gli URI verranno sempre decodificati tramite URL (URL-decoded) !
     */
    'uri_protocol' => 'REQUEST_URI',


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Sicurezza
     *
     * Caratteri permessi nelle URI.
     */
    'permitted_uri_chars' => 'a-z 0-9~%.:_\-',


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Retrocompabitilità
     * !Non più utilizzato
     */
    'enable_query_strings' => false,


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Url Applicazione Web
     */
    'site_url' => Registry::get('__pat_os_app_domain_name__'),


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Suffisso nelle url
     */
    'url_suffix' => '.html',


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Chartset
     */
    'charset' => 'UTF-8',


    //------------------------------------------------------------------------------------------------------------------


    /**
     * PATH Scrittura logs
     */
    'path_log' => 'Logs',


    //------------------------------------------------------------------------------------------------------------------


    /**
     *
     */
    'allow_get_array' => true,


    //------------------------------------------------------------------------------------------------------------------


    /**
     * IP proxy inverso
     * Se il server si trova dietro un proxy inverso, è necessario autorizzare il proxy
     * Indirizzi IP da cui  dovrebbe fidarsi per intestazioni come HTTP_X_FORWARDED_FOR e HTTP_CLIENT_IP
     * al fine di identificare correttamente l'indirizzo IP del visitatore.
     *
     * Puoi utilizzare un array o un elenco separato da virgole di indirizzi proxy,
     * oltre a specificare intere sottoreti. Ecco alcuni esempi:
     * Virgole separati: '10.0.1.200,192.168.5.0/24'
     * Array:        ['10.0.1.200', '192.168.5.0/24']
     */
    'proxy_ips' => '',


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Lingua di default
     * Definisce quale set della lingua deve essere utilizzato
     */
    'language' => 'it',


    //------------------------------------------------------------------------------------------------------------------

    /**
     * CSRF
     * Nome utilizzato nei form al fine di evitare il Cross Site Request Forgery
     */
    'csrf_token_name' => 'sec_token',


    //------------------------------------------------------------------------------------------------------------------

    /**
     * CSRF gloab
     * Definisce se nei form la stampa del token viene inserta di default o meno.
     */
    'csrf_enable' => true,

    //------------------------------------------------------------------------------------------------------------------

    /**
     * CSRF gloab
     * Definisce la scadenza del token
     */
    'csrf_expire' => 7200,


    //------------------------------------------------------------------------------------------------------------------


    /**
     * Headers
     * Forza negli headers il tipo di chartset
     */
    'force_send_header_output' => false,

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Definizione prefisso creazione cartella file manager
     */
    'prefix_user_dir' => 'utente',
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creazione template index.html
     * Viene utilizzata quando si creano le cartelle per il file manager alla creazione di un nuovo utente o di un nuovo ente
     */
    'tpl_index_html' => '<!DOCTYPE html><html lang="en"><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>',

    /**
     * Creazione template .htaccess
     * Viene utilizzata quando si creano le cartelle per il file manager alla creazione di un nuovo utente o di un nuovo ente
     */
    'tpl_htacces' => 'Options -Indexes' . "\n" . '<FilesMatch "\.(php|tmp|sh|py)$">' . "\n" . 'Order Allow,Deny' . "\n" . 'Deny from all' . "\n" . '</FilesMatch>',

    /**
     * Tema di default del frontend
     */
    'vfo' => 'v1',
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Per i periodi dei tassi di assenza
     */
    'absenceRatesPeriod' => [
        '01' => 'Gennaio',
        '02' => 'Febbraio',
        '03' => 'Marzo',
        '04' => 'Aprile',
        '05' => 'Maggio',
        '06' => 'Giugno',
        '07' => 'Luglio',
        '08' => 'Agosto',
        '09' => 'Settembre',
        '10' => 'Ottobre',
        '11' => 'Novembre',
        '12' => 'Dicembre',
        'It' => 'ITrimestre',
        'IIt' => 'IITrimestre',
        'IIIt' => 'IIITrimestre',
        'IVt' => 'IVTrimestre'
    ],
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Tipologia amministrazione per i bandi di gara
     */
    'administrationType' => [
        '' => '',
        'Organi istituzionali' => 'Organi istituzionali',
        'Ministeri' => 'Ministeri',
        'Organi giurisdizionali e avvocatura' => 'Organi giurisdizionali e avvocatura',
        'Amministrazioni indipendenti' => 'Amministrazioni indipendenti',
        'Regioni' => 'Regioni',
        'Aziende speciali regionalizzate' => 'Aziende speciali regionalizzate',
        'Province' => 'Provincie',
        'Aziende speciali provincializzate' => 'Aziende speciali provincializzate',
        'Comuni' => 'Comuni',
        'Enti di previdenza e prevenzione' => 'Enti di previdenza e prevenzione',
        'Enti preposti ad attività sportive' => 'Enti preposti ad attività sportive',
        'Enti scientifici di ricerca e sperimentazione' => 'Enti scientifici di ricerca e sperimentazione',
        'Enti di promozione culturale e artistica' => 'Enti di promozione culturale e artistica',
        'Aziende speciali municipalizzate' => 'Aziende speciali municipalizzate',
        'Istituti autonomi case popolari' => 'Istituti autonomi case popolari',
        'Aziende del servizio sanitario nazionale' => 'Aziende del servizio sanitario nazionale',
        'Autorità di bacino' => 'Autorità di bacino',
        'Comunità montane' => 'Comunità montane',
        'Comunità di valle' => 'Comunità di valle',
        'Enti di bonifica e di sviluppo agricolo' => 'Enti di bonifica e di sviluppo agricolo',
        'Consorzi di industrializzazione' => 'Consorzi di industrializzazione',
        'Consorzi autonomi di regioni province e comuni' => 'Consorzi autonomi di regioni province e comuni',
        'Consorzi enti ed autorità portuali' => 'Consorzi enti ed autorità portuali',
        'Università ed altri enti' => 'Università ed altri enti',
        'Istituzioni europee' => 'Istituzioni europee',
        'Istituti bancari e finanziari' => 'Istituti bancari e finanziari',
        'Enti ed istituti religiosi' => 'Enti ed istituti religiosi',
        'Concessionari e imprese di gestione reti e infrastrutture' => 'Concessionari e imprese di gestione reti e infrastrutture',
        'Associazioni di imprese' => 'Associazioni di imprese',
        'Imprese a partecipazione pubblica' => 'Imprese a partecipazione pubblica',
        'Consorzi di imprese' => 'Consorzi di imprese',
        'Imprese ed altri soggetti privati non in forma associata' => 'Imprese ed altri soggetti privati non in forma associata',
        'Associazioni di categoria e organizzazioni sindacali' => 'Associazioni di categoria e organizzazioni sindacali',
        'Camere di commercio' => 'Camere di commercio',
        'Soggetti esterni' => 'Soggetti esterni',
        'Provveditorato regionale alle opere pubbliche' => 'Provveditorato regionale alle opere pubbliche',
        'Organismi di diritto pubblico' => 'Organismi di diritto pubblico',
        'Altri soggetti pubblici e privati' => 'Altri soggetti pubblici e privati',
        'Ente pubblico non economico' => 'Ente pubblico non economico'
    ],
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Organi di indirizzo politico per il Personale
     */
    'politicalAdministrative' => [
        1 => 'Comitato di Indirizzo',
        2 => 'Collegio dei revisori dei conti',
        3 => 'Direzione Generale'
    ],

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Organi di indirizzo politico per il Personale
     */
    'assignmentTypologies' => [
        1 => 'Incarichi retribuiti e non retribuiti dei propri dipendenti',
        2 => 'Incarichi retribuiti e non retribuiti affidati a soggetti esterni'
    ],

    //------------------------------------------------------------------------------------------------------------------
    /**
     * Tipologie di provvedimenti
     */
    'measureTypologies' => [
        13 => 'Provvedimento dirigenziale',
        14 => 'Provvedimento organo indirizzo-politico'
    ],

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Tipologie per le normative
     */
    'normativeTypologies' => [
        1 => 'Decreto dirigenziale',
        2 => 'Decreto interministeriale',
        3 => 'Decreto legge',
        4 => 'Decreto legislativo',
        5 => 'Decreto ministeriale',
        6 => 'Decreto Presidente Consiglio dei Ministri',
        7 => 'Decreto Presidente della Repubblica',
        8 => 'Legge',
        9 => 'Regolamento CEE',
        10 => 'Altro',
    ],

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Abilità il log delle azione sul archivio degli allegati.
     */
    'write_file_manager_filesystem' => false,

    //------------------------------------------------------------------------------------------------------------------


    /**
     * Prefisso per le chiamate REST
     */
    'prefix_api' => API,

    /**
     * Invio per email se l'interprete ha sollevato un errore.
     */
    'mail_errors' => false,

    /**
     * Limita le richieste API
     */
    'limits_call_api' => 100,
];
