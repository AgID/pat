<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use Helpers\FileSystem\File;
use Helpers\OS;
use Helpers\Security\Acl;
use System\Email;
use System\Input;
use System\Registry;
use System\Security;
use System\Utf8;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('verifyFirstOrLastSlash')) {

    /**
     * Ritorna il site url
     *
     * @param string $uri
     * @param null $protocol
     * @return string
     * @throws Exception
     */
    function verifyFirstOrLastSlash($uri = null, $ltrim = false): string
    {
        $segments = defined('CUSTOM_PATH') ? CUSTOM_PATH : '';
        $uri = trim($uri, '/');

        if (strlen($segments) >= 1 && strlen($uri) >= 1) {
            $uri = (substr($segments, -1) !== '/' && $uri[0] !== '/')
                ? $segments . '/' . $uri
                : $segments . $uri;
        } else {
            if (strlen($segments) >= 1 && substr($segments, -1) !== '/') {
                $uri = $segments;
            }
        }

        if ($ltrim) {
            $uri = ltrim($uri, '/');
        }

        return $uri;
    }
}

if (!function_exists('siteUrl')) {

    /**
     * Ritorna il site url
     *
     * @param string $uri
     * @param null $protocol
     * @return string
     * @throws Exception
     */
    function siteUrl(string $uri = '', $protocol = null): string
    {

        $uri = verifyFirstOrLastSlash($uri);
        $base = new \System\Base();

        return $base->siteUrl($uri, $protocol);
    }
}


if (!function_exists('form_open')) {
    /**
     * @param string    the URI segments of the form destination
     * @param array    a key/value pair of attributes
     * @param array    a key/value pair hidden data
     * @return    string
     */
    function form_open($action = '', $attributes = [], $hidden = [], $csfr_token = null, $reload_all_request = false)
    {

        $base = new \System\Base();
        $uri = new \System\Uri();
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');
        $csfrIsEnable = $config->get('csrf_enable');

        if (!$action) {

            $action = $base->siteUrl($uri->uriString());
        } elseif (preg_match('#{*?}#s', $action)) {

            $action = $action;
        } elseif (strpos($action, '://') === false) {

            $action = verifyFirstOrLastSlash($action);
            $action = $base->siteUrl($action);
        }

        $attributes = stringifyAttributes($attributes);

        if (stripos($attributes, 'method=') === false) {
            $attributes .= ' method="post"';
        }

        if (stripos($attributes, 'accept-charset=') === false) {
            $attributes .= ' accept-charset="' . strtolower(config('charset', null, 'app')) . '"';
        }

        $form = '<form action="' . $action . '"' . $attributes . ">\n";

        if (is_array($hidden)) {
            foreach ($hidden as $name => $value) {
                $form .= '<input type="hidden" name="' . $name . '" value="' . htmlEscape($value) . '" />' . "\n";
            }
        }

        if (($csfrIsEnable === true) && ($csfr_token === true || $csfr_token === null)) {

            $form .= csrf_input_token($reload_all_request);
        }

        return $form . "\n";
    }
}

if (!function_exists('getDynamicController')) {
    /**
     * @return
     */
    function getDynamicController()
    {
        $controller = null;
        $uri = (int) uri()->segment(2, 0);

        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $result = \System\Database::table('section_fo')
            ->select(['section_fo.id', 'name', 'controller'])
            //->where('section_fo.hide', 0)
            ->where('section_fo.id', $uri);

        $result = $result ?? null;

        $result = $result->first();

        // Mantiene raggiungibile la pagina di servizio anche nei database Open
        // precedenti che non contengono ancora la relativa sezione.
        if (empty($result) && $uri === 256) {
            return '\\Http\\Web\\Front\\OtherSectionsFrontController@accessibility';
        }

        if (!empty($result)) {

            /*
             * La pagina Accessibilita e richiamata dal footer con un identificativo
             * stabile e deve sempre usare il controller applicativo dedicato. In
             * questo modo restano funzionanti anche le installazioni Open precedenti
             * che hanno creato la sezione senza valorizzarne il controller.
             */
            if ($uri === 256) {
                $result->controller = '\\Http\\Web\\Front\\OtherSectionsFrontController@accessibility';
            }

            $section = new \Helpers\Utility\SectionFrontOffice();

            $ancestors = $section->getAncestors($result->id, false, true, null, $institutionTypeId);

            // In caso una pagina dovesse avere più traduzioni(una per il tipo ente e una per Ente), allora si da
            //precedenza a quelle per l'Ente
            $pagine = [];
            foreach ($ancestors as $a) {
                if (empty($pagine[$a->id])) {
                    $pagine[$a->id] = $a;
                }
            }

            $ancestors = [];
            $ancestorsBreadcrumb = [];
            foreach ($pagine as $p) {
                $ancestors[] = $p;
                $ancestorsBreadcrumb[] = (object) ['id' => $p->id, 'name' => $p->name, 'label' => $p->label];
            }

            Registry::set('__breadcrumbs_front_office', $ancestorsBreadcrumb);

            $controller = "";

            $arrayController = explode('@', $result->controller);
            //            \System\Registry::set('__controller_detail', "\Http\Web\Front\\" . $arrayController[0] . '@details');
            Registry::set('__controller_detail', $arrayController[0] . '@details');

            if (count($arrayController) > 1) {

                $controller .= $result->controller;
            } else {

                $controller .= $result->controller . '@index';
            }
        }

        return $controller;
    }
}


if (!function_exists('getDynamicControllerOpendata')) {
    /**
     * @return string|null
     */
    function getDynamicControllerOpendata(): ?string
    {
        $controller = null;
        $uriSegmentId = uri()->segment(3, 0);

        $result = \System\Database::table('section_fo')
            ->select(['id', 'controller_open_data'])
            ->where('id', $uriSegmentId)
            ->whereNotNull('controller_open_data')
            ->where('controller_open_data', '!=', '')
            ->first();

        if (!empty($result)) {

            $controller = "\Http\Web\Front\\";

            $arrayController = explode('@', $result->controller_open_data);

            if (count($arrayController) > 1) {

                $controller .= $result->controller_open_data;
            } else {

                $controller .= $result->controller_open_data . '@index';
            }
        }

        return $controller;
    }
}
if (!function_exists('PatOsInstituteId')) {

    /**
     * return institute ID
     *
     * @return Mix bool|int
     */
    function PatOsInstituteId()
    {
        //Controlla se le informazioni dell'Ente sono nel Registro
        if (Registry::exist('pat_os_info_domain')) {
            $patOS_InfoDomain = Registry::get('pat_os_info_domain');
            return $patOS_InfoDomain['id'];
        }

        //Altrimenti le recupera dalla sessione
        if (session()->has('pat_os_domain_info')) {
            $patOS_InfoDomain = session()->get('pat_os_domain_info');
            return $patOS_InfoDomain['id'];
        }

        return false;
    }
}

if (!function_exists('checkAlternativeInstitutionId')) {

    /**
     * Ritorna l'ID dell'ente che si sta gestendo.
     *
     * L'ente è determinato dal dominio corrente.
     *
     * @return Mix bool|int
     */
    function checkAlternativeInstitutionId()
    {
        return PatOsInstituteId();
    }
}

if (!function_exists('instituteDir')) {

    /**
     * Ritorna il nome della cartella dei media dell'Ente specificato nei parametri, altrimenti dell'ente in cui si è loggati
     *
     * @param $shortName {nome breve dell'ente}
     * @return mixed|null
     */
    function instituteDir($shortName = null)
    {
        $pathName = null;

        //Controlla se è stato passato nei parametri il nome breve dell'ente
        if (!empty($shortName)) {

            $pathName = $shortName;
        } else {

            // Recupero il nome breve dell'ente associato al dominio corrente.
            if (Registry::exist('pat_os_info_domain')) {
                $patOS_InfoDomain = Registry::get('pat_os_info_domain');
                $pathName = $patOS_InfoDomain['short_institution_name'];
            }
        }

        if ($pathName !== null) {

            //Ritorna il nome della cartella dei media dell'Ente
            return $pathName;
        }

        return null;
    }
}

if (!function_exists('patOsInstituteInfo')) {

    /**
     * return the information of the institution
     *
     * @param null $args
     * @return Mix string|array|bool
     */
    function patOsInstituteInfo($args = null)
    {
        $found = false;
        $patOS_Info = null;

        //Controlla se le informazioni sono presenti nel Registro
        if (Registry::exist('pat_os_info_domain')) {

            $patOS_Info = Registry::get('pat_os_info_domain');
            $found = true;
        }

        //Se non sono presenti nel registro controlla se sono presenti in sessione
        if (!$found && session()->has('pat_os_domain_info')) {

            $patOS_Info = session()->get('pat_os_domain_info');
        }

        // Se l'argomento non esiste ritorna tutta la matrice.
        if ($args === null) {

            return $patOS_Info;
        }

        // Se l'argomento è un array seleziona più chiavi.
        if (is_array($args) && count($args) >= 1 && !empty($patOS_Info) && is_array($patOS_Info)) {

            $data = [];

            foreach ($args as $a) {

                if (isset($patOS_Info[$a])) {

                    $data[$a] = $patOS_Info[$a];
                }
            }

            return $data;
        }

        // Se l'argomento è una stringa ritorna una chiave solamente.
        if (is_string($args)) {

            return !empty($patOS_Info[$args]) ? $args : false;
        }

        return false;
    }
}

if (!function_exists('normalizeSmtpValue')) {

    /**
     * Normalizza un valore di configurazione mail rimuovendo spazi e caratteri
     * invisibili ai bordi. I parametri SMTP/PEC possono essere copiati da email,
     * documenti o password manager con spazi non visibili: lasciarli nel valore
     * salvato o risolto puo causare errori DNS o autenticazioni fallite difficili
     * da ricondurre alla configurazione inserita.
     *
     * @param mixed $value Valore di configurazione da normalizzare.
     *
     * @return string
     */
    function normalizeSmtpValue($value) {
        $value = strip_tags((string)$value);
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
        $value = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value);

        return trim($value, " \t\n\r\0\x0B\xC2\xA0");
    }
}

if (!function_exists('normalizeSmtpHost')) {

    /**
     * Normalizza l'host SMTP usando la stessa pulizia applicata agli altri campi
     * mail. L'host viene tenuto separato per rendere esplicito il punto che puo
     * generare errori di risoluzione come "Name or service not known".
     *
     * @param mixed $value Host SMTP da normalizzare.
     *
     * @return string
     */
    function normalizeSmtpHost($value) {
        return normalizeSmtpValue($value);
    }
}

