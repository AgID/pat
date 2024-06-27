<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\S;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\GeneralActsDocumentsModel;
use Model\SectionsFoModel;
use System\Action;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Atti e documenti di carattere generale riferiti a tutte le procedure
 */
class GeneralActsDocumentsFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Avviso finalizzato ad acquisire le manifestazioni di interesse degli operatori economici
     * in ordine ai lavori di possibile completamento di opere incompiute nonché alla gestione delle stesse",
     * nella sezione dei Bandi di gara e contratti (nuova gestione BDNCP))
     * ID sezione 582
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function uncompletedWorksAlert(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Comunicazione circa la mancata redazione del programma triennale",
     * nella sezione dei Bandi di gara e contratti (nuova gestione BDNCP))
     * ID sezione 583
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function triennialProgram(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Atti recanti norme, criteri oggettivi per il funzionamento del sistema di qualificazione,
     * l’eventuale aggiornamento periodico dello stesso e durata, criteri soggettivi (requisiti relativi alle capacità economiche,
     * finanziarie, tecniche e professionali) per l’iscrizione al sistema",
     * nella sezione dei Bandi di gara e contratti (nuova gestione BDNCP))
     * ID sezione 584
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function qualificationSystemActs(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Atti eventualmente adottati recanti l'elencazione delle condotte che costituiscono
     * gravi illeciti professionali agli effetti degli artt. 95, co. 1, lettera e) e 98 (cause di esclusione dalla gara per gravi illeciti professionali)",
     * nella sezione dei Bandi di gara e contratti (nuova gestione BDNCP))
     * ID sezione 585
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function seriousProfessionalMisconductActs(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Elenco annuale dei progetti finanziati",
     * nella sezione dei Bandi di gara e contratti (nuova gestione BDNCP))
     * ID sezione 586
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function annualListFundedProjects(): void
    {
        $this->getDataResultsForPublicIn();
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
        $element = GeneralActsDocumentsModel::where('id', $elementId);
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

        $label = 'general_acts_documents';
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

        $data['currentPageId'] = $currentPageId;

        renderFront(config('vfo', null, 'app') . '/general_acts_documents/details', $data, 'frontend');
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

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

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
        $documents = GeneralActsDocumentsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->orderBy('document_date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_bdncp_general_acts_documents.id', 'object_bdncp_general_acts_documents.object', 'document_date', 'start_date', 'cup', 'financing_amount', 'typology'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());

        $documents = !empty($documents) ? $documents->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($documents['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(GeneralActsDocumentsModel::select(['created_at'])
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(GeneralActsDocumentsModel::select(['updated_at'])
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
        $table = $this->createTableRows($documents, $currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['items'] = $documents;
        $data['instance'] = $currentPage;
        $data['instances'] = $documents;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);

        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;

        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . '/general_acts_documents/general_acts_documents', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $documents {dati da inserire nella tabella}
     * @param int|null $sectionId Id della sezione
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($documents = null, int $sectionId = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($documents['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $typologies = [
                'lavori' => 'Lavori pubblici, per assenza di lavori',
                'acquisti' => 'Acquisti di forniture e servizi, per assenza di acquisti di forniture e servizi',
            ];

            $headers = ['Oggetto'];

            if ($sectionId == 586) {
                array_push($headers, 'CUP', 'Data di avvio', 'Importo finanziamento');
            } elseif ($sectionId == 583) {
                array_push($headers, 'Data documento', 'Tipologia');
            } else {
                array_push($headers, 'Data documento');
            }

            $table->set_heading($headers);

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($documents['data'] as $document) {

                $rows = [
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $document['id'] . '/' . urlTitle($document['object'])) . '" data-id="' . $document['id'] . ' ">' . escapeXss($document['object']) . '</a>',
                ];

                if ($sectionId == 586) {
                    array_push(
                        $rows,
                        !empty($document['cup']) ? $document['cup'] : null,
                        !empty($document['start_date']) ? date('d-m-Y', strtotime($document['start_date'])) : null,
                        !empty($document['financing_amount']) ? '&euro; ' . escapeXss(S::currency($document['financing_amount'], 2, ',', '.')) : null
                    );
                } elseif ($sectionId == 583) {
                    $rows[] = !empty($document['document_date']) ? date('d-m-Y', strtotime($document['document_date'])) : null;
                    $rows[] = !empty($document['typology']) ? $typologies[$document['typology']] : null;
                } else {
                    $rows[] = !empty($document['document_date']) ? date('d-m-Y', strtotime($document['document_date'])) : null;
                }

                $table->add_row(
                    $rows
                );
            }
        }

        return $table;
    }
}
