<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Funzione che restituisce l'immagine associata all'oggetto
 * Usata per il personale, l'utente e le commissioni
 *
 * @param string|null $logoFile  Nome del file logo ente
 * @param string|null $shortName Nome breve ente
 * @param null        $altString Eventuale testo alternativo per l'immagine
 * @return string
 * @throws Exception
 */
function getImage(string $logoFile = null, string $shortName = null, $altString = null): string
{
    $alt = (!empty($altString)) ? $altString : 'Immagine generica';
    return !empty($logoFile) && !empty($shortName)
        ? '<img class="img-circle elevation-2" style="width: 40px; height: auto;" title="Logo" ' .
        'src="' . baseUrl('media/' . $shortName . '/assets/images/' . $logoFile) . '" alt="' . $alt . '">'
        : '';
}
