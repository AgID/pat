{{-- Form store Commissioni e gruppi consigliari --}}
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
                        Aggiunta Commissione/Gruppo Consiliare
                    @elseif($_storageType === 'update')
                        Modifica Commissione/Gruppo Consiliare
                    @else
                        Duplicazione Commissione/Gruppo Consiliare
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/commission') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco commissioni e gruppi
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

                    @if(!empty($commission['image']))
                        <div class="col-md-12 text-center mb-3">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" style="width: 60px; height: auto;"
                                     src="{{ baseUrl('media/' . instituteDir() . '/assets/images/' . $commission['image']) }}"
                                     alt="{{ $commission['name'] }}">
                            </div>
                            @if(!empty($commission['id']))
                                <div class="mt-1">
                                    <small class="text-muted">
                                        Commissione/Gruppo creato in data
                                        <strong>{{ date("d-m-Y", strtotime($commission['created_at']))}}</strong> alle
                                        ore
                                        <strong>{{ date("H:i:s", strtotime($commission['created_at']))}}</strong>
                                    </small>
                                </div>
                            @endif
                            <hr class="mb-3"/>
                        </div>
                    @endif

                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Nome --}}
                                <div class="form-group col-md-6" id="input_name">
                                    <label for="name">Nome *</label>
                                    {{ form_input([
                                        'name' => 'name',
                                        'value' => !empty($commission['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $commission['name'] : $commission['name']) : null,
                                        'placeholder' => 'Nome',
                                        'id' => 'input_name',
                                        'class' => 'form-control input_name',
                                    ]) }}
                                </div>

                                {{-- Campo Tipo --}}
                                <div class="form-group col-md-6">
                                    <label for="typology">Tipo *</label>
                                    <div class="select2-blue" id="input_typology">
                                        {{ form_dropdown(
                                            'typology',
                                            ['' => '','commissione'=>'Commissione','gruppo consiliare'=>'Gruppo consiliare'],
                                            @$commission['typology'],
                                            'class="form-control select2-typology" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Personale componente')}}

                            {{-- Campo Presidente o capogruppo --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_president" id="c_president_label">Presidente o capogruppo
                                            *</label>
                                    </div>
                                    <div id="ajax_president"></div>
                                    <input type="hidden" value="" name="president_id" id="input_president_id"
                                           class="president_id">
                                </div>
                            </div>

                            {{-- Campo Vicepresidente --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_vice_president"
                                               id="c_vice_president_label">Vicepresidente/i</label>
                                    </div>
                                    <div id="ajax_vice_president"></div>
                                    <input type="hidden" value="" name="vice_presidents" id="input_vice_president"
                                           class="vice_president">
                                </div>
                            </div>

                            {{-- Campo Segretari --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_secretaries" id="c_secretaries_label">Segretari</label>
                                    </div>
                                    <div id="ajax_secretaries"></div>
                                    <input type="hidden" value="" name="secretaries" id="input_secretaries"
                                           class="secretaries">
                                </div>
                            </div>

                            {{-- Campo Membri supplenti --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_members_substitutes" id="c_members_substitutes_label">Membri
                                            supplenti</label>
                                    </div>
                                    <div id="ajax_members_substitutes"></div>
                                    <input type="hidden" value="" name="substitutes"
                                           id="input_members_substitutes"
                                           class="members_substitutes">
                                </div>
                            </div>

                            {{-- Campo Membri --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_members" id="c_members_label">Membri</label>
                                    </div>
                                    <div id="ajax_members"></div>
                                    <input type="hidden" value="" name="members"
                                           id="input_members"
                                           class="members">
                                </div>
                            </div>

                            {{generateSeparator('Informazioni e recapiti')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Recapito email --}}
                                <div class="form-group col-md-6">
                                    <label for="email">Recapito email</label>
                                    {{ form_input([
                                        'name' => 'email',
                                        'value' => !empty($commission['email']) ? $commission['email'] : null,
                                        'placeholder' => 'Email',
                                        'id' => 'input_email',
                                        'class' => 'form-control input_email'
                                    ]) }}
                                </div>

                                <div class="form-group col-md-6">
                                    {{-- Campo Recapito telefonico fisso --}}
                                    <label for="phone">Recapito telefonico fisso</label>
                                    {{ form_input([
                                        'name' => 'phone',
                                        'value' => !empty($commission['phone']) ? $commission['phone'] : null,
                                        'placeholder' => 'Telefono',
                                        'id' => 'input_phone',
                                        'class' => 'form-control input_phone'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Recapito fax --}}
                                <div class="form-group col-md-6">
                                    <label for="fax">Recapito fax</label>
                                    {{ form_input([
                                        'name' => 'fax',
                                        'value' => !empty($commission['fax']) ? $commission['fax'] : null,
                                        'placeholder' => 'Fax',
                                        'id' => 'input_fax',
                                        'class' => 'form-control input_fax'
                                    ]) }}
                                </div>

                                <div class="form-group col-md-6">
                                    {{-- Campo Indirizzo --}}
                                    <label for="address">Indirizzo</label>
                                    {{ form_input([
                                        'name' => 'address',
                                        'value' => !empty($commission['address']) ? $commission['address'] : null,
                                        'placeholder' => 'Indirizzo',
                                        'id' => 'input_address',
                                        'class' => 'form-control input_address'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Attiva dal --}}
                                <div class="form-group col-md-6">
                                    <label for="activation_date">Attiva dal</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$activation_date }}"
                                               id="activation_date">
                                    </div>
                                </div>

                                {{-- Campo Attiva fino al  --}}
                                <div class="form-group col-md-6">
                                    <label for="expiration_date">Attiva fino al </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="expiration_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$expiration_date }}"
                                               id="expiration_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo descrizione --}}
                                <label for="description">Testo di descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($commission['description']) ? $commission['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Immagine da visualizzare --}}
                                <div class="form-group col-md-6">
                                    <label for="img">Immagine da visualizzare </label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="img_file"
                                               name="img" accept="image/png, image/gif, image/jpeg">
                                        <label class="custom-file-label" for="img_file"
                                               id="label_attach_logo">
                                            Allega immagine da visualizzare
                                        </label>
                                    </div>
                                </div>

                                {{-- Campo Ordine --}}
                                <div class="form-group col-md-6">
                                    <label for="order"> Ordine di visualizzazione *</label>
                                    {{ form_input([
                                        'type' => 'number',
                                        'name' => 'order',
                                        'value' => !empty($commission['order']) ? $commission['order'] : 1,
                                        'placeholder' => 'Ordine di visualizzazione',
                                        'id' => 'input_order',
                                        'class' => 'form-control input_order'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div id="preview-url-image" class="form-group col-md-6">
                                    <div class="mt-2">
                                        <img src="" alt="Immagine da visualizzare" id="src-image"
                                             class="img-thumbnail attach-image">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-xs btn-outline-danger"
                                                    id="clear-preview-image">
                                                <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina
                                            </button>
                                        </div>
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
            <div class="card-footer" id="__save_">
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

        @if(!empty($commission['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $commission['id'],
                'id' => 'commission_id',
                'class' => 'commission_id',
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

        {{ form_hidden('institute_id',PatOsInstituteId()) }}
        {{ form_close() }}
    </div>
</div>

{{-- Includo il modale per l'aggiunta di nuovi oggetti direttamente dal form  --}}
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
{{ js('admin/get/config.js?box='.$is_box) }}

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

        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

        $('#preview-url-image').hide();
        let formModified = false;
        let institutionId = $('#institution_id').val();

        // Tabella per la selezione del presidente della commissione(dal personale)
        let presidentTable = $('#ajax_president').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Presidente selezionato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_president_id',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#c_president_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($commission['president']))
        presidentTable.patOsAjaxPagination.setValue('{{ $commission['president']['id'] }}', '{{ (!empty($commission['president']['title'])?htmlEscape($commission['president']['title']).' - ':'').htmlEscape($commission['president']['full_name']).' - '.htmlEscape($commission['president']['name']).' - '.(!empty($commission['president']['email'])?$commission['president']['email']:'N.D') }}', true);
        @endif

        // Tabella per la selezione dei vice-presidenti della commissione(da archivio del personale)
        let vicePresidentsTable = $('#ajax_vice_president').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Vicepresidenti selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_vice_president',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#c_vice_president_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dei vicepresidenti gia selezionati
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($commission['vicepresidents']))
        @foreach($commission['vicepresidents'] as $vicePresident)
        vicePresidentsTable.patOsAjaxPagination.setValue('{{ $vicePresident['id'] }}', '{{ (!empty($vicePresident['title'])?htmlEscape($vicePresident['title']).' - ':'').htmlEscape($vicePresident['full_name']).' - '.htmlEscape($vicePresident['name']).' - '.(!empty($vicePresident['email'])?$vicePresident['email']:'N.D') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione dei segretari della commissione(da archivio del personale)
        let secretariesTable = $('#ajax_secretaries').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Segretari selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_secretaries',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#c_secretaries_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dei segretari gia selezionati
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($commission['secretaries']))
        @foreach($commission['secretaries'] as $secretary)
        vicePresidentsTable.patOsAjaxPagination.setValue('{{ $secretary['id'] }}', '{{ (!empty($secretary['title'])?htmlEscape($secretary['title']).' - ':'').htmlEscape($secretary['full_name']).' - '.htmlEscape($secretary['name']).' - '.(!empty($secretary['email'])?$secretary['email']:'N.D') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione dei membri supplenti della commissione(da archivio del personale)
        let membersSubstitutesTable = $('#ajax_members_substitutes').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Membri supplenti selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_members_substitutes',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#c_members_substitutes_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dei membri supplenti gia selezionati
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($commission['substitutes']))
        // Setto i membri supplenti gia selezionati
        @foreach($commission['substitutes'] as $subMember)
        membersSubstitutesTable.patOsAjaxPagination.setValue('{{ $subMember['id'] }}', '{{ (!empty($subMember['title'])?htmlEscape($subMember['title']).' - ':'').htmlEscape($subMember['full_name']).' - '.htmlEscape($subMember['name']).' - '.(!empty($subMember['email'])?$subMember['email']:'N.D') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione dei membri della commissione(da archivio del personale)
        let membersTable = $('#ajax_members').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Membri selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_members',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#c_members_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dei membri gia selezionati
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($commission['members']))
        @foreach($commission['members'] as $member)
        membersTable.patOsAjaxPagination.setValue('{{ $member['id'] }}', '{{ (!empty($member['title'])?htmlEscape($member['title']).' - ':'').htmlEscape($member['full_name']).' - '.htmlEscape($member['name']).' - '.(!empty($member['email'])?$member['email']:'N.D') }}', true);
        @endforeach
        @endif

        {{-- Begin metodi per campi Select --}}
        {{-- Select2 campo "Tipo" --}}
        let $dropdownTypology = $('.select2-typology');
        $dropdownTypology.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End metodi per campi Select --}}

        {{-- Begin preview Immagine --}}
        $('#img_file').bind('change', function (e) {
            let reader,
                files = document.getElementById("img_file").files;
            reader = new FileReader();
            reader.onload = function (e) {
                $('#src-image').attr('src', e.target.result);
            };
            reader.readAsDataURL(files[0]);
            $('#preview-url-image').fadeIn(200);
        });

        $('#clear-preview-image').bind('click', function (e) {
            document.getElementById("img_file").value = null;
            $('#preview-url-image').hide();
        });
        {{-- End preview Immagine --}}

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description');

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
                createValidatorFormSuccessToast(response.data.message, 'Commissione');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/commission') }}';
                }, 800);
                @else
                {{-- Controllo, se sono all'interno di un modale lo chiudo dopo il salvataggio --}}
                setTimeout(function () {
                    window.parent.$('#exampleModal').modal('hide');
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

    });
</script>
{% endblock %}