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
use Model\ContestsActsModel;
use Model\NoticesActsModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Atti delle amministrazioni
 */
class NoticeActsFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Contratti",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 116
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexManagerialPositions(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Funzione per le pagine che hanno come criterio di pubblicazione
     * il solo campo pubblica in
     *
     * @return void
     * @throws Exception
     */
    private function getDataResultsForPublicIn(): void
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
        $noticeActs = NoticesActsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_notices_acts.id', 'object_notices_acts.object', 'date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());

        $noticeActs = !empty($noticeActs) ? $noticeActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($noticeActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(NoticesActsModel::select(['created_at'])
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(NoticesActsModel::select(['updated_at'])
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($noticeActs);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['items'] = $noticeActs;
        $data['instance'] = $currentPage;
        $data['instances'] = $noticeActs;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/notice_acts/notice_acts', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $noticeActs {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($noticeActs = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($noticeActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Oggetto', 'Data');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($noticeActs['data'] as $noticeAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $noticeAct['id'] . '/' . urlTitle($noticeAct['object'])) . '" data-id="' . $noticeAct['id'] . ' ">' . escapeXss($noticeAct['object']) . '</a>',
                    !empty($noticeAct['date']) ? date('d-m-Y', strtotime($noticeAct['date'])) : null,
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Composizioni delle commissioni giudicatrici e i curricula dei suoi componenti",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 115
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCommissionComposition(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Provvedimenti che determinano le esclusioni dalla procedura di affidamento e le
     * ammissioni all'esito delle valutazioni dei requisiti soggettivi, economico-finanziari e tecnico-professionali",
     * nella sezione dei Bandi di gara -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 114
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexMeasuresExclusions(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Resoconti della gestione finanziaria dei contratti al termine della loro esecuzione",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 117
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexFinancialManagementReports(): void
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
        $noticeActs = NoticesActsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_notices_acts.id', 'object_notices_acts.object', 'date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'obj', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());
        $noticeActs = !empty($noticeActs) ? $noticeActs->toArray() : [];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $contestActs = ContestsActsModel::whereIn('object_contests_acts.typology', ['result', 'foster'])
            ->where(
                function ($query) {
                    $query->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                        ->orWhereNull('object_contests_acts.expiration_date');
                }
            )
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id', 'contraent_choice']);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
            }])
            ->orderBy('expiration_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.expiration_date', 'object_contests_acts.award_amount_value',
                'object_contests_acts.work_start_date', 'object_contests_acts.work_end_date', 'object_contests_acts.cig', 'object_contests_acts.relative_procedure_id',
                'object_contests_acts.relative_notice_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Cig', 'Oggetto', 'Data inizio', 'Data fine', 'Importo contratto', 'Importo liquidato', 'Scostamento');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                // Somme liquidate
                $sumLiquidated = array_reduce($contestAct['relative_liquidation'], function ($i, $obj) {
                    return $i + ((float)$obj['amount_liquidated']);
                });

                $table->add_row(
                    !empty($contestAct['cig']) ? $contestAct['cig'] : $contestAct['relative_notice']['cig'] ?? null,
                    '<a href="' . siteUrl('page/' . 110 . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . $contestAct['object'] . '</a>',
                    !empty($contestAct['work_start_date']) ?
                        date('d-m-Y', strtotime($contestAct['work_start_date']))
                        : null,
                    !empty($contestAct['work_end_date']) ?
                        date('d-m-Y', strtotime($contestAct['work_end_date']))
                        : null,
                    !empty($contestAct['award_amount_value']) ? '&euro; ' . S::currency($contestAct['award_amount_value'], 2, ',', '.') : null,
                    !empty($contestAct['relative_liquidation']) ? '&euro; ' . S::currency($sumLiquidated, 2, ',', '.') : null,
                    '&euro; ' . S::currency((float)$contestAct['award_amount_value'] - $sumLiquidated, 2, ',', '.')
                );
            }

        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $actsTable = $this->createTableRows($noticeActs);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['actsTable'] = !empty($actsTable) ? $actsTable->generate() : $actsTable;
        $data['instances'] = $contestActs;
        $data['notices'] = $noticeActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['allowSearchNotices'] = true;
        $data['title'] = true;
        $data['titleNotices'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['doubleIssue'] = true;
        $data['rangeOpenData'] = 2000;
        $data['openDataPublication'] = 'contest-act';

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/notice_acts/financial_management_reports', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Trasparenza nella partecipazione di portatori di interessi e dibattito pubblico",
     * nella sezione dei Bandi di gara
     * ID sezione 529
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTransparencyOfParticipation(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Collegi consultivi tecnici",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 530
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTechnicalAdvisoryColleges(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Progetti di investimento pubblico",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 531
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPublicInvestmentProjects(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Fase esecutiva",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 527
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExecutiveStage(): void
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
        $noticeActs = NoticesActsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_notices_acts.id', 'object_notices_acts.object', 'date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'obj', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());
        $noticeActs = !empty($noticeActs) ? $noticeActs->toArray() : [];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'foster')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                        ->orWhereNull('object_contests_acts.expiration_date');
                }
            )
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id', 'contraent_choice']);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
            }])
            ->orderBy('expiration_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.expiration_date', 'object_contests_acts.award_amount_value',
                'object_contests_acts.work_start_date', 'object_contests_acts.work_end_date', 'object_contests_acts.cig', 'object_contests_acts.relative_procedure_id',
                'object_contests_acts.relative_notice_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Cig', 'Oggetto', 'Data inizio', 'Data fine', 'Importo contratto', 'Importo liquidato', 'Scostamento');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                // Somme liquidate
                $sumLiquidated = array_reduce($contestAct['relative_liquidation'], function ($i, $obj) {
                    return $i + ((float)$obj['amount_liquidated']);
                });

                $table->add_row(
                    !empty($contestAct['cig']) ? $contestAct['cig'] : $contestAct['relative_notice']['cig'] ?? null,
                    '<a href="' . siteUrl('page/' . 110 . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . $contestAct['object'] . '</a>',
                    !empty($contestAct['work_start_date']) ?
                        date('d-m-Y', strtotime($contestAct['work_start_date']))
                        : null,
                    !empty($contestAct['work_end_date']) ?
                        date('d-m-Y', strtotime($contestAct['work_end_date']))
                        : null,
                    !empty($contestAct['award_amount_value']) ? '&euro; ' . S::currency($contestAct['award_amount_value'], 2, ',', '.') : null,
                    !empty($contestAct['relative_liquidation']) ? '&euro; ' . S::currency($sumLiquidated, 2, ',', '.') : null,
                    '&euro; ' . S::currency((float)$contestAct['award_amount_value'] - $sumLiquidated, 2, ',', '.')
                );
            }

        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $actsTable = $this->createTableRows($noticeActs);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['actsTable'] = !empty($actsTable) ? $actsTable->generate() : $actsTable;
        $data['instances'] = $contestActs;
        $data['notices'] = $noticeActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['title'] = true;
        $data['titleNotices'] = true;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['doubleIssue'] = true;
        $data['openDataPublication'] = 'contest-act';
        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/notice_acts/financial_management_reports', $data, 'frontend');
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
        $element = NoticesActsModel::where('id', $elementId)
            ->with('relative_contest_act:id,object')
            ->with('assignments:id,name')
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
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'notices_acts';
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
            'indexable',
            'active',
            'created_at',
            'updated_at'
        ];
        // $showOnlyPublic = true;
        $sectionId = (int)uri()->segment(2, 0);

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

        renderFront(config('vfo', null, 'app') . '/notice_acts/details', $data, 'frontend');
    }
}