if (!function_exists('loadConfigPecMail')) {

    // Configurazione P.E.C. per invio Email
    function loadConfigPecMail()
    {
        $configs = patOsInstituteInfo([
            'pec_smtp_user',
            'pec_smtp_from',
            'pec_smtp_pass',
            'pec_smtp_host',
            'pec_smtp_port',
            'pec_smtp_security',
            'pec_show_smtp_auth',
        ]);

        $mailConfig = [];
        $mailConfig['protocol'] = 'smtp';

        // Mappatura delle chiavi PEC sulle proprietà standard della classe Email
        $mailConfig['smtp_user'] = !empty($configs['pec_smtp_user']) ? normalizeSmtpValue(checkDecrypt($configs['pec_smtp_user'])) : '';
        $mailConfig['smtp_from'] = !empty($configs['pec_smtp_from']) ? normalizeSmtpValue(checkDecrypt($configs['pec_smtp_from'])) : '';
        $mailConfig['smtp_pass'] = !empty($configs['pec_smtp_pass']) ? normalizeSmtpValue(checkDecrypt($configs['pec_smtp_pass'])) : '';
        $mailConfig['smtp_host'] = !empty($configs['pec_smtp_host']) ? normalizeSmtpHost(checkDecrypt($configs['pec_smtp_host'])) : '';
        $mailConfig['smtp_port'] = !empty($configs['pec_smtp_port']) ? normalizeSmtpValue($configs['pec_smtp_port']) : '';
        $mailConfig['smtp_crypto'] = !empty($configs['pec_smtp_security']) ? strtolower(normalizeSmtpValue($configs['pec_smtp_security'])) : '';
        /*
         * Quando il flag non e' impostato l'autenticazione viene dedotta dalla presenza delle
         * credenziali: un Ente che ha salvato username e password si aspetta che vengano usate,
         * e senza questa deduzione il mailer dialogherebbe in chiaro con un server che le
         * richiede, fallendo con "Relay access denied". La deduzione appartiene alla
         * configurazione risolta, quindi vale anche per i controlli che la leggono.
         */
        $mailConfig['smtp_auth'] = isset($configs['pec_show_smtp_auth'])
            ? (int)normalizeSmtpValue($configs['pec_show_smtp_auth']) === 1
            : (!empty($mailConfig['smtp_user']) && !empty($mailConfig['smtp_pass']));

        $mailConfig['crlf'] = "\r\n";
        $mailConfig['newline'] = "\r\n";
        $mailConfig['wordwrap'] = true;
        $mailConfig['mailtype'] = "html";
        $mailConfig['priority'] = 3;
        $mailConfig['wrapchars'] = 76;

        return $mailConfig;
    }
}

if (!function_exists('mailSenderAddress')) {

    /**
     * Restituisce l'indirizzo mittente da usare con una configurazione mail già risolta,
     * oppure una stringa vuota quando nessun indirizzo utilizzabile è disponibile.
     *
     * Il mittente non coincide necessariamente con lo username: diversi gestori rilasciano
     * credenziali di accesso che non sono indirizzi email, e per questo i form dell'Ente
     * espongono il campo "From" accanto allo username. La precedenza applicata qui è la
     * stessa di System\Email in from(), che sostituisce il mittente indicato dal chiamante
     * con smtp_from ogni volta che è configurato: per questo un smtp_from presente ma non
     * utilizzabile come indirizzo non viene sostituito dallo username, che il mailer
     * scarterebbe comunque.
     *
     * La stringa vuota indica che l'invio non è tentabile: i chiamanti devono trattarla
     * come configurazione incompleta, invece di lasciare che l'errore emerga dalla
     * validazione interna del mailer, dove la causa non sarebbe riconoscibile.
     *
     * @param array $configs Configurazione email già risolta e decifrata.
     * @return string Indirizzo mittente utilizzabile, stringa vuota se non disponibile.
     */
    function mailSenderAddress(array $configs = []): string
    {
        $smtpFrom = normalizeSmtpValue((string)($configs['smtp_from'] ?? ''));

        if ($smtpFrom !== '') {
            return filter_var($smtpFrom, FILTER_VALIDATE_EMAIL) ? $smtpFrom : '';
        }

        $smtpUser = normalizeSmtpValue((string)($configs['smtp_user'] ?? ''));

        return filter_var($smtpUser, FILTER_VALIDATE_EMAIL) ? $smtpUser : '';
    }
}

if (!function_exists('isPecMailTransportValid')) {
    /**
     * Verifica il solo trasporto della configurazione PEC dell'Ente, senza il mittente.
     *
     * Il servizio PEC usa il trasporto SMTP: host e porta devono essere validi, le
     * modalità di sicurezza ammesse sono quelle esposte dal form dell'Ente e le
     * credenziali sono obbligatorie soltanto quando è abilitata l'autenticazione.
     *
     * Sullo username non viene verificato alcun formato: diversi gestori PEC rilasciano
     * credenziali di accesso che non sono indirizzi email, e pretendere qui un indirizzo
     * renderebbe privi di configurazione PEC valida Enti che inviano regolarmente.
     *
     * La separazione dal mittente serve a distinguere le due cause di una configurazione
     * incompleta: gli avvisi possono così indicare il campo effettivamente mancante,
     * invece di segnalare genericamente la sezione.
     *
     * @param array $configs Configurazione PEC già risolta e decifrata
     * @return bool
     */
    function isPecMailTransportValid(array $configs = []): bool
    {
        $protocol = strtolower(trim((string)($configs['protocol'] ?? '')));
        $smtpCrypto = strtolower(trim((string)($configs['smtp_crypto'] ?? '')));

        if (
            $protocol !== 'smtp'
            || !in_array($smtpCrypto, ['', 'no', 'ssl', 'tls'], true)
        ) {
            return false;
        }

        return isMailConfigValid($configs);
    }
}

if (!function_exists('isMailSenderRequirementMet')) {
    /**
     * Verifica il requisito sul mittente di una configurazione mail dell'Ente.
     *
     * Il mittente serve sempre, indipendentemente dall'autenticazione: quest'ultima riguarda
     * l'accesso al server SMTP, mentre il mittente è un'intestazione del messaggio, che la
     * classe System\Email valida a ogni invio. Un server che accetta posta senza credenziali
     * pretende comunque un indirizzo mittente.
     *
     * L'indirizzo è il campo "From" quando valorizzato, altrimenti lo username, che deve
     * allora essere un indirizzo email: le utenze di alcuni gestori non lo sono, e in quel
     * caso è il "From" a doverlo fornire. Lo username invece è obbligatorio soltanto quando
     * è abilitata l'autenticazione, condizione già verificata dal trasporto: senza
     * autenticazione può mancare, purché il "From" sia compilato.
     *
     * Il requisito riguarda le sole configurazioni dell'Ente: in quella di piattaforma il
     * mittente è indicato dai chiamanti, che passano indirizzi di servizio propri.
     *
     * @param array $configs Configurazione mail già risolta e decifrata.
     * @return bool
     */
    function isMailSenderRequirementMet(array $configs = []): bool
    {
        return mailSenderAddress($configs) !== '';
    }
}

if (!function_exists('isMailConfigEnteValid')) {
    /**
     * Verifica una configurazione mail dell'Ente: trasporto valido e requisito sul mittente.
     *
     * A differenza di isMailConfigValid(), che valida il solo trasporto ed è usata anche per
     * la configurazione di piattaforma, qui si aggiunge il mittente alle condizioni descritte
     * da isMailSenderRequirementMet().
     *
     * @param array $configs Configurazione mail dell'Ente già risolta e decifrata.
     * @return bool
     */
    function isMailConfigEnteValid(array $configs = []): bool
    {
        return isMailConfigValid($configs) && isMailSenderRequirementMet($configs);
    }
}

if (!function_exists('isPecMailConfigValid')) {
    /**
     * Verifica i parametri minimi della configurazione PEC dell'Ente corrente.
     *
     * Al trasporto verificato da isPecMailTransportValid() aggiunge il requisito sul
     * mittente descritto da isMailSenderRequirementMet(): una casella PEC che non espone
     * un indirizzo mittente utilizzabile non puo' recapitare nulla.
     *
     * @param array $configs Configurazione PEC già risolta e decifrata
     * @return bool
     */
    function isPecMailConfigValid(array $configs = []): bool
    {
        return isPecMailTransportValid($configs) && isMailSenderRequirementMet($configs);
    }
}

if (!function_exists('mailFromRichiesto')) {
    /**
     * Verifica se il campo "From" è necessario per la configurazione indicata.
     *
     * Vale per entrambe le sezioni del form dell'Ente, SMTP ordinaria e PEC: il mittente è
     * il campo "From" quando valorizzato, altrimenti lo username. Le utenze di alcuni
     * gestori non sono indirizzi email, e senza autenticazione lo username può mancare del
     * tutto: in entrambi i casi il "From" è l'unico indirizzo disponibile e la sua assenza
     * rende impossibile l'invio, che fallirebbe nella validazione del mittente eseguita
     * dal mailer.
     *
     * Il campo non viene richiesto a chi non sta configurando quella sezione: almeno un
     * parametro compilato è la condizione che rende sensata la richiesta.
     *
     * @param string|null $username Username inserito o già salvato, in chiaro.
     * @param string|null $from Indirizzo "From" inserito o già salvato, in chiaro.
     * @param bool $sezioneCompilata Se la sezione contiene almeno un parametro.
     * @return bool
     */
    function mailFromRichiesto($username = null, $from = null, bool $sezioneCompilata = true): bool
    {
        if (!$sezioneCompilata) {
            return false;
        }

        $username = normalizeSmtpValue((string)($username ?? ''));
        $from = normalizeSmtpValue((string)($from ?? ''));

        if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return !filter_var($username, FILTER_VALIDATE_EMAIL);
    }
}

if (!function_exists('institutionMailSender')) {

    /**
     * Istanzia il mailer System\Email con la configurazione risolta dell'ente.
     *
     * @param array $configs Configurazione email gia risolta.
     * @param int|null $senderType Tipologia di invio, se gia nota al chiamante.
     * @return Email
     */
    function institutionMailSender(array $configs = [], $senderType = null) {
        if ($senderType === null) {
            $senderType = 0;
        }

        $normalizedConfigKeys = [
            'smtp_user',
            'smtp_from',
            'smtp_pass',
            'smtp_port',
            'smtp_crypto',
            'smtp_security',
            'oauth2_username',
            'oauth2_password',
            'oauth2_tenant_id',
            'oauth2_client_id',
            'oauth2_client_secret',
            'acs_sender_email',
            'acs_endpoint',
            'acs_access_key',
            'graph_email',
        ];

        foreach ($normalizedConfigKeys as $normalizedConfigKey) {
            if (isset($configs[$normalizedConfigKey]) && is_scalar($configs[$normalizedConfigKey])) {
                $configs[$normalizedConfigKey] = normalizeSmtpValue((string)$configs[$normalizedConfigKey]);
            }
        }

        if (isset($configs['smtp_host']) && is_scalar($configs['smtp_host'])) {
            $configs['smtp_host'] = normalizeSmtpHost((string)$configs['smtp_host']);
        }

        $mailSenderClass = Email::class;

        /*
         * Le configurazioni Ente, PEC e quelle costruite direttamente dai form non ereditano
         * automaticamente i parametri tecnici definiti in app/Config/email.php. Il timeout viene
         * applicato qui, nel punto comune in cui viene creata ogni istanza del mailer, soltanto
         * quando il chiamante non ne ha gia specificato uno. Non viene effettuato alcun merge della
         * configurazione: host, mittente e credenziali restano quelli del canale selezionato.
         */
        if (!isset($configs['smtp_timeout'])) {
            $defaultMailConfig = loadConfigMail();
            if (isset($defaultMailConfig['smtp_timeout'])) {
                $configs['smtp_timeout'] = (int)$defaultMailConfig['smtp_timeout'];
            }
        }

        return new $mailSenderClass($configs);
    }
}

