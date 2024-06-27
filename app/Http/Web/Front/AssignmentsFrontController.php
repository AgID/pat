<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\Meta;
use Model\AssignmentsModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Incarichi e consulenze
 */
class AssignmentsFrontController extends BaseFrontController
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
     * @description Metodo per la pagina di snodo dell'archivio incarico e consulenze
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Pagamenti di Consulenti e collaboratori",
     * nella sezione dei Pagamenti dell'amministrazione -> DATI SUI PAGAMENTI
     * ID sezione 156
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPaymentsConsultantsCollaborators(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $assignmentsLiquidations = AssignmentsModel::where('object_assignments.typology', 'liquidation')
            ->where('object_assignments.dirigente', '!=', 1)
            ->orWhereNull('object_assignments.dirigente')
            ->whereHas('related_assignment', function ($query) {
                $query->where('assignment_type', '!=', '1');
            })
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object']);
            }])
            ->orderBy('object_assignments.liquidation_date')
            ->paginate(20, ['object_assignments.id', 'object_assignments.name', 'object_assignments.object', 'object_assignments.liquidation_year',
                'object_assignments.compensation_provided', 'object_assignments.related_assignment_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['fn', 'year', 'object', 'sec_token']))
            ->setPath(currentUrl());

        $assignmentsLiquidations = !empty($assignmentsLiquidations) ? $assignmentsLiquidations->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assignmentsLiquidations['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AssignmentsModel::select(['created_at'])
                ->where('object_assignments.typology', 'liquidation')
                ->where('object_assignments.dirigente', '!=', 1)
                ->orWhereNull('object_assignments.dirigente')
                ->whereHas('related_assignment', function ($query) {
                    $query->where('assignment_type', '!=', '1');
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AssignmentsModel::select(['updated_at'])
                ->where('object_assignments.typology', 'liquidation')
                ->where('object_assignments.dirigente', '!=', 1)
                ->orWhereNull('object_assignments.dirigente')
                ->whereHas('related_assignment', function ($query) {
                    $query->where('assignment_type', '!=', '1');
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assignmentsLiquidations, 'liquidation');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $assignmentsLiquidations;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/assignments', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Incarichi conferiti e autorizzati ai dipendenti (dirigenti e non dirigenti)",
     * nella sezione del PERSONALE
     * ID sezione 67
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExecutivesNonExecutives(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $assignments = AssignmentsModel::where('typology', 'assignment')
            ->where('assignment_type', '=', '1')
            ->where(
                function ($query) {
                    $query->where('dirigente', '!=', 1)
                        ->orWhereNull('dirigente');
                }
            )
            ->where(
                function ($query) {
                    $query->orWhere('assignment_end', '')
                        ->orWhereNull('assignment_end')
                        ->orWhere('assignment_end', '>=', date('Y-m-d H:i:s'));
                }
            )
            ->orderBy('assignment_start', 'DESC')
            ->paginate(20, ['id', 'name', 'object', 'compensation', 'assignment_start', 'assignment_end'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['fn', 'ob', 'str', 's', 'e', 'sec_token']))
            ->setPath(currentUrl());

        $assignments = !empty($assignments) ? $assignments->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assignments['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AssignmentsModel::select(['created_at'])
                ->where('typology', 'assignment')
                ->where('assignment_type', '=', '1')
                ->where(
                    function ($query) {
                        $query->where('dirigente', '!=', 1)
                            ->orWhereNull('dirigente');
                    }
                )
                ->where(
                    function ($query) {
                        $query->orWhere('assignment_end', '')
                            ->orWhereNull('assignment_end')
                            ->orWhere('assignment_end', '>=', date('Y-m-d H:i:s'));
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AssignmentsModel::select(['updated_at'])
                ->where('typology', 'assignment')
                ->where('assignment_type', '=', '1')
                ->where(
                    function ($query) {
                        $query->where('dirigente', '!=', 1)
                            ->orWhereNull('dirigente');
                    }
                )
                ->where(
                    function ($query) {
                        $query->orWhere('assignment_end', '')
                            ->orWhereNull('assignment_end')
                            ->orWhere('assignment_end', '>=', date('Y-m-d H:i:s'));
                    }
                )
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assignments, 'assignment');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $assignments;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/assignments', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Consulenti e collaboratori"
     * Mostra tutti gli incarichi a dipendenti esterni che hanno una data di fine non precedente agli ultimi 3 anni
     * ID sezione 3
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexConsultantsAndCollaborators(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'archive_name',
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

        // Recupero gli incarichi da mostrare
        $assignments = AssignmentsModel::where('typology', 'assignment')
            ->where('assignment_type', '!=', '1')
            ->where(
                function ($query) {
                    $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                }
            )
            ->paginate(20, ['id', 'name', 'object', 'compensation', 'assignment_start', 'assignment_end'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['fn', 'ob', 'str', 's', 'e'], true))
            ->setPath(currentUrl());

        $assignments = !empty($assignments) ? $assignments->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assignments['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AssignmentsModel::select(['created_at'])
                ->where('typology', 'assignment')
                ->where('assignment_type', '!=', '1')
                ->where(
                    function ($query) {
                        $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AssignmentsModel::select(['updated_at'])
                ->where('typology', 'assignment')
                ->where('assignment_type', '!=', '1')
                ->where(
                    function ($query) {
                        $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                    }
                )
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assignments);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $assignments;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/assignments', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Titolari di incarichi di collaborazione o consulenza",
     * nella sezione Consulenti e collaboratori
     * Vengono mostrati gli incarichi esterni attivi
     * ID sezione 46
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOfficeHolders(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'controller_open_data', 'archive_name',
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

        // Recupero gli incarichi esterni attivi da mostrare
        $assignments = AssignmentsModel::where('assignment_type', 2)
            ->where('typology', 'assignment')
            ->where(
                function ($query) {
                    $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                }
            )
            ->where(
                function ($query) {
                    $query->where('assignment_end', '>=', date('Y-m-d H:i:s'))->orWhereNull('assignment_end');
                }
            )
            ->orderBy('assignment_start', 'DESC')
            ->paginate(20, ['id', 'name', 'object', 'compensation', 'assignment_start', 'assignment_end'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['fn', 'ob', 'str', 's', 'e', 'sec_token']))
            ->setPath(currentUrl());

        $assignments = !empty($assignments) ? $assignments->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assignments['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AssignmentsModel::select(['created_at'])
                ->where('assignment_type', 2)
                ->where('typology', 'assignment')
                ->where(
                    function ($query) {
                        $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                    }
                )
                ->where(
                    function ($query) {
                        $query->where('assignment_end', '>=', date('Y-m-d H:i:s'))->orWhereNull('assignment_end');
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AssignmentsModel::select(['updated_at'])
                ->where('assignment_type', 2)
                ->where('typology', 'assignment')
                ->where(
                    function ($query) {
                        $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                    }
                )
                ->where(
                    function ($query) {
                        $query->where('assignment_end', '>=', date('Y-m-d H:i:s'))->orWhereNull('assignment_end');
                    }
                )
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assignments);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $assignments;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/assignments', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Archivio incarichi di collaborazione o consulenza",
     * nella sezione Titolari di incarichi di collaborazione o consulenza
     * Vengono mostrati gli incarichi esterni non attivi, con data di fine incarico non più vecchia di tre anni
     * ID sezione 47
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexArchiveOfficeHolders(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'controller_open_data', 'archive_name',
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

        // Recupero gli incarichi esterni attivi da mostrare
        $assignments = AssignmentsModel::where('assignment_type', 2)
            ->where('typology', 'assignment')
            ->where('assignment_end', '<', date('Y-m-d H:i:s'))
            ->orderBy('assignment_start', 'ASC')
            ->paginate(20, ['id', 'name', 'object', 'compensation', 'assignment_start', 'assignment_end'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['fn', 'ob', 'str', 's', 'e', 'sec_token']))
            ->setPath(currentUrl());

        $assignments = !empty($assignments) ? $assignments->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assignments['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AssignmentsModel::select(['created_at'])
                ->where('assignment_type', 2)
                ->where('typology', 'assignment')
                ->where('assignment_end', '<', date('Y-m-d H:i:s'))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AssignmentsModel::select(['updated_at'])
                ->where('assignment_type', 2)
                ->where('typology', 'assignment')
                ->where('assignment_end', '<', date('Y-m-d H:i:s'))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assignments);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $assignments;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/assignments', $data, 'frontend');
    }


    /**
     * @description Metodo chiamato per la pagina "Archivio incarichi dipendenti",
     * nella sezione del Personale
     * ID sezione 68
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexEmployeeAssignments(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'controller_open_data', 'archive_name',
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

        // Recupero gli incarichi esterni attivi da mostrare
        $assignments = AssignmentsModel::where('assignment_type', 1)
            ->where('typology', 'assignment')
            ->where('assignment_end', '<', date('Y-m-d H:i:s'))
            ->where(function ($query) {
                $query->where('dirigente', '!=', 1)
                    ->orWhereNull('dirigente');
            })
            ->orderBy('assignment_start', 'ASC')
            ->paginate(20, ['id', 'name', 'object', 'compensation', 'assignment_start', 'assignment_end'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['fn', 'ob', 'str', 's', 'e', 'asy', 'sec_token']))
            ->setPath(currentUrl());

        $assignments = !empty($assignments) ? $assignments->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assignments['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AssignmentsModel::select(['created_at'])
                ->where('assignment_type', 1)
                ->where('typology', 'assignment')
                ->where('assignment_end', '<', date('Y-m-d H:i:s'))
                ->where(function ($query) {
                    $query->where('dirigente', '!=', 1)
                        ->orWhereNull('dirigente');
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AssignmentsModel::select(['updated_at'])
                ->where('assignment_type', 1)
                ->where('typology', 'assignment')
                ->where('assignment_end', '<', date('Y-m-d H:i:s'))
                ->where(function ($query) {
                    $query->where('dirigente', '!=', 1)
                        ->orWhereNull('dirigente');
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assignments);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $assignments;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/assignments', $data, 'frontend');
    }


    /**
     * @description Metodo chiamato per la pagina "Collegio dei Revisori dei Conti,
     * Nella sezione del Consulenti e collaboratori
     * ID sezione 50
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexRevisoriDeiConti(): void
    {
        $this->pivot();
    }


    /**
     * @description Metodo chiamato per la pagina "Archivio Collegio dei Revisori dei Conti,
     * Nella sezione del Consulenti e collaboratori
     * ID sezione 51
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexArchiveRevisoriDeiConti(): void
    {
        $this->pivot();
    }


    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $assignments {dati da inserire nella tabella}
     * @param null $type Indica la tipologia (Incarico/Liquidazione)
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($assignments = null, $type = null): ?Table
    {
        $table = null;

        if (!empty($assignments['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            // Setto l'header della tabella in base alla tipologia dell'incarico(Incarico o Liquidazione)
            if ($type == 'liquidation') {

                $table->set_heading('Nominativo', 'Oggetto', 'Compenso erogato', 'Anno');
            } else {

                $table->set_heading('Nominativo', 'Oggetto', 'Compenso', 'Inizio incarico', 'Fine incarico');
            }

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($assignments['data'] as $assignment) {

                // Se è una liquidazione prendo la data della sovvenzione associata,
                // altrimenti prendo la data della sovvenzione stessa
                $name = escapeXss(!empty($assignment['related_assignment']['name'])
                    ? $assignment['related_assignment']['name']
                    : $assignment['name']);

                // Se è una liquidazione prendo l'oggetto della sovvenzione associata,
                // altrimenti prendo l'oggetto della sovvenzione stessa
                $object = !empty($assignment['related_assignment']['object'])
                    ? escapeXss($assignment['related_assignment']['object'], true, false)
                    : escapeXss($assignment['object'], true, false);

                $elementId = $assignment['id'];

                $rows = [
                    $name,
                    '<a href="' . siteUrl('page/46/details/' . $elementId . '/' . urlTitle($object)) . '" data-id="' . $elementId . ' ">' . escapeXss($object) . '</a>'
                ];

                // Setto le colonne da mostrare in base alla tipologia dell'incarico
                if ($type == 'liquidation') {
                    $rows[] = !empty($assignment['compensation_provided'])
                        ? '&euro; ' . escapeXss(S::currency($assignment['compensation_provided'], 2, ',', '.'))
                        : null;
                    $rows[] = escapeXss($assignment['liquidation_year']);
                } else {
                    $rows[] = !empty($assignment['compensation'])
                        ? '&euro; ' . escapeXss(S::currency($assignment['compensation'], 2, ',', '.'))
                        : null;

                    $rows[] = !empty($assignment['assignment_start'])
                        ? date('d-m-Y', (strtotime($assignment['assignment_start'])))
                        : null;

                    $rows[] = !empty($assignment['assignment_end'])
                        ? date('d-m-Y', strtotime($assignment['assignment_end']))
                        : null;
                }

                $table->add_row(
                    $rows
                );
            }
        }

        return $table;
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     * Se l'id è di un incarico con data di fine precedente agli ultimi 3 anni,
     * viene mostrata una pagina error 404 apposita.
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
        $element = AssignmentsModel::where('id', $elementId)
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object', 'assignment_type', 'object_structures_id', 'assignment_end']);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with('measures:id,object')
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['related_assignment_id', 'id', 'liquidation_year', 'liquidation_date', 'compensation_provided']);
            }])
            ->first();

        // Se l'elemento non esiste oppure se non è piu pubblicato mostro la pagina di errore
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

        if (!empty($element['related_assignment'])) {
            $pageName = $element['related_assignment']['object'];
        } else {
            $pageName = $element['object'];
        }

        $assignmentType = !empty($element['related_assignment']['assignment_type'])
            ? $element['related_assignment']['assignment_type']
            : $element['assignment_type'];

        $assignmentTypology = config('assignmentTypologies', null, 'app');
        $assignmentTypology = array_key_exists($assignmentType, $assignmentTypology) ? $assignmentTypology[$assignmentType] : '';

        $name = !empty($element['related_assignment']['name'])
            ? $element['related_assignment']['name']
            : $element['name'];

        $structure = !empty($element['related_assignment']['structure'])
            ? $element['related_assignment']['structure']
            : $element['structure'];

        // Dati passati alla vista
        $data['pageName'] = $pageName;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $pageName, 'link' => '/');
        $data['instance'] = $element;
        $data['assignmentType'] = $assignmentTypology;
        $data['name'] = $name;
        $data['structure'] = $structure;
        $data['currentPageId'] = $currentPageId;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $data['hasDifferentType'] = ($element['typology'] == 'liquidation') ? 'liquidation' : 'assignment';

        $label = 'assignments';
        $elementId = $element['id'];
        $selectFields = [
            'id',
            'archive_name',
            'archive_id',
            'client_name',
            'file_name',
            'file_type',
            'file_ext',
            'file_size',
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
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/assignments/details', $data, 'frontend');
    }
}
