<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Security;

use Model\AclProfilesModel;
use Model\SectionsBoModel;
use Model\SectionsFoModel;
use System\Input;
use System\JsonResponse;
use System\Registry;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Classe per la gestione dei permessi degli utenti in base ai profili Acl che ha associati
 */
class Acl
{
    private mixed $profiles = null;
    private mixed $acl = null;

    //Permessi sulle sezioni B.O.
    private array $keys = [
        'read' => 0,
        'create' => 0,
        'update' => 0,
        'delete' => 0,
        'send_notify_app_io' => 0
    ];

    private $records;

    //Permessi generali, non di sezione
    static public array $general = [
        'versioning' => 0,
        'archiving' => 0,
        'lock_user' => 0,
        'advanced' => 0,
        'export_csv' => 0,
        'scp' => 0
    ];

    /**
     * @param array|string|null $nameSpace Namespace del controller della sezione
     */
    public function __construct(array|string $nameSpace = null)
    {
        //Controllo se l'utente è un super admin oppure admin
        if (isSuperAdmin(true)) {

            // Se è superadmin setto tutti i permessi sulle sezioni = 1
            $this->keys = [
                'read' => 1,
                'create' => 1,
                'update' => 1,
                'delete' => 1,
                'send_notify_app_io' => 1
            ];

            //Se è superadmin setto tutti i permessi generali = 1
            self::$general = [
                'versioning' => 1,
                'archiving' => 1,
                'lock_user' => 1,
                'advanced' => 1,
                'export_csv' => 1,
                'editor_wishing' => 'expert',
                'scp' => 1
            ];

        } else {

            //Se non è super admin setto i permessi sulle sezioni e quelli generali
            $options = AuthPatOS()->getIdentity(['options']);

            if (!empty($options['options']['profiles'])) {
                $this->profiles = unserialize($options['options']['profiles']);
            }

            if ($nameSpace !== null) {

                $tmpName = explode('\\', $nameSpace);
                $className = end($tmpName);

                $sectionInfo = SectionsBoModel::select(['id'])->where('controller', $className)->first()->toArray();
                $sectionID = $sectionInfo['id'];

                $this->records = AclProfilesModel::whereIn('id', $this->profiles)->with(['permits' => function ($query) use ($sectionID) {
                    $query->select(['sections_bo_id', 'acl_profiles_id', 'create', 'read', 'update', 'delete', 'send_notify_app_io'])
                        ->where('sections_bo_id', $sectionID);
                }]);

                $this->records = $this->records->get()
                    ->toArray();

                if (!empty($this->records)) {

                    foreach ($this->records as $record) {

                        // Setto i permessi sulle sezioni
                        if (!empty($record['permits'])) {

                            foreach ($record['permits'][0] as $key => $value) {

                                if (array_key_exists($key, $this->keys)) {

                                    $this->keys[$key] = ($this->keys[$key] >= 1) ? 1 : $value;

                                }

                            }

                        }

                        // Permessi generali
                        $this->profileList($record);

                    }
                }

            } else {

                //Sono sulla dashboard o sul profilo utente, setto solo i permessi generali e non quelli di sezione
                $options = AuthPatOS()->getIdentity(['options']);

                if (!empty($options['options']['profiles'])) {
                    $profiles = unserialize($options['options']['profiles']);
                }

                $records = AclProfilesModel::whereIn('id', $profiles)
                    ->get()
                    ->toArray();

                if (!empty($records)) {

                    foreach ($records as $record) {
                        // Permessi generali
                        $this->profileList($record);
                    }
                }

            }

        }

        //Salvo i permessi nel Registro
        Registry::set('___patos___general___', self::$general);
        Registry::set('___patos___profiles___', $this->keys);

    }

