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
                        Aggiunta nuovo utente
                    @else
                        <i class="fas fa-user-edit fa-sm mr-1"></i>
                        Modifica Utente
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/user') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco utenti
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

                    @if(!empty($user['profile_image']))
                        <div class="col-md-12 text-center mb-3">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" style="width: 60px; height: auto;"
                                     src="{{xss: baseUrl('media/'. instituteDir() . '/assets/images/' .  $user['profile_image']) }}"
                                     alt="{{xss: checkEncrypt($user['username']) }}">
                            </div>
                            @if(!empty($user['id']))
                                <div class="mt-1">
                                    <small class="text-muted">
                                        Utente creato in data
                                        <strong>{{date('d-m-Y|date'): $user['created_at'] }}</strong> alle ore
                                        <strong>{{date('H.i.s|date'): $user['created_at'] }}</strong>
                                    </small>
                                </div>
                            @endif
                            <hr class="mb-3"/>
                        </div>
                    @endif

                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div class="row">

                            {{-- Campo Nome utente --}}
                            <div class="form-group col-md-6">
                                <label for="name">Nome utente *</label>
                                {{ form_input([
                                    'name' => 'name',
                                    'value' => !empty($user['name']) ? checkDecrypt($user['name']) : null,
                                    'placeholder' => 'Nome utente',
                                    'id' => 'input_name',
                                    'class' => 'form-control input_name',

                                ]) }}
                            </div>

                            {{-- Campo Username --}}
                            <div class="form-group col-md-6">
                                <label for="username">Username *</label>
                                {{ form_input([
                                    'name' => 'username',
                                    'value' => !empty($user['username']) ? checkDecrypt($user['username']) : null,
                                    'placeholder' => 'Username',
                                    'id' => 'input_username',
                                    'class' => 'form-control input_username ' . (($_storageType == 'update') ? 'readonly' : ''),
                                    (($_storageType == 'update') ? 'readonly' : '') => (($_storageType == 'update') ? 'readonly' : ''),
                                ]) }}
                            </div>

                            {{-- Campo Password --}}
                            <div class="form-group col-md-6">
                                <label for="password">Password *</label>
                                <div class="input-group">
                                    {{ form_password([
                                        'name' => 'password',
                                        'value' => '',
                                        'placeholder' => 'Password',
                                        'id' => 'input_password',
                                        'class' => 'form-control input_password',

                                    ]) }}
                                </div>
                            </div>

                            {{-- Campo Conferma Password --}}
                            <div class="form-group col-md-6">
                                <label for="re_password">Conferma password *</label>
                                {{ form_password([
                                    'name' => 're_password',
                                    'value' => '',
                                    'placeholder' => 'Conferma password',
                                    'id' => 'input_re_password',
                                    'class' => 'form-control input_re_password',

                                ]) }}
                            </div>

                            {{-- Campo Indirizzo email --}}
                            <div class="form-group col-md-6">
                                <label for="email">Email *</label>
                                {{ form_input([
                                    'name' => 'email',
                                    'value' => !empty($user['email']) ? checkDecrypt($user['email']) : null,
                                    'placeholder' => 'Email',
                                    'id' => 'input_email',
                                    'class' => 'form-control input_email',

                                ]) }}
                            </div>

                            {{-- Campo Conferma email --}}
                            <div class="form-group col-md-6">
                                <label for="re_email">Conferma email *</label>
                                {{ form_input([
                                    'name' => 're_email',
                                    'value' => !empty($user['email']) ? checkDecrypt($user['email']) : null,
                                    'placeholder' => 'Conferma email',
                                    'id' => 'input_re_email',
                                    'class' => 'form-control input_re_email',

                                ]) }}
                            </div>

                            {{-- Campo Recapito cellulare --}}
                            <div class="form-group col-md-6">
                                <label for="phone">Recapito cellulare</label>
                                {{ form_input([
                                    'type' => '',
                                    'name' => 'phone',
                                    'value' => !empty($user['phone']) ? checkDecrypt($user['phone']) : null,
                                    'placeholder' => 'Recapito cellulare',
                                    'id' => 'input_phone',
                                    'class' => 'form-control input_phone',

                                ]) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{-- Allegato Immagine del Profilo --}}
                                <label for="profile_image">Immagine profilo</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="profile_image_file"
                                           name="profile_image" accept="image/png, image/gif, image/jpeg">
                                    <label class="custom-file-label" for="profile_image_file"
                                           id="label_attach_logo">
                                        Allega immagine profilo
                                    </label>
                                </div>
                            </div>

                            @if($_storageType === 'insert')
                                {{-- Campo Tipo di registrazione --}}
                                <div class="form-group col-md-6">
                                    <label for="registration_type">Tipo di registrazione *</label>
                                    <div class="select2-blue" id="input_registration_type">
                                        {{ form_dropdown(
                                            'registration_type',
                                            [
                                                0 => 'Registra utente attivo senza mail di notifica',
                                                1 => 'Registra utente attivo con mail di notifica'
                                            ],
                                            @$user['registration_type'],
                                            'class="form-control select2-registration_type" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            {{-- Preview Immagine Profilo --}}
                            <div id="preview-url-image" class="col-md-6">
                                <div class="mt-2">
                                    <img src="" alt="Immagine Profilo" id="src-profile_image"
                                         class="img-thumbnail attach-image">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-xs btn-outline-danger"
                                                id="clear-preview-image">
                                            <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina immagine
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Profili ACL --}}
                            <div class="col-md-6">
                                <div class="form-group" id="input_profili_acl">
                                    <label for="profiles">Profili ACL</label>
                                    <div class="select2-blue">
                                        {{ form_dropdown(
                                            'profiles[]',
                                            @$acl,
                                           @$profilesIds,
                                            'class="form-control select2-profiles input_profiles" multiple="multiple" data-placeholder="Seleziona profili" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Validità Password --}}
                            <div class="form-group col-md-6">
                                <label for="password_expiration_days">Validità della password</label>
                                <div class="select2-blue" id="input_password_expiration_days">
                                    {{ form_dropdown(
                                        'password_expiration_days',
                                        [
                                            '' => '',
                                            25 => '25 gg',
                                            50 => '50 gg',
                                            100 => '100 gg',
                                            180 => '180 gg',
                                            365 => '365 gg'
                                        ],
                                        !empty($user['password_expiration_days']) ? $user['password_expiration_days'] : 365,
                                        'class="form-control select2-password_expiration_days" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{-- Campo Impedisci ripetizione delle utlime 5 password --}}
                            <div class="form-group col-md-6">
                                <label for="prevent_password_repetition">Impedisci ripetizione delle ultime 5
                                    password</label>
                                <div class="select2-blue" id="input_prevent_password_repetition">
                                    {{ form_dropdown(
                                        'prevent_password_repetition',
                                        [
                                            '' => '',
                                            1 => 'Si',
                                            2 => 'No'
                                        ],
                                        !empty($user['prevent_password_repetition']) ? $user['prevent_password_repetition'] : 2,
                                        'class="form-control select2-prevent_password_repetition" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{-- Campo Impedisci il cambio password più di una volta al giorno --}}
                            <div class="form-group col-md-6">
                                <label for="prevent_password_change_day">Impedisci il cambio password più di una
                                    volta al giorno</label>
                                <div class="select2-blue" id="input_prevent_password_change_day">
                                    {{ form_dropdown(
                                        'prevent_password_change_day',
                                        [
                                            '' => '',
                                            1 => 'Si',
                                            2 => 'No'
                                        ],
                                        !empty($user['prevent_password_repetition_6_months']) ? $user['prevent_password_repetition_6_months'] : 2,
                                        'class="form-control select2-prevent_password_change_day" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{-- Campo Disattivazione account per mancato utilizzo --}}
                            <div class="form-group col-md-6">
                                <label for="account_deactivation_for_non_use">Disattivazione account per mancato
                                    utilizzo</label>
                                <div class="select2-blue" id="input_deactivate_account_no_use">
                                    {{ form_dropdown(
                                        'deactivate_account_no_use',
                                        [
                                            '' => '',
                                            25 => 'Dopo 25 giorni',
                                            50 => 'Dopo 50 giorni',
                                            100 => 'Dopo 100 giorni',
                                            180 => 'Dopo 180 giorni',
                                            365 => 'Dopo 365 giorni'
                                        ],
                                         !empty($user['deactivate_account_no_use']) ? $user['deactivate_account_no_use'] : 365,
                                        'class="form-control select2-account_deactivation_for_non_use" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{-- Campo Note --}}
                            <div class="form-group col-md-12">
                                <label for="note">Note</label>
                                {{ form_textarea([
                                    'name' => 'notes',
                                    'value' => !empty($user['notes']) ? $user['notes'] : null,
                                    'placeholder' => 'Note',
                                    'id' => 'input_notes',
                                    'class' => 'form-control input_notes',
                                    'cols' => '10',
                                    'rows' => '3',
                                ]) }}
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

        @if(!empty($user['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $user['id'],
                'id' => 'user_id',
                'class' => 'user_id',
            ]) }}
        @endif

        @if(!empty($profilesIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_profilesIds',
                'value' => implode(',',$profilesIds),
                'id' => '_profilesIds',
                'class' => '_profilesIds',
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

<div class="modal" tabindex="-1" role="dialog" id="modale-dialog-password-ws">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Password web services</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <div class="callout callout-warning">
                        <h5>Credenziali di accesso web services</h5>
                        <p>
                            <strong>Attenzione:</strong>
                            La password sarà visibile in chiaro solamente alla prima generazione della stessa.
                        </p>

                    </div>
                    <p>
                        Username: <strong id="ws_username"></strong>
                        <button class="btn btn-sm btn-link"
                                id="copy_username"
                                data-toggle="tooltip" data-placement="top"
                                data-original-title="Copia username" aria-label="Copia username">
                            <i class="fas fa-clone"></i>
                        </button>
                        <br/>
                        Token: <strong id="ws_password"></strong>
                        <button class="btn btn-sm btn-link"
                                id="copy_token"
                                data-toggle="tooltip" data-placement="top"
                                data-original-title="Copia token" aria-label="Copia token">
                            <i class="fas fa-clone"></i>
                        </button>
                    </p>
                    {{ form_input([
                        'type' => 'hidden',
                        'value' => '',
                        'id' => 'h_username',
                    ]) }}
                    {{ form_input([
                        'type' => 'hidden',
                        'value' => '',
                        'id' => 'h_token',
                    ]) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi finestra</button>
            </div>
        </div>
    </div>
</div>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
<style>
    .readonly {
        cursor: not-allowed;
    }
</style>
{% endblock %}

{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
<script type="text/javascript">
    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    function openModalShowPassword() {
        let args = arguments;

        $('#ws_username').empty().text(args[0]);
        $('#ws_password').empty().text(args[1]);

        $('#h_username').val(args[0]);
        $('#h_token').val(args[1]);
        $('#modale-dialog-password-ws').modal('show');
    }

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        $('#preview-url-image').hide();

        {{-- Generatore di password casuali --}}
        $('#generate-password-random').click((e) => {
            let password = generatePassword(16);
            $('#input_password').val(password);
            $('#input_re_password').val(password);

        });

        $('#copy_username').bind('click', function (e) {
            e.preventDefault();
            var copyUsername = document.getElementById("h_username");
            copyUsername.select();
            //copyUsername.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyUsername.value);
            $(document).Toasts('create', {
                class: 'bg-success z99999999',
                autohide: true,
                delay: 3500,
                title: 'ATTENZIONE',
                body: 'Username copiata con successo!'
            });
        });

        $('#copy_token').bind('click', function (e) {
            e.preventDefault();
            var copyToken = document.getElementById("h_token");
            copyToken.select();
            //copyUsername.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyToken.value);
            $(document).Toasts('create', {
                class: 'bg-success z99999999',
                autohide: true,
                delay: 3500,
                title: 'ATTENZIONE',
                body: 'password WS copiata con successo!'
            });
        });

        @if(!empty($user['id'] ))
        $('#generate_new_token_ws').bind('click', function (e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ siteUrl('admin/user/ws/generate/password') }}',
                dataType: "JSON",
                data: {
                    id: {{ $user['id'] }}
                },
                beforeSend: function () {
                    showFullModalSpinner()
                },
                success: function (data) {
                    let response = parseJson(data);
                    // createValidatorFormSuccessToast(response.data.message, 'Utente');
                    openModalShowPassword(response.data.username, response.data.password);

                },
                complete: function (xhr) {
                    hideFullModalSpinner();
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);

                    // Funzione che genera il toast con gli errori
                    createValidatorFormErrorToast(response.errors.error, 7000);
                }
            });
        })
        @endif

        $('#change-view-password').click((e) => {

            let typeInputPassword = $('#input_password');
            let typeInputRePassword = $('#input_re_password');
            let btnEye = $('#change-view-password');

            if (typeInputPassword.attr('type') == 'password') {

                typeInputPassword.attr('type', 'text');
                typeInputRePassword.attr('type', 'text');
                btnEye.empty().append('<i class="far fa-eye-slash"></i>');

            } else {

                typeInputPassword.attr('type', 'password');
                typeInputRePassword.attr('type', 'password');
                btnEye.empty().append('<i class="far fa-eye"></i>');

            }

        });

        {{-- Begin preview Immagine --}}
        $('#profile_image_file').bind('change', function (e) {
            var reader,
                files = document.getElementById("profile_image_file").files;
            reader = new FileReader();
            reader.onload = function (e) {
                $('#src-profile_image').attr('src', e.target.result);
            };
            reader.readAsDataURL(files[0]);
            $('#preview-url-image').fadeIn(200);
        });

        $('#clear-preview-image').bind('click', function (e) {
            document.getElementById("profile_image_file").value = null;
            $('#preview-url-image').hide();
        });
        {{-- End preview Immagine --}}

        {{-- Begin metodi per campi Select --}}

        {{-- Begin Select2 campo "Profili ACL" --}}
        let $dropdownProfiles = $('.select2-profiles');
        $dropdownProfiles.select2({
            placeholder: 'Seleziona o cerca tra i ruoli disponibili....',
        });
        {{-- End Select2 campo "Profili ACL" --}}

        {{-- Campo select Validità della password --}}
        let $dropdownPasswordExpirationDays = $('.select2-password_expiration_days');
        $dropdownPasswordExpirationDays.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Campo select Impedisci ripetizione delle ultime 5 password --}}
        let $dropdownPreventPasswordRepetition = $('.select2-prevent_password_repetition');
        $dropdownPreventPasswordRepetition.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Campo select Impedisci il cambio password più di una volta al giorno --}}
        let $dropdownPreventPasswordChangeDay = $('.select2-prevent_password_change_day');
        $dropdownPreventPasswordChangeDay.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Campo select Disattivazione account per mancato utilizzo --}}
        let $dropdownAccountDeactivationForNoUse = $('.select2-account_deactivation_for_non_use');
        $dropdownAccountDeactivationForNoUse.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Campo select Visualizza i soli elementi dell'utente --}}
        let $dropdownfiltraRecordProprietario = $('.select2-filter_owner_record');
        $dropdownfiltraRecordProprietario.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Campo select Tipo di registrazione --}}
        let $dropdownRegistrationType = $('.select2-registration_type');
        $dropdownRegistrationType.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End metodi per campi Select --}}

        {{-- Campo select per l'abilitazione ai Web Services --}}
        let $dropdownWebServices = $('.select2-account_is_web_services');
        $dropdownWebServices.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- END campo select per l'abilitazione ai Web Services --}}

        {{-- Campo select per rigenerare nuovo token --}}
        let $dropdownRegenerateToken = $('.select2-account_regenerate_token');
        $dropdownRegenerateToken.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        $dropdownRegenerateToken.on("select2:select", function (e) {
            let value = parseInt($(this).val());

            if (value === 1) {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    autohide: true,
                    delay: 5500,
                    title: 'ATTENZIONE',
                    body: 'Clicca sul pulsante salva per generare un nuovo token!'
                });
            }

        });
        {{-- END cCampo select per rigenerare nuovo token --}}

        {{-- Editor wishing --}}
        let $dropdownEditorWishing = $('.select2-editor_wishing');
        $dropdownEditorWishing.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        /**
         * Metodo per il salvataggio
         */
                {{-- Begin Salvataggio Utente --}}
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
                        window.location.href = '{{ siteUrl('admin/user') }}';
                    }, 800);
                } else {
                    openModalShowPassword(response.data.username, response.data.password);
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

        {{-- Pulsante per Generare un nuovo token --}}
        $('#generate-token-random').click(function (e) {
            e.preventDefault();

            let btnSend = $('#generate-token-random');
            $.ajax({
                type: 'GET',
                url: '{{siteUrl("/admin/user/regenerate/token")}}',
                dataType: "JSON",
                data: {
                    token: 1
                },
                beforeSend: function () {
                    btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                    $('.error-toast').remove();
                },
                success: function (data) {
                    let response = parseJson(data);
                    formModified = false;

                    // Funzione che genera il toast con il messaggio di successo
                    {{-- (vedere nel footer) --}}
                    createValidatorFormSuccessToast(response.data.message, 'Utente');

                    setTimeout(function () {
                        window.location.href = '{{ siteUrl('admin/user') }}';
                    }, 800);

                },
                complete: function (xhr) {
                    btnSend.empty().append('Genera token');
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);

                    // Funzione che genera il toast con gli errori
                    {{-- (vedere nel footer) --}}
                    createValidatorFormErrorToast(response.errors.error, 7000);
                    btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
                }
            });
        });
        {{-- End pulsante per Generare un nuovo token --}}
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