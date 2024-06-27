<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/**
 * Homepage sito web
 */

namespace Http\Web\Front;

use Exception;
use Model\SectionsFoModel;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Home
 */
class HomeController extends BaseFrontController
{
    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {

        parent::__construct();
        helper('url');

    }

    /**
     * Metodo chiamato per la pagina "Home" dell'ente
     *
     * @url /
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $institutionInfo = patOsInstituteInfo();

        //Eventuale testo personalizzato che compare nell'Home page
        $welcomeText = $institutionInfo['welcome_text'] ?? null;
        $data = [];

        // Recupero la lista delle sezioni navigabili da mostrare
        $records = SectionsFoModel::select(['section_fo.id', 'name', 'url', 'controller'])
            ->where('is_system', '=', 1)
            ->where('parent_id', '=', 0)
            ->where('hide', 0)
            ->where('section_fo.id', '!=', 19)
            ->orderBy('sort', 'ASC')
            ->get();

        $records = (!empty($records)) ? $records->toArray() : [];

        // Dati passati alla vista
        $data['sections'] = $records;
        $data['notEditable'] = true;
        $data['welcomeText'] = $welcomeText;
        $data['title'] = 'Amministrazione trasparente';

        renderFront(config('vfo', null, 'app') . '/home/home', $data, 'frontend');
    }
}
