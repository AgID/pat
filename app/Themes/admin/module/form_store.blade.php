{{-- Form store Modulistica --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row justify-content-center">
    <div class="col-xl-10">
        {{ form_open($formAction,$formSettings) }}
        <div class="card mb-4" id="card-filter">
            <h4 class="card-header">
                <span>
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>
                    @if($_storageType === 'insert')
                        Aggiunta Modulistica
                    @elseif($_storageType === 'update')
                        Modifica Modulistica
                    @else
                        Duplicazione Modulistica
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/module') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco moduli
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </h4>

            <div class="card-body card-primary">



                <div class="row">
                    <div class="text-muted col-md-9 mb-4">
                        <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} I Campi contrassegnati dal
                        simbolo asterisco (*) sono obbligatori.
                    </div>


                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div>



                            <div class="form-row d-flex align-items-end">
                            </div>
                            <hr class="patos-hr">


                            <div class="form-row d-flex align-items-end">
                                {{-- Inclusione di librerie esterne --}}

                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <label for="title">Titolo *</label>
                                    {{ form_input([
                                        'name' => 'title',
                                        'value' => !empty($module['title']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $module['title'] : $module['title']) : null,
                                        'placeholder' => 'Titolo',
                                        'id' => 'input_title',
                                        'class' => 'form-control input_title',
                                    ]) }}
                                </div>
                            </div>
                            <div class="form-row d-flex align-items-end">

                                    <div class="form-group col-md-6">
                                        <label for="typology">Tipologia *</label>
                                        <div id="input_typology">
                                            {{ form_dropdown(
                                                'typology',
                                                ['' => '','modulo'=>'Modulistica (pubblicazione in Atti generali - Modulistica)',
                                                 'dichiarazione sostitutiva'=>'Dichiarazione sostitutiva (pubblicazione in Attività e procedimenti - Dichiarazioni sostitutive e acquisizione d\'ufficio dei dati)'],
                                                @$module['typology'],
                                                'class="ts-typology" style="width: 100%;"'
                                            ) }}
                                        </div>
                                    </div>

                            </div>

                            {{-- Campo Valido per i procedimenti --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="proceedings">Valido per i procedimenti</label>
                                    <div id="input_proceedings">
                                        {{ form_dropdown(
                                            'proceedings[]',
                                            '',
                                            '',
                                            'class="ts-proceedings" multiple="multiple" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addStructures',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/proceeding/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>


                            <div class="form-group">
                                {{-- Campo Testo di descrizione --}}
                                <label for="description">Testo di descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($module['description']) ? $module['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Ordine --}}
                                <div class="form-group col-md-3">
                                    <label for="order">Ordine di visualizzazione *</label>
                                    {{ form_input([
                                        'type' => 'number',
                                        'name' => 'order',
                                        'value' => !empty($module['order']) ? $module['order'] : 1,
                                        'placeholder' => 'Ordine di visualizzazione',
                                        'id' => 'input_order',
                                        'class' => 'form-control input_order',
                                    ]) }}
                                </div>
                            </div>
                        </div>

                        {{-- Inclusione di librerie esterne --}}

                        {{-- END: Form --}}
                    </div>

                    {{-- ***** BEGIN: include attach**** --}}
                    {% include layout/partials/attach %}
                    {{-- ***** END: include attach**** --}}


                </div>
            </div>
            {{-- Card Footer --}}
            <div class="card-footer">

                    {{ btnSave() }}


            </div>
        </div>

        {{ form_input([
            'type' => 'hidden',
            'name' => '_storage_type',
            'value' => $_storageType,
            'id' => '_storage_type',
            'class' => '_storage_type',
        ]) }}

        @if(!empty($module['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $module['id'],
                'id' => 'module_id',
                'class' => 'module_id',
            ]) }}
        @endif

        @if(!empty($proceedingIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_proceedingIds',
                'value' => implode(',',$proceedingIds),
                'id' => '_proceedingIds',
                'class' => '_proceedingIds',
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


        {{ form_hidden('institute_id',checkAlternativeInstitutionId()) }}
        {{ form_close() }}
    </div>
</div>

{% include layout/partials/form_modal %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}

{{ js(verifyFirstOrLastSlash('admin/get/config.js')) }}
{{ js('ckeditor4/ckeditor.js', 'common') }}

<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    $(document).ready(function () {

        let formModified = false;
       let institutionId = document.getElementById('institution_id').value ?? '';

        const proceedingsSelect = createAjaxTomSelect({
            selector: ".ts-proceedings",
            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: "#_proceedingIds",
            model: 6,
            institutionId,
            placeholder: "Seleziona o cerca procedimenti...",
            loadSelected: {{ in_array($_storageType, ['update', 'duplicate']) ? 'true' : 'false' }},
            onError: function () {
                createValidatorFormErrorToast(
                    "Ops c'è un problema. Si prega di contattare l'assistenza clienti.",
                    5000,
                    "Validatore select"
                );
            }
        });

        new TomSelect(".ts-typology", TS_SINGLE);

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        /**
         * Metodo per il salvataggio
         */
                {{-- Begin salvataggio --}}
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
                $('.error-toast').remove();
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                let response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                createValidatorFormSuccessToast(response.data.message, 'Modulistica');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/module') }}';
                }, 800);
                @else
                setTimeout(function () {
                    window.parent.$('#formModal').modal('hide');

                }, 800);
                @endif

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                //Messaggi di errore del validatore campi e degli allegati
                let msg = [response.data.error_partial_attach, response.errors.error].filter(Boolean).join(", ");

                // Funzione che genera il toast con gli errori
                createValidatorFormErrorToast(msg, 6000, '', false);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });


        /**
         * Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form
         */
        openModalForm();

        /**
         * Funzione che viene eseguita alla chiusura del modale di aggiunta di nuovi elementi direttamente dal form.
         * Pulisce l'iframe.
         */
        closeModalForm();

        /**
         * Controllo per l'uscita dal form se i campi di input sono stati toccati
         */
        document.addEventListener("focusin", function (event) {
            if (event.target.matches(".ts-control, input")) {
                formModified = true;
            }
        });

        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });

        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        checkIfRestore();

    });
</script>
{% endblock %}
