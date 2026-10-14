<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

@php
$menuSameLevel = \System\Registry::get('menu_same_level');
$menuSameLevelParent = \System\Registry::get('menu_same_level_parent');
@endphp

{{-- Menù in fondo alla pagina --}}
{{-- Se si buole nascondere il menu quando non ci sono pagine figlie si può utilizzare la classe menu-same-level --}}
@if(!empty($menuPages))
<div class="container my-5@php echo !empty($menuSameLevel)?" menu-same-level":""@endphp">
    <h3 class="h4 fw-bold mt-4 mb-3">Esplora le sottosezioni di <span class="explorePage">{{ $menuSameLevelParent ?? $pageName }}</span></h3>
    <ul class="cards-correlate mb-4">
        {{-- Pero ogni pagina creo una voce nel menu --}}
        @foreach($menuPages as $item)
            @if($item['is_system'] == 1)
                @php
                    $url = ($item['url']!='#!') ? explode(',',$item['url']) : null;
                    $external_url = !empty($item['p_e_u_g_p_r']['meta_value']) ? $item['p_e_u_g_p_r']['meta_value'] : null;
                    $name = !empty($item['label']) ? $item['label'] : $item['name'];
                    $tag = !empty($item['tag']) ? '_tg' : '';
                @endphp
                @if($item['id'] == uri()->segment(2,0))
                    <li class="cards-correlate-item current">
                        <a href="{{ siteUrl('page'.$tag.'/'.((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name))) }}">
                            <h4>{{e: $name }}</h4>
                        </a>
                    </li>
                @else
                    @if(!empty($external_url))
                        <li class="cards-correlate-item">
                            <a href="{{$external_url}}" target="_blank">
                               <span class="d-flex align-items-center"><h4>{{e: $name }}</h4> <i class="fas fa-external-link-alt ms-2 "></i></span>
                            </a>
                        </li>
                    @else
                    <li class="cards-correlate-item">
                        <a href="{{siteUrl('page'.$tag.'/'.((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name)))}}">
                            <h4>{{e: $name }}</h4>
                        </a>
                    </li>
                    @endif
                @endif
            @endif
        @endforeach
        {{-- Mostro alla fine le pagine non di sistema --}}
        @foreach($menuPages as $item)
            @if($item['is_system'] == 0)
                @php
                    $url = ($item['url']!='#!') ? explode(',',$item['url']) : null;
                    $external_url = !empty($item['p_e_u_g_p_r']['meta_value']) ? $item['p_e_u_g_p_r']['meta_value'] : null;
                    $name = !empty($item['label']) ? $item['label'] : $item['name'];
                    $tag = !empty($item['tag']) ? '_tg' : '';
                @endphp
                @if($item['id'] == uri()->segment(2,0))
                    <li class="cards-correlate-item current">
                        <a href="{{ siteUrl('page'.$tag.'/'.((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name))) }}">
                            <h4>{{e: $name }}</h4>
                        </a>
                    </li>
                @else
                    @if(!empty($external_url))
                        <li class="cards-correlate-item">
                            <a href="{{$external_url}}" target="_blank">
                               <span class="d-flex align-items-center"><h4>{{e: $name }}</h4> <i class="fas fa-external-link-alt ms-2 "></i></span>
                            </a>
                        </li>
                    @else
                    <li class="cards-correlate-item">
                        <a href="{{siteUrl('page'.$tag.'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name)))}}">
                            <h4>{{e: $name }}</h4>
                        </a>
                    </li>
                    @endif
                @endif
            @endif
        @endforeach
    </ul>
</div>
@endif
<script>
document.addEventListener("DOMContentLoaded", () => {
    if(window.location.href.includes("/details/")){
        let breadCr = document.getElementsByClassName('breadcrumb-item');
        let explorePage = document.querySelector(".explorePage");

        if (breadCr.length >= 2 && explorePage) {
            let newPageName = breadCr[breadCr.length - 2].innerText;
            newPageName = newPageName.substring(0, newPageName.length - 2);
            explorePage.innerText = newPageName;
        }
    }
})
</script>
