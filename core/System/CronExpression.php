<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class CronExpression
{
    public static function isDue(string $cronExpression): bool
    {
        $cronParts = explode(' ', $cronExpression);
        if (count($cronParts) !== 5) {
            throw new InvalidArgumentException('Invalid cron expression');
        }

        $dateParts = [
            'minute' => intval(date('i')),
            'hour' => intval(date('H')),
            'day' => intval(date('d')),
            'month' => intval(date('m')),
            'dayOfWeek' => intval(date('w')),
        ];

        $cronPartsMap = [
            0 => 'minute',
            1 => 'hour',
            2 => 'day',
            3 => 'month',
            4 => 'dayOfWeek',
        ];

        for ($i = 0; $i < 5; $i++) {
            $mappedPart = $cronPartsMap[$i];
            $cronPart = $cronParts[$i];
            $datePart = $dateParts[$mappedPart];

            if ($cronPart !== '*') {
                $range = explode('-', $cronPart);

                if (count($range) === 2) {
                    if ($datePart < intval($range[0]) || $datePart > intval($range[1])) {
                        return false;
                    }
                } else {
                    $values = explode(',', $cronPart);
                    $found = false;

                    foreach ($values as $value) {
                        if (intval($value) === $datePart) {
                            $found = true;
                        }
                    }

                    if (!$found) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}