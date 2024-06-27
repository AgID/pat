<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-6">
        {{-- Campo Nome completo ente --}}
        <div class="form-group">
            <label for="full_name_institution">Nome completo ente *</label>
            {{ form_input([
                'name' => 'full_name_institution',
                'value' => !empty($institution['full_name_institution']) ? $institution['full_name_institution'] : null,
                'placeholder' => 'Nome completo ente',
                'id' => 'input_full_name_institution',
                'class' => 'form-control input_full_name_institution'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Campo Partita IVA --}}
        <div class="form-group">
            <label for="vat">Partita IVA *</label>
            {{ form_input([
                'name' => 'vat',
                'value' => !empty($institution['vat']) ? $institution['vat'] : null,
                'placeholder' => 'Partita IVA',
                'id' => 'input_vat',
                'class' => 'form-control input_vat',
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Indirizzo email normale --}}
        <div class="form-group">
            <label for="email_address">Indirizzo email normale *</label>
            {{ form_input([
                'name' => 'email_address',
                'value' => !empty($institution['email_address']) ? $institution['email_address'] : null,
                'placeholder' => 'Indirizzo email normale',
                'id' => 'input_email_address',
                'class' => 'form-control input_email_address',
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Indirizzo email certificata --}}
        <div class="form-group">
            <label for="certified_email_address">Indirizzo email certificata *</label>
            {{ form_input([
                'name' => 'certified_email_address',
                'value' => !empty($institution['certified_email_address']) ? $institution['certified_email_address'] : null,
                'placeholder' => 'Indirizzo email certificata',
                'id' => 'input_certified_email_address',
                'class' => 'form-control input_certified_email_address',
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{--  Ente di appartenenza --}}
        <div class="form-group">
            <label for="top_level_institution_name">Ente di appartenenza</label>
            {{ form_input([
                'name' => 'top_level_institution_name',
                'value' => !empty($institution['top_level_institution_name']) ? $institution['top_level_institution_name'] : null,
                'placeholder' => 'Ente di appartenenza',
                'id' => 'input_top_level_institution_name',
                'class' => 'form-control input_top_level_institution_name'
            ]) }}
        </div>
    </div>

    <div class="col-md-6">
        {{--  URL ente di appartenenza --}}
        <div class="form-group">
            <label for="snapchat">URL ente di appartenenza</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-link"></i>
                    </span>
                </div>
                {{ form_input([
                    'name' => 'top_level_institution_url',
                    'value' => !empty($institution['top_level_institution_url']) ? $institution['top_level_institution_url'] : null,
                    'placeholder' => 'https://www.',
                    'id' => 'input_top_level_institution_url',
                    'class' => 'form-control input_top_level_institution_url'
                ]) }}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        {{-- Allegato logo ente --}}
        <div class="form-group">
            <label for="simple_logo_file">Logo ente* </label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="simple_logo_file"
                       name="simple_logo_file" accept="image/png, image/gif, image/jpeg">
                <label class="custom-file-label" for="simple_logo_file"
                       id="label_attach_logo">
                    Allega il logo dell'ente
                </label>
            </div>
            <div id="preview-url-logo">
                <div class="mt-2">
                    <img src="" alt="Logo Ente" id="src-logo-ente"
                         class="img-thumbnail attach-image">
                    <div class="mt-2">
                        <button type="button" class="btn btn-xs btn-outline-danger"
                                id="clear-preview-logo">
                            <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina logo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        {{-- Allegato immagine favico --}}
        <div class="form-group">
            <label for="full_name_institution">Icon favicon* </label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="favicon_file"
                       name="favicon_file" accept="image/png, image/ico">
                <label class="custom-file-label" for="favicon_file" id="label_favicon_file">
                    Allega l'icona di favicon
                </label>
            </div>
            <div id="preview-url-favicon">
                <div class="mt-2">
                    <img src="" alt="Icon favicon" id="src-favicon-ente"
                         class="img-thumbnail attach-image">
                    <div class="mt-2">
                        <button type="button" class="btn btn-xs btn-outline-danger"
                                id="clear-preview-favicon">
                            <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina favicon
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        {{--  Url portale istituzionale --}}
        <div class="form-group">
            <label for="snapchat">Url portale istituzionale</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-link"></i>
                    </span>
                </div>
                {{ form_input([
                    'name' => 'institutional_website_url',
                    'value' => !empty($institution['institutional_website_url']) ? $institution['institutional_website_url'] : null,
                    'placeholder' => 'https://www.',
                    'id' => 'input_institutional_website_url',
                    'class' => 'form-control input_institutional_website_url'
                ]) }}
            </div>
        </div>
    </div>
</div>