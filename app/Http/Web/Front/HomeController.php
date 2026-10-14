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
use Helpers\Utility\Meta;
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
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];
        $institutionInfo = patOsInstituteInfo();

        //Eventuale testo personalizzato che compare nell'Home page
        $welcomeText = $institutionInfo['welcome_text'] ?? null;
        $data = [];

        // Recupero la lista delle sezioni navigabili da mostrare
        $records = SectionsFoModel::select(['section_fo.id', 'name', 'url', 'controller', 'rel_institution_type_sections_labeling.label'])
            ->where('is_system', '=', 1)
            ->where('parent_id', '=', 0)
            ->where('hide', 0)
            ->where('section_fo.id', '!=', 19)
            ->labelfilter((int)$institutionTypeId)
            ->orderBy('sort', 'ASC')
            ->get();

        $records = (!empty($records)) ? $records->toArray() : [];

        // Dati passati alla vista
        $data['sections'] = $records;
        $data['notEditable'] = true;
        $data['welcomeText'] = $welcomeText;
        $data['title'] = 'Amministrazione trasparente';


        //Evita che venga stampato due volte la stessa cosa per chi imposta come nome ente -> "Amministrazione trasparente"
        $title = (strtolower(trim($data['title'])) == strtolower(trim($institutionInfo['full_name_institution'])))
        ? $institutionInfo['full_name_institution']
        : $data['title'] . ' - ' . $institutionInfo['full_name_institution'];

        // Set Meta
        $data['_meta_page'] = Meta::getInstance()
//            ->setTitle(config('meta_dc_title', null, 'custom'))
            ->setDcterms(Meta::dctermsPublisher, 'Portale Trasparenza ' . $institutionInfo['full_name_institution'])
            ->setDcterms(Meta::dctermsTitle, $data['title'] . ' -  '. $institutionInfo['full_name_institution'])
            ->setDescription('Il portale della Trasparenza conforme al D.Lgs. 33/2013 - Portale '. $data['title'] . ' ' . $institutionInfo['full_name_institution'])
            ->tagTitle($title)
            ->toHtml();

        //Inclusione del nodo JSON-LD della pagina

        renderFront(config('vfo', null, 'app') . '/home/home', $data, 'frontend');
    }
}