if (!function_exists('sendEmailGuaranteed')) {

    /**
     * Invia il messaggio gia' costruito su $email e restituisce l'esito del mailer.
     * La configurazione viene scelta dal chiamante prima di costruire il messaggio;
     * questa funzione non la sostituisce e non ritenta l'invio con altri account.
     *
     * @param \System\Email $email Istanza gia' configurata con from/to/subject/message.
     * @return bool
     */
    function sendEmailGuaranteed(\System\Email $email): bool
    {
        return $email->send();
    }
}

if (!function_exists('getMailConfig')) {

    /**
     * Restituisce la configurazione di invio email per l'Ente corrente oppure per la piattaforma.
     *
     * Le due configurazioni non vengono mai unite tra loro (nessun merge campo-per-campo):
     * un invio deve usare o interamente quella dell'Ente o interamente quella di piattaforma,
     * per evitare configurazioni ibride (es. host dell'Ente con credenziali di piattaforma)
     * che non corrispondono a nessun account SMTP realmente funzionante.
     *
     * Per lo scope 'institution' viene letta la configurazione dell'Ente e costruita direttamente
     * la sola configurazione SMTP classica, l'unico trasporto gestito qui. Se l'Ente non ha alcun
     * campo SMTP valorizzato viene restituito un array vuoto: la decisione se ricadere sulla
     * configurazione di piattaforma (garanzia di consegna) spetta al chiamante.
     *
     * @param string $scope 'institution' (default) per la config SMTP dell'Ente corrente, 'platform' per quella di piattaforma
     * @return array
     */
    function getMailConfig(string $scope = 'institution', int $institutionId = 0): array
    {
        if ($scope === 'platform') {
            $platformConfig = loadConfigMail();

            /*
             * La configurazione di piattaforma arriva da .env e non passa dai form Ente,
             * dove il valore della sicurezza SMTP viene gia normalizzato. Il mailer del
             * core verifica STARTTLS con confronto stretto su "tls", quindi normalizziamo
             * qui per evitare che valori come "TLS" saltino la negoziazione TLS.
             */
            if (isset($platformConfig['smtp_crypto'])) {
                $platformConfig['smtp_crypto'] = strtolower($platformConfig['smtp_crypto']);
            }

            /*
             * In app/Config/email.php questi valori storicamente sono dichiarati tra
             * apici singoli ('\r\n'), quindi arrivano come testo letterale e non come
             * caratteri CRLF reali. Le configurazioni Ente e i test SMTP usano invece
             * "\r\n": riallineiamo la piattaforma allo stesso formato prima di creare
             * il mailer, evitando differenze nel dialogo SMTP del fallback.
             */
            $platformConfig['crlf'] = "\r\n";
            $platformConfig['newline'] = "\r\n";

            /*
             * Per la configurazione di piattaforma non esiste un flag separato "usa
             * autenticazione" come nelle impostazioni Ente: se .env espone username e
             * password, il server SMTP va usato autenticandosi. Senza questa assegnazione
             * un valore vuoto di smtp_auth puo sovrascrivere l'inferenza automatica del
             * mailer e causare invii non autenticati con errore "Relay access denied".
             */
            $platformConfig['smtp_auth'] = !empty($platformConfig['smtp_user'])
                && !empty($platformConfig['smtp_pass']);

            return $platformConfig;
        }

        // Invio Email
        $mailConfigKeys = [
            'smtp_user',
            'smtp_from',
            'smtp_pass',
            'smtp_host',
            'smtp_port',
            'smtp_security',
            'smtp_auth',
        ];

        $trasparenzaUrl = null;

        if ($institutionId > 0) {

            $institution = \Model\InstitutionsModel::withoutGlobalScopes()
                ->select(array_values(array_unique(array_merge(
                    ['id', 'trasparenza_urls'],
                    $mailConfigKeys
                ))))
                ->where('id', $institutionId)
                ->first();

            if (empty($institution)) {
                return [];
            }

            $customInstitution = [];

            foreach ($mailConfigKeys as $mailConfigKey) {
                $customInstitution[$mailConfigKey] = $institution->$mailConfigKey ?? null;
            }

            $trasparenzaUrl = $institution->trasparenza_urls ?? null;
        } else {
            $customInstitution = patOsInstituteInfo($mailConfigKeys);
            $institutionInfo = patOsInstituteInfo(['trasparenza_urls']);
            $trasparenzaUrl = $institutionInfo['trasparenza_urls'] ?? null;
        }

        if (empty($customInstitution) || !array_filter($customInstitution)) {

            // Nessun campo SMTP valorizzato per l'Ente: nessun fallback automatico su
            // piattaforma in questa funzione, la decisione spetta al chiamante.
            return [];
        }

        $senderType = isset($customInstitution['sender_type_mailer']) ? (int) $customInstitution['sender_type_mailer'] : 0;
        $configs = [];

        switch ($senderType) {
            case 0: // SMTP Classico
                $configs['protocol'] = 'smtp';
                $configs['smtp_host'] = !empty($customInstitution['smtp_host']) ? normalizeSmtpHost(checkDecrypt($customInstitution['smtp_host'])) : null;
                $configs['smtp_user'] = !empty($customInstitution['smtp_user']) ? normalizeSmtpValue(checkDecrypt($customInstitution['smtp_user'])) : '';
                $configs['smtp_from'] = !empty($customInstitution['smtp_from']) ? normalizeSmtpValue(checkDecrypt($customInstitution['smtp_from'])) : '';
                $configs['smtp_pass'] = !empty($customInstitution['smtp_pass']) ? normalizeSmtpValue(checkDecrypt($customInstitution['smtp_pass'])) : null;
                $configs['smtp_port'] = !empty($customInstitution['smtp_port']) ? normalizeSmtpValue($customInstitution['smtp_port']) : null;
                $configs['smtp_crypto'] = !empty($customInstitution['smtp_security']) ? strtolower(normalizeSmtpValue($customInstitution['smtp_security'])) : '';
                $configs['smtp_auth'] = !empty(normalizeSmtpValue($customInstitution['smtp_auth'] ?? ''));
                break;

            default:
                break;
        }

        $configs['crlf'] = "\r\n";
        $configs['newline'] = "\r\n";
        $configs['wordwrap'] = true;
        $configs['mailtype'] = "html";
        $configs['priority'] = 3;
        $configs['wrapchars'] = 76;

        if (
            isset($configs['smtp_host']) &&
            in_array($configs['smtp_host'], ['smtp.gmail.com', 'smtp-relay.gmail.com'])
        ) {
            $urlParts = !empty($trasparenzaUrl) ? parse_url($trasparenzaUrl) : [];

            if (!empty($urlParts['host'])) {
                $configs['smtp_helo'] = $urlParts['host'];
            }
        }

        if (isset($configs['smtp_security'])) {
            unset($configs['smtp_security']);
        }

        if (isset($configs['smtp_crypto'])) {
            $configs['smtp_crypto'] = strtolower($configs['smtp_crypto']);
        }

        return $configs;
    }
}


if (!function_exists('isMailConfigValid')) {

    /**
     * Verifica che la configurazione email risolta contenga i parametri minimi richiesti dal
     * protocollo di invio, prima di tentare l'invio vero e proprio.
     *
     * Viene applicata la validazione SMTP classica: host e porta sempre obbligatori,
     * credenziali obbligatorie solo
     * quando l'Ente ha abilitato l'autenticazione.
     *
     * @param array $configs Configurazione email gia' risolta e decifrata (es. da getMailConfig())
     * @return bool
     */
    function isMailConfigValid(array $configs = []): bool
    {
        $protocol = strtolower(trim((string)($configs['protocol'] ?? '')));

        if ($protocol === 'smtp') {
            $smtpPort = filter_var(
                $configs['smtp_port'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 65535]]
            );

            if (empty($configs['smtp_host']) || $smtpPort === false) {
                return false;
            }

            if (
                !empty($configs['smtp_auth']) &&
                (empty($configs['smtp_user']) || empty($configs['smtp_pass']))
            ) {
                return false;
            }

            return true;
        }

        return false;
    }
}

if (!function_exists('arrayMergeRecursiveDistinct')) {

    /**
     * Merge di due array multidimensionali
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    function arrayMergeRecursiveDistinct($array1, $array2)
    {
        static $level = 0;
        $merged = [];
        if (!empty($array2["mergeWithParent"]) || $level == 0) {

            $merged = $array1;
        }

        if (!empty($array2) && is_array($array2)) {
            foreach ($array2 as $key => &$value) {

                if (is_numeric($key)) {

                    $merged[] = $value;
                } else {

                    $merged[$key] = $value;
                }

                if (
                    is_array($value) && isset($array1[$key]) && is_array($array1[$key])
                ) {

                    $level++;
                    $merged[$key] = arrayMergeRecursiveDistinct($array1[$key], $value);
                    $level--;
                }
            }
        }

        unset($merged["mergeWithParent"]);
        return $merged;
    }
}

if (!function_exists('authPatOs')) {

    /**
     * Funzione autenticazione custom Pat OS
     *
     * @return Auth
     */
    function authPatOs()
    {
        // Memoizzata: il costruttore di AuthPatOS ([app/Helpers/AuthPatOS.php:163]) costa ~4.7ms
        // (Config::load, Encryption, new Session, new Database con bootEloquent + setAsGlobal, piu'
        // un register_shutdown_function), e questo helper veniva chiamato 34 volte per richiesta
        // front-office: ~160ms, circa un terzo del tempo totale.
        //
        // Perche' e' sicuro restituire sempre la stessa istanza:
        // - l'unico stato mutabile di AuthPatOS e' $isValid e $error, scritti solo da
        //   authenticate()/setErrorAuth(). Nessun chiamante di questo helper li usa: attraverso
        //   authPatOs() passano solo getIdentity, id, hasIdentity, getStorage, addStorage,
        //   clearIdentity e regenerateSession. I percorsi di login costruiscono la propria istanza
        //   con "new Auth(AuthPatOS::class)" (AuthAdminController,
        //   AuthCinecaController) e restano quindi non memoizzati, con $isValid pulito;
        // - AuthPatOS non mette in cache nulla: $this->session opera su $_SESSION live
        //   (System\Session non tiene snapshot), quindi getIdentity() vede sempre lo stato corrente,
        //   compresa l'impersonificazione scritta da UtilityController::changeInstitution();
        // - $this->encryption usa la chiave da config, deterministica;
        // - SPID, CIE, Cineca e Microsoft/LDAP usano adattatori propri (AuthSPID, AuthCIE, ...),
        //   istanziati direttamente e non da qui.
        //
        // Effetto collaterale voluto: un solo setAsGlobal() sul Capsule di Eloquent per richiesta
        // invece di 34, e un solo shutdown handler invece di 34.
        //
        // La static vale per la durata del processo-richiesta (PHP-FPM / mod_php). Su un runtime
        // persistente (Swoole, RoadRunner) andrebbe rivista, perche' vivrebbe fra piu' richieste.
        static $auth = null;

        if ($auth === null) {
            $auth = new \System\Auth(\Helpers\AuthPatOS::class);
        }

        return $auth;
    }
}

