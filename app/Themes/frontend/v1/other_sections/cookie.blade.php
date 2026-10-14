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
                <div class="col-lg-12">
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
                            Le seguenti specifiche tecniche sono inerenti il Portale Amministrazione Trasparente</p>
                        <p>
                            <strong>Cookie</strong><br>
                            I cookie sono file di testo che i siti visitati dagli utenti inviano ai loro terminali e che
                            vengono ritrasmessi ai siti stessi alla visita successiva. I cookie si possono suddividere
                            in due macro-categorie: “cookie di profilazione” e “cookie tecnici”.<br>
                            <br>
                            <strong>Cookie di profilazione<br>
                            </strong> Il presente sito NON utilizza cookie di profilazione, cioè cookie volti a creare
                            profili relativi all'utente al fine di inviare messaggi pubblicitari in linea con le
                            preferenze manifestate nell'ambito della navigazione sul sito.&nbsp;Nessun dato personale
                            degli utenti viene di proposito acquisito dal sito.<br>
                            <br>
                            <strong>Cookie tecnici</strong><br>
                            Il sito utilizza cookie tecnici per consentire l’esplorazione sicura, rapida ed efficiente
                            del sito stesso e per fornire agli utenti i servizi richiesti. Per l'installazione di tali
                            cookie non è richiesto il preventivo consenso degli utenti.<br>
                            <br>
                            <strong>Cookie tecnici di sessione</strong><br>
                            I cookie di sessione sono utilizzati per la navigazione e per l’autenticazione ai servizi
                            online e alle aree riservate. L’uso di questi cookie (che non vengono memorizzati in modo
                            persistente sul computer dell’utente e svaniscono con la chiusura del browser) è
                            strettamente limitato alla trasmissione di identificativi di sessione (costituiti da numeri
                            casuali generati dal server) necessari per consentire la navigazione efficace del sito. I
                            cookie di sessione utilizzati in questo sito evitano il ricorso ad altre tecniche
                            informatiche potenzialmente pregiudizievoli per la riservatezza della navigazione degli
                            utenti e non consentono l’acquisizione di dati personali identificativi dell’utente.<br>
                            L’utilizzo di cookie permanenti è strettamente limitato all’acquisizione dei dati statistici
                            utili a comprendere il livello di utilizzo del proprio sito.<br>
                            <br>
                            <strong>Cookie analytics</strong><br>
                            Sono assimilati ai cookie tecnici e sono utilizzati per raccogliere informazioni, in forma
                            aggregata, sul numero degli utenti e su come questi visitano il sito. I dati ricavabili da
                            questi cookie (compresi gli indirizzi IP) sono gestiti dall’Ente in qualità di gestore del
                            sito esclusivamente per finalità statistiche e per l’elaborazione di report sull’utilizzo
                            del sito stesso.<br>
                            <br>
                            <strong>Cookie di terze parti</strong><br>
                            Non sono installati cookie di terze parti<br>
                            <br>
                            <strong>Come disabilitare i cookie (opt-out)<br>
                            </strong> La maggior parte dei browser accettano i cookies automaticamente, ma è possibile
                            rifiutarli. Se non si desidera ricevere o memorizzare i cookie, si possono modificare le
                            impostazioni di sicurezza del browser (Internet Explorer, Google Chrome, MozillaFirefox,
                            Safari Opera, ecc…). Ciascun browser presenta procedure diverse per la gestione delle
                            impostazioni:</p>
                        <p>
                            <br>
                            Microsoft Internet Explorer<br>
                            Da “Strumenti” selezionare “Opzioni internet”. Nella finestra pop up selezionare “Privacy” e
                            regolare le impostazioni dei cookies oppure tramite i link:<br>
                            <a href="https://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies">http://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies</a><br>
                            <a href="https://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies#ie=ie-9">http://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies#ie=ie-9</a><br>
                            <br>
                            Google Chrome per Desktop<br>
                            Selezionare “Impostazioni”, poi “Mostra impostazioni avanzate”, successivamente nella
                            sezione “Privacy” selezionare “Impostazione Contenuti” e regolare le impostazioni dei cookie
                            oppure accedere tramite i link:<br>
                            <a href="https://support.google.com/chrome/bin/answer.py?hl=en&amp;answer=95647&amp;p=cpn_cookies">https://support.google.com/chrome/bin/answer.py?hl=en&amp;answer=95647&amp;p=cpn_cookies</a><br>
                            <a href="https://support.google.com/accounts/answer/61416?hl=it">https://support.google.com/accounts/answer/61416?hl=it</a><br>
                            <br>
                            Google Chrome per Mobile<br>
                            Accedere tramite link:<br>
                            <a href="https://support.google.com/chrome/answer/2392971?hl=it">https://support.google.com/chrome/answer/2392971?hl=it</a><br>
                            <br>
                            Mozilla Firefox<br>
                            Selezionare “Opzioni” e nella finestra di pop up selezionare “Privacy” per regolare le
                            impostazioni dei cookie, oppure accedere tramite i link:</p>
                        <p>
                            <a href="https://support.mozilla.org/en-US/kb/Enabling%20and%20disabling%20cookies">http://support.mozilla.org/en-US/kb/Enabling%20and%20disabling%20cookies</a><br>
                            <a href="https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie">https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie</a><br>
                            <br>
                            Apple Safari<br>
                            Selezionare “Preferenze” e poi “Sicurezza” dove regolare le impostazioni dei cookie oppure
                            accedere tramite il link:<br>
                            <a href="https://support.apple.com/it-it/HT201265">https://support.apple.com/it-it/HT201265</a><br>
                            <br>
                            Opera<br>
                            Selezionare “Preferenze”, poi “Avanzate” e poi “Cookie” dove regolare le impostazioni dei
                            cookie oppure accedere tramite i link:</p>
                        <p>
                            <a href="https://www.opera.com/help/tutorials/security/cookies/">http://www.opera.com/help/tutorials/security/cookies/</a><br>
                            <a href="https://help.opera.com/Windows/10.00/it/cookies.html">http://help.opera.com/Windows/10.00/it/cookies.html</a><br>
                            <br>
                            Browser nativo Android<br>
                            Selezionare “Impostazioni”, poi “Privacy” e selezionare o deselezionare la casella “Accetta
                            cookie”.</p>
                        <p>
                            &nbsp;</p>
                        <div class="table-responsive">
                            <table summary="Elenco dei cookie presenti" class="table table-bordered table-sm">
                                <caption>
                                    <span><span>Elenco dei cookie presenti</span></span>
                                </caption>
                                <thead>
                                <tr class="intestazione-tabella">
                                    <th scope="col">
                                        NOME
                                    </th>
                                    <th scope="col">
                                        FINALITA'
                                    </th>
                                    <th scope="col">
                                        DURATA
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        PHPSESSID
                                    </td>
                                    <td>
                                        Propagazione session id
                                    </td>
                                    <td>
                                        sessione
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        cookieAccepted
                                    </td>
                                    <td>
                                        Cookie tecnico per la chiusura del banner informativa cookie
                                    </td>
                                    <td>
                                        15 giorni
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

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
