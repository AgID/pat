<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use Model\ContentSectionFoModel;
use Model\RecallsModel;
use Model\SectionsFoModel;
use System\Breadcrumbs;
use System\Cache;
use System\Database;
use System\JsonResponse;
use System\View;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('isAuth')) {

    /**
     * Funzione che verifica se un utente ha effettuato l'autenticazione.
     * @return bool|array
     */
    function isAuth(): bool|array
    {
        $identity = authPatOs()->getIdentity();
        $identity = $identity ?? null;
        return !empty($identity) && is_array($identity)
            ? $identity
            : false;
    }
}

if (!function_exists('getFavicon')) {
    /**
     * Funzione che stampa a video la favicon dell'ente
     * @return string|null
     * @throws Exception
     */
    function getFavicon(): ?string
    {
        $data = null;
        $favicon = patOsInstituteInfo(['favicon_file']);

        if (!empty($favicon)) {

            if (\Helpers\FileSystem\File::exists(MEDIA_PATH . instituteDir() . '/assets/images/' . $favicon['favicon_file'])) {

                $info = \Helpers\FileSystem\File::mime(MEDIA_PATH . instituteDir() . '/assets/images/' . $favicon['favicon_file']);

                $attributes = stringifyAttributes([
                    'rel' => 'shortcut icon',
                    'type' => 'image/x-icon',
                    'href' => baseUrl('media/' . instituteDir() . '/assets/images/' . $favicon['favicon_file'])
                ]);

                $data = '<link ' . $attributes . ' >' . "\n";
            }
        }

        return $data;
    }
}

if (!function_exists('renderFront')) {
    /**
     * Funzione per la vista
     * @param string $layout File della vista da renderizzare
     * @param array $data Dati da passare alla vista
     * @param null $theme Tema da utilizzare per il render
     * @return void
     * @throws Exception
     */
    function renderFront(string $layout = '', array $data = [], $theme = null): void
    {
        if (empty($data['_meta_page'])) {
            $institutionInfo = patOsInstituteInfo(['full_name_institution']);
            $institutionName = !empty($institutionInfo['full_name_institution'])
                ? trim($institutionInfo['full_name_institution'])
                : '';
            $pageName = !empty($data['pageName']) && is_string($data['pageName'])
                ? trim($data['pageName'])
                : '';

            // Garantisce un titolo HTML anche quando la pagina non dispone di metadati specifici.
            $data['_page_title'] = 'Portale Trasparenza';
            if ($institutionName !== '') {
                $data['_page_title'] .= ' ' . $institutionName;
            }
            if ($pageName !== '') {
                $data['_page_title'] .= ' - ' . $pageName;
            }
        }

        render($layout, $data, $theme);
    }
}

if (!function_exists('isFrontFragmentRequest')) {
    /**
     * Verifica se la richiesta corrente chiede solo il frammento dei risultati (tabella + paginazione)
     * invece della pagina completa. Richiede sia l'header dedicato sia una richiesta AJAX, in modo
     * che nessuna navigazione normale possa finire su questo percorso.
     *
     * @return bool
     */
    function isFrontFragmentRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_PATOS_FRAGMENT']) && \System\Input::isAjax();
    }
}

if (!function_exists('renderFrontFragment')) {
    /**
     * Se la richiesta è un frammento, emette in JSON la sola tabella dei risultati e la relativa
     * paginazione e termina la richiesta, evitando il render del layout e tutto il lavoro che
     * serve solo alla pagina completa (menu, filtri, contenuti).
     *
     * La terminazione avviene qui e non nel chiamante
     *
     * Consuma le stesse chiavi di $data usate da "layout/partials/search_results".
     *
     * @param array $data Dati della vista, con almeno "table" e "instances"
     * @return bool false se la richiesta non è un frammento (in caso contrario non ritorna)
     * @throws Exception
     */
    function renderFrontFragment(array $data): bool
    {
        if (!isFrontFragmentRequest()) {

            return false;

        }


        $data = $data ?? null;

        header('Content-Type: application/json; charset=' . CHARSET);

        echo json_encode([
            'table' => (string) ($data['table'] ?? ''),
            'pagination' => !empty($data['instances']) ? paginateBootstrap($data['instances'], 'render') : '',
            'current_page' => $data['instances']['current_page'] ?? 1,
            'last_page' => $data['instances']['last_page'] ?? 1,
        ]);

        exit;
    }
}

