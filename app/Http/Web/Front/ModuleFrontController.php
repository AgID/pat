<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\Meta;
use Model\ModulesRegulationsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end per la Modulistica
 */
class ModuleFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Dichiarazioni sostitutive e acquisizione d'ufficio dei dati"
     * I dati vengono visualizzati nella sezione dei Procedimenti
     * ID sezione 101
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexSubstituteDeclarations(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system', 'controller_open_data'])
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

        // Recupero la modulistica di tipo (dichiarazione sostitutiva) da mostrare
        $modules = ModulesRegulationsModel::where('typology', 'dichiarazione sostitutiva')
            ->paginate(20, ['id', 'title as name'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());

        $modules = !empty($modules) ? $modules->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($modules['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ModulesRegulationsModel::select(['created_at'])
                ->where('typology', 'dichiarazione sostitutiva')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ModulesRegulationsModel::select(['updated_at'])
                ->where('typology', 'dichiarazione sostitutiva')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['finalSectionId'] = 101;
        $data['paragraphs'] = $contents;
        $data['instances'] = $modules;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/modulistics/modulistics', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina "DISPOSIZIONI GENERALI -> ATTI GENERALI -> MODULISTICA"
     * I dati vengono visualizzati nella sezione dei Modulistica
     * Id sezione 30
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexModulistics(): void
    {
        $data = [];

        // Validazione dei filtri per la ricerca
        $validation = new Validator();
        $validation->label('Testo da cercare')->value(Input::get('s'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml());
            die();

        }

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

        // Recupero la modulistica di tipo (dichiarazione sostitutiva) da mostrare
        $modules = ModulesRegulationsModel::where('typology', 'modulo')
            ->paginate(20, ['id', 'title as name'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['s', 'sec_token']))
            ->setPath(currentUrl());

        $modules = !empty($modules) ? $modules->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($modules['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ModulesRegulationsModel::select(['created_at'])
                ->where('typology', 'modulo')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ModulesRegulationsModel::select(['updated_at'])
                ->where('typology', 'modulo')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['finalSectionId'] = $currentPageId;
        $data['paragraphs'] = $contents;
        $data['instances'] = $modules;
        $data['instance'] = $currentPage;
        $data['allowSearch'] = true;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;

        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/modulistics/modulistics', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     *
     * @return void
     * @throws Exception
     */
    public function details(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);
        $elementId = uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = ModulesRegulationsModel::where('id', $elementId)
            ->with('proceedings:id,name')
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

        $element = $element->toArray();

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['pageName'] = $element['title'];
        $data['menuPages'] = $sectionFO;


        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['title'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'modules_regulations';
        $elementId = $element['id'];
        $selectFields = [
            'id',
            'cat_id',
            'archive_name',
            'archive_id',
            'client_name',
            'file_name',
            'file_type',
            'file_size',
            'file_ext',
            'label',
            'indexable',
            'active',
            'created_at',
            'updated_at'
        ];

        // Allegati
        $attach = new AttachmentArchive();
        $data['listAttach'] = $attach->getAllByObject(
            $label,
            $elementId,
            $selectFields,
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['title'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/modulistics/details', $data, 'frontend');

    }
}
