{{--  UTENTE index (Paginazione) Form store USER --}}
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
                    @if($_storageType === 'insert')
                        <i class="fas fa-user-plus fa-sm mr-1"></i>
                        Aggiunta nuovo Destinatario
                    @else
                        <i class="fas fa-user-edit fa-sm mr-1"></i>
                        Modifica Destinatario
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/report-publication-recipients') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco Destinatari
                            </a>
                        </li>
                    </ul>
                </div>
            </h4>

            <div class="card-body card-primary">
                <div class="row">
                    <div class="col-md-9 mb-4">
                        I campo contrassegnati dal simbolo asterisco (*) sono obbligatori.
                    </div>

                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div class="row">

                            {{-- Campo Nome destinatario --}}
                            <div class="form-group col-md-6">
                                <label for="name">Nome *</label>
                                {{ form_input([
                                    'name' => 'name',
                                    'value' => !empty($recipient['name']) ? checkDecrypt($recipient['name']) : null,
                                    'placeholder' => 'Nome utente',
                                    'id' => 'input_name',
                                    'class' => 'form-control input_name',

                                ]) }}
                            </div>

                            {{-- Campo Indirizzo email destinatario --}}
                            <div class="form-group col-md-6">
                                <label for="email">Email *</label>
                                {{ form_input([
                                    'name' => 'email',
                                    'value' => !empty($recipient['email']) ? checkDecrypt($recipient['email']) : null,
                                    'placeholder' => 'Email',
                                    'id' => 'input_email',
                                    'class' => 'form-control input_email',

                                ]) }}
                            </div>

                            {{-- Campo Attivo, indica se il destinatario deve ricevere o meno l'email con il report --}}
                            <div class="form-group col-md-6">
                                <label for="active">Attivo</label>
                                <div class="select2-blue" id="input_active">
                                    {{ form_dropdown(
                                        'active',
                                        [
                                            '' => '',
                                            '1' => 'Si',
                                            '0' => 'No'
                                        ],
                                         !empty($recipient['active']) ? $recipient['active'] : '',
                                        'class="form-control select2-active" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>
                        </div>
                        {{-- END: Form --}}
                    </div>
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

        @if(!empty($recipient['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $recipient['id'],
                'id' => 'user_id',
                'class' => 'user_id',
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

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}

{% endblock %}

{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
<script type="text/javascript">
    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName!='TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Campo select Attivo --}}
        let $dropdownAccountDeactivationForNoUse = $('.select2-active');
        $dropdownAccountDeactivationForNoUse.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        /**
         * Metodo per il salvataggio
         */
                {{-- Begin Salvataggio Destinatario --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
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
                createValidatorFormSuccessToast(response.data.message, 'Utente');

                if (!response.data.hasOwnProperty('is_modal')) {
                    setTimeout(function () {
                        window.location.href = '{{ siteUrl('admin/report-publication-recipients') }}';
                    }, 800);
                }
            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(response.errors.error, 7000);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End Salvataggio Utente --}}

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