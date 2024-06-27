<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [
    // Menu BackOffice
    'back_office' => [
        'table' => 'section_bo',
        'primary_key' => 'id',
        'name' => 'name',
        'parent_id' => 'parent_id',
        'lineage' => 'lineage',
        'deep' => 'deep',
        'sort' => 'sort',
        'hide' => 'hide',
        'deleted' => 'deleted',
        'is_system' => 'is_system',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'deleted_at' => 'deleted_at',
        'parent_id_default' => 0,
        'padding_count' => 6,
        'padding_string' => '0',
        'has_sort' => true
    ],

    // Menu Front Office
    'front_office' => [
        'table' => 'section_fo',
        'primary_key' => 'id',
        'name' => 'name',
        'parent_id' => 'parent_id',
        'lineage' => 'lineage',
        'institution_id' => 'institution_id',
        'is_system' => 'is_system',
        'hide' => 'hide',
        'deep' => 'deep',
        'sort' => 'sort',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'deleted_at' => 'deleted_at',
        'deleted' => 'deleted',
        'parent_id_default' => 0,
        'padding_count' => 6,
        'padding_string' => '0',
        'has_sort' => true
    ],

    //File Manager
    'file_manager' => [
        'table' => 'attachment_cats',
        'primary_key' => 'id',
        'name' => 'name',
        'parent' => 'parent_id',
        'lineage' => 'lineage',
        'deep' => 'deep',
        'sort' => 'sort',
        'institution_id' => 'institution_id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'deleted_at' => 'deleted_at',
        'parent_id_default' => 0,
        'padding_count' => 6,
        'padding_string' => '0',
        'has_sort' => true
    ]
];