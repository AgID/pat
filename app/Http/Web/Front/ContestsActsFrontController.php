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
use Model\SectionsFoModel;
use System\Arr;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Bandi di gara
 */
class ContestsActsFrontController extends BaseFrontController
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
     * Metodo per la pagina di snodo dei bandi di gara e contratti
     * Id sezione 10
     *
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

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

        // Recupero il contenuto della pagina e i richiami dei vari paragrafi
        $contents = getPageContents($currentPageId);

        // Genero le voci per il menu
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = (!empty($sectionFO)) ? $sectionFO : [];
        $data['currentPageId'] = $currentPageId;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/index', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Pagamenti di Consulenti e collaboratori",
     * nella sezione dei Pagamenti dell'amministrazione -> DATI SUI PAGAMENTI
     * ID sezione 157
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPaymentsContestsActs(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

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

        // Recupero le liquidazioni dei bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', '=', 'liquidation')
            ->where('object_contests_acts.amount_liquidated', '!=', '')
            ->orWhereNotNull('object_contests_acts.amount_liquidated')
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'relative_procedure_id', 'object', 'cig']);
                $query->with('awardees:id,name');
            }])
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->paginate(20, ['object_contests_acts.id', 'object_contests_acts.amount_liquidated', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.relative_procedure_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->setPath(currentUrl())
            ->appends(Input::get(['structures', 'object', 'contraent', 'cig', 'start_p_date', 'end_p_date', 'sec_token'], true));

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', '=', 'liquidation')
                ->where('object_contests_acts.amount_liquidated', '!=', '')
                ->orWhereNotNull('object_contests_acts.amount_liquidated')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', '=', 'liquidation')
                ->where('object_contests_acts.amount_liquidated', '!=', '')
                ->orWhereNotNull('object_contests_acts.amount_liquidated')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Importo', 'Data di pubblicazione', 'Beneficiario');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $awardees = !empty($contestAct['relative_procedure']['awardees']) ? Arr::pluck($contestAct['relative_procedure']['awardees'], 'name') : null;

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['amount_liquidated']) ? '&euro; ' . number_format((float)$contestAct['amount_liquidated'], 2, ',', '.') : null,
                    !empty($contestAct['activation_date']) ? date('d-m-Y', strtotime($contestAct['activation_date'])) : null,
                    !empty($awardees) ? implode('</br>', escapeXss($awardees)) : null,
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina "Dati previsti dall'articolo 1, comma 32, della legge 6 novembre 2012, n. 190. Informazioni sulle singole procedure",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 110
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexIndividualProceduresTabularFormat(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                    ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                    ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                    ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
                }
            )
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'cig']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'cig']);
            }])
            ->orderBy('activation_date', 'DESC')
            ->paginate(20, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.cig', 'object_contests_acts.typology',
                'object_contests_acts.activation_date', 'object_contests_acts.relative_notice_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                        ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                        ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                        ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                        ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                        ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                        ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
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
        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'CIG', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                if ($contestAct['typology'] == 'notice') {
                    $tmpCig = !empty($contestAct['relative_lots'])
                        ? escapeXss(implode(', ', Arr::pluck($contestAct['relative_lots'], 'cig')))
                        : escapeXss($contestAct['cig']);
                } elseif ($contestAct['typology'] == 'result') {
                    $tmpCig = !empty($contestAct['relative_notice'])
                        ? escapeXss($contestAct['relative_notice']['cig'])
                        : null;
                } else {
                    $tmpCig = !empty($contestAct['cig'])
                        ? escapeXss($contestAct['cig'])
                        : null;
                }

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    $tmpCig,
                    !empty($contestAct['activation_date']) ? date('d-m-Y', strtotime($contestAct['activation_date'])) : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 112
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexActsOfContractingAuthorities(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

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

        // Recupero tutti i bandi(liquidazioni escluse) da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', '!=', 'liquidation')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.cig', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Cig', 'Struttura competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    escapeXss($contestAct['cig']),
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Procedure scadute",
     * nella sezione dei Bandi di gara e contratti
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 119
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExpiredProcedures(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', '!=', 'liquidation')
            ->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
            ->whereNotNull('object_contests_acts.expiration_date')
            ->orderBy('expiration_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.expiration_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                ->whereNotNull('object_contests_acts.expiration_date')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                ->whereNotNull('object_contests_acts.expiration_date')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Data di scadenza');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['expiration_date']) ?
                        date('d-m-Y', strtotime($contestAct['expiration_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Avvisi di preinformazione",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 257
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPreinformationNotices(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $alertSectors = config('sectorAlert', null, 'noticeActs');

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'alert')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.sector', 'object_contests_acts.cig', 'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'alert')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'alert')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
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

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Avvisi e bandi",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 524
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexNoticesAndAdvertisements(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $noticeSectors = config('sector', null, 'noticeActs');

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(function ($query) use ($currentPageId) {
                $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                    ->orWhere(function ($query) {
                        $query->where('object_contests_acts.typology', 'alert')
                            ->orWhere('object_contests_acts.typology', 'notice');
                    });
            })
            ->with('structure:id,structure_name')
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->orWhere(function ($query) {
                            $query->where('object_contests_acts.typology', 'alert')
                                ->orWhere('object_contests_acts.typology', 'notice');
                        });
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->orWhere(function ($query) {
                            $query->where('object_contests_acts.typology', 'alert')
                                ->orWhere('object_contests_acts.typology', 'notice');
                        });
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) .
                        '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Delibera a contrarre",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 528
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDeliberation(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $deliberationSectors = config('sectorDeliberation', null, 'noticeActs');

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        // Recupero tutte le delibere con la data di attivazione <= a oggi
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'deliberation')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.sector', 'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'],
                'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'deliberation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'deliberation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) . '" 
                        data-id="' . $contestAct['object_structures_id'] . '">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Concessioni e partenariato pubblico privato",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 525
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexConcessionsAndPartnership(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $noticeSectors = config('sector', null, 'noticeActs');

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where(function ($query) {
            $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                ->orWhereNull('object_contests_acts.expiration_date');
        })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(function ($query) use ($currentPageId) {
                $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                    ->whereIn('object_contests_acts.typology', ['alert', 'notice', 'deliberation', 'result']);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->whereIn('object_contests_acts.typology', ['alert', 'notice']);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->whereIn('object_contests_acts.typology', ['alert', 'notice']);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Affidamenti diretti di lavori, servizi e forniture di somma urgenza e di protezione civile",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 532
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDirectFoster(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $noticeSectors = config('sector', null, 'noticeActs');

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
        $contestActs = ContestsActsModel::where('object_contests_acts.decree_163', 1)
            ->where('object_contests_acts.typology', 'foster')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.decree_163', 1)
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.decree_163', 1)
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) .
                        '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina "Affidamenti in house",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 533
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexInHouseContracting(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $noticeSectors = config('sector', null, 'noticeActs');

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
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'foster')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('contraent_choice', function ($query) {
                $query->where('id', '=', 14);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('contraent_choice', function ($query) {
                    $query->where('id', '=', 14);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('contraent_choice', function ($query) {
                    $query->where('id', '=', 14);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Procedure negoziate afferenti agli investimenti pubblici finanziati, in tutto o in parte,
     * con le risorse previste dal PNRR e dal PNC e dai programmi cofinanziati dai fondi strutturali dell'Unione europea",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 526
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPnrAndPncAndEuropeanFinancing(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $noticeSectors = config('sector', null, 'noticeActs');

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
        $contestActs = ContestsActsModel::where(function ($query) {
            $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                ->orWhereNull('object_contests_acts.expiration_date');
        })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d-m-Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     *
     * @return void
     * @throws Exception
     */
    public function details(): void
    {

        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::select(['id', 'typology'])
            ->where('id', $elementId)
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

        $element = $element->toArray();

        // Se è presente il parametro in get, allora renderizzo la pagina Informazioni d'indicizzazione
        if (Input::get('st', true)) {
            $this->indexingInformation();
        } else { //Altrimenti chiamo il metodo details per lo specifico tipo di bando
            $func = $element['typology'] . 'Details';
            if (!method_exists($this, $func)) {
                echo showError('Metodo non trovato');
                die();
            }
            $this->$func();
        }
    }

    /**
     * Metodo per Informazioni d'indicizzazione
     * @return void
     * @throws Exception
     */
    private function indexingInformation(): void
    {
        $data = [];

        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object', 'asta_base_value', 'cig']);
            }])
            ->with('requirements:id,denomination,code')
            ->first();

        $element = !empty($element) ? $element->toArray() : [];
        $isMulticig = (bool)$element['is_multicig'];

        $astaValueSum = ($isMulticig) ? collect(Arr::pluck($element['relative_lots'], 'asta_base_value'))
            ->map(function ($items) {
                return (float)S::currency($items, 2, null, null, false);
            })->sum() : $element['asta_base_value'];
        $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');

        $administrationType = ['' => '', 1 => 'Regioni', 2 => 'Provincie', 3 => 'Comuni', 4 => 'Università', 5 => 'Ministeri', 6 => 'Organi istituzionali', 7 => 'Altri soggetti pubblici e privati'];
        $contractType = ['' => '', 1 => 'Lavori', 2 => 'Servizi', 3 => 'Forniture'];

        $data['element'] = $element;

        $requirements = [];
        if (!empty($element['requirements'])) {
            foreach ($element['requirements'] as $requirement) {
                $requirements [] = $requirement['denomination'];
            }
        }

        // Informazioni per la tabella d'indicizzazione delle informazioni
        $data['headers'] = [
            'Tipo' => !empty($element['type']) ? escapeXss($element['type']) : '',
            'Contratto' => !empty($element['contract']) ? escapeXss($contractType[$element['contract']]) : '',
            'Denominazione dell\'amministrazione aggiudicatrice' => !empty($element['adjudicator_name']) ? escapeXss($element['adjudicator_name']) : '',
            'Tipo di amministrazione' => !empty($element['administration_type'] && !empty($administrationType[$element['administration_type']])) ? escapeXss($administrationType[$element['administration_type']]) : '',
            'Provincia sede di gara' => !empty($element['province_office']) ? escapeXss($element['province_office']) : '',
            'Comune sede di gara' => !empty($element['municipality_office']) ? escapeXss($element['municipality_office']) : '',
            'Indirizzo sede di gara' => !empty($element['office_address']) ? escapeXss($element['office_address']) : '',
            'Senza importo' => !empty($element['no_amount']) ? (escapeXss($element['no_amount']) == 1 ? 'NO' : 'SI') : '',
            'Valore importo dell\'appalto' => !empty($astaValueSum) ? '€&nbsp;' . $astaValueSum : '',
            'Valore importo di aggiudicazione' => !empty($element['award_amount_value']) ? '€&nbsp;' . S::currency($element['award_amount_value'], 2, ',', '.') : null,
            'Data pubblicazione' => !empty($element['activation_date']) ? date('d-m-Y', strtotime($element['activation_date'])) : null,
            'Data scadenza bando' => !empty($element['expiration_date']) ? date('d-m-Y', strtotime($element['expiration_date'])) : null,
            'Data scadenza pubblicazione esito' => null,
            'Requisiti di qualificazione' => !empty($requirements) ? implode(' | ', $requirements) : null,
            'Codice cpv' => !empty($element['cpv_code_id']) ? escapeXss($element['cpv_code_id']) : '',
            'Codice scp' => !empty($element['codice_scp']) ? escapeXss($element['codice_scp']) : '',
            'Url di pubblicazione su www.Serviziocontrattipubblici.It' => !empty($element['url_scp']) ? escapeXss($element['url_scp']) : '',
            'Codice CIG' => !empty($element['relative_lots']) ? implode(', ', Arr::pluck($element['relative_lots'], 'cig')) : $element['cig'],
        ];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, 'Informazioni d\'indicizzazione')
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - Informazioni d\'indicizzazione')
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/indexing_information', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di una delibera
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function deliberationDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('proceedings:id,object')
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'deliberation';

        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/deliberation_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un esito di gara
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function resultDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('participants:id,name')
            ->with('awardees:id,name')
            ->with('proceedings:id,object')
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'expiration_date',
                    'relative_notice_id', 'contraent_choice', 'object_structures_id', 'object_personnel_id']);
                $query->with('contraent_choice:id,name');
                $query->with('structure:id,structure_name');
                $query->with('rup:id,full_name');
            }])
            ->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'result';

        //Allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/result_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un bando di gara
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function noticeDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['contraent_choice' => function ($query) {
                $query->select(['id', 'name']);
            }])
            ->with('proceedings:id,object')
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with('relative_deliberation:id,object')
            ->with(['relative_results' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object']);
            }])
            ->with(['relative_alerts' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object']);
            }])
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['measures' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['relative_foster' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object', 'asta_base_value', 'cig']);
            }])
            ->first();

        $element = !empty($element) ? $element->toArray() : [];
        $isMulticig = (bool)$element['is_multicig'];

        $astaValueSum = ($isMulticig) ? collect(Arr::pluck($element['relative_lots'], 'asta_base_value'))
            ->map(function ($items) {
                return (float)S::currency($items, 2, null, null, false);
            })->sum() : $element['asta_base_value'];
        $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');

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
        $data['astaValueSum'] = $astaValueSum;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $data['hasDifferentType'] = 'notice';

        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/notice_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un esito affidamento
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function fosterDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['contraent_choice' => function ($query) {
                $query->select(['id', 'name']);
            }])
            ->with('participants:id,name')
            ->with('awardees:id,name')
            ->with('other_proceedings:id,object')
            ->with('relative_deliberation:id,object')
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['measures' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_foster' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object']);
            }])
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'foster';

        //Allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/foster_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un avviso
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function alertDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('proceedings:id,object')
            ->with('other_proceedings:id,object')
            ->with('relative_deliberation:id,object')
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'expiration_date']);
            }])
            ->with(['relative_foster' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'alert';

        //Allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/alert_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un lotto
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function lotDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id',
                    'contraent_choice', 'object_structures_id']);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
            }])
            ->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'lot';

        //Allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/lot_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di una liquidazione
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function liquidationDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object', 'cig']);
                $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_procedure_id', 'relative_notice_id', 'object_structures_id']);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
                $query->with(['relative_notice' => function ($query) {
                    $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_notice_id', 'object_structures_id']);
                    $query->with(['structure' => function ($query) {
                        $query->select(['id', 'structure_name']);
                    }]);
                }]);
            }])
            ->with('relative_procedure_awardees:id,name')
            ->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'liquidation';

        //Allegati
        $attach = new AttachmentArchive();
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $element['id'],
            ['*'],
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/liquidation_details', $data, 'frontend');
    }
}
