<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<section id="risultati-ricerca">
    <div class="container">
        <div class="row">

            {% include v1/layout/partials/open_data %}

            <div class="col-lg-12">
                @if(!empty($instances['data']) && $instances['data'] != '_hide')
                    <ul class="card-list">
                        @foreach($instances['data'] as $instance)
                            <li>
                                <a
                                        href="{{ siteUrl('page/'.(!empty($finalSectionId) ? $finalSectionId : '#!').'/details/'.$instance['id'].'/'.urlTitle($instance['name'])) }}"
                                        data-id="{{e: $instance['id'] }}"
                                        style="display: unset;">

                                    <h3><i class="far fa-file-alt"></i> {{e: $instance['name'] }}</h3>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if(!empty($_institution_info['show_update_date']) && !empty($instances) && !empty($latsUpdatedElement))
                        <p class="data-creazione mt-5" style="font-size: 14px;">
                            <span class="icona far fa-clock"></span>
                            @php
                                $hideCreatedAt = false;
                            @endphp

                            <strong>
                                @if(!empty($hideCreatedAt))
                                    {{ !empty($latsUpdatedElement['updated_at']) ? 'Contenuto aggiornato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) }}
                                    @else
                                {{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                                {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) }}
                            @endif
                            </strong>
                        </p>
                    @endif

                    {{-- Paginazione della tabella --}}
                    {{ paginateBootstrap($instances) }}
                @else
                    @if(!isset($hideElementCheck))
                        @if($instances['data'] != '_hide')
                            <div class="box-card-shadow my-4">
                <h5 class="title">
                    <svg class="icon">
                        <use href="/assets/frontend/v1/css/sprites.svg#it-note"></use>
                    </svg>
                    Nessun elemento presente
                </h5>
            </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
    @if((!empty($allowSearch) && !empty($instances['data'])) || (!empty($allowSearch) && empty($instances['data'])) || !empty(@$forceBottomMenu))
        {{-- Right Drawer --}}
        {% include v1/layout/partials/bottom_menu %}
    @else
        {{-- Right Drawer --}}
        {% include v1/layout/partials/bottom_menu %}
    @endif
</section>
