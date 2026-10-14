{{-- Form generazione paragrafi personalizzati --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
{{ form_open($formAction,$formSettings) }}
<div class="row justify-content-center">
    <div class="col-xl-10">

        <div class="card mb-4" id="card-filter">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>
                    @if($_storageType === 'insert')
                        Aggiunta paragrafo personalizzato {{ !empty($pageName) ? ' nella pagina <strong>'.$pageName.'</strong>' : '' }}
                    @elseif($_storageType === 'update')
                        Modifica paragrafo personalizzato {{ !empty($pageName) ? ' nella pagina <strong>'.$pageName.'</strong>' : '' }}
                    @else
                        Duplica paragrafo
                    @endif
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/generic-page') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna indietro
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="card-body" id="form-container">


                <div class="form-row d-flex align-items-end">
                </div>
                <hr class="patos-hr">


                {{-- Campo Tipologia --}}
                <div class="form-group">
                    <label for="title">Titolo Paragrafo</label>
                    {{ form_input([
                        'name' => 'title',
                        'value' => ($_storageType === 'duplicate') ? 'Copia di ' .$paragraph['name'] : (!empty($paragraph['name']) ? $paragraph['name'] : ''),
                        'placeholder' => 'Titolo Paragrafo',
                        'id' => 'input_title',
                        'class' => 'form-control input_title'
                    ]) }}
                </div>
                @if($_storageType === 'duplicate')
                    <div id="list_section_tree"></div>
                    <div class="text-muted col-md-12 mb-4">
                        <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} Scegliendo una destinazione diversa da
                        quella selezionata per il paragrafo, i richiami non verranno duplicati.
                    </div>
                @else
                @endif
                <div class="form-group">
                    <label for="content">Contenuto Paragrafo *</label>
                    {{ form_editor([
                        'name' => 'content',
                        'value' => !empty($paragraph['content']) ? $paragraph['content'] : null,
                        'placeholder' => 'Contenuto Paragrafo',
                        'id' => 'input_content',
                        'class' => 'form-control input_content',
                        'required' => null,
                    ]) }}
                </div>

                <div id="selects">
                    @php
                        $i = 0;
                    @endphp
                    @foreach($recalls as $recall)
                        <h3>{{$recall['recall_label']}}</h3>
                        <hr>
                        <div class="form-group">
                            <label for="recall_name">Titolo del richiamo</label>
                            {{ form_input([
                                        'name' => $recall['recall_label'] . '_name',
                                        'value' => !empty($recall['recalls']) ? $recall['recalls'][0]['recall_name'] : '',
                                        'placeholder' => 'Titolo del richiamo',
                                        'id' => 'input_recall_name',
                                        'class' => 'form-control input_recall_name'
                                    ]) }}
                        </div>

                        <div class="form-row d-flex align-items-end">
                            <div class="form-group col-md-9">
                                <label for="responsible">Riferimenti a {{$recall['recall_label']}}</label>
                                <div class="select2-blue">
                                    {{ form_dropdown(
                                        $recall['recall_label'].'[]',
                                        '',
                                        '',
                                        'class="form-control select2-recall-' . $recall['model'] . ' input_operator" multiple="multiple" data-placeholder="Seleziona o cerca ' . $recall['recall_label'] .'..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>
                            <div class="form-group col-md-3 disponibleResp" style="padding-top: 31px;">
                                {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addResponsible',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/' . $uri[$i] .'/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                            </div>
                        </div> 
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </div>


            </div>

            <div class="card-footer">

                    {{ btnSave() }}

            </div>
        </div>

    </div>
</div>


@if(!empty($sectionId))
    {{ form_input([
        'type' => 'hidden',
        'name' => 'section_id',
        'value' => $sectionId,
        'id' => 'section_id',
        'class' => 'section_id',
    ]) }}
@endif

{{ form_hidden('parent_id',$parentId) }}

{{ form_input([
            'type' => 'hidden',
            'name' => 'mode',
            'value' => $_storageType,
            'id' => 'mode',
            'class' => 'mode',
        ]) }}

@if(!empty($recalls))
    {{ form_input([
        'type' => 'hidden',
        'name' => 'recalls',
        'value' => implode(', ', array_column($recalls, 'model')),
        'id' => 'recallsConfig',
        'class' => 'recallsConfig',
    ]) }}
@endif

@if(!empty($paragraph['id']))
    {{ form_input([
        'type' => 'hidden',
        'name' => 'paragraph_id',
        'value' => $paragraph['id'],
        'id' => 'paragraph_id',
        'class' => 'paragraph_id',
    ]) }}
@endif

@if(!empty($institution_id))
    {{ form_input([
        'type' => 'hidden',
        'name' => 'institution_id',
        'value' => $institution_id,
        'id' => 'institution_id',
        'class' => 'institution_id',
    ]) }}
@endif
{{ form_close() }}

{% include layout/partials/form_modal %}

{% endblock %}
{% block css %}
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff !important;
        border-color: #006fe6 !important;
        color: #fff !important;
        padding: 0 10px !important;
        margin-top: 0.31rem !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, .7) !important;
        float: right !important;
        margin-left: 5px !important;
        margin-right: -2px !important;
    }

    .my-badge {
        font-size: 13px;
    }
</style>
{% endblock %}
{% block javascript %}
{{ js('ckeditor4/ckeditor.js', 'common') }}

<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    $(document).ready(function () {
        let oldSelected = [];
        let formModified = false;

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function(e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_content',{
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        let institutionId = document.getElementById('institution_id')?.value ?? '';
        let id = $('#section_id').val();
        let mode = $('#mode').val();

        //Se si sta duplicando un paragrafo appendo la select2 con l'alberatura delle pagine per scegliere la destinazione
        if (mode === 'duplicate') {

            $.ajax({
                type: 'GET',
                url: '{{ siteUrl('admin/generic-page/section/duplicate') }}',
                dataType: "JSON",
                data: {
                    id,
                    institution_id: institutionId
                },
                beforeSend: function () {

                },
                success: function (response) {

                    response = parseJson(response);
                    // Appendo la select2 con l'alberatura delle pagine per scegliere la destinazione
                    $('#list_section_tree').empty().html(response.data.select_tree).show();

                    let tree = $('#select_tree');
                    tree.select2({
                        dropdownParent: $('#form-container')
                    });
                    $dropdawnTree = tree;

                    // Preseleziono nella select la pagina di appartenenza del paragrafo che si sta duplicando
                    $dropdawnTree.val(id);
                    $dropdawnTree.trigger('change');

                },
                complete: function () {

                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);

                    // Funzione che genera il toast con gli errori
                    {{-- (vedere nel footer) --}}
                    createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
                }
            });

        }

        // Ids dei record dei richiami
        let recIds = '{{!empty($recallsIds) ? json_encode($recallsIds) : ''}}';
        let ids;

        if (recIds.length > 0) {
            ids = $.parseJSON(recIds);
        }

        {{-- Begin salvataggio --}}
        /**
         * Metdo per il salvataggio
         */
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            // Aggiorno il valore dei campi CKEDITOR prima che vengono recuperati per l'invio
            beforeSerialize: function ($Form, options) {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
                return true;
            },
            beforeSend: function () {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                let response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.msg, 'Pagine generiche');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/generic-page' . (!empty($sectionId) ? '?id='.$sectionId : null)) }}';
                }, 800);

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {

                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(response.errors.error, 3500);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End salvataggio --}}

        let tmp = $('#recallsConfig').val();
        let rec = (tmp !== null && tmp !== undefined) ? tmp.split(',') : null;

        // Select2 per i richiami
        if (rec !== null) {
            let i = 0;
            // Per ogni archivio richiamabile nella sezione, recupero i dati per la select2
            $.each(rec, function () {
                let recallModel = this.replace(/\s+/, "");
                let $dropdownRecall = $('.select2-recall-' + recallModel);
                $dropdownRecall.select2({
                    allowClear: true,
                    // Recupero i dati per le options della select
                    ajax: {
                        url: '{{siteUrl("/admin/asyncData")}}',
                        type: "get",
                        dataType: 'json',
                        delay: 250,
                        data: function (data) {
                            return {
                                model: recallModel,
                                searchTerm: data.term
                            };
                        },
                        error: function (jqXHR, status) {
                            let response = parseJson(jqXHR.responseText);
                        },
                        processResults: function (response) {
                            return {
                                results: response.data.options
                            };
                        },
                        cache: true
                    },
                });
                @if($_storageType === 'update' || $_storageType === 'duplicate')
                // Recupero gli elementi gia selezionati e li setto nella select
                $.ajax({
                    type: 'GET',
                    url: '{{siteUrl("/admin/asyncSelectedData")}}',
                    data: {
                        id: ids[i].join(','),
                        model: recallModel,
                    },
                    error: function (jqXHR, status) {
                        let response = parseJson(jqXHR.responseText);
                    }
                }).then(function (data) {
                    let item = data.data.selected;

                    oldSelected.push(data.data.selected);
                    // Creo l'opzione e l'appendo alla select
                    for (const el of item) {
                        var option = new Option(String(el.text), el.id, true, true);
                        $dropdownRecall.append(option).trigger('change');
                    }
                });
                @endif
                    i++;
            });
        }

        /**
         * Controllo per l'uscita dal form se i campi di input sono stati toccati
         */
        $(document).on('focus',
            '.select2-selection.select2-selection--single, .select2-selection.select2-selection--multiple, input',
            function (e) {
                formModified = true;
            });

        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });

        /**
         * Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form
         * Per i richiami
         */
        {{-- Vedere nel footer --}}
        openModalForm();

        /**
         * Funzione che viene eseguita alla chiusura del modale di aggiunta di nuovi elementi direttamente dal form.
         * Per i richiami
         */
        {{-- Vedere nel footer --}}
        closeModalForm();

        /**
         * Funzione che in base alla pagina di destinazioen scelta per il paragrafo duplicato,
         * genera le select2 per i richiami da visualizzare
         */
        $('#list_section_tree').on('select2:select', function (e) {
            var data = e.params.data;

            $('#selects').empty();

            $.ajax({
                type: 'GET',
                url: '{{ siteUrl('admin/generic-page/paragraph/duplicate/asyncRecalls') }}',
                dataType: "JSON",
                data: {
                    id: data.id,
                },
                beforeSend: function () {

                },
                success: function (response) {

                    let buildHtml = createRecalls(response.data.recalls, response.data.uri);
                    $('#selects').append(buildHtml);
                    inizializeSelect(response.data.recalls);

                },
                complete: function () {

                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);

                    // Funzione che genera il toast con gli errori
                    {{-- (vedere nel footer) --}}
                    createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
                }
            });

        });

        /**
         * Metodo che inizializza le select2 per i richiami del paragrafo da duplicare
         */
        function inizializeSelect() {
            let arg = arguments;
            let recalls = arg[0];

            $.each(recalls, function (i, item) {
                {{-- Select2 campo  "Responsabile non disponibile" --}}
                let $dropdown = $('.select2-recall-' + item.model);
                $dropdown.select2({
                    allowClear: true,
                    //Recupero i dati per le options della select
                    ajax: {
                        url: '{{siteUrl("/admin/asyncData")}}',
                        type: "get",
                        dataType: 'json',
                        delay: 250,
                        data: function (data) {
                            return {
                                model: item.model,
                                searchTerm: data.term
                            };
                        },
                        error: function (jqXHR, status) {
                            let response = parseJson(jqXHR.responseText);
                        },
                        processResults: function (response) {
                            return {
                                results: response.data.options
                            };
                        },
                        cache: true
                    },
                });

                // Se la pagina scelta come destinazione per il paragrafo è la stessa del paragrafo che si sta duplicando,
                // preseleziono gli stessi richiami
                let id = $('#section_id').val();
                if (id == item.section_fo_id) {
                    for (const el of oldSelected[i]) {
                        var option = new Option(String(el.text), el.id, true, true);
                        $dropdown.append(option).trigger('change');
                    }
                }

            });
        }
    });

    /**
     * Metodo che crea le select2 per i richiami del paragrafo da duplicare
     * @returns {string}
     */
    function createRecalls() {
        let arg = arguments;
        let recalls = arg[0];
        let urls = arg[1];

        let html = '';
        let i = 0;
        $.each(recalls, function (i, item) {

            html += '<div class="form-row d-flex align-items-end">';
            html += '<div class="form-group col-md-9">';
            html += '<label for="' + urls[i] + '">Riferimenti a ' + item.recall_label + '</label>';
            html += '<select name="' + item.recall_label + '[]" class="select2-recall-' + item.model + '"  data-placeholder="Seleziona o cerca ' + item.recall_label + '..." data-dropdown-css-class="select2-blue" multiple="multiple" style="width: 100%;">' + '</select>';
            html += '</div>';
            html += '</div>';
        });

        return html;
    }

</script>
{% endblock %}
