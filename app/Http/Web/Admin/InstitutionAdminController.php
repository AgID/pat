<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\File;
use Helpers\Validators\InstitutionValidator;
use Model\InstitutionLinksModel;
use Model\InstitutionsModel;
use System\Email;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Registry;
use System\Token;
use System\Uploads;
use System\View;

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
        $noticeActs = [];
        $registerInfo = [];
        $ckb = [];
        $data['institution'] = InstitutionsModel::where('id', checkAlternativeInstitutionId())
            ->first()
            ->toArray();

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

        $validator = new InstitutionValidator();
        $check = $validator->validateSMTP();

        if ($check['is_success']) {

            $info = patOsInstituteInfo(['full_name_institution']);
            $config = [];
            $config['smtp_user'] = strip_tags(Input::post('smtp_username', true));
            $config['smtp_pass'] = strip_tags(Input::post('smtp_password', true));
            $config['smtp_host'] = strip_tags(Input::post('smtp_host', true));
            $config['smtp_port'] = strip_tags(Input::post('smtp_port', true));
            if (Input::post('smtp_security') != 'no') {
                $config['smtp_security'] = strip_tags(Input::post('smtp_security', true));
            }

            $configs = patOsConfigMail(true, $config);

            $email = new Email($configs);
            $send = @$email->from(strip_tags(Input::post('smtp_user', true)))
                ->to(strip_tags(Input::post('email', true)))
                ->set_newline("\r\n")
                ->subject('Test del server smtp - ' . $info['full_name_institution'] . ' - Pat OS')
                ->message('Test configurazione smtp')
                ->send();

            if ($send === true) {

                $json->set('message', 'Il test ha dato esito POSITIVO. E-mail inviata con successo.');

            } else {

                $code = $json->bad();
                $json->error('error', 'Il test ha dato esito NEGATIVO. Errore invio e-mail.');
                Log::danger('[ ENTE:' . checkAlternativeInstitutionId() . '] ' . $email->print_debugger());

            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

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
        $this->acl->setRoute('update');

        $hasError = false;
        $doUpload = null;
        $doUploadFavicon = null;
        $json = new JsonResponse();
        $validator = new InstitutionValidator();
        $code = $json->success();
        $check = $validator->check();

        // Controllo se la validazione del form è avvenuta con successo
        if ($check['is_success']) {

            $institutionId = !empty(Input::post('id')) ? (int)Input::post('id') : checkAlternativeInstitutionId();
            $institution = InstitutionsModel::find($institutionId);
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            // Controllo se è da aggiornare il file del logo
            if (filesUploaded('simple_logo_file') === true) {

                $settings = [
                    'field' => 'simple_logo_file',
                    'dir' => instituteDir($institution->short_institution_name),
                    'allowed_types' => 'png|gif|jpeg|jpg',
                ];

                // Carico il nuovo file del logo Ente
                $doUpload = $this->doUpload($settings);
                $hasError = (bool)$doUpload['success'];

                if (!$hasError) {

                    // Se esiste elimino il vecchio file logo dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->simple_logo_file)) {

                        File::delete(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->simple_logo_file);

                    }

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
                $hasError = (bool)$doUploadFavicon['success'];

                if (!$hasError) {

                    // Se esiste elimino il vecchio file logo dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->favicon_file)) {

                        File::delete(MEDIA_PATH . instituteDir($institution->short_institution_name) . '/assets/images/' . $institution->favicon_file);

                    }

                }

            }

            if (!$hasError) {

                $data = [
                    'id_creator' => $getIdentity['id'],
                    'full_name_institution' => strip_tags(Input::post('full_name_institution', true)),
                    'vat' => strip_tags(Input::post('vat', true)),
                    'email_address' => strip_tags(Input::post('email_address', true)),
                    'certified_email_address' => strip_tags(Input::post('certified_email_address', true)),
                    'top_level_institution_name' => strip_tags(Input::post('top_level_institution_name', true)),
                    'top_level_institution_url' => strip_tags(Input::post('top_level_institution_url', true)),
                    'institutional_website_url' => strip_tags(Input::post('institutional_website_url', true)),
                    'trasp_responsible_user_id' => setDefaultData((int)strip_tags(Input::post('trasp_responsible_user_id', true)), null, ['', 0]),
                    'simple_logo_file' => !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : $institution->simple_logo_file,
                    'favicon_file' => !empty($doUploadFavicon['data']['file_name']) ? $doUploadFavicon['data']['file_name'] : $institution->favicon_file,
                    'bulletin_board_url' => strip_tags(Input::post('bulletin_board_url', true)),
                    'tabular_display_org_ind_pol' => setDefaultData(strip_tags(Input::post('tabular_display_org_ind_pol', true)), null, ['']),
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
                    'smtp_user' => strip_tags(Input::post('smtp_username', true)),
                    'smtp_pass' => setDefaultData(strip_tags(Input::post('smtp_password', true)), $institution->smtp_pass, ['']),
                    'smtp_host' => strip_tags(Input::post('smtp_host', true)),
                    'smtp_port' => strip_tags(Input::post('smtp_port', true)),
                    'smtp_security' => setDefaultData(strip_tags(Input::post('smtp_security', true)), null, ['']),
                    'show_smtp_auth' => setDefaultData(strip_tags(Input::post('show_smtp_auth', true)), null, ['']),
                    'statistics_tracking_code' => Input::post('statistics_tracking_code'),
                    'limits_call_api' => (int) Input::post('limits_call_api'),
                    'expiration_date' => date('Y-m-d H:i:s', strtotime('+1 year'))
                ];

                // Solo il super admin può modificare il tipo ente
                if (isSuperAdmin(true) && (uri()->segment('4') || uri()->segment('3') == 'create')) {
                    $data['institution_type_id'] = setDefaultData(Input::post('institution_type_id'), null, ['', null]);
                    $data['trasparenza_urls'] = strip_tags(Input::post('trasparenza_urls', true));
                }

                // Update dell'ente
                $update = InstitutionsModel::where('id', '=', $institutionId)->update($data);

                if ($update) {

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Modifica Ente',
                        'description' => 'Modifica Ente "' . $institution->full_name_institution . '" con ID (' . $institutionId . ')',
                        'request_post' => [
                            'post' => @$_POST,
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                    ]);

                    $json->set('message', sprintf(__('success_edit', null, 'patos'), 'Ente '));

                } else {

                    $code = $json->bad();
                    $json->error('error', __('tmp_error', null, 'patos'));

                }

            } else {

                $code = $json->bad();
                $json->error('error', $doUpload['data']);

            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Storage per il links personalizzati di un ente
     *
     * @return void
     * @throws Exception
     * @url /admin/institution/link/save.html
     * @method AJAX
     */
    public function storeCustomLinks(): void
    {
        $this->acl->setRoute('update');
        $hasError = false;
        $json = new JsonResponse();
        $validator = new InstitutionValidator();
        $code = $json->success();

        /**
         * Storage del record
         */
        if (Input::post('action') == 'insert') {

            $check = $validator->storageLinks();
            $hasError = true;

            if ($check['is_success']) {

                $institutionId = !empty(Input::post('institution_id'))
                    ? (int)Input::post('institution_id')
                    : checkAlternativeInstitutionId();

                $count = InstitutionLinksModel::where('institution_id', $institutionId)
                    ->where('position', strip_tags(Input::post('type', true)))
                    ->count();

                InstitutionLinksModel::create([
                    'title' => strip_tags(Input::post('name', true)),
                    'position' => strip_tags(Input::post('type', true)),
                    'url' => strip_tags(Input::post('url', true)),
                    'institution_id' => $institutionId,
                    'sort' => $count + 1,
                ]);

                // Storage Activity log
                ActivityLog::create([
                    'action' => 'Creazione nuovo link',
                    'description' => 'Creazione di un nuovo link denominato "' . strip_tags((string)Input::post('name')) . '" per l\'ente con ID (' . $institutionId . ')',
                    'request_post' => [
                        'post' => Input::post(),
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                ]);
                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('action', 'Insert');

            } else {

                $code = $json->bad();
                $json->error('error', $check['errors']);
            }

        }

        /**
         * Update del record
         */
        if (Input::post('action') == 'update') {

            $check = $validator->storageLinks(true);

            if ($check['is_success']) {

                InstitutionLinksModel::where('id', (int)Input::post('id'))
                    ->update([
                        'title' => strip_tags(Input::post('name', true)),
                        'url' => strip_tags(Input::post('url', true)),
                    ]);

                $json->set('action', 'update');
                $json->set('message', 'operazione avvenuta con successo');

            } else {

                $code = $json->bad();
                $json->error('error', $check['errors']);
            }
        }

        /**
         * Disattivazione del record
         */
        if (Input::get('action') == 'delete') {

            $check = $validator->deleteLinks();
            $hasError = true;

            if ($check['is_success']) {

                InstitutionLinksModel::where('id', Input::get('id'))
                    ->update([
                        'deleted_at' => date('Y-m-d h:i:s'),
                    ]);

                $json->set('action', 'delete');
                $json->set('message', 'operazione avvenuta con successo');

            } else {

                $code = $json->bad();
                $json->error('error', $check['errors']);
            }

        }

        /**
         * Edit del record
         */
        if (Input::get('action') == 'edit') {

            $check = $validator->editLinks();

            if ($check['is_success']) {

                $json->set('record', Registry::get('___institution_link_'));

            } else {

                $code = $json->bad();
                $json->error('error', $check['errors']);
            }

        }

        /**
         * Ordinamento dei records
         */
        if (Input::get('action') == 'sort') {

            $check = $validator->sortLinks();

            if ($check['is_success']) {

                $institutionId = !empty(Input::get('institution_id'))
                    ? Input::get('institution_id')
                    : checkAlternativeInstitutionId();

                //Setto l'ordinamento(se da spostare su o giu)
                if (Input::get('direction') === 'up') {

                    $sort = 'DESC';
                    $operator = '<';

                } else {

                    $sort = 'ASC';
                    $operator = '>';

                }

                // Recupero il record con cui scambiare l'ordinamento
                $querySwitch = InstitutionLinksModel::where('institution_id', $institutionId)
                    ->whereNull('deleted_at')
                    ->where('sort', $operator, Input::get('sort_id'))
                    ->where('position', Input::get('position'))
                    ->orderBy('sort', $sort)
                    ->first();

                if (!empty($querySwitch)) {

                    $switch = $querySwitch->toArray();

                    // Recupero posizione del record corrente da scambiare
                    $currentRecord = Registry::get('___institution_link_');

                    // Update record con cui scambiare l'ordinamento
                    InstitutionLinksModel::where('id', $switch['id'])
                        ->update([
                            'sort' => $currentRecord['sort']
                        ]);

                    // Update posizione del record corrente da scambiare
                    InstitutionLinksModel::where('id', $currentRecord['id'])
                        ->update([
                            'sort' => $switch['sort']
                        ]);

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Modifica ordinamento links ',
                        'description' => 'Modifica ordinamento[' . strip_tags((string)Input::get('direction')) . '] links "' . $currentRecord['title'] . '" con ID "' . $currentRecord['id']
                            . '" riuscita con successo. Ordinamento invertito con il link "' . $switch['title'] . ' ed ID "' . $switch['id'] . '"',
                        'request_post' => [
                            'post' => Input::post(),
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                    ]);

                    $json->set('message', 'Operazione avvenuta correttamente');

                }


            } else {

                $code = $json->bad();
                $json->error('error', $check['errors']);

            }

        }

        /**
         * Operazione avvenuta con successo.
         */
        if ($hasError === true) {

            $json->set('message', 'Operazione avvenuta con success');

        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per recuperare la lista dei links customizzati
     *
     * @return void
     * @throws Exception
     * @url /admin/institution/link/list
     * @method AJAX
     */
    function asyncListCustomLinks(): void
    {
        $this->acl->setRoute('update');

        $json = new JsonResponse();
        $validator = new InstitutionValidator();
        $code = $json->success();
        $institutionId = !empty(Input::get('institution_id')) ? Input::get('institution_id') : null;

        $check = $validator->validateListCustomLinks();

        if ($check['is_success'] && (!empty($institutionId) || Input::get('storage_type') == 'insert')) {

            $query = InstitutionLinksModel::select(['*']);
            $query->where('position', Input::get('type'));
            $query->whereNull('deleted_at');

            if (isSuperAdmin(true)) {
                $query->where('institution_id', $institutionId);
            }

            $query->orderby('sort', 'ASC');
            $items = $query->get();

            $data['results'] = !empty($items) ? $items->toArray() : [];
            $data['type'] = Input::get('type');
            $view = View::create('institution/links', $data, 'admin')->render();

            $json->set('message', $view);

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();
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
