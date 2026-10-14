<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

use Exception;
use Model\InstitutionLinksModel;
use Model\InstitutionsModel;
use System\Database;
use System\Input;
use System\Registry;
use System\Validator;
use function Symfony\Component\Translation\t;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Validator per l'oggetto Ente (institutions)
 */
class InstitutionValidator
{
    public Validator $validator;

    /**
     * @description Costrutto
     *
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste un bilancio con quell'ID per l'ente
     *
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId(): array
    {
        $this->validator->label('ID ente')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () {

                //Recupero l'ente con l'id passato in input
                $check = InstitutionsModel::select()->find(uri()->segment(4, 0));

                //Se non esiste l'ente con questo id, mostro un messaggio di errore
                if ($check == null) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'ente ')
                    ];
                }

                //Se esiste l'ente lo salvo nel registro
                Registry::set('institution', $check);

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
     * @return array
     * @throws Exception
     */
    public function check(): array
    {
        return $this->validate();
    }

    /**
     * Metodo per la validazione dei campi del form
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(): array
    {
        // Inizio Validatore modulo
        $this->validator->label('Nome completo ente')
            ->value(Input::post('full_name_institution'))
            ->required()
            ->betweenString(2, 191)
            ->end();



        $this->validator->label('Tipo Ente')
            ->value(Input::post('institution_type_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Partita IVA')
            ->value(Input::post('vat'))
            ->required()
            ->add(function () {
                $vatCheck = new VatRule(Input::post('vat'));
                if (!$vatCheck->isValidate()) {
                    return ['error' => __('vat_error', null, 'patos')];
                }
            })
            ->end();


        $this->validator->label('Codice fiscale')
            ->value(Input::post('fiscal_code'));


        $this->validator->add(function () {
            $fcCheck = new VatRule(Input::post('fiscal_code'));
            //NON utilizzare FiscalCodeRule::class perchè funziona solo per i codici fiscali di persone fisiche
            if (!$fcCheck->isValidate()) {
                return ['error' => __('fiscal_code_error_generic', null, 'patos')];
            }

            $checkExist = InstitutionsModel::query()->where('fiscal_code', Input::post('fiscal_code'));

            //controllo se esiste un altro ente con lo stesso codice fiscale
            if (Input::post('_storage_type') == 'update') {
                $checkExist = $checkExist->where('id', '!=', (int) Input::post('id'));
            }

            $checkExist = $checkExist->first();

            if (!empty($checkExist)) {
                return ['error' => __('fiscal_code_exist', null, 'patos')];
            }
        })->end();

        $this->validator->label('Indirizzo email normale')
            ->value(Input::post('email_address'))
            ->required()
            ->isEmail()
            ->end();

        $this->validator->label('Indirizzo email certificata')
            ->value(Input::post('certified_email_address'))
            ->required()
            ->isEmailRelaxed()
            ->end();

        $this->validator->label('Ente di appartenenza')
            ->value(Input::post('certified_email_address'))
            ->betweenString(5, 500)
            ->end();

        $this->validator->label('URL ente di appartenenza')
            ->value(Input::post('top_level_institution_url'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        if (filesUploaded('simple_logo_file')) {
            $this->validator->label('simple_logo_file')
                ->file(Input::files('simple_logo_file'))
                ->required()
                ->allowed(['png', 'jpeg', 'jpg', 'gif'])
                ->maxSize('5MB')
                ->minSize('1KB')
                ->allowedDimensions('50', '1024', '50', '1024')
                ->end();
        }

        if (filesUploaded('custom_css')) {
            $this->validator->label('File css personalizzato')
                ->file(Input::files('custom_css'))
                ->required()
                ->allowed(['css'])
                ->maxSize('5MB')
                ->end();
        }

        $this->validator->label('Url portale istituzionale')
            ->value(Input::post('institutional_website_url'))
            ->isUrl()
            ->betweenString(1, 200)
            ->end();

        $this->validator->label('Url Albo Pretorio')
            ->value(Input::post('bulletin_board_url'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        $this->validator->label('Etichetta Url Albo Pretorio')
            ->value(trim((string)Input::post('bulletin_board_url_name')))
            ->betweenString(4, 25)
            ->end();

        $this->validator->label('Supporto cliente')
            ->value(Input::post('customer_support'))
            ->in('0,1')
            ->end();

        $this->validator->label('Indicizzabile dai motori di ricerca')
            ->value(Input::post('indexable'))
            ->in('0,1')
            ->end();

        $this->validator->label('Responsabile della Trasparenza')
            ->value(Input::post('trasp_responsible_user_id'))
            ->isInt()
            ->end();

        $this->validator->label('Mostra la data di ultimo aggiornamento dei contenuti')
            ->value(Input::post('show_update_date'))
            ->in('0,1')
            ->end();

        if (!empty(Input::post('qualifications'))) {
            foreach (Input::post('qualifications') as $qualification) {
                $this->validator->label('Qualifica')
                    ->value($qualification)
                    ->betweenString(1, 90)
                    ->end();
            }
        }

        $this->validator->label('Mostra Norma associata alla Struttura organizzativa')
            ->value(Input::post('show_regulation_in_structure'))
            ->in('0,1')
            ->end();



        $this->validator->label('Comune')
            ->value(Input::post('address_city'))
            ->required()
            ->betweenString(2, 60)
            ->end();

        $this->validator->label('Indirizzo')
            ->value(Input::post('address_street'))
            ->required()
            ->betweenString(2, 60)
            ->end();

        $this->validator->label('Provincia')
            ->value(Input::post('address_province'))
            ->required()
            ->betweenString(2, 5)
            ->end();

        $this->validator->label('CAP')
            ->value(Input::post('address_zip_code'))
            ->required()
            ->maxLength(6)
            ->minLength(2)
            ->regex('/^([0-9]){5}$/')
            ->end();

        $this->validator->label('Recapito telefonico principale')
            ->value(Input::post('phone'))
            ->required()
            ->maxLength(17, __('max_phone_length', null, 'patos'))
            ->minLength(3, __('min_phone_length', null, 'patos'))
            ->regex('/^([0-9().\-+ ]){3,17}$/', __('phone_error', null, 'patos'))
            ->end();

        $this->validator->label('Responsabile del procedimento di pubblicazione')
            ->value(Input::post('publication_responsible'))
            ->betweenString(5, 40)
            ->end();

        $this->validator->label('Url Privacy')
            ->value(Input::post('privacy_url'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        $this->validator->label('Testo iniziale homepage')
            ->value(Input::post('welcome_text'))
            ->minLength(12)
            ->maxLength(10000)
            ->end();

        $this->validator->label('Testo iniziale homepage')
            ->value(Input::post('welcome_text'))
            ->minLength(12)
            ->maxLength(10000)
            ->end();

        $this->validator->label('Codice di monitoraggio statistiche')
            ->value(Input::post('statistics_tracking_code'))
            ->regex("/<script\b[^>]*>(.*?)<\/script>/is", 'Codice statistiche non valido! Il codice javascript deve essere inserito comprensivo del tag "script"')
            ->end();

        $this->validator->label('Testo accessibilità')
            ->value(htmlEscape(Input::post('accessibility_text')))
            ->minLength(12)
            ->maxLength(10000)
            ->end();

        $senderType = (int) Input::post('sender_type_mailer');

        if ($senderType === 0) {
            $this->validator->label('SMTP - Username')
                ->value(Input::post('smtp_username'))
                ->betweenString(2, 40)
                ->end();

            $this->validator->label('SMTP - From')
                ->value(Input::post('smtp_from'))
                ->isEmail()
                ->betweenString(2, 191)
                ->end();

            $this->validator->label('SMTP - Password')
                ->value(Input::post('smtp_password'))
                ->end();

            $this->validator->label('SMTP - Indirizzo server')
                ->value(Input::post('smtp_host'))
                ->isUri()
                ->betweenString(4, 191)
                ->end();

            $this->validator->label('SMTP - Porta')
                ->value(Input::post('smtp_port'))
                ->isInt()
                ->isNaturalNoZero()
                ->end();

            $this->validator->label('SMTP - SSL')
                ->value(Input::post('smtp_security'))
                ->in('no,ssl,tls')
                ->end();

            $this->validator->label('SMTP - Usa autenticazione')
                ->value(Input::post('show_smtp_auth'))
                ->in('1,2')
                ->end();
        } elseif ($senderType === 1) {
            // SMTP Microsoft 365
            $this->validator->label('Microsoft SMTP - Username')
                ->value(Input::post('ms_smtp_username'))
                ->required()
                ->betweenString(2, 191)
                ->end();

            $this->validator->label('Microsoft SMTP - Password')
                ->value(Input::post('ms_smtp_password'))
                ->end();

            $this->validator->label('Microsoft SMTP - Tenant ID')
                ->value(Input::post('ms_smtp_tenant_id'))
                ->required()
                ->betweenString(2, 191)
                ->end();

            $this->validator->label('Microsoft SMTP - Client ID')
                ->value(Input::post('ms_smtp_client_id'))
                ->required()
                ->betweenString(2, 191)
                ->end();

            $this->validator->label('Microsoft SMTP - Client Secret')
                ->value(Input::post('ms_smtp_client_secret'))
                ->end();

        } elseif ($senderType === 2) {
            // Microsoft Azure Communication Services (ACS)
            $this->validator->label('ACS - Sender Email')
                ->value(Input::post('ms_acs_sender_email'))
                ->required()
                ->isEmail()
                ->end();

            $this->validator->label('ACS - Endpoint')
                ->value(Input::post('ms_acs_endpoint'))
                ->required()
                ->isUrl(null, true)
                ->end();

            $this->validator->label('ACS - Access Key')
                ->value(Input::post('ms_acs_access_key'))
                ->end();

        } elseif ($senderType === 3) {
            // Microsoft Graph
            $this->validator->label('Graph - Email')
                ->value(Input::post('ms_graph_email'))
                ->required()
                ->isEmail()
                ->end();

            $this->validator->label('Graph - Tenant ID')
                ->value(Input::post('ms_graph_tenant_id'))
                ->required()
                ->betweenString(2, 191)
                ->end();

            $this->validator->label('Graph - Client ID')
                ->value(Input::post('ms_graph_client_id'))
                ->required()
                ->betweenString(2, 191)
                ->end();

            $this->validator->label('Graph - Client Secret')
                ->value(Input::post('ms_graph_client_secret'))
                ->end();
        }

        $this->validator = $this->validator ?? null;

        // Validazione configurazione PEC (Sempre SMTP Classico)
        $this->validator->label('PEC SMTP - Username')
            ->value(Input::post('pec_smtp_username'))
            ->betweenString(2, 40)
            ->end();

        $this->validator->label('PEC SMTP - From')
            ->value(Input::post('pec_smtp_from'))
            ->isEmail()
            ->betweenString(2, 191)
            ->end();

        $this->validator->label('PEC SMTP - Password')
            ->value(Input::post('pec_smtp_password'))
            ->end();

        $this->validator->label('PEC SMTP - Indirizzo server')
            ->value(Input::post('pec_smtp_host'))
            ->isUri()
            ->betweenString(4, 191)
            ->end();

        $this->validator->label('PEC SMTP - Porta')
            ->value(Input::post('pec_smtp_port'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('PEC SMTP - SSL')
            ->value(Input::post('pec_smtp_security'))
            ->in('no,ssl,tls')
            ->end();

        $this->validator->label('PEC SMTP - Usa autenticazione')
            ->value(Input::post('pec_show_smtp_auth'))
            ->in('1,2')
            ->end();

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        foreach (config('field_global_social', null, 'app') as $key => $item) {

            $this->validator->label($item['label'])
                ->value(Input::post($key))
                ->isUrl(null, true)
                ->end();
        }

        $this->validator = $this->validator ?? null;

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @return array
     * @throws Exception
     */
    public function validateSMTP(): array
    {
        //        $this->validator->verifyToken()
        //            ->end();

        $this->validator->label('SMTP - Username')
            ->value(Input::post('smtp_username'))
            //            ->required()
            ->betweenString(2, 50)
            ->end();

        /*
         * Il mittente serve a prescindere dall'autenticazione, che riguarda l'accesso al
         * server e non l'intestazione del messaggio: senza uno username in forma di
         * indirizzo email il campo "From" diventa l'unica fonte possibile. La richiesta
         * vale solo per chi sta effettivamente configurando questa sezione, cioe' quando
         * almeno un parametro e' compilato.
         */
        $smtpSezioneCompilata = normalizeSmtpValue((string)Input::post('smtp_username')) !== ''
            || normalizeSmtpValue((string)Input::post('smtp_password')) !== ''
            || normalizeSmtpHost((string)Input::post('smtp_host')) !== ''
            || normalizeSmtpValue((string)Input::post('smtp_port')) !== '';
        $smtpFromRichiesto = mailFromRichiesto(
            Input::post('smtp_username'),
            Input::post('smtp_from'),
            $smtpSezioneCompilata
        );
        $smtpFromValidator = $this->validator->label('SMTP - From')
            ->value(Input::post('smtp_from'));

        if ($smtpFromRichiesto) {
            $smtpFromValidator->required(
                'Il campo "SMTP - From" e\' obbligatorio quando lo username non e\' un indirizzo '
                . 'email utilizzabile come mittente: senza di esso le notifiche non potrebbero '
                . 'essere inviate.'
            );
        }

        $smtpFromValidator
            ->isEmail()
            ->betweenString(2, 191)
            ->end();

        $this->validator->label('SMTP - Password')
            ->value(Input::post('smtp_password'))
            //            ->required()
            ->betweenString(2, 32)
            ->end();

        $this->validator->label('SMTP - Indirizzo server')
            ->value(Input::post('smtp_host'))
            ->required()
            ->betweenString(2, 40)
            ->end();

        $this->validator->label('SMTP - Porta')
            ->value(Input::post('smtp_port'))
            ->required()
            ->isInt()
            ->end();

        $this->validator->label('SMTP - SSL')
            ->value(Input::post('smtp_security'))
            //            ->required()
            ->in('no,ssl,tls')
            ->end();

        $this->validator->label('SMTP - Usa autenticazione')
            ->value(Input::post('smtp_auth'))
            //            ->required()
            ->isInt()
            ->in('1,2')
            ->end();


        $this->validator->label('Email di test')
            ->value(Input::post('email'))
            ->required()
            ->isEmail()
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo per la validazione dei campi del form PEC
     *
     * @return array
     * @throws Exception
     */
    public function validatePecSMTP(): array
    {
        $this->validator->label('PEC SMTP - Username')
            ->value(Input::post('pec_smtp_username'))
            ->betweenString(2, 50)
            ->end();

        /*
         * Il mittente serve a prescindere dall'autenticazione, che riguarda l'accesso al
         * server e non l'intestazione del messaggio: senza uno username in forma di
         * indirizzo email il campo "From" diventa l'unica fonte possibile. La richiesta
         * vale solo per chi sta effettivamente configurando questa sezione, cioe' quando
         * almeno un parametro e' compilato.
         */
        $pecSezioneCompilata = normalizeSmtpValue((string)Input::post('pec_smtp_username')) !== ''
            || normalizeSmtpValue((string)Input::post('pec_smtp_password')) !== ''
            || normalizeSmtpHost((string)Input::post('pec_smtp_host')) !== ''
            || normalizeSmtpValue((string)Input::post('pec_smtp_port')) !== '';
        $pecFromRichiesto = mailFromRichiesto(
            Input::post('pec_smtp_username'),
            Input::post('pec_smtp_from'),
            $pecSezioneCompilata
        );
        $pecFromValidator = $this->validator->label('PEC SMTP - From')
            ->value(Input::post('pec_smtp_from'));

        if ($pecFromRichiesto) {
            $pecFromValidator->required(
                'Il campo "PEC SMTP - From" e\' obbligatorio quando lo username non e\' un indirizzo '
                . 'email utilizzabile come mittente: senza di esso le comunicazioni non '
                . 'potrebbero essere inviate.'
            );
        }

        $pecFromValidator
            ->isEmail()
            ->betweenString(2, 191)
            ->end();

        $this->validator->label('PEC SMTP - Password')
            ->value(Input::post('pec_smtp_password'))
            ->betweenString(2, 32)
            ->end();

        $this->validator->label('PEC SMTP - Indirizzo server')
            ->value(Input::post('pec_smtp_host'))
            ->required()
            ->betweenString(2, 40)
            ->end();

        $this->validator->label('PEC SMTP - Porta')
            ->value(Input::post('pec_smtp_port'))
            ->required()
            ->isInt()
            ->end();

        $this->validator->label('PEC SMTP - SSL')
            ->value(Input::post('pec_smtp_security'))
            ->in('no,SSL,TLS')
            ->end();

        $this->validator->label('PEC SMTP - Usa autenticazione')
            ->value(Input::post('pec_smtp_auth'))
            ->isInt()
            ->in('1,2')
            ->end();

        $this->validator->label('Email di test')
            ->value(Input::post('email'))
            ->required()
            ->isEmail()
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validatore per la richiesta di storage di nuovi links personalizzati
     *
     * @param bool $validateId
     * @return array
     * @throws Exception
     */
    public function storageLinks(bool $validateId = false): array
    {
        // ID ente
        $instiutionId = !empty(Input::post('institution_id'))
            ? Input::post('institution_id')
            : checkAlternativeInstitutionId();

        $this->validator->label('titolo del collegamento ipertestuale')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(2, 80)
            ->end();

        $this->validator->label('url del collegamento ipertestuale')
            ->value(Input::post('url'))
            ->required()
            ->isUrl(null, true)
            ->end();

        $this->validator->label('azione del collegamento ipertestuale')
            ->value(Input::post('action'))
            ->required()
            ->in('insert,update,delete,sort')
            ->end();

        $this->validator->label('tipologia del collegamento ipertestuale')
            ->value(Input::post('type'))
            ->required()
            ->in('header,footer')
            ->end();

        if (in_array(Input::post('action'), ['update', 'sort']) || $validateId === true) {

            $this->validator->label('ID del collegamento ipertestuale')
                ->value(Input::post('id'))
                ->required()
                ->isNaturalNoZero()
                ->add(function () use ($instiutionId) {

                    $query = InstitutionLinksModel::where('id', '=', Input::post('id'))
                        ->where('institution_id', $instiutionId)
                        ->first();

                    if (empty($query)) {

                        return ['error' => 'Non hai i permessi oppure il record non esiste'];
                    } else {

                        Registry::set('___institution_link_', $query->toArray());
                    }

                    return null;
                })
                ->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validatore per la richiesta di eliminazione dei links personalizzati
     *
     * @return array
     * @throws Exception
     */
    public function deleteLinks(): array
    {
        // ID ente
        $instiutionId = !empty(Input::get('institution_id'))
            ? Input::get('institution_id')
            : checkAlternativeInstitutionId();

        $this->validator->label('ID del collegamento ipertestuale')
            ->value(Input::get('id'))
            ->required()
            ->isNaturalNoZero()
            ->add(function () use ($instiutionId) {

                $query = InstitutionLinksModel::where('id', '=', Input::get('id'))
                    ->where('institution_id', $instiutionId)
                    ->first();

                if (empty($query)) {

                    return ['error' => 'Non hai i permessi oppure il record non esiste'];
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
     * Validazione per la chiamata di modifica di un custom link dell'ente
     *
     * @return array|string[]
     * @throws Exception
     */
    public function editLinks(): array
    {
        // ID ente
        $instiutionId = !empty(Input::get('institution_id'))
            ? Input::get('institution_id')
            : checkAlternativeInstitutionId();

        $this->validator->label('id del collegamento ipertestuale')
            ->value(Input::get('id'))
            ->required()
            ->isNaturalNoZero()
            ->add(function () use ($instiutionId) {

                $query = InstitutionLinksModel::where('id', '=', Input::get('id'))
                    ->where('institution_id', $instiutionId)
                    ->first();

                if (empty($query)) {

                    return ['error' => 'Non hai i permessi oppure il record non esiste'];
                } else {

                    Registry::set('___institution_link_', $query->toArray());
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
     * Validazione in caso di ordinamento dei link
     * @return array|string[]
     * @throws Exception
     */
    public function sortLinks(): array
    {
        // ID ente
        $instiutionId = !empty(Input::get('institution_id'))
            ? Input::get('institution_id')
            : checkAlternativeInstitutionId();

        $this->validator->label('Tipologia spostamento')
            ->value(Input::get('direction'))
            ->required()
            ->in('up,down')
            ->end();

        $this->validator->label('numero posizione link')
            ->value(Input::get('sort_id'))
            ->required()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('blocco link')
            ->value(Input::get('position'))
            ->required()
            ->in('header,footer')
            ->end();

        $this->validator->label('identificativo link')
            ->value(Input::get('id'))
            ->required()
            ->isNaturalNoZero()
            ->add(function () use ($instiutionId) {

                $query = InstitutionLinksModel::where('id', '=', Input::get('id'))
                    ->where('sort', Input::get('sort_id'))
                    ->where('position', Input::get('position'))
                    ->where('institution_id', $instiutionId)
                    ->first();

                if (empty($query)) {

                    return ['error' => 'Non hai i permessi oppure il record non esiste'];
                } else {

                    Registry::set('___institution_link_', $query->toArray());
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
     * Validatore dell'eliminazione multipla
     *
     * @return array
     * @throws Exception
     */
    public function multipleSelection(): array
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

            if ($isError === true) {

                $institution = InstitutionsModel::select(['id', 'full_name_institution', 'short_institution_name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($institution === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $institution);
                }
            }

            if (!$isError) {

                return ['error' => __('no_permits', null, 'patos')];
            }

            return null;
        });

        $this->validator->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validazione richiesta get dei custom links dell'ente
     * @return array
     * @throws Exception
     */
    public function validateListCustomLinks(): array
    {
        $this->validator->label('Tipologia')
            ->value(Input::get('type'))
            ->required()
            ->in('header,footer')
            ->end();

        $this->validator->label('Richiesta ajax')
            ->value(Input::isAjax())
            ->required()
            ->add(function () {

                $args = func_get_args();

                if (!$args) {

                    return ['error' => 'No ajax request'];
                }

                return null;
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
