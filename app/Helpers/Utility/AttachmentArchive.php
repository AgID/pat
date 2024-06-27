<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\File;
use Model\AttachmentsModel;
use Scope\InstitutionScope;
use System\Input;
use System\Registry;
use System\Uploads;
use System\Utf8;
use System\Validator;
use function Addons\Isweb\Helpers\htmlToArrayErrorsByTagLi;

class AttachmentArchive
{
    private array $config;
    private int $institutionId;
    private string $errors = '';
    private Utf8 $utf8;
    protected $bdncpCat = null;
    protected $bdncpCatHasValidator = false;

    /**
     * @param int|null $institutionId Id dell'Ente
     * @throws Exception
     */
    public function __construct(?int $institutionId = null)
    {
        helper('url');
        $this->utf8 = new Utf8(null);
        $this->institutionId = !empty ($institutionId) && ctype_cntrl($institutionId)
            ? $institutionId
            : PatOsInstituteId();

        $this->config = [
            'upload_path' => config('upload_path', null, 'perms_files'),
            'file_ext_tolower' => config('file_ext_tolower', null, 'perms_files'),
            'encrypt_name' => config('encrypt_name', null, 'perms_files'),
            'remove_spaces' => config('remove_spaces', null, 'perms_files'),
            'max_size' => config('max_size', null, 'perms_files'),
            'allowed_types' => config('allowed_types', null, 'perms_files'),
        ];

    }

    public function setBdncpCat(?string $bdncpCat = null): object
    {
        $this->bdncpCat = $bdncpCat;
        $this->bdncpCatHasValidator = true;

        return $this;
    }

