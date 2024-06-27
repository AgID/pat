<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<section id="risultati-ricerca">
    <div class="container">
        <div class="row">

            <div class="col-lg-12">
                @if(!empty($instances['data']))
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
                            <strong>{{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                                {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) }}</strong>
                        </p>
                    @endif

                    {{-- Paginazione della tabella --}}
                    {{ paginateBootstrap($instances) }}
                @else
                    <h5 class="font-weight-bold">Nessun elemento presente</h5>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Drawer --}}
    {% include v1/layout/partials/bottom_menu %}


</section>