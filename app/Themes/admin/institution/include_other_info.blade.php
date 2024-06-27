<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-6">
        {{--  Responsabile del procedimento di pubblicazione --}}
        <div class="form-group">
            <label for="">Responsabile del procedimento di pubblicazione </label>
            {{ form_input([
                'name' => 'publication_responsible',
                'value' => !empty($institution['publication_responsible']) ? $institution['publication_responsible'] : null,
                'placeholder' => 'Responsabile',
                'id' => 'input_publication_responsible',
                'class' => 'form-control input_publication_responsible'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{--  Url Privacy --}}
        <div class="form-group">
            <label for="snapchat">Url Privacy</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-link"></i>
                    </span>
                </div>
                {{ form_input([
                    'name' => 'privacy_url',
                    'value' => !empty($institution['privacy_url']) ? $institution['privacy_url'] : null,
                    'placeholder' => 'https://www.',
                    'id' => 'input_privacy_url',
                    'class' => 'form-control input_privacy_url'
                ]) }}
            </div>
        </div>
    </div>

    <div class="form-group">

    </div>

    <div class="col-md-12">
        {{-- Testo iniziale homepage --}}
        <div class="form-group">
            <label for="welcome_text">Testo iniziale homepage</label>
            {{form_editor([
                'name' => 'welcome_text',
                'value' => !empty($institution['welcome_text']) ? $institution['welcome_text'] : null,
                'id' => 'input_welcome_text',
                'class' => 'form-control input_welcome_text'
            ]) }}
        </div>
    </div>

    <div class="col-md-12">
        {{-- Testo nel footer --}}
        <div class="form-group">
            <label for="footer_text">Testo nel footer</label>
            {{form_editor([
                'name' => 'footer_text',
                'value' => !empty($institution['footer_text']) ? $institution['footer_text'] : null,
                'id' => 'input_footer_text',
                'class' => 'form-control input_footer_text'
            ]) }}
        </div>
    </div>

    <div class="col-md-12">
        {{-- Testo accessibilità --}}
        <div class="form-group">
            <label for="accessibility_text">Testo accessibilità</label>
            {{form_editor([
                'name' => 'accessibility_text',
                'value' => !empty($institution['accessibility_text']) ? $institution['accessibility_text'] : null,
                'id' => 'input_accessibility_text',
                'class' => 'form-control input_accessibility_text'
            ]) }}
        </div>
    </div>
</div>
