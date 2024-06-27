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
 * Altre sezioni (privacy e accessibilità)
 */
class OtherSectionsFrontController extends BaseFrontController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    /**
     * Metodo chiamato per la pagina "Privacy"
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function privacy(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'created_at', 'updated_at'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        $institutionPrivacyUrl = patOsInstituteInfo()['privacy_url'];

        if (!empty($institutionPrivacyUrl)) {
            redirect($institutionPrivacyUrl);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['instances'] = null;
        $data['allowSearch'] = false;

        renderFront(config('vfo', null, 'app') . '/other_sections/privacy', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Cookie policy"
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function cookie(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }]);

        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['instances'] = null;
        $data['allowSearch'] = false;


        renderFront(config('vfo', null, 'app') . '/other_sections/cookie', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Accessibilità"
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function accessibility(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        $instAccessibilityText = patOsInstituteInfo()['accessibility_text'];

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['accessibilityText'] = $instAccessibilityText;
        $data['instances'] = null;
        $data['allowSearch'] = false;

        renderFront(config('vfo', null, 'app') . '/other_sections/accessibility', $data, 'frontend');
    }
}