if (!function_exists('getInstitutionLogo')) {

    /**
     * Funzione che restituisce il logo dell'ente di appartenenza del dominio
     * Utilizzata nel front-end
     *
     * @return string
     * @throws Exception
     */
    function getInstitutionLogo(): string
    {
        $institution = patOsInstituteInfo();

        if (!empty($institution['simple_logo_file']) && file_exists('media/' . $institution['short_institution_name'] . '/assets/images/' . $institution['simple_logo_file'])) {
            return !empty($institution)
                ? 'src="' . baseUrl('media/' . $institution['short_institution_name'] . '/assets/images/' . $institution['simple_logo_file']) . '"alt="' . $institution['full_name_institution'] . '"'
                : '';
        } else {
            return '';
        }
    }
}

if (!function_exists('getInstitutionFullAddress')) {

    /**
     * Funzione che restituisce l'indirizzo completo dell'ente
     * Utilizzata nel front-end nella sezione footer
     *
     * @return string
     * @throws Exception
     */
    function getInstitutionFullAddress(): string
    {
        $institution = patOsInstituteInfo();

        return !empty($institution)
            ? $institution['address_street'] . ' - ' . $institution['address_zip_code'] . ' ' . $institution['address_city'] . '(' . $institution['address_province'] . ')'
            : '';
    }
}


