<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\BdncpProcedureModel;
use Model\SectionsFoModel;
use System\Action;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end PROCEDURE DELIBERA 261/2023
 */
class BdncpProcedureFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina index dei Bandi di gara e contratti
     * ID sezione 10
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $procedure = BdncpProcedureModel::where('typology', 'procedure')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_bdncp_procedure.id', 'object', 'cig', 'bdncp_link', 'updated_at'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'c', 'sec_token']))
            ->setPath(currentUrl());

        $procedure = !empty($procedure) ? $procedure->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($procedure['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(BdncpProcedureModel::select(['created_at'])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(BdncpProcedureModel::select(['updated_at'])
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($procedure, 'procedure');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['items'] = $procedure;
        $data['instance'] = $currentPage;
        $data['instances'] = $procedure;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . '/bdncp_procedure/index', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina Avvisi
     * ID sezione 588
     * @return void
     * @throws Exception
     */
    public function alerts(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $alerts = BdncpProcedureModel::where('typology', 'alert')
            ->orderBy('updated_at', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_bdncp_procedure.id', 'object', 'alert_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'c', 'sec_token']))
            ->setPath(currentUrl());

        $alerts = !empty($alerts) ? $alerts->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($alerts['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(BdncpProcedureModel::select(['created_at'])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(BdncpProcedureModel::select(['updated_at'])
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($alerts, 'alert');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['items'] = $alerts;
        $data['instance'] = $currentPage;
        $data['instances'] = $alerts;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);

        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;

        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . '/bdncp_procedure/index', $data, 'frontend');
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
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = BdncpProcedureModel::where('id', $elementId)
            ->with('commission:id,object,name')
            ->with('board:id,object,name')
            ->with('relative_bdncp_procedure')
            ->with('measures:id,object,object_bdncp_procedure_id')
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
        $data['pageName'] = $element['object'];
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['object'], 'link' => '/');
        $data['instance'] = $element;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'bdncp_procedure';
        $elementId = $element['id'];
        $selectFields = [
            'id',
            'cat_id',
            'archive_name',
            'archive_id',
            'client_name',
            'file_name',
            'file_type',
            'file_ext',
            'file_size',
            'label',
            'bdncp_cat',
            'indexable',
            'active',
            'created_at',
            'updated_at'
        ];

        $sectionId = (int)uri()->segment(2, 0);


        // Allegati
        $attach = new AttachmentArchive();
        $data['listAttach'] = $attach->getAllByObject(
            $label,
            $elementId,
            $selectFields,
            true
        );

        $tmpAttachs = [];

        foreach ($data['listAttach'] as $attach) {
            $tmpAttachs[$attach['bdncp_cat']][] = $attach;
        }

        $data['attachs'] = $tmpAttachs;

        $tmpCat = [];
        $bdncpProcedureCat = config('config', null, 'bdncp_procedure_config');
        if (!empty($bdncpProcedureCat) && is_array($bdncpProcedureCat)) {
            foreach ($bdncpProcedureCat as $k => $v) {
                if ($element[$v['field'] . '_check']) {
                    $tmpCat [$v['step']][$k] = $v;
                }
            }
        }

        $data['procedureCat'] = $tmpCat;

        $data['currentPageId'] = $currentPageId;

        $viewToRender = $element['typology'] === 'procedure' ? '/bdncp_procedure/details' : '/bdncp_procedure/alert_details';

        renderFront(config('vfo', null, 'app') . $viewToRender, $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $procedures {dati da inserire nella tabella}
     * @param string $typology {indica la tipologia (procedure o avvisi)}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($procedures = null, string $typology = ''): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($procedures['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $headers = ['Oggetto'];

            if ($typology === 'procedure') {
                array_unshift($headers, 'CIG');
                $headers [] = 'Link BDNCP';
            } else {
                $headers [] = 'Data avviso';
            }

            $table->set_heading($headers);

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($procedures['data'] as $procedure) {

                $rows = [
                    $typology === 'procedure'
                        ? escapeXss($procedure['object'])
                        : '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $procedure['id'] . '/' . urlTitle(escapeXss($procedure['object']))) . '" data-id="' . $procedure['id'] . ' ">' . escapeXss($procedure['object']) . '</a>'
                ];

                if ($typology === 'procedure') {
                    array_unshift($rows, '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $procedure['id'] . '/' . urlTitle($procedure['object'])) . '" data-id="' . $procedure['id'] . ' ">' . escapeXss($procedure['cig']) . '</a>');
                    $rows [] = (!empty($procedure['bdncp_link']) ? escapeXss($procedure['bdncp_link']) : 'N/A');
                } else {
                    $rows [] = (!empty($procedure['alert_date']) ? date('d-m-Y', strtotime($procedure['alert_date'])) : 'N/A');
                }

                $table->add_row(
                    $rows
                );
            }
        }

        return $table;
    }
}
