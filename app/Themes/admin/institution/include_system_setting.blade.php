<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-6">
        {{--  Url portale PAT --}}
        <div class="form-group">
            <label for="snapchat">Url portale PAT *</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-link"></i>
                    </span>
                </div>
                {{ form_input([
                    'name' => 'trasparenza_urls',
                    'value' => !empty($institution['trasparenza_urls']) ? $institution['trasparenza_urls'] : null,
                    'placeholder' => 'https://www.',
                    'id' => 'input_trasparenza_urls',
                    'class' => 'form-control input_trasparenza_urls'
                ]) }}
            </div>
        </div>
    </div>

    {{--  Campo Nome breve ente --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="trasparenza_urls">Nome breve ente *</label>
            {{ form_input([
                'name' => 'short_institution_name',
                'value' => !empty($institution['short_institution_name']) ? $institution['short_institution_name'] : null,
                'placeholder' => 'Nome breve ente',
                'id' => 'input_short_institution_name',
                'class' => 'form-control input_short_institution_name'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Campo Tipo di ente --}}
        <div class="form-group">
            <label for="institution_type_id">Tipo di ente</label>
            <div class="select2-blue" id="input_institution_type_id">
                {{ form_dropdown(
                    'institution_type_id',
                    ['' => ''],
                    @$institution['institution_type_id'],
                    'class="select2-institution_type_id" data-dropdown-css-class="select2-blue" style="width: 100%; height: unset;"'
                ) }}
            </div>
        </div>
    </div>

    {{--  Campo Nome breve ente --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="trasparenza_urls">Cookie - Dominio *</label>
            {{ form_input([
                'name' => 'domain_cookies',
                'value' => !empty($institution['domain_cookies']) ? $institution['domain_cookies'] : null,
                'placeholder' => 'Cookie - Dominio',
                'id' => 'input_domain_cookies',
                'class' => 'form-control input_domain_cookies'
            ]) }}
        </div>
    </div>
</div>

@if(!empty($sectionIds))
    {{ form_input([
        'type' => 'hidden',
        'name' => '_sectionIds',
        'value' => implode(',',$sectionIds),
        'id' => '_sectionIds',
        'class' => '_sectionIds',
    ]) }}
@endif