    /**
     * @description Metodo che controlla se hai i permessi per una determinata sezione
     * @param string|null $className Nome del controller della sezione su cui effettuare il controllo dei permessi
     * @return false|int[]
     */
    public static function hasPermit(string $className = null): array|bool
    {
        if (isSuperAdmin(true)) {

            $data = [
                'institution_id' => 1,
                'sections_bo_id' => 1,
                'acl_profiles_id' => 1,
                'create' => 1,
                'read' => 1,
                'update' => 1,
                'delete' => 1,
                'send_notify_app_io' => 1,
            ];


        } else {
            $data = [
                'institution_id' => 0,
                'sections_bo_id' => 0,
                'acl_profiles_id' => 0,
                'create' => 0,
                'read' => 0,
                'update' => 0,
                'delete' => 0,
                'send_notify_app_io' => 0,
            ];

            $options = AuthPatOS()->getIdentity(['options']);

            if (!empty($options['options']['profiles'])) {
                $profiles = unserialize($options['options']['profiles']);
            }

            $query = SectionsBoModel::select(['id'])->where('controller', $className)->first();

            if (empty($query)) {

                return $data;

            }

            $sectionInfo = $query->toArray();
            $sectionID = $sectionInfo['id'];

            $queryAcl = AclProfilesModel::select(['id', 'institution_id'])->whereIn('id', $profiles)->with(['permits' => function ($query) use ($sectionID) {
                $query->select(['sections_bo_id', 'acl_profiles_id', 'create', 'read', 'update', 'delete', 'send_notify_app_io'])
                    ->where('sections_bo_id', $sectionID);
            }])->get();

            if (empty($queryAcl)) {

                return $data;

            }

            $records = $queryAcl->toArray();

            if (!empty($records)) {

                foreach ($records as $record) {

                    if ($data['institution_id'] < 1 && $record['institution_id'] >= 0) {

                        $data['institution_id'] = $record['institution_id'];

                    }

                    if (!empty($record['permits'])) {

                        foreach ($record['permits'] as $permit) {

                            if ($data['sections_bo_id'] < 1 && $permit['sections_bo_id'] >= 0) {

                                $data['sections_bo_id'] = $permit['sections_bo_id'];

                            }

                            if ($data['acl_profiles_id'] < 1 && $permit['acl_profiles_id'] >= 0) {

                                $data['acl_profiles_id'] = $permit['acl_profiles_id'];

                            }

                            if ($data['create'] < 1 && $permit['create'] >= 1) {

                                $data['create'] = $permit['create'];

                            }

                            if ($data['read'] < 1 && $permit['read'] >= 1) {

                                $data['read'] = $permit['read'];

                            }

                            if ($data['update'] < 1 && $permit['update'] >= 1) {

                                $data['update'] = $permit['update'];

                            }

                            if ($data['delete'] < 1 && $permit['delete'] >= 1) {

                                $data['delete'] = $permit['delete'];

                            }

                            if ($data['send_notify_app_io'] < 1 && $permit['send_notify_app_io'] >= 1) {

                                $data['send_notify_app_io'] = $permit['send_notify_app_io'];

                            }


                        }

                    }

                }

            }
        }
        return $data;
    }

    /**
     * Funzione che setta i permessi generali(presi dai profili Acl dell'utente)
     *
     * @param array $record Permessi Acl dell'utente
     * @return void
     */
    private function profileList(array $record): void
    {
        foreach (self::$general as $key => $value) {

            if(array_key_exists($key, $record)){
                self::$general[$key] = ($record[$key] >= 1) ? 1 : $value;
            }


        }
    }

    /**
     * Funzione chiamata nel caso in cui l'utente naviga nella Dashboard o nel Profilo Utente,
     * (quindi non vengono settati i permessi di sezione)
     * @return mixed
     */
    public static function notRun(): mixed
    {
        $v = new static();
        Registry::set('___patos___profiles___not_run__', true);
        return $v->acl;
    }

    /**
     * Funzione che setta il metodo della rotta(Read, Create, Update, Delete)
     *
     * @param $method array|string
     * @param bool $check  Usato per le chiamate Ajax
     * @return void
     */
    public function setRoute(array|string $method, bool $check = false): void
    {
        if (!is_array($method)) {
            $method = [$method];
        }
        Registry::set('___patos___profiles___method__', $method);

        //Per le chiamate Ajax
        if ($check) {
            if (Input::isAjax()) {
                if (!guard()) {
                    $json = new JsonResponse();
                    $bad = $json->bad();
                    $json->error('error', 'Non hai i permessi necessari.');
                    $json->setStatusCode($bad);
                    $json->response();
                }
            }
        }

    }

    /**
     * Funzione che restituisce i permessi dell'utente sulla sezione in cui si trova
     *
     * @return array
     */
    public function getProfiles(): array
    {
        return $this->keys;
    }

    /**
     * Funzione che restituisce il permesso di lettura che ha l'utente sulla sezione in cui si trova
     *
     * @return bool
     */
    public function getRead(): bool
    {
        return (bool)$this->keys['read'];
    }

    /**
     * Funzione che restituisce il permesso di creazione che ha l'utente sulla sezione in cui si trova
     *
     * @return bool
     */
    public function getCreate(): bool
    {
        return (bool)$this->keys['create'];
    }

    /**
     * Funzione che restituisce il permesso di modifica che ha l'utente sulla sezione in cui si trova
     *
     * @return bool
     */
    public function getUpdate(): bool
    {
        return (bool)$this->keys['update'];
    }

    /**
     * Funzione che restituisce il permesso di eliminazione che ha l'utente sulla sezione in cui si trova
     *
     * @return bool
     */
    public function getDelete(): bool
    {
        return (bool)$this->keys['delete'];
    }

