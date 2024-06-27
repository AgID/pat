<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use Illuminate\Database\Eloquent\Collection;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('treeHtmlStructures')) {
    /**
     * Creazione del codice HTML degli elementi per la struttura ad albero dell'organigramma delle strutture
     *
     * @param array $tree Array delle strutture
     * @return string
     * @throws Exception
     */
    function treeHtmlStructures(array $tree): string
    {
        $html = '';
        foreach ($tree as $item) {

            if (!empty($item) && is_array($item) && count($item) > 1) {
                $parentId = (!empty($item['structure_of_belonging_id']) && $item['structure_of_belonging_id'] !== null)
                    ? ' data-node-pid="' . $item['structure_of_belonging_id'] . '"'
                    : '';

                $name = !empty($item['structure_name']) ? escapeXss($item['structure_name']) : '';

                $html .= '<ul>' . "\n";
                $html .= '<li><a href="' . siteUrl('page/40/details/' . $item['id'] . '/' . urlTitle($name)) . '">' . $name . '</a></li>' . "\n";

                if (!empty($item['children']) && is_array($item['children'])) {
                    $html .= treeHtmlStructures($item['children']);
                }
                $html .= '</ul>' . "\n";
            }

        }

        return $html;
    }
}

if (!function_exists('treeStructures')) {
    /**
     * Creazione della struttura ad albero delle strutture per l'organigramma.
     * Si basa sul parent_id(structure_og_belong_id)
     *
     * @param array|Collection $organigram Array strutture
     * @return array
     */
    function treeStructures(Collection|array $organigram): array
    {
        $refs = [];
        $list = [];

        // Ciclo sulle strutture e creo l'alberatura
        foreach ($organigram as $row) {
            $ref = &$refs[$row['id']];

            $ref['structure_of_belonging_id'] = $row['structure_of_belonging_id'];
            $ref['structure_name'] = escapeXss($row['structure_name'], true, false);
            $ref['id'] = $row['id'];

            if ($row['structure_of_belonging_id'] == 0) {
                $list[$row['id']] = &$ref;
            } else {
                $refs[$row['structure_of_belonging_id']]['children'][$row['id']] = &$ref;
            }
        }

        return $list;
    }
}