if (!function_exists('getCurrentPageInfo')) {

    /**
     * Funzione che restituisce le informazioni della pagina corrente che si sta navigando
     *
     * @param array $data Dati da passare alla vista
     * @param string $tgCondition Eventuale condizione aggiuntiva
     * @return mixed
     */
    function getCurrentPageInfo(array &$data, string &$tgCondition): mixed
    {
        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Id della pagina corrente
        $currentPageId = (int) uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select([
            'name',
            'section_fo.id',
            'parent_id',
            'form_filter',
            'controller_open_data',
            'archive_name',
            'rel_institution_type_sections_labeling.label',
            'section_fo.created_at',
            'section_fo.updated_at',
            'is_system'
        ])
            ->where('section_fo.id', $currentPageId)
            ->with([
                'contents' => function ($query) {
                    $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                        ->orderBy('updated_at', 'DESC')
                        ->orderBy('created_at', 'DESC')
                        ->first();
                }
            ])
            ->labelfilter((int) $institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();


        return $currentPage;
    }
}

if (!function_exists('checkPoliticSectionEmpty')) {

    /**
     * Restituisce le sezioni degli organi politici prive di contenuto.
     *
     * @return array
     */
    function checkPoliticSectionEmpty(): array
    {
        $politicSectionIds = [233, 234, 235, 236];

        $configuredSectionIds = optional(\Model\SectionFoPoliticOrganModel::whereIn('section_fo_id', $politicSectionIds)
            ->where('institution_id', checkAlternativeInstitutionId())
            ->whereNotNull('skills')
            ->where('skills', '!=', '')
            ->pluck('section_fo_id'))->toArray();

        return array_values(array_diff($politicSectionIds, $configuredSectionIds));
    }
}


if (!function_exists('getRightOrBottomMenu')) {

    /**
     * Funzione che ritorna le voci da mostrare nel menu laterale destro o in fondo alla pagina,
     * in base alla sezione corrente
     *
     * @param $sectionId {id della pagina corrente}
     * @param $parentId {id della pagina padre di quella corrente}
     * @return mixed
     */
    function getRightOrBottomMenu($sectionId = null, $parentId = null): mixed
    {
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];
        $institutionId = checkAlternativeInstitutionId();
        $DB = new \System\Database();
        $sameLevel = false;
        // Esclusione della sezione "Altre sezioni".
        $excludedSections = [19];

        if ($sectionId == 605 || $parentId == 605) {
            array_push($excludedSections, ...checkPoliticSectionEmpty());
        }

        // Recupero le pagine figlie della pagina corrente per il menù laterale destro
        $sectionFO = Model\SectionsFoModel::select([
            'section_fo.id',
            'section_fo.name',
            'section_fo.url',
            'section_fo.is_system',
            'section_fo.tag',
            $DB::raw('MAX(rel_institution_type_sections_labeling.label) as label')
        ])
            ->where('parent_id', $sectionId)
            ->where('deleted', 0)
            ->where('hide', 0)
            ->institution()
            ->whereNotIn('section_fo.id', $excludedSections)
            ->where(function ($query) use ($institutionId) {
                $query->where('is_system', 1)
                    ->orWhere(function ($query) use ($institutionId) {
                        $query->where('is_system', 0)
                            ->where('section_fo.institution_id', $institutionId);
                    });
            })
            ->leftJoin("rel_institution_type_sections_labeling", function ($join) use ($institutionTypeId, $institutionId) {
                $join->on("rel_institution_type_sections_labeling.sections_id", "=", "section_fo.id")
                    ->where(function ($query) use ($institutionTypeId, $institutionId) {
                        $query->where(function ($query) use ($institutionTypeId) {
                            $query->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
                            $query->whereNull("rel_institution_type_sections_labeling.institution_id");
                        })
                            ->orWhere(function ($query) use ($institutionId) {
                                $query->where("rel_institution_type_sections_labeling.institution_id", "=", $institutionId);
                                $query->whereNull("rel_institution_type_sections_labeling.institution_type_id");
                            });
                    });
            })
            ->groupBy('section_fo.id', 'section_fo.name', 'section_fo.url', 'section_fo.is_system', 'section_fo.tag')
            ->orderBy('sort', 'ASC')
            ->orderBy('name', 'ASC');

        $sectionFO = $sectionFO->get()->toArray();

        // Se la pagina corrente non ha figli, mostro nel menù le pagine dello stello livello
        if (empty($sectionFO) && !empty($parentId)) {

            $sameLevel = true;

            $sectionFO = Model\SectionsFoModel::select([
                'section_fo.id',
                'section_fo.name',
                'section_fo.url',
                'section_fo.is_system',
                'section_fo.tag',
                $DB::raw('MAX(rel_institution_type_sections_labeling.label) as label')
            ])
                ->where('parent_id', $parentId)
                ->where('deleted', 0)
                ->where('hide', 0)
                ->institution()
                ->whereNotIn('section_fo.id', $excludedSections)
                ->where(function ($query) use ($institutionId) {
                    $query->where('is_system', 1)
                        ->orWhere(function ($query) use ($institutionId) {
                            $query->where('is_system', 0)
                                ->where('section_fo.institution_id', $institutionId);
                        });
                })
                ->leftJoin("rel_institution_type_sections_labeling", function ($join) use ($institutionTypeId, $institutionId) {
                    $join->on("rel_institution_type_sections_labeling.sections_id", "=", "section_fo.id")
                        ->where(function ($query) use ($institutionTypeId, $institutionId) {
                            $query->where(function ($query) use ($institutionTypeId) {
                                $query->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
                                $query->whereNull("rel_institution_type_sections_labeling.institution_id");
                            })
                                ->orWhere(function ($query) use ($institutionId) {
                                    $query->where("rel_institution_type_sections_labeling.institution_id", "=", $institutionId);
                                    $query->whereNull("rel_institution_type_sections_labeling.institution_type_id");
                                });
                        });
                })
                ->groupBy('section_fo.id', 'section_fo.name', 'section_fo.url', 'section_fo.is_system', 'section_fo.tag')
                ->orderBy('sort', 'ASC')
                ->orderBy('name', 'ASC');

            $sectionFO = $sectionFO->get()->toArray();

            $sameLevelParentQuery = Model\SectionsFoModel::select([
                'section_fo.id',
                'section_fo.name',
                $DB::raw('MAX(rel_institution_type_sections_labeling.label) as label')
            ])
                ->where('section_fo.id', $parentId)
                ->where('deleted', 0)
                ->where('hide', 0)
                ->institution()
                ->whereNotIn('section_fo.id', $excludedSections)
                ->where(function ($query) use ($institutionId) {
                    $query->where('is_system', 1)
                        ->orWhere(function ($query) use ($institutionId) {
                            $query->where('is_system', 0)
                                ->where('section_fo.institution_id', $institutionId);
                        });
                })
                ->leftJoin("rel_institution_type_sections_labeling", function ($join) use ($institutionTypeId, $institutionId) {
                    $join->on("rel_institution_type_sections_labeling.sections_id", "=", "section_fo.id")
                        ->where(function ($query) use ($institutionTypeId, $institutionId) {
                            $query->where(function ($query) use ($institutionTypeId) {
                                $query->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
                                $query->whereNull("rel_institution_type_sections_labeling.institution_id");
                            })
                                ->orWhere(function ($query) use ($institutionId) {
                                    $query->where("rel_institution_type_sections_labeling.institution_id", "=", $institutionId);
                                    $query->whereNull("rel_institution_type_sections_labeling.institution_type_id");
                                });
                        });
                });

            $sameLevelParent = optional($sameLevelParentQuery->first())->toArray();

            if (!empty($sameLevelParent)) {
                \System\Registry::set('menu_same_level_parent', !empty($sameLevelParent['label']) ? $sameLevelParent['label'] : ($sameLevelParent["name"] ?? ''));
            }
        }

        if ($sectionId == 37) {
            $sectionFO = $sectionFO ?? null;
        }

        \System\Registry::set('menu_same_level', $sameLevel);

        return $sectionFO;
    }
}

