<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Tassi di assenza --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        @php
                            $title = !empty($instance['structure']) ? $instance['structure']['structure_name'] : $instance['structure_name'];
                        @endphp
                        <h1 class="page-title">{{escape_xss: !empty($h1) ? $h1 : $title }}</h1>
                    </div>

                    <div class="attributi mb-3">
                        @if(!empty($instance['year']))
                            <div class="attributo">
                                <span class="titolo">Anno:</span>
                                <span class="mr-2 text-muted">{{escape_xss: $instance['year'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['month']))
                            @php
                                $periods = config('absenceRatesPeriod', null, 'app');
                                $i = 0;
                                $months = explode(',',$instance['month']);
                                $len = count($months)-1;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Mese:</span>
                                <span class="text-muted">
                                    @foreach($months as $month)
                                        @if(!empty($periods[$month]) || !empty($periods['0'.$month]))
                                            {{escape_xss: $periods[$month] ?? $periods['0'.$month]}}
                                            @if($i++ < $len)
                                                {{ ', ' }}
                                            @endif
                                        @endif
                                    @endforeach
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['presence_percentage']))
                            <div class="attributo">
                                <span class="titolo">Presenza (%):</span>
                                <span class="mr-2 text-muted">{{ $instance['presence_percentage'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['total_absence']))
                            <div class="attributo">
                                <span class="titolo">Assenza totale (%):</span>
                                <span class="mr-2 text-muted">{{ $instance['total_absence'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                </div>

                {{-- Right Menu --}}
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