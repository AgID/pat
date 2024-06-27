<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Menù laterale destro e riferimenti normativi --}}
<div class="col-lg-4">
    <aside class="sidebar">
        {{-- Menù --}}
        <h3 class="mb-2 mt-2 h6" style="text-transform: uppercase">In questa pagina</h3>
        <ul class="lista-bullet">
            @foreach($menuPages as $item)
                @if($item['is_system'] == 1)
                    @php
                        $url = ($item['url']!='#!') ? explode(',',$item['url']) : null;
                        $name = !empty($item['label']) ? $item['label'] : $item['name'];
                    @endphp
                    @if($item['id'] == uri()->segment(2,0))
                        <li class="current">
                            <a href="{{ siteUrl('page/'.((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name))) }}">
                                {{e: $name }}
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{siteUrl('page/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name)))}}">
                                {{e: $name }}
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
            {{-- Mostro alla fine le pagine non di sistema --}}
            @foreach($menuPages as $item)
                @if($item['is_system'] == 0)
                    @php
                        $url = ($item['url']!='#!') ? explode(',',$item['url']) : null;
                        $name = !empty($item['label']) ? $item['label'] : $item['name'];
                    @endphp
                    @if($item['id'] == uri()->segment(2,0))
                        <li class="current">
                            <a href="{{ siteUrl('page/'.((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name))) }}">
                                {{e: $name }}
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{siteUrl('page/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[0] :$item['id']).'/'. ((!empty($url) && is_array($url) && count($url)>1) ? $url[1] : urlTitle($name)))}}">
                                {{e: $name }}
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </aside>
</div>