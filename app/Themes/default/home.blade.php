<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{% extends layout %}
{% block content %}
    <h1>PatOS</h1>
    <p>{{ siteUrl() }}</p>
    <p>{{ anchor('/auth', 'LOGIN') }}</p>
    <p>
        Version Pat OS: <strong>{{ VERSION_PATOS }}</strong>
    </p>
{% endblock %}