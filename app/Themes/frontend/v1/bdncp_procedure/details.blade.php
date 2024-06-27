<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Atti delle amministrazioni --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                    </div>

                    <div class="attributi mb-3">
                        @if(!empty($instance))
                            @if(!empty($instance['cig']))
                                <div class="attributo" style="border: none;">
                                    <span class="titolo">CIG:</span>
                                    <span class="mr-2 text-muted">{{e: $instance['cig'] }}</span>
                                </div>
                            @endif

                            @if(!empty($instance['bdncp_link']))
                                <div class="attributo" style="border: none;">
                                    <span class="titolo"><!--<i class="fas fa-link"></i>--> Link BDNCP:</span>
                                    <span class="mr-2">
                                        <a class="mt-2 text-muted" href="{{$instance['bdncp_link']}}" target="_blank" aria-label="Link BDNCP (Apri in una nuova finestra)">
                                            {{$instance['bdncp_link']}}
                                        </a>
                                    </span>
                                </div>
                            @endif

                            @if(!empty($instance['measures']))
                                <div class="mt-2">
                                    <span style="font-weight: 600;">Provvedimenti relativi:</span>
                                    <ul>
                                        @foreach($instance['measures'] as $measure)
                                            <li>
                                                <a class="mr-2 text-muted"
                                                   href="{{ siteUrl('page/9/details/'. (int)$measure['id'].'/'.urlTitle($measure['object'])) }}">
                                                    {{e: $measure['object'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif
                    </div>


                    @if(!empty($procedureCat['publication']))
                        <div>
                            <div class="custom-separator"><h3 id="publication">FASE PUBBLICAZIONE</h3></div>

                            @php
                                $i = 0;
                            @endphp
                            @foreach($procedureCat['publication'] as $k => $v)
                                @if(empty($v['attachs']))
                                    <h4 class="testo-blu anchor page-subtitle mt-3">{{ $v['title'] }}</h4>

                                    {{-- Attach List --}}
                                        <?php $attId = $k; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; ?>
                                    {% include v1/layout/partials/attach_list %}
                                    @if(!empty($instance[$v['field'].'_notes']))
                                        <h5 class="testo-blu anchor page-subtitle mt-3">Note:</h5>
                                        <div class="attributo">
                                            <span class="mr-2 text-muted">{{xss: $instance[$v['field'].'_notes'] }}</span>
                                        </div>
                                    @endif
                                    @if($i++ < count($procedureCat['publication'])-1)
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($procedureCat['fostering']))
                        <div>
                            <div class="custom-separator"><h3 id="publication">FASE AFFIDAMENTO</h3></div>

                            @if(!empty($instance['commission']))
                                <h4 class="testo-blu anchor page-subtitle mt-3">Composizione delle commissioni
                                    giudicatrice</h4>


                                <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-2">Incarichi:</h3>
                                <ul>
                                    @foreach($instance['commission'] as $commission)
                                        <li>
                                            <a class="mr-2 text-muted"
                                               href="{{ siteUrl('page/67/details/'.$commission['id'].'/'.urlTitle($commission['name'])) }}">
                                                {{e: $commission['object'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                @if(!empty($instance['judging_commission_notes']))
                                    <h5 class="testo-blu anchor page-subtitle mt-3">Note</h5>
                                    <div class="attributo">
                                        <span class="mr-2 text-muted">{{xss: $instance['judging_commission_notes'] }}</span>
                                    </div>
                                @endif

                                <hr>
                            @endif

                            @php
                                $i = 0;
                            @endphp
                            @foreach($procedureCat['fostering'] as $k => $v)
                                @if(empty($v['attachs']))
                                    <h4 class="testo-blu anchor page-subtitle mt-3">{{ $v['title'] }}</h4>

                                    {{-- Attach List --}}
                                        <?php $attId = $k; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; ?>
                                    {% include v1/layout/partials/attach_list %}
                                    @if(!empty($instance[$v['field'].'_notes']))
                                        <h5 class="testo-blu anchor page-subtitle mt-3">Note:</h5>
                                        <div class="attributo">
                                            <span class="mr-2 text-muted">{{xss: $instance[$v['field'].'_notes'] }}</span>
                                        </div>
                                    @endif
                                    @if($i++ < count($procedureCat['fostering'])-1)
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($procedureCat['executive']))
                        <div>
                            <div class="custom-separator"><h3>FASE ESECUTIVA</h3></div>

                            @if(!empty($instance['board']))
                                <h4 class="testo-blu anchor page-subtitle mt-3">Composizione del Collegio consultivo
                                    tecnico</h4>

                                <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-2">Incarichi:</h3>
                                <ul>
                                    @foreach($instance['board'] as $board)
                                        <li>
                                            <a class="mr-2 text-muted"
                                               href="{{ siteUrl('page/67/details/'.$board['id'].'/'.urlTitle($board['name'])) }}">
                                                {{e: $board['object'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                @if(!empty($instance['advisory_board_technical_notes']))
                                    <h5 class="testo-blu anchor page-subtitle mt-3">Note</h5>
                                    <div class="attributo">
                                        <span class="mr-2 text-muted">{{xss: $instance['advisory_board_technical_notes'] }}</span>
                                    </div>
                                @endif

                                <hr>
                            @endif

                            @php
                                $i = 0;
                            @endphp
                            @foreach($procedureCat['executive'] as $k => $v)
                                @if(empty($v['attachs']))
                                    <h4 class="testo-blu anchor page-subtitle mt-3">{{ $v['title'] }}</h4>

                                    {{-- Attach List --}}
                                        <?php $attId = $k; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; ?>
                                    {% include v1/layout/partials/attach_list %}
                                    @if(!empty($instance[$v['field'].'_notes']))
                                        <h5 class="testo-blu anchor page-subtitle mt-3">Note:</h5>
                                        <div class="attributo">
                                            <span class="mr-2 text-muted">{{xss: $instance[$v['field'].'_notes'] }}</span>
                                        </div>
                                    @endif
                                    @if($i++ < count($procedureCat['executive'])-1)
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($procedureCat['sponsorship']))
                        <div>
                            <div class="custom-separator"><h3 id="sponsorship">FASE SPONSORIZZAZIONI</h3></div>

                            @php
                                $i = 0;
                            @endphp
                            @foreach($procedureCat['sponsorship'] as $k => $v)
                                @if(empty($v['attachs']))
                                    <h4 class="testo-blu anchor page-subtitle mt-3">{{ $v['title'] }}</h4>

                                    {{-- Attach List --}}
                                        <?php $attId = $k; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; ?>
                                    {% include v1/layout/partials/attach_list %}
                                    @if(!empty($instance[$v['field'].'_notes']))
                                        <h5 class="testo-blu anchor page-subtitle mt-3">Note:</h5>
                                        <div class="attributo">
                                            <span class="mr-2 text-muted">{{xss: $instance[$v['field'].'_notes'] }}</span>
                                        </div>
                                    @endif
                                    @if($i++ < count($procedureCat['sponsorship'])-1)
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($procedureCat['emergency_procedure']))
                        <div>
                            <div class="custom-separator"><h3 id="emergency_procedure">FASE PROCEDURE DI SOMMA URGENZA E
                                    DI PROTEZIONE
                                    CIVILE</h3>
                            </div>

                            @php
                                $i = 0;
                            @endphp
                            @foreach($procedureCat['emergency_procedure'] as $k => $v)
                                @if(empty($v['attachs']))
                                    <h4 class="testo-blu anchor page-subtitle mt-3">{{ $v['title'] }}</h4>

                                    {{-- Attach List --}}
                                        <?php $attId = $k; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; ?>
                                    {% include v1/layout/partials/attach_list %}
                                    @if(!empty($instance[$v['field'].'_notes']))
                                        <h5 class="testo-blu anchor page-subtitle mt-3">Note:</h5>
                                        <div class="attributo">
                                            <span class="mr-2 text-muted">{{xss: $instance[$v['field'].'_notes'] }}</span>
                                        </div>
                                    @endif
                                    @if($i++ < count($procedureCat['emergency_procedure'])-1)
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($procedureCat['project_finance']))
                        <div>
                            <div class="custom-separator"><h3 id="project_finance">FASE FINANZA DI PROGETTO</h3></div>

                            @php
                                $i = 0;
                            @endphp
                            @foreach($procedureCat['project_finance'] as $k => $v)
                                @if(empty($v['attachs']))
                                    <h4 class="testo-blu anchor page-subtitle mt-3">{{ $v['title'] }}</h4>

                                    {{-- Attach List --}}
                                        <?php $attId = $k; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; ?>
                                    {% include v1/layout/partials/attach_list %}
                                    @if(!empty($instance[$v['field'].'_notes']))
                                        <h5 class="testo-blu anchor page-subtitle mt-3">Note:</h5>
                                        <div class="attributo">
                                            <span class="mr-2 text-muted">{{xss: $instance[$v['field'].'_notes'] }}</span>
                                        </div>
                                    @endif
                                    @if($i++ < count($procedureCat['project_finance'])-1)
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($procedureCat['notes']) || !empty($attachs['_generalInfo']))
                        <div>
                            <div class="custom-separator"><h3>INFORMAZIONI GENERALI</h3></div>

                            {{-- Attach List --}}
                                <?php $attId = '_generalInfo'; ?>
                                <?php $listAttach = $attachs[$attId] ?? []; ?>
                            {% include v1/layout/partials/attach_list %}
                            @if(!empty($instance['notes']))
                                <h5 class="testo-blu anchor page-subtitle mt-3">Note</h5>
                                <div class="attributo">
                                    <span class="mr-2 text-muted">{{xss: $instance['notes'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                </div>

                {{-- Indice della pagina --}}
                {% include v1/layout/partials/right_menu %}

            </div>
        </div>
    </section>
</main>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}

{% endblock %}