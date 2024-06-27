<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class TimezoneHelper
{
    private static ?TimezoneHelper $instance = null;

    private string $timezone;

    private function __construct(string $timezone = null)
    {
        if ($timezone === null) {
            $timezone = date_default_timezone_get();
        }

        $this->setTimezone($timezone);
    }

    public static function getInstance(string $timezone = null): TimezoneHelper
    {
        if (self::$instance === null) {
            self::$instance = new self($timezone);
        }

        return self::$instance;
    }

    public function setTimezone(string $timezone): void
    {
        if (in_array($timezone, timezone_identifiers_list())) {
            $this->timezone = $timezone;
            date_default_timezone_set($this->timezone);
        } else {
            throw new Exception("Fuso orario non valido: {$timezone}");
        }
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function listTimezones(): array
    {
        return timezone_identifiers_list();
    }
}