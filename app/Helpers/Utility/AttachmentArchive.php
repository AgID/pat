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
use System\Input;
use System\Registry;
use System\Uploads;
use System\Utf8;
use System\Validator;

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
        $this->institutionId = !empty($institutionId) && ctype_cntrl($institutionId)
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
     * Restringe i formati acquisibili per uno specifico flusso di upload.
     *
     * @param string $allowedTypes Tipi ammessi nel formato est1|est2
     * @return object
     */
    public function setAllowedTypes(string $allowedTypes = ''): object
    {
        if ($allowedTypes !== '') {
            $this->config['allowed_types'] = $allowedTypes;
        }

        return $this;
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
     * @param bool $log Indica se salvare l'azione nei log o meno
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return array
     * @throws Exception
     */
    public function storage(?string $inputFileName = null, ?string $label = null, ?int $id = null, string $field = '', string|null $archive = '', int $institutionId = null, int $userId = null, bool $log = true, null|string $bdncpCat = ''): array
    {
        $data = [];
        $index = 0;

        if (!empty($bdncpCat)) {
            $tmp = explode('O__O', $bdncpCat);
            $bdncpCat = $tmp[0];
            $index = (int)$tmp[1];
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

            if (\Helpers\FileSystem\File::isMultiUpload($inputFileName)) {
                // Multi upload
                for ($i = 0; $i < count($_FILES[$inputFileName]['name']); $i++) {

                    $_FILES['file']['name'] = $_FILES[$inputFileName]['name'][$i];
                    $_FILES['file']['type'] = $_FILES[$inputFileName]['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES[$inputFileName]['error'][$i];
                    $_FILES['file']['size'] = $_FILES[$inputFileName]['size'][$i];

                    // Controllo se sto duplicando un file
                    if (!empty($_POST['attach_id' . $bdncpCat][$i]) && $_POST['attach_id' . $bdncpCat][$i] !== 'null') {
                        //Per i casi di duplicazione di un elemento
                        duplicateAttach($id, $label, $_POST['attach_id' . $bdncpCat][$i], $i, $this->utf8, $bdncpCat);
                    } else {
                        $upload = new Uploads($this->config);

                        if ($upload->doUpload('file') === true) {
                            $uploadData = $upload->data();

                            // $data['success'][] = $uploadData;

                            // Set temp DATA
                            $_POST['temp_label_attach'] = !empty($_POST['label_attach' . $bdncpCat][$i])
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$i]), true, false))
                                : null;
                            // Set temp DATA
                            $_POST['temp_bdncp_cat'] = !empty($_POST['bdncp_cat' . $bdncpCat][$i])
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['bdncp_cat' . $bdncpCat][$i]), true, false))
                                : null;
                            $_POST['temp_publish'] = !empty($_POST['publish' . $bdncpCat]) && (!empty($_POST['publish' . $bdncpCat][$i]) || $_POST['publish' . $bdncpCat][$i] == 0)
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                                : 1;
                            $_POST['temp_omissis'] = !empty($_POST['omissis' . $bdncpCat]) && !empty($_POST['omissis' . $bdncpCat][$i])
                                ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$i]), true, false))
                                : 0;
                            $_POST['temp_category'] = !empty($_POST['category'][$i])
                                ? $_POST['category'][$i]
                                : 1;

                            $insert = AttachmentsModel::create([
                                'archive_name' => $label,
                                'sort' => $i + 1,
                                'institution_id' => ($institutionId === null) ? checkAlternativeInstitutionId() : $institutionId,
                                'cat_id' => (int)Input::post('temp_category'),
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
                                'label' => !empty(Input::post('temp_label_attach')) ? strip_tags((string)Input::post('temp_label_attach', true)) : 'Allegato',
                                'bdncp_cat' => !empty(Input::post('temp_bdncp_cat')) ? strip_tags((string)Input::post('temp_bdncp_cat', true)) : null,
                                'active' => (int)strip_tags((string)Input::post('temp_publish', true)),
                                'indexable' => (int)strip_tags((string)Input::post('temp_omissis', true)),
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
                                    'object_id' => 999,
                                    'record_id' => $insert->id,
                                    'area' => 'object',
                                    'platform' => 'all'
                                ]);
                            }

                            $uploadData = array_merge([
                                'insert_id' => $insert->id,
                                'archive_id' => $insert->archive_id
                            ], $uploadData);
                            $data['success'][] = $uploadData;
                        } else {
                            $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' -> ) ' . strip_tags((string)Input::post('temp_label_attach', true)) . ' </li>');
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
                if (!empty($_POST['attach_id'][0]) && $_POST['attach_id'][0] !== 'null') {
                    //Per i casi di duplicazione di un elemento
                    duplicateAttach($id, $label, $_POST['attach_id'][0], 0, $this->utf8, $bdncpCat);
                } else {
                    $upload = new Uploads($this->config);

                    if ($upload->doUpload('file') === true) {
                        $uploadData = $upload->data();

                        // $data['success'][] = $uploadData;

                        // Set temp DATA
                        $_POST['temp_label_attach'] = !empty($_POST['label_attach'][0])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach'][0]), true, false))
                            : null;
                        $_POST['temp_publish'] = !empty($_POST['publish']) && (!empty($_POST['publish'][0]) || $_POST['publish'][0] == 0)
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish'][0]), true, false))
                            : 1;
                        $_POST['temp_omissis'] = !empty($_POST['omissis']) && !empty($_POST['omissis'][0])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis'][0]), true, false))
                            : 0;
                        $_POST['temp_category'] = !empty($_POST['category'][0])
                            ? $_POST['category'][0]
                            : 1;

                        $insert = AttachmentsModel::create([
                            'archive_name' => $label,
                            'sort' => 1,
                            'institution_id' => ($institutionId === null) ? checkAlternativeInstitutionId() : $institutionId,
                            'cat_id' => (int)Input::post('temp_category'),
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
                            'active' => (int)strip_tags((string)Input::post('temp_publish', true)),
                            'indexable' => (int)strip_tags((string)Input::post('temp_omissis', true)),
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
                            'object_id' => 999,
                            'record_id' => $insert->id,
                            'area' => 'object',
                            'platform' => 'all'
                        ]);

                        $uploadData = array_merge([
                            'insert_id' => $insert->id,
                            'archive_id' => $insert->archive_id
                        ], $uploadData);

                        $data['success'][] = $uploadData;
                    } else {
                        $error = $upload->displayErrors('<li>', ' (Allegato 1 -> ) ' . strip_tags((string)Input::post('temp_label_attach', true)) . ' </li>');
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
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return array
     * @throws Exception
     */
    public function update(string $inputFileName = null, string $label = null, int $id = null, int $institutionId = null, string $field = '', string $archive = '', int $userId = null, null|string $bdncpCat = ''): array
    {
        $data = [];
        $ids = [];
        $validator = new Validator();

        $index = 0;

        if (!empty($bdncpCat)) {
            $tmp = explode('O__O', $bdncpCat);
            $bdncpCat = $tmp[0];
            $index = (int)$tmp[1];
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

        $query = AttachmentsModel::select([
                'id', 
                'institution_id', 
                'archive_name', 
                'archive_id', 
                'file_name',
                'label',
                'indexable',
                'active'
            ])
            ->where('archive_name', $label)
            ->where('archive_id', (int)$id);

        if (!empty($bdncpCat)) {
            $query->where('bdncp_cat', $bdncpCat);
        }

        $query = $query->get();

        $attachIds = !empty($query) ? $query : null;

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

        if (!empty($ids)) {
            //Path per l'eliminazione degli allegati dell'elemento
            $path = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $label . DIRECTORY_SEPARATOR;

            //Log delle attività per eliminazione allegati
            $logValues = [
                'user_id' => $userId,
                'action' => 'Eliminazione Allegato',
                'action_type' => 'deleteObjectInstance',
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

                $idToDelete = $attach->id;

                $logValues['description'] = 'Eliminazione Allegato (ID ' . $idToDelete . ') <br>'
                    . 'Allegati per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')';

                $logValues['recordId'] = $idToDelete;

                //Elimino l'allegato dal database
                $attach->delete();


                // Storage Activity log
                ActivityLog::create($logValues);
            }




            //            foreach ($ids as $singleId) {
            //                $logValues['description'] = 'Eliminazione Allegato (ID ' . $singleId . ') <br>'
            //                    . 'Allegati per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')';
            //
            //                $logValues['recordId'] = $singleId;
            //
            //                // Storage Activity log
            //                ActivityLog::create($logValues);
            //            }
        }

            if (filesUploaded($inputFileName)) {
                $countFiles = count($_FILES[$inputFileName]['name']);

                if ($countFiles >= 1) {
                    for ($i = 0; $i < $countFiles; $i++) {
                        $dataValue = [];

                        $_FILES['file']['name'] = $_FILES[$inputFileName]['name'][$i];
                        $_FILES['file']['type'] = $_FILES[$inputFileName]['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES[$inputFileName]['error'][$i];
                        $_FILES['file']['size'] = $_FILES[$inputFileName]['size'][$i];

                        $_POST['temp_label_attach'] = !empty($_POST['label_attach' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$i]), true, false))
                            : null;
                        // Set temp DATA
                        $_POST['temp_bdncp_cat'] = !empty($_POST['bdncp_cat' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['bdncp_cat' . $bdncpCat][$i]), true, false))
                            : null;
                        $_POST['temp_publish'] = !empty($_POST['publish' . $bdncpCat]) && (!empty($_POST['publish' . $bdncpCat][$i]) || $_POST['publish' . $bdncpCat][$i] == 0)
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                            : 1;
                        $_POST['temp_omissis'] = !empty($_POST['omissis' . $bdncpCat]) && !empty($_POST['omissis' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$i]), true, false))
                            : 0;
                        $_POST['temp_category'] = !empty($_POST['category' . $bdncpCat][$i])
                            ? $_POST['category' . $bdncpCat][$i]
                            : 1;

                        $dataValue['archive_name'] = $label;
                        $dataValue['archive_id'] = $id;
                        $dataValue['label'] = strip_tags((string)Input::post('temp_label_attach', true));
                        $dataValue['active'] = (int)Input::post('temp_publish');
                        $dataValue['indexable'] = (int)Input::post('temp_omissis');
                        $dataValue['institution_id'] = $institutionId;
                        $dataValue['sort'] = $i + 1;
                        $dataValue['bdncp_cat'] = !empty(Input::post('temp_bdncp_cat')) ? strip_tags((string)Input::post('temp_bdncp_cat', true)) : null;

                        if (Input::post('temp_category') !== null) {
                            $dataValue['cat_id'] = (int)Input::post('temp_category');
                        }

                        $validator->label('identificato')
                            ->value(!empty($_POST['attach_id' . $bdncpCat][$i]) ? $_POST['attach_id' . $bdncpCat][$i] : false)
                            ->required()
                            ->isNaturalNoZero()
                            ->end();

                        if (!$validator->isSuccess()) {
                            $upload = new Uploads($this->config);
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

                                $dataValue = $dataValue ?? null;

                                //Inserisco i nuovi allegati
                                $insert = AttachmentsModel::create($dataValue);


                                // Storage Activity log
                                ActivityLog::create([
                                    'user_id' => $userId,
                                    'action' => 'Aggiunta Allegati',
                                    'action_type' => 'addObjectInstance',
                                    'description' => 'Aggiunta Allegato (ID ' . $insert->id . ') <br>'
                                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
                                    'object_id' => 999,
                                    'record_id' => $insert->id,
                                    'area' => 'object',
                                    'platform' => 'all'
                                ]);
                            } else {
                                $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' -> ' . strip_tags((string)Input::post('temp_label_attach', true)) . ') </li>');
                                $data['error'][] = $error;
                                $this->errors .= $error;
                                unset($error);
                            }
                        } else {
                            $updateId = (int)$_POST['attach_id' . $bdncpCat][$i];

                            //Degli allegati non nuovi ma che erano già presenti, aggiorno solo quelli per cui è cambiato una delle
                            //informazioni modificabili dagli utenti: label, omissis e indicizzabil

                            $fieldToCheck = ['label', 'indexable', 'active'];
                            $fieldToCheck = $fieldToCheck ?? null;

                            $attachment = AttachmentsModel::find($updateId);
                            if ($attachment) {
                                $attachment->fill($dataValue);
                                if ($attachment->isDirty($fieldToCheck)) {
                                    $attachment->save();
                                    $updated = true;
                                } elseif ($attachment->isDirty('sort')) {
                                    $attachment->timestamps = false;
                                    $attachment->save();
                                    $updated = true;
                                } else {
                                    $updated = false;
                                }
                            } else {
                                $updated = false;
                            }


                            if ($updated) {
                                // Storage Activity log
                                ActivityLog::create([
                                    'user_id' => $userId,
                                    'action' => 'Modifica Allegati',
                                    'action_type' => 'updateObjectInstance',
                                    'description' => 'Modifica Allegato (ID ' . $updateId . ') <br>'
                                        . 'Allegato per ' . $field . ' (' . $archivio . ' - ID: ' . $id . ')',
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

        $query = AttachmentsModel::select($select);

        $query = $query ?? null;

        $query = $query->whereNull('deleted_at')
            ->where('archive_name', $label)
            ->where('archive_id', $id)
            ->where('deleted', 0)
            ->where(function ($query) use ($showOnlyPublic) {
                if ($showOnlyPublic) {
                    $query->where('active', '1');
                }
            });

        $query = $query ?? null;

        $query = $query
            ->orderBy('sort', 'ASC')
            ->orderBy('label', 'ASC')
            ->get();

        if (!empty($query)) {
            $i = 0;
            foreach ($query->toArray() as $item) {

                $files[$i]['id'] = $item['id'];
                if (isset($item['source_id']) && $item['source_id'] !== null) {
                    $files[$i]['source_id'] = $item['source_id'];
                }
                $files[$i]['label'] = $item['label'];
                $files[$i]['omissis'] = $item['indexable'];
                $files[$i]['public'] = $item['active'];
                $files[$i]['category_id'] = $item['cat_id'] ?? null;
                $files[$i]['bdncp_cat'] = $item['bdncp_cat'] ?? null;
                $files[$i]['file']['name'] = $item['client_name'];
                $files[$i]['file']['size'] = $item['file_size'];
                $files[$i]['file']['type'] = $item['file_type'];
                $files[$i]['file']['ext'] = $item['file_ext'];
                $files[$i]['file']['client_name'] = $item['client_name'];
                $files[$i]['fingerprint'] = $item['fingerprint'] ?? null;
                $files[$i]['created_at'] = $item['created_at'];
                $files[$i]['updated_at'] = $item['updated_at'];

                //                $files[] = [
                //                    'id' => $item['id'],
                //                    'label' => $item['label'],
                //                    'omissis' => $item['indexable'],
                //                    'public' => $item['active'],
                //                    'category_id' => $item['cat_id'] ?? null,
                //                    'bdncp_cat' => $item['bdncp_cat'] ?? null,
                //                    // Alias categoria
                //                    'file' => [
                //                        //'lastModified' => (int)microtime(strtotime($item['created_at'])),
                //                        //'lastModifiedDate' => date("F d Y H:i:s.", strtotime($item['created_at'])),
                //                        'name' => $item['client_name'],
                //                        'size' => $item['file_size'],
                //                        'type' => $item['file_type'],
                //                        'ext' => $item['file_ext'],
                //                        //'webkitRelativePath' => ''
                //                    ],
                //                    'fingerprint' => $item['fingerprint'] ?? null,
                //                    'created_at' => $item['created_at'],
                //                    'updated_at' => $item['updated_at']
                //                ];

                $lastKey = array_key_last($files);

                $files[$lastKey] = $files[$lastKey] ?? null;

                $i++;
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
     * @param string|null $bdncpCat Categoria per gli allegati delle procedure BDNCP
     * @return array
     * @throws Exception
     */
    public function validate(string $inputFileName = null, null|string $bdncpCat = ''): array
    {
        $data = [];

        if ($inputFileName == null) {
            return $data;
        }

            if (filesUploaded($inputFileName) === true) {
                if (\Helpers\FileSystem\File::isMultiUpload($inputFileName)) {

                    // Multi upload
                    for ($i = 0; $i < count($_FILES[$inputFileName]['name']); $i++) {
                        $validator = new Validator();

                        $_FILES['file']['name'] = $_FILES[$inputFileName]['name'][$i];
                        $_FILES['file']['type'] = $_FILES[$inputFileName]['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES[$inputFileName]['error'][$i];
                        $_FILES['file']['size'] = $_FILES[$inputFileName]['size'][$i];

                        $_POST['temp_label_attach'] = !empty($_POST['label_attach' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$i]), true, false))
                            : null;
                        $_POST['temp_publish'] = !empty($_POST['publish' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                            : null;
                        $_POST['temp_omissis'] = !empty($_POST['omissis' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$i]), true, false))
                            : null;

                        $_POST['temp_publish'] = !empty($_POST['publish' . $bdncpCat][$i])
                            ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$i]), true, false))
                            : null;

                        $_POST['temp_category'] = !empty($_POST['temp_category' . $bdncpCat][$i])
                            ? (int)$_POST['bdncp_cat' . $bdncpCat][$i]
                            : null;

                        $validator->label('identificato')
                            ->value(!empty($_POST['attach_id' . $bdncpCat][$i]) ? $_POST['attach_id' . $bdncpCat][$i] : false)
                            ->required()
                            ->isNaturalNoZero()
                            ->end();

                        if (!$validator->isSuccess()) {
                            $upload = new Uploads($this->config);

                            if ($upload->doUpload('file', false)) {
                                $uploadData = $upload->data();
                                $data['success'][] = $uploadData;
                            } else {
                                $error = $upload->displayErrors('<li>', ' (Allegato ' . ($i + 1) . ' con etichetta: ' . strip_tags((string)Input::post('temp_label_attach', true)) . ') </li>');
                                $data['error'][] = $error;
                                $this->errors .= $error;
                                unset($error);
                            }
                        }
                    }
                } else {
                    // upload singolo

                    $validator = new Validator();

                    $_FILES['file']['name'] = $_FILES[$inputFileName]['name'];
                    $_FILES['file']['type'] = $_FILES[$inputFileName]['type'];
                    $_FILES['file']['tmp_name'] = $_FILES[$inputFileName]['tmp_name'];
                    $_FILES['file']['error'] = $_FILES[$inputFileName]['error'];
                    $_FILES['file']['size'] = $_FILES[$inputFileName]['size'];

                    $_POST['temp_label_attach'] = !empty($_POST['label_attach'][0])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach'][0]), true, false))
                        : null;
                    $_POST['temp_publish'] = !empty($_POST['publish'][0])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish'][0]), true, false))
                        : null;
                    $_POST['temp_omissis'] = !empty($_POST['omissis'][0])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis'][0]), true, false))
                        : null;
                    $_POST['temp_category'] = !empty($_POST['temp_category'][0])
                        ? (int)$_POST['category'][0]
                        : null;

                    $_POST['temp_bdncp_cat'] = !empty($_POST['temp_category'][0])
                        ? (int)$_POST['bdncp_cat'][0]
                        : null;

                    $validator->label('identificato')
                        ->value(!empty($_POST['attach_id'][0]) ? $_POST['attach_id'][0] : false)
                        ->required()
                        ->isNaturalNoZero()
                        ->end();

                    if (!$validator->isSuccess()) {
                        $upload = new Uploads($this->config);

                        if ($upload->doUpload('file', false)) {
                            $uploadData = $upload->data();
                            $data['success'][] = $uploadData;
                        } else {
                            $error = $upload->displayErrors('<li>', 'Allegato con etichetta: ' . strip_tags((string)Input::post('temp_label_attach', true)) . ') </li>');
                            $data['error'][] = $error;
                            $this->errors .= $error;
                            unset($error);
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

            $stringCat = rtrim(trim($stringCat), ',') . ',_generalInfo';
        }

        return $stringCat;
    }
}
