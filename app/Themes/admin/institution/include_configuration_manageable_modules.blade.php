<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    @if(!empty($institution['trasp_responsible_user_id']))
        {{ form_input([
            'type' => 'hidden',
            'name' => 'input_trasp_responsible_user_id',
            'value' => $institution['trasp_responsible_user_id'],
            'id' => 'input_trasp_responsible_user_id',
            'class' => 'trasp_responsible_user_id',
        ]) }}
    @endif

    <div class="col-md-6">
        {{-- Campo Url Albo Pretorio --}}
        <div class="form-group">
            <label for="snapchat">Url Albo Pretorio</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-link"></i>
                    </span>
                </div>
                {{ form_input([
                    'name' => 'bulletin_board_url',
                    'value' => !empty($institution['bulletin_board_url']) ? $institution['bulletin_board_url'] : null,
                    'placeholder' => 'https://www.',
                    'id' => 'input_bulletin_board_url',
                    'class' => 'form-control input_bulletin_board_url'
                ]) }}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        {{-- Campo Visualizzazione tabellare degli Organi di ind. politico --}}
        <div class="form-group">
            <label for="tabular_display_org_ind_pol">Visualizzazione tabellare degli Organi
                di ind. politico</label>
            {{ form_dropdown(
                'tabular_display_org_ind_pol',
                [0=>'No',1=>'Si'],
                !empty($institution['tabular_display_org_ind_pol']) ? $institution['tabular_display_org_ind_pol'] : null,
                'class="form-control select2-tabular_display_org_ind_pol" style="width: 100%;"' )
            }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Campo Mostra la data di ultimo aggiornamento dei contenuti --}}
        <div class="form-group">
            <label for="show_update_date">
                Mostra la data di ultimo aggiornamento dei contenuti
            </label>
            {{ form_dropdown(
                 'show_update_date',
                 [0=>'No',1=>'Si'],
                 !empty($institution['show_update_date']) ? $institution['show_update_date'] : null,
                 'class="form-control select2-show_update_date" style="width: 100%;"' )
             }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Indicizzabile dai motori di ricerca --}}
        <div class="form-group">
            <label for="indexable">Indicizzabile dai motori di ricerca</label>
            {{ form_dropdown(
                 'indexable',
                 ['0'=>'No','1'=>'Si'],
                 !empty($institution['indexable']) ? $institution['indexable'] : null,
                 'class="form-control select2-show_indexable" style="width: 100%;"' )
             }}
        </div>
    </div>

    <div class="col-md-6">
        {{-- Campo Mostra la Normativa associata alla Struttura organizzativa visualizzata --}}
        <div class="form-group">
            <label for="show_regulation_in_structure">
                Mostra Norma associata alla Struttura organizzativa
            </label>
            {{ form_dropdown(
                 'show_regulation_in_structure',
                 [0=>'No', 1=>'Si'],
                !empty($institution['show_regulation_in_structure']) ? $institution['show_regulation_in_structure'] : null,
                 'class="form-control select2-show_regulation_in_structure" style="width: 100%;"' )
             }}
        </div>
    </div>

    @if($_storageType === 'update')
    <div class="col-md-6">
        {{-- Campo Responsabile della Trasparenza --}}
        <div class="form-group">
            <label for="user">Responsabile della Trasparenza</label>
            <div class="select2-blue">
                {{ form_dropdown(
                    'trasp_responsible_user_id',
                    ['' => ''],
                    '',
                    'class="form-control select2-users" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                ) }}
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-12">
        {{-- Codice di monitoraggio statistiche --}}
        <div class="form-group">
            <label for="statistics_tracking_code">
                Codice di monitoraggio statistiche
            </label>
            {{ form_textarea([
                'name' => 'statistics_tracking_code',
                'value' => !empty($institution['statistics_tracking_code']) ? $institution['statistics_tracking_code'] : null,
                'placeholder' => 'Codice di monitoraggio statistiche',
                'id' => 'statistics_tracking_code',
                'class' => 'form-control statistics_tracking_code',
                'rows' => '5',
                'cols' => '10'
            ]) }}
        </div>
    </div>
</div>
