<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Model\SectionsFoModel;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


/**
 * Controller per le pagine statiche(es. Crediti)
 * Non devono contenere paragrafi o altri contenuti dinamici
 */
class StaticPageController extends BaseFrontController
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
     * Metodo per la pagina dei Crediti
     * ID sezione 272
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function indexCredits(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente e nome ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];
        $institutionName = patOsInstituteInfo()['full_name_institution'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])->first();

        $currentPage = !empty($currentPage) ? $currentPage->toArray() : [];

        // Dati passati alla vista
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['currentPageId'] = $currentPageId;
        $data['institutionName'] = $institutionName;

        renderFront(config('vfo', null, 'app') . '/static_page/credits', $data, 'frontend');
    }
}
