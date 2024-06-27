<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\File;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Model\AttachmentsModel;
use Scope\DeletedScope;

class Model extends EloquentModel
{

    /**
     * Costruttore
     * @param array                    $attributes
     * @param ConnectionInterface|null $connection
     * @throws Exception
     */
    public function __construct(array $attributes = [], ConnectionInterface $connection = null)
    {
        parent::__construct($attributes);

        if ($connection) {
            $resolver = new ConnectionResolver();
            $resolver->addConnection('default', $connection);
            $resolver->setDefaultConnection('default');
            $this->setConnectionResolver($resolver);
        } else {
            $container = \System\Container::getInstance();
            $connection = $container->make('db');
            $resolver = new ConnectionResolver();
            $resolver->addConnection('default', $connection);
            $resolver->setDefaultConnection('default');
            $this->setConnectionResolver($resolver);
        }

        // Abilita il log delle query per il modello
        $this->getConnection()->enableQueryLog();
    }

    /**
     * Custom Builder
     * @param $query
     * @return BuilderWithLogs
     */
    public function newEloquentBuilder($query): BuilderWithLogs
    {
        return new BuilderWithLogs($query);
    }


    /**
     * @return \Illuminate\Database\Connection
     * Recupero la connessione...
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    /**
     * @description Funzione create custom. Estende la funzionalità create standard
     * @param array    $options Options
     * @param int|null $userId  UserId
     * @return Builder|EloquentModel
     * @throws Exception
     */
    public static function createWithLogs(array $options = [], int|null $userId = null): EloquentModel|Builder
    {
        //Recupero il modello dell'elemento appena creato
        $model = self::query()->getModel();

        // PRE UPDATE
        if (method_exists($model, 'preInsert')) {
            $model->preInsert($options);
        }

        //Record appena creato
        $record = static::query()->create($options);

        //Campi del modello per il log delle attività
        $field = $model->activityLog['field'] ?? null;

        $type = $model->activityLog['objectTypeField'] ?? null;
        $objectTypology = $model->activityLog['objectType'] ?? null;

        //Per gli archivi con più tipologie setto i campi e le informazioni necessarie al log delle attività
        if ($type) {
            $typology = $record->$type;
            $objectType = $record->$objectTypology;
            $field = $model->activityLog[$objectType] ?? null;
            $model->objectName = $model->objectName . ' [' . $typology . ']';
        }

        //Campi delle relazioni necessari per il log delle attività
        if (is_array($field)) {
            $relationship = $field[0];
            $relatedObj = $record->$relationship;
            $label = $relatedObj[$field[1]] ?? '';
        } else {
            $label = (!empty($model->encrypted) && in_array($field, $model->encrypted)) ? checkDecrypt($options[$field]) : $options[$field];
        }

        // Dati per registrazione ActivityLog e Versioning
        $getIdentity = authPatOs()->getIdentity(['id']);

        if(is_cli()){
            $userId = !empty($userId) ? $userId : 1;
        }else{
            if ($userId !== null) {
                $userId = $getIdentity['id'] ?? 1;
            }
        }


        // Storage Activity log
        ActivityLog::create([
            'user_id' => $userId,
            'institution_id' => !empty($options['institution_id']) ? $options['institution_id'] : 0,
            'action' => 'Aggiunta Oggetto',
            'action_type' => $model->activityLog['create'] ?? 'addObjectInstance',
            'description' => 'Creazione ' . $model->objectName . ' (ID ' . $record->id . ') <br>'
                . (!empty($field) ? 'Istanza: ' . $label : ''),
            'request_post' => [
                'post' => @$_POST,
                'get' => Input::get(),
                'server' => Input::server(),
            ],
            'object_id' => $model->objectId,
            'record_id' => $record->id,
            'area' => $model->activityLog['area'] ?? 'object',
            'platform' => $model->activityLog['platform'] ?? null
        ]);

        //POST INSERT
        if (method_exists($model, 'postInsert')) {
            $model->postInsert($record);
        }

        return $record;
    }

