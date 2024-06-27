<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class VatRule
{
    protected bool $isValidate = false;

    public function isValidate(): bool
    {
        return $this->isValidate;
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function __construct($value)
    {
        if ($value == '') {
            $this->isValidate = false;
            return false;
        }

        if (strlen((string) $value) != 11) {
            return false;
        }

        if (!ctype_digit((string) $value)) {
            $this->isValidate = false;
            return false;
        }

        $first = 0;

        for ($i = 0; $i <= 9; $i += 2) {
            $first += ord($value[$i]) - ord('0');
        }

        for ($i = 1; $i <= 9; $i += 2) {
            $second = 2 * (ord($value[$i]) - ord('0'));

            if ($second > 9) {
                $second = $second - 9;
            }

            $first += $second;

        }

        if ((10 - $first % 10) % 10 != ord($value[10]) - ord('0')) {
            $this->isValidate = false;
            return false;
        }

        $this->isValidate = true;

    }
}
