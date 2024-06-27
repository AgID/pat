<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{--  Form per l'autenticazione dell'utente --}}
{% extends layout/auth %}

{% block title %}Nuova procedura di recupero avviata{% endblock %}

{% block content %}
<p>{{ __('head_auth_lost_password',null,'patos_auth') }}</p>

<div class="row">
    <div class="col-md-12">
        <p>
            La procedura di recupero password è stata avviata.
        </p>
        <p>
            Il sistema sta verificando se la casella email inserita &egrave; presente nell'archvio digitale. Se la verifica
            dar&agrave; esito positivo, verr&agrave; inviata una email in automatico  con le istruzioni per il recupero della password.
        </p>
        <p>
            Tale procedura ha una validit&agrave; di <strong>24 ore</strong>, a partire dalla data di richiesta di
            recupero password.<br/>
        </p>
        <p>
            Alla scadenza sar&agrave; necessario avviare una nuova procedura.
        </p>
    </div>

    <div class="col-md-12 mt-3">
        {{ anchor('/auth', '<i class="fas fa-sign-in-alt"></i> Torna alla pagina di autenticazione') }}
    </div>
</div>

{% endblock %}
