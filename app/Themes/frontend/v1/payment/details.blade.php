<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

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

                            $object = 'Pagamento';
                            if(!empty($instance['object'])){
                                $object = $instance['object'];
                            }else{
                                $object = "Pagamento a ".($paymentSupplier[$instance['supplier']] ?? '');
                            }
                            $categories = [1 => 'Uscite correnti', 2=>'Uscite in conto capitale'];
                        @endphp
                        <h1 class="page-title">{{escape_xss: !empty($h1) ? $h1 : $object }}</h1>
                    </div>

                    <div class="attributi mb-3">

                        @if(!empty($instance['supplier']))
                            <div class="attributo">
                                <span class="titolo">Beneficiario:</span>
                                <span class="mr-2 text-muted">
                                    {{escape_xss: (!empty($paymentSupplier[$instance['supplier']]) ? $paymentSupplier[$instance['supplier']] : '') }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['year']))
                            <div class="attributo">
                                <span class="titolo">Anno:</span>
                                <span class="mr-2 text-muted">{{escape_xss: $instance['year'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['trimester']))
                            <div class="attributo">
                                <span class="titolo">Trimestre:</span>
                                <span class="mr-2 text-muted">{{escape_xss: $instance['trimester'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['compensation']))
                            <div class="attributo">
                                <span class="titolo">Importo:</span>
                                <span class="mr-2 text-muted">{{currency('filter=escape decimal_separator=, thousands_separator=.'): $instance['compensation'] }} &euro;</span>
                            </div>
                        @endif

                        @if(!empty($instance['expense_category']))
                            <div class="attributo">
                                <span class="titolo">Categoria:</span>
                                <span class="mr-2 text-muted">{{ $categories[$instance['expense_category']] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['expense_typology']))
                            <div class="attributo">
                                <span class="titolo">Tipologia:</span>
                                <span class="mr-2 text-muted">{{ $typologies[$instance['expense_typology']] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['notes']))
                            <h3 class="testo-blu page-subtitle mt-3">Dettagli</h3>
                            {{xss: $instance['notes'] }}
                        @endif

                    </div>

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                    {{-- Inclusione di librerie esterne --}}
                </div>
            </div>
        </div>
    </section>
</main>

{{-- Right Menu --}}
{% include v1/layout/partials/bottom_menu %}

{{-- Link utilii --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}

{% endblock %}
