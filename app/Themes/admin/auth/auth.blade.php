<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{--  Form per l'autenticazione dell'utente --}}
{% extends layout/auth %}

{% block title %}Autenticazione utente{% endblock %}

{% block content %}
<p>{{ __('head_auth_user',null,'patos_auth') }}</p>

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
        'class' => 'form-control',
        'autocomplete' => 'off',
        'placeholder' =>  __('placeholder_auth_password',null,'patos_auth'),
    ]) }}
    <div class="input-group-append">
        <div class="input-group-text">
            <span class="fas fa-lock"></span>
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
<p class="mb-1 mt-3">
    {{ anchor('/lost-password', __('anchor_lost_password',null,'patos_auth')) }}
</p>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        detectIncognito().then((result) => {
            {{-- console.log(result.browserName, result.isPrivate); --}}
            var selectElement = document.querySelector('input[name="dpm"]');
            selectElement.value = result.isPrivate;
        });
    });

</script>
{% endblock %}
