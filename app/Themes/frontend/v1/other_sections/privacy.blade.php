<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Pagina pivot per le pagine di snodo --}}
{% extends v1/layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<main>
    <section class="my-2">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    {{-- Nome della pagina --}}
                    <div class="div-title">
                        <h1 class="mb-4 page-title">{{e: $pageName }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>
                    {{-- Contenuto della pagina --}}
                    @if(!empty($paragraphs))
                        {% include v1/layout/partials/page_content %}
                    @else
                        <p>
                            <strong>Informativa sul trattamento dei dati personali ai sensi dell’art. 13 del Regolamento (UE) 2016/679</strong><br>
                            Ai sensi del Regolamento (UE) 2016/679 e della normativa italiana vigente per la protezione dei dati personali, questa pagina descrive
                            il trattamento dei dati personali degli utenti che consultano il sito web “Portale Amministrazione Trasparente” {{ $_institution_info['full_name_institution'] }} (Titolare del Trattamento)
                            accessibile all’indirizzo <a href="{{ baseUrl() }}">{{ baseUrl() }}</a>. L’informativa riguarda esclusivamente il presente sito; gli eventuali siti esterni, raggiungibili mediante collegamenti,
                            sono disciplinati dalle rispettive informative.
                            <br>
                            <br>
                            <strong>Titolare del trattamento</strong><br>
                            Il titolare del trattamento dei dati personali è l’ENTE contattabile ai recapiti presenti a questa pagina.<br>
                            <br>
                            <strong>Responsabile della protezione dei dati</strong><br>
                            Il Responsabile della protezione dei dati (RPD/DPO) è contattabile ai recapiti forniti dall’Ente.<br>
                            <br>
                            <strong>Base giuridica del trattamento</strong><br>
                            I dati personali indicati in questa pagina sono trattati dal Titolare del trattamento nell'esecuzione dei propri compiti di interesse pubblico o comunque
                            connessi all'esercizio dei propri pubblici poteri.<br>
                            <br>
                            <strong>Dati trattati e finalità</strong><br>
                            Dati di navigazione: il funzionamento del portale comporta il trattamento di dati tecnici connessi alle comunicazioni Internet; tra questi possono
                            rientrare dati personali la cui trasmissione è implicita dell’uso dei protocolli di comunicazione di internet. Tali dati sono trattati per il
                            corretto funzionamento tecnico del sito.<br>
                            <br>
                            <strong>Cookie e altri strumenti di tracciamento</strong><br>
                            Si rimanda alla sezione cookie policy.
                            <br>
                            <br>
                            <strong>Modalità del trattamento e destinatari</strong><br>
                            I dati sono trattati mediante strumenti informatici, con misure tecniche e organizzative adeguate alla loro protezione,
                            da personale autorizzato del Titolare e da fornitori che svolgono attività per suo conto,
                            designati responsabili del trattamento ai sensi dell’art. 28 del Regolamento (UE) 2016/679, ove previsto.<br>
                            <br>
                            <strong>Trasferimenti internazionali</strong><br>
                            I dati sono trattati esclusivamente all'interno dell'Unione Europea
                            <br>
                            <br>
                            <strong>Conservazione</strong><br>
                            I dati personali saranno conservati per un arco di tempo non superiore al conseguimento delle finalità per le quali sono stati
                            raccolti o successivamente trattati, conformemente a quanto previsto dagli obblighi di legge. <br>
                            <br>
                            <strong>Esercizio dei diritti garantiti dalla legge</strong><br>
                            Gli interessati possono esercitare, nei casi e nei limiti previsti dagli artt. 15–22 del Regolamento (UE) 2016/679, i diritti di accesso,
                            rettifica, cancellazione, limitazione del trattamento, opposizione e, ove applicabile, portabilità dei dati, contattando
                            il Titolare del trattamento). Gli interessati che ritengono che il trattamento dei propri dati personali avvenga in violazione
                            della normativa applicabile possono proporre reclamo al Garante per la protezione dei dati personali, secondo le modalità indicate
                            sul sito www.garanteprivacy.it, ferma restando la possibilità di ricorrere all’autorità giudiziaria.
                            <br>
                            <br>
                            <strong>Aggiornamento Informativa</strong><br>
                            Le informazioni indicate all’interno della presente informativa possono essere oggetto di aggiornamento a seguito di:
                        <ul>
                            <li>
                                modifiche dell’attuale normativa vigente in materia di protezione dei dati personali;
                            </li>
                            <li>
                                aggiornamento dei servizi offerti tramite il presente sito web che impattino sulle attuali modalità di trattamento;
                            </li>
                            <li>
                                variazione/aggiornamento dei dati di contatto ivi indicati.
                            </li>
                        </ul>
                        Eventuali modifiche saranno pubblicate in questa pagina, pertanto, è consigliabile la consultazione periodica.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
{% include v1/layout/partials/bottom_menu %}

{{-- Cards Link --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
