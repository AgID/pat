{{-- Form BDNCP - Atti e Documenti di carattere generale --}}
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
                        Aggiunta nuovo atto/documento
                    @elseif($_storageType === 'update')
                        Modifica atto/documento
                    @else
                        Duplicazione atto/documento
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/general-acts-documents') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco atti e documenti di carattere
                                    generale
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

                            {% include general-acts-documents/normative_info %}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Codice Oggetto --}}
                                <div class="form-group col-md-12">
                                    <label for="object" id="cig-label">Oggetto *
                                    </label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($document['object']) ? $document['object'] : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data del documento --}}
                                <div class="form-group col-md-6">
                                    <label for="document_date">Data del documento *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="document_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$document['document_date'] }}"
                                               id="input_document_date">
                                    </div>
                                </div>

                                {{-- Campo Link esterno --}}
                                <div class="form-group col-md-6" id="input_external_link">
                                    <label for="external_link">Link esterno</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'external_link',
                                        'value' => !empty($document['external_link']) ? $document['external_link'] : null,
                                        'placeholder' => 'https://www.',
                                        'id' => 'input_external_link',
                                        'class' => 'form-control input_external_link',
                                    ]) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Pubblica in --}}
                                <div class="form-group col-md-12">
                                    <label for="public_in">Pubblica in *</label>
                                    <div class="select2-blue" id="public_in">
                                        {{ form_dropdown(
                                            'public_in',
                                            @$publicIn,
                                            @$publicInIDs,
                                            'class="form-control select2-public_in" data-placeholder="Seleziona" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Note --}}
                                <div class="form-group col-md-12">
                                    <label for="notes">Note</label>
                                    {{form_editor([
                                        'name' => 'notes',
                                        'value' => !empty($document['notes']) ? $document['notes'] : null,
                                        'id' => 'input_notes',
                                        'class' => 'form-control input_notes'
                                    ]) }}
                                </div>
                            </div>

                            <div id="work_fields">

                                {{generateSeparator('Comunicazioni circa la mancata redazione del programma triennale')}}

                                <div class="form-row mt-2">
                                    {{-- Campo Tipologia --}}
                                    <div class="form-group col-md-6">
                                        <label for="typology">Tipologia *</label>
                                        <div class="select2-blue" id="input_typology">
                                            {{ form_dropdown(
                                                'typology',
                                                [
                                                    '' => null,
                                                    'lavori' => 'Lavori pubblici, per assenza di lavori',
                                                    'acquisti' => 'Acquisti di forniture e servizi, per assenza di acquisti di forniture e servizi'
                                                ],
                                                @$document['typology'],
                                                'class="form-control select2-typology" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                            ) }}
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="purchases_fields">

                                {{generateSeparator('Elenco annuale dei progetti finanziati')}}

                                <div class="form-row mt-2">
                                    {{-- Campo CUP --}}
                                    <div class="form-group col-md-6">
                                        <label for="cup" id="cig-label">CUP
                                        </label>
                                        {{ form_input([
                                            'name' => 'cup',
                                            'value' => !empty($document['cup']) ? $document['cup'] : null,
                                            'placeholder' => 'Codice CUP',
                                            'id' => 'input_cup',
                                            'class' => 'form-control input_cup'
                                        ]) }}
                                    </div>

                                    {{-- Campo Data del documento --}}
                                    <div class="form-group col-md-6">
                                        <label for="start_date">Data avvio</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                            class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" name="start_date"
                                                   placeholder="GG/MM/AAAA"
                                                   autocomplete="off" class="form-control"
                                                   value="{{ @$document['start_date'] }}"
                                                   id="input_start_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    {{-- Campo Importo atto di concessione --}}
                                    <div class="form-group col-md-6">
                                        <label for="financing_amount">Importo finanziamento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                            </div>
                                            {{ form_input([
                                            'name' => 'financing_amount',
                                            'value' => !empty($document['financing_amount']) ? $document['financing_amount'] : null,
                                            'placeholder' => 'Inserire importo finanziamento',
                                            'id' => 'input_financing_amount',
                                            'class' => 'form-control input_financing_amount a-num-class',
                                        ]) }}
                                        </div>
                                    </div>

                                    {{-- Campo fonti finanziarie --}}
                                    <div class="form-group col-md-6">
                                        <label for="financial_sources" id="cig-label">Fonti finanziarie
                                        </label>
                                        {{ form_input([
                                            'name' => 'financial_sources',
                                            'value' => !empty($document['financial_sources']) ? $document['financial_sources'] : null,
                                            'placeholder' => 'Fonti finanziarie',
                                            'id' => 'input_financial_sources',
                                            'class' => 'form-control input_financial_sources'
                                        ]) }}
                                    </div>
                                </div>


                                <div class="form-row">
                                    {{-- Campo fonti finanziarie --}}
                                    <div class="form-group col-md-12">
                                        <label for="procedural_implementation_status" id="cig-label">Stato di attuazione
                                            procedurale
                                        </label>
                                        {{ form_input([
                                            'name' => 'procedural_implementation_status',
                                            'value' => !empty($document['procedural_implementation_status']) ? $document['procedural_implementation_status'] : null,
                                            'placeholder' => 'Stato di attuazione procedurale',
                                            'id' => 'input_procedural_implementation_status',
                                            'class' => 'form-control input_procedural_implementation_status'
                                        ]) }}
                                    </div>
                                </div>
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

        @if(!empty($document['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $document['id'],
                'id' => 'canon_id',
                'class' => 'canon_id',
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
{{ js('ckeditor4/ckeditor.js', 'common') }}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}
{{ js('admin/get/config.js') }}
<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        // $('#work_fields').hide();
        // $('#purchases_fields').hide();

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert') {
            @if(empty($contest['document_date']))
            $('#input_document_date').attr('value', "<?= date('Y-m-d') ?>");
            @endif
        }

        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_notes');

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Select2 per campo "Tipo di documento (pubblica in) *" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2();

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        $dropdownPublicIn.on('change', function () {
            let selected = $(this).val();
            showFieldFromPublicIn(selected);
        });

        showFieldFromPublicIn($dropdownPublicIn.val());

        {{-- Select2 per campo "Tipo di documento (tipologia) *" --}}
        let $dropdownTypology = $('.select2-typology');
        $dropdownTypology.select2({
            placeholder: 'Seleziona tipologia',
            minimumResultsForSearch: -1
        });

        {{-- Begin salvataggio --}}
        /**
         * Metodo per il salvataggio
         */
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
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
                createValidatorFormSuccessToast(response.data.message, 'Atti e documenti');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/general-acts-documents') }}';
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
                createValidatorFormErrorToast(msg, 6000);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End salvataggio --}}

        /**
         * Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form
         */
        {{-- Vedere nel footer --}}
        openModalForm();

        /**
         * Funzione che viene eseguita alla chiusura del modale di aggiunta di nuovi elementi direttamente dal form.
         * Pulisce l'iframe.
         */
        {{-- Vedere nel footer --}}
        closeModalForm();

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
         * Controllo se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();
    });

    function showFieldFromPublicIn(selected) {
        $('#work_fields').hide();
        $('#purchases_fields').hide();

        //progetti in pubblico
        if (selected.includes('583')) {
            $('#work_fields').show();
        } else {
            $('#work_fields').hide();
        }

        if (selected.includes('586')) {
            $('#purchases_fields').show();
        } else {
            $('#purchases_fields').hide();
        }


    }

</script>
{% endblock %}