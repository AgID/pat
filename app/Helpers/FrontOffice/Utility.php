<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use System\Breadcrumbs;
use System\Cache;
use System\Database;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('isAuth')) {

    /**
     * Funzione che verifica se un utente ha effettuato l'autenticazione.
     * @return bool|array
     */
    function isAuth(): bool|array
    {
        $identity = authPatOs()->getIdentity();

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
        render($layout, $data, $theme);
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
        return !empty($institution)
            ? 'src="' . baseUrl('media/' . $institution['short_institution_name'] . '/assets/images/' . $institution['simple_logo_file']) . '"alt="' . $institution['full_name_institution'] . '"'
            : '';
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

        // Recupero le pagine figlie della pagina corrente per il menù laterale destro
        $sectionFO = Model\SectionsFoModel::select(['name', 'section_fo.id', 'url', 'is_system'])
            ->where('parent_id', $sectionId)
            ->where('deleted', 0)
            ->where('hide', 0)
            ->institution()
            ->where(
                function ($query) use ($institutionId) {
                    $query->where('is_system', 1)
                        ->orWhere(function ($query) use ($institutionId) {
                            $query->where('is_system', 0)
                                ->where('section_fo.institution_id', $institutionId);
                        });
                })
            ->groupBy('section_fo.id')
            ->orderBy('sort', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();

        // Se la pagina corrente non ha figli, mostro nel menù le pagine dello stello livello
        if (empty($sectionFO)) {

            $sectionFO = Model\SectionsFoModel::select(['name', 'section_fo.id', 'url', 'is_system'])
                ->where('parent_id', $parentId)
                ->where('deleted', 0)
                ->where('hide', 0)
                ->institution()
                ->where(
                    function ($query) use ($institutionId) {
                        $query->where('is_system', 1)
                            ->orWhere(function ($query) use ($institutionId) {
                                $query->where('is_system', 0)
                                    ->where('section_fo.institution_id', $institutionId);
                            });
                    })
                ->groupBy('section_fo.id')
                ->orderBy('sort', 'ASC')
                ->orderBy('name', 'ASC')
                ->get()
                ->toArray();
        }

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
        // Estraggo i paragrafi associasti a questa sezione
        $queryContents = Model\ContentSectionFoModel::select(['content_section_fo.id as content_id', 'content_section_fo.name',
            'content_section_fo.content', 'content_section_fo.last_update_date', 'content_section_fo.created_at as create_at',
            'content_section_fo.updated_at as update_at'])
            ->where('content_section_fo.section_fo_id', $currentPageId)
            ->whereNull('content_section_fo.deleted_at')
            ->orderBy('content_section_fo.sort', 'ASC')
            ->orderBy('content_section_fo.id', 'ASC')
            ->get();

        $contents = [];
        $db = new Database();
        $i = 0;
        $j = 0;

        $currentId = null;

        //Recupero i richiami associati ai paragrafi della pagina corrente
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
        $institutionId = checkAlternativeInstitutionId();

        // Verifico se nella cache è presente il dato.
        if (!$html = $cache->get($keyCache)) {

            // riprendo tutta l'alberatura dalla home
            $sectionFOController = new Helpers\Utility\SectionFrontOffice();
            $tree = $sectionFOController->getGroupedChildren(0, 0, null, $institutionTypeId);

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
                $html .= '<li><a href="'
                    . siteUrl('page/' . $currentNode['id'] . '/' . urlTitle($name))
                    . '" aria-selected="false" class="' . $class . '">' . $name
                    . '</a></li>';

                //se il nodo ha figli chiamo la funzione ricorsiva passando i suoi figli
            } else {
                $html .= '<li>';
                // Se il nodo è di terzo livello non mostro i figli
                if ($currentNode['deep'] < 3) {
                    $html .= '<details>';
                    $html .= '<summary class="' . $class . '" data-focus-mouse="false">' . $name . '</summary>';
                    $html .= '<ul>';
                    // inserisco come primo elemento della lista la pagina di snodo
                    $html .= '<li><a href="' . siteUrl('page/' . $currentNode['id'] . '/' . urlTitle($name))
                        . '" aria-selected="false" class="sotto-voce" id="title-0-0">' . $name . '</a></li>';
                    // chiamo la funzione ricorsiva passando i suoi figli
                    $html .= nestedNodeMenuHtml($currentNode['children']);
                    $html .= '</ul>';
                    $html .= '</details>';
                } else {
                    // inserisco come primo elemento della lista la pagina di snodo
                    $html .= '<li>';
                    $html .= '<a href="' . siteUrl('page/' . $currentNode['id'] . '/' . urlTitle($name))
                        . '" aria-selected="false" class="sotto-voce">' . $name . '</a>';
                    $html .= '</li>';
                }

                $html .= '</li>';
            }

        }
        return $html;
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

        $breadcrumbs = new System\Breadcrumbs([
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

                if ($concat === true) {

                    $link = '/page/' . $item->id . '/' . urlTitle($name) . '.html';

                } else {

                    $link = ($i != $count)
                        ? '/page/' . $item->id . '/' . urlTitle($name) . '.html'
                        : '/';
                }

                $breadcrumbs->push(convertEncodeQuotes(escapeXss($name, true, false)), $link);
                $i++;
            }
        }

        if (!empty($bread)) {

            foreach ($bread as $item) {

                $breadcrumbs->push(escapeXss($item['name'], true, false), str_replace('.html', '', $item['link']) . '.html');
                $i++;

            }
        }

        return $i >= 1 ? $breadcrumbs->show() : null;
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
        return (int)uri()->segment(2, 0);
    }
}
