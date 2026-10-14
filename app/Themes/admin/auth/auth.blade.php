<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{--  Form per l'autenticazione dell'utente --}}
{% extends layout/auth %}

{% block content %}

@if(!empty($loginSap))


    <p style="display: flex; justify-content: center;"><a class="btn btn-primary" data-toggle="collapse" href="#collapseLogin" role="button" aria-expanded="false" aria-controls="collapseLogin">Autenticazione utente</a></p>
    <div class="collapse" id="collapseLogin">
@else
    <p>Autenticazione utente</p>
@endif

{{-- Errore autenticazione --}}
@if(!empty($errors))
    <div class="alert alert-danger alert-dismissible" role="alert" style="padding:1rem">
        @foreach($errors AS $key=>$value)
            <div style="margin-bottom:1rem">{{ $value }}</div>
        @endforeach
    </div>
@endif
{{-- Logout utente --}}
@if(!empty($logout))
    <div class="alert alert-info">
        {{ $logout }}
    </div>
@endif
{{-- Notifica procedura di recupero password--}}
@if(!empty($recoverySuccess))
    <div class="alert alert-success">
        {{  $recoverySuccess }}
    </div>
@endif
{{ form_open('/auth?t='.$token, ['method'=>'POST']) }}
{{ form_input([
   'type' => 'hidden',
   'name' => 'dpm',
   'id' => 'dpm',
   'value' => 'false'
]) }}
<div class="input-group mb-3">
    {{ form_input([
        'name' => 'username',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'placeholder' => __('placeholder_auth_username',null,'patos_auth'),
        'required' => 'required'
    ]) }}
    <div class="input-group-append">
        <div class="input-group-text">
            <span class="fas fa-user"></span>
        </div>
    </div>
</div>
<div class="input-group mb-3">
    {{ form_password([
        'name' => 'password',
        'id' => 'password-field',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'placeholder' =>  __('placeholder_auth_password',null,'patos_auth'),
        'required' => 'required'
    ]) }}
    <div class="input-group-append">
        <div class="input-group-text">
            <span id="toggle-password" class="fas fa-eye" style="cursor: pointer;" title="Mostra password"></span>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('btn_auth_submit',null,'patos_auth') }}
        </button>
    </div>

</div>
{{ form_close("\n") }}

<p class="mb-1 mt-3" style="display: flex;justify-content: space-between;">
    {{ anchor('/lost-password', __('anchor_lost_password',null,'patos_auth')) }}
    <a href="{{ baseUrl() }}">Torna alla home</a>
</p>

@if(!empty($loginSap))
   </div>
@endif
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        detectIncognito().then((result) => {
            {{-- console.log(result.browserName, result.isPrivate); --}}
            var selectElement = document.querySelector('input[name="dpm"]');
            if (selectElement) {
                selectElement.value = result.isPrivate;
            }
        }).catch((error) => {
            console.warn('detectIncognito non è riuscito a determinare il browser:', error);
        });

        // Funzionalità toggle password
        const togglePassword = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password-field');

        togglePassword.addEventListener('click', function() {
            // Cambia il tipo del campo password
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Cambia l'icona e il tooltip
            if (type === 'password') {
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
                this.setAttribute('title', 'Mostra password');
            } else {
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
                this.setAttribute('title', 'Nascondi password');
            }
        });
    });
</script>

@if(empty($loginSap))

@endif
{% endblock %}