if (!function_exists('getPageContents')) {
    /**
     * Funzione che restituisce l'eventuale contenuto di una pagina,
     * quindi tutti i suoi paragrafi ed eventuali loro richiami
     *
     * @param int|null $currentPageId Id della pagina corrente
     * @return array
     * @throws Exception
     */
    function getPageContents(int $currentPageId = null): array
    {
        // Estraggo i paragrafi associasti a questa sezione con eventuali relativi richiami
        $selectFields = [
            'content_section_fo.id',
            'content_section_fo.name',
            'content_section_fo.content',
            'content_section_fo.last_update_date',
            'content_section_fo.created_at as create_at',
            'content_section_fo.updated_at as update_at',
            'content_section_fo.section_fo_parent_id',
            'content_section_fo.institution_id',
            'content_section_fo.user_id',
            'content_section_fo.owner_id',
        ];

        // I campi aggiuntivi vengono composti nel punto di estensione dedicato.
        $selectFields = $selectFields ?? null;

        $query = ContentSectionFoModel::select($selectFields);

        $queryContents = $query->where('content_section_fo.section_fo_id', $currentPageId)
            ->with('recallsConfig', function ($query) {
                $query->select(['id', 'section_fo_id', 'section_bo_id', 'recall_label', 'recall_fo_id']);
                $query->with('sectionBo:id,model');
            })
            ->whereNull('content_section_fo.deleted_at')
            ->orderBy('content_section_fo.sort', 'ASC')
            ->orderBy('content_section_fo.updated_at', 'DESC')
            ->orderBy('content_section_fo.id', 'ASC')
            ->get();

        $queryContents = $queryContents->isNotEmpty() ? $queryContents->toArray() : [];

        $contents = [];
        $db = new Database();

        $currentId = null;
        $i = 0;

        if (!empty($queryContents)) {
            $hideCreatedAt = false;
        }

        //Recupero i richiami associati ai paragrafi della pagina corrente
        foreach ($queryContents as $content) {

            $contents[$i] = $content;
            $contents[$i]['hideCreatedAt'] = $hideCreatedAt;
            $j = 0;

            if (!empty($content['recalls_config'])) {
                foreach ($content['recalls_config'] as $recallConfig) {

                    // Verifico se per la pagina sono presenti dei richiami o meno
                    // Se sono presenti li appendo ai paragrafi
                    if (!empty($recallConfig['section_bo']) && $recallConfig['section_bo']['model'] !== null) {

                        $recalls = RecallsModel::where('recalls_configuration_id', $recallConfig['id'])
                            ->where('paragraph_id', $content['id'])
                            ->get()
                            ->toArray();

                        $ids = array_column($recalls, 'record_id');

                        // Setto i campi da recuperare
                        $field = config($recallConfig['section_bo']['model'], null, 'modelConfigs');
                        $fieldModel = $field['field'];

                        if ($fieldModel == null) {

                            $json = new JsonResponse();
                            $json->error('error', 'parametri non validi');
                        } else {
                            // Setto il modello su cui effettuare la query
                            $model = config($recallConfig['section_bo']['model'], null, 'modelConfigs');
                            $modelName = $model['model'];
                            $class = $modelName;

                            // Query per prelevare i dati dal db
                            $items = $class::whereIn("id", $ids)
                                //->select(['id'])
                                ->select([$db::raw("CONCAT(" . implode(", ' - ',", $fieldModel) . ") AS field"), 'id'])
                                ->get()
                                ->toArray();

                            if (!empty($items)) {
                                // Se il paragrafo corrente non è lo stesso di quello prima lo aggiungo nell'array con i richiami
                                $contents[$i]['recalls'][$j] =
                                    [
                                        'title' => $recallConfig['recall_label'],
                                        'section_fo_id' => $recallConfig['recall_fo_id'],
                                        'records' => $items,
                                        'recall_name' => !empty($recalls[0]) ? $recalls[0]['recall_name'] : null
                                    ];

                                $j++;
                            }
                        }
                    } else {
                        // Se per la pagina non ci sono dei richiami a degli archivi
                        // prendo solo i paragrafi
                        if (($currentId === $content['id'] && $i == 0) || $currentId !== $content['id']) {

                            $contents[$i] = $content;
                            $currentId = $content['id'];
                            $j = $i;
                            $i++;
                        }
                    }
                }
            }
            $i++;
        }
        return $contents;
    }
}

