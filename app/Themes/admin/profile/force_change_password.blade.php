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
            </h4>

            <div class="card-body card-primary">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        I campo contrassegnati dal simbolo asterisco (*) sono obbligatori.
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
                </div>
            </div>
            {{-- Card Footer --}}
            <div class="card-footer">
                {{ btnSave() }}
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
            {{ form_hidden('institute_id',PatOsInstituteId()) }}
            {{ form_close() }}
        </div>
    </div>
</div>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}

{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
<script type="text/javascript">
    $(document).ready(function () {
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
    });
</script>
{% endblock %}