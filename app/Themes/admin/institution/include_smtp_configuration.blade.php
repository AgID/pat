<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
@php
    $smtpGuides = '';
@endphp

<div class="row">

    <div class="col-md-{{ !empty($smtpGuides) ? '8' : '12' }}">
        {{--  SMTP - Username --}}
        <div class="form-group">
            <label for="sender_type_mailer">Seleziona la tipologia di invio</label>
            {{  form_dropdown(
                    'sender_type_mailer', 
                    [0 => 'SMTP classico'],
                    !empty($institution['sender_type_mailer']) ? $institution['sender_type_mailer'] : 0,
                    'class="form-control input_sender_type_mailer" id="input_sender_type_mailer"',
                ) }}
        </div>
    </div>

    @if(!empty($smtpGuides))
    <div class="col-md-4">
        <div class="form-group">
            <label>&nbsp;</label>
            {{ $smtpGuides }}
        </div>
    </div>
    @endif

</div>

{{-- Container 0: SMTP Classico --}}
<div class="row sender-config-container" id="container_type_0">
    <div class="col-md-6">
        {{--  SMTP - Username --}}
        <div class="form-group">
            <label for="smtp_username">SMTP - Username</label>
            {{ form_input([
                'name' => 'smtp_username',
                'value' => !empty($institution['smtp_user']) ? $institution['smtp_user'] : null,
                'placeholder' => 'SMTP Username',
                'id' => 'input_smtp_username',
                'class' => 'form-control input_smtp_username'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - From --}}
        <div class="form-group">
            <label for="smtp_from">SMTP - From</label>
            {{ form_input([
                'name' => 'smtp_from',
                'value' => !empty($institution['smtp_from']) ? $institution['smtp_from'] : null,
                'placeholder' => 'SMTP - From',
                'id' => 'input_smtp_from',
                'class' => 'form-control input_smtp_from'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- SMTP - Password --}}
        <div class="form-group">
            <label for="smtp_password">SMTP - Password </label>
            {{ form_password([
                'name' => 'smtp_password',
                'value' =>  !empty($institution['smtp_pass']) ? '********'  : null,
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
                     'ssl' => 'SSL',
                     'tls' => 'TLS'
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

<div class="row">
    <div class="col-md-12">
        <div class="custom-separator"><h5>CONFIGURAZIONE SMTP P.E.C.</h5></div>
    </div>
</div>

{{-- Configurazione PEC: solo SMTP Classico --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_username">PEC SMTP - Username</label>
            {{ form_input([
                'name' => 'pec_smtp_username',
                'value' => !empty($institution['pec_smtp_user']) ? $institution['pec_smtp_user'] : null,
                'placeholder' => 'PEC SMTP Username',
                'id' => 'input_pec_smtp_username',
                'class' => 'form-control input_pec_smtp_username'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_from">PEC SMTP - From</label>
            {{ form_input([
                'name' => 'pec_smtp_from',
                'value' => !empty($institution['pec_smtp_from']) ? $institution['pec_smtp_from'] : null,
                'placeholder' => 'PEC SMTP - From',
                'id' => 'input_pec_smtp_from',
                'class' => 'form-control input_pec_smtp_from'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_password">PEC SMTP - Password </label>
            {{ form_password([
                'name' => 'pec_smtp_password',
                'value' =>  !empty($institution['pec_smtp_pass']) ? '********'  : null,
                'placeholder' => 'PEC SMTP - Password',
                'id' => 'input_pec_smtp_password',
                'class' => 'form-control input_pec_smtp_password'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_host">PEC SMTP - Indirizzo server </label>
            {{ form_input([
                'name' => 'pec_smtp_host',
                'value' => !empty($institution['pec_smtp_host']) ? $institution['pec_smtp_host'] : null,
                'placeholder' => 'PEC SMTP - Indirizzo server',
                'id' => 'input_pec_smtp_host',
                'class' => 'form-control input_pec_smtp_host'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_port">PEC SMTP - Porta </label>
            {{ form_input([
                'name' => 'pec_smtp_port',
                'value' => !empty($institution['pec_smtp_port']) ? $institution['pec_smtp_port'] : null,
                'placeholder' => 'PEC SMTP - Porta',
                'id' => 'input_pec_smtp_port',
                'class' => 'form-control input_pec_smtp_port'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_security">
                PEC SMTP - SSL
            </label>
            {{ form_dropdown(
                 'pec_smtp_security',
                 [
                     'no' => 'No',
                     'ssl' => 'SSL',
                     'tls' => 'TLS'
                 ],
                !empty($institution['pec_smtp_security']) ? $institution['pec_smtp_security'] : null,
                 'id="pec_smtp_security" class="form-control select2-pec_smtp_security" style="width: 100%;"' )
             }}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="pec_smtp_auth">
                PEC SMTP - Usa autenticazione
            </label>
            {{ form_dropdown(
                 'pec_show_smtp_auth',
                 [''=>'',1=>'Si', 2=>'No'],
                !empty($institution['pec_show_smtp_auth']) ? $institution['pec_show_smtp_auth'] : null,
                 'id="pec_smtp_auth" class="form-control select2-pec_show_smtp_auth" style="width: 100%;"' )
             }}
        </div>
    </div>
</div>

<div class="form-row d-flex align-items-end">
    <div class="form-group col-md-6">
        {{-- Test del server inserisci un indirizzo email --}}
        <label for="pec_smtp_test_email">Test del server PEC inserisci un indirizzo email </label>
        {{ form_input([
            'name' => 'pec_smtp_test_email',
            'value' => null,
            'placeholder' => 'Test del server PEC inserisci un indirizzo email',
            'id' => 'input_pec_smtp_test_email',
            'class' => 'form-control input_pec_smtp_test_email'
        ]) }}
    </div>

    <div class="form-group col-md-2">
        {{ form_button([
            'name' => 'send_pec',
            'id' => 'btn_sendPecMail',
            'class' => 'btn btn-outline-primary',
            'style' => 'width:100%;',
        ],'<i class="far fa-envelope"></i> &nbsp; Invia mail PEC') }}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(() => {

        // Funzione per gestire la visualizzazione dei container in base alla selezione
        const toggleSenderContainers = () => {
            const selectedType = $('#input_sender_type_mailer').val();
            $('.sender-config-container').hide();
            $('#container_type_' + selectedType).show();
        };

        // Esegui al caricamento
        toggleSenderContainers();

        // Esegui al cambio selezione
        $('#input_sender_type_mailer').on('change', () => {
            toggleSenderContainers();
        });

        $('#btn_sendMail').bind('click', (e) => {

            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ siteUrl("admin/institution/try/sending/email") }}',
                dataType: "JSON",
                data: {
                    'sender_type_mailer': $('#input_sender_type_mailer').val(),
                    // SMTP Classico
                    'smtp_username': $('#input_smtp_username').val(),
                    'smtp_from': $('#input_smtp_from').val(),
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
                        .attr("disabled", true);
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

        $('#btn_sendPecMail').bind('click', (e) => {

            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ siteUrl("admin/institution/try/sending/pec-email") }}',
                dataType: "JSON",
                data: {
                    'pec_smtp_username': $('#input_pec_smtp_username').val(),
                    'pec_smtp_from': $('#input_pec_smtp_from').val(),
                    'pec_smtp_password': $('#input_pec_smtp_password').val(),
                    'pec_smtp_host': $('#input_pec_smtp_host').val(),
                    'pec_smtp_port': $('#input_pec_smtp_port').val(),
                    'pec_smtp_security': $('#pec_smtp_security').find(':selected').val(),
                    'pec_smtp_auth': $('#pec_smtp_auth').find(':selected').val(),
                    'email': $('#input_pec_smtp_test_email').val()
                },

                beforeSend: () => {

                    $('#btn_sendPecMail')
                        .empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...')
                        .attr("disabled", true);
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
                    $('#btn_sendPecMail')
                        .empty()
                        .append('<i class="far fa-envelope"></i> &nbsp; Invia mail PEC')
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