if (!function_exists('getSocialLinks')) {
    /**
     * Funzione che restituisce i link ai canali social dell'ente
     *
     * @return mixed
     * @throws Exception
     */
    function getSocialLinks(): mixed
    {
        $cache = new cache();
        $keyCache = PatOsInstituteId() . '_social_links';

        // Verifico se nella cache è presente il dato.
        if (!$socials = $cache->get($keyCache)) {

            $patInfo = patOsInstituteInfo(['socials']);

            if (!empty($patInfo['socials']) && is_array($patInfo['socials'])) {

                $socials = [];

                foreach ($patInfo['socials'] as $item) {

                    $socials[] = [
                        'name' => $item['meta_label'],
                        'icon' => $item['meta_icon'],
                        'url' => $item['meta_value'],
                    ];
                }
            }

            // Salvo in cache
            $cache->set($keyCache, $socials, 3600 * 24 * 30);
        }

        return $socials;
    }
}
if (!function_exists('filterArrayByKeyValue')) {

    /**
     * @param array $array Array su cui effettuare l'operazione
     * @param int|string $key Chiave da cercare nell'array
     * @param mixed $keyValue Valore da cercare nell'array
     * @return array
     */
    function filterArrayByKeyValue(array $array, int|string $key, mixed $keyValue): array
    {
        return array_filter($array, function ($value) use ($key, $keyValue) {
            return $value[$key] == $keyValue;
        });
    }
}

if (!function_exists('burgerMenuHtml')) {
    /**
     * Creazione html burger menu
     * @return string {html completo del burger menu}
     * @throws Exception
     */
    function burgerMenuHtml(): string
    {
        $cache = new Cache();

        $keyCache = PatOsInstituteId() . '_burger_menu';

        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Verifico se nella cache è presente il dato.
        if (!$html = $cache->get($keyCache)) {

            // Esclusione della sezione "Altre sezioni".
            $excludedSections = [19];
            array_push($excludedSections, ...checkPoliticSectionEmpty());

            // riprendo tutta l'alberatura dalla home
            $sectionFOController = new Helpers\Utility\SectionFrontOffice();
            $tree = $sectionFOController->getGroupedChildren(0, 0, null, $institutionTypeId, $excludedSections);

            $html = '<ul role="menubar">';

            $html .= nestedNodeMenuHtml($tree);

            $html .= '</ul>';

            // Salvo in cache
            $cache->set($keyCache, $html, 3600 * 24 * 30);
        }

        return $html;
    }
}

if (!function_exists('nestedNodeMenuHtml')) {
    /**
     * Funzione ricorsiva che costruisce la struttura del burger menu
     *
     * @param $tree {alberatura sito}
     * @return string {html ramo albero}
     * @throws Exception
     */
    function nestedNodeMenuHtml(&$tree): string
    {

        $html = '';
        foreach ($tree as $index => $currentNode) {

            // imposto il valore della classe a seconda della profondità del nodo
            switch ($currentNode['deep']) {
                case 1:
                    $class = "primo-livello";
                    break;
                case 2:
                    $class = "secondo-livello";
                    break;
                default:
                    $class = "terzo-livello";
                    break;
            }

            $name = escapeXss(!empty($currentNode['label']) ? $currentNode['label'] : $currentNode['name']);

            //se il nodo non ha figli inserisco il solo link
            if (empty($currentNode['children'])) {
                if (!empty($meta) && !empty($meta['meta_value'])) {
                    $html .= '<li><a href="'
                        . $meta['meta_value']
                        . '" target="_blank" aria-selected="false" class="' . $class . '">' . $name
                        . '</a><i class="fas fa-external-link-alt ms-2"></i></li>';
                } else {
                    $html .= '<li><a href="'
                        . siteUrl('page/' . $currentNode['id'] . '/' . urlTitle($name))
                        . '" aria-selected="false" class="' . $class . '">' . $name
                        . '</a></li>';
                }

                //se il nodo ha figli chiamo la funzione ricorsiva passando i suoi figli
            } else {
                $html .= '<li>';
                // Se il nodo è di terzo livello non mostro i figli
                if ($currentNode['deep'] < 3) {
                    $html .= '<details>';
                    $html .= '<summary class="' . $class . '" data-focus-mouse="false">' . $name . '</summary>';
                    $html .= '<ul>';
                    // inserisco come primo elemento della lista la pagina di snodo
                    if (!empty($meta) && !empty($meta['meta_value'])) {
                        $html .= '<li><a href="'
                            . $meta['meta_value']
                            . '" target="_blank" aria-selected="false" class="sotto-voce">' . $name
                            . '</a><i class="fas fa-external-link-alt ms-2"></i></li>';
                    } else {
                        $html .= '<li><a href="'
                            . siteUrl('page/' . $currentNode['id'] . '/' . urlTitle($name))
                            . '" aria-selected="false" class="sotto-voce">' . $name
                            . '</a></li>';
                    }
                    // chiamo la funzione ricorsiva passando i suoi figli
                    $html .= nestedNodeMenuHtml($currentNode['children']);
                    $html .= '</ul>';
                    $html .= '</details>';
                } else {
                    // inserisco come primo elemento della lista la pagina di snodo
                    $html .= '<li>';
                    if (!empty($meta) && !empty($meta['meta_value'])) {
                        $html .= '<li><a href="'
                            . $meta['meta_value']
                            . '" target="_blank" aria-selected="false" class="sotto-voce">' . $name
                            . '</a><i class="fas fa-external-link-alt ms-2"></i></li>';
                    } else {
                        $html .= '<li><a href="'
                            . siteUrl('page/' . $currentNode['id'] . '/' . urlTitle($name))
                            . '" aria-selected="false" class="sotto-voce">' . $name
                            . '</a></li>';
                    }
                    $html .= '</li>';
                }

                $html .= '</li>';
            }
        }
        return $html;
    }
}