    /**
     * Funzione che restituisce i permessi dell'utente sulla sezione in cui si trova
     *
     * @return bool|array
     */
    public function getCrud(): bool|array
    {
        foreach ($this->keys as $k => $v) {
            if (in_array($k, ['create', 'update', 'delete', 'read']) && empty($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Funzione che restituisce il permesso generale di versioning che ha l'utente
     *
     * @return bool
     */
    public static function getVersioning(): bool
    {
        return self::getGeneral('versioning');
    }

    /**
     * Funzione che restituisce il permesso generale di archiviazione che ha l'utente
     *
     * @return bool
     */
    public static function getArchiving(): bool
    {
        return self::getGeneral('archiving');
    }

    /**
     * Funzione che restituisce il permesso generale di blocco/sblocco degli utenti che ha l'utente
     *
     * @return bool
     */
    public static function getLockUser(): bool
    {
        return self::getGeneral('lock_user');
    }

    /**
     * Funzione che restituisce il permesso generale di modifica avanzata del profilo che ha l'utente
     *
     * @return bool
     */
    public static function getModifyProfile(): bool
    {
        return self::getGeneral('advanced');
    }

    /**
     * Funzione che restituisce il permesso generale di esportazione di CSV che ha l'utente
     *
     * @return bool
     */
    public static function getExportCsv(): bool
    {
        return self::getGeneral('export_csv');
    }


    /**
     * Funzione che restituisce il permesso generale specificato nel parametro
     *
     * @param string $typeName Nome del permesso da controllare
     * @return bool
     */
    private static function getGeneral(string $typeName): bool
    {
        $general = Registry::exist('___patos___general___')
            ? Registry::get('___patos___general___')
            : self::$general;
        return (bool)$general[$typeName];
    }

    /**
     * @return bool
     * @noinspection PhpUnused
     */
    public function getSendnotifyAppIo(): bool
    {
        return (bool)$this->keys['send_notify_app_io'];
    }

    /**
     * @description Metodo che controlla se hai i permessi per una determinata sezione del front-office
     * @param int|null $pageId Id della pagina
     * @return false|int[]
     */
    public static function hasPagePermit(?int $pageId = null): array|bool
    {
        //Se l'utente è super admin ha i permessi su tutte le sezioni
        if (isSuperAdmin(true)) {

            $data = [
                'sections_fo_id' => 1,
            ];

        } else {
            $data = [
                'sections_fo_id' => 0,
            ];

            $identity = AuthPatOS()->getIdentity();
            $options = $identity['options'];

            // Prendo i profili ACL dell'utente
            if (!empty($options['profiles'])) {
                $profiles = unserialize($options['profiles']);
            }

            $query = SectionsFoModel::select(['id', 'owner_id'])->where('id', $pageId)->first();

            if (empty($query)) {

                return $data;

            }

            $sectionInfo = $query->toArray();
            $sectionID = $sectionInfo['id'];

            //Prendo i permessi associati ai profili ACL dell'utente
            $queryAcl = AclProfilesModel::select(['id', 'institution_id', 'is_admin'])->whereIn('id', $profiles)->with(['permits' => function ($query) use ($sectionID) {
                $query->select(['sections_bo_id', 'sections_fo_id', 'acl_profiles_id', 'create', 'read', 'update', 'delete', 'send_notify_app_io'])
                    ->where('sections_fo_id', $sectionID);
            }])->get();

            if (empty($queryAcl)) {

                return $data;

            }

            $records = $queryAcl->toArray();

            if (!empty($records)) {

                foreach ($records as $record) {

                    //Se l'utente è un amministratore competo setto il permesso a true ed esco dal ciclo
                    if ($record['is_admin']) {
                        $data['sections_fo_id'] = 1;
                        break;
                    }

                    //Verifico se l'utente ha i permessi per gestire la pagina
                    if (!empty($record['permits'])) {

                        foreach ($record['permits'] as $permit) {

                            if ($data['sections_fo_id'] < 1 && $permit['sections_fo_id'] >= 0) {

                                $data['sections_fo_id'] = $permit['sections_fo_id'];

                            }
                        }

                    }
                }
            }
        }

        //Se la sezione è stata creata dall'utente, allora setto il permesso a true
        $data['owned'] = !empty($identity) ? ($identity['id'] == $sectionInfo['owner_id']) : null;
        return $data;
    }


    /**
     * Funzione che restituisce il permesso generale passato nel parametro
     *
     * @param string $name Nome del permesso generale da restituire
     * @return bool
     */
    public static function getAclProfileInfo(string $name = ''): bool
    {
        return self::getGeneral($name);
    }


    /**
     * Funzione che restituisce gli id dei profili acl
     *
     * @return array
     */
    public function getAclIds(): array
    {
        if(!empty($this->profiles)){
            return $this->profiles;
        }
        return array();
    }


}
