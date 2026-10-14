<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Hierarchy
{
    protected Database $DB;
    protected string $primary_key;
    protected string $parent_id;
    protected string $lineage;
    protected string $deep;
    protected string $institution_id;
    protected string $is_system;
    protected string $deleted;
    protected string $hide;
    protected string $created_at;
    protected string $updated_at;
    protected string $deleted_at;
    protected int $parent_id_default;
    protected int $padding_count;
    protected string $padding_string;
    protected bool $has_sort;
    private ?array $identity = null;
    protected array $config;
    protected string $table;
    protected string $sort;

    public function __construct($field = 'back_office')
    {
        $this->DB = new Database();
        $this->config = config($field, null, 'hierarchy');

        foreach ($this->config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        $this->parent_id_default = $this->config['parent_id_default'];
        $this->has_sort = $this->config['has_sort'];
        $this->identity = authPatOs()->getIdentity();
    }

    public function getConfigValue($key)
    {
        return $this->config[$key] ?? null;
    }

    /**
     * Inserisce un nuovo record. Se non è incluso parent_id, presuppone un elemento di primo livello
     *
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        if (!empty($data[$this->parent_id])) {

            $parent = $this->getOne($data[$this->parent_id]);
            $data[$this->deep] = $parent[$this->deep] + 1;
            $data[$this->sort] = (int)$this->getCountChildren($data[$this->parent_id]) + 1;

        } else {

            $data[$this->parent_id] = $this->parent_id_default;
            $data[$this->deep] = 1;
            $data[$this->sort] = 1;

        }

        $data[$this->created_at] = date('Y-m-d H:i:s');

        $insertId = $this->DB::table($this->table)
            ->insertGetId($data);

        $update[$this->lineage] = (empty($parent[$this->lineage]))
            ? str_pad($insertId, $this->padding_count, $this->padding_string, STR_PAD_LEFT)
            : $parent[$this->lineage] . '-' . str_pad($insertId, $this->padding_count, $this->padding_string, STR_PAD_LEFT);

        return $this->update($insertId, $update);

    }

    /**
     * Updates record
     *
     * @param $id
     * @param $data
     * @return int
     */
    public function update($id, $data)
    {
        $this->DB::table($this->table)
            ->where($this->primary_key, $id)
            ->update($data);

        return (int)$id;
    }

    /**
     * Recupera un singolo record in base alla chiave primaria.
     *
     * @param null $id
     * @return array
     */
    public function getOne($id = null)
    {
        $row = $this->DB::table($this->table)
            ->where($this->primary_key, $id)
            ->first();

        return (array)$row;
    }

    /**
     * Reinizializzazione alberatura
     */
    function resync()
    {
        $brother = false;

        $q = $this->DB::table($this->table);
        $q->orderBy($this->parent_id, 'ASC');

        if ($this->has_sort) {

            $q->orderBy($this->sort, 'ASC');

        }

        $currentData = $q->get()->toArray();

        if (!empty($currentData)) {
            $i = 1;
            foreach ($currentData as $row) {

                $row = (array)$row;
                $update[$this->deep] = 0;

                if (!empty($row[$this->parent_id])) {

                    $parent = $this->getOne($row[$this->parent_id]);
                    $update[$this->deep] = $parent[$this->deep] + 1;

                    if ($brother != $row[$this->parent_id]) {

                        $brother = $row[$this->parent_id];
                        $i = 1;

                    }

                }

                $update[$this->sort] = $i;

                $update[$this->lineage] = (empty($parent[$this->lineage]))
                    ? str_pad($row[$this->primary_key], $this->padding_count, $this->padding_string, STR_PAD_LEFT)
                    : $parent[$this->lineage] . '-' . str_pad($row[$this->primary_key], $this->padding_count, $this->padding_string, STR_PAD_LEFT);
                $this->update($row[$this->primary_key], $update);
                unset($parent);

                $i++;
            }

        }

    }

    /**
     * Recupera tutti i record in base alla chiave primaria, ordinati in base alla loro discendenza ed ordine.
     *
     * @param int $topId
     * @param int|null $institutionId
     * @param bool $onlySystem
     * @return array
     */
    public function get(int $topId = 0, int $institutionId = null, bool $onlySystem = false): array
    {
        $query = $this->DB::table($this->table);

        if (!empty($topId)) {
            $parent = $this->getOne($topId);
            if (!empty($parent)) {
                $query->where($this->lineage, 'like', $parent[$this->lineage] . '%');
            }
        }

        $query->where($this->deleted, '=', 0);
        $query->where($this->hide, '=', 0);

        if (!empty($institutionId)) {
            $query->where(function ($query) use ($institutionId, $onlySystem) {
                $query->where($this->is_system, 1);

                if(empty($onlySystem)) {
                    $query->orWhere($this->table.'.'.$this->institution_id, $institutionId);
                }
            });
        }

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        $query->orderBy($this->lineage);

        return $query->get()->toArray();
    }

    /**
     * Recupera tutti i record discendenti in base all'ID padre, ordinati in base alla loro discendenza
     * e raggruppali come un array multidimensionale.
     *
     * @param int $top_id
     * @return array
     */
    public function getGroupedChildren($top_id = 0, $institutionId = null, bool $onlySystem = false)
    {

        $result = $this->get($top_id, $institutionId, $onlySystem);

        return $this->findChildren($result);
    }

    /**
     * Recupera tutti i record figlio diretti in base all'ID padre, ordinati in base alla loro discendenza
     *
     * @param int $parentId
     * @return array
     */
    public function getChildren($parentId = 0)
    {
        $query = $this->DB::table($this->table);
        $query->where($this->parent_id, $parentId);
        $query->orderBy($this->lineage);

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();

    }

    /**
     * Conta tutti i record figlio diretti in base all'ID padre
     *
     * @param int $parentId
     * @return array
     */
    public function getCountChildren($id = 0, $parentId = true)
    {
        $column = ($parentId === true) ? $this->parent_id : $this->primary_key;
        // $this->DB::enableQueryLog();
        return $this->DB::table($this->table)
            ->distinct()
            ->where($column, $id)
            ->count($this->primary_key);
        //  trace($this->DB::getQueryLog(),true);
    }

    /**
     * Recupera tutti i record discendenti in base all'ID padre, ordinati in base alla loro discendenza.
     *
     * @param int $parentId
     * @return array
     */
    public function getDescendents($parentId = 0)
    {
        $parent = $this->getOne($parentId);

        if (empty($parent)) {

            return [];

        }

        $query = $this->DB::table($this->table);
        $query->where($this->lineage, 'like', $parent[$this->lineage] . '%');
        $query->orderBy($this->lineage);

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();
    }

    /**
     * Recupera tutti i record degli antenati in base all'id, ordinati in base al colonna
     * "lineage" (dall'alto verso il basso).
     *
     * @param $id
     * @param false $removeThis
     * @return array
     */
    public function getAncestors($id = 0, $removeThis = false)
    {
        $current = $this->getOne($id);

        if (empty($current)) {

            return [];

        }

        $lineageIds = explode('-', $current[$this->lineage]);

        if ($removeThis) {

            unset($lineageIds[count($lineageIds) - 1]);

        }

        $query = $this->DB::table($this->table);
        $query->whereIn($this->primary_key, $lineageIds);
        $query->orderBy($this->lineage);

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();
    }


    /**
     * Recupera il genitore del record in base all'id
     *
     * @param int $id
     * @return array
     */
    public function getParent($id = 0)
    {
        $current = $this->getOne($id);
        if (empty($current)) {
            return [];
        }

        return $this->DB::table($this->table)
            ->where($this->primary_key, $current[$this->parent_id])
            ->first()
            ->toArray();
    }

    /**
     * Ottiene la profondità massima di qualsiasi ramo di albero
     *
     * @return int
     */
    public function maxDeep(): int
    {
        $row = $this->DB::select("select max(`{$this->deep}`) AS max_deep from {$this->table}");
        return $row[0]->max_deep + 1;
    }

    /**
     * Cancella tutti i records
     *
     * @param $id
     * @param false $withChildren
     */
    public function delete($id, $withChildren = false)
    {
        $parent = false;

        if ($withChildren) {
            $parent = $this->getOne($id);
        }

        $query = $this->DB::table($this->table);

        $query->where('id', '=', $id);

        if (!empty($parent) && $withChildren) {

            $query->orWhere($this->lineage, 'like', $parent[$this->lineage] . '%');

        }

        $query->delete();

    }

    /**
     * Metodo ricorsivo per ricotruire l'alberatura dei records
     *
     * @param $nodeList
     * @param null $parentId
     * @return array
     */
    public function findChildren(&$nodeList, $parentId = null)
    {
        $nodes = [];

        foreach ($nodeList as $node) {

            $node = (array)$node;

            if ($node[$this->parent_id] == $parentId) {

                $node['children'] = $this->findChildren($nodeList, $node[$this->primary_key]);
                $nodes[] = $node;

            }

        }

        return $nodes;
    }
}