if (!function_exists('isTechnicalUser')) {
    /**
     * Funzione che verifica se l'utente in sessione e' un'utenza tecnica
     *
     * @param $alsoCheckAdmin
     * @return bool
     */
    function isTechnicalUser($alsoCheckAdmin = false)
    {
        if (!is_cli()) {
            $auth = authPatOs();

            if ($auth->hasIdentity()) {

                $userInfo = $auth->getIdentity();

                return ($userInfo['technical_user'] != 0) ? true : false;
            }
        }

        return false;
    }
}

if (!function_exists('isAdmin')) {

    /**
     * Funzione che verifica se l'utente in sessione e' un admin (Amministratore della piattaforma)
     *
     * @return bool
     */
    function isAdmin()
    {
        $auth = authPatOs();

        if ($auth->hasIdentity()) {

            $userInfo = $auth->getIdentity();

            return (int) $userInfo['admin'] !== 0;
        }

        return false;
    }
}

if (!function_exists('br2nl')) {

    /**
     * Trasforma <br/> in new line
     *
     * @param $string
     * @return array|string|string[]|null
     */
    function br2nl($string)
    {
        $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
        return preg_replace("=&lt;br */?&gt;=i", "\n", $string);
    }
}

if (!function_exists('toFloat')) {

    /**
     * Trasforma il valore passato nel tipo float
     * Utilizzata per gli importi
     *
     * @param $num
     * @return float
     */
    function toFloat($num)
    {
        // Verifica se il numero è negativo
        $isNegative = strpos($num, '-') === 0;

        // Trova la posizione dell'ultimo punto (.) o della virgola (,)
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        // Rimuovi tutti i caratteri non numerici tranne il punto o la virgola
        if (!$sep) {
            // Gestione caso senza separatore decimale
            $num = preg_replace("/[^0-9]/", '', $num);
        } else {
            // Gestione caso con separatore decimale
            $num = preg_replace("/[^0-9]/", '', substr((string) $num, 0, $sep)) . '.' .
                preg_replace("/[^0-9]/", '', substr((string) $num, $sep + 1));
        }

        // Ritorna il numero come float, mantenendo il segno negativo se presente
        return $isNegative ? -floatval($num) : floatval($num);
    }
}


if (!function_exists('translateMonth')) {

    /**
     * Funzione che dato un numero restituisce il nome del mese corrispondente.
     *
     * @param $month
     * @return string
     */
    function translateMonth($month = null): string
    {

        $getMonth = '';

        if (!empty($month)) {

            if (is_numeric($month)) {

                if ((int) strlen((string) $month) === 1) {

                    $month = '0' . $month;
                }

                switch ($month) {

                    case '01':
                        $getMonth = 'Gennaio';
                        break;

                    case '02':
                        $getMonth = 'Febbraio';
                        break;

                    case '03':
                        $getMonth = 'Marzo';
                        break;

                    case '04':
                        $getMonth = 'Aprile';
                        break;

                    case '05':
                        $getMonth = 'Maggio';
                        break;

                    case '06':
                        $getMonth = 'Giugno';
                        break;

                    case '07':
                        $getMonth = 'Luglio';
                        break;

                    case '08':
                        $getMonth = 'Agosto';
                        break;

                    case '09':
                        $getMonth = 'Settembre';
                        break;

                    case '10':
                        $getMonth = 'Ottobre';
                        break;

                    case '11':
                        $getMonth = 'Novembre';
                        break;

                    default:
                        $getMonth = 'Dicembre';
                }
            } else {

                $getMonth = $month;
            }
        }

        return $getMonth;
    }
}


if (!function_exists('iconBtn')) {

    /**
     * Funzione per la creazione dell'icona di un pulsante
     *
     * @param string $name Testo da visualizzare nel pulsante
     * @param string $id id dell'icona da mostrare nel bottone
     * @param string $ico icona da mostrare nel bottone
     * @return string
     */
    function iconBtn($name = 'Salva', $id = "icon-save", $ico = 'fa-save')
    {
        return '<i id="' . $id . '" class="far ' . $ico . '"></i> ' . $name;
    }
}

if (!function_exists('endKeyOnlyPagination')) {
    /**
     * Paginazione Bootstrap
     *
     * @param $pages
     */
    function endKeyOnlyPagination($pages)
    {
        end($pages);
        return key($pages);
    }
}

if (!function_exists('boundedQueryInt')) {

    /**
     * Legge un intero dalla query string limitandolo a un massimo.
     *
     * Serve ai parametri che il client puo' gonfiare a piacere - `take` e `skip` degli export
     * OpenData, `p` degli elenchi paginati - dove un singolo valore fuori scala costa quanto
     * migliaia di richieste normali.
     *
     * Retro-compatibilita': il comportamento e' identico a quello precedente per ogni valore
     * che oggi funziona.
     *  - assente, vuoto, non numerico, zero o negativo => $default, cioe' lo stesso valore che
     *    dava il ternario `!empty(...) ? (int)... : $default` che questa funzione sostituisce;
     *  - valore positivo <= $max => restituito invariato;
     *  - valore positivo > $max => limitato a $max. E' l'unico caso che cambia, ed e' quello
     *    dell'abuso: nessuna richiesta legittima supera i massimi impostati ai punti d'uso.
     *
     * Due casi limite, entrambi coperti dal check:
     *  - non numerico (`?take=abc`): `(int)` da' 0 e il valore 0 viene restituito tale e quale,
     *    esattamente come prima. Farlo ricadere nel default significherebbe rispondere a una
     *    stringa spazzatura con l'export completo, cioe' il contrario di quello che serve;
     *  - negativo (`?take=-1`): non veniva ignorato da Eloquent ma toglieva del tutto il LIMIT
     *    (`Builder::limit()` assegna solo se `$value >= 0`), quindi scaricava l'intera tabella,
     *    piu' di quanto facesse `?take=999999`. Ricade nel default - non in zero - perche' chi
     *    lo usasse come "dammi tutto" continua a ricevere l'export completo, ora pero' limitato.
     *    Su `skip` e `p` il default e' 0, che e' gia' l'offset effettivo che Eloquent calcolava
     *    da un negativo (`offset()` fa `max(0, ...)`): li' non cambia nulla.
     *
     * @param string $name Nome del parametro in query string
     * @param int $max Valore massimo accettato
     * @param int $default Valore usato quando il parametro e' assente o non positivo
     * @return int
     */
    function boundedQueryInt(string $name, int $max, int $default = 0): int
    {
        $raw = Input::get($name);

        // Stessa porta d'ingresso del ternario `!empty(...)` che questa funzione sostituisce:
        // assente, stringa vuota e '0' ricadono tutti nel default, come prima.
        if (empty($raw)) {
            return $default;
        }

        $value = (int)$raw;

        return $value < 0 ? $default : min($value, $max);
    }
}

if (!function_exists('paginateBootstrap')) {

    /**
     * Paginazione Bootstrap 4
     *
     * @param $elements
     * @param string $rendering
     * @return string
     * @throws Exception
     */
    function paginateBootstrap($elements, $rendering = 'display')
    {
        $data['paginator'] = $elements;
        return \System\View::create('pagination_bootstrap', $data)->$rendering();
    }
}

if (!function_exists('form_editor')) {
    /**
     * Funzione che creare un campo di input di tipo CKEDITOR
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_editor($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'name' => is_array($data) ? '' : $data,
        );

        if (!is_array($data) or !isset($data['value'])) {

            $val = $value;
        } else {

            $val = $data['value'];
            unset($data['value']);
        }

        return '<textarea ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " >" . htmlEscape($val) . "</textarea>\n";
    }
}

if (!function_exists('sessionSetNotify')) {

    /**
     * Funzione che permette di settare una notifica con il messaggio
     *
     * @param string|null $message
     * @param string $type success | warning | info | danger
     */
    function sessionSetNotify(string $message = null, string $type = 'success'): void
    {
        $acceptType = ['success', 'warning', 'info', 'danger'];

        $type = in_array($type, $acceptType) ? $type : 'info';

        $session = new \System\Session();

        $session->setFlash('___has_notify___', true);
        $session->setFlash('___msg_notify___', $message);
        $session->setFlash('___type_notify___', $type);
    }
}

if (!function_exists('sessionHasNotify')) {

    /**
     * Funzione che ritorna true se c'è una notifica in sessione, false altrimenti
     *
     * @return bool
     */
    function sessionHasNotify()
    {
        $session = new \System\Session();

        return $session->getFlash('___has_notify___') ? true : false;
    }
}

if (!function_exists('sessionGetNotify')) {

    /**
     * Funzione che ritorna il messaggio della notifica
     *
     * @return string|null
     */
    function sessionGetNotify()
    {
        $session = new \System\Session();

        return $session->getFlash('___msg_notify___');
    }
}

if (!function_exists('sessionTypeNotify')) {

    /**
     * Funzione che ritorna il tipo della notifica
     *
     * @return string|null
     */
    function sessionTypeNotify()
    {
        $session = new \System\Session();

        return $session->getFlash('___type_notify___');
    }
}


