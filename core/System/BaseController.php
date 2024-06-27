<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class BaseController
{

    public function __construct()
    {

        Event::call('pre_controller');


    }

    public function __destruct()
    {

        Event::call('post_controller');

    }
}
