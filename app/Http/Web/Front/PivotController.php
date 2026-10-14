<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/**
 * Disposizioni generali sito web
 */

namespace Http\Web\Front;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\SectionsFoModel;

/**
 * Controller per le pagine di snodo del front-end
 */
class PivotController extends BaseFrontController
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
     * Pagina di pivot, utilizzata per le pagine generiche o per le pagine di sistema quando non hanno contenuti
     * @param bool $snodo Indica se la pagina è una sezione di snodo e quindi viene mostrato il relativo messaggio
     * @return void
     * @throws Exception
     */
    public function index(bool $snodo = true): void
    {


        $data = [];

        // Id della pagina corrente
        $currentPageId = (int) uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select([
            'name',
            'parent_id',
            'section_fo.id',
            'rel_institution_type_sections_labeling.label',
            'meta_description',
            'meta_keywords',
            'section_fo.created_at',
            'section_fo.updated_at',
            'is_system',
            'no_required',
            'hide'
        ])
            ->where('section_fo.id', $currentPageId)
            ->with([
                'contents' => function ($query) {
                    $query->select(['id', 'created_at', 'updated_at', 'section_fo_id', 'last_update_date'])
                        ->orderBy('last_update_date', 'DESC')
                        ->orderBy('updated_at', 'DESC')
                        ->orderBy('created_at', 'DESC')
                        ->first();
                }
            ])
            ->labelfilter((int) $institutionTypeId);

        $currentPage = $currentPage->first();

        if($currentPage->hide === 1){
            echo show404();
            exit();
        }

        $currentPage = !empty($currentPage) ? $currentPage->toArray() : [];

        // Recupero il contenuto della pagina e i richiami dei vari paragrafi
        $contents = getPageContents($currentPageId);

        // Genero le voci per il menu
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['paragraphs'] = $contents;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = (!empty($sectionFO)) ? $sectionFO : [];
        $data['currentPageId'] = $currentPageId;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['snodo'] = $snodo;

        renderFront(config('vfo', null, 'app') . '/pivot/pivot', $data, 'frontend');
    }


    public function civicAccess(): void
    {

            $this::index();
    }

}
