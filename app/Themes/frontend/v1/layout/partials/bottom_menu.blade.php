<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Menù in fondo alla pagina --}}
<div class="container">

    <h3 class="titolo-sm mb-4" style="text-transform: uppercase">In questa pagina</h3>
    <ul class="lista-bullet-orizzontale">
        {{-- Pero ogni pagina creo una voce nel menu --}}
        @foreach($menuPages as $item)
            @php
                $url = ($item['url']!='#!') ? explode(',', $item['url']) : null;
                $name = !empty($item['label']) ? $item['label'] : $item['name'];
            @endphp
            {{-- Se la pagina da inserire nel menù è quella corrente aggiungo la classe current --}}
            @if($item['id'] == uri()->segment(2,0))
                <li class="current">
                    <a href="{{ siteUrl('page/'. ((!empty($url)) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name))) }}">
                        {{e: $name }}
                    </a>
                </li>
            @else
                <li>
                    <a href="{{siteUrl('page/'. ((!empty($url)) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name))) }}">
                        {{e: $name }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
