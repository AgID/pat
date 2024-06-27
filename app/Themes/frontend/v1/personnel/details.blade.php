<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<?php helper('form') ?>
{{-- Pagina per Personale --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

@php
    $anchorsNumber = 1;
@endphp

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <h1 class="mb-2 page-title">
                        {{ !empty($pageName) && in_array($currentPageId, [238,239,242]) ? ($pageName . '<br> <hr>') : '' }}
                        {{e: !empty($h1) ? $h1 : ( !empty($instance['firstname']) ? $instance['title'] . ' ' . $instance['lastname'] . ' ' . $instance['firstname'] : $instance['title'] . ' ' . $instance['full_name']) }}
                    </h1>
                    @if(!empty($instance))

                        @if(!empty($instance['role']))
                            <h3 class="text-secondary page-subtitle">
                                {{e: $instance['role']['name'] }}
                            </h3>
                        @endif

                        @if(!empty($openData))
                                <button type="button" class="btn btn-xs btn-primary open-data-download-btn" id="btn-open-model-data">
                                    <span class="fas fa-save"></span> Scarica Open data
                                </button>
                            <br><br>
                        @endif

                        @if(!empty($instance['photo']))
                            <img class="img-evidenza-personale" title="Foto Personale"
                                 src="{{baseUrl('media/' . $_institution_info['short_institution_name'] . '/assets/images/' . $instance['photo'])}}"
                                 alt="Foto di {{escape_xss: $instance['full_name'] }}">
                        @endif

                        <div class="attributi">
                            @if(!empty($instance['role']))
                                <h3 id="anchor1" class="testo-blu anchor page-subtitle">Ruolo</h3>
                                @if(!empty($instance['in_office_since']))
                                    <div class="attributo">
                                        <span class="titolo">In carica dal:</span>
                                        <span class="mr-2 text-muted">{{date('d|date'): $instance['in_office_since'] }}
                                            {{ getItMonth(date('m', strtotime($instance['in_office_since']))) }}
                                            {{date('Y|date'): $instance['in_office_since'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($instance['in_office_until']))
                                    <div class="attributo">
                                        <span class="titolo">In carica fino al:</span>
                                        <span class="mr-2 text-muted">{{date('d|date'): $instance['in_office_until'] }}
                                            {{ getItMonth(date('m', strtotime($instance['in_office_until']))) }}
                                            {{date('Y|date'): $instance['in_office_until'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($instance['on_leave']))
                                    <div class="attributo">
                                        <span class="titolo">In aspettativa</span>
                                    </div>
                                @endif

                                <div class="attributo">
                                    <span class="titolo">Ruolo:</span>
                                    <span class="mr-2 text-muted">{{e: $instance['role']['name'] }}</span>
                                </div>

                                @if(in_array($instance['role_id'],['8', '7', '9', '12']) && !empty($instance['political_organ']))
                                    @php
                                        $i = 0;
                                        $len = count($instance['political_organ'])-1;
                                        $organs = config('politicalAdministrative', null, 'app');
                                    @endphp

                                    <div class="attributo">
                                        <span class="titolo">Organo politico-amministrativo:</span>
                                        @foreach($instance['political_organ'] AS $political_organ)
                                            @if(array_key_exists($political_organ['political_organ_id'], $organs))
                                            <span class="text-muted">{{e: $organs[$political_organ['political_organ_id']] }}</span>
                                            {{ $i++ < $len ? ',  ' : '' }}
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @if(in_array($instance['role_id'],['5', '7']) && !empty($instance['political_role']))

                                    <div class="attributo">
                                            <span class="titolo">Incarico di stampo politico</span>

                                        <span class="mr-2 text-muted">{{e: $instance['political_role'] }}</span>
                                        {{ $i++ < $len ? ',  ' : '' }}
                                    </div>
                                @endif
                            @endif
                        </div>

                        <h3 id="anchor2" class="testo-blu anchor page-subtitle mt-2">Contatti</h3>
                        <div class="attributi">
                            <div class="attributo">
                                <span class="titolo">Email:</span>
                                @if(!empty($instance['email']))
                                    <a class="mr-2 text-muted" href="mailto:{{escape_xss: $instance['email'] }}">
                                        {{e: $instance['email'] }}
                                    </a>
                                @else
                                    <span class="mr-2 text-muted">
                                        {{e: $instance['not_available_email_txt'] }}
                                    </span>
                                @endif
                            </div>

                            @if(!empty($instance['certified_email']))
                                <div class="attributo">
                                    <span class="titolo">Pec:</span>
                                    <a class="mr-2 text-muted"
                                       href="mailto:{{escape_xss: $instance['certified_email'] }}">
                                        {{e: $instance['certified_email'] }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($instance['phone']))
                                <div class="attributo">
                                    <span class="titolo">Telefono:</span>
                                    <a class="mr-2 text-muted" href="{{escape_xss: $instance['phone'] }}">
                                        {{e: $instance['phone'] }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($instance['mobile_phone']))
                                <div class="attributo">
                                    <span class="titolo">Telefono mobile:</span>
                                    <a class="mr-2 text-muted" href="{{escape_xss: $instance['mobile_phone'] }}">
                                        {{e: $instance['mobile_phone'] }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($instance['fax']))
                                <div class="attributo">
                                    <span class="titolo">Fax:</span>
                                    <a class="mr-2 text-muted" href="{{escape_xss: $instance['fax'] }}">
                                        {{e: $instance['fax'] }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($instance['responsible_structures']))
                                <h3 id="anchor3" class="testo-blu anchor page-subtitle mt-2">Referente per le
                                    strutture</h3>
                                <ul>
                                    @foreach($instance['responsible_structures'] as $structure)
                                        <li>
                                            <a class="text-muted"
                                               href="{{ siteUrl('page/40/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}">
                                                {{e: $structure['structure_name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if(!empty($instance['referent_structures']))
                                @php
                                    $i = 0;
                                    $len = count($instance['referent_structures'])-1;
                                @endphp
                                <div class="attributo">
                                    <span class="titolo">Strutture organizzative:</span>
                                    @foreach($instance['referent_structures'] as $s)
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/40/details/'.$s['id'].'/'.urlTitle($s['structure_name'])) }}">
                                            {{e: $s['structure_name']}}
                                        </a>
                                        {{ $i++ < $len ? ', ' : '' }}
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if(!empty($instance['responsibles']))
                            <h3 id="anchor4" class="testo-blu anchor page-subtitle mt-2">Procedimenti seguiti come
                                responsabile di procedimento</h3>
                            <ul>
                                @foreach($instance['responsibles'] as $responsibles)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/98/details/'.$responsibles['id'].'/'.urlTitle($responsibles['name'])) }}">
                                            {{e: $responsibles['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['measure_responsibles']))
                            <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-2">Procedimenti seguiti come
                                responsabile di provvedimento</h3>
                            <ul>
                                @foreach($instance['measure_responsibles'] as $responsibles)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/98/details/'.$responsibles['id'].'/'.urlTitle($responsibles['name'])) }}">
                                            {{e: $responsibles['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['commissions']))
                            <h3 id="anchor6" class="testo-blu anchor page-subtitle mt-2">Membro di</h3>
                            <ul>
                                @foreach($instance['commissions'] as $commission)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/245/details/'.$commission['id'].'/'.urlTitle($commission['name'])) }}">
                                            {{e: $commission['name'] }}
                                            {{ !empty($commission['pivot']['typology']) ? '-  ' . getCommissionRole($commission['pivot']['typology']) : '' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['assignments']))
                            <h3 id="anchor7" class="testo-blu anchor page-subtitle mt-2">Incarichi assegnati</h3>
                            <ul>
                                @foreach($instance['assignments'] as $assignments)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/67/details/'.$assignments['id'].'/'.urlTitle($assignments['name'])) }}">
                                            {{e: $assignments['object'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <h3 id="anchor8" class="testo-blu anchor page-subtitle mt-2">Altri dati</h3>
                        <p>
                            Contratto a tempo
                            determinato: {{ !empty($instance['determined_term']) ? 'si' : 'no' }}
                        </p>

                        @if(!empty($instance['notes']) && $instance['role']['political'])
                            <h3 id="anchor9" class="testo-blu anchor page-subtitle">
                                Documentazione Art. 14 e Art. 47, c. 1, Dlgs n.
                                33/2013; Art. 1,2,3,4 l. n. 441/1982
                            </h3>
                            <p>
                                {{xss: $instance['notes'] }}
                            </p>
                        @endif

                        @if(!empty($instance['extremes_of_conference']))
                            <h3 id="anchor10" class="testo-blu anchor page-subtitle">Estremi atto di nomina o
                                proclamazione</h3>
                            {{xss: $instance['extremes_of_conference'] }}
                        @endif

                        @if(!empty($instance['measures']))
                            <h3 id="anchor11" class="testo-blu anchor page-subtitle">Atti correlati</h3>
                            <ul>
                                @foreach($instance['measures'] as $measure)
                                    <li>
                                        <a class="mr-2"
                                           href="{{ siteUrl('page/103/details/'.$measure['id'].'/'.urlTitle($measure['object'])) }}">
                                            {{e: $measure['object'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['compensations']))
                            <h3 id="anchor12" class="testo-blu anchor page-subtitle">
                                Compensi connessi alla carica
                            </h3>
                            {{xss: $instance['compensations'] }}
                        @endif

                        @if(!empty($instance['trips_import']))
                            <h3 id="anchor13" class="testo-blu anchor page-subtitle">Importi di viaggi di servizio e
                                missioni</h3>
                            {{xss: $instance['trips_import'] }}
                        @endif

                        @if(!empty($instance['other_assignments']))
                            <h3 id="anchor14" class="testo-blu anchor page-subtitle">Altri incarichi con oneri a
                                carico della finanza
                                pubblica e relativi compensi </h3>
                            {{xss: $instance['other_assignments'] }}
                        @endif

                        @if(!empty($instance['other_info']))
                            <h3 id="anchor15" class="testo-blu anchor page-subtitle">Altre informazioni </h3>
                            {{xss: $instance['other_info'] }}
                        @endif

                        <!-- Storico incarichi -->
                        @if(!empty($instance['historical_datas']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor16" class="anchor testo-blu page-subtitle mt-4">
                                Storico incarichi</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-4">
                                    <thead>
                                    <tr class="intestazione-tabella">
                                        <th scope="col">Ruolo</th>
                                        <th scope="col">Struttura</th>
                                        <th scope="col">Dal</th>
                                        <th scope="col">Al</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($instance['historical_datas'] as $historical)
                                        <tr>
                                            <td>{{e: !empty($historical['historical_role']) ? $historical['historical_role'] : '' }}</td>
                                            <td>
                                                {{e: !empty($historical['historical_structure']) ? $historical['historical_structure'] : '' }}
                                            </td>
                                            <td>{{e: !empty($historical['historical_from_date']) ? date('d/m/Y', strtotime($historical['historical_from_date'])) : '' }}</td>
                                            <td>{{e: !empty($historical['historical_to_date']) ? date('d/m/Y', strtotime($historical['historical_to_date'])) : '' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if(!empty($instance['information_archive']))
                            <h3 id="anchor17" class="testo-blu anchor page-subtitle">Archivio informazioni </h3>
                            {{xss: $instance['information_archive'] }}
                        @endif


                        {{-- Attach List --}}
                        {% include v1/layout/partials/attach_list %}

                        {{--  Created/Update Info --}}
                        {% include v1/layout/partials/created_updated_info %}

                    @else
                        <h5 class="font-weight-bold">Nessun elemento presente</h5>
                    @endif
                </div>

                {{-- Indice della pagina --}}
                {% include v1/layout/partials/anchor_index %}

            </div>
        </div>
    </section>
</main>

{{-- Bottom Menu --}}
{% include v1/layout/partials/bottom_menu %}



{{-- Modal success open data--}}
<div class="modal fade" tabindex="-1" role="dialog" id="modalOpenDataSuccessPersonnel"
     aria-labelledby="modalOpenDataSuccessPersonnel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalTitle">Open data</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Chiudi">
                    <span class="fas fa-times"></span>
                </button>
            </div>
            <div class="modal-body mb-4">
                <div class="mb-4 mt-4 text-center">
                    <div class="col-md-12 mb-3">
                        <i class="fas fa-thumbs-up fa-2x color-primary"></i>
                    </div>
                    <div class="col-md-12">
                        <h5>Download open data avvenuto con successo!</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
@if(!empty($linkDownloadOpenData))
    <script type="text/javascript">
        $(document).ready(function () {
            $('#btn-modal-open-data-personnel').on('click', (e) => {
                e.preventDefault();
                let $this = $(e.currentTarget);

                $.fileDownload('{{ siteUrl('download/open-data/' . $linkDownloadOpenData) }}', {
                    httpMethod: "GET",
                    data: {
                        '{{ config('csrf_token_name',null,'app') }}': $("input[name='{{ config('csrf_token_name',null,'app') }}']").val(),
                    },
                    prepareCallback: (url) => {
                        $this.empty().append('<i class="fas fa-spinner fa-spin"></i> Attendere, elaborazione in corso..');
                    },

                    successCallback: (url) => {
                        $this.empty().append('<span class="fas fa-save"></span>  Scarica Open Data');
                        notificationShow('notity-opendata-success');
                        $('#modalOpenDataSuccessPersonnel').modal('show')

                        setTimeout(() => {

                            $('#modalOpenDataSuccessPersonnel').modal('hide');

                        }, 1500);
                    },

                    abortCallback: (url) => {
                        alert('Attenzione, si è verificato un blocco temporaneo. Riprovare più tardi')
                    },

                    failCallback: (responseHtml, url, error) => {
                        $('#modalOpenData').modal('hide');
                        $('#response-html-error-download').empty().html(jQuery(responseHtml).text());
                        notificationShow('notificationDism');

                        setTimeout(() => {

                            if ($('#notificationDism').is(':visible')) {
                                $('#notificationDism').hide();
                            }

                        }, 5000);
                    }
                });
                return false;
            });
        })
    </script>
@endif
{% endblock %}