if (!class_exists('JsonLdBreadcrumbs')) {
    /**
     * Sottoclasse di Breadcrumbs che traccia in un array pubblico le voci passate a
     * push()/unshift()
     *
     * Serve a ricavare il breadcrumb per il JSON-LD senza leggere proprietà o metodi
     * interni di System\Breadcrumbs
     */
    class JsonLdBreadcrumbs extends Breadcrumbs
    {
        public array $trackedItems = [];

        public function push($page, $href, $avoidSiteUrl = false)
        {
            parent::push($page, $href, $avoidSiteUrl);

            if ($page && $href) {
                $this->trackedItems[] = ['name' => $page, 'url' => $avoidSiteUrl ? $href : siteUrl($href)];
            }
        }

        public function unshift($page, $href, $avoidSiteUrl = false)
        {
            parent::unshift($page, $href, $avoidSiteUrl);

            if ($page && $href) {
                array_unshift($this->trackedItems, ['name' => $page, 'url' => $avoidSiteUrl ? $href : siteUrl($href)]);
            }
        }
    }
}

if (!function_exists('buildBreadcrumbs')) {
    /**
     * Costruisce le voci del breadcrumb della pagina.
     *
     * Unica fonte di verità per il breadcrumb: getBreadcrumb() ne stampa l'HTML e
     * getBreadcrumbItems() ne ricava l'array per il JSON-LD, così le due
     * rappresentazioni non possono divergere (hook 'c_l_h' e 'p_e_u_g_p_q' inclusi).
     *
     * @param Breadcrumbs|array|null $bread Breadcrumbs
     * @param bool $concat Indica se si deve fare il concatenamento
     * @param int|null $crumbCount Valorizzato con il numero di voci presenti prima della Home
     * @return System\Breadcrumbs
     */
    function buildBreadcrumbs(Breadcrumbs|array $bread = null, bool $concat = false, ?int &$crumbCount = null): System\Breadcrumbs
    {
        //Nella stessa richiesta la funzione è chiamata due volte (JSON-LD nel controller,
        //HTML nella vista) e l'hook 'p_e_u_g_p_q' fa una query per voce: memorizzo il risultato.
        static $memo = [];

        $memoKey = md5(serialize([$bread, $concat]));

        if (isset($memo[$memoKey])) {
            $crumbCount = $memo[$memoKey]['count'];

            return $memo[$memoKey]['breadcrumbs'];
        }

        $breadcrumbs = new JsonLdBreadcrumbs([
            'base_url' => '/',
            'ico_home' => ' ',
            'crumb_divider' => '<span class="separator">/</span>',
            'tag_open' => '<ol class="breadcrumb">',
            'tag_close' => '</ol>',
            'crumb_open' => '<li class="breadcrumb-item">',
            'crumb_last_open' => '<li class="active breadcrumb-item" aria-current="page">',
            'crumb_close' => '</li>',
        ]);

        $breadcrumbItems = System\Registry::get('__breadcrumbs_front_office');

        $i = 0;

        if (!empty($breadcrumbItems)) {

            $count = count($breadcrumbItems);

            foreach ($breadcrumbItems as $item) {

                $name = !empty($item->label) ? $item->label : $item->name;
                $name = html_entity_decode($name);

                if ($concat === true) {
                    if (!empty($meta) && !empty($meta['meta_value'])) {
                        $link = $meta['meta_value'];
                    } else {
                        $link = '/page/' . $item->id . '/' . urlTitle($name);
                    }
                } else {

                    if (!empty($meta) && !empty($meta['meta_value'])) {
                        $link = $meta['meta_value'];
                    } else {
                        $link = ($i != $count)
                            ? '/page/' . $item->id . '/' . urlTitle($name)
                            : '/';
                    }
                }


                if (!empty($meta) && !empty($meta['meta_value'])) {
                    $breadcrumbs->push(convertEncodeQuotes(html_entity_decode(escapeXss($name, true, false))), $link, true);
                } else {
                    $breadcrumbs->push(convertEncodeQuotes(html_entity_decode(escapeXss($name, true, false))), $link);
                }
                $i++;
            }
        }

        if (!empty($bread)) {

            foreach ($bread as $item) {

                $link = str_replace('.html', '', $item['link']);
                if ($concat === false) {
                    $link .= '.html';
                }
                $breadcrumbs->push(escapeXss($item['name'], true, false), $link);
                $i++;
            }
        }

        $crumbCount = $i;

            $breadcrumbs->unshift('Home', siteUrl(), true);

        $memo[$memoKey] = ['breadcrumbs' => $breadcrumbs, 'count' => $crumbCount];

        return $breadcrumbs;
    }
}

