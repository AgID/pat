<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Funzione che restituisce il logo dell'ente se presente
 *
 * @param null $logoFile      Nome del file logo ente
 * @param null $shortName     Nome breve ente
 * @param null $noInstitution Per il testo alternativo, è settata a true quando la funzione non è utilizzata per il logo
 *                            dell'ente
 * @return string
 * @throws Exception
 */
function getLogoInstitution($logoFile = null, $shortName = null, $noInstitution = null): string
{
    $alt = (!empty($noInstitution)) ? 'Foto' : 'Logo ente';
    return !empty($logoFile) && !empty($shortName)
        ? '<div class="widget-user-image">
                    <img class="img-circle elevation-2" style="width: 40px; height: auto;" title="Logo Ente" ' .
        'src="' . baseUrl('media/' . $shortName . '/assets/images/' . $logoFile) . '" alt="' . $alt . '">' .
        '</div>'
        : '';
}
