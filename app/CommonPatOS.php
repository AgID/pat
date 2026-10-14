<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use Helpers\FileSystem\Dir;
use Helpers\FileSystem\File;
use Helpers\OS;
use Model\SectionsFoModel;
use System\Input;
use System\Log;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('createdByCheckDeleted')) {

    /**
     * Funzione che mostra nel datatable se un utente è stato cancellato
     *
     * @param string|null $name Nome dell'utente cifrato
     * @param int|null $deleted Indica se l'utente è
     *                             stato eliminato o meno
     * @return string
     */
    function createdByCheckDeleted(string|null $name = null, int|null $deleted = 0): string
    {
        $html = '';

        if ($deleted === 1) {

            $html .= '<span class="badge badge-danger" aria-label="' . checkDecrypt($name) . ': Utente eliminato" data-toggle="tooltip" data-placement="top" data-original-title="Utente Eliminato">';
        } else {

            $html .= '<span class="badge badge-success">';
        }

        if ($name !== null) {

            $html .= escapeXss(checkDecrypt($name));
        } else {
            $html .= '';
        }

        if ($deleted === 1) {

            $html .= nbs(2) . '<i class="fas fa-user-times"></i></span>';
        } else {

            $html .= nbs(2) . '</span>';
        }

        return $html;
    }
}

if (!function_exists('getSections')) {
    function getSections($sectionId = null): mixed
    {
        $sections = SectionsFoModel::select(['id', 'name'])->where('parent_id', $sectionId)->get();
        return $sections->isEmpty() ? [] : $sections->toArray();
    }
}

if (!function_exists('getSectionPagesBackOffice')) {

    /**
     * Funzione che restituisce il menù laterale di sinistra e la sua alberatura
     *
     * @return array
     */
    function getSectionPagesBackOffice()
    {
        $sections = new \System\Hierarchy();
        return $sections->getGroupedChildren();
    }
}