    /**
     * @param string|null $label -  Nome dell'archivio: contest_acts
     * @param int $archiveId - Identificativo del record dell'archivio
     * @param int $institutionId - Identificativo dell'istituto
     * @param int $userId - Identificativo dell'utente
     * @param object|null $model - Il modello sotto forma di oggetto
     * @param string|null $typology - la tipologia : deliberation (per le delibere)
     * @param string $field - Campo del record a cui sono associati gli allegati da mostrare nei log
     * @param null|array $optional - Parametri di default
     * @return array
     * @throws Exception
     */
    public function storageForApi(?string $label, int $archiveId, int $institutionId, int $userId, object|null $model = null, ?string $typology = null, string $field = '', null|array $optional = null): array
    {
        $data = [];
        $data['is_success'] = true;
        $data['errors'] = '';
        $data['data'] = [];

        $this->config['upload_path'] = $this->config['upload_path'] . DIRECTORY_SEPARATOR . $label;

        if (!file_exists($this->config['upload_path'])) {
            mkdir($this->config['upload_path'], 0777, true);
        }

        $validator = new Validator();

        $validator->label('ID')
            ->value($archiveId)
            ->required()
            ->isNaturalNoZero()
            ->add(function () use ($model, $archiveId, $institutionId, $typology) {

                if ($typology !== null) {
                    $query = $model::where('typology', $typology)
                        ->withoutGlobalScopes([InstitutionScope::class])
                        ->where('id', $archiveId)
                        ->where('institution_id', '=', $institutionId)
                        ->first();
                } else {
                    $query = $model->first();
                }

                if ($query === null) {
                    return [
                        'error' => 'Archivio non valido'
                    ];
                }

                return null;
            })
            ->end();

        if ($optional === null && !isset ($optional['omissis'])) {
            $validator->label('Omissis')
                ->value(Input::stream('omissis'))
                ->required()
                ->in('0,1')
                ->end();

        }

        if ($optional === null && !isset ($optional['publish'])) {
            $validator->label('elemento pubblicato')
                ->value(Input::stream('publish'))
                ->required()
                ->in('0,1')
                ->end();
        }

        if ($optional === null && !isset ($optional['label'])) {
            $validator->label('nome etichetta allegato')
                ->value(Input::stream('label'))
                ->required()
                //->maxLength('191')
                ->end();

        }

        if ($optional === null && !isset ($optional['file_name'])) {
            $validator->label('Nome del file')
                ->value(Input::stream('file_name'))
                ->required()
                ->maxLength('191')
                ->end();
        }

        if ($optional === null && !isset ($optional['file_ext'])) {
            $validator->label('Estensione file')
                ->value(Input::stream('file_ext'))
                ->minLength(2)
                ->maxLength(15)
                ->required()
                ->add(function () {

                    $ext = Input::stream('file_ext');
                    if ($ext[0] === '.') {
                        return "l'estensione non deve iniziare con un punto.";
                    }

                    if (!preg_match("/^[a-zA-Z0-9_-]*$/", $ext)) {
                        return [
                            'error' => 'l\'estensione contiene caratteri non validi'
                        ];
                    }

                    return null;
                })
                ->end();
        }

        if ($optional === null && !isset ($optional['category_id'])) {
            $validator->label('nome categoria')
                ->value(Input::stream('category_id'))
                ->isNaturalNozero()
                ->end();
        }

        if ($this->bdncpCatHasValidator !== false) {
            // Le stringhe valide sono le seguenti:
            // _publicDebate
            // _noticeDocuments
            // _equalOpportunitiesAf
            // _localPublicServices
            // _equalOpportunitiesEs
            // _freeContract
            // _emergencyFoster
            // _fosterProcedure
            // _judgingCommission
            // _advisoryBoardTechnical
            $validator->label('Categoria allegati BDNCP')
                ->value($this->bdncpCat)
                ->required()
                ->in($this->getConfigFieldsBdncpCategories())
                ->end();
        }

        if ($validator->isSuccess()) {

            $omissis = ($optional !== null && in_array($optional['omissis'], [0, 1]))
                ? (int) $optional['omissis']
                : Input::stream('omissis', true, true, true, ['int']);

            $publish = ($optional !== null && in_array($optional['publish'], [0, 1]))
                ? (int) $optional['publish']
                : Input::stream('publish', true, true, true, ['int']);

            $labelRequest = ($optional !== null && isset ($optional['label']))
                ? $optional['label']
                : Input::stream('label', true);

            $categoryID = ($optional !== null && isset ($optional['category_id']))
                ? (int) $optional['category_id']
                : Input::stream('category_id', true, true, true, ['int']);

            $fileName = ($optional !== null && isset ($optional['file_name']))
                ? $optional['file_name']
                : Input::stream('file_name', true);


            $fileExt = ($optional !== null && isset ($optional['file_ext']))
                ? $optional['file_ext']
                : Input::stream('file_ext', true);


            $fileBase64 = ($optional !== null && isset ($optional['file_base64']))
                ? $optional['file_base64']
                : Input::stream('file_base64', true);

            $upload = new Uploads($this->config);

            if ($upload->doUploadBase64($fileBase64, $fileName, $fileExt, true)) {

                $data['data'] = $upload->data();

                $count = AttachmentsModel::where('archive_name', '=', $label)
                    ->withoutGlobalScopes([InstitutionScope::class])
                    ->where('archive_id', '=', $archiveId)
                    ->where('institution_id', '=', $institutionId)
                    ->count();

                $dataCreate = [
                    'archive_name' => $label,
                    'sort' => $count + 1,
                    'institution_id' => $institutionId,
                    'cat_id' => $categoryID,
                    'archive_id' => $archiveId,
                    'file_name' => $data['data']['file_name'],
                    'file_type' => $data['data']['file_type'],
                    'file_path' => $data['data']['file_path'],
                    'full_path' => $data['data']['full_path'],
                    'raw_name' => $data['data']['raw_name'],
                    'orig_name' => $this->utf8->cleanString(urlTitle($data['data']['orig_name'])),
                    'client_name' => $data['data']['client_name'],
                    'file_ext' => $data['data']['file_ext'],
                    'file_size' => $data['data']['file_size'],
                    'is_image' => $data['data']['is_image'],
                    'image_width' => $data['data']['image_width'],
                    'image_height' => $data['data']['image_height'],
                    'image_type' => $data['data']['image_type'],
                    'image_size_str' => $data['data']['image_size_str'],
                    'fingerprint' => $data['data']['fingerprint'],
                    'label' => $labelRequest,
                    'active' => $publish,
                    'indexable' => $omissis,
                ];

                if ($this->bdncpCat !== null) {
                    $dataCreate['bdncp_cat'] = $this->bdncpCat;
                }

                $insert = AttachmentsModel::create($dataCreate);

                $archivio = config($label, null, 'archiveConfig')['name'] ?? '';

                ActivityLog::create([
                    'user_id' => $userId,
                    'action' => 'Aggiunta Allegati',
                    'action_type' => 'addObjectInstance',
                    'description' => 'Aggiunta Allegato (ID ' . $insert->id . ') <br>'
                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $archiveId . ')',
                    'request_post' => [
                        'post' => Input::stream(),
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                    'object_id' => 999,
                    'record_id' => $insert->id,
                    'area' => 'object',
                    'platform' => 'all'
                ]);

                $data['insert_id'] = $insert->id;

            } else {

                $data['is_success'] = false;
                $data['errors'] = $upload->displayErrors();
            }

        } else {
            $data['is_success'] = false;
            $data['errors'] = $validator->getErrors();
        }

        return $data;
    }


    public function updateForApi($label, $archiveId, $id, $institutionId, $userId, $model, $typology, $field = '', null|array $optional = null): array
    {
        $data = [];
        $data['is_success'] = true;
        $data['errors'] = '';
        $data['data'] = [];
        $arraValues = [];

        $this->config['upload_path'] = $this->config['upload_path'] . DIRECTORY_SEPARATOR . $label;

        if (!file_exists($this->config['upload_path'])) {
            mkdir($this->config['upload_path'], 0777, true);
        }

        $validator = new Validator();

        $validator->label('identificativo archivio')
            ->value($archiveId)
            ->required()
            ->isNaturalNoZero()
            ->add(function () use ($model, $archiveId, $institutionId, $typology) {

                if ($typology !== null) {
                    $query = $model::where('typology', $typology)
                        ->withoutGlobalScopes([InstitutionScope::class])
                        ->where('id', $archiveId)
                        ->where('institution_id', '=', $institutionId)
                        ->first();
                } else {
                    if (is_bool($model) && $model === true) {
                        $query = true;
                    } else {
                        $query = $model->first();
                    }
                }

                if ($query === null) {
                    return [
                        'error' => 'archivio non trovato'
                    ];
                }

                return null;
            })
            ->end();

        $validator->label('identificativo allegato')
            ->value($id)
            ->required()
            ->isNaturalNoZero()
            ->add(function () use ($label, $id, $institutionId, $archiveId) {
                $query = AttachmentsModel::where('id', $id)
                    ->withoutGlobalScopes([InstitutionScope::class])
                    ->where('id', $id)
                    ->where('archive_name', $label)
                    ->where('archive_id', $archiveId)
                    ->where('institution_id', '=', $institutionId)
                    ->first();

                if ($query === null) {
                    return [
                        'error' => 'Identificativo allegato non valido'
                    ];
                }
                Registry::set('___model_update_for_api_attach', $query);
                return null;
            })
            ->end();

        if ($optional === null && !isset ($optional['omissis'])) {
            $validator->label('Omissis')
                ->value(Input::stream('omissis'))
                ->in('0,1')
                ->end();
        }

        if ($optional === null && !isset ($optional['publish'])) {
            $validator->label('elemento pubblicato')
                ->value(Input::stream('publish'))
                ->in('0,1')
                ->end();
        }

        if ($optional === null && !isset ($optional['label'])) {
            $validator->label('nome etichetta allegato')
                ->value(Input::stream('label'))
                //->maxLength('191')
                ->end();
        }

        if ($optional === null && !isset ($optional['file_name'])) {
            $validator->label('Nome del file')
                ->value(Input::stream('file_name'))
                ->maxLength('191')
                ->end();
        }

        if ($optional === null && !isset ($optional['file_ext'])) {
            $validator->label('Estensione file')
                ->value(Input::stream('file_ext'))
                ->minLength(2)
                ->maxLength(15)
                ->add(function () {

                    $ext = Input::stream('file_ext');
                    if ($ext[0] === '.') {
                        return "l'estensione non deve iniziare con un punto.";
                    }

                    if (!preg_match("/^[a-zA-Z0-9_-]*$/", $ext)) {
                        return [
                            'error' => 'l\'estensione contiene caratteri non validi'
                        ];
                    }

                    return null;
                })
                ->end();
        }

        if ($optional === null && !isset ($optional['category_id'])) {
            $validator->label('nome categoria')
                ->value(Input::stream('category_id'))
                ->isNaturalNozero()
                ->end();
        }

        if ($this->bdncpCatHasValidator !== false) {
            // Le stringhe valide sono le seguenti:
            // _publicDebate
            // _noticeDocuments
            // _equalOpportunitiesAf
            // _localPublicServices
            // _equalOpportunitiesEs
            // _freeContract
            // _emergencyFoster
            // _fosterProcedure
            // _judgingCommission
            // _advisoryBoardTechnical
            $validator->label('Categoria allegati BDNCP')
                ->value($this->bdncpCat)
                ->required()
                ->in($this->getConfigFieldsBdncpCategories())
                ->end();
        }

        if ($validator->isSuccess()) {

            $model = Registry::get('___model_update_for_api_attach');

            //Recupero il nome dell'archivio per i log
            $archivio = config($label, null, 'archiveConfig')['name'] ?? '';

            if ($optional !== null && in_array($optional['omissis'], [0, 1])) {
                $omissis = (int) $optional['omissis'];
            } else {
                $omissis = Input::stream('omissis')
                    ? Input::stream('omissis', true, true, true, ['int'])
                    : $model->indexable;
            }

            if ($optional !== null && in_array($optional['publish'], [0, 1])) {
                $active = (int) $optional['publish'];
            } else {
                $active = Input::stream('publish')
                    ? Input::stream('publish', true, true, true, ['int'])
                    : $model->active;
            }

            if ($optional !== null && isset ($optional['label'])) {
                $labelRequest = $optional['label'];
            } else {
                $labelRequest = Input::stream('label')
                    ? Input::stream('label', true)
                    : $model->label;
            }
            if ($optional !== null && isset ($optional['category_id'])) {
                $categoryID = $optional['category_id'];
            } else {
                $categoryID = Input::stream('category_id')
                    ? Input::stream('category_id', true, true, true, ['int'])
                    : $model->cat_id;
            }
            if ($optional !== null && isset ($optional['file_name'])) {
                $fileName = $optional['file_name'];
            } else {
                $fileName = Input::stream('file_name') != null
                    ? Input::stream('file_name', true)
                    : $model->file_name;
            }

            if ($optional !== null && isset ($optional['file_ext'])) {
                $fileExt = $optional['file_ext'];
            } else {
                $fileExt = Input::stream('file_ext')
                    ? Input::stream('file_ext', true)
                    : $model->file_ext;
            }

            if ($optional !== null && isset ($optional['sort'])) {
                $sort = $optional['sort'];
            } else {
                $sort = Input::stream('sort') != null
                    ? Input::stream('sort', true, true, true, ['int'])
                    : $model->sort;
            }

            if ($this->bdncpCat !== null) {
                $arraValues['bdncp_cat'] = $this->bdncpCat;
            }

            $arraValues['indexable'] = $omissis;
            $arraValues['active'] = $active;
            $arraValues['label'] = $labelRequest;
            $arraValues['cat_id'] = $categoryID;
            $arraValues['file_name'] = $fileName;
            $arraValues['file_ext'] = $fileExt;
            $arraValues['sort'] = $sort;

            $fileBase64 = Input::stream('file_base64');

            if ($fileBase64 !== null) {

                $upload = new Uploads($this->config);

                if ($upload->doUploadBase64($fileBase64, $fileName, $fileExt, true)) {

                    $data['data'] = $upload->data();

                    $arraValues['file_name'] = $data['data']['file_name'];
                    $arraValues['file_type'] = $data['data']['file_type'];
                    $arraValues['file_path'] = $data['data']['file_path'];
                    $arraValues['full_path'] = $data['data']['full_path'];
                    $arraValues['raw_name'] = $data['data']['raw_name'];
                    $arraValues['orig_name'] = $data['data']['orig_name'];
                    $arraValues['client_name'] = $data['data']['client_name'];
                    $arraValues['file_ext'] = $data['data']['file_ext'];
                    $arraValues['file_size'] = $data['data']['file_size'];
                    $arraValues['is_image'] = $data['data']['is_image'];
                    $arraValues['image_width'] = $data['data']['image_width'];
                    $arraValues['image_height'] = $data['data']['image_height'];
                    $arraValues['image_type'] = $data['data']['image_type'];
                    $arraValues['image_size_str'] = $data['data']['image_size_str'];
                    $arraValues['fingerprint'] = $data['data']['fingerprint'];

                } else {
                    $data['is_success'] = false;
                    $data['errors'] = htmlToArrayErrorsByTagLi($upload->displayErrors());
                }
            }

            if ($data['is_success']) {

                AttachmentsModel::where('id', '=', $id)->update($arraValues);

                $logValues = [
                    'user_id' => $userId,
                    'action' => 'Eliminazione Allegato',
                    'action_type' => 'deleteObjectInstance',
                    'request_post' => [
                        'post' => Input::stream(),
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                    'object_id' => 999,
                    'area' => 'object',
                    'platform' => 'all',
                    'recordId' => $archiveId,
                    'description' => 'Eliminazione Allegato (ID ' . $archiveId . ') <br>'
                        . 'Allegati per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                ];

                $data['insert_id'] = $id;
                // Storage Activity log
                ActivityLog::create($logValues);

            }

        } else {
            $data['is_success'] = false;
            $data['errors'] = $validator->getErrors();
        }

        return $data;
    }

    public function deleteForApi($id, $archiveName, array $institution): array
    {
        $data = [];
        $data['is_success'] = true;
        $data['errors'] = '';
        $data['data'] = [];

        $shortInstitutionName = $institution['short_institution_name'];

        $validator = new Validator();
        $validator->label('identificatico archivio')
            ->value($id)
            ->required()
            ->isNaturalNoZero()
            ->end()

            ->label('nome archivio')
            ->value($archiveName)
            ->required()
            ->isAlphaDash()
            ->add(function () use ($id, $archiveName, $institution) {
                $record = AttachmentsModel::where('id', '=', $id)
                    ->withoutGlobalScopes([InstitutionScope::class])
                    ->where('institution_id', '=', $institution['id'])
                    ->where('archive_name', '=', $archiveName)
                    ->first();

                if ($record === null) {
                    return [
                        'error' => 'Allegato non trovato'
                    ];
                }

                Registry::set('___model_delete_for_api_attach', $record);

                return null;

            }, 'Allegato non trovato')
            ->end();


        if ($validator->isSuccess()) {

            $model = Registry::get('___model_delete_for_api_attach');

            $filePath = MEDIA_PATH .
                DIRECTORY_SEPARATOR .
                $shortInstitutionName .
                DIRECTORY_SEPARATOR .
                'object_attachs' .
                DIRECTORY_SEPARATOR .
                $model['archive_name'] .
                DIRECTORY_SEPARATOR .
                $model['file_name'];

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $model->delete();

            $data['is_success'] = true;
            $data['data']['deleted_id'] = $id;

        } else {

            $data['is_success'] = false;
            $data['errors'] = $validator->getErrors();

        }

        return $data;
    }

    /**
     * @description Funzione che effettua lo storage degli allegati nella tabella "attachments"
     * @param string|null $inputFileName Nome del file da salvare
     * @param string|null $label Etichetta del file da salvare
     * @param int|null $id Id del record a cui sono associati gli allegati (nelle varie tabelle personale, strutture...)
     * @param string $field Campo del record a cui sono associati gli allegati da mostrare nei log
     * @param string|null $archive Nome dell'archivio del record a cui sono associati gli allegati(usato per i log)
     * @param int|null $institutionId ID istituto
     * @param int|null $userId id Utente
     * @param bool $hasApi Verifica se la chiamata e di tipo API.
     * @param bool $log Indica se salvare l'azione nei log o meno
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return array
     * @throws Exception
     */
    public function storage(?string $inputFileName = null, ?string $label = null, ?int $id = null, string $field = '', string|null $archive = '', int $institutionId = null, int $userId = null, bool $hasApi = null, bool $log = true, null|string $bdncpCat = ''): array
    {
        $data = [];
        $index = 0;

        if (!empty ($bdncpCat)) {
            $tmp = explode('O__O', $bdncpCat);
            $bdncpCat = $tmp[0];
            $index = (int) $tmp[1];
        }

        if ($index === 0) {
            $this->config['upload_path'] = $this->config['upload_path'] . DIRECTORY_SEPARATOR . $label;
        }

        if (!file_exists($this->config['upload_path'])) {
            mkdir($this->config['upload_path'], 0777, true);
        }

        if ($inputFileName == null && $label == null && $id == null) {

            return $data;
        }

        if (filesUploaded($inputFileName)) {

            if ($userId !== null && $hasApi) {
                // Dati per registrazione ActivityLog e Versioning
                $getIdentity = authPatOs()->getIdentity(['id']);
                $userId = $getIdentity['id'] ?? 1;
            }

            if (\Helpers\FileSystem\File::isMultiUpload($inputFileName)) {

                // Multi upload
                for ($i = 0; $i < count($_FILES[$inputFileName]['name']); $i++) {

                    // Hack multi upload

                    $_FILES['file']['name'] = $_FILES[$inputFileName]['name'][$i];
                    $_FILES['file']['type'] = $_FILES[$inputFileName]['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES[$inputFileName]['error'][$i];
                    $_FILES['file']['size'] = $_FILES[$inputFileName]['size'][$i];

                    // Controllo se sto duplicando un file
                    if (!empty ($_POST['attach_id' . $bdncpCat][$i]) && $_POST['attach_id' . $bdncpCat][$i] !== 'null') {
                        //Per i casi di duplicazione di un elemento
                        duplicateAttach($id, $label, $_POST['attach_id' . $bdncpCat][$i], $i, $this->utf8, $bdncpCat);
                    } else {
                        $upload = new Uploads($this->config);

                        if ($upload->doUpload('file') === true) {

                            $uploadData = $upload->data();

                            $data['success'][] = $uploadData;

                            // Set temp DATA
                            $_POST['temp_label_attach'] = !empty ($_POST['label_attach' . $bdncpCat][$i])
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$i]), true, false))
                                : null;
                            // Set temp DATA
                            $_POST['temp_bdncp_cat'] = !empty ($_POST['bdncp_cat' . $bdncpCat][$i])
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['bdncp_cat' . $bdncpCat][$i]), true, false))
                                : null;
                            $_POST['temp_publish'] = !empty ($_POST['publish' . $bdncpCat]) && (!empty ($_POST['publish' . $bdncpCat][$i]) || $_POST['publish' . $bdncpCat][$i] == 0)
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                                : 1;
                            $_POST['temp_omissis'] = !empty ($_POST['omissis' . $bdncpCat]) && !empty ($_POST['omissis' . $bdncpCat][$i])
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$i]), true, false))
                                : 0;
                            $_POST['temp_category'] = !empty ($_POST['category'][$i])
                                ? $_POST['category'][$i]
                                : 1;

                            $insert = AttachmentsModel::create([
                                'archive_name' => $label,
                                'sort' => $i + 1,
                                'institution_id' => ($institutionId === null) ? checkAlternativeInstitutionId() : $institutionId,
                                'cat_id' => (int) Input::post('temp_category'),
                                'archive_id' => $id,
                                // Insert ID
                                'file_name' => $uploadData['file_name'],
                                'file_type' => $uploadData['file_type'],
                                'file_path' => $uploadData['file_path'],
                                'full_path' => $uploadData['full_path'],
                                'raw_name' => $uploadData['raw_name'],
                                'orig_name' => $this->utf8->cleanString(urlTitle($uploadData['orig_name'])),
                                'client_name' => $uploadData['client_name'],
                                'file_ext' => $uploadData['file_ext'],
                                'file_size' => $uploadData['file_size'],
                                'is_image' => $uploadData['is_image'],
                                'image_width' => $uploadData['image_width'],
                                'image_height' => $uploadData['image_height'],
                                'image_type' => $uploadData['image_type'],
                                'image_size_str' => $uploadData['image_size_str'],
                                'fingerprint' => $uploadData['fingerprint'] ?? null,
                                'label' => !empty (Input::post('temp_label_attach')) ? strip_tags((string) Input::post('temp_label_attach', true)) : 'Allegato',
                                'bdncp_cat' => !empty (Input::post('temp_bdncp_cat')) ? strip_tags((string) Input::post('temp_bdncp_cat', true)) : null,
                                'active' => (int) strip_tags((string) Input::post('temp_publish', true)),
                                'indexable' => (int) strip_tags((string) Input::post('temp_omissis', true)),
                            ]);


                            //Recupero il nome dell'archivio per i log
                            $archivio = config($label, null, 'archiveConfig')['name'] ?? $archive;

                            if ($log) {
                                // Storage Activity log
                                ActivityLog::create([
                                    'user_id' => $userId,
                                    'action' => 'Aggiunta Allegati',
                                    'action_type' => 'addObjectInstance',
                                    'description' => 'Aggiunta Allegato (ID ' . $insert->id . ') <br>'
                                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                                    'request_post' => [
                                        'post' => @$_POST,
                                        'get' => Input::get(),
                                        'server' => Input::server(),
                                    ],
                                    'object_id' => 999,
                                    'record_id' => $insert->id,
                                    'area' => 'object',
                                    'platform' => 'all'
                                ]);
                            }

                        } else {
                            $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' -> ) ' . strip_tags((string) Input::post('temp_label_attach', true)) . ' </li>');
                            $data['error'][] = $error;
                            $this->errors .= $error;
                            unset($error);
                        }
                    }
                }
            } else {

                // upload singolo
                $_FILES['file']['name'] = $_FILES[$inputFileName]['name'];
                $_FILES['file']['type'] = $_FILES[$inputFileName]['type'];
                $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'];
                $_FILES['file']['error'] = $_FILES[$inputFileName]['error'];
                $_FILES['file']['size'] = $_FILES[$inputFileName]['size'];

                //Controllo se sto duplicando un file
                if (!empty ($_POST['attach_id'][0]) && $_POST['attach_id'][0] !== 'null') {
                    //Per i casi di duplicazione di un elemento
                    duplicateAttach($id, $label, $_POST['attach_id'][0], 0, $this->utf8, $bdncpCat);
                } else {
                    $upload = new Uploads($this->config);

                    if ($upload->doUpload('file') === true) {

                        $uploadData = $upload->data();

                        $data['success'][] = $uploadData;

                        // Set temp DATA
                        $_POST['temp_label_attach'] = !empty ($_POST['label_attach'][0])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach'][0]), true, false))
                            : null;
                        $_POST['temp_publish'] = !empty ($_POST['publish']) && (!empty ($_POST['publish'][0]) || $_POST['publish'][0] == 0)
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish'][0]), true, false))
                            : 1;
                        $_POST['temp_omissis'] = !empty ($_POST['omissis']) && !empty ($_POST['omissis'][0])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis'][0]), true, false))
                            : 0;
                        $_POST['temp_category'] = !empty ($_POST['category'][0])
                            ? $_POST['category'][0]
                            : 1;

                        $insert = AttachmentsModel::create([
                            'archive_name' => $label,
                            'sort' => 1,
                            'institution_id' => ($institutionId === null) ? checkAlternativeInstitutionId() : $institutionId,
                            'cat_id' => (int) Input::post('temp_category'),
                            'archive_id' => $id,
                            // Insert ID
                            'file_name' => $uploadData['file_name'],
                            'file_type' => $uploadData['file_type'],
                            'file_path' => $uploadData['file_path'],
                            'full_path' => $uploadData['full_path'],
                            'raw_name' => $uploadData['raw_name'],
                            'orig_name' => $this->utf8->cleanString(urlTitle($uploadData['orig_name'])),
                            'client_name' => $uploadData['client_name'],
                            'file_ext' => $uploadData['file_ext'],
                            'file_size' => $uploadData['file_size'],
                            'is_image' => $uploadData['is_image'],
                            'image_width' => $uploadData['image_width'],
                            'image_height' => $uploadData['image_height'],
                            'image_type' => $uploadData['image_type'],
                            'image_size_str' => $uploadData['image_size_str'],
                            'fingerprint' => $uploadData['fingerprint'],
                            'label' => strip_tags(Input::post('temp_label_attach', true)),
                            'active' => (int) strip_tags((string) Input::post('temp_publish', true)),
                            'indexable' => (int) strip_tags((string) Input::post('temp_omissis', true)),
                        ]);

                        //Recupero il nome dell'archivio per i log
                        $archivio = config($label, null, 'archiveConfig')['name'] ?? $archive;

                        // Storage Activity log
                        ActivityLog::create([
                            'user_id' => $userId,
                            'action' => 'Aggiunta Allegati',
                            'action_type' => 'addObjectInstance',
                            'description' => 'Aggiunta Allegato (ID ' . $insert->id . ') <br>'
                                . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                            'request_post' => [
                                'post' => @$_POST,
                                'get' => Input::get(),
                                'server' => Input::server(),
                            ],
                            'object_id' => 999,
                            'record_id' => $insert->id,
                            'area' => 'object',
                            'platform' => 'all'
                        ]);
                    } else {
                        $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' -> ) ' . strip_tags((string) Input::post('temp_label_attach', true)) . ' </li>');
                        $data['error'][] = $error;
                        $this->errors .= $error;
                        unset($error);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @description Funzione che effettua l'update degli allegati
     * @param string|null $inputFileName Nome del file da aggiornare
     * @param string|null $label Etichetta del file da aggiornare
     * @param int|null $id Id del record a cui sono associati gli allegati (nelle varie tabelle personale, strutture...)
     * @param int|null $institutionId Id dell'ente
     * @param string $field Campo del record a cui sono associati gli allegati da mostrare nei log
     * @param string $archive Nome dell'archivio del record a cui sono associati gli allegati(usato per i log)
     * @param int|null $userId id Utente
     * @param bool $hasApi Verifica se la chiamata e di tipo API.
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return array
     * @throws Exception
     */
    public function update(string $inputFileName = null, string $label = null, int $id = null, int $institutionId = null, string $field = '', string $archive = '', int $userId = null, bool $hasApi = false, null|string $bdncpCat = ''): array
    {
        $data = [];
        $ids = [];
        $validator = new Validator();

        $index = 0;

        if (!empty ($bdncpCat)) {
            $tmp = explode('O__O', $bdncpCat);
            $bdncpCat = $tmp[0];
            $index = (int) $tmp[1];
        }

        if ($index === 0) {
            $this->config['upload_path'] = $this->config['upload_path'] . DIRECTORY_SEPARATOR . $label;
        }

        if (!file_exists($this->config['upload_path'])) {
            mkdir($this->config['upload_path'], 0777, true);
        }

        if ($inputFileName == null && $label == null && $id == null) {

            return $data;
        }

        $postAttachIds = Input::post('attach_id' . $bdncpCat, true);

        $query = AttachmentsModel::select(['id', 'institution_id', 'archive_name', 'archive_id', 'file_name'])
            ->where('archive_name', $label)
            ->where('archive_id', (int) $id);

        if (!empty ($bdncpCat)) {
            $query->where('bdncp_cat', $bdncpCat);
        }

        $query = $query->get();

        $attachIds = !empty ($query) ? $query : null;

        if ($postAttachIds === null) {

            $ids = $attachIds;
        } else {

            if ($query) {

                foreach ($attachIds as $item) {

                    if (!in_array($item->id, $postAttachIds)) {

                        $ids[] = $item;
                    }
                }
            }
        }

        // Dati per registrazione ActivityLog e Versioning
        if ($userId === null) {

            $getIdentity = authPatOs()->getIdentity(['id']);
            $userId = $getIdentity['id'] ?? 1;
        }

        //Recupero il nome dell'archivio per i log
        $archivio = config($label, null, 'archiveConfig')['name'] ?? $archive;

        if (!empty ($ids)) {

            //Path per l'eliminazione degli allegati dell'elemento
            $path = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $label . DIRECTORY_SEPARATOR;

            //Log delle attività per eliminazione allegati
            $logValues = [
                'user_id' => $userId,
                'action' => 'Eliminazione Allegato',
                'action_type' => 'deleteObjectInstance',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'object_id' => 999,
                'area' => 'object',
                'platform' => 'all'
            ];

            //Eliminazione degli allegati dal file system
            foreach ($ids as $attach) {
                $filePath = $path . $attach['file_name'];

                // Controllo se il file allegato esiste prima di eliminarlo dal file system
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $logValues['description'] = 'Eliminazione Allegato (ID ' . $attach->id . ') <br>'
                    . 'Allegati per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')';

                $logValues['recordId'] = $attach->id;

                //Elimino l'allegato dal database
                $attach->delete();

                // Storage Activity log
                ActivityLog::create($logValues);
            }
        }

        if (!$hasApi) {
            if (filesUploaded($inputFileName)) {

                $countFiles = count($_FILES[$inputFileName]['name']);

                if ($countFiles >= 1) {

                    for ($i = 0; $i < $countFiles; $i++) {

                        $dataValue = [];

                        // Hack multi upload
                        $_FILES['file']['name'] = $_FILES[$inputFileName]['name'][$i];
                        $_FILES['file']['type'] = $_FILES[$inputFileName]['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES[$inputFileName]['error'][$i];
                        $_FILES['file']['size'] = $_FILES[$inputFileName]['size'][$i];

                        $_POST['temp_label_attach'] = !empty ($_POST['label_attach' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$i]), true, false))
                            : null;
                        // Set temp DATA
                        $_POST['temp_bdncp_cat'] = !empty ($_POST['bdncp_cat' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['bdncp_cat' . $bdncpCat][$i]), true, false))
                            : null;
                        $_POST['temp_publish'] = !empty ($_POST['publish' . $bdncpCat]) && (!empty ($_POST['publish' . $bdncpCat][$i]) || $_POST['publish' . $bdncpCat][$i] == 0)
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                            : 1;
                        $_POST['temp_omissis'] = !empty ($_POST['omissis' . $bdncpCat]) && !empty ($_POST['omissis' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$i]), true, false))
                            : 0;
                        $_POST['temp_category'] = !empty ($_POST['category' . $bdncpCat][$i])
                            ? $_POST['category' . $bdncpCat][$i]
                            : 1;

                        $dataValue['archive_name'] = $label;
                        $dataValue['archive_id'] = $id;
                        $dataValue['label'] = strip_tags((string) Input::post('temp_label_attach', true));
                        $dataValue['active'] = (int) Input::post('temp_publish');
                        $dataValue['indexable'] = (int) Input::post('temp_omissis');
                        $dataValue['institution_id'] = $institutionId;
                        $dataValue['sort'] = $i + 1;
                        $dataValue['bdncp_cat'] = !empty (Input::post('temp_bdncp_cat')) ? strip_tags((string) Input::post('temp_bdncp_cat', true)) : null;

                        if (Input::post('temp_category') !== null) {
                            $dataValue['cat_id'] = (int) Input::post('temp_category');
                        }

                        $validator->label('identificato')
                            ->value(!empty ($_POST['attach_id' . $bdncpCat][$i]) ? $_POST['attach_id' . $bdncpCat][$i] : false)
                            ->required()
                            ->isNaturalNoZero()
                            ->end();

                        if (!$validator->isSuccess()) {

                            $upload = new Uploads($this->config, $hasApi);
                            if ($upload->doUpload('file')) {

                                $uploadData = $upload->data();

                                $data['success'][] = $uploadData;
                                $dataValue['file_name'] = $uploadData['file_name'];
                                $dataValue['file_type'] = $uploadData['file_type'];
                                $dataValue['file_path'] = $uploadData['file_path'];
                                $dataValue['full_path'] = $uploadData['full_path'];
                                $dataValue['raw_name'] = $uploadData['raw_name'];
                                $dataValue['orig_name'] = $this->utf8->cleanString(urlTitle($uploadData['orig_name']));
                                $dataValue['client_name'] = $uploadData['client_name'];
                                $dataValue['file_ext'] = $uploadData['file_ext'];
                                $dataValue['file_size'] = $uploadData['file_size'];
                                $dataValue['is_image'] = $uploadData['is_image'];
                                $dataValue['image_width'] = $uploadData['image_width'];
                                $dataValue['image_height'] = $uploadData['image_height'];
                                $dataValue['image_type'] = $uploadData['image_type'];
                                $dataValue['image_size_str'] = $uploadData['image_size_str'];
                                $dataValue['fingerprint'] = $uploadData['fingerprint'] ?? null;

                                //Inserisco i nuovi allegati
                                $insert = AttachmentsModel::create($dataValue);

                                // Storage Activity log
                                ActivityLog::create([
                                    'user_id' => $userId,
                                    'action' => 'Aggiunta Allegati',
                                    'action_type' => 'addObjectInstance',
                                    'description' => 'Aggiunta Allegato (ID ' . $insert->id . ') <br>'
                                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                                    'request_post' => [
                                        'post' => @$_POST,
                                        'get' => Input::get(),
                                        'server' => Input::server(),
                                    ],
                                    'object_id' => 999,
                                    'record_id' => $insert->id,
                                    'area' => 'object',
                                    'platform' => 'all'
                                ]);

                            } else {
                                $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' -> ' . strip_tags((string) Input::post('temp_label_attach', true)) . ') </li>');
                                $data['error'][] = $error;
                                $this->errors .= $error;
                                unset($error);
                            }
                        } else {

                            $updateId = (int) $_POST['attach_id' . $bdncpCat][$i];

                            //Degli allegati non nuovi ma che erano già presenti, aggiorno solo quelli per cui è cambiato una delle
                            //informazioni modificabili dagli utenti: label, omissis e indicizzabile
                            $updated = AttachmentsModel::where('id', $updateId)
                                ->where(function ($query) {
                                    $query->where('label', '!=', $_POST['temp_label_attach'])
                                        ->orWhere('indexable', '!=', $_POST['temp_omissis'])
                                        ->orWhere('active', '!=', $_POST['temp_publish']);
                                })
                                ->update($dataValue);


                            if ($updated) {
                                // Storage Activity log
                                ActivityLog::create([
                                    'user_id' => $userId,
                                    'action' => 'Modifica Allegati',
                                    'action_type' => 'updateObjectInstance',
                                    'description' => 'Modifica Allegato (ID ' . $updateId . ') <br>'
                                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                                    'request_post' => [
                                        'post' => @$_POST,
                                        'get' => Input::get(),
                                        'server' => Input::server(),
                                    ],
                                    'object_id' => 999,
                                    'record_id' => $updateId,
                                    'area' => 'object',
                                    'platform' => 'all'
                                ]);
                            }
                        }
                    }
                }
            }

        } else {

            // Validazione PUT
            $files = Input::stream('file');

            if (isset ($files['name']) && is_array($files['name']) && count($files['name']) >= 1) {

                $countFiles = count($files['name']);
                $attachIds = Input::stream('post.attach_id');

                $countAttach = AttachmentsModel::where('archive_name', '=', $label)
                    ->where('archive_id', '=', $id)
                    ->count();

                for ($i = 0; $i < $countFiles; $i++) {

                    $requestAttachLabel = !empty (Input::post('label_attach')[$i])
                        ? strip_tags(Input::post('label_attach')[$i])
                        : '';

                    $requestAttachOmissis = !empty (Input::post('omissis')[$i])
                        ? (int) Input::post('omissis')[$i]
                        : 0;

                    $requestAttachPublish = !empty (Input::post('publish')[$i])
                        ? (int) Input::post('publish')[$i]
                        : 1;

                    $requestAttachPublishCategory = !empty (Input::post('category')[$i])
                        ? (int) Input::post('category')[$i]
                        : null;

                    $dataValue = [];
                    $dataValue['archive_name'] = $label;
                    $dataValue['archive_id'] = $id;
                    $dataValue['label'] = $requestAttachLabel;
                    $dataValue['active'] = $requestAttachPublish;
                    $dataValue['indexable'] = $requestAttachOmissis;
                    $dataValue['institution_id'] = $institutionId;
                    $dataValue['sort'] = $countAttach + $i + 1;

                    if ($requestAttachPublishCategory !== null) {
                        $dataValue['cat_id'] = (int) $requestAttachPublishCategory;
                    }

                    $validator->label('identificato')
                        ->value(!empty ($attachIds['attach_id'][$i]) ? $attachIds['attach_id'][$i] : false)
                        ->required()
                        ->isNaturalNoZero()
                        ->end();

                    if (!$validator->isSuccess()) {
                        $upload = new Uploads($this->config, true);

                        if ($upload->doUpload('file.' . $i, true)) {

                            $uploadData = $upload->data();

                            $data['success'][] = $uploadData;

                            $dataValue['file_name'] = $uploadData['file_name'];
                            $dataValue['file_type'] = $uploadData['file_type'];
                            $dataValue['file_path'] = $uploadData['file_path'];
                            $dataValue['full_path'] = $uploadData['full_path'];
                            $dataValue['raw_name'] = $uploadData['raw_name'];
                            $dataValue['orig_name'] = $this->utf8->cleanString(urlTitle($uploadData['orig_name']));
                            $dataValue['client_name'] = $uploadData['client_name'];
                            $dataValue['file_ext'] = $uploadData['file_ext'];
                            $dataValue['file_size'] = $uploadData['file_size'];
                            $dataValue['is_image'] = $uploadData['is_image'];
                            $dataValue['image_width'] = $uploadData['image_width'];
                            $dataValue['image_height'] = $uploadData['image_height'];
                            $dataValue['image_type'] = $uploadData['image_type'];
                            $dataValue['image_size_str'] = $uploadData['image_size_str'];
                            $dataValue['fingerprint'] = $uploadData['fingerprint'];

                            //Inserisco i nuovi allegati
                            $insert = AttachmentsModel::create($dataValue);

                            // Storage Activity log
                            ActivityLog::create([
                                'user_id' => $userId,
                                'action' => 'Aggiunta Allegati',
                                'action_type' => 'addObjectInstance',
                                'description' => 'Aggiunta Allegato (ID ' . $insert->id . ') <br>'
                                    . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                                'request_post' => [
                                    'post' => @$_POST,
                                    'get' => Input::get(),
                                    'server' => Input::server(),
                                ],
                                'object_id' => 999,
                                'record_id' => $insert->id,
                                'area' => 'object',
                                'platform' => 'all'
                            ]);

                        } else {


                            $updateId = (int) $attachIds['attach_id'][$i];

                            $updated = AttachmentsModel::where('id', $updateId)
                                ->where(function ($query) {
                                    $query->where('label', '!=', $requestAttachLabel)
                                        ->orWhere('indexable', '!=', $requestAttachOmissis)
                                        ->orWhere('active', '!=', $requestAttachPublish);
                                })
                                ->update($dataValue);

                            if ($updated) {
                                // Storage Activity log
                                ActivityLog::create([
                                    'user_id' => $userId,
                                    'action' => 'Modifica Allegati',
                                    'action_type' => 'updateObjectInstance',
                                    'description' => 'Modifica Allegato (ID ' . $updateId . ') <br>'
                                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                                    'request_post' => [
                                        'post' => Input::post(),
                                        'get' => Input::get(),
                                        'server' => Input::server(),
                                    ],
                                    'object_id' => 999,
                                    'record_id' => $updateId,
                                    'area' => 'object',
                                    'platform' => 'all'
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @description Funzione che restituisce tutti gli allegati di un determinato record
     * @param string|null $label Nome dell'archivio del record a cui sono associati gli allegati da recuperare
     * @param int|null $id Id del record a cui sono associati gli allegati (nelle varie tabelle personale, strutture...)
     * @param array|string|null $select Campi che si vogliono includere nella query di select
     * @param bool $showOnlyPublic Indica se prendere o meno solo gli allegati pubblici
     * @return array
     */
    public function getAllByObject(?string $label = null, ?int $id = null, array|string|null $select = ['*'], bool $showOnlyPublic = false): array
    {
        $files = [];

        $query = AttachmentsModel::select($select)
            ->withoutGlobalScope(InstitutionScope::class)
            ->whereNull('deleted_at')
            ->where('archive_name', $label)
            ->where('archive_id', $id)
            ->where('deleted', 0)
            ->where(function ($query) use ($showOnlyPublic) {
                if ($showOnlyPublic) {
                    $query->where('active', '1');
                }
            })
            ->orderBy('sort', 'ASC')
            ->orderBy('label', 'ASC')
            ->get();

        if (!empty ($query)) {
            foreach ($query->toArray() as $item) {

                $files[] = [
                    'id' => $item['id'],
                    'label' => $item['label'],
                    'omissis' => $item['indexable'],
                    'public' => $item['active'],
                    'category_id' => $item['cat_id'] ?? null,
                    'bdncp_cat' => $item['bdncp_cat'] ?? null,
                    // Alias categoria
                    'file' => [
                        //'lastModified' => (int)microtime(strtotime($item['created_at'])),
                        //'lastModifiedDate' => date("F d Y H:i:s.", strtotime($item['created_at'])),
                        'name' => $item['client_name'],
                        'size' => $item['file_size'],
                        'type' => $item['file_type'],
                        'ext' => $item['file_ext'],
                        //'webkitRelativePath' => ''
                    ],
                    'fingerprint' => $item['fingerprint'] ?? null,
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at']
                ];
            }
        }
        return $files;
    }

    /**
     * @description Da finire
     * @param string|null $label Nome dell'archivio del record a cui sono associati gli allegati
     * @param int|null $id Id del record a cui sono associati gli allegati
     * @return void|null
     */
    public function delete(string $label = null, int $id = null)
    {

        if ($label == null && $id == null) {
            return null;
        }
    }

    /**
     * @description Funzione che ritorna eventuali errori sotto forma di una lista html
     * @return string
     */
    public function errorsToString(): string
    {
        $html = '<ul>';
        $html .= $this->errors;
        $html .= '</ul>';
        return $html;
    }

    /**
     * @description Metodo per la validazione degli allegati
     *
     * @param string|null $inputFileName Nome del file da validare
     * @param bool $haPut Se la richiesta è di tipo PUt o
     *                                   altro (POST)
     * @param bool $hasApi Se la richiesta è di
     *                                   tipo API
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return array
     * @throws Exception
     */
    public function validate(string $inputFileName = null, bool $haPut = false, bool $hasApi = false, null|string $bdncpCat = ''): array
    {
        $data = [];

        if ($inputFileName == null) {
            return $data;
        }

        $validator = new Validator();

        if (!$haPut) {
            if (filesUploaded($inputFileName) === true) {

                if (\Helpers\FileSystem\File::isMultiUpload($inputFileName)) {
                    // Multi upload
                    for ($i = 0; $i < count($_FILES[$inputFileName]['name']); $i++) {
                        $_FILES['file']['name'] = $_FILES[$inputFileName]['name'][$i];
                        $_FILES['file']['type'] = $_FILES[$inputFileName]['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES[$inputFileName]['error'][$i];
                        $_FILES['file']['size'] = $_FILES[$inputFileName]['size'][$i];

                        $_POST['temp_label_attach'] = !empty ($_POST['label_attach' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$i]), true, false))
                            : null;
                        $_POST['temp_publish'] = !empty ($_POST['publish' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                            : null;
                        $_POST['temp_omissis'] = !empty ($_POST['omissis' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$i]), true, false))
                            : null;

                        $_POST['temp_publish'] = !empty ($_POST['publish' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                            : null;

                        $_POST['temp_category'] = !empty ($_POST['temp_category' . $bdncpCat][$i])
                            ? (int) $_POST['bdncp_cat' . $bdncpCat][$i]
                            : null;

                        $validator->label('identificato')
                            ->value(!empty ($_POST['attach_id' . $bdncpCat][$i]) ? $_POST['attach_id' . $bdncpCat][$i] : false)
                            ->required()
                            ->isNaturalNoZero()
                            ->end();

                        if (!$validator->isSuccess()) {

                            $upload = new Uploads($this->config);

                            if ($upload->doUpload('file', false)) {

                                $uploadData = $upload->data();
                                $data['success'][] = $uploadData;
                            } else {
                                $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' con etichetta: ' . strip_tags((string) Input::post('temp_label_attach', true)) . ') </li>');
                                $data['error'][] = $error;
                                $this->errors .= $error;
                                unset($error);
                            }
                        }
                    }
                } else {
                    // upload singolo
                    $_FILES['file']['name'] = $_FILES[$inputFileName]['name'];
                    $_FILES['file']['type'] = $_FILES[$inputFileName]['type'];
                    $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'];
                    $_FILES['file']['error'] = $_FILES[$inputFileName]['error'];
                    $_FILES['file']['size'] = $_FILES[$inputFileName]['size'];

                    $_POST['temp_label_attach'] = !empty ($_POST['label_attach'][0])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach'][0]), true, false))
                        : null;
                    $_POST['temp_publish'] = !empty ($_POST['publish'][0])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish'][0]), true, false))
                        : null;
                    $_POST['temp_omissis'] = !empty ($_POST['omissis'][0])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis'][0]), true, false))
                        : null;
                    $_POST['temp_category'] = !empty ($_POST['temp_category'][0])
                        ? (int) $_POST['category'][0]
                        : null;

                    $_POST['temp_bdncp_cat'] = !empty ($_POST['temp_category'][0])
                        ? (int) $_POST['bdncp_cat'][0]
                        : null;

                    $validator->label('identificato')
                        ->value(!empty ($_POST['attach_id'][0]) ? $_POST['attach_id'][0] : false)
                        ->required()
                        ->isNaturalNoZero()
                        ->end();

                    if (!$validator->isSuccess()) {

                        $upload = new Uploads($this->config);

                        if ($upload->doUpload('file', false)) {
                            $uploadData = $upload->data();
                            $data['success'][] = $uploadData;
                        } else {
                            $error = $upload->displayErrors('<li>', 'Allegato con etichetta: ' . strip_tags((string) Input::post('temp_label_attach', true)) . ') </li>');
                            $data['error'][] = $error;
                            $this->errors .= $error;
                            unset($error);
                        }
                    }
                }
            }
        } else {

            // Validazione PUT
            $files = Input::stream('file');

            if (isset ($files['name']) && is_array($files['name']) && count($files['name']) >= 1) {

                $countFiles = count($files['name']);

                for ($i = 0; $i < $countFiles; $i++) {

                    $attachIds = Input::stream('post.attach_id');
                    $validator->label('identificato')
                        ->value(!empty ($attachIds['attach_id'][$i]) ? $attachIds['attach_id'][$i] : false)
                        ->required()
                        ->isNaturalNoZero()
                        ->end();

                    if (!$validator->isSuccess()) {
                        $upload = new Uploads($this->config, true);

                        if ($upload->doUpload('file.' . $i, false)) {
                            $uploadData = $upload->data();
                            $data['success'][] = $uploadData;
                        } else {
                            $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' con etichetta: ' . strip_tags((string) Input::post('temp_label_attach', true)) . ') </li>');
                            $data['error'][] = $error;
                            $this->errors .= $error;
                            unset($error);
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function getConfigFieldsBdncpCategories(): string
    {
        $procedureCat = config('config', null, 'bdncp_procedure_config');

        $stringCat = '';
        if (is_array($procedureCat)) {
            foreach ($procedureCat as $k => $v) {
                $stringCat .= $k . ',';
            }

            $stringCat = rtrim(trim($stringCat), ',');
        }

        return $stringCat;
    }
}