if (!function_exists('getBreadcrumb')) {
    /**
     * Funzione che costruisce il breadcrumb menu della pagina
     *
     * @param Breadcrumbs|array|null $bread Breadcrumbs
     * @param bool $concat Indica se si deve fare il concatenamento
     * @return string|null
     */
    function getBreadcrumb(Breadcrumbs|array $bread = null, bool $concat = false): ?string
    {
        $breadcrumbs = buildBreadcrumbs($bread, $concat, $crumbCount);

        return $crumbCount >= 1 ? $breadcrumbs->show() : null;
    }
}

if (!function_exists('getBreadcrumbItems')) {
    /**
     * Restituisce il breadcrumb in forma di array strutturato (name/url) invece che HTML,
     * a partire dalle stesse voci rese da getBreadcrumb(): gli URL sono quindi già
     * normalizzati (suffisso .html, override dell'hook 'p_e_u_g_p_q', Home dell'hook 'c_l_h').
     * Pensata per alimentare StructuredData::breadcrumbList() nel JSON-LD.
     *
     * @param array|null $bread Stesse voci passate a getBreadcrumb() (di solito $data['bread'])
     * @param bool $concat Stesso flag passato a getBreadcrumb() (di solito $data['concatBreadcrumb'])
     * @return array Elenco ordinato ['name' => ..., 'url' => ...]
     */
    function getBreadcrumbItems(?array $bread = null, bool $concat = false): array
    {
        $items = [];
        $breadcrumbsObj = buildBreadcrumbs($bread, $concat);
        $crumbs = $breadcrumbsObj instanceof JsonLdBreadcrumbs ? $breadcrumbsObj->trackedItems : [];

        $last = count($crumbs) - 1;

        foreach ($crumbs as $i => $crumb) {
            $items[] = [
                //Le voci sono già passate da escapeXss() per la resa HTML: qui serve il testo
                'name' => html_entity_decode($crumb['name']),
                //L'ultima voce è la pagina corrente: schema.org non ne richiede l'URL
                'url'  => $i < $last ? $crumb['url'] : null,
            ];
        }

        return $items;
    }
}

if (!function_exists('clearCustomLinksCache')) {

    /**
     * Invalida la cache dei custom link di un ente, in entrambe le posizioni.
     *
     * Sta qui e non nei controller admin perche' la chiave la costruisce getCustomLinks() poche
     * righe sotto: tenerne una sola definizione evita che le due si separino, che e' esattamente
     * il modo in cui questa cache era gia' stata disattivata una volta.
     *
     * Entrambe le posizioni, sempre: i punti che scrivono conoscono l'id del link, non la sua
     * posizione, e recuperarla sarebbe una query in piu' per risparmiare la cancellazione di una
     * chiave che si rigenera alla prima visita.
     *
     * @param int $institutionId
     * @return void
     * @throws Exception
     */
    function clearCustomLinksCache(int $institutionId): void
    {
        $cache = new Cache();

        foreach (['header', 'footer'] as $position) {
            $cache->delete($institutionId . '_get_link' . $position);
        }
    }
}

