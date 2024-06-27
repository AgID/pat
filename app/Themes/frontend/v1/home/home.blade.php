<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<section id="intro-home">
    <div class="container">
        <h2 class="mb-2"><span class="fas fa-university"></span> {{ $title }}</h2>
        <div>
            @if(!empty($welcomeText))
                {{ $welcomeText }}
            @else
                <p>I dati personali pubblicati sono riutilizzabili solo alle condizioni previste dalla normativa vigente sul
                    riuso dei dati pubblici (direttiva comunitaria 2003/98/CE e D.lgs. 36/2006 di recepimento della stessa),
                    in termini compatibili con gli scopi per i quali sono stati raccolti e registrati, e nel rispetto della
                    normativa in materia di protezione dei dati personali.
                    In questo portale saranno pubblicati, raggruppati secondo le indicazioni di legge, documenti, informazioni
                    e dati concernenti l'organizzazione dell'amministrazione, le sue attività e le relative modalità di
                    realizzazione.
                    ( Decreto Legislativo 14 marzo 2013, n.33 - Riordino della disciplina riguardante gli obblighi di
                    pubblicità,
                    trasparenza e diffusione di informazioni da parte delle pubbliche amministrazioni - pubblicato in Gazzetta
                    Ufficiale n. 80 in data 05/04/2013 - in vigore dal 20/04/2013).</p>
            @endif
        </div>
    </div>
</section>

<section id="argomenti">
    <div class="container">
        <h3 class="mb-5">Argomenti</h3>
        <ul class="lista-argomenti">
            @foreach($sections as $section)
                @php
                    $name = !empty($section['label']) ? $section['label'] :$section['name'];
                @endphp
                <li>
                    <a href="{{ siteUrl('page/'. $section['id'].'/'. urlTitle($name)) }}" title="Vai alla pagina {{ $name }}">
                        {{e: $name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}