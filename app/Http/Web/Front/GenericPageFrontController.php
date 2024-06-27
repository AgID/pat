<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/**
 * Pagine generiche
 */

namespace Http\Web\Front;

use System\BaseController;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class GenericPageFrontController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

    }
}