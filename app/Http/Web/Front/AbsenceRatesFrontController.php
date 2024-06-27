<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\Meta;
use Model\AbsenceRatesModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Tassi di assenza
 */
class AbsenceRatesFrontController extends BaseFrontController
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');

    }

    /**
     * Metodo chiamato per la pagina "Tassi di assenza",
     * nella sezione del Personale
     * ID sezione 66
     *
     * @url /page/page_id/page_name
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
        $absenceRates = AbsenceRatesModel::with('structure:id,structure_name')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->paginate(20, ['id', 'year', 'month', 'presence_percentage', 'total_absence', 'object_structures_id', 'structure_name'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['structures', 'start', 'months', 'sec_token']))
            ->setPath(currentUrl());

        $absenceRates = !empty($absenceRates) ? $absenceRates->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle date di creazione e di aggiornamento
        if (!empty($absenceRates['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AbsenceRatesModel::select(['created_at'])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AbsenceRatesModel::select(['updated_at'])
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($absenceRates);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $absenceRates;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['normative_references'] = $currentPage['normatives'] ?? null;

        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/absence_rates/absence_rates', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $absenceRates {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($absenceRates = null): ?Table
    {

        $currentPageId = (int)uri()->segment(2, 0);

        $table = null;

        if (!empty($absenceRates['data'])) {

            $periods = config('absenceRatesPeriod', null, 'app');

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Struttura', 'Anno', 'Periodo', '% Presenza', '% Assenza totale');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($absenceRates['data'] as $absenceRate) {

                $months = !empty($absenceRate['month']) ? explode(',', $absenceRate['month']) : null;

                $tmpMonth = [];
                foreach ($months as $month) {
                    $tmpMonth [] = $periods[$month] ?? $periods['0' . $month];
                }

                $months = implode(',', $tmpMonth);

                $name = !empty($absenceRate['structure_name']) ? escapeXss($absenceRate['structure_name']) : 'tasso di assenza';

                $tmpName = !empty($absenceRate['structure']) ? escapeXss($absenceRate['structure']['structure_name']) : $name;

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $absenceRate['id'] . '/' . urlTitle($tmpName)) . '"' . ' data-id="' . $absenceRate['id'] . ' ">' . $tmpName . '</a>',
                    escapeXss($absenceRate['year']),
                    $months,
                    escapeXss($absenceRate['presence_percentage']),
                    escapeXss($absenceRate['total_absence'])
                );
            }
        }

        return $table;
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
        $element = AbsenceRatesModel::where('id', $elementId)
            ->with('structure:id,structure_name');

        $element = $element->first();

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
        $tmpName = $element['structure_name'] ?? 'Tasso di assenza';
        $data['pageName'] = $element['structure']['structure_name'] ?? $tmpName;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['structure']['structure_name'] ?? $tmpName, 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'absence_rates';
        $elementId = $element['id'];
        $selectFields = [
            'id',
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
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - Tasso di assenza')
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/absence_rates/details', $data, 'frontend');
    }
}
