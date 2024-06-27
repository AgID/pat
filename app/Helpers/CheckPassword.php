<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Funzione che controlla se la password rispetta i criteri minimi di sicurezza:
 * -lunghezza minima di 14 caratteri (standard)
 * -lunghezza massima di 32 caratteri (standard)
 * -almeno un carattere speciale !(@#$%*-
 * -almeno una lettera maiuscola
 * -almeno un numero
 *
 * @param string|null $input Password da controllare
 * @param int         $min   Lunghezza minima che deve avere la password
 * @param int         $max   Lunghezza massima che deve avere la password
 * @return array|null
 * @throws Exception
 */
function checkPassword(string $input = null, int $min = 14, int $max = 32): ?array
{
    if (!preg_match('/^\S*(?=\S{' . $min . ',' . $max . '})(?=(?:.*[!(@#$%*-]){1,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $input)) {
        return [
            'error' => __('check_password_error', null, 'patos')
        ];
    }
    return null;
}
