<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{--  Form per l'autenticazione dell'utente --}}
{% extends layout/auth %}

{% block title %}
Procedura di recupero password
{% endblock %}

{% block content %}
<p>{{ __('head_auth_lost_password',null,'patos_auth') }}</p>

@if(!empty($errors))
    <div class="alert alert-danger alert-dismissible" role="alert" style="padding:1rem">
        @foreach($errors AS $key=>$value)
            <div style="margin-bottom:1rem">{{ $value }}</div>
        @endforeach
    </div>
@endif
{{ form_open('/lost-password?t='.$token, ['method'=>'POST'], 'class="login100-form"') }}
<div class="input-group mb-3">
    {{ form_input([
        'name' => 'email',
        'class' => 'form-control',
        'placeholder' =>  __('placeholder_lost_password_email',null,'patos_auth'),
    ]) }}
    <div class="input-group-append">
        <div class="input-group-text">
            <span class="fas fa-envelope"></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('btn_get_new_password',null,'patos_auth') }}
        </button>
    </div>

</div>
{{ form_close("\n") }}
<p class="mb-1 mt-3">
    {{ anchor('/auth', __('anchor_back_auth',null,'patos_auth')) }}
</p>
{% endblock %}
