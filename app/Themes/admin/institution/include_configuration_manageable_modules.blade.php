<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{ generateSeparator('CONFIGURAZIONI GENERALI') }}
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
        <div class="form-group">
            <label for="bulletin_board_url_name">Etichetta Url Albo Pretorio</label>
            <div class="input-group">
                {{ form_input([
                    'name' => 'bulletin_board_url_name',
                    'value' => !empty($institution['bulletin_board_url_name']) ? $institution['bulletin_board_url_name'] : null,
                    'placeholder' => 'Albo pretorio',
                    'id' => 'input_bulletin_board_url_name',
                    'class' => 'form-control input_bulletin_board_url_name'
                ]) }}
            </div>
        </div>
    </div>

        @php
            //Mostra la data di ultimo aggiornamento dei contenuti
            //Con l'aggiornamento al PNA 2025, la data deve essere sempre pubblicata.
            echo form_input([
                    'type' => 'hidden',
                    'name' => 'show_update_date',
                    'value' => 1,
                    'id' => 'show_update_date',

                ]);
        @endphp

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



</div>

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


{{generateSeparator("Mappa dei link")}}
<div class="form-row align-items-end">

    <div class="alert alert-info mt-2 col-md-12" role="alert">
        Se non viene caricata alcuna mappa dei link, la piattaforma ne genererà automaticamente una. Se invece carichi
        un file (formato JSON), verrà sempre utilizzata la versione fornita.<br>
        Puoi consultare la mappa attualmente in uso al seguente <a class="text-blue" href="{{siteUrl("/at_map.json")}}"
                                                                   target="_blank">link</a>.<br>
        @if(!empty($custom_at_map_exist))
            <span><strong>Nota:</strong> è stata inserita una mappa personalizzata.</span>
        @else
            <span><strong>Nota:</strong> la mappa è generata automaticamente dalla piattaforma.</span>
        @endif
    </div>

    <div class="form-group col-md-5" id="insert-atmap">
        <label for="custom_at_map">AT Map</label>
        <div class="custom-file mb-2">
            <input type="file"
                   class="custom-file-input"
                   id="custom_at_map"
                   name="custom_at_map"
                   accept=".json,application/json"
                   aria-describedby="custom_at_map_help">
            <label class="custom-file-label" for="custom_at_map" id="label_attach_custom_at_map">
                Seleziona file JSON
            </label>
        </div>
        <small id="custom_at_map_help" class="form-text text-muted">
            Formato ammesso: JSON. Dimensione massima:
            {{ \Helpers\Utility\FileUpload::maxSizeLabel(5024 * 1024) }}.
        </small>
    </div>

    @if(@$custom_at_map_exist)
        <input type="hidden" name="delete_custom_at_map" id="delete_custom_at_map" value=""/>
        <div class="form-group col-md-2">
            <button type="button" class="btn btn-outline-danger mb-2" id="btn-remove-map" onclick="rimuoviAtMap()">
                Rimuovi la mappa caricata
            </button>
        </div>
    @endif
</div>

{{ generateSeparator('Personale') }}
<div class="row">
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


    <div class="form-group col-md-12">
        <div class="row">
            <div class="col-md-12">
            </div>
            <div class="col-md-6">
                <label for="additional_qualifications">
                    Qualifiche aggiuntive per il personale
                    <span class="far fa-question-circle fa-xs" data-toggle="tooltip"
                          data-placement="right" data-html="true"
                          data-original-title="L'inserimento di qualifiche comporta la comparsa di un apposito campo denominato <b>Qualifica</b> nel from dell'archivio del Personale">
                                    </span>
                </label>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-default btn-sm" data-toggle="modal"
                        data-target="#qualifications-modal" data-storage-type="new"
                        id="new-data-qualifications">
                    Aggiungi nuova qualifica
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="col-md-12 mt-3 table-responsive">
                <table class="table table-hover table-bordered table-striped table-sm"
                       id="table-qualifications">
                    <thead class="thead-dark">
                    <tr>
                        <th style="width:10%;" class="text-center">Nome qualifica</th>
                        <th style="width:5%;" class="text-center">Visibile nel form del personale?</th>
                        <th style="width:3%;" class="text-center">Azioni</th>
                    </tr>
                    </thead>
                    <tbody id="qualifications-table">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




