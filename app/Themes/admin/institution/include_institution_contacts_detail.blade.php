<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">

    <div class="col-md-6">
        {{--  Comune --}}
        <div class="form-group">
            <label for="address_city">Comune *</label>
            {{ form_input([
                'name' => 'address_city',
                'value' => !empty($institution['address_city']) ? $institution['address_city'] : null,
                'placeholder' => 'Comune',
                'id' => 'input_address_city',
                'class' => 'form-control input_address_city',
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{--  Indirizzo --}}
        <div class="form-group">
            <label for="address_street">Indirizzo *</label>
            {{ form_input([
                'name' => 'address_street',
                'value' => !empty($institution['address_street']) ? $institution['address_street'] : null,
                'placeholder' => 'Indirizzo',
                'id' => 'input_address_street',
                'class' => 'form-control input_address_street',
            ]) }}
        </div>
    </div>

    <div class="col-md-4">
        {{--  CAP --}}
        <div class="form-group">
            <label for="address_zip_code">CAP *</label>
            {{ form_input([
                'name' => 'address_zip_code',
                'value' => !empty($institution['address_zip_code']) ? $institution['address_zip_code'] : null,
                'placeholder' => 'Codice avviamento postale',
                'id' => 'input_address_zip_code',
                'class' => 'form-control input_address_zip_code',
            ]) }}
        </div>
    </div>

    <div class="col-md-4">
        {{--  CAP --}}
        <div class="form-group">
            <label for="address_province">Provincia *</label>
            {{ form_input([
                'name' => 'address_province',
                'value' => !empty($institution['address_province']) ? $institution['address_province'] : null,
                'placeholder' => 'Provincia',
                'id' => 'input_address_province',
                'class' => 'form-control input_address_province',
            ]) }}
        </div>
    </div>

    <div class="col-md-4">
        {{--  Recapito telefonico principale --}}
        <div class="form-group">
            <label for="phone">Recapito telefonico principale *</label>
            {{ form_input([
                'name' => 'phone',
                'value' => !empty($institution['phone']) ? $institution['phone'] : null,
                'placeholder' => 'Recapito telefonico principale',
                'id' => 'input_phone',
                'class' => 'form-control input_phone',
            ]) }}
        </div>
    </div>

</div>
