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
use Model\NewsNoticesModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * News e avvisi
 */
class NewsNoticeFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "News e avvisi"
     * Id sezione 180
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function index(): void
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

        // Data del giorno successivo al corrente
        $tomorrow = date("Y-m-d H:i:s", strtotime('tomorrow'));

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $news = NewsNoticesModel::where('start_date', '<=', $tomorrow)
            ->where('end_date', '>', date('Y-m-d H:i:s'))
            ->orderBy('news_date', 'DESC')
            ->orderBy('title', 'DESC')
            ->paginate(20, ['id', 'news_date', 'title', 'typology'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['title', 'start_news_date', 'end_news_date', 'sec_token']))
            ->setPath(currentUrl());

        $news = !empty($news) ? $news->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($news['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(NewsNoticesModel::select(['created_at'])
                ->where('start_date', '<=', $tomorrow)
                ->where('end_date', '>', date('Y-m-d H:i:s'))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(NewsNoticesModel::select(['updated_at'])
                ->where('start_date', '<=', $tomorrow)
                ->where('end_date', '>', date('Y-m-d H:i:s'))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;

        $table = null;

        if (!empty($news['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Titolo', 'Tipologia', 'Data');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($news['data'] as $n) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $n['id'] . '/' . urlTitle($n['title'])) . '" data-id="' . $n['id'] . ' ">' . escapeXss($n['title']) . '</a>',
                    escapeXss($n['typology']),
                    date('d-m-Y', strtotime($n['news_date']))
                );
            }

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $news;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/news_notice/news_notice', $data, 'frontend');
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
        $element = NewsNoticesModel::where('id', $elementId)
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

        $label = 'news_notices';
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
            'label',
            'file_ext',
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


        renderFront(config('vfo', null, 'app') . '/news_notice/details', $data, 'frontend');

    }
}
