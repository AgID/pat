<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-6">
        {{--  SMTP - Username --}}
        <div class="form-group">
            <label for="smtp_username">SMTP - Username </label>
            {{ form_input([
                'name' => 'smtp_username',
                'value' => !empty($institution['smtp_username']) ? $institution['smtp_username'] : null,
                'placeholder' => 'SMTP Username',
                'id' => 'input_smtp_username',
                'class' => 'form-control input_smtp_username'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - Password --}}
        <div class="form-group">
            <label for="smtp_password">SMTP - Password </label>
            {{ form_password([
                'name' => 'smtp_password',
                'value' =>  null,
                'placeholder' => 'SMTP - Password',
                'id' => 'input_smtp_password',
                'class' => 'form-control input_smtp_password'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - Indirizzo server --}}
        <div class="form-group">
            <label for="smtp_host">SMTP - Indirizzo server </label>
            {{ form_input([
                'name' => 'smtp_host',
                'value' => !empty($institution['smtp_host']) ? $institution['smtp_host'] : null,
                'placeholder' => 'SMTP - Indirizzo server',
                'id' => 'input_smtp_host',
                'class' => 'form-control input_smtp_host'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - Porta --}}
        <div class="form-group">
            <label for="smtp_port">SMTP - Porta </label>
            {{ form_input([
                'name' => 'smtp_port',
                'value' => !empty($institution['smtp_port']) ? $institution['smtp_port'] : null,
                'placeholder' => 'SMTP - Porta',
                'id' => 'input_smtp_port',
                'class' => 'form-control input_smtp_port'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - SSL --}}
        <div class="form-group">
            <label for="smtp_security">
                SMTP - SSL
            </label>
            {{ form_dropdown(
                 'smtp_security',
                 [
                     ''=>'',
                     'no' => 'No',
                     'SSL' => 'SSL',
                     'TLS' => 'TLS'
                 ],
                !empty($institution['smtp_security']) ? $institution['smtp_security'] : null,
                 'id="smtp_security" class="form-control select2-smtp_security" style="width: 100%;"' )
             }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - Usa autenticazione --}}
        <div class="form-group">
            <label for="smtp_auth">
                SMTP - Usa autenticazione
            </label>
            {{ form_dropdown(
                 'show_smtp_auth',
                 [''=>'',1=>'Si', 2=>'No'],
                !empty($institution['show_smtp_auth']) ? $institution['show_smtp_auth'] : null,
                 'id="smtp_auth" class="form-control select2-show_smtp_auth" style="width: 100%;"' )
             }}
        </div>
    </div>
</div>

<div class="form-row d-flex align-items-end">
    <div class="form-group col-md-6">
        {{-- Test del server inserisci un indirizzo email --}}
        <label for="smtp_test_email">Test del server inserisci un indirizzo email </label>
        {{ form_input([
            'name' => 'smtp_test_email',
            'value' => null,
            'placeholder' => 'Test del server inserisci un indirizzo email',
            'id' => 'input_smtp_test_email',
            'class' => 'form-control input_smtp_test_email'
        ]) }}
    </div>

    <div class="form-group col-md-2">
        {{ form_button([
            'name' => 'send',
            'id' => 'btn_sendMail',
            'class' => 'btn btn-outline-primary',
            'style' => 'width:100%;',
        ],'<i class="far fa-envelope"></i> &nbsp; Invia mail') }}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        $('#btn_sendMail').bind('click', (e) => {

            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ siteUrl('admin/institution/try/sending/email') }}',
                dataType: "JSON",
                data: {
                    'smtp_username': $('#input_smtp_username').val(),
                    'smtp_password': $('#input_smtp_password').val(),
                    'smtp_host': $('#input_smtp_host').val(),
                    'smtp_port': $('#input_smtp_port').val(),
                    'smtp_security': $('#smtp_security').find(':selected').val(),
                    'smtp_auth': $('#smtp_auth').find(':selected').val(),
                    'email': $('#input_smtp_test_email').val(),
                },

                beforeSend: () => {

                    $('#btn_sendMail')
                        .empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...')
                        .attr("disabled", false);
                },

                success: (response) => {
                    let resp = parseJson(response);

                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'ATTENZIONE',
                        subtitle: 'ok',
                        autohide: true,
                        delay: 5000,
                        body: resp.data.message
                    });
                },

                complete: () => {
                    $('#btn_sendMail')
                        .empty()
                        .append('<i class="far fa-envelope"></i> &nbsp; Invia mail')
                        .attr("disabled", false);
                },

                error: (jqXHR, status) => {
                    let response = parseJson(jqXHR.responseText);
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'ATTENZIONE',
                        subtitle: 'Errori',
                        autohide: true,
                        delay: 5000,
                        body: response.errors.error
                    });
                }
            });
        });
    });
</script>