if (!function_exists('filesUploaded')) {

    /**
     * Funzione per l'upload dei files
     *
     * @return bool
     */
    function filesUploaded($field = 'userfile'): bool
    {

        if (empty($_FILES)) {
            return false;
        }

        $files = @$_FILES[$field]['tmp_name'];

        if (empty($files)) {
            return false;
        }

        if (is_array($files) == false) {
            if (is_uploaded_file($files) == true || file_exists($files) == true) {
                return true;
            }
        } else {
            foreach ($files as $field_title => $temp_name) {
                if (!empty($temp_name) && (is_uploaded_file($temp_name) == true || file_exists($temp_name) == true)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('avatar')) {

    /**
     * Funzione che restituisce l'avatar con l'immagine del profilo dell'utente
     *
     * @return string
     * @throws Exception
     */
    function avatar()
    {
        $identity = authPatOs()->getIdentity(['profile_image']);

        $avatar = (!empty($identity['profile_image']) && $identity !== null && $identity['profile_image'] !== '')
            ? 'media/' . instituteDir() . '/assets/images/' . $identity['profile_image']
            : 'assets/admin/img/avatar.png';

        return baseUrl($avatar);
    }
}

if (!function_exists('isIe11')) {

    /**
     * Funzione che controlla se il browser in uso è Internet Explorer 11
     *
     * @return bool
     */
    function isIe11()
    {
        $agent = new Jenssegers\Agent\Agent();
        $browser = $agent->browser();
        $version = $agent->version($browser);

        if ($version == '11.0') {

            return true;
        }

        return false;
    }
}

if (!function_exists('guard')) {

    /**
     * Funzione che verifica se un determinato utente ha il privilegio di accedere a una determinata
     * pagina.
     *
     * @return bool
     */
    function guard(): bool
    {
        $guard = false;

        if (!Registry::exist('___patos___profiles___not_run__')) {

            if (Registry::exist('___patos___profiles___') && Registry::exist('___patos___profiles___method__')) {

                $profiles = Registry::get('___patos___profiles___');
                $methodInRoute = Registry::get('___patos___profiles___method__');

                foreach ($methodInRoute as $method) {
                    if (!empty($profiles[$method])) {

                        $guard = true;
                        break;
                    }
                }
            }
        } else {

            $guard = true;
        }


        return $guard;
    }
}

if (!function_exists('removeDotHtml')) {

    /**
     * Funzione che rimuove ".html" o ".htm" da una stringa
     *
     * @param null $str
     * @return string
     */
    function removeDotHtml($str = null)
    {
        return str_replace(['.html', '.htm'], ['', ''], $str);
    }
}

if (!function_exists('setUpperCaseRowTable')) {

    /**
     * Funzione che converte tutti i caratteri di una riga di una tabella in caratteri maiuscoli
     *
     * @param bool $mode
     * @param $string
     * @param bool $strong
     * @return string
     */
    function setUpperCaseRowTable($string, $mode = false, $strong = false)
    {
        $strongOpen = $strong === true ? '<strong>' : '';
        $strongClose = $strong === true ? '</strong>' : '';
        return ($mode === true)
            ? $strongOpen . mb_strtoupper($string, CHARSET) . $strongClose
            : $strongOpen . $string . $strongClose;
    }
}

if (!function_exists('btnSave')) {

    /**
     * Funzione che crea un pulsante per il salvataggio dei dati
     *
     * @param string $id
     * @param bool $title
     * @return string
     * @throws Exception
     */
    function btnSave($id = "btn_save", $title = false)
    {
        $title = ($title == false) ? __('brn_save', null, 'patos') : $title;
        $html = '<button name="send" type="submit" id="' . $id . '" class="btn btn-outline-primary">';
        $html .= $title;
        $html .= '</button>';
        $html .= nbs(2);
        $html .= '<span></span>';

        return $html;
    }
}

if (!function_exists('searchArrayByField')) {

    /**
     * Funzione che cerca un riga all'interno di un array multidimensionale
     *
     * @param null $value
     * @param array $data
     * @param string $field
     * @return array|null
     */
    function searchArrayByField($value = null, $data = [], $field = '')
    {
        return array_search($value, array_column($data, $field));
    }
}

if (!function_exists('multiSearch')) {

    /**
     * Funzione che cerca un riga all'interno di un array multidimensionale
     *
     * @param array $array
     * @param array $pairs
     * @return array|null
     */
    function multiSearch(array $array, array $pairs)
    {
        $found = array();
        foreach ($array as $aKey => $aVal) {

            $coincidences = 0;
            foreach ($pairs as $pKey => $pVal) {

                if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {

                    $coincidences++;
                }
            }

            if ($coincidences == count($pairs)) {

                $found[$aKey] = $aVal;
            }
        }

        return $found;
    }
}

if (!function_exists('convertDateToDatabase')) {

    /**
     * Funzione che converte una data nel formato datetime per essere salvata sul database
     *
     * @param $dateTime
     * @return false|string
     */
    function convertDateToDatabase($dateTime)
    {
        $dateTime = explode('-', $dateTime);

        return date($dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2]);
    }
}

if (!function_exists('convertDateToImport')) {

    /**
     * Funzione che converte una data nel formato d-m-Y
     *
     * @param $dateTime
     * @return false|string
     */
    function convertDateToImport($dateTime)
    {
        $dateTime = explode('/', $dateTime);

        return date($dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2]);
    }
}

if (!function_exists('convertDateToForm')) {

    /**
     * Funzione che converte una data nel formato timestamp accettato dal form
     *
     * @param $timeStamp
     * @return array
     */
    function convertDateToForm($timeStamp)
    {

        $data = [
            'date' => null,
            'hours' => null
        ];

        if (!empty($timeStamp)) {

            $temp = explode(' ', $timeStamp);

            $dateTime = explode('-', $temp[0]);
            $hours = explode(':', $temp[1]);

            if (!empty($dateTime) && is_array($dateTime)) {

                $data['date'] = $dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2];
            }

            if (!empty($hours) && is_array($hours)) {

                $data['hours'] = $hours[0] . ':' . $hours[1];
            }
        }

        return $data;
    }
}

if (!function_exists('setDefaultData')) {

    /**
     * Funzione che ritorna un parametro predefinito in base al valore passato
     *
     * @param null $data
     * @param null $default
     * @param string $expected
     * @return mixed|null
     */
    function setDefaultData($data = null, $default = null, $expected = [null, 0, false])
    {

        if (in_array($data, $expected, true)) {

            return $default;
        }

        return $data;
    }
}

if (!function_exists('setOrderDatatable')) {

    /**
     * Funzione che imposta la colonna su cui effettuare l'ordinamento nel datatable
     *
     * @param null $columnName
     * @param array $orderable
     * @param string $default
     * @return mixed|null
     */
    function setOrderDatatable($columnName = null, array $orderable = [], string $default = '')
    {
        return !empty($columnName) && is_int($columnName) && array_key_exists($columnName, $orderable)
            ? $orderable[$columnName]
            : $default;
    }
}

if (!function_exists('getAclVersioning')) {

    /**
     * Funzione che restituisce il permesso di versioning che ha l'utente
     *
     * @return bool
     */
    function getAclVersioning()
    {
        return Acl::getVersioning();
    }
}

if (!function_exists('getAclArchiving')) {

    /**
     * Funzione che restituisce il permesso di versioning che ha l'utente
     *
     * @return bool
     */
    function getAclArchiving()
    {
        return Acl::getArchiving();
    }
}

if (!function_exists('getAclLockUser')) {

    /**
     * Funzione che restituisce il permesso di blocco/sblocco degli utenti che ha l'utente
     *
     * @return bool
     */
    function getAclLockUser()
    {
        return Acl::getLockUser();
    }
}

if (!function_exists('getAclModifyProfile')) {

    /**
     * Funzione che restituisce il permesso di modifica avanzata del profilo che ha l'utente
     *
     * @return bool
     */
    function getAclModifyProfile()
    {
        return Acl::getModifyProfile();
    }
}

if (!function_exists('getAclExportCsv')) {

    /**
     * Funzione che restituisce i permessi di export dei dati in CSV dell'utente
     *
     * @return bool
     */
    function getAclExportCsv()
    {
        return Acl::getExportCsv();
    }
}

if (!function_exists('getAclDelete')) {

    /**
     * Funzione che restituisce i permessi di eliminazione dell'utente su una sezione
     *
     * @return bool
     */
    function getAclDelete()
    {
        if (Registry::exist('___patos___profiles___')) {

            $keys = Registry::get('___patos___profiles___');
            return (bool) $keys['delete'];
        }

        return false;
    }
}

if (!function_exists('getAclAdd')) {

    /**
     * Funzione che restituisce i permessi di inserimento dell'utente su una sezione
     *
     * @return bool
     */
    function getAclAdd()
    {
        if (Registry::exist('___patos___profiles___')) {

            $keys = Registry::get('___patos___profiles___');
            return (bool) $keys['create'];
        }

        return false;
    }
}

if (!function_exists('instituteNameSelected')) {
    /**
     * Funzione che ritorna il nome dell'ente selezionato
     *
     * @return string
     */
    function instituteNameSelected()
    {
        $fullName = patOsInstituteInfo(['full_name_institution']);
        $patSsFullName = session()->has('alternative_pat_os_full_name') ? session()->get('alternative_pat_os_full_name') : $fullName['full_name_institution'];
        return 'data-placeholder="' . $patSsFullName . '" placeholder="' . $patSsFullName . '" class="form-control form-control-sm select2-_cinst _cinst" id="_cinst" style="width:15rem !important; font-size:90%"';
    }
}

if (!function_exists('wordLimiter')) {
    /**
     * Limits a string to X number of words.
     *
     * @param string $str
     * @param int $limit
     * @param string $end_char the end character
     * @return string
     */
    function wordLimiter($str, $limit = 100, $end_char = '&#8230;')
    {
        if (trim($str) === '') {

            return $str;
        }

        preg_match('/^\s*+(?:\S++\s*+){1,' . (int) $limit . '}/', $str, $matches);

        if (strlen((string) $str) === strlen((string) $matches[0])) {

            $end_char = '';
        }

        return rtrim($matches[0]) . $end_char;
    }
}


if (!function_exists('characterLimiter')) {
    /**
     * Limita le parole data una stringa
     * @param $str
     * @param $n
     * @param $endChar
     * @return array|mixed|string|string[]|void|null
     */
    function characterLimiter($str, $n = 500, $endChar = '&#8230;')
    {
        if (mb_strlen((string) $str) < $n) {
            return $str;
        }

        $str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $str));

        if (mb_strlen((string) $str) <= $n) {
            return $str;
        }

        $out = '';
        foreach (explode(' ', trim($str)) as $val) {
            $out .= $val . ' ';

            if (mb_strlen($out) >= $n) {
                $out = trim($out);
                return (mb_strlen($out) === mb_strlen((string) $str)) ? $out : $out . $endChar;
            }
        }
    }
}

if (!function_exists('shortInstitutionName')) {

    /**
     * Funzione che restituisce il nome breve dell'ente
     *
     * @param string $str Nome completo dell'Ente
     * @return string
     */
    function shortInstitutionName($str = null)
    {
        helper('url');

        return urlTitle(wordLimiter($str, 3, ''), '_', true);
    }
}

if (!function_exists('moveFileInDirMedia')) {

    /**
     * Funzione che sposta il file nella cartella media dell'Ente quando viene creato
     *
     * @param string $fileName Nome del file da spostare
     * @param string $instituteDir Nome della cartella media dell'ente
     * @return void
     */
    function moveFileInDirMedia($fileName = null, $instituteDir = null)
    {
        $from = MEDIA_PATH . '/' . $fileName;
        $to = MEDIA_PATH . '/' . $instituteDir . '/' . $fileName;
        File::copy($from, $to);
        File::delete($from);
    }
}

