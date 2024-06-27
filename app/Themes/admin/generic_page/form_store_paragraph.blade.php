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
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName!=='TEXTAREA') {
            e.preventDefault();
            return false;
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
        CKEDITOR.replace('input_content');

        let institutionId = $('#institution_id').val();
        let id = $('#section_id').val();
        let mode = $('#mode').val();

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