    /**
     * @description Funzione update custom. Estende la funzione update standard
     * @param object   $element    Record da aggiornare
     * @param array    $attributes Dati da aggiornare
     * @param bool     $log        Indica se inserire il log o meno
     * @param int|null $userId     identificativo utente
     * @return int
     * @throws Exception
     */
    public function updateWithLogs(object $element, array $attributes = [], bool $log = true, int|null $userId = null): int
    {
        // PRE UPDATE
        if (method_exists($this, 'preUpdate')) {
            $this->preUpdate($element, $attributes);
        }

        //Campi del modello per il log delle attività
        if (!empty($element)) {
            $type = $this->activityLog['objectTypeField'] ?? null;
            $objectTypology = $this->activityLog['objectType'] ?? null;
            $field = $this->activityLog['field'] ?? null;
//            $label = $attributes[$field] ?? '';

            //Per gli archivi con più tipologie
            if ($type) {
                $typology = $element->$type;
                $objectType = $element->$objectTypology;
                $this->objectName = $this->objectName . ' [' . $typology . ']';
                $field = $this->activityLog[$objectType] ?? null;
            }

            if (is_array($field)) {
                $relatedObj = optional($this::with($field[0])->first())->toArray();
                $label = $relatedObj[$field[1]] ?? '';
            } else {
                $label = (!empty($this->encrypted) && in_array($field, $this->encrypted)) ? checkDecrypt($attributes[$field]) : $attributes[$field];
            }
        }

        $v = $element->toArray();

        //Update dell'elemento
        $id = $element->update($attributes);

        if ($log) {
            // Dati per registrazione ActivityLog e Versioning
            $getIdentity = authPatOs()->getIdentity(['id']);

            // Storage Activity log
            ActivityLog::create([
                'user_id' => $userId !== null ? $userId : $getIdentity['id'],
                'action' => 'Modifica Oggetto',
                'action_type' => 'updateObjectInstance',
                'description' => 'Modifica ' . $this->objectName . ' (ID ' . $element->id . ')' .
                    (!empty($field) ? '<br>Istanza: ' . $label : ''),
                'request_post' => [
                    'post' => Input::stream() !== null ? @file_get_contents('php://input') : @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'object_id' => $this->objectId,
                'record_id' => $element->id,
                'area' => $this->activityLog['area'] ?? 'object'
            ]);
        }

        // POST UPDATE
        if (method_exists($this, 'postUpdate')) {
            $this->postUpdate($element);
        }

        return $id;

    }

    /**
     * @description Funzione delete custom. Estende la funzione delete standard
     * @param Model $element Record da eliminare
     * @return void
     * @throws Exception
     */
    public function deleteWithLogs(Model &$element): void
    {
        // PRE DELETE
        if (method_exists($this, 'preDelete')) {
            $this->preDelete($element);
        }

        $type = $this->activityLog['objectTypeField'] ?? null;
        $objectTypology = $this->activityLog['objectType'] ?? null;
        $field = $this->activityLog['field'] ?? null;

        //Per gli archivi con più tipologie
        if ($type) {
            $typology = $element[$type];
            $objectType = $element[$objectTypology];
            $this->objectName = $this->objectName . ' [' . $typology . ']';
            $field = $this->activityLog[$objectType] ?? null;
        }

        //Campi delle relazioni per il log delle attività
        if (is_array($field)) {
            $relatedObj = optional($this::with($field[0])->first())->toArray();
            $label = $relatedObj[$field[1]] ?? '';
        } else {
            $label = (!empty($this->encrypted) && in_array($field, $this->encrypted)) ? checkDecrypt($element[$field]) : $element[$field];
        }

        //Eliminazione elemento
        $saved = $element->delete();

        if ($saved) {

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id']);

            // Storage Activity log
            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => 'Eliminazione Oggetto',
                'action_type' => 'deleteObjectInstance',
                'description' => 'Eliminazione ' . $this->objectName . ' (ID ' . $element->id . ')'
                    . (!empty($field) ? '<br>Istanza: ' . $label : ''),
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'object_id' => $this->objectId,
                'record_id' => $element->id,
                'area' => $this->activityLog['area'] ?? 'object'
            ]);

            // POST DELETE
            if (method_exists($this, 'postDelete')) {
                $this->postDelete($element);
            }

            // Lista degli allegati
            $attachs = AttachmentsModel::withoutGlobalScope(DeletedScope::class)
                ->select(['id', 'file_name'])
                ->where('archive_name', $this->archiveName)
                ->where('archive_id', $element->id)
                ->get();

            //Path per l'eliminazione degli allegati dell'elemento
            $path = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $this->archiveName . DIRECTORY_SEPARATOR;

            if (!empty($attachs)) {
                //Eliminazione degli allegati dal file system
                foreach ($attachs as $fileAttach) {
                    $filePath = $path . $fileAttach['file_name'];

                    // Controllo se il file allegato esiste prima di eliminarlo dal file system
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }

                    //Elimino l'allegato dal database
                    $fileAttach->delete();
                }
            }

            //Rimuovo i dati dell'elemento da eventuali tabelle di pivot settate nel modello
            if (!empty($this->relationshipsToDelete)) {
                foreach ($this->relationshipsToDelete as $relationship) {
                    $element->$relationship()->detach();
                }
            }
        }
    }

    /**
     * @description Funzione toArray - Esclude il campo updated_at dalle tabelle di relazione
     * @return array
     */
    public function toArray(): array
    {
        $attributes = $this->attributesToArray();
        $attributes = array_merge($attributes, $this->relationsToArray());
        unset($attributes['pivot']['updated_at']);
        unset($attributes['pivot']['created_at']);
        return $attributes;
    }
}
