<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\ActivityLog;
use Helpers\Utility\SectionFrontOffice;
use Helpers\Validators\GenericPageValidator;
use Model\ContentSectionFoModel;
use Model\SectionsFoModel;
use System\Arr;
use System\Cache;
use System\Database;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Token;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Pagine Generiche
 */
class GenericPageAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
        helper('url');
    }

    /**
     * @description Renderizza la pagina index delle Pagine Generiche
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $this->breadcrumb->push('Pagine Generiche', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Pagine Generiche';
        $data['subTitleSection'] = 'GESTIONE DEI NORMALI CONTENUTI DI PAGINA';
        $data['sectionIcon'] = '<i class="far fa-edit fa-3x"></i>';

        $data['formAction'] = '/admin/normative';
        $data['formSettings'] = [
            'name' => 'form_generic-page',
            'id' => 'form_generic-page',
            'class' => 'form_generic-page',
        ];

        $validator = new Validator();
        $validator->label('Section id')
            ->value(Input::get('id'))
            ->isInt();

        if (!$validator->isSuccess()) {
            echo showError('Attenzione', 'Id paragrafo non valido!');
            exit();
        }

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);
        $data['sectionId'] = (int)Input::get('id');

        render('generic_page/index', $data, 'admin');
    }

    /**
     * @description Funzione per la generazione dell'alberatura delle pagine generiche(Sezioni di Front Office)
     *
     * @return void
     * @url /admin/generic-page/list.html
     * @method AJAX
     * @throws Exception
     */
    public function asyncGetGenericsPage(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('read');

        //Controllo se è una richiesta Ajax
        if (Input::isAjax() === true) {

            $results = SectionsFoModel::select(['section_fo.id', 'parent_id as parent', 'name as text', 'is_system',
                'section_fo.institution_id'])
                ->whereNull('section_fo.deleted_at')
                ->where('hide', 0)
                ->where('section_fo.id', '!=', 22)
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }])
                ->institution()
                ->orderBy('section_fo.is_system', 'DESC')
                ->orderBy('section_fo.institution_id', 'ASC')
                ->orderBy('sort', 'ASC')
                ->get();

            //Sostituisco i parent ID uguali a 0 con '#' per JsTree
            foreach ($results as $r) {

                if ($r->parent === 0) {

                    $r->parent = "#";
                }

                if ((int)$r->is_system === 0) {

                    $r->text = '<span class="badge badge-secondary my-badge" style="font-size: 13px;" data-toggle="tooltip" data-placement="top" data-original-title="Pagina personalizzata">
                                    [P] </span> ' . strip_tags(escapeXss($r->text));

                } else {
                    //Per quelle di sistema controllo se hanno un eventuale traduzione del nome
                    $r->text = !empty($r->label) ? $r->label : $r->text;
                }
            }

            echo json_encode($results);
        }
    }

    /**
     * @description Metodo che restituisce il contenuto della pagina, ovvero i suoi paragrafi e i relativi richiami agli archivi
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/get.html
     * @method GET
     */
    public function getSection(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('update');

        $json = new JsonResponse();
        $code = $json->success();
        $db = new Database();

        //Validatore id sezione
        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateContentsPageBySectionFoId();

        $isAjaxRequest = Input::isAjax();

        if ($isAjaxRequest === true && $validator['is_success'] === true) {

            // Estraggo i paragrafi associasti a questa sezione
            $queryContents = ContentSectionFoModel::select(['content_section_fo.id as content_id', 'content_section_fo.institution_id', 'content_section_fo.name',
                'content_section_fo.content', 'content_section_fo.section_fo_parent_id'])
                ->where('content_section_fo.section_fo_id', (int)Input::get('id'))
                ->institution()
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }])
                ->orderBy('content_section_fo.institution_id', 'ASC')
                ->orderBy('content_section_fo.sort', 'ASC')
                ->orderBy('content_section_fo.id', 'ASC')
                ->get();

            $contents = [];
            $i = 0;
            $j = 0;

            $currentId = null;

            foreach ($queryContents->toArray() as $content) {

                if ($i == 0) {
                    $currentId = $content['content_id'];
                }

                // Se per la pagina non ci sono dei richiami a degli archivi
                // prendo solo i paragrafi
                if (($currentId === $content['content_id'] && $i == 0) || $currentId !== $content['content_id']) {

                    $contents[$i] = $content;
                    $currentId = $content['content_id'];
                    $j = $i;
                    $i++;
                }
            }

            // Estraggo le info della sezione
            $query = SectionsFoModel::select(['section_fo.id', 'section_fo.name', 'section_fo.is_system',
                'sf.name as parent_name', 'section_fo.guide']);
            $query->where('section_fo.id', (int)Input::get('id'));
            $query->join('section_fo as sf', 'sf.id', '=', 'section_fo.parent_id', 'left outer');
            $query->institution();
            $query->whereNull('section_fo.deleted_at');
            $q = $query->first();

            $section = !empty($q) ? $q->toArray() : null;

            //Verifico se l'utente ha i permessi per modificare la pagina e il suo contenuto
            $permit = $this->acl->hasPagePermit((int)Input::get('id'));

            //Setto i permessi sulla pagina se l'utente li ha oppure se ha creato lui la pagina
            $permit = !empty($permit) ? ($permit['sections_fo_id'] || $permit['owned']) : 0;

            $json->set('section', $section);
            $json->set('contents', $contents);
            $json->set('permit', $permit);
        } else {

            $code = $json->bad();

            if (!$isAjaxRequest) {

                $errors = ['No ajax request'];
            } else {

                $errors = $validator['errors'];
            }

            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo che restituisce i dati della sezione da modificare
     *
     * @return void
     * @throws Exception
     * @url generic-page/section/edit
     * @method GET
     */
    public function editSection(): void
    {
        // Setto il metodo della rotta
        $this->acl->setRoute('update');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore id sezione
        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateSectionId();

        $isAjaxRequest = Input::isAjax();

        if ($isAjaxRequest === true && $validator['is_success'] === true) {

            // Recupero le informazioni della sezione da modificare
            $query = SectionsFoModel::select(['id', 'name', 'meta_keywords', 'meta_description', 'parent_id', 'is_system'])
                ->where(function ($query) {
                    $query->where('institution_id', (int)Input::get('institution_id'));
                    $query->orWhereNull('institution_id');
                })
                ->where('id', (int)Input::get('id'))
                ->first();

            $result = $query->toArray();

            $json->set('section', $result);

        } else {

            $code = $json->bad();

            if (!$isAjaxRequest) {

                $errors = ['No ajax request'];
            } else {

                $errors = $validator['errors'];
            }

            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per l' Inserimento, aggiornamento o duplicazione della sezione non di sistema
     *
     * @url generic-page/section/register.html
     * @method POST
     * @return void
     * @throws Exception
     */
    public function storeSection(): void
    {
        $json = new JsonResponse();
        $code = $json->success();
        $data = [];

        // Validatore per i dati della sezione da creare, modificare o duplicare
        $genericPageValidator = new GenericPageValidator();

        // Dati per registrazione ActivityLog
        $getIdentity = authPatOs()->getIdentity(['id', 'name']);

        $isAjaxRequest = Input::isAjax();

        if ($isAjaxRequest === true) {

            $section = new SectionFrontOffice();
            $foundMode = true;

            // Se sono super admin setto l'id dell'ente del paragrafo
            $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : strip_tags((int)Input::post('institution_id', true));

            if (Input::post('mode') == 'insert') { /* Insert record */

                $validator = $genericPageValidator->validateStorageSection();

                if ($validator['is_success']) {
                    // Setto il metodo della rotta per i permessi
                    $this->acl->setRoute('create');

                    // Conto le pagine già presenti nell'alberatura allo stesso livello, per gestire l'ordine
                    $countRows = SectionsFoModel::select(['id'])
                        ->where('institution_id', $institutionId)
                        ->where('parent_id', strip_tags(Input::post('parent_id', true)))
                        ->count();

                    // Elimino la cache
                    $cache = new Cache();
                    $institutionId = checkAlternativeInstitutionId();
                    $institutionId = (!empty($institutionId)) ? $institutionId : PatOsInstituteId();
                    $cache->delete($institutionId . '_burger_menu');

                    $arrayValues = [
                        'name' => strip_tags((string)Input::post('name', true)),
                        'parent_id' => strip_tags((string)Input::post('parent_id', true)),
                        'institution_id' => $institutionId,
                        'owner_id' => authPatOs()->id(),
                        'sort' => $countRows + 1,
                        'is_system' => 0,
                        'controller' => '\Http\Web\Front\PivotController',
                        'url' => '#!',
                        'meta_keywords' => strip_tags((string)Input::post('meta_keywords', true))
                    ];

                    // Creo la nuova sezione
                    $insert = $section->insert($arrayValues, false);

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Creazione nuova pagina generica',
                        'description' => 'Creazione nuova pagina generica con ID (' . $insert . ')',
                        'request_post' => [
                            'post' => @$_POST,
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                        'object_id' => 44,
                        'record_id' => $insert,
                        'area' => 'object',
                        'action_type' => 'addObjectInstance',
                    ]);
                } else {
                    $code = $json->bad();
                    $errors = $validator['errors'];
                    $json->error('error', $errors);
                }
            } elseif (Input::post('mode') == 'update') { /* Update record */
                $this->acl->setRoute('update');

                // Recupero la sezione attuale prima di modificarla e la salvo nel versioning
                $toUpdate = SectionsFoModel::find(strip_tags((int)Input::post('section_id', true)));

                $validator = $genericPageValidator->validateStorageSection($toUpdate->is_system);

                if ($validator['is_success']) {

                    // Elimino la cache
                    $cache = new Cache();
                    $institutionId = checkAlternativeInstitutionId();
                    $institutionId = (!empty($institutionId)) ? $institutionId : PatOsInstituteId();
                    $cache->delete($institutionId . '_burger_menu');
                    $arrayValues = [];

                    // Se la sezione non è di sistema effettuo l'update
                    if (!$toUpdate->is_system) {
                        $arrayValues = [
                            'name' => escapeXss(strip_tags((string)Input::post('name', true))),
                            'url' => urlTitle(strip_tags((string)Input::post('name', true))),
                            'meta_keywords' => escapeXss(strip_tags((string)Input::post('meta_keywords', true))),
                        ];

                        // Update della sezione
                        $section->update((int)Input::post('section_id', true), $arrayValues, $institutionId);
                    }

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Modifica pagina generica',
                        'description' => 'Modifica pagina generica con ID (' . $toUpdate->id . ')',
                        'request_post' => [
                            'post' => @$_POST,
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                        'object_id' => 44,
                        'record_id' => $toUpdate->id,
                        'area' => 'object',
                        'action_type' => 'updateObjectInstance',
                    ]);
                } else {
                    $code = $json->bad();
                    $errors = $validator['errors'];
                    $json->error('error', $errors);
                }
            } elseif (Input::post('mode') == 'duplicate') { /* Duplicazione record */

                $this->acl->setRoute('create');

                $validator = $genericPageValidator->validateStorageSection();

                if ($validator['is_success']) {

                    // Conto le pagine già presenti nell'alberatura allo stesso livello, per gestire l'ordine
                    $countRows = SectionsFoModel::select(['id'])
                        ->where('institution_id', strip_tags((string)Input::post('institution_id', true)))
                        ->where('parent_id', strip_tags((string)Input::post('parent_id', true)))
                        ->count();

                    // Inserisco la sezione duplicata
                    $insert = $section->insert([
                        'name' => escapeXss(strip_tags((string)Input::post('name', true))),
                        'parent_id' => strip_tags((int)Input::post('select_tree', true)),
                        'institution_id' => strip_tags((int)Input::post('institution_id', true)),
                        'owner_id' => authPatOs()->id(),
                        'sort' => $countRows + 1,
                        'is_system' => 0,
                        'controller' => '\Http\Web\Front\PivotController',
                        'url' => '#!',
                        'meta_keywords' => escapeXss(strip_tags((string)Input::post('meta_keywords', true)))
                    ]);

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Creazione nuova pagina generica',
                        'description' => 'Creazione nuova pagina generica con ID (' . $insert . ')',
                        'request_post' => [
                            'post' => @$_POST,
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                        'object_id' => 44,
                        'record_id' => $insert,
                        'area' => 'object',
                        'action_type' => 'addObjectInstance',
                    ]);
                }
            } else {

                $foundMode = false;
            }

            if (!$foundMode) {

                $code = $json->bad();
                $json->error('error', 'Tipologia di storage non valida');
            } else {

                // Generazione nuovo token
                if (!referenceOriginForRegenerateToken(2, 'generic-page')) {
                    //  Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }

                $json->set('contents', 'Operazione avvenuta con successo');
            }
        } else {

            $code = $json->bad();
            $errors = '';

            if (!$isAjaxRequest) {

                $errors = ['No ajax request'];
            }

            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per modificare l'ordinamento delle sezioni
     * Sposta su o giu di una posizione
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/section/sorting
     * @method GET
     */
    public function sortingSection(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : (int)Input::get('institution_id');

        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateSort('section', $institutionId);

        $direction = (Input::get('dir') === 'up') ? 'con ordinamento minore' : 'con ordinamento maggiore';

        if ($validator['is_success'] === true) {

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            // Recupero la sezione da spostare
            $queryCurrent = SectionsFoModel::where('institution_id', $institutionId)
                ->where('is_system', 0)
                ->where('id', (int)Input::get('id'))
                ->first();

            if (!empty($queryCurrent)) {

                $resultCurrent = $queryCurrent->toArray();

                // Setto l'ordinamento(se da spostare su o giu)
                if (Input::get('dir') === 'up') {

                    $sort = 'DESC';
                    $operator = '<';
                } else {

                    $sort = 'ASC';
                    $operator = '>';
                }

                // Recupero la sezione con cui scambiare l'ordinamento
                $querySwitch = SectionsFoModel::where('institution_id', $institutionId)
                    ->where('parent_id', $resultCurrent['parent_id'])
                    ->where('is_system', 0)
                    ->where('sort', $operator, $resultCurrent['sort'])
                    ->orderBy('sort', $sort)
                    ->first();

                // Aggiorno il sort delle sezioni
                if (!empty($querySwitch)) {

                    $resultSwitch = $querySwitch->toArray();

                    SectionsFoModel::where('id', $resultCurrent['id'])
                        ->update([
                            'sort' => $resultSwitch['sort']
                        ]);

                    SectionsFoModel::where('id', $resultSwitch['id'])
                        ->update([
                            'sort' => $resultCurrent['sort']
                        ]);

                    $json->set('msg', 'Operazione avvenuta con successo!');

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Modifica ordinamento pagina generica',
                        'description' => 'Modifica ordinamento[' . Input::get('dir', true) . '] pagina generica con ID (' . $resultCurrent['id'] . ') . Ordinamento invertito con la pagina con ID (' . $resultSwitch['id'] . ')',
                        'request_post' => [
                            'post' => @$_POST,
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                        'object_id' => 44,
                        'record_id' => $resultCurrent['id'],
                        'area' => 'object',
                        'action_type' => 'updateObjectInstance',
                    ]);

                } else {

                    $code = $json->bad();
                    $json->error('error', 'Attenzione, impossibile spostare la pagina. Non esiste una pagina ' . $direction);
                }
            } else {

                $code = $json->bad();
                $json->error('error', 'Record non esistente');
            }
        } else {

            $code = $json->bad();
            $json->error('error', $validator['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per l'eliminazione di una pagina personalizzata e tutte le sue pagine figlie con i relativi contenuti
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/section/delete
     * @method POST
     */
    public function deleteSection(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $json = new JsonResponse();
        $code = $json->success();
        $data = [];

        // Validatore id sezione
        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateSectionId();

        if ($validator['is_success'] === true) {

            // Recupero la pagina da cancellare
            $query = SectionsFoModel::where('institution_id', (int)Input::get('institution_id'))
                ->where('is_system', 0)
                ->where('id', (int)Input::get('id'))
                ->first();

            $DB = new Database();

            // Recupero le pagine con un ordinamento maggiore di quella da eliminare
            $toUpdate = SectionsFoModel::select(['id', 'sort'])
                ->where('institution_id', (int)Input::get('institution_id'))
                ->where('is_system', 0)
                ->where('parent_id', (int)$query->parent_id)
                ->where('sort', '>', (int)$query->sort)
                ->where('deep', '=', (int)$query->deep)
                ->get()
                ->toArray();

            $toUpdateIds = Arr::pluck($toUpdate, 'id');

            // Aggiorno l'ordinamento delle pagine
            SectionsFoModel::whereIn('id', $toUpdateIds)->update(['sort' => $DB::raw('sort - 1')]);

            if (!empty($query)) {

                $results = $query->toArray();

                $section = new SectionFrontOffice();
                $eId = (checkAlternativeInstitutionId() === 0) ? (int)Input::get('institution_id') : null;

                // Recupero l'alberatura delle pagine figlie di quella da eliminare
                $tree = $section->getDescendents($results['id'], $eId);

                if (!empty($tree)) {

                    // Estraggo gli ID delle pagine da eliminare
                    $ids = Arr::pluck($tree, 'id');

                    // Elimino la cache
                    $cache = new Cache();
                    $institutionId = checkAlternativeInstitutionId();
                    $institutionId = (!empty($institutionId)) ? $institutionId : PatOsInstituteId();
                    $cache->delete($institutionId . '_burger_menu');

                    // Elimino la pagina e tutte le sue figlie
                    $hasUpdate = SectionsFoModel::where('institution_id', (int)Input::get('institution_id'))
                        ->where('is_system', 0)
                        ->whereIn('id', $ids)
                        ->delete();

                    // Recupero gli id dei paragrafi eliminati
                    $paragraphToDelete = ContentSectionFoModel::select(['id'])
                        ->where('institution_id', (int)Input::get('institution_id'))
                        ->whereIn('section_fo_id', $ids)->get();

                    // Elimino i paragrafi associati alle pagine eliminate
                    ContentSectionFoModel::whereIn('section_fo_id', $ids)
                        ->where('institution_id', (int)Input::get('institution_id'))
                        ->delete();

                    $paragraphIds = Arr::pluck($paragraphToDelete, 'id');

                    if (!empty($hasUpdate)) {

                        // Storage Activity log
                        ActivityLog::create([
                            'action' => 'Eliminazione pagina generica',
                            'description' => 'Eliminazione pagina generica con ID (' . $query->id . ')',
                            'request_post' => [
                                'post' => @$_POST,
                                'get' => Input::get(),
                                'server' => Input::server(),
                            ],
                            'object_id' => 44,
                            'record_id' => $query->id,
                            'area' => 'object',
                            'action_type' => 'deleteObjectInstance',
                        ]);

                        // Generazione nuovo token
                        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                        $json->set('msg', 'Operazione avvenuta con successo');
                    } else {

                        $code = $json->bad();
                        $json->error('error', 'Errore eliminazione dati. contattare servizio assistenza');
                    }
                }
            }
        } else {

            $code = $json->bad();
            $json->error('error', $validator['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per l'aggiunta di un nuovo paragrafo all'interno di una sezione
     *
     * @return void
     * @url generic-page/paragraph/add/:num
     * @method GET
     * @throws Exception
     */
    public function addParagraph(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $parentId = uri()->segment(5, 0);
        $uriSection = [];
        $isSystem = null;
        $pageName = null;

        // Validatore id sezione
        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateSectionId($parentId);

        // Recupero la sezione genitore della sezione corrente
        $currentPage = SectionsFoModel::select('is_system', 'section_fo.id', 'section_fo.institution_id', 'name')
            ->where('section_fo.id', $parentId)
            ->first();

        if (!empty($currentPage)) {

            $c = $currentPage->toArray();
            $isSystem = $c['is_system'];
            $pageName = $c['label'] ?? $c['name'];
        }

        // Se la pagina corrente è di sistema non prendo il parent_id, ma il suo ID
        if ((int)$isSystem === 1) {

            $parentId = uri()->segment(5, 0);
        } else {

            // Se la pagina corrente non è di sistema recupero il parent_id della pagina di sistema genitore
            $s = new SectionFrontOffice();
            $result = array_reverse(Arr::pluck($s->getAncestors($parentId, true, false), 'id'));

            $pages = SectionsFoModel::select(['is_system', 'id'])
                ->whereIn('id', $result)
                ->where('is_system', 1)
                ->orderBy('id', 'DESC')
                ->first();

            if (!empty($pages)) {

                $r = $pages->toArray();
                $parentId = $r['id'];
            }
        }

        $this->breadcrumb->push('Pagine Generiche', '/admin/generic-page');
        $this->breadcrumb->push('Aggiungi nuovo paragrafo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        // Dati header della sezione
        $data['titleSection'] = 'Nuovo paragrafo';
        $data['subTitleSection'] = 'GESTIONE PESONALIZZATE DEI CONTENUTI';
        $data['sectionIcon'] = '<i class="far fa-edit fa-3x"></i>';

        $data['formAction'] = '/admin/generic-page/paragraph/store';
        $data['formSettings'] = [
            'name' => 'form_paragraph',
            'id' => 'form_paragraph',
            'class' => 'form_paragraph',
        ];

        $data['sectionId'] = uri()->segment(5, 0);
        $data['parentId'] = $parentId;
        $data['uri'] = $uriSection;
        $data['institution_id'] = $currentPage->institution_id;
        $data['_storageType'] = 'insert';
        $data['pageName'] = $pageName;

        render('generic_page/form_store_paragraph', $data, 'admin');
    }

    /**
     * @description Metodo per il salvataggio di un nuovo paragrafo all'interno di una sezione
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/paragraph/store.html
     * @method POST
     */
    public function storeParagraph(): void
    {
        $this->acl->setRoute(['create']);

        $json = new JsonResponse();
        $code = $json->success();
        $data = [];
        $isSystem = 0;
        $hasError = false;

        // Se sono super admin setto l'id dell'ente del paragrafo
        $institutionId = ((int)checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : strip_tags((int)Input::post('institution_id', true));

        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateParagraph();

        $isAjaxRequest = Input::isAjax();

        if ($isAjaxRequest === true && $validator['is_success'] === true) {

            $parentId = strip_tags((int)Input::post('parent_id', true));

            $sectionId = ((Input::post('mode') == 'duplicate') && Input::post('select_tree') !== null)
                ? strip_tags((int)Input::post('select_tree', true))
                : strip_tags((int)Input::post('section_id', true));

            // Conto il numero di paragrafi già presenti nella sezione, per l'ordinamento
            $countRows = ContentSectionFoModel::select(['id'])
                ->where('section_fo_id', $sectionId)
                ->where('institution_id', $institutionId)
                ->count();

            $parentId = (Input::post('mode') == 'duplicate' && Input::post('select_tree'))
                ? strip_tags((int)Input::post('select_tree', true))
                : strip_tags((int)Input::post('parent_id', true));

            // Recupero la sezione genitore della sezione corrente
            $selectedPage = SectionsFoModel::select('is_system', 'id')
                ->where('id', $sectionId)
                ->where('institution_id', $institutionId)
                ->first();

            if (!empty($selectedPage)) {
                $c = $selectedPage->toArray();
                $isSystem = $c['is_system'];
            }

            // Se la pagina corrente è di sistema non prendo il parent_id, ma il suo ID
            if ((int)$isSystem === 1) {

                $parentId = (int)$c['id'];
            } else {

                // Se la pagina corrente non è di sistema recupero il parent_id della pagina di sistema genitore
                $s = new SectionFrontOffice();
                $result = array_reverse(Arr::pluck($s->getAncestors($parentId, false, false, $institutionId), 'id'));

                $page = SectionsFoModel::select(['is_system', 'id'])
                    ->whereIn('id', $result)
                    ->where('is_system', 1)
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!empty($page)) {

                    $r = $page->toArray();
                    $parentId = $r['id'];
                } else {
                    $hasError = true;
                    $code = $json->bad();
                    $json->error('error', 'Attenzione! C\'è un problema, contattare l\'assistenza');
                }
            }

            if (!$hasError) {

                $values = [
                    'section_fo_id' => $sectionId,
                    'section_fo_parent_id' => $parentId,
                    'institution_id' => $institutionId,
                    'user_id' => authPatOs()->id(),
                    'name' => strip_tags(Input::post('title', true)),
                    'content' => Input::post('content', true),
                    'sort' => $countRows + 1,
                ];

                // Creo il nuovo paragrafo
                $created = ContentSectionFoModel::create($values);

                // Storage Activity log
                ActivityLog::create([
                    'action' => 'Creazione nuovo paragrafo',
                    'description' => 'Creazione nuovo paragrafo con ID (' . $created->id . ') nella pagina con ID (' . $parentId . ')',
                    'request_post' => [
                        'post' => @$_POST,
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                    'object_id' => 44,
                    'record_id' => $created->id,
                    'area' => 'object',
                    'action_type' => 'addObjectInstance',
                ]);

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('msg', 'Operazione avvenuta con successo!');
                $json->set('last_id', $created->id);
            }
        } else {

            $code = $json->bad();

            if (!$isAjaxRequest) {

                $errors = ['No ajax request'];
            } else {

                $errors = $validator['errors'];
            }

            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per la modifica di un paragrafo all'interno di una sezione
     *
     * @return void
     * @url generic-page/paragraph/edit/:num
     * @method GET
     * @throws Exception
     */
    public function editParagraph(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateParagraphId();

        if (!$validator['is_success']) {
            sessionSetNotify($validator['errors'], 'danger');
            redirect('admin/generic-page');
            exit();
        }

        $paragraph = ContentSectionFoModel::where('id', (int)Input::get('id'))
            ->first()
            ->toArray();

        $paragraphId = (int)Input::get('id');

        $parentId = uri()->segment(5, 0);
        $uriSection = [];
        $isSystem = null;
        $pageName = null;

        // Recupero la sezione genitore della sezione corrente
        $currentPage = SectionsFoModel::select('is_system', 'id', 'name')
            ->where('id', $parentId)
            ->first();

        if (!empty($currentPage)) {

            $c = $currentPage->toArray();
            $isSystem = $c['is_system'];
            $pageName = $c['name'];
        }

        // Se la pagina corrente è di sistema non prendo il parent_id, ma il suo ID
        if ((int)$isSystem === 1) {

            $parentId = uri()->segment(5, 0);
        } else {

            // Se la pagina corrente non è di sistema recupero il parent_id della pagina di sistema genitore
            $s = new SectionFrontOffice();
            $result = array_reverse(Arr::pluck($s->getAncestors($parentId, true, false), 'id'));

            $pages = SectionsFoModel::select(['is_system', 'id'])
                ->whereIn('id', $result)
                ->where('is_system', 1)
                ->orderBy('id', 'DESC')
                ->first();

            if (!empty($pages)) {

                $r = $pages->toArray();
                $parentId = $r['id'];
            }
        }

        $this->breadcrumb->push('Pagine Generiche', '/admin/generic-page');
        $this->breadcrumb->push('Aggiungi nuovo paragrafo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        // Dati header della sezione
        $data['titleSection'] = 'Nuovo paragrafo';
        $data['subTitleSection'] = 'GESTIONE PESONALIZZATE DEI CONTENUTI';
        $data['sectionIcon'] = '<i class="far fa-edit fa-3x"></i>';

        $data['formAction'] = '/admin/generic-page/paragraph/update';
        $data['formSettings'] = [
            'name' => 'form_paragraph',
            'id' => 'form_paragraph',
            'class' => 'form_paragraph',
        ];

        $data['sectionId'] = uri()->segment(5, 0);
        $data['parentId'] = $parentId;
        $data['uri'] = $uriSection;
        $data['paragraph'] = $paragraph;
        $data['institution_id'] = $paragraph['institution_id'];
        $data['pageName'] = $pageName;
        $data['_storageType'] = 'update';

        render('generic_page/form_store_paragraph', $data, 'admin');
    }

    /**
     * @description Metodo per l'update di un paragrafo all'interno di una sezione
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/paragraph/update.html
     * @method POST
     */
    public function updateParagraph(): void
    {
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateParagraph();

        $isAjaxRequest = Input::isAjax();

        if ($isAjaxRequest === true && $validator['is_success'] === true) {

            // Se sono super admin setto l'id dell'ente del paragrafo
            $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : strip_tags((string)Input::post('institution_id', true));

            // Recupero il paragrafo attuale prima di modificarlo e lo salvo nel versioning
            $paragraph = ContentSectionFoModel::find(strip_tags((string)Input::post('paragraph_id', true)))->toArray();

            $parentId = (int)strip_tags(Input::post('parent_id', true));

            $sectionId = (int)strip_tags(Input::post('section_id', true));

            $data = [];
            $data['name'] = strip_tags(Input::post('title', true));
            $data['content'] = Input::post('content', false, false, false);

            // Update Procedimento
            ContentSectionFoModel::where('id', (int)strip_tags(Input::post('paragraph_id', true)))->update($data);

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            // Storage Activity log
            ActivityLog::create([
                'action' => 'Modifica paragrafo',
                'description' => 'Modifica paragrafo con ID (' . $paragraph['id'] . ')',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'object_id' => 44,
                'record_id' => $paragraph['id'],
                'area' => 'object',
                'action_type' => 'updateObjectInstance',
            ]);

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            $json->set('msg', 'Operazione avvenuta con successo!');
            $json->set('last_id', $paragraph['id']);
        } else {

            $code = $json->bad();

            if (!$isAjaxRequest) {

                $errors = ['No ajax request'];
            } else {

                $errors = $validator['errors'];
            }

            $json->error('error', $errors);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per l'aggiunta di un nuovo paragrafo all'interno di una sezione.
     * Il nuovo paragrafo viene creato dalla duplicazione di uno gia esistente.
     *
     * @return void
     * @url generic-page/paragraph/duplicate/:num
     * @method GET
     * @throws Exception
     */
    public function duplicateParagraph(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        $paragraph = ContentSectionFoModel::where('id', Input::get('par', true))
            ->first()
            ->toArray();

        $parentId = uri()->segment(5, 0);
        $uriSection = [];
        $isSystem = null;

        //Recupero la sezione genitore della sezione corrente
        $currentPage = SectionsFoModel::select('is_system', 'id')
            ->where('id', $parentId)
            ->first();

        if (!empty($currentPage)) {

            $c = $currentPage->toArray();
            $isSystem = $c['is_system'];
        }

        // Se la pagina corrente è di sistema non prendo il parent_id, ma il suo ID
        if ((int)$isSystem === 1) {

            $parentId = uri()->segment(5, 0);
        } else {

            // Se la pagina corrente non è di sistema recupero il parent_id della pagina di sistema genitore
            $s = new SectionFrontOffice();
            $result = array_reverse(Arr::pluck($s->getAncestors($parentId, true, false), 'id'));

            $pages = SectionsFoModel::select(['is_system', 'id'])
                ->whereIn('id', $result)
                ->where('is_system', 1)
                ->orderBy('id', 'DESC')
                ->first();

            if (!empty($pages)) {

                $r = $pages->toArray();
                $parentId = $r['id'];
            }
        }

        $this->breadcrumb->push('Pagine Generiche', '/admin/generic-page');
        $this->breadcrumb->push('Aggiungi nuovo paragrafo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Nuovo paragrafo';
        $data['subTitleSection'] = 'GESTIONE PESONALIZZATE DEI CONTENUTI';
        $data['sectionIcon'] = '<i class="far fa-edit fa-3x"></i>';

        $data['formAction'] = '/admin/generic-page/paragraph/store';
        $data['formSettings'] = [
            'name' => 'form_paragraph',
            'id' => 'form_paragraph',
            'class' => 'form_paragraph',
        ];

        $data['sectionId'] = uri()->segment(5, 0);
        $data['parentId'] = $parentId;
        $data['uri'] = $uriSection;
        $data['paragraph'] = $paragraph;
        $data['institution_id'] = $paragraph['institution_id'];

        $data['_storageType'] = 'duplicate';

        render('generic_page/form_store_paragraph', $data, 'admin');
    }

    /**
     * @description Metodo per modificare l'ordinamento dei paragrafi
     * Sposta su o giu di una posizione
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/section/sort-paragraph
     * @method AJAX
     */
    public function sortingParagraph(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : (int)Input::get('institution_id');

        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateSort('paragraph', $institutionId);

        if ($validator['is_success'] === true) {

            // Per messaggio di errore
            $direction = (Input::get('dir') === 'up') ? 'con ordinamento minore' : 'con ordinamento maggiore';

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            // Prendo il paragrafo di cui si vuole modificare l'ordinamento
            $queryCurrent = ContentSectionFoModel::where('id', (int)Input::get('id'))
                ->where('institution_id', $institutionId)
                ->first();

            if (!empty($queryCurrent)) {

                $resultCurrent = $queryCurrent->toArray();

                //Setto l'ordinamento(se da spostare su o giu)
                if (Input::get('dir') === 'up') {

                    $sort = 'DESC';
                    $operator = '<';
                } else {

                    $sort = 'ASC';
                    $operator = '>';
                }

                // Recupero il paragrafo con cui scambiare l'ordinamento
                $querySwitch = ContentSectionFoModel::where('institution_id', $institutionId)
                    ->where('section_fo_id', $resultCurrent['section_fo_id'])
                    ->where('sort', $operator, $resultCurrent['sort'])
                    ->orderBy('sort', $sort)
                    ->first();

                //Aggiorno il sort dei paragrafi
                if (!empty($querySwitch)) {

                    $resultSwitch = $querySwitch->toArray();

                    ContentSectionFoModel::where('id', $resultCurrent['id'])
                        ->update([
                            'sort' => $resultSwitch['sort']
                        ]);

                    ContentSectionFoModel::where('id', $resultSwitch['id'])
                        ->update([
                            'sort' => $resultCurrent['sort']
                        ]);

                    // Storage Activity log
                    ActivityLog::create([
                        'action' => 'Modifica ordinamento paragrafo',
                        'description' => 'Modifica ordinamento[' . strip_tags((string)Input::get('dir')) . '] paragrafo con ID (' . $resultCurrent['id'] .
                            '. Ordinamento invertito con il paragrafo con ID (' . $resultSwitch['id'] . ')',
                        'request_post' => [
                            'post' => @$_POST,
                            'get' => Input::get(),
                            'server' => Input::server(),
                        ],
                        'object_id' => 44,
                        'record_id' => $resultCurrent['id'],
                        'area' => 'object',
                        'action_type' => 'updateObjectInstance',
                    ]);

                    $json->set('msg', 'Operazione avvenuta con successo!');
                } else {

                    $code = $json->bad();
                    $json->error('error', 'Attenzione, impossibile spostare il paragrafo. Non esiste una paragrafo ' . $direction);
                }
            } else {

                $code = $json->bad();
                $json->error('error', 'Record non esistente');
            }
        } else {

            $code = $json->bad();
            $json->error('error', $validator['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Funzione che effettua l'eliminazione di un paragrafo
     *
     * @return void
     * @throws Exception
     * @url /admin/generic-page/paragraph/delete.html
     * @method GET
     */
    public function deleteParagraph(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $json = new JsonResponse();
        $code = $json->success();

        $genericPageValidator = new GenericPageValidator();
        $validator = $genericPageValidator->validateParagraphId();

        // Se sono super admin setto l'id dell'ente del paragrafo
        $institutionId = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : (int)Input::get('institution_id');

        if (!$validator['is_success']) {

            $code = $json->bad();
            $json->error('error', $validator['errors']);
        } else {

            $paragraph = Registry::get('paragraph');

            //Elimino il paragrafo
            $paragraph->delete();

            // Storage Activity log
            ActivityLog::create([
                'action' => 'Eliminazione Paragrafo',
                'description' => 'Eliminazione Paragrafo con ID (' . $paragraph->id . ')',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'object_id' => 44,
                'record_id' => $paragraph->id,
                'area' => 'object',
                'action_type' => 'deleteObjectInstance',
            ]);

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            $json->set('msg', 'Operazione avvenuta con successo');
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @param array $array Array da sanificare
     * @return array
     */
    private function sanitizeArray(array $array): array
    {
        $validFields = ['object', 'name', 'code', 'denomination', 'title', 'object_structures.structure_name'];
        $data = null;
        if (!empty($array) && is_array($array)) {
            foreach ($array as $arr) {
                $item = !empty($arr)
                    ? preg_replace("/[^A-Za-z_.]/", '', removeInvisibleCharacters($arr))
                    : '';

                if (in_array($item, $validFields)) {
                    $data[] = $item;
                }
            }
        }

        return $data;
    }
}
