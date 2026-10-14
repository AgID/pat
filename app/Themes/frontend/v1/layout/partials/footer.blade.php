<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed');
helper('form');
//
?>

{{-- Footer del patos --}}
@php
    $customLinks = getCustomLinks('footer');
@endphp


<footer>
    <div class="container">
        <div class="logo">
            <img {{xss: getInstitutionLogo() }}>
            <h2>{{e: $_institution_info['full_name_institution'] }}</h2>
        </div>

        <div class="wrapper">




            {{-- Colonna Contatti --}}
            <div class="colonna">
                <h3>Contatti</h3>
                <p>
                    {{e: $_institution_info['full_name_institution'] }} <br>

                    {{e: getInstitutionFullAddress() }} <br>

                    @if(!in_array('phone', $_hideInfo))
                        Telefono: {{e: $_institution_info['phone'] }} <br>
                    @endif

                    @if(!in_array('vat', $_hideInfo))
                        @if($_institution_info['id']==505 || $_institution_info['id'] == 150)
                            CODICE FISCALE:
                        @else
                            P. IVA:
                        @endif

                        {{e: $_institution_info['vat'] }} <br>
                    @endif

                    @if(!in_array('fiscal_code', $_hideInfo))
                        @if(!empty($_institution_info['fiscal_code']))
                            C.F.: {{e: $_institution_info['fiscal_code'] }} <br>
                        @endif
                    @endif

                    @if(!in_array('certified_email_address', $_hideInfo))
                        @if(!empty($_institution_info['certified_email_address']))
                            PEC: <a href="mailto:{{xss: $_institution_info['certified_email_address'] }}"
                                    style="word-break: break-word;">
                                {{e: $_institution_info['certified_email_address'] }}
                            </a>
                            <br>
                        @endif
                    @endif
                    @if(!in_array('email_address', $_hideInfo))
                        @if(!empty($_institution_info['email_address']))
                            <span class="email_footer">
                            EMAIL: <a href="mailto:{{xss: $_institution_info['email_address'] }}">
                                {{e: $_institution_info['email_address'] }}
                            </a>
                            </span>
                            <br>
                        @endif
                    @endif
                    @php
                        if(!empty($_extraMail)){
                           list($_lbEM, $_vEM) = explode("|", $_extraMail);
                           echo '<span class="email_footer">';
                           echo $_lbEM.": <a href='mailto:".$_vEM."'>".$_vEM;
                           echo'</span><br>';
                        }
                    @endphp

                </p>
                @php
                    $getSocialLinks = getSocialLinks();
                @endphp
                @if($getSocialLinks!=null)
                    <ul class="social">
                        @foreach($getSocialLinks AS $item)
                            <li>
                                <a title="{{e: $item['name'] }} (Apre una nuova scheda)" href="{{xss: $item['url'] }}"
                                   aria-label="{{e: $item['name'] }}" target="_blank">
                                    {{xss: $item['icon'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Colonna Link Utili --}}
            <div class="colonna">
                <h3>Link utili</h3>
                <ul>
                    @if(!empty($_institution_info['institutional_website_url']))
                        <li>
                            <a href="{{xss: $_institution_info['institutional_website_url'] }}"
                               title="Vai al sito istituzionale">
                                Sito istituzionale
                            </a>
                        </li>
                    @endif

                    @if(!empty($customLinks))
                        {{xss: $customLinks }}
                    @endif

                    @if(empty($register))
                        @if($_institution_info['id'] != 150)
                            @if(empty($_institution_info['privacy_url']))
                                <li>
                                    <a href="{{ siteUrl('/page/182/privacy') }}"
                                       title="Vai alla pagina di Privacy e Cookie policy">
                                        Privacy e Cookie policy
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a href="{{xss: $_institution_info['privacy_url'] }}"
                                       title="Vai alla pagina Privacy">
                                        Privacy
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ siteUrl('/page/181/cookie-policy') }}"
                                       title="Vai alla pagina Cookie policy">
                                        Cookie policy
                                    </a>
                                </li>
                            @endif
                        @endif

                        <li>
                            <a href="{{ siteUrl('/page/256/accessibiltà') }}" title="Vai alla pagina Accessibilità">
                                Accessibilità
                            </a>
                        </li>
                    @endif

                </ul>
            </div>

            @if(!empty($_institution_info['footer_text']) && empty($register))
                {{-- Colonna personalizzata per l'ente --}}
                <div class="colonna">
                    <h3>Informazioni</h3>
                    <p>{{xss: $_institution_info['footer_text'] }}</p>
                </div>
            @endif


                <div class="colonna">
                    <img class="logo-it" src="{{ baseUrl('assets/frontend/v1/img/logo_lg_it.png') }}"
                         alt="Il presente sito applica le nuove linee guida di design per i servizi web della PA">
                </div>


        </div>
    </div>


</footer>

{{-- Modale ricerca --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modalRicerca" aria-labelledby="modalRicercaTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="container py-5">
                <div class="modal-header">
                    <button data-bs-dismiss="modal" type="button" aria-label="Chiudi ricerca"><i
                                class="fas fa-arrow-left"></i></button>
                    <h2 class="modal-title " id="modalRicercaTitle">Cerca</h2>
                </div>
                {{ form_open('search',['id'=>'prev_search','name'=>'prev_search','class'=>'prev_search','method'=>'GET']) }}
                <div class="form-row position-relative mb-4">
                    <label class="sr-only" for="testo_ricerca">Inserisci il testo da cercare</label>
                    {{ form_input([
                        'id' => 'testo_ricerca',
                        'placeholder' => 'Cerca informazioni, persone, servizi',
                        'autocomplete' => 'off',
                        'name' => 's'
                    ]) }}
                    <span class="icona-cerca fas fa-search"></span>
                </div>

                <div class="form-row mb-4">
                    <div class="form-check">
                        {{ form_checkbox([
                            'id' => 'exact_search',
                            'name' => 'exact_search',
                            'value' => '1',
                            'class' => 'form-check-input'
                        ]) }}
                        <label class="form-check-label" for="exact_search">
                            Cerca frase esatta
                        </label>
                        <span style="cursor:pointer;"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              data-bs-container="body"
                              title="Seleziona questa opzione per cercare esattamente la frase inserita">
                            <i class="far fa-question-circle fa-xs"></i>
                        </span>
                    </div>
                </div>

                <div class="form-row">
                    <button type="submit" id="btn_search" class="btn btn-outline-primary">
                        Cerca
                    </button>
                </div>
                {{ form_close() }}
            </div>
        </div>
    </div>
</div>

<script>

    // Autofocus sul campo di ricerca
    $(document).ready(function () {

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });

        $('#modalRicercaBtn').on('click', function (e) {
            e.preventDefault();
            setTimeout(() => {
                $('#testo_ricerca').focus();
            }, "480")
        });
        $('#modalRicercaCompactBtn').on('click', function (e) {
            e.preventDefault();
            setTimeout(() => {
                $('#testo_ricerca').focus();
            }, "480")
        });


        $('.set-active-label input[type=text]').each(function (i) {
            let input = $(this)[0].id;
            if (input !== null && input !== undefined) {
                let fieldValue = $('#' + input).val();
                if (fieldValue !== null && fieldValue !== undefined && fieldValue !== '') {
                    $('[for=' + input + ']').attr('class', 'active');
                }
            }
        });


        document.querySelectorAll('.ts-select-pat').forEach((select) => {

            const isMultiple = select.hasAttribute('multiple');

            new TomSelect(select, {
                maxItems: isMultiple ? null : 1, // numero di elementi selezionabili
                maxOptions: 200, //numero di opzioni visibili nella tendina
                allowEmptyOption: !isMultiple,
                placeholder: isMultiple ? "Seleziona una o più opzioni" : "Seleziona",
                create: false,
                persist: false,
                hideSelected: true,
                closeAfterSelect: !isMultiple,
                render: {
                    no_results: function(data, escape) {
                        //Label mostrata quando non vengono trovati risultati
                        return '<div class="no-results">Nessun risultato</div>';
                    }
                },
                plugins: isMultiple ? {
                    //select multipla
                    remove_button: {
                        title: 'Rimuovi questo elemento'
                    },
                    clear_button: {
                        title: 'Rimuovi tutti gli elementi selezionati'
                    }
                } : {
                    //select singola
                    remove_button: {
                        title: 'Rimuovi questo elemento'
                    },
                },
                onInitialize: function () {
                    updateClearButtonClass(this);
                    if (!isMultiple){
                        //Nel caso sia singola serve per mostrare il placeholder.
                        this.clear(true);
                    }
                },

                onChange: function () {
                    updateClearButtonClass(this);
                },

                onItemAdd: function() {
                    //Quando viene aggiunto un elemento pulisco la ricerca
                    this.setTextboxValue('');
                    this.refreshOptions(false);
                }
            });

        });

        /* Questa funzione assegna una specifica classe al pulsante di clear per renderlo sempre visibile quando c'è almeno una option selezionata */
        function updateClearButtonClass(elem) {
            const btn = elem.control.querySelector('.clear-button');

            if (!btn) return;

            if (elem.items.length > 0) {
                btn.classList.add('clear-button-os');
            } else {
                btn.classList.remove('clear-button-os');
            }
        }


    });
</script>
