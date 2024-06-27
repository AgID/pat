<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

use Exception;
use System\Database;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class SectionFrontOffice
{
    protected Database $DB;
    protected $primary_key;
    protected $parent;
    protected $parent_id;
    protected $lineage;
    protected $deep;
    protected $created_at;
    protected $updated_at;
    protected $deleted_at;
    protected $deleted;
    protected $parent_id_default;
    protected $padding_count;
    protected $padding_string;
    protected $has_sort;
    protected $institution_id;
    protected $is_system;
    protected $hide;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->DB = new Database();
        $config = config('front_office', null, 'hierarchy');

        foreach ($config as $key => $val) {

            $this->$key = $val;

        }

        $this->parent_id_default = $config['parent_id_default'];
        $this->has_sort = $config['has_sort'];
    }

    /**
     * Inserisce un nuovo record. Se non è incluso parent_id, presuppone un elemento di primo livello
     *
     * @param array $data       Dati da inserire
     * @param bool  $includeSys Indica se includere le sezioni di sistema
     * @return int
     */
    public function insert(array $data, bool $includeSys = true): int
    {
        if (!empty($data[$this->parent_id])) {

            $parent = $this->getOne($data[$this->parent_id], $includeSys, $data[$this->institution_id]);

            $data[$this->deep] = $parent[$this->deep] + 1;
            $data[$this->sort] = $this->getCountChildren($data[$this->parent_id]) + 1;

        } else {

            $data[$this->parent_id] = $this->parent_id_default;
            $data[$this->deep] = 1;

        }

        if (isSuperAdmin(true) && !empty($data['institution_id'])) {

            $data[$this->institution_id] = $data['institution_id'];

        } else {

            $data[$this->institution_id] = checkAlternativeInstitutionId();

        }

        $data[$this->created_at] = date('Y-m-d H:i:s');

        $insertId = $this->DB::table($this->table)
            ->insertGetId($data);

        $update[$this->lineage] = (empty($parent[$this->lineage]))
            ? str_pad($insertId, $this->padding_count, $this->padding_string, STR_PAD_LEFT)
            : $parent[$this->lineage] . '-' . str_pad($insertId, $this->padding_count, $this->padding_string, STR_PAD_LEFT);

        return $this->update($insertId, $update, $data[$this->institution_id]);
    }

    /**
     * Updates record
     *
     * @param int      $id            Id del record da aggiornare
     * @param array    $data          Dati per fare l'update
     * @param int|null $institutionId Id dell'Ente
     * @return int
     */
    public function update(int $id, array $data, int $institutionId = null): int
    {
        $institutionId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $query = $this->DB::table($this->table);
        $query->where($this->primary_key, $id);
        $query->where($this->institution_id, $institutionId);
        $query->update($data);
        return $id;
    }

    /**
     * @description Recupera un singolo record in base alla chiave primaria.
     * @param int|null  $id            Id del record da recuperare
     * @param bool|null $includeSys    Indica se prendere anche le sezioni di sistema
     * @param int|null  $institutionId Id dell'ente
     * @return array
     */
    public function getOne(?int $id = null, ?bool $includeSys = true, ?int $institutionId = null): array
    {
        $eId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $query = $this->DB::table($this->table);
        $query->where($this->primary_key, $id);
        $query->where($this->deleted, '=', 0);

        if ($includeSys) {
            $query->where(function ($query) use ($eId) {
                $query->where($this->is_system, 1)->orWhere($this->institution_id, $eId);
            });
        }
        $row = $query->first();
        return (array)$row;
    }

    /**
     * Reinizializzazione alberatura
     * @return void
     */
    function resync(): void
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

                    $parent = $this->getOne($row[$this->parent_id], true, $row[$this->institution_id]);
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
     * @description Recupera tutti i record in base alla chiave primaria, ordinati in base alla loro discendenza e ordine.
     * @param int        $topId             Id padre di partenza
     * @param int|null   $institutionId     Id dell'Ente
     * @param int|null   $institutionTypeId Id del tipo Ente
     * @return array
     */
    public function get(int $topId = 0, ?int $institutionId = null, ?int $institutionTypeId = null): array
    {
        $eId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $query = $this->DB::table($this->table);

        $parent = $this->getOne($topId, true, $eId);

        if (!empty($parent)) {

            $query->where($this->lineage, 'like', $parent[$this->lineage] . '%');

        }

        $query->where($this->deleted, '=', 0);
        $query->where($this->hide, '=', 0);

        $query->where(function ($query) use ($eId) {
            $query->where($this->is_system, 1)->orWhere($this->table . '.' . $this->institution_id, $eId);
        });

        $query->whereNotIn($this->table . '.' . $this->primary_key);

        // Aggiunto per le traduzioni dei nomi delle sezioni in base al tipo dell'Ente
        if (!empty($institutionTypeId)) {
            $query->leftJoin('rel_institution_type_sections_labeling', function ($join) use ($institutionTypeId, $eId) {
                $join->on('rel_institution_type_sections_labeling.sections_id', '=', 'section_fo.id')
                    ->where(function ($query) use ($institutionTypeId, $eId) {
                        $query->where('rel_institution_type_sections_labeling.institution_type_id', '=', $institutionTypeId)
                            ->whereNull('rel_institution_type_sections_labeling.institution_id');
                        $query->orWhere(function ($query) use ($eId) {
                            $query->where('rel_institution_type_sections_labeling.institution_id', '=', $eId)
                                ->whereNull('rel_institution_type_sections_labeling.institution_type_id');
                        });
                    });
            });

            $query->select($this->table . '.*');
        }

        $query->orderBy($this->lineage);

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();
    }

    /**
     * @description Recupera tutti i record discendenti in base all'ID padre, ordinati in base alla loro discendenza
     * e raggruppali come un array multidimensionale.
     * @param int        $topId             Id del record di partenza
     * @param string     $parentId          Id del padre
     * @param int|null   $institutionId     Id dell'Ente
     * @param int|null   $institutionTypeId Id del tipo Ente
     * @return array
     */
    public function getGroupedChildren(int $topId = 0, string $parentId = '#', ?int $institutionId = null, ?int $institutionTypeId = null): array
    {
        $result = $this->get($topId, $institutionId, $institutionTypeId);
        return $this->findChildren($result, $parentId);
    }

    /**
     * @description Recupera tutti i record figli diretti in base all'ID padre, ordinati in base alla loro discendenza
     * @param int $parentId Id del record padre
     * @return array
     */
    public function getChildren(int $parentId = 0): array
    {
        $query = $this->DB::table($this->table);
        $query->where($this->parent_id, $parentId);
        $query->where($this->institution_id, checkAlternativeInstitutionId());
        $query->orderBy($this->lineage);

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();

    }

    /**
     * @param int $id Id del record di cui prendere i figli
     * @return array
     */
    public function getChildrens(int $id): array
    {

        $result = $this->getOne($id);

        if (empty($result)) {

            return [];

        }

        return $this->DB::table($this->table)
            ->where($this->institution_id, checkAlternativeInstitutionId())
            ->where($this->lineage, 'like', $result[$this->lineage] . '%')
            ->get()
            ->toArray();
    }

    /**
     * @description Conta tutti i record figlio diretti in base all'ID padre
     * @param int      $id       Id del record di cui prendere i figli
     * @param bool|int $parentId Id del record padre
     * @return int
     */
    public function getCountChildren(int $id = 0, bool|int $parentId = true): int
    {
        $column = ($parentId === true) ? $this->parent_id : $this->primary_key;
        return $this->DB::table($this->table)
            ->distinct()
            ->where($column, $id)
            ->where($this->institution_id, checkAlternativeInstitutionId())
            ->count($this->primary_key);
    }

    /**
     * @description Recupera tutti i record discendenti in base all'ID padre, ordinati in base alla loro discendenza.
     * @param int      $parentId      Id del record padre
     * @param int|null $institutionId Id dell'Ente
     * @return array
     */
    public function getDescendents(int $parentId = 0, ?int $institutionId = null): array
    {
        $eId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $parent = $this->getOne($parentId, true, $eId);

        if (empty($parent)) {

            return [];

        }

        $query = $this->DB::table($this->table);
        $query->where($this->institution_id, $eId);
        $query->where($this->lineage, 'like', $parent[$this->lineage] . '%');
        $query->orderBy($this->lineage);

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();
    }

    /**
     * @description Recupera tutti i record degli antenati in base all'id, ordinati in base alla colonna
     * "lineage" (dall'alto verso il basso).
     * @param int      $id                Id del record di cui recuperare gli antenati
     * @param bool     $removeThis        Indica se rimuovere l'elemento corrente dall'alberatura restituita
     * @param bool     $includeSys        Indica se includere le sezioni di sistema
     * @param int|null $institutionId     Id dell'Ente
     * @return array
     */
    public function getAncestors(int $id = 0, bool $removeThis = false, bool $includeSys = true, ?int $institutionId = null): array
    {
        $eId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $current = $this->getOne($id, $includeSys, $eId);

        if (empty($current)) {

            return [];

        }

        $lineageIds = explode('-', $current[$this->lineage]);
//        trace($lineageIds, true);

        if ($removeThis) {

            unset($lineageIds[count($lineageIds) - 1]);

        }

        $query = $this->DB::table($this->table);
        $query->where(function ($query) use ($eId) {
            $query->where($this->is_system, 1)->orWhere($this->table . '.' . $this->institution_id, $eId);
        });
        //$query->where($this->institution_id, checkAlternativeInstitutionId());

        $query->whereIn($this->table . '.' . $this->primary_key, $lineageIds);

        // Aggiunto per le traduzioni dei nomi delle sezioni in base al tipo dell'Ente
        if (!empty($institutionTypeId)) {
            $query->leftJoin('rel_institution_type_sections_labeling', function ($join) use ($lineageIds, $institutionTypeId, $eId) {
                $join->on('rel_institution_type_sections_labeling.sections_id', '=', 'section_fo.id')
                    ->where(function ($query) use ($institutionTypeId, $eId, $lineageIds) {
                        $query->where(function ($query) use ($institutionTypeId, $lineageIds) {
                            $query->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
                            $query->whereNull("rel_institution_type_sections_labeling.institution_id")
                                ->whereIn('rel_institution_type_sections_labeling.sections_id', $lineageIds);
                        })
                            ->orWhere(function ($query) use ($eId, $lineageIds) {
                                $query->where("rel_institution_type_sections_labeling.institution_id", "=", $eId);
                                $query->whereNull("rel_institution_type_sections_labeling.institution_type_id")
                                    ->whereIn('rel_institution_type_sections_labeling.sections_id', $lineageIds);
                            });
                    });

            });

            $query->orWhere('rel_institution_type_sections_labeling.institution_type_id', '=', $institutionTypeId);
            $query->select($this->table . '.*');
            $query->orderBy($this->lineage);
            $query->orderBy('l_i_id', 'DESC');

        } else {
            $query->orderBy($this->lineage);

        }

        if ($this->has_sort) {

            $query->orderBy($this->sort, 'ASC');

        }

        return $query->get()->toArray();
    }


    /**
     * @description Recupera il genitore del record in base all'id
     * @param int $id Id del record di cui recuperare il padre
     * @return array
     */
    public function getParent(int $id = 0): array
    {
        $current = $this->getOne($id);
        if (empty($current)) {
            return [];
        }

        return $this->DB::table($this->table)
            ->where($this->primary_key, $current[$this->parent_id])
            ->where($this->institution_id, checkAlternativeInstitutionId())
            ->first()
            ->toArray();
    }

    /**
     * @description Ottiene la profondità massima di qualsiasi ramo di albero
     * @return int
     */
    public function maxDeep(): int
    {
        $row = $this->DB::select("select max(`{$this->deep}`) AS max_deep from {$this->table}");
        return $row[0]->max_deep + 1;
    }

    /**
     * @description Cancella tutti i records
     * @param int  $id           del record da eliminare
     * @param bool $withChildren Indica se eliminare anche i figli
     * @return void
     */
    public function delete(int $id, bool $withChildren = false): void
    {
        $parent = false;

        if ($withChildren) {
            $parent = $this->getOne($id);
        }

        $query = $this->DB::table($this->table);

        $query->where('id', '=', $id);
        //$query->where($this->institution_id, checkAlternativeInstitutionId());

        if (!empty($parent) && $withChildren) {

            $query->orWhere($this->lineage, 'like', $parent[$this->lineage] . '%');

        }

        $query->delete();

    }

    /**
     * Metodo ricorsivo per ricostruire l'alberatura dei records
     *
     * @param array|null $nodeList Alberatura aggiornata ricorsivamente
     * @param int|string $parentId Id padre di cui si vogliono recuperare i figli, aggiornato ricorsivamente
     * @return array
     */
    public function findChildren(?array &$nodeList, int|string $parentId = '#'): array
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