if (!function_exists('getCustomLinks')) {

    /**
     * Funzione che ristorna eventuali custom link dell'ente da inserire nel menu nell'header
     *
     * @param string $position Indica la posizione dei link (es: Header, Footer)
     * @return string|null
     * @throws Exception
     */
    function getCustomLinks(string $position = 'header'): ?string
    {

        $cache = new Cache();
        $keyCache = PatOsInstituteId() . '_get_link' . $position;

        // Cache ripristinata (era commentata). Senza, ogni pagina pagava due volte questa
        // funzione - una per l'header e una per il footer - e ogni volta una query piu' un
        // View::create()->render() per ciascun link: sull'ente di sviluppo sono 9 render per
        // pagina per un frammento di HTML che cambia solo quando un redattore tocca i link.
        // Stesso schema e stesso TTL di burgerMenuHtml() poche righe sopra.
        //
        // L'invalidazione e' in InstitutionAdminController, sui quattro punti che scrivono
        // InstitutionLinksModel (creazione, modifica, eliminazione, riordino): senza quella, un
        // link modificato sarebbe rimasto invisibile per 30 giorni. E' lo stesso difetto gia'
        // corretto per il burger menu.
        if (!$html = $cache->get($keyCache)) {

            $data['position'] = $position;

            $query = Model\InstitutionLinksModel::where('institution_id', PatOsInstituteId())
                ->where('position', $position)
                ->whereNull('deleted_at')
                ->orderBy('sort', 'ASC')
                ->get();

            $results = !empty($query) ? $query->toArray() : false;
            $html = null;

            if (!empty($results)) {

                $html = '';

                foreach ($results as $result) {

                    $data['result'] = $result;

                    $html .= View::create('general/links', $data, 'frontend/v1')->render();
                }
            }

            $cache->set($keyCache, $html, 3600 * 24 * 30);
        }

        return $html;
    }
}


if (!function_exists('getItMonth')) {
    /**
     * Funzione che ritorna la stringa del nome del mese in lingua italiana
     *
     * @param int|string $numMonth Numero indicante del mese
     * @return string
     */
    function getItMonth(int|string $numMonth): string
    {
        switch ($numMonth) {

            case ($numMonth == '1' || $numMonth == '01'):
                $month = 'Gennaio';
                break;

            case ($numMonth == '2' || $numMonth == '02'):
                $month = 'Febbraio';
                break;

            case ($numMonth == '3' || $numMonth == '03'):
                $month = 'Marzo';
                break;

            case ($numMonth == '4' || $numMonth == '04'):
                $month = 'Aprile';
                break;

            case ($numMonth == '5' || $numMonth == '05'):
                $month = 'Maggio';
                break;

            case ($numMonth == '6' || $numMonth == '06'):
                $month = 'Giugno';
                break;

            case ($numMonth == '7' || $numMonth == '07'):
                $month = 'Luglio';
                break;

            case ($numMonth == '8' || $numMonth == '08'):
                $month = 'Agosto';
                break;

            case ($numMonth == '9' || $numMonth == '09'):
                $month = 'Settembre';
                break;

            case ($numMonth == '10'):
                $month = 'Ottobre';
                break;

            case ($numMonth == '11'):
                $month = 'Novembre';
                break;

            default:
                $month = 'Dicembre';
        }

        return $month;
    }
}

if (!function_exists('getCommissionRole')) {
    /**
     * Funzione che ritorna il nome del ruolo ricoperto dal personale all'interno di una commissione,
     * nel front-office
     *
     * @param string|null $type Ruolo all'interno della commissione
     * @return string|null
     */
    function getCommissionRole(string $type = null): ?string
    {
        if (!empty($type)) {
            $types = [
                'vice-president' => 'Vicepresidente',
                'secretarie' => 'Segretario',
                'substitute' => 'Membro supplente',
                'member' => 'Membro'
            ];

            return $types[$type];
        }
        return null;
    }
}

if (!function_exists('getTitle')) {
    /**
     * @param string|null $title Titolo della pagina
     * @return string
     * @throws Exception
     */
    function getTitle(string $title = null): string
    {
        if (currentUrl() === siteUrl()) {
            $title = 'Homepage';
        }
        $institute = patOsInstituteInfo(['full_name_institution']);

        return 'Portale Trasparenza ' . $institute['full_name_institution'] . ' - ' . $title;
    }
}

if (!function_exists('getCurrentPageId')) {
    /**
     * @return int
     */
    function getCurrentPageId(): int
    {
        return (int) uri()->segment(2, 0);
    }
}
