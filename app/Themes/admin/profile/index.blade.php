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
        {{ form_open_multipart($formAction,$formSettings) }}
        <div class="card mb-4" id="card-filter">
            <h4 class="card-header">
                <span><i class="fas fa-user-edit"></i> Modifica Profilo </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/dashboard') }}" title="Torna alla Home"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-home"></i> Torna alla Home
                            </a>
                        </li>
                    </ul>
                </div>
            </h4>

            <div class="card-body card-primary">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        I campo contrassegnati dal simbolo asterisco (*) sono obbligatori.
                    </div>

                    @if(!empty($user['profile_image']))
                        <div class="col-md-12 text-center mb-3">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" style="width: 60px; height: auto;"
                                     src="{{ baseUrl('media/' . instituteDir() . '/assets/images/' . $user['profile_image']) }}"
                                     alt="{{ $user['username'] }}">
                            </div>
                            @if(!empty($user['id']))
                                <div class="mt-1">
                                    <small class="text-muted">
                                        Utente creato in data
                                        <strong>{{ date("d-m-Y", strtotime($user['created_at']))}}</strong> alle ore
                                        <strong>{{ date("H:i:s", strtotime($user['created_at']))}}</strong>
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
                                    'class' => 'form-control input_username disabled readonly',
                                    'readonly' => 'readonly',
                                    'disabled' => 'disabled',
                                ]) }}
                            </div>

                            {{-- Campo Password --}}
                            <div class="form-group col-md-6">
                                <label for="password">Password *</label>
                                {{ form_password([
                                    'name' => 'password',
                                    'value' => '',
                                    'placeholder' => 'Password',
                                    'id' => 'input_password',
                                    'class' => 'form-control input_password',

                                ]) }}
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

                            @if(getAclModifyProfile()===true)
                                {{-- Campo Profili ACL --}}
                                <div class="col-md-12">
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
                            @endif
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

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('select2/css/select2.min.css','common') }}
{{ css('select2-bootstrap4-theme/select2-bootstrap4.min.css','common') }}
<style type="text/css">
    .select2-container--default .select2-selection--single {
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px !important;
    }

    .readonly {
        cursor: not-allowed;
    }
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('select2/js/select2.full.min.js','common') }}
<script type="text/javascript">
    $(document).ready(function () {

        let institutionId = $('#institution_id').val();

        let formModified = false;

        $('#preview-url-image').hide();

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

        {{-- Begin Select2 campo "Profili ACL" --}}
        let $dropdownProfiles = $('.select2-profiles');
        $dropdownProfiles.select2({
            placeholder: 'Seleziona o cerca tra i ruoli disponibili....',
        });
        {{-- End Select2 campo "Profili ACL" --}}

        let $dropdownContractingStations = $('.select2-contracting_stations');
        $dropdownContractingStations.select2()
        $dropdownContractingStations.on('change', function () {
            $('#operator_ids').val($(this).val());
        });

        let $dropdownPasswordExpirationDays = $('.select2-password_expiration_days');
        $dropdownPasswordExpirationDays.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownPreventPasswordRepetition = $('.select2-prevent_password_repetition');
        $dropdownPreventPasswordRepetition.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownPreventPasswordRepetition6Months = $('.select2-prevent_password_repetition_6_months');
        $dropdownPreventPasswordRepetition6Months.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownWrongPasswordLock = $('.select2-wrong_password_lock');
        $dropdownWrongPasswordLock.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownWrongPasswordLockTime = $('.select2-wrong_password_lock_time');
        $dropdownWrongPasswordLockTime.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownPreventPasswordChangeDay = $('.select2-prevent_password_change_day');
        $dropdownPreventPasswordChangeDay.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownAccountDeactivationForNoUse = $('.select2-account_deactivation_for_non_use');
        $dropdownAccountDeactivationForNoUse.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownfiltraRecordProprietario = $('.select2-filter_owner_record');
        $dropdownfiltraRecordProprietario.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownRegistrationType = $('.select2-registration_type');
        $dropdownRegistrationType.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End metodi per campi Select --}}

        {{-- Begin Salvataggio Utente --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            // resetForm: true,

            beforeSend: function () {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
                let response = parseJson(data);
                formModified = false;

                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Profilo',
                    subtitle: 'Modifica',
                    autohide: true,
                    delay: 2000,
                    body: response.data.message
                });

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/dashboard') }}';
                }, 1500);

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            },
            error: function (jqXHR, status) {

                let response = parseJson(jqXHR.responseText);

                $(document).Toasts('create', {
                    class: 'bg-danger error-toast',
                    title: 'ATTENZIONE',
                    subtitle: 'Validatore modulo',
                    autohide: true,
                    delay: 60000,
                    body: response.errors.error
                });

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);

            }
        });
        {{-- End Salvataggio Utente --}}

        {{-- Controllo per l'uscita dal form se i campi di input sono stati toccati --}}
        $(document).on('focus', '.select2-selection.select2-selection--single, .select2-selection.select2-selection--multiple, input', function (e) {
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