if (!function_exists('write_file')) {
    /**
     *
     * Funzione che permette di scrivere i dati nel file specificato nel percorso.
     * Se il file non esiste lo crea.
     *
     * @param string $path File path
     * @param string $data Data to write
     * @param string $mode fopen() mode (default: 'wb' write in binary mode)
     * @return    bool
     */
    function write_file($path, $data, $mode = 'wb')
    {
        if (!$fp = @fopen($path, $mode)) {
            return false;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen((string) $data); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr((string) $data, $written))) === false) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }
}

if (!function_exists('loadElfinderJs')) {

    /**
     * Paginazione del file-manager elFinder
     *
     * @param $elements
     * @throws Exception
     */
    function loadElfinderJs($elements = null)
    {

        // header("X-Frame-Options: sameorigin");
        $data[] = $elements;
        $data['isCkEditor'] = (bool) empty($elements['isCkEditor']) ? false : true;

        $data['base_url'] = baseUrl(verifyFirstOrLastSlash('admin/sys/filemanager'));
        $data['my_folder'] = false
            ? MEDIA_PATH . instituteDir() . '/' . authPatOs()->id()
            : MEDIA_PATH;

        return \System\View::create('elfinder_javascript', $data)->display();
    }
}


if (!function_exists('treeSelectOptionValue')) {
    /**
     * Creazione del tag option per la select
     *
     * @param $data
     */
    function treeSelectOptionValue($tree, $parentId = null, $institutionId = null)
    {
        $eId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $html = '';
        foreach ($tree as $item) {

            if ((int) $item['is_system'] === 1 || (int) $eId == (int) $item['institution_id']) {

                $customPage = ($item['is_system'] === 0 && $eId === $item['institution_id'])
                    ? '[p] '
                    : '';

                $selected = (int) $item['id'] === (int) $parentId ? ' selected ' : '';

                $html .= '<option value="' . $item['id'] . '" ' . $selected . '>';

                $html .= '|' . str_repeat("_", $item['deep']) . '/ ' . $customPage . $item['name'];

                $html .= "</option>";

                if ($item['children']) {

                    $html .= treeSelectOptionValue(
                        $item['children'],
                        $parentId,
                        $eId
                    );
                }
            }
        }

        return $html;
    }
}

/**
 * Funzione che restituisce un array di ID
 *
 * @param object $obj
 * @return array|null
 */
if (!function_exists('extractIds')) {
    function extractIds($obj)
    {
        $result = [];

        if (isset($obj['id'])) {
            $result[] = $obj['id'];
        }

        if (isset($obj['children'])) {
            return array_merge($result, ...array_map('extractIds', $obj['children']));
        }

        return $result;
    }
}

if (!function_exists('getLasHistoryUrl')) {

    /**
     * Funzione che cerca un riga all'interno di un array multidimensionale
     *
     * @param null $value
     * @param array $data
     * @param string $field
     * @return array|null
     */
    function getLasHistoryUrl()
    {
        // $session = new System\Session();
        // return $session->getFlash('last_history_url');
        return Registry::get('last_history_url');
    }
}

if (!function_exists('fileToBase64')) {

    /**
     * Funzione che converte un file in Base 64.
     * @param $file
     * @return string
     */
    function fileToBase64($file)
    {
        $fInfo = new finfo(FILEINFO_MIME_TYPE);
        $type = $fInfo->file($file);
        return 'data:' . $type . ';base64,' . base64_encode(File::getContent($file));
    }
}

if (!function_exists('floatvalue')) {

    /**
     * Funzione per la formattazione degli importi nel formato italiano
     * @param $val
     * @return float
     */
    function floatvalue($val)
    {
        $val = str_replace(",", ".", $val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }
}

if (!function_exists('forceDownloadDynamic')) {

    function forceDownloadDynamic($templateString = '', $filename = '', $contentType = 'text/html', $printDoc = false)
    {

        $now = gmdate("D, d M Y H:i:s");

        header('Pragma: private');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: " . $now . " GMT");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen((string) $templateString));
        header('Connection: close');

        echo $templateString;
    }
}
if (!function_exists('downloader')) {

    function downloader($filename = '', $data = '', $setMime = false, $alias = null, $robotsIndex = false, $printDoc = false)
    {

        if ($filename === '' or $data === '') {
            return;
        } elseif ($data === null) {

            if (is_array($filename)) {
                if (count($filename) !== 1) {
                    return;
                }

                reset($filename);
                $filepath = key($filename);
                $filename = current($filename);

                if (is_int($filepath)) {
                    return;
                }
            } else {

                $filepath = $filename;
                $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
                $filename = end($filename);
            }

            if (!@is_file($filepath) or ($filesize = @filesize($filepath)) === false) {

                return;
            }
        } else {

            $filesize = strlen((string) $data);
        }

        if ($filesize == 0) {
            echo show404('ATTENZIONE', 'File non presente');
            die();
        }

        $mime = 'application/octet-stream';

        $x = explode('.', $filename);
        $extension = end($x);

        if ($setMime === true) {
            if (count($x) === 1 or $extension === '') {
                return;
            }

            $mimes = &getMimes();

            if (isset($mimes[$extension])) {
                $mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
            }
        }

        if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {

            $x[count($x) - 1] = strtoupper($extension);
            $filename = implode('.', $x);
        }

        if (ob_get_level() !== 0 && @ob_end_clean() === false) {

            @ob_clean();
        }

        $utf8 = new Utf8(null);
        $charset = strtoupper(CHARSET);

        if ($alias != null) {

            $utf8Filename = ($charset !== 'UTF-8')
                ? $utf8->convertToUtf8($alias, $charset)
                : $alias;
        } else {

            $utf8Filename = ($charset !== 'UTF-8')
                ? $utf8->convertToUtf8($filename, $charset)
                : $filename;
        }

        isset($utf8Filename[0]) && $utf8Filename = " filename*=UTF-8''" . rawurlencode($utf8Filename);

        $contentDisposition = 'attachment';

        if ($printDoc === true) {

            $contentTypeForPrintDoc = [
                'application/pdf',
                'application/x-pdf',
                'application/acrobat',
                'applications/vnd.pdf',
                'text/pdf',
                'text/x-pdf',
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/bmp',
                'image/tiff',
                'image/x-tiff',
                'image/svg+xml',
            ];

            $contentDisposition = in_array($mime, $contentTypeForPrintDoc)
                ? 'inline'
                : 'attachment';
        }

        header('Content-Type: ' . $mime);
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');
        header('Content-Disposition: ' . $contentDisposition . '; filename="' . $filename . '";' . $utf8Filename);

        if ($robotsIndex) {
            header("X-Robots-Tag: noindex, nofollow", true);
        }

        if ($data !== null) {
            exit($data);
        }

        if (@readfile($filepath) === false) {
            return;
        }
    }
}


if (!function_exists('checkGet')) {

    function checkGet($data = [])
    {

        foreach (\System\Input::get($data) as $key => $value) {

            if ($value != null) {

                return true;

                continue;
            }
        }
        return false;
    }
}
if (!function_exists('isArrayMultidimensional')) {
    function isArrayMultidimensional($array)
    {
        return is_array($array[array_key_first($array)]);
    }
}

if (!function_exists('checkRecordOwner')) {
    /**
     * Metodo che controlla se l'utente loggato è il creatore del record con l'id passato nel parametro
     * @param int|null $recordOwnerId Id utente
     * @param int|null $userId identificativo dell'utente da prendere come riferimento
     * @return bool
     */
    function checkRecordOwner(int $recordOwnerId = null, int $userId = null): bool
    {
        if ($userId === null) {
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);
            if (!empty($recordOwnerId)) {
                return $getIdentity['id'] == $recordOwnerId;
            }
        } else {
            return $userId === $recordOwnerId;
        }

        return false;
    }
}

if (!function_exists('sanitizeArray')) {
    /**
     * Funzione di sanificazione per la concat nelle query
     * @param $array dei campi da sanificare
     * @return string
     */
    function sanitizeArray($array)
    {
        $validFields = ['object', 'name', 'code', 'denomination', 'title', 'object_structures.structure_name'];
        $data = null;
        if (!empty($array) && is_array($array)) {
            foreach ($array as $arr) {
                $item = !empty($arr)
                    ? preg_replace("/[^A-Za-z_.]/", '', removeInvisibleCharacters($arr))
                    : '';

                if (in_array($item, $validFields)) {
                    $data[] = $item;
                }
            }
        }

        return $data;
    }
}

if (!function_exists('checkEncrypt')) {

    /**
     * Funzione controlla ed avvia eventualmente la crittografica statica della stringa passata
     * @param $data campo avviare il controlloo della crittografia
     * @return string
     */
    function checkEncrypt($data = '')
    {

        if (strlen((string) $data) >= 1) {

            return (!empty(_env('ENCRYPTION_DB_DATA')) ? \Helpers\Security\Crypto::encrypt((string) $data) : (string) $data);
        }

        return null;
    }
}

if (!function_exists('checkDecrypt')) {

    /**
     * Funzione controlla e avvia eventualmente la decrittografica statica della stringa passata
     * @param string $data campo avviare il controllo della decrittografia
     * @return string|null
     */
    function checkDecrypt(string|null $data = ''): ?string
    {

        static $encryptionDbData = null;

        if ($encryptionDbData === null) {
            $encryptionDbData = !empty(_env('ENCRYPTION_DB_DATA'));
        }

        if ($data != null && strlen($data) >= 1) {

            return ($encryptionDbData ? \Helpers\Security\Crypto::verify($data) : $data);
        }

        return null;
    }
}

if (!function_exists('insertMeta')) {
    /**
     * Funzione per l'inserimento dei dati nelle tabelle META
     * @param $insertId
     * @param $institutionId
     * @param $model_name
     * @param $value
     * @param $newColumnName
     * @param $MetaGroup
     * @return void
     */
    function insertMeta($insertId, $institutionId, $model_name, $value, $newColumnName, $metaGroup = null)
    {
        $model = '\\Model\\' . $model_name;

        if (!empty($value) or (empty($value) and is_bool($value))) {

            $insertMetaID = $model::create([
                'institution_id' => $institutionId,
                'reference_id' => $insertId,
                //                        'meta_label' => serialize([
                //                            'key' => $agent->platform()
                //                        ]),
                'meta_key' => $newColumnName,
                'meta_value' => $value,
                'meta_group' => $metaGroup,
            ]);
        }
    }
}

