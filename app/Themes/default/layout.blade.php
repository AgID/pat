<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>


<!DOCTYPE html>
<html lang="{{ config('language',null,'app') }}">
<head>
    <title>Pat OS - Homepage</title>
    <meta charset="{{ CHARSET }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    @yield(content)
</body>
</html>
