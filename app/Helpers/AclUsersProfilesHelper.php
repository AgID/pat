<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('badgeAclUserProfile')) {
    /**
     * Funzione che genera in modo ricorsivo iniettando il codice all'interno della funzione
     * l'instanza della classe preposta alla generazione del file CSV.
     */
    function recursiveAddRowCsvAclUserProfile($object, $data = [], $permits = false)
    {
        if (!empty($data)) {

            foreach ($data as $d) {

                $modify = 'No';

                if (is_numeric($d['id'])) {
                    if (!empty($permits)) {

                        $itemArray = multiSearch($permits, [
                                'sections_fo_id' => $d['id']
                            ]
                        );

                        if (!empty($itemArray)) {

                            $modify = 'Si';

                        }
                    }
                }

                $object->addRow([
                    str_repeat(' ', $d['deep'] * 3) . $d['text'],
                    'Modifica (' . $modify . ')',
                    null,
                    null,
                    null
                ]);

                if (!empty($d['children'])) {
                    recursiveAddRowCsvAclUserProfile($object, $d['children'], $permits);
                }

            }
        }

    }
}

if (!function_exists('badgeAclUserProfile')) {
    /**
     * Funzione che genera uno snippet html
     * l'instanza della classe preposta alla generazione del file CSV.
     */
    function badgeAclUserProfile($string = null)
    {
        $tagOpen = '<strong class="text ' . (($string === 'Si') ? 'text-primary' : 'text-danger') . ' text-wrap" >';
        $content = ($string === 'Si') ? '<i class="fas fa-check"></i>' : '<i class="fas fa-ban"></i>';
        $tagClose = '</strong>';

        return $tagOpen . $content . $tagClose;

    }
}

if (!function_exists('treeTableACL')) {
    /**
     * Creazione riche nelle tabelle di tipo tree
     *
     * @param $data
     */
    function treeTableACL($tree, $permits = false)
    {
        $html = '';
        foreach ($tree as $item) {

            $parentId = ($item['parent_id'] !== 0) ? ' data-node-pid="' . $item['parent_id'] . '"' : '';
            $displayStyleTagTr = ($item['parent_id'] !== 0) ? 'style="display:none"' : '';
            $expandStyleLeft = 'display: margin-left:' . (int)$item['deep'] * 20 . 'px;';
            $tmpName = $item['label'] ?? $item['name'];
            $name = setUpperCaseRowTable(escapeXss(convertEncodeQuotes($tmpName), true, false), false, ($item['parent_id'] === 0) ? true : false);

            $checked = ' ';
            if (!empty($permits)) {

                $itemArray = multiSearch($permits, [
                        'sections_fo_id' => $item['id']
                    ]
                );

                if (!empty($itemArray)) {

                    $checked = ' checked ';

                }
            }

            $html .= '<tr data-node-id="' . $item['id'] . '"' . $parentId . ' ' . $displayStyleTagTr . '>' . "\n";
            $html .= '<td style="width: 80%"><span class="tree-icon tree-closed" style="' . $expandStyleLeft . '"></span> &nbsp;' . $name . '</td>' . "\n";
            $html .= '<td style="width: 20%" class="text-center section_fo_td_' . $item['id'] . '">' . "\n";
            $html .= '<input name="section_fo[' . $item['id'] . ']" value="1"' . $checked . 'type="checkbox" class="sbo section_fo_' . $item['id'] . '">' . "\n";
            $html .= '<input type="hidden" name="name_fo[' . $item['id'] . ']" value="' . htmlEscape($item['name']) . '" type="checkbox" class="sbo_name name_fo_' . $item['id'] . '">' . "\n";
            $html .= '</td>' . "\n";
            $html .= '</tr>' . "\n";

            if ($item['children']) {
                $html .= treeTableACL($item['children'], $permits);
            }

        }

        return $html;
    }
}

if (!function_exists('treeTableReadOnlyACL')) {
    /**
     * Creazione righe nelle tabelle di tipo tree
     *
     * @param $tree
     * @param bool $permits
     * @return string
     */
    function treeTableReadOnlyACL($tree, $permits = false)
    {
        $html = '';
        foreach ($tree as $item) {

            $parentId = ($item['parent_id'] !== 0) ? ' data-node-pid="' . $item['parent_id'] . '"' : '';
            $displayStyleTagTr = ($item['parent_id'] !== 0) ? 'style="display:none"' : '';
            $expandStyleLeft = 'display: margin-left:' . (int)$item['deep'] * 20 . 'px;';
            $tmpName = $item['label'] ?? $item['name'];
            $name = setUpperCaseRowTable($tmpName, false, ($item['parent_id'] === 0) ? true : false);

            $checked = 'No';
            if (!empty($permits)) {

                $itemArray = multiSearch($permits, [
                        'sections_fo_id' => $item['id']
                    ]
                );

                if (!empty($itemArray)) {

                    $checked = 'Si';

                }
            }

            $html .= '<tr data-node-id="' . $item['id'] . '"' . $parentId . ' ' . $displayStyleTagTr . '>' . "\n";
            $html .= '<td style="width: 80%"><span class="tree-icon tree-closed" style="' . $expandStyleLeft . '"></span> &nbsp;' . $name . '</td>' . "\n";
            $html .= '<td style="width: 20%" class="text-center section_fo_td_' . $item['id'] . '">' . "\n";
            $html .= badgeAclUserProfile($checked);
            $html .= '</td>' . "\n";
            $html .= '</tr>' . "\n";

            if ($item['children']) {
                $html .= treeTableReadOnlyACL($item['children'], $permits);
            }

        }

        return $html;
    }
}
