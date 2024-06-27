<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{--  Form per l'autenticazione dell'utente --}}
{% extends layout/auth %}

{% block title %}Reset Password{% endblock %}


{% block content %}

@if($hasError===false)

    {{ form_open('/recovery/password?t='.$uriToken, ['method'=>'POST'], 'class="login100-form"') }}

    <p>{{ __('head_recovery_password',null,'patos_auth') }}</p>

    <p style="line-height: 16px">
        {{ __('msg_recovery_password',null,'patos_auth') }}
    </p>


    {{-- Errore procedura recupero password --}}
    @if(!empty($errors))
        <div class="alert alert-danger alert-dismissible" role="alert" style="padding:1rem">
                @foreach($errors AS $key=>$value)
                    <div style="margin-bottom:1rem">{{ $value }}</div>
                @endforeach
        </div>
    @endif

    <div class="input-group mb-3">
        {{ form_password([
            'name' =>'password',
            'class' => 'form-control',
            'placeholder' => __('placeholder_password',null,'patos_auth')
        ]) }}
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>

    <div class="input-group mb-3">
        {{ form_password([
            'name' => 're_password',
            'class' => 'form-control',
            'placeholder' =>  __('placeholder_repeat_password',null,'patos_auth')
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
                {{ __('btn_set_new_password',null,'patos_auth') }}
            </button>
        </div>
    </div>

    {{-- Token --}}
    {{ form_hidden('token',$token) }}
    {{-- User ID --}}
    {{ form_hidden('uid',$uid) }}
    {{-- Institute ID --}}
    {{ form_hidden('iid',$iid) }}

    {{ form_close("\n") }}

    <p class="mb-1 mt-3">
        {{ anchor('/auth', __('anchor_back_auth',null,'patos_auth'), 'class="txt2"') }}
    </p>

@else
    <div>
        <div class="alert alert-danger" role="alert">
            <h4>{{ __('btn_auth_submit',null,'patos_auth') }}</h4>
            <p>{{ __('notify_error_body',null,'patos_auth') }}</p>
        </div>
    </div>
@endif

{% endblock %}