if (!function_exists('referenceOriginForRegenerateToken')) {
    /**
     * Funzione che verifica e
     * @param int $num Numero di segmento
     * @param string $segmentName Segment name
     * @return bool
     * @throws Exception
     */
    function referenceOriginForRegenerateToken(int $num = 3, string $segmentName = 'create-box.html'): bool
    {
        $ref = session()->get('__frm_history_t');

        if (!empty($ref)) {

            $segments = explode('/', parse_url($ref['url'], PHP_URL_PATH));

            if (!empty($segments[$num]) && rtrim($segments[$num], '.html') === rtrim($segmentName, '.html')) {
                session()->kill('__frm_history_t');
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('getFullName')) {
    /**
     * Funzione che ritorna il nome completo unendo nome e cognome
     * @param $str1
     * @param $str2
     * @return string
     */
    function getFullName($str1, $str2): string
    {
        return $str1 . ' ' . $str2;
    }
}

if (!function_exists('setDataTableData')) {
    /**
     * @description Funzione che setta i dati per il funzionamento dei datatable nel back-office
     * @param $data       array
     * @param $columnName string Nome della colonna su cui effettuare l'ordinamento
     * @return void
     */
    function setDataTableData(array &$data, string $columnName): void
    {
        $data['draw'] = !empty(Input::get('draw')) ? Input::get('draw', true) : 1;
        $data['start'] = !empty(Input::get("start")) ? (int) Input::get("start", true) : 0;
        $data['rowPerPage'] = !empty(Input::get("length")) ? Input::get("length", true) : 25;

        $security = new Security();

        $columnIndexArr = Input::get('order', true);
        $columnNameArr = Input::get('columns', true);
        $orderArr = Input::get('order', true);
        $searchArr = !empty($_GET['search']) ? $security->xssClean(removeInvisibleCharacters($_GET['search'])) : null;

        $columnIndex = !empty($columnIndexArr[0]['column']) ? $columnIndexArr[0]['column'] : null;
        $data['columnName'] = !empty($columnNameArr[$columnIndex]['data']) ? (int) $columnNameArr[$columnIndex]['data'] : $columnName;
        $data['columnSortOrder'] = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'ASC';
        $data['searchValue'] = !empty($searchArr['value']) ? trim($searchArr['value']) : null;
    }
}

if (!function_exists('urlWithoutWww')) {
    /**
     * @description Funzione che analizza una stringa e ritorna un array con e senza il www
     * @param $url string la stringa das analizzare
     * @return void
     */
    function urlWithoutWww(string $url): array
    {
        $urlWithoutWww = preg_replace('/www\./', '', $url);

        if (!preg_match("/^https?:\/\/www\./", $url)) {
            $url = preg_replace('/https?:\/\//', 'http://www.', $url);
        }

        return [
            $urlWithoutWww,
            $url
        ];
    }
}

if (!function_exists('addWwwToUrl')) {
    /**
     * @description Funzione che analizza una stringa e verifica se esiste il www. Se non esiste glielo inserisce
     * @param $url
     * @return mixed|string
     */
    function addWwwToUrl($url)
    {
        if (strpos($url, 'http://') === 0) {
            $prefix = 'http://';
            $url = substr($url, 7);
        } elseif (strpos($url, 'https://') === 0) {
            $prefix = 'https://';
            $url = substr($url, 8);
        } else {
            return $url;
        }

        if (strpos($url, 'www.') !== 0) {
            $url = 'www.' . $url;
        }

        return $prefix . $url;
    }
}


if (!function_exists('duplicateAttach')) {
    /**
     * @description Funzione che duplica gli allegati dopo la duplicazione di un oggetto
     * @param int|null $id Id dell'elemento appena inserito di cui duplicare gli allegati
     * @param string $archiveName Nome dell'archivio dell'elemento duplicato
     * @param int|null $originId Id dell'allegato che si deve duplicare
     * @param int $sort Ordinamento dell'allegato
     * @param Utf8|null $utf8 Classe per la pulizia del nome dell'allegato
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return void
     */
    function duplicateAttach(int $id = null, string $archiveName = '', int $originId = null, int $sort = 1, Utf8 $utf8 = null, null|string $bdncpCat = ''): void
    {

        if (!empty($originId)) {

            //Id degli allegati da duplicare
            $attachIds = Input::post('attach_id' . $bdncpCat) ?? [];

            $attachIds = array_diff($attachIds, ['null']);

            //Recupero gli allegati originali che devo duplicare
            $originAttach = optional(\Model\AttachmentsModel::where('id', $originId)
                ->first())
                ->toArray();

            if (!empty($originAttach)) {

                //Path in cui duplicare gli allegati
                $uploadPath = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $archiveName . DIRECTORY_SEPARATOR;

                $ext = $originAttach['file_ext'];
                $tmpName = md5(uniqid(mt_rand()));

                //Duplico il file fisico
                if (File::copy($originAttach['full_path'], $uploadPath . $tmpName . $ext)) {

                    $_POST['temp_label_attach'] = !empty($_POST['label_attach' . $bdncpCat][$sort])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$sort]), true, false))
                        : null;
                    $_POST['temp_publish'] = !empty($_POST['publish']) && (!empty($_POST['publish' . $bdncpCat][$sort]) || $_POST['publish' . $bdncpCat][$sort] == 0)
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$sort]), true, false))
                        : 1;
                    $_POST['temp_omissis'] = !empty($_POST['omissis']) && !empty($_POST['omissis' . $bdncpCat][$sort])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$sort]), true, false))
                        : 0;
                    $_POST['temp_bdncp_cat'] = !empty($_POST['bdncp_cat' . $bdncpCat]) && !empty($_POST['bdncp_cat' . $bdncpCat][$sort])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['bdncp_cat' . $bdncpCat][$sort]), true, false))
                        : 0;
                    $_POST['temp_category'] = !empty($_POST['category'][$sort])
                        ? $_POST['category'][$sort]
                        : 1;


                    //Aggiorno il record del file nel db con il nuovo nome utilizzato per salvarlo sul file system
                    // e le altre relative informazioni
                    \Model\AttachmentsModel::create([
                        'file_name' => $tmpName . $ext,
                        'raw_name' => $tmpName,
                        'file_path' => $uploadPath,
                        'full_path' => $uploadPath . $tmpName . $ext,
                        'fingerprint' => hash_file('sha256', $uploadPath . $tmpName . $ext),
                        'file_size' => $originAttach['file_size'],
                        'archive_id' => $id,
                        // Insert ID
                        'archive_name' => $archiveName,
                        'sort' => $sort + 1,
                        'institution_id' => checkAlternativeInstitutionId(),
                        'cat_id' => (int) Input::post('temp_category'),
                        'file_type' => $originAttach['file_type'],
                        'orig_name' => $utf8->cleanString(urlTitle($originAttach['orig_name'])),
                        'client_name' => $originAttach['client_name'],
                        'file_ext' => $originAttach['file_ext'],
                        'is_image' => $originAttach['is_image'],
                        'image_width' => $originAttach['image_width'],
                        'image_height' => $originAttach['image_height'],
                        'image_type' => $originAttach['image_type'],
                        'image_size_str' => $originAttach['image_size_str'],
                        'label' => strip_tags(Input::post('temp_label_attach', true)),
                        'bdncp_cat' => strip_tags(Input::post('temp_bdncp_cat', true)),
                        'active' => (int) strip_tags((string) Input::post('temp_publish', true)),
                        'indexable' => (int) strip_tags((string) Input::post('temp_omissis', true)),
                    ]);
                }
            }
        }
    }
}

if (!function_exists('toSql')) {
    /**
     * Ritorna la query SQL costruita (utile solo per scopi di debug)
     * @param string $sql
     * @param array $bindings
     * @return string
     * @description Valori da passate sono i seguenti: primo valore $query->toSql() - Secondo valore $bindings = $query->getBindings();
     */
    function toSql(string $sql = '', array $bindings = []): string
    {
        $data = '';
        if (strlen($sql) >= 1 && count($bindings) >= 1) {
            // Itera sugli elementi dell'array $bindings
            foreach ($bindings as $key => $value) {
                // Verifica se l'elemento è una stringa e aggiungi gli apici
                if (is_string($value)) {
                    $bindings[$key] = "'" . $value . "'";
                }
            }

            // Costruisco la query SQL utilizzando gli elementi modificati dell'array $bindings
            $data = vsprintf(str_replace('?', '%s', $sql), $bindings);
        }

        return $data;
    }
}

if (!function_exists('toSqlEloquent')) {
    /**
     * Ritorna la query SQL costruita (utile solo per scopi di debug)
     * @param object $eloquent
     * @return string
     */
    function toSqlEloquent($eloquent): string
    {
        $sql = $eloquent->toSql();
        $bindings = $eloquent->getBindings();

        $data = '';
        if (strlen($sql) >= 1 && count($bindings) >= 1) {
            // Itera sugli elementi dell'array $bindings
            foreach ($bindings as $key => $value) {
                // Verifica se l'elemento è una stringa e aggiungi gli apici
                if (is_string($value)) {
                    $bindings[$key] = "'" . $value . "'";
                }
            }

            // Costruisco la query SQL utilizzando gli elementi modificati dell'array $bindings
            $data = vsprintf(str_replace('?', '%s', $sql), $bindings);
        }

        return $data;
    }
}

if (!function_exists('toSqlEloquent')) {
    /**
     * Ritorna la query SQL costruita (utile solo per scopi di debug)
     * @param mixed $query
     * @return string
     */
    function toSqlEloquent(mixed $query): string
    {
        return toSql($query->toSql(), $query->getBindings());
    }
}


if (!function_exists('getAclProfileInfo')) {

    /**
     * Funzione che restituisce il permesso di che ha l'utente riguardo name
     *
     * @param string $name Nome del permesso generale che si vuole controllare
     * @return bool
     */
    function getAclProfileInfo(string $name = ''): bool
    {
        return Acl::getAclProfileInfo($name);
    }
}

if (!function_exists('removePhoto')) {
    /**
     * @description Funzione per la cancellazione della foto
     * @param string|null $fileName Nome del file da cancellare
     * @return void
     */
    function removePhoto(?string $fileName = ''): void
    {
        $path = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

        // Controllo se il file allegato esiste prima di eliminarlo
        if (!empty($fileName) && File::exists($path . $fileName)) {
            File::delete($path . $fileName);
        }
    }
}
if (!function_exists('isSerialized')) {
    function isSerialized($value, &$result = null)
    {
        if (!is_string($value)) {
            return false;
        }

        set_error_handler(function () {}, E_NOTICE);

        $result = unserialize($value);
        restore_error_handler();

        return $result !== false || $value === 'b:0;';
    }
}

if (!function_exists('convertDateForCsv')) {

    /**
     * Funzione che converte una data nel formato datetime per essere salvata sul database
     *
     * @param $date {data da convertire}
     * @param bool $hours {true se si vuole restituire anche ora:minuti:secondi}
     * @return null|string
     */
    function convertDateForCsv($date = null, bool $hours = false): null|string
    {
        if (!empty($date)) {
            $explodeDate = explode(' ', $date);
            $data = explode('-', $explodeDate[0]);

            if ($hours) {
                return $data[2] . '/' . $data[1] . '/' . $data[0] . ' ' . $explodeDate[1];
            } else {
                return $data[2] . '/' . $data[1] . '/' . $data[0];
            }
        }
        return null;
    }
}


