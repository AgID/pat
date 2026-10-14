<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Couchbase\Role;
use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\File;
use Helpers\Validators\InstitutionValidator;
use Model\InstitutionsModel;
use Model\PersonnelQualificationsModel;
use System\Cache;
use System\Email;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Uploads;
use function Symfony\Component\Translation\t;

/**
 *
 * Controller Ente (per utente normale)
 * Per la gestione dell'ente di appartenenza del dominio in cui si è loggati
 *
 */
class InstitutionAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
        helper('checkPassword');
    }

    /**
     * @description Renderizza la pagina index della gestione dell'Ente
     *
     * @return void
     * @throws Exception
     * @url /admin/institution.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $this->breadcrumb->push('Ente', '/');
        $data = [];
        $socials = [];
        $scp = [];
        $perlapa = [];
        $civicAccess = [];
        $noticeActs = [];
        $registerInfo = [];
        $ckb = [];
        $institutionQuery = InstitutionsModel::where('id', checkAlternativeInstitutionId());
        $data['institution'] = $institutionQuery->first()->toArray();

        if (!empty($data['institution']['notice_acts_info'])) {

            foreach ($data['institution']['notice_acts_info'] as $value) {

                $noticeActs[$value['meta_key']] = $value['meta_value'];
            }
        }

        if (!empty($data['institution']['socials'])) {

            foreach ($data['institution']['socials'] as $value) {

                $socials[$value['meta_key']] = $value['meta_value'];
            }
        }

        if (!empty($data['institution']['scp'])) {

            foreach ($data['institution']['scp'] as $value) {

                $scp[$value['meta_key']] = $value['meta_value'];
            }
        }

        if (!empty($data['institution']['perlapa'])) {

            foreach ($data['institution']['perlapa'] as $value) {

                $perlapa[$value['meta_key']] = $value['meta_value'];
            }
        }

        if (!empty($data['institution']['civicaccess'])) {
            foreach ($data['institution']['civicaccess'] as $value) {
                $civicAccess[$value['meta_key']] = $value['meta_value'];
            }
        }

        if (!empty($data['institution']['register_info'])) {

            foreach ($data['institution']['register_info'] as $value) {

                $registerInfo[$value['meta_key']] = $value['meta_value'];
            }
        }

        if (!empty($data['institution']['ck_b'])) {

            foreach ($data['institution']['ck_b'] as $value) {

                $ckb[$value['meta_key']] = $value['meta_value'];
            }
        }


        $institutionRoles = [];

        $institutionQualifications = optional(PersonnelQualificationsModel::select(['id', 'name as qualification_name', 'view_in_personnel_form as qualification_visible'])
            ->get())
            ->toArray();

        // Decifro i dati del SMTP
        $data['institution']['smtp_user'] = normalizeSmtpValue(checkDecrypt($data['institution']['smtp_user'] ?? ''));
        $data['institution']['smtp_from'] = normalizeSmtpValue(checkDecrypt($data['institution']['smtp_from'] ?? ''));
        $data['institution']['smtp_host'] = normalizeSmtpHost(checkDecrypt($data['institution']['smtp_host'] ?? ''));

        // Decifro i dati SMTP PEC
        $data['institution']['pec_smtp_user'] = normalizeSmtpValue(checkDecrypt($data['institution']['pec_smtp_user'] ?? ''));
        $data['institution']['pec_smtp_from'] = normalizeSmtpValue(checkDecrypt($data['institution']['pec_smtp_from'] ?? ''));
        $data['institution']['pec_smtp_host'] = normalizeSmtpHost(checkDecrypt($data['institution']['pec_smtp_host'] ?? ''));

        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        // Dati header della sezione
        $data['titleSection'] = 'Configurazione Ente';
        $data['subTitleSection'] = 'OPZIONI DI CONFIGURAZIONE AVANZATE DEL PORTALE';
        $data['sectionIcon'] = '<i class="fas fa-cogs fa-3x"></i>';

        $data['formAction'] = '/admin/institution';
        $data['formSettings'] = [
            'name' => 'form_institution',
            'id' => 'form_institution',
            'class' => 'form_institution'
        ];

        $data['_storageType'] = 'update';
        $data['socials'] = $socials;
        $data['scp'] = $scp;
        $data['perlapa'] = $perlapa;
        $data['civicAccess'] = $civicAccess;
        $data['noticeAct'] = $noticeActs;
        $data['registerInfo'] = $registerInfo;
        $data['ckb'] = $ckb;
        $data['ir'] = $institutionRoles;
        $data['institutionqualifications'] = $institutionQualifications;

        $data['custom_at_map_exist'] = @file_exists('./media' . DIRECTORY_SEPARATOR . instituteDir($data['institution']['short_institution_name']) . DIRECTORY_SEPARATOR.'custom_at_map.json');

        render('institution/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che ha il compito di testare la configurazione per l'invio delle email
     *
     * @return void
     * @throws Exception
     * @url /institution/try/sending/email.html
     * @method GET
     */
    public function trySendingEmail(): void
    {
        parent::__construct(__CLASS__);

        $json = new JsonResponse();
        $code = $json->success();

        $senderType = (int)Input::post('sender_type_mailer');
        $isValid = true;
        $errors = '';
        $mailTest = [
            'handled' => false,
            'keys_to_fetch' => ['full_name_institution'],
            'config' => [],
            'from' => 'noreply@patisweb.it',
            'label' => 'Configurazione non riconosciuta'
        ];

        // Validazione in base al tipo di invio
        if ($senderType === 0) {
            $validator = new InstitutionValidator();
            $check = $validator->validateSMTP();

            if (!$check['is_success']) {
                $isValid = false;
                $errors = $check['errors'];
            }

            $mailTest['handled'] = true;
            $mailTest['keys_to_fetch'][] = 'smtp_pass';
        } else {

            if (empty($mailTest['handled'])) {
                $isValid = false;
                $errors = 'Tipologia di invio non valida.';
            } else {
                $isValid = !empty($mailTest['is_valid']);
                $errors = !empty($mailTest['errors']) ? $mailTest['errors'] : '';
            }
        }

        if ($isValid) {
            if (empty($mailTest['handled'])) {
                $code = $json->bad();
                $json->error('error', 'Tipologia di invio non valida.');
                $json->setStatusCode($code);
                $json->response();
                return;
            }

            $info = patOsInstituteInfo($mailTest['keys_to_fetch']);
            $config = [];
            $from = 'noreply@patisweb.it'; // Default

            // Configurazione in base al tipo
            if ($senderType === 0) {
                $password = !empty(Input::post('smtp_password', true)) && Input::post('smtp_password', true) != '********'
                    ? normalizeSmtpValue(Input::post('smtp_password', true))
                    : normalizeSmtpValue(checkDecrypt($info['smtp_pass'] ?? ''));

                $config['protocol'] = 'smtp';
                $config['smtp_user'] = normalizeSmtpValue(Input::post('smtp_username', true));
                $config['smtp_from'] = normalizeSmtpValue(Input::post('smtp_from', true));
                $config['smtp_pass'] = $password;
                $config['smtp_host'] = normalizeSmtpHost(Input::post('smtp_host', true));
                $config['smtp_port'] = normalizeSmtpValue(Input::post('smtp_port', true));
                $config['smtp_security'] = normalizeSmtpValue(Input::post('smtp_security', true));
                $config['smtp_auth'] = (int) Input::post('smtp_auth', true) === 1;

                // Il form SMTP classico invia il campo "smtp_security", mentre il mailer core
                // (System\Email) legge il crittografia del trasporto dal campo "smtp_crypto":
                // normalizza qui per evitare che il test invii sempre in chiaro.
                if (isset($config['smtp_security']) && !isset($config['smtp_crypto'])) {
                    $config['smtp_crypto'] = $config['smtp_security'];
                }

                $from = mailSenderAddress($config) ?: 'noreply@patisweb.it';
            } else {

                $config = !empty($mailTest['config']) && is_array($mailTest['config']) ? $mailTest['config'] : [];
                $from = !empty($mailTest['from']) ? $mailTest['from'] : $from;
            }

            // Test di connessione: nessun fallback su piattaforma, altrimenti un Ente con
            // configurazione non funzionante risulterebbe "positivo" perche' in realta' avrebbe
            // inviato con l'account di piattaforma. Si integrano solo le costanti tecniche fisse,
            // senza passare da getMailConfig()/loadConfigMail().
            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $config['wordwrap'] = true;
            $config['mailtype'] = 'html';
            $config['priority'] = 3;
            $config['wrapchars'] = 76;
            $configs = $config;

            $configurationError = $this->validateMailTestResolvedConfig($senderType, $config);
            if (!empty($configurationError)) {
                $code = $json->bad();
                $json->error('error', $this->buildMailTestErrorMessage($senderType, $configurationError));
                $json->setStatusCode($code);
                $json->response();
                return;
            }

            $mailSenderClass = Email::class;

            if (
                !class_exists($mailSenderClass)
                || ($mailSenderClass !== Email::class && !is_subclass_of($mailSenderClass, Email::class))
            ) {
                $code = $json->bad();
                $json->error('error', 'Tipologia di invio non valida.');
                $json->setStatusCode($code);
                $json->response();
                return;
            }

            // Attiva il debug del mailer solo per l'SMTP classico, dove serve il dettaglio della
            // negoziazione (banner, HELO/EHLO, STARTTLS).
            if ($senderType === 0) {
                $configs['debug'] = true;
            }

            $email = new $mailSenderClass($configs);

            $phpWarnings = [];
            set_error_handler(function ($errno, $errstr) use (&$phpWarnings) {
                $phpWarnings[] = $errstr;
                return true;
            }, E_ALL);

            // La catena di invio viene avvolta in try/finally: se una qualunque chiamata solleva
            // un'eccezione, restore_error_handler() deve comunque essere eseguito, altrimenti
            // l'error handler custom (che inghiotte silenziosamente ogni warning/notice) resterebbe
            // attivo per il resto della richiesta HTTP.
            try {
                $send = $email->from($from, 'IsWeb S.p.A - test invio email')
                    ->to(Input::post('email', true, true, true, Input::CAST_STRIP))
                    ->set_newline("\r\n")
                    ->subject('Test del server smtp - ' . ($info['full_name_institution'] ?? 'Ente') . ' - Pat OS')
                    ->message('Test configurazione smtp (Tipo: ' . $senderType . ')')
                    ->send();
            } finally {
                restore_error_handler();
            }

            if ($send === true) {

                $json->set('message', 'Il test ha dato esito POSITIVO. E-mail inviata con successo.');
                Log::info($this->sanitizeMailTestErrorText('[ ENTE:' . checkAlternativeInstitutionId() . '] ' . $email->print_debugger()));
            } else {

                $code = $json->bad();
                $debugMessage = $email->print_debugger([]);
                $json->error('error', $this->buildMailTestErrorMessage($senderType, $debugMessage));


                $contextParts = ['Tipo invio: ' . $senderType];
                if ($senderType === 0) {
                    $contextParts[] = 'Host: ' . ($configs['smtp_host'] ?? 'n/d');
                    $contextParts[] = 'Porta: ' . ($configs['smtp_port'] ?? 'n/d');
                    $contextParts[] = 'Sicurezza: ' . ($configs['smtp_crypto'] ?? 'n/d');
                    $contextParts[] = 'Auth: ' . (!empty($configs['smtp_auth']) ? 'si' : 'no');
                }
                if (!empty($phpWarnings)) {
                    $contextParts[] = 'Warning PHP: ' . implode(' / ', $phpWarnings);
                }

                // La sanitizzazione viene applicata UNA SOLA VOLTA sull'intera riga di log (contesto
                // tecnico + warning PHP + debug del mailer), non solo sull'output di print_debugger(),
                // così eventuali valori sensibili presenti nei warning PHP vengono comunque oscurati.
                $logLine = '[ ENTE:' . checkAlternativeInstitutionId() . '] '
                    . implode(' | ', $contextParts)
                    . ' | ' . $email->print_debugger();
                Log::danger($this->sanitizeMailTestErrorText($logLine));
                // trace($email->print_debugger());
            }
        } else {

            $code = $json->bad();
            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * Costruisce un messaggio di errore leggibile per il test di invio email.
     * Il debug completo resta nei log server; all'utente viene mostrata solo una sintesi
     * sanitizzata, in modo da non esporre password, client secret, access key o token.
     *
     * @param int $senderType
     * @param string $debugMessage
     * @param bool $isPec
     * @return string
     */
    private function buildMailTestErrorMessage(int $senderType, string $debugMessage = '', bool $isPec = false): string
    {
        $senderTypes = [0 => 'SMTP classico'];

        $configurationName = $isPec
            ? 'SMTP PEC'
            : ($senderTypes[$senderType] ?? 'Configurazione non riconosciuta');

        $message = $isPec
            ? 'Il test PEC ha dato esito NEGATIVO.'
            : 'Il test ha dato esito NEGATIVO.';

        $message .= '<br>Configurazione testata: ' . htmlEscape($configurationName) . '.';

        $detail = $this->extractMailTestErrorDetail($debugMessage);
        if (!empty($detail)) {
            $message .= '<br><br>Dettaglio errore:<br>' . $detail;
        } else {
            $message .= '<br><br>Dettaglio errore:<br>Il server di posta non ha restituito un dettaglio tecnico utilizzabile.';
        }

        $hint = $this->getMailTestErrorHint($senderType, $detail, $isPec);
        if (!empty($hint)) {
            $message .= '<br><br>Controlli consigliati:<br>' . $hint;
        }

        return $message;
    }

    /**
     * Verifica i parametri effettivamente usati dal test dopo il recupero dei valori mascherati.
     * Questo evita invii ambigui quando il form contiene ******** ma il valore salvato non esiste,
     * oppure quando l'utente abilita l'autenticazione SMTP senza indicare la password.
     *
     * @param int $senderType
     * @param array $config
     * @param bool $isPec
     * @return string
     */
    private function validateMailTestResolvedConfig(int $senderType, array $config = [], bool $isPec = false): string
    {
        $errors = [];

        if ($senderType === 0) {
            if (empty($config['smtp_host'])) {
                $errors[] = 'Errore configurazione: indirizzo server SMTP mancante.';
            }

            if (empty($config['smtp_port'])) {
                $errors[] = 'Errore configurazione: porta SMTP mancante.';
            }

            if (!empty($config['smtp_auth'])) {
                if (empty($config['smtp_user'])) {
                    $errors[] = 'Errore configurazione: username SMTP obbligatorio perché l\'autenticazione è abilitata.';
                }

                if (empty($config['smtp_pass'])) {
                    $errors[] = 'Errore configurazione: password SMTP obbligatoria perché l\'autenticazione è abilitata.';
                }
            }

            if ($isPec && empty($config['smtp_user'])) {
                $errors[] = 'Errore configurazione: username PEC SMTP mancante.';
            }
            /*
             * Il mittente e' il campo "From" quando valorizzato, altrimenti lo username:
             * se nessuno dei due e' un indirizzo email il test fallirebbe nella validazione
             * del mittente eseguita dal mailer, senza indicare il campo da correggere.
             */
            if (mailSenderAddress($config) === '') {
                $errors[] = $isPec
                    ? 'Errore configurazione: mittente PEC non utilizzabile. Compila il campo '
                        . '"PEC SMTP - From" con un indirizzo email valido.'
                    : 'Errore configurazione: mittente non utilizzabile. Compila il campo '
                        . '"SMTP - From" con un indirizzo email valido.';
            }
        }

        return implode("\n", $errors);
    }

    /**
     * Estrae dal debug del client Email solo le righe utili alla diagnosi dell'errore.
     * Le informazioni sensibili vengono oscurate prima di restituire il testo al popup AJAX.
     *
     * @param string $debugMessage
     * @return string
     */
    private function extractMailTestErrorDetail(string $debugMessage = ''): string
    {
        $debugMessage = html_entity_decode($debugMessage, ENT_QUOTES, 'UTF-8');
        $debugMessage = preg_replace('/<br\s*\/?>/i', "\n", $debugMessage);
        $debugMessage = preg_replace('/<\/?(strong|pre)>/i', "\n", $debugMessage);
        $debugMessage = strip_tags($debugMessage);
        $debugMessage = $this->sanitizeMailTestErrorText($debugMessage);

        $lines = preg_split('/\r\n|\r|\n/', $debugMessage);
        $usefulLines = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/\s+/', ' ', $line));
            if ($line === '') {
                continue;
            }

            if (stripos($line, 'test configurazione') !== false) {
                continue;
            }

            if (
                stripos($line, 'error') !== false ||
                stripos($line, 'errore') !== false ||
                stripos($line, 'failed') !== false ||
                stripos($line, 'failure') !== false ||
                stripos($line, 'authentication') !== false ||
                stripos($line, 'auth') !== false ||
                stripos($line, 'http') !== false ||
                stripos($line, 'smtp') !== false ||
                stripos($line, 'oauth') !== false ||
                preg_match('/^[45][0-9]{2}\b/', $line) === 1
            ) {
                $usefulLines[] = htmlEscape($line);
                $translatedLine = $this->translateMailServerErrorLine($line);
                if (!empty($translatedLine)) {
                    $usefulLines[] = htmlEscape($translatedLine);
                }
            }

            if (count($usefulLines) >= 6) {
                break;
            }
        }

        return implode('<br>', array_unique($usefulLines));
    }

    /**
     * Traduce i codici SMTP/HTTP piu frequenti in indicazioni leggibili, mantenendo comunque
     * nel messaggio anche la riga tecnica originale restituita dal server.
     *
     * @param string $line
     * @return string
     */
    private function translateMailServerErrorLine(string $line = ''): string
    {
        $line = strtolower($line);

        if (strpos($line, '530') !== false || strpos($line, 'authentication required') !== false || strpos($line, 'auth required') !== false) {
            return 'Diagnosi: il server richiede autenticazione. Verificare che username e password siano presenti e che "Usa autenticazione" sia impostato su Si.';
        }

        if (strpos($line, '535') !== false || strpos($line, 'authentication failed') !== false || strpos($line, 'authentications unsuccessful') !== false) {
            return 'Diagnosi: autenticazione rifiutata dal server. Verificare username, password e permessi dell\'account.';
        }

        if (strpos($line, '534') !== false || strpos($line, '5.7.14') !== false || strpos($line, '5.7.139') !== false) {
            return 'Diagnosi: il provider ha rifiutato l\'accesso per policy di sicurezza o autenticazione moderna. Verificare MFA, app password, OAuth2 e criteri del tenant.';
        }

        if (strpos($line, '550') !== false || strpos($line, 'sender') !== false && strpos($line, 'denied') !== false) {
            return 'Diagnosi: mittente o destinatario rifiutato dal server. Verificare mailbox mittente, dominio autorizzato e destinatario di test.';
        }

        if (strpos($line, '553') !== false || strpos($line, 'relaying denied') !== false || strpos($line, 'relay access denied') !== false) {
            return 'Diagnosi: relay non consentito. Il server non autorizza questo account o questo mittente a inviare verso il destinatario indicato.';
        }

        if (strpos($line, 'connection timed out') !== false || strpos($line, 'timed out') !== false) {
            return 'Diagnosi: connessione scaduta. Verificare host, porta, firewall/proxy e raggiungibilita del server.';
        }

        if (strpos($line, 'unauthorized') !== false || strpos($line, '401') !== false) {
            return 'Diagnosi: richiesta non autorizzata. Verificare credenziali, token e permessi applicativi.';
        }

        if (strpos($line, 'forbidden') !== false || strpos($line, '403') !== false) {
            return 'Diagnosi: accesso negato dal servizio. Verificare credenziali, mittente, dominio autorizzato o policy del server.';
        }

        return '';
    }

    /**
     * Oscura valori tecnici che non devono essere mostrati nel messaggio AJAX di errore.
     *
     * @param string $text
     * @return string
     */
    private function sanitizeMailTestErrorText(string $text = ''): string
    {
        // I pattern Bearer/Signature vanno valutati PRIMA di quello generico per keyword:
        // altrimenti "Authorization: Bearer <token>" viene consumato dal pattern generico,
        // che oscura solo la parola "Bearer" e lascia il token in chiaro dopo di essa.
        // Tra la keyword e il separatore è ammessa solo un'eventuale annotazione tra parentesi
        // (es. "Access Token (first 50 chars): ..."): una finestra più permissiva farebbe
        // scattare l'oscuramento su un ":" non correlato, corrompendo messaggi diagnostici
        // legittimi come "535 Username and Password not accepted. Learn more at ...".
        // Le virgolette opzionali attorno a keyword e valore coprono la forma JSON prodotta dai
        // token endpoint OAuth2 (es. {"access_token":"..."}), che altrimenti sfuggirebbe al match.
        $patterns = [
            '/(Bearer)\s+[A-Za-z0-9._~+\-\/=]+/i',
            '/(Signature)=[A-Za-z0-9+\/=]+/i',
            '/(client_secret|password|smtp_pass|oauth2_password|access[_\s]?key|acs_access_key|refresh[_\s]?token|access[_\s]?token|authorization)"?(?:\s*\([^)]{0,40}\))?\s*[:=]\s*"?[^&\s<"]+/i'
        ];

        return preg_replace($patterns, '$1: [oscurato]', $text);
    }

    /**
     * Aggiunge un'indicazione operativa coerente con la modalita di invio selezionata.
     *
     * @param int $senderType
     * @param string $detail
     * @param bool $isPec
     * @return string
     */
    private function getMailTestErrorHint(int $senderType, string $detail = '', bool $isPec = false): string
    {
        $detail = strtolower(strip_tags($detail));

        if (strpos($detail, 'password smtp obbligatoria') !== false) {
            return 'inserire la password del server SMTP oppure impostare "Usa autenticazione" su No se il server consente invio senza credenziali.';
        }

        if (strpos($detail, 'curl') !== false || strpos($detail, 'connection') !== false || strpos($detail, 'connect') !== false) {
            return 'verificare connettivita del server, DNS, firewall/proxy e raggiungibilita dell\'host configurato.';
        }

        if (strpos($detail, 'auth') !== false || strpos($detail, 'login') !== false || strpos($detail, '535') !== false || strpos($detail, '401') !== false || strpos($detail, '403') !== false) {
            return 'verificare username, password e autorizzazione SMTP del server configurato.';
        }

        if (strpos($detail, 'tls') !== false || strpos($detail, 'ssl') !== false || strpos($detail, 'starttls') !== false) {
            return 'verificare porta, protocollo SSL/TLS e certificato del server di posta.';
        }

        if ($isPec || $senderType === 0) {
            return 'verificare host, porta, SSL/TLS, autenticazione e credenziali SMTP.';
        }

        return '';
    }

    /**
     * @description Funzione che ha il compito di testare la configurazione per l'invio delle email PEC
     *
     * @return void
     * @throws Exception
     * @url /institution/try/sending/pec-email.html
     * @method POST
     */
    public function trySendingPecEmail(): void
    {
        parent::__construct(__CLASS__);

        $json = new JsonResponse();
        $code = $json->success();

        $isValid = true;
        $errors = '';

        $validator = new InstitutionValidator();
        $check = $validator->validatePecSMTP();

        if (!$check['is_success']) {
            $isValid = false;
            $errors = $check['errors'];
        }

        if ($isValid) {
            $info = patOsInstituteInfo(['full_name_institution', 'pec_smtp_pass']);
            $config = [];

            $password = !empty(Input::post('pec_smtp_password', true)) && Input::post('pec_smtp_password', true) != '********'
                ? normalizeSmtpValue(Input::post('pec_smtp_password', true))
                : normalizeSmtpValue(checkDecrypt($info['pec_smtp_pass'] ?? ''));

            $config['protocol'] = 'smtp';
            $config['smtp_user'] = normalizeSmtpValue(Input::post('pec_smtp_username', true));
            $config['smtp_from'] = normalizeSmtpValue(Input::post('pec_smtp_from', true));
            $config['smtp_pass'] = $password;
            $config['smtp_host'] = normalizeSmtpHost(Input::post('pec_smtp_host', true));
            $config['smtp_port'] = normalizeSmtpValue(Input::post('pec_smtp_port', true));
            $config['smtp_crypto'] = strtolower(normalizeSmtpValue(Input::post('pec_smtp_security', true)));
            $config['smtp_auth'] = (int) Input::post('pec_smtp_auth', true) === 1;

            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $config['wordwrap'] = true;
            $config['mailtype'] = "html";
            $config['priority'] = 3;
            $config['wrapchars'] = 76;

            $from = mailSenderAddress($config) ?: 'noreply@patisweb.it';

            $configurationError = $this->validateMailTestResolvedConfig(0, $config, true);
            if (!empty($configurationError)) {
                $code = $json->bad();
                $json->error('error', $this->buildMailTestErrorMessage(0, $configurationError, true));
                $json->setStatusCode($code);
                $json->response();
                return;
            }

            // Attiva il debug del mailer per ottenere il dettaglio della negoziazione SMTP
            // (banner, HELO/EHLO, STARTTLS) nel log in caso di fallimento del test PEC.
            // Qui l'attivazione è incondizionata perché il flusso PEC è sempre SMTP classico.
            $config['debug'] = true;

            $email = institutionMailSender($config, 0);

            $phpWarnings = [];
            set_error_handler(function ($errno, $errstr) use (&$phpWarnings) {
                $phpWarnings[] = $errstr;
                return true;
            }, E_ALL);

            // La catena di invio viene avvolta in try/finally: se una qualunque chiamata solleva
            // un'eccezione, restore_error_handler() deve comunque essere eseguito, altrimenti
            // l'error handler custom (che inghiotte silenziosamente ogni warning/notice) resterebbe
            // attivo per il resto della richiesta HTTP.
            try {
                $send = $email->from($from, 'IsWeb S.p.A - test invio PEC')
                    ->to(Input::post('email', true, true, true, Input::CAST_STRIP))
                    ->set_newline("\r\n")
                    ->subject('Test del server PEC - ' . ($info['full_name_institution'] ?? 'Ente') . ' - Pat OS')
                    ->message('Test configurazione PEC SMTP Classico')
                    ->send();
            } finally {
                restore_error_handler();
            }

            if ($send === true) {
                $json->set('message', 'Il test PEC ha dato esito POSITIVO. E-mail PEC inviata con successo.');
                Log::info($this->sanitizeMailTestErrorText('[ ENTE:' . checkAlternativeInstitutionId() . '] ' . $email->print_debugger()));
            } else {
                $code = $json->bad();
                $debugMessage = $email->print_debugger([]);
                $json->error('error', $this->buildMailTestErrorMessage(0, $debugMessage, true));

                // La sanitizzazione viene applicata UNA SOLA VOLTA sull'intera riga di log (contesto
                // tecnico + warning PHP + debug del mailer), non solo sull'output di print_debugger(),
                // così eventuali valori sensibili presenti nei warning PHP vengono comunque oscurati.
                $logLine = '[ ENTE:' . checkAlternativeInstitutionId() . '] '
                    . 'Tipo invio: PEC SMTP classico | '
                    . 'Host: ' . ($config['smtp_host'] ?? 'n/d') . ' | '
                    . 'Porta: ' . ($config['smtp_port'] ?? 'n/d') . ' | '
                    . 'Sicurezza: ' . ($config['smtp_crypto'] ?? 'n/d') . ' | '
                    . 'Auth: ' . (!empty($config['smtp_auth']) ? 'si' : 'no')
                    . (!empty($phpWarnings) ? ' | Warning PHP: ' . implode(' / ', $phpWarnings) : '')
                    . ' | ' . $email->print_debugger();
                Log::danger($this->sanitizeMailTestErrorText($logLine));
            }
        } else {
            $code = $json->bad();
            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Per la gestione dell'Ente.
     * Funzione che effettua l'update dell'ente in cui si è loggati
     *
     * @return void
     * @throws Exception
     * @url /admin/dashboard/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update', true);


        $hasError = false;
        $doUpload = null;
        $doUploadFavicon = null;
        $json = new JsonResponse();
        $validator = new InstitutionValidator();

        $code = $json->success();
        $check = $validator->check();

        // Controllo se la validazione del form è avvenuta con successo
        if ($check['is_success']) {

            $institutionId = !empty(Input::post('id')) ? (int) Input::post('id') : checkAlternativeInstitutionId();
            $institution = InstitutionsModel::find($institutionId);
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            $uploadData = [];

            // Controllo se è da aggiornare il file del logo
            if (filesUploaded('simple_logo_file') === true) {

                $settings = [
                    'field' => 'simple_logo_file',
                    'dir' => instituteDir($institution->short_institution_name),
                    'allowed_types' => 'png|gif|jpeg|jpg',
                ];

                // Carico il nuovo file del logo Ente
                $doUpload = $this->doUpload($settings);
                $hasError = (bool) $doUpload['success'];

                if (!$hasError) {

                    // Se esiste elimino il vecchio file logo dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->simple_logo_file)) {

                        File::delete(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->simple_logo_file);
                    }
                }else{
                    $uploadData = $doUpload;
                }
            }

            // Controllo se è da aggiornare la favicon
            if (filesUploaded('favicon_file') === true) {

                $settings = [
                    'field' => 'favicon_file',
                    'dir' => instituteDir($institution->short_institution_name),
                    'allowed_types' => 'png|gif|jpeg|jpg|ico',
                ];

                $doUploadFavicon = $this->doUpload($settings);
                $hasError = (bool) $doUploadFavicon['success'];

                if (!$hasError) {

                    // Se esiste elimino il vecchio file logo dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->favicon_file)) {

                        File::delete(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->favicon_file);
                    }
                }else{
                    $uploadData = $doUploadFavicon;
                }
            }


            $pathATMAP = './media' . DIRECTORY_SEPARATOR . instituteDir($institution->short_institution_name) . DIRECTORY_SEPARATOR;

            if (filesUploaded('custom_at_map') === true) {

                $settings = [
                    'field' => 'custom_at_map',
                    'dir' => instituteDir($institution->short_institution_name),
                    'allowed_types' => 'json',
                    'upload_path' => $pathATMAP
                ];

                $doUploadAtMAP = $this->doUpload($settings);
                $hasError = (bool) $doUploadAtMAP['success'];

                if (!$hasError) {
                    if(file_exists($pathATMAP."custom_at_map.json")){
                        @unlink($pathATMAP."custom_at_map.json");
                    }
                    $processedName = $doUploadAtMAP['data']['file_name'] ?? '';
                    if(!empty($processedName)){
                        @rename($pathATMAP.$processedName, $pathATMAP."custom_at_map.json");
                    }
                }else{
                    $uploadData = $doUploadAtMAP;
                }
            }elseif ((bool)Input::post('delete_custom_at_map', true)){
                @unlink($pathATMAP."custom_at_map.json");
            }

            if (!$hasError) {

                // Nota: i campi elencati qui sotto vengono confrontati con i valori già salvati
                // per indicare nel log delle attività quali impostazioni sono state modificate.
                // Aggiungendo un nuovo campo, aggiungere la relativa etichetta in
                // "app/Config/institutionSettingsLabels.php".
                $data = [
                    'id_creator' => $getIdentity['id'],
                    'full_name_institution' => strip_tags(Input::post('full_name_institution', true)),
                    'vat' => strip_tags(Input::post('vat', true)),
                    'fiscal_code' => !empty(Input::post('fiscal_code')) ? strip_tags(Input::post('fiscal_code', true)) : null,
                    'email_address' => strip_tags(Input::post('email_address', true)),
                    'certified_email_address' => strip_tags(Input::post('certified_email_address', true)),
                    'top_level_institution_name' => strip_tags(Input::post('top_level_institution_name', true)),
                    'top_level_institution_url' => strip_tags(Input::post('top_level_institution_url', true)),
                    'institutional_website_url' => strip_tags(Input::post('institutional_website_url', true)),
                    'trasp_responsible_user_id' => setDefaultData((int) strip_tags(Input::post('trasp_responsible_user_id', true)), null, ['', 0]),
                    'simple_logo_file' => !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : $institution->simple_logo_file,
                    'favicon_file' => !empty($doUploadFavicon['data']['file_name']) ? $doUploadFavicon['data']['file_name'] : $institution->favicon_file,
                    'bulletin_board_url' => strip_tags(Input::post('bulletin_board_url', true)),
                    'bulletin_board_url_name' => strip_tags(Input::post('bulletin_board_url_name', true)),
                    'show_update_date' => setDefaultData(strip_tags(Input::post('show_update_date', true)), 1, ['', null]),
                    'indexable' => setDefaultData(strip_tags(Input::post('indexable', true)), 0, ['', null]),
                    'show_regulation_in_structure' => setDefaultData(strip_tags(Input::post('show_regulation_in_structure', true)), null, ['']),
                    'address_city' => strip_tags(Input::post('address_city', true)),
                    'address_street' => strip_tags(Input::post('address_street', true)),
                    'address_province' => strip_tags(Input::post('address_province', true)),
                    'address_zip_code' => strip_tags(Input::post('address_zip_code', true)),
                    'phone' => strip_tags(Input::post('phone', true)),
                    'publication_responsible' => strip_tags(Input::post('publication_responsible', true)),
                    'privacy_url' => strip_tags(Input::post('privacy_url', true)),
                    'welcome_text' => Input::post('welcome_text', true),
                    'footer_text' => Input::post('footer_text', true),
                    'accessibility_text' => setDefaultData(Input::post('accessibility_text', true), null, ['']),
                    'smtp_user' => !empty(Input::post('smtp_username')) ? checkEncrypt(normalizeSmtpValue(Input::post('smtp_username', true))) : '',
                    'smtp_from' => !empty(Input::post('smtp_from')) ? checkEncrypt(normalizeSmtpValue(Input::post('smtp_from', true))) : '',
                    'smtp_pass' => (!empty(Input::post('smtp_password')) ? (Input::post('smtp_password') !== '********' ? checkEncrypt(normalizeSmtpValue(Input::post('smtp_password', true))) : $institution->smtp_pass) : ''),
                    'smtp_host' => !empty(Input::post('smtp_host')) ? checkEncrypt(normalizeSmtpHost(Input::post('smtp_host', true))) : '',
                    'smtp_port' => normalizeSmtpValue(Input::post('smtp_port', true)),
                    'smtp_security' => setDefaultData(normalizeSmtpValue(Input::post('smtp_security', true)), null, ['']),
                    'show_smtp_auth' => setDefaultData(normalizeSmtpValue(Input::post('show_smtp_auth', true)), null, ['']),
                    'smtp_auth' => (int)Input::post('show_smtp_auth', true) === 1 ? 1 : 0,

                    // SMTP PEC
                    'pec_smtp_user' => !empty(Input::post('pec_smtp_username')) ? checkEncrypt(normalizeSmtpValue(Input::post('pec_smtp_username', true))) : '',
                    'pec_smtp_from' => !empty(Input::post('pec_smtp_from')) ? checkEncrypt(normalizeSmtpValue(Input::post('pec_smtp_from', true))) : '',
                    'pec_smtp_pass' => (!empty(Input::post('pec_smtp_password')) && Input::post('pec_smtp_password') !== '********') ? checkEncrypt(normalizeSmtpValue(Input::post('pec_smtp_password', true))) : $institution->pec_smtp_pass,
                    'pec_smtp_host' => !empty(Input::post('pec_smtp_host')) ? checkEncrypt(normalizeSmtpHost(Input::post('pec_smtp_host', true))) : '',
                    'pec_smtp_port' => normalizeSmtpValue(Input::post('pec_smtp_port', true)),
                    'pec_smtp_security' => setDefaultData(normalizeSmtpValue(Input::post('pec_smtp_security', true)), null, ['']),
                    'pec_show_smtp_auth' => setDefaultData(normalizeSmtpValue(Input::post('pec_show_smtp_auth', true)), null, ['']),

                    'statistics_tracking_code' => Input::post('statistics_tracking_code'),
                    'expiration_date' => date('Y-m-d H:i:s', strtotime('+1 year')),
                ];




                // Elenco delle impostazioni realmente modificate rispetto ai valori già salvati.
                // Viene calcolato prima dell'update perché i confronti devono avvenire sui valori
                // ancora presenti sul database, in modo da rendere tracciabile nel log delle attività
                // quali impostazioni dell'Ente sono state cambiate.
                $campiModificati = $this->getCampiEnteModificati($institution, $data);

                // Social e qualifiche del personale non stanno sulla tabella "institutions" ma su
                // tabelle collegate, quindi vengono raccolti durante il rispettivo salvataggio
                // per poter comparire anch'essi nella descrizione del log delle attività.
                $socialModificati = [];
                $qualificheModificate = [];

                //
                $update = InstitutionsModel::where('id', '=', $institutionId)->update($data);

                // Update dei link social dell'ente e dei dati relativi ai bandi di gara
                if ((int) $institutionId !== 0 && (bool) $update === true) {


                    if (Input::post('_qualifications', true)) {
                        // Insert dei dati del historical
                        $qualificationsData = objectToArray(json_decode(Input::post('_qualifications', true)));

                        if (!empty($qualificationsData)) {

                            // Le qualifiche già salvate vengono lette in un'unica query, così da
                            // riconoscere quali cambiano davvero senza interrogare il database
                            // a ogni giro del ciclo.
                            $qualificheSalvate = [];

                            foreach (PersonnelQualificationsModel::where('institution_id', $institutionId)->get()->toArray() as $qualificaSalvata) {

                                $qualificheSalvate[$qualificaSalvata['id']] = $qualificaSalvata;
                            }

                            foreach ($qualificationsData as $qualification) {

                                $dataQualification = [
                                    'institution_id' => $institutionId,
                                    'name' => strip_tags($qualification['qualification_name']),
                                    'view_in_personnel_form' => $qualification['qualification_visible'] == 0 ? 0 : 1,
                                ];

                                if (!empty($qualification['id'])) {

                                    $qualificaPrecedente = !empty($qualificheSalvate[$qualification['id']])
                                        ? $qualificheSalvate[$qualification['id']]
                                        : null;

                                    // La qualifica viene segnalata quando cambia il nome oppure la
                                    // visibilità nel form del personale.
                                    $qualificaCambiata = $qualificaPrecedente === null
                                        || (string) $qualificaPrecedente['name'] !== (string) $dataQualification['name']
                                        || (int) $qualificaPrecedente['view_in_personnel_form'] !== (int) $dataQualification['view_in_personnel_form'];

                                    if ($qualificaCambiata) {

                                        $qualificheModificate[] = $dataQualification['name'] . ' (modificata)';
                                    }

                                    PersonnelQualificationsModel::where('id', $qualification['id'])->update($dataQualification);
                                } else {

                                    $qualificheModificate[] = $dataQualification['name'] . ' (aggiunta)';

                                    PersonnelQualificationsModel::create($dataQualification);
                                }
                            }
                        }
                    }

                    // Elimino la cache
                    $cache = new Cache();
                    $cache->delete($institutionId . '_social_links');

                }

                if ($update) {

                    $descrizioneLog = 'Modifica Ente "' . $institution->full_name_institution . '" con ID (' . $institutionId . ')';

                    if (!empty($campiModificati)) {

                        $descrizioneLog .= '. Impostazioni modificate: ' . implode(', ', $campiModificati);
                    }

                    if (!empty($socialModificati)) {

                        $descrizioneLog .= '. Social modificati: ' . implode(', ', $socialModificati);
                    }

                    if (!empty($qualificheModificate)) {

                        $descrizioneLog .= '. Qualifiche del personale modificate: ' . implode(', ', $qualificheModificate);
                    }

                    // Il form può essere salvato senza variazioni: registrare comunque il log
                    // permette di distinguere un salvataggio a vuoto da una modifica reale.
                    if (empty($campiModificati) && empty($socialModificati) && empty($qualificheModificate)) {

                        $descrizioneLog .= '. Nessuna impostazione modificata';
                    }

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Modifica Ente',
                        'description' => $descrizioneLog,
                        'area' => 'institution',
                        'object_id' => 56,
                        'record_id' => $institutionId,
                    ]);

                    $json->set('message', sprintf(__('success_edit', null, 'patos'), 'Ente '));
                } else {

                    $code = $json->bad();
                    $json->error('error', __('tmp_error', null, 'patos'));
                }
            } else {

                $code = $json->bad();
                $json->error('error', $uploadData['data']);
            }
        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }


    /**
     * @description Confronta i valori già salvati dell'Ente con quelli in fase di salvataggio e
     * restituisce le etichette delle impostazioni realmente modificate.
     *
     * Le etichette e i campi da escludere dal confronto sono definiti in
     * "app/Config/institutionSettingsLabels.php"
     *
     * Vengono restituiti solo i nomi delle impostazioni e mai i relativi valori, perché la
     * configurazione dell'Ente contiene credenziali SMTP e PEC che non devono essere scritte
     * nel log delle attività.
     *
     * @param InstitutionsModel $institution Record dell'Ente con i valori attualmente salvati
     * @param array             $data        Valori che stanno per essere scritti sul database
     * @return array Elenco delle etichette delle impostazioni modificate
     * @throws Exception
     */
    private function getCampiEnteModificati(InstitutionsModel $institution, array $data = []): array
    {
        $etichette = config('institution_settings_labels', [], 'institutionSettingsLabels');
        $campiDaIgnorare = config('institution_settings_excluded_fields', [], 'institutionSettingsLabels');

        $etichette = is_array($etichette) ? $etichette : [];
        $campiDaIgnorare = is_array($campiDaIgnorare) ? $campiDaIgnorare : [];

        $etichette = is_array($etichette) ? $etichette : [];
        $campiDaIgnorare = is_array($campiDaIgnorare) ? $campiDaIgnorare : [];

        $campiModificati = [];

        foreach ($data as $campo => $valore) {

            if (in_array($campo, $campiDaIgnorare, true)) {
                continue;
            }

            // Il confronto avviene sulla rappresentazione testuale perché i valori provenienti
            // dal form sono stringhe mentre quelli letti dal database possono essere interi o null.
            $valoreSalvato = (string) $institution->{$campo};

            if ($valoreSalvato === (string) $valore) {
                continue;
            }

            // Se il campo non è ancora stato mappato in configurazione viene usato il nome della
            // colonna reso leggibile, in modo che una nuova impostazione risulti comunque
            // tracciata nel log anche prima che venga censita la sua etichetta.
            $campiModificati[] = !empty($etichette[$campo])
                ? $etichette[$campo]
                : ucfirst(str_replace('_', ' ', $campo));
        }

        return $campiModificati;
    }

    /**
     * @description Funzione per l'upload dei file
     *
     * @param array $settings Setting per l'upload
     * @return array
     */
    private function doUpload(array $settings = []): array
    {
        $field = $settings['field'];
        $dir = $settings['dir'];
        $allowedTypes = $settings['allowed_types'];

        $data = [];

        $dir = !empty($dir) ? $dir : '';

        $upload = new Uploads();
        $config['upload_path'] = './media' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

        if(!empty($settings['upload_path'])){
            $config['upload_path'] = $settings['upload_path'];
        }

        $config['allowed_types'] = $allowedTypes;
        $config['encrypt_name'] = true;
        $config['file_ext_tolower'] = true;
        $config['remove_spaces'] = true;
        $config['max_size'] = 5024;
        $config['max_width'] = 1024;
        $config['max_height'] = 1024;
        $config['min_width'] = 50;
        $config['min_height'] = 50;
        $config['max_filename'] = 50;

        $upload->initialize($config);

        if ($upload->doUpload($field)) {

            $data['success'] = false;
            $data['data'] = $upload->data();
        } else {

            $data['success'] = true;
            $data['data'] = $upload->displayErrors();
        }

        return $data;
    }
}
