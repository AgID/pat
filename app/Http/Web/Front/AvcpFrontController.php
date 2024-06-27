<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Bandi di gara
 */
class AvcpFrontController extends BaseFrontController
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    /**
     * Metodo chiamato per la pagina "Tabelle riassuntive ai sensi dell'Art. 1 comma 32 della legge n. 190/2012"
     * ID sezione 111
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $pivot = new PivotController();
        $pivot->index(false);
    }

    /**
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function details(): void
    {
        $pivot = new PivotController();
        $pivot->index(false);
    }
}