if (!function_exists('generateSeparator')) {

    /**
     * Funzione che genera il separatore presente nei vari form
     *
     * @param $name {titolo del separatore}
     * @param $icon {icona}
     * @return string
     */
    function generateSeparator(string $name = '', string $icon = ''): string
    {
        if ($icon != '') {
            return '<div class="custom-separator"><h5><i class="' . $icon . '"></i> ' . $name . '</h5></div>';
        } else {
            return '<div class="custom-separator"><h5>' . $name . '</h5></div>';
        }
    }
}

if (!function_exists('extractIntegersFromString')) {

    /**
     * @param string $input
     * @return array
     */
    function extractIntegersFromString(string $input): array
    {
        $result = [];

        if (is_numeric($input)) {
            $result[] = intval($input);
            return $result;
        }

        $split = explode(',', $input);

        foreach ($split as $item) {
            if (is_numeric($item)) {
                $result[] = intval($item);
            }
        }

        return $result;
    }
}

if (!function_exists('mapMetaKeyToValue')) {
    function mapMetaKeyToValue(array|null $data): array
    {
        $result = [];

        if (!empty($data) && is_array($data)) {
            foreach ($data as $item) {
                if (isset($item['meta_key']) && isset($item['meta_value'])) {
                    if ($item['meta_key'] == 'avcp_error') {

                        preg_match('/data-original-title="(.*?)"/', $item['meta_label'], $matches);
                        $originalTitle = $matches[1] ?? '';

                        $dom = new \DOMDocument;
                        libxml_use_internal_errors(true);  // Ignora gli errori di HTML malformato
                        $dom->loadHTML($originalTitle);
                        libxml_clear_errors();  // Pulisci gli errori dopo il caricamento
                        $xpath = new \DOMXPath($dom);

                        // Trova tutti i tag <li>
                        $liNodes = $xpath->query('//li');

                        // Trasforma i nodi in un array
                        $liArray = [];
                        foreach ($liNodes as $li) {

                            // elimina il tag li e lo appende in un array
                            $liArray[] = strip_tags($dom->saveHTML($li));
                        }

                        $result[$item['meta_key']] = $liArray;
                    } else {

                        $result[$item['meta_key']] = $item['meta_value'];
                    }
                }
            }
        }

        return $result;
    }
}


if (!function_exists('convertAccentedCharacters')) {
    function convertAccentedCharacters($str)
    {
        static $arrayFrom, $arrayTo;

        if (!is_array($arrayFrom)) {
            $foreignCharacters = array(
                '/ä|æ|ǽ/' => 'ae',
                '/ö|œ/' => 'oe',
                '/ü/' => 'ue',
                '/Ä/' => 'Ae',
                '/Ü/' => 'Ue',
                '/Ö/' => 'Oe',
                '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
                '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
                '/Б/' => 'B',
                '/б/' => 'b',
                '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
                '/ç|ć|ĉ|ċ|č/' => 'c',
                '/Д|Δ/' => 'D',
                '/д|δ/' => 'd',
                '/Ð|Ď|Đ/' => 'Dj',
                '/ð|ď|đ/' => 'dj',
                '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
                '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
                '/Ф/' => 'F',
                '/ф/' => 'f',
                '/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
                '/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
                '/Ĥ|Ħ/' => 'H',
                '/ĥ|ħ/' => 'h',
                '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
                '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
                '/Ĵ/' => 'J',
                '/ĵ/' => 'j',
                '/Θ/' => 'TH',
                '/θ/' => 'th',
                '/Ķ|Κ|К/' => 'K',
                '/ķ|κ|к/' => 'k',
                '/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
                '/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
                '/М/' => 'M',
                '/м/' => 'm',
                '/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
                '/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
                '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
                '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
                '/П/' => 'P',
                '/п/' => 'p',
                '/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
                '/ŕ|ŗ|ř|ρ|р/' => 'r',
                '/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
                '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
                '/Ț|Ţ|Ť|Ŧ|Τ|Т/' => 'T',
                '/ț|ţ|ť|ŧ|τ|т/' => 't',
                '/Þ|þ/' => 'th',
                '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
                '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
                '/Ƴ|Ɏ|Ỵ|Ẏ|Ӳ|Ӯ|Ў|Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
                '/ẙ|ʏ|ƴ|ɏ|ỵ|ẏ|ӳ|ӯ|ў|ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
                '/В/' => 'V',
                '/в/' => 'v',
                '/Ŵ/' => 'W',
                '/ŵ/' => 'w',
                '/Φ/' => 'F',
                '/φ/' => 'f',
                '/Χ/' => 'CH',
                '/χ/' => 'ch',
                '/Ź|Ż|Ž|Ζ|З/' => 'Z',
                '/ź|ż|ž|ζ|з/' => 'z',
                '/Æ|Ǽ/' => 'AE',
                '/ß/' => 'ss',
                '/Ĳ/' => 'IJ',
                '/ĳ/' => 'ij',
                '/Œ/' => 'OE',
                '/ƒ/' => 'f',
                '/Ξ/' => 'KS',
                '/ξ/' => 'ks',
                '/Π/' => 'P',
                '/π/' => 'p',
                '/Β/' => 'V',
                '/β/' => 'v',
                '/Μ/' => 'M',
                '/μ/' => 'm',
                '/Ψ/' => 'PS',
                '/ψ/' => 'ps',
                '/Ё/' => 'Yo',
                '/ё/' => 'yo',
                '/Є/' => 'Ye',
                '/є/' => 'ye',
                '/Ї/' => 'Yi',
                '/Ж/' => 'Zh',
                '/ж/' => 'zh',
                '/Х/' => 'Kh',
                '/х/' => 'kh',
                '/Ц/' => 'Ts',
                '/ц/' => 'ts',
                '/Ч/' => 'Ch',
                '/ч/' => 'ch',
                '/Ш/' => 'Sh',
                '/ш/' => 'sh',
                '/Щ/' => 'Shch',
                '/щ/' => 'shch',
                '/Ъ|ъ|Ь|ь/' => '',
                '/Ю/' => 'Yu',
                '/ю/' => 'yu',
                '/Я/' => 'Ya',
                '/я/' => 'ya'
            );

            if (empty($foreignCharacters) or !is_array($foreignCharacters)) {
                $arrayFrom = array();
                $arrayTo = array();

                return $str;
            }

            $arrayFrom = array_keys($foreignCharacters);
            $arrayTo = array_values($foreignCharacters);
        }

        return preg_replace($arrayFrom, $arrayTo, $str);
    }
}

if (!function_exists('convertAccentsEncode')) {
    function convertAccentsEncode($string = null)
    {

        if ($string !== null) {
            $pattern = [
                '/[à]/ui',
                '/[èé]/ui',
                '/[ì]/ui',
                '/[ò]/ui',
                '/[ù]/ui',
            ];

            $replacement = [
                '&agrave;',
                '&egrave;',
                '&igrave;',
                '&ugrave;'
            ];

            return preg_replace($pattern, $replacement, $string);
        }
    }
}

if (!function_exists('setFileDownloadCookie')) {
    function setFileDownloadCookie(): void
    {
        header('Cache-Control: max-age=60, must-revalidate');
        $sameSiteAttributes = isHttps() ? 'SameSite=None; Secure' : 'SameSite=Lax';
        header('Set-Cookie: fileDownload=true; path=/; ' . $sameSiteAttributes);
    }
}


if (!function_exists('setFileDownloadCookieCSV')) {
    function setFileDownloadCookieCSV(string $fileName = 'download.csv'): void
    {
        $sameSiteAttributes = isHttps() ? 'SameSite=None; Secure' : 'SameSite=Lax';
        header('Set-Cookie: fileDownload=true; path=/; ' . $sameSiteAttributes);
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }
}


if (!function_exists('toJson')) {
    function toJson($array, $printAndExit = false, $options = JSON_PRETTY_PRINT, $depth = 512)
    {
        $json = json_encode($array, $options, $depth);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON encode error: ' . json_last_error_msg());
        }

        if ($printAndExit) {
            echo $json;
            die();
        }

        return $json;
    }
}

if (!function_exists('lognascosto')) {
    function lognascosto($s, $par = array())
    {

        $style = "display:none;";
        echo "<pre style='$style'><br/>Log: ";
        echo $s . "<br/>";
        if (!empty($par)) {
            print_r($par);
        }
        echo "</pre>";
    }
}

if (!function_exists('_log')) {

    /**
     * Funzione LOG
     *
     * @param $notify
     * @param $log
     * @param $exit
     * @return void
     */
    function _log($notify = 'INFO', $log = '', $exit = false): void
    {
        \Helpers\CustomLog::write($notify, $log, $exit);
    }
}

if (!function_exists('execInBackground')) {
    /**
     * Funzione che permette di eseguire in background un processo CLI
     * @param string $cmd Comando da eseguire
     * @param array $args args da passare al comando
     * @param string $mode r -> lettura, w -> scrittura
     * @return mixed
     */
    function execInBackground($cmd, $args = [], $mode = "r"): mixed
    {
        if (!empty($args)) {
            $cmd .= ' ' . implode(' ', $args);
        }

        $pid = 0;
        if (OS::isWin()) {
            $r = popen("start /B " . $cmd, $mode);
            // echo "'$r'; " . gettype($r) . "\n";
            pclose($r);
        } else {
            $pid = exec($cmd . " > /dev/null &");

            /*
            //stampa l'ouput
            ob_start();
            $pid = exec($cmd . " 2>&1", $output);
            $result = ob_get_contents();
            ob_end_clean();
            // var_dump($output); die();
            */
        }

        return $pid;
    }
}

if (!function_exists('guardZone')) {
    /**
     * Funzione che permette di proteggere una zona di codice da accessi non autorizzati
     * @return void
     */
    function guardZone(mixed $data = null): void
    {
        if ($data !== null && !guard()) {
            Registry::set('___patos___profiles___method__', $data);
            $json = new \System\JsonResponse();
            if (Input::isAjax()) {
                $json->error('error', ['Non hai i permessi necessari']);
                $json->setStatusCode($json->bad());
                $json->response();
            } else {
                echo showError('Ops', 'Non hai i permessi necessari.', $json->bad(), true);
            }
            die();
        }
    }
}

if (!function_exists('jsonOut')) {

    /**
     * Emette una risposta JSON dichiarandone il Content-Type.
     *
     * Da usare al posto di "echo json_encode(...)" negli endpoint che restituiscono JSON.
     * Senza il tipo corretto la risposta viaggia come text/html e, se contiene valori
     * provenienti dalla richiesta, puo' essere interpretata dal browser come documento.
     *
     * Il corpo prodotto e' identico a json_encode(): la funzione non incapsula i dati,
     * quindi e' compatibile con i formati attesi dai datatable.
     *
     * @param mixed $data Dati da serializzare
     * @param int $flags Flag da passare a json_encode (es. JSON_INVALID_UTF8_SUBSTITUTE)
     * @return void
     */
    function jsonOut(mixed $data, int $flags = 0): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($data, $flags);
    }
}
