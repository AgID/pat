<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

use Exception;
use Model\InstitutionLinksModel;
use Model\InstitutionsModel;
use System\Input;
use System\Registry;
use System\Validator;

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

        if (isSuperAdmin(true) && (uri()->segment('4') || uri()->segment('3') == 'create' || uri()->segment('3') == 'store')) {
            $this->validator->label('Nome breve ente')
                ->value(Input::post('short_institution_name'))
                ->required()
                ->betweenString(2, 50)
                ->end();
        }

        $this->validator->label('Tipo Ente')
            ->value(Input::post('institution_type_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Limite richieste per minuto chiamate API')
            ->value(Input::post('limits_call_api'))
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

        $this->validator->label('Indirizzo email normale')
            ->value(Input::post('email_address'))
            ->required()
            ->isEmail()
            ->add(function () {
                $emailExist = InstitutionsModel::where('id', '!=', Input::post('id'))
                    ->where('email_address', Input::post('email_address'))->first();
                if (!empty($emailExist)) {
                    return ['error' => __('email_exist', null, 'patos')];
                }
                return null;
            })
            ->end();

        $this->validator->label('Indirizzo email certificata')
            ->value(Input::post('certified_email_address'))
            ->required()
            ->isEmail()
            ->add(function () {
                $pecExist = InstitutionsModel::where('id', '!=', Input::post('id'))
                    ->where('certified_email_address', Input::post('certified_email_address'))->first();
                if (!empty($pecExist)) {
                    return ['error' => __('certified_email_exist', null, 'patos')];
                }
                return null;
            })
            ->end();

        $this->validator->label('Ente di appartenenza')
            ->value(Input::post('certified_email_address'))
            ->betweenString(5, 60)
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

        $this->validator->label('Albo Online - ID Ente')
            ->value(Input::post('online_register_id'))
            ->isInt()
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

        $this->validator->label('Visualizzazione tabellare degli Organi di ind. politico')
            ->value(Input::post('tabular_display_org_ind_pol'))
            ->in('0,1')
            ->end();

        $this->validator->label('Mostra la data di ultimo aggiornamento dei contenuti')
            ->value(Input::post('show_update_date'))
            ->in('0,1')
            ->end();

        $this->validator->label('Mostra Norma associata alla Struttura organizzativa')
            ->value(Input::post('show_regulation_in_structure'))
            ->in('0,1')
            ->end();

        if(isSuperAdmin()) {
            $this->validator->label('Url portale PAT')
                ->value(Input::post('trasparenza_urls'))
                ->required()
                ->isUrl(null, true)
                ->betweenString(5, 200)
                ->end();
        }

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

        $this->validator->label('Testo nel footer')
            ->value(Input::post('footer_text'))
            ->minLength(12)
            ->maxLength(1000)
            ->end();

        $this->validator->label('Testo accessibilità')
            ->value(htmlEscape(Input::post('accessibility_text')))
            ->minLength(12)
            ->maxLength(1000)
            ->end();

        $this->validator->label('SMTP - Username')
            ->value(Input::post('smtp_username'))
            ->betweenString(2, 40)
            ->end();

        $this->validator->label('SMTP - Password')
            ->value(Input::post('smtp_password'))
            ->add(function () {
                return checkPassword(Input::post('smtp_password'));
            })
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
            ->in('no,SSL,TLS')
            ->end();

        $this->validator->label('SMTP - Usa autenticazione')
            ->value(Input::post('show_smtp_auth'))
            ->in('1,2')
            ->end();

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
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('SMTP - Username')
            ->value(Input::post('smtp_user'))
            ->required()
            ->betweenString(2, 50)
            ->end();

        $this->validator->label('SMTP - Password')
            ->value(Input::post('smtp_pass'))
            ->required()
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
            ->required()
            ->in('no,SSL,TLS')
            ->end();

        $this->validator->label('SMTP - Usa autenticazione')
            ->value(Input::post('smtp_auth'))
            ->required()
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

        if (isSuperAdmin(true) && checkAlternativeInstitutionId() == 0 && empty($instiutionId)) {

            return ['error' => 'Seleziona un ente prima di compiere qualsiasi operazione'];
        } else {

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

        if (isSuperAdmin(true) && checkAlternativeInstitutionId() == 0 && empty($instiutionId)) {

            return ['error' => 'Seleziona un ente prima di compiere qualsiasi operazione'];
        } else {

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
        }

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

        if (isSuperAdmin(true) && checkAlternativeInstitutionId() == 0 && empty($instiutionId)) {

            return ['error' => 'Seleziona un ente prima di compiere qualsiasi operazione'];
        } else {

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
        }

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
