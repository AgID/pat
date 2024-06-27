<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\Utility\AttachmentArchive;
use Model\CommissionsModel;
use Model\PersonnelModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Commissioni e gruppi consiliari
 */
class CommissionFrontController extends BaseFrontController
{
    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper(['url', 'image']);
    }

    /**
     * Metodo chiamato per la pagina "Commissioni",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * ID sezione 245
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCommission(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
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

        // Recupero i canoni di locazione percepiti da mostrare
        $commission = $this->getDataResults('commissione', $data);

        $commission = !empty($commission) ? $commission->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = null;

        // Recupero il personale da mostrare nella pagina in base
        $personnel = PersonnelModel::with('referent_structures:id,structure_name')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('role:id,name')
            ->with('political_organ')
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('rel_personnel_public_in.public_in_id', $currentPageId)
            ->where('archived', '!=', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->paginate(20, ['object_personnel.id', 'full_name', 'email', 'photo', 'not_available_email_txt', 'role_id', 'political_role'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['sec_token']))
            ->setPath(currentUrl());

        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $pFirstCreated = optional(PersonnelModel::select(['object_personnel.created_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $currentPageId)
                ->where('archived', '!=', 1)
                ->orderBy('object_personnel.created_at', 'ASC')
                ->first())
                ->toArray();
            $pLasUpdated = optional(PersonnelModel::select(['object_personnel.updated_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $currentPageId)
                ->where('archived', '!=', 1)
                ->orderBy('object_personnel.updated_at', 'DESC')
                ->first())
                ->toArray();

            $pCreatedUpdatedInfo = array_merge($pFirstCreated, $pLasUpdated);
        }

        // Dati passati alla vista
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $commission;
        $data['personnelInstances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['currentPageId'] = $currentPageId;
        $data['allowSearch'] = false;
        $data['openData'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['pLatsUpdatedElement'] = $pCreatedUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . '/commissions/commissions', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null  $type {la tipologia per cui filtrare i dati}
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, array &$data = []): mixed
    {

        // Recupero i canoni di locazione percepiti da mostrare
        $commissions = CommissionsModel::where('typology', $type)
            ->where('archived', '!=', 1)
            ->paginate(20, ['id', 'name', 'image'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            //->appends(Input::get(['operator', 'service', 'type', 'municipal', 'service', 'start', 'end', 'customer', 'per_page']))
            ->setPath(currentUrl());

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($commissions) && !empty($commissions->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(CommissionsModel::select(['created_at'])
                ->where('typology', $type)
                ->where('archived', '!=', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(CommissionsModel::select(['updated_at'])
                ->where('typology', $type)
                ->where('archived', '!=', 1)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $commissions;
    }

    /**
     * Metodo chiamato per la pagina "Gruppi consiliari",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * Id sezione 244
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexGroup(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
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

        // Recupero i canoni di locazione percepiti da mostrare
        $commission = $this->getDataResults('gruppo consiliare', $data);

        $commission = !empty($commission) ? $commission->toArray() : [];

        // Recupero il personale da mostrare nella pagina in base
        $personnel = PersonnelModel::with('referent_structures:id,structure_name')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('role:id,name')
            ->with('political_organ')
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('rel_personnel_public_in.public_in_id', $currentPageId)
            ->where('archived', '!=', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->paginate(20, ['object_personnel.id', 'full_name', 'email', 'photo', 'not_available_email_txt', 'role_id', 'political_role'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['sec_token']))
            ->setPath(currentUrl());

        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $pFirstCreated = optional(PersonnelModel::select(['object_personnel.created_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $currentPageId)
                ->where('archived', '!=', 1)
                ->orderBy('object_personnel.created_at', 'ASC')
                ->first())
                ->toArray();
            $pLasUpdated = optional(PersonnelModel::select(['object_personnel.updated_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $currentPageId)
                ->where('archived', '!=', 1)
                ->orderBy('object_personnel.updated_at', 'DESC')
                ->first())
                ->toArray();

            $pCreatedUpdatedInfo = array_merge($pFirstCreated, $pLasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = null;

        // Dati passati alla vista
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $commission;
        $data['personnelInstances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['currentPageId'] = $currentPageId;
        $data['allowSearch'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['pLatsUpdatedElement'] = $pCreatedUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . '/commissions/commissions', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     *
     * @return void
     * @throws Exception
     * @url /page/page_id/details/element_id/element_name
     */
    public function details(): void
    {
        $data = [];
        $archive = false;

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);
        $elementId = uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = CommissionsModel::where('id', $elementId)
            ->with(['president' => function ($query) {
                $query->select(['id', 'full_name', 'archived']);
            }])
            ->with('vicepresidents:id,full_name,archived')
            ->with('secretaries:id,full_name,archived')
            ->with('substitutes:id,full_name,archived')
            ->with('members:id,full_name,archived')
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
        $data['pageName'] = $element['name'];
        $data['menuPages'] = $sectionFO;
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;

        // Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'commissions';
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


        $data['instance'] = is_array($element) ? $element : $elementId->toArray();

        renderFront(config('vfo', null, 'app') . '/commissions/details', $data, 'frontend');
    }
}
