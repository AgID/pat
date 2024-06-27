<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Pagina Crediti --}}
{% extends v1/layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<main>
    <section class="my-2">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    {{-- Nome della pagina --}}
                    <h1 class="mb-4">{{e: $pageName }}</h1>

                    <p style="font-size: 18px;">
                        <strong>Il Portale Amministrazione Trasparente di {{ $institutionName }}</strong> è realizzato
                        mediante il riuso
                        della soluzione applicativa <strong>PAT</strong>, pubblicata su Developers Italia, "<a
                                href="https://developers.italia.it/it/software/agid-agid-pat">il catalogo previsto
                            dalle
                            Linee Guida per l'Acquisizione e il Riuso del Software</a>" che include le soluzioni messe a
                        riuso
                        dalla Pubblica Amministrazione ai sensi dell'art. 69 del Codice dell'Amministrazione Digitale.

                        L'infrastruttura applicativa del sito è implementata, personalizzata e mantenuta da <a
                                href="https://www.isweb.it/" title="Vai sul sito di ISWEB">ISWEB S.p.A.</a>
                        che eroga specifici <a
                                href="https://www.isweb.it/pagina642_amministrazione-trasparente-di-agid-servizi-annessi-al-riuso.html"
                                title="Vai alla pagina dei servizi di ISWEB">servizi
                            di supporto al riuso</a> e che cura lo sviluppo di PAT.
                    </p>
                </div>

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