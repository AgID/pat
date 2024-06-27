{{-- Form store Atti di programmazione --}}
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
                        Aggiunta Atto di programmazione
                    @elseif($_storageType === 'update')
                        Modifica Atto di programmazione
                    @else
                        Duplicazione Atto di programmazione
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/programming-act') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Atti di programmazione
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
                                {{-- Campo Oggetto --}}
                                <div class="form-group col-md-6" id="input_object">
                                    <label for="object">Oggetto *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($programming_act['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $programming_act['object'] : $programming_act['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object',
                                    ]) }}
                                </div>

                                {{-- Campo Data --}}
                                <div class="form-group col-md-6">
                                    <label for="issue_date">Data *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$programming_act['date'] }}"
                                               id="date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Tipo atto --}}
                                <div class="form-group col-md-6">
                                    <label for="act_type">Tipo atto *</label>
                                    <div class="select2-blue" id="input_act_type">
                                        {{ form_dropdown(
                                            'act_type',
                                            ['' => '',1=>'Programma biennale degli acquisti di beni e servizi',2=>'Programma triennale dei lavori pubblici'],
                                            @$programming_act['act_type'],
                                            'class="form-control select2-act_type" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Pubblica in "Opere pubbliche - Documenti di programmazione" --}}
                                <div class="form-group col-md-6">
                                    <label for="public_in_public_works">Pubblica in "Opere pubbliche - Documenti di
                                        programmazione"</label>
                                    <div class="select2-blue" id="input_public_in_public_works">
                                        {{ form_dropdown(
                                            'public_in_public_works',
                                            [0=>'No',1=>'Si'],
                                            @$programming_act['public_in_public_works'],
                                            'class="form-control select2-public_in_public_works" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Testo di descrizione --}}
                                <label for="description">Descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($programming_act['description']) ? $programming_act['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>
                        </div>
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

        @if(!empty($programming_act['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $programming_act['id'],
                'id' => 'programming_act_id',
                'class' => 'programming_act_id',
            ]) }}
        @endif

        {{ form_hidden('institute_id',checkAlternativeInstitutionId()) }}
        {{ form_close() }}
    </div>
</div>
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
{{ js('admin/get/config.js') }}
{{ js('ckeditor4/ckeditor.js', 'common') }}

<script type="text/javascript">
    $(document).ready(function () {

        //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
        $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
                e.preventDefault();
                return false;
            }
        });

        let formModified = false;

        {{-- Creazione campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description');

        {{-- Begin creazione campi select --}}
        {{-- Campo select Tipo atto --}}
        let $dropdownActType = $('.select2-act_type');
        $dropdownActType.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        {{-- Campo select Pubblica in "Opere pubbliche - Documenti di programmazione" --}}
        let $dropdownPublic = $('.select2-public_in_public_works');
        $dropdownPublic.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End creazione campi select --}}

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
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message, 'Atto di programmazione');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/programming-act') }}';
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
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(msg, 6000, '', false);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End salvataggio --}}

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
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

    });
</script>
{% endblock %}