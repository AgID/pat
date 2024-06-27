{{-- Form store Strutture Organizzative --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row justify-content-center">
    <div class="col-xl-10">
        {{ form_open($formAction,$formSettings) }}
        <div class="card mb-4" id="card-filter">
            <h4 class="card-header">
                <span>
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>
                    @if($_storageType === 'insert')
                        Aggiunta Struttura
                    @elseif($_storageType === 'update')
                        Modifica Struttura
                    @else
                        Duplicazione Struttura
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/structure') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco strutture
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </h4>
            <div class="card-body card-primary" id="new">

                <div class="row">
                    <div class="text-muted col-md-9 mb-4">
                        <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} I Campi contrassegnati dal
                        simbolo asterisco (*) sono obbligatori.
                    </div>

                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div>
                            {{-- Campo Nome struttura --}}
                            <div class="form-group">
                                <label for="name">Nome struttura * </label>
                                {{ form_input([
                                    'name' => 'structure_name',
                                    'value' => !empty($structure['structure_name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $structure['structure_name'] : $structure['structure_name']) : null,
                                    'placeholder' => 'Nome struttura',
                                    'id' => 'input_structure_name',
                                    'class' => 'form-control input_structure_name'
                                ]) }}
                            </div>

                            {{-- Campo Struttura di appartenenza --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structure_of_belonging" id="structure_of_belonging_label">Struttura
                                            di appartenenza</label>
                                    </div>
                                    <div id="ajax_structure_of_belonging"></div>
                                    <input type="hidden" value="" name="structure_of_belonging_id"
                                           id="input_structure_of_belonging_id"
                                           class="structure_of_belonging_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Utilizza in Articolazione degli Uffici --}}
                                <div class="form-group col-md-6">
                                    <label for="articulationtion">Utilizza in Articolazione degli Uffici</label>
                                    <div class="select2-blue" id="input_articulation">
                                        {{ form_dropdown(
                                            'articulation',
                                            [''=>'',1=>'Si',0=>'No'],
                                            @$structure['articulation'],
                                            'class="select2-articulation" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Ordine --}}
                                <div class="form-group col-md-6">
                                    <label for="order">Ordine di visualizzazione</label>
                                    {{ form_input([
                                        'type' => 'number',
                                        'name' => 'order',
                                        'value' => !empty($structure['order']) ? $structure['order'] : 1,
                                        'placeholder' => 'Ordine di visualizzazione',
                                        'id' => 'input_order',
                                        'class' => 'form-control input_order'
                                    ]) }}
                                </div>
                            </div>

                            {{generateSeparator('Personale collegato')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Responsabile non disponibile --}}
                                <div class="form-group col-md-6">
                                    <label for="responsible_not_available">Responsabile disponibile</label>
                                    <div class="select2-blue" id="input_responsible_not_available">
                                        {{ form_dropdown(
                                            'responsible_not_available',
                                            [1=>'Si',0=>'No',],
                                            @$structure['responsible_not_available'],
                                            'class="form-control select2-responsible_not_available" style="width: 100%;"' )
                                        }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="notAvaiableReferent">
                                    {{-- Campo Note responsabile non disponibile: --}}
                                    <label for="referent_not_available_txt">Note responsabile non disponibile *</label>
                                    {{ form_input([
                                        'name' => 'referent_not_available_txt',
                                        'value' => !empty($structure['referent_not_available_txt']) ? $structure['referent_not_available_txt'] : 'Questa struttura non prevede responsabile',
                                        'placeholder' => 'Note responsabile non disponibile',
                                        'id' => 'input_referent_not_available_txt',
                                        'class' => 'form-control input_referent_not_available_txt'
                                    ]) }}
                                </div>

                                {{-- Campo Ad interim --}}
                                <div class="form-group col-md-6 input_ad_interim disponibleResp">
                                    <label for="ad_interim">Ad interim</label>
                                    {{ form_dropdown(
                                            'ad_interim',
                                            [0=>'No',1=>'Si'],
                                            @$structure['ad_interim'],
                                            'class="form-control select2-ad_interim" style="width: 100%;"')
                                        }}
                                </div>
                            </div>

                            {{-- Campo Responsabile/i --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12 disponibleResp" id="disponibleResp">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_responsibles" id="responsibles_label">Responsabile/i *</label>
                                    </div>
                                    <div id="ajax_responsibles"></div>
                                    <input type="hidden" value="" name="responsibles" id="input_responsibles"
                                           class="responsibles">
                                </div>
                            </div>

                            {{-- Campo Personale da contattare --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="input_contact_personnel">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_to_contacts" id="to_contacts_label">Personale da
                                            contattare</label>
                                    </div>
                                    <div id="ajax_to_contacts"></div>
                                    <input type="hidden" value="" name="toContacts" id="input_to_contacts"
                                           class="toContacts">
                                </div>
                            </div>

                            {{generateSeparator('Informazioni e recapiti')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Indirizzo email non disponibile --}}
                                <div class="form-group col-md-6">
                                    <label for="email_not_available">Indirizzo email disponibile</label>
                                    <div class="select2-blue" id="input_email_not_available">
                                        {{ form_dropdown(
                                            'email_not_available',
                                            [1=>'Si',0=>'No'],
                                            @$structure['email_not_available'],
                                            'class="select2-email_not_available" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Indirizzo email --}}
                                <div class="form-group col-md-6" id="showEmail">
                                    <label for="reference_email">Indirizzo email *</label>
                                    {{ form_input([
                                        'name' => 'reference_email',
                                        'value' => !empty($structure['reference_email']) ? $structure['reference_email'] : null,
                                        'placeholder' => 'Indirizzo email',
                                        'id' => 'input_reference_email',
                                        'class' => 'form-control input_reference_email'
                                    ]) }}
                                </div>

                                {{-- Campo Note email non disponibile --}}
                                <div class="form-group col-md-6" id="showEmailNotAvaiable">
                                    {{-- Campo Note email non disponibile --}}
                                    <label for="email_not_available_txt">Note email non disponibile *</label>
                                    {{ form_input([
                                        'name' => 'email_not_available_txt',
                                        'value' => !empty($structure['email_not_available_txt']) ? $structure['email_not_available_txt'] : 'Indirizzo email non disponibile',
                                        'placeholder' => 'Email non disponibile',
                                        'id' => 'input_email_not_available_txt',
                                        'class' => 'form-control input_email_not_available_txt'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Indirizzo email certificata --}}
                                <div class="form-group col-md-4">
                                    <label for="certified_email">Indirizzo email certificata</label>
                                    {{ form_input([
                                        'name' => 'certified_email',
                                        'value' => !empty($structure['certified_email']) ? $structure['certified_email'] : null,
                                        'placeholder' => 'Indirizzo email certificata',
                                        'id' => 'input_certified_email',
                                        'class' => 'form-control input_certified_email'
                                    ]) }}
                                </div>

                                {{-- Campo Recapito telefonico --}}
                                <div class="form-group col-md-4">
                                    <label for="phone">Recapito telefonico</label>
                                    {{ form_input([
                                        'name' => 'phone',
                                        'value' => !empty($structure['phone']) ? $structure['phone'] : null,
                                        'placeholder' => 'Recapito telefonico',
                                        'id' => 'input_phone',
                                        'class' => 'form-control input_phone'
                                    ]) }}
                                </div>

                                {{-- Campo Recapito fax --}}
                                <div class="form-group col-md-4">
                                    <label for="fax">Recapito fax</label>
                                    {{ form_input([
                                        'name' => 'fax',
                                        'value' => !empty($structure['fax']) ? $structure['fax'] : null,
                                        'placeholder' => 'Recapito telefonico',
                                        'id' => 'input_fax',
                                        'class' => 'form-control input_fax'
                                    ]) }}
                                </div>
                            </div>

                            {{-- Campo Descrizione delle attività --}}
                            <div class="form-group">
                                <label for="description">Descrizione delle attività *</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($structure['description']) ? $structure['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            {{-- Campo Orari al pubblico --}}
                            <div class="form-group">
                                <label for="timetables">Orari al pubblico</label>
                                {{ form_textarea([
                                    'name' => 'timetables',
                                    'value' => !empty($structure['timetables']) ? $structure['timetables'] : null,
                                    'placeholder' => 'Orari al pubblico',
                                    'id' => 'input_timetables',
                                    'class' => 'form-control input_timetables',
                                    'cols' => '10',
                                    'rows' => '4',
                                ]) }}
                            </div>

                            {{-- Campo Struttura con sede --}}
                            <div class="form-group">
                                <label for="based_structure"> Struttura con sede </label>
                                <div class="select2-blue" id="input_based_structure">
                                    {{ form_dropdown(
                                        'based_structure',
                                        [0 => 'No',1=>'Si'],
                                        @$structure['based_structure'],
                                        'class="select2-based_structure" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{-- Inizio Sezione Maps --}}
                            <div class="col-md-12 showMaps">
                                <div id="map" style="min-height: 350px; margin-bottom: 20px;">

                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Indirizzo --}}
                                <div class="form-group col-md-9 showMaps">
                                    <label for="address">Indirizzo</label>
                                    {{ form_input([
                                        'name' => 'address',
                                        'value' => !empty($structure['address']) ? $structure['address'] : null,
                                        'placeholder' => 'Indirizzo',
                                        'id' => 'input_address',
                                        'class' => 'form-control input_address'
                                    ]) }}
                                </div>

                                <div class="form-group col-md-3 showMaps">
                                    {{ form_button([
                                            'name' => 'add',
                                            'id' => 'btn_viewOnMap',
                                            'class' => 'btn btn-outline-primary',
                                            'style' => 'width:100%;'
                                        ],'Visualizza su mappa &nbsp; <i class="fas fa-map-marker-alt"></i>') }}
                                </div>

                                {{-- Campo Latitudine --}}
                                <div class="form-group col-md-5 showMaps" hidden>
                                    <label for="address">Latitudine</label>
                                    {{ form_input([
                                        'name' => 'lat',
                                        'value' => @$structure['lat'],
                                        'placeholder' => 'lat',
                                        'id' => 'input_lat',
                                        'class' => 'form-control input_lat',
                                    ]) }}
                                </div>

                                {{-- Campo Longitudine --}}
                                <div class="form-group col-md-5 showMaps" hidden>
                                    <label for="address">Longitudine</label>
                                    {{ form_input([
                                        'name' => 'lon',
                                        'value' => @$structure['lon'],
                                        'placeholder' => 'lon',
                                        'id' => 'input_lon',
                                        'class' => 'form-control input_lon',
                                    ]) }}
                                </div>
                            </div>
                            {{-- Fine Sezione Maps --}}

                            {{-- Campo Dettaglio indirizzo --}}
                            <div class="form-group">
                                <label for="address_detail">Dettaglio indirizzo</label>
                                {{ form_input([
                                    'name' => 'address_detail',
                                    'value' => !empty($structure['address_detail']) ? $structure['address_detail'] : null,
                                    'placeholder' => 'Dettaglio indirizzo',
                                    'id' => 'input_address_detail',
                                    'class' => 'form-control input_address_detail',
                                ]) }}
                                <small class="form-text text-muted"> Compilare solo se l'indirizzo non è
                                    correttamente censito su Maps</small>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Riferimenti normativi --}}
                                <div class="form-group col-md-9" id="input_normatives">
                                    <label for="normatives">Riferimenti normativi</label>
                                    <div class="select2-blue">
                                        {{ form_dropdown(
                                            'normatives[]',
                                            '',
                                            '',
                                            'class="form-control select2-normatives" multiple="multiple" data-placeholder="Seleziona o cerca normativa..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                @if(empty($is_box))
                                    <div class="form-group col-md-3">
                                        {{ form_button([
                                                'name' => 'add',
                                                'id' => 'btn_viewOnMap',
                                                'class' => 'btn btn-outline-primary open-modal',
                                                'style' => 'width:100%;',
                                                'data-url' => siteUrl('admin/normative/create-box')
                                            ],'Aggiungi Nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        {{-- END: Form --}}
                    </div>

                    {{-- ***** BEGIN: include attach **** --}}
                    {% include layout/partials/attach %}
                    {{-- ***** END: include attach **** --}}
                </div>
            </div>

            {{-- Card Footer --}}
            <div class="card-footer" id="__save_">
                {{ btnSave() }}
            </div>

        </div>

        {{ form_input([
            'type' => 'hidden',
            'name' => '_storage_type',
            'value' => $_storageType,
            'id' => '_storage_type',
            'class' => '_storage_type',
        ]) }}

        @if(!empty($structure['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $structure['id'],
                'id' => 'structure_id',
                'class' => 'structure_id',
            ]) }}
        @endif

        @if(!empty($normativeIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_normativeIds',
                'value' => implode(',',$normativeIds),
                'id' => '_normativeIds',
                'class' => '_normativeIds',
            ]) }}
        @endif

        @if(!empty($institution_id))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'institution_id',
                'value' => $institution_id,
                'id' => 'institution_id',
                'class' => 'institution_id',
            ]) }}
        @endif

        {{ form_hidden('institute_id',PatOsInstituteId()) }}
        {{ form_close() }}
    </div>
</div>

{{-- Includo il modale per l'aggiunta di oggetti direttamente dal form  --}}
{% include layout/partials/form_modal %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('leaflet/leaflet.css', 'common') }}

<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('ckeditor4/ckeditor.js', 'common') }}
{{ js('leaflet/leaflet.js', 'common') }}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}
{{ js('admin/get/config.js?box='.$is_box) }}

<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {

        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();
        let map = L.map('map');
        setTimeout(function () {
            map.invalidateSize();
        }, 150)

        {{-- Campo CKEDITOR  Descrizione delle attività --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });

        CKEDITOR.replace('input_description');

        {{--Inizializzazione Mappa --}}
        let baseLayers = {
            'mappa': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            })
        }
        {{-- Fine inizializzazione Mappa --}}

        {{-- Begin dinamicizzazione campi form --}}
        {{-- In fase di modifica nascondo le note del responsabile non disponibile, in caso sia disponibile e viceversa --}}
        @if($_storageType== 'insert' || (!empty($structure['responsible_not_available']) && $structure['responsible_not_available'] == 1))
        document.getElementById("notAvaiableReferent").style.display = "none";
                @else
        for (let i = 0; i < document.getElementsByClassName('disponibleResp').length; i++) {
            document.getElementsByClassName('disponibleResp')[i].style.display = 'none';
        }
        @endif

        {{-- In fase di modifica nascondo le note della mail  non disponibile, in caso sia disponibile e viceversa --}}
        @if($_storageType == 'insert' || ( !empty($structure['email_not_available']) && $structure['email_not_available'] == 1))
        document.getElementById("showEmailNotAvaiable").style.display = "none";
        @else
        document.getElementById("showEmailNotAvaiable").style.display = "block";
        document.getElementById("showEmail").style.display = "none";
        @endif

        {{-- In fase di modifica nascondo la mappa se non è presente --}}
        @if(empty($structure['based_structure']) || $structure['based_structure'] === 0)
        let tmp = document.getElementsByClassName("showMaps");
        for (let i = 0; i < tmp.length; i++) {
            tmp[i].style.display = 'none';
        }
        @endif

        {{-- Se sono presenti latitudine e longitudine li setto sulla mappa --}}
        @if(!empty($structure['lat']) && !empty($structure['lon']))
        map.setView([document.getElementById('input_lat').value, document.getElementById('input_lon').value], 13);
        baseLayers['mappa'].addTo(map);
        marker = L.marker([document.getElementById('input_lat').value, document.getElementById('input_lon').value]).addTo(map);
        marker.bindPopup("<b>{{ $structure['structure_name'] }}</b></br>{{ $structure['address'] }}").openPopup();
        @else
        map.setView([41.8933203, 12.4829321], 13);
        baseLayers['mappa'].addTo(map);
        let marker = L.marker([41.8933203, 12.4829321]).addTo(map);
        let input = '';
        @endif

        {{-- Per responsabile non disponibile --}}
        $('#input_responsible_not_available').on('select2:select', function (e) {
            let data = e.params.data;
            if (data.text === 'No') {
                document.getElementById("notAvaiableReferent").style.display = "block";
                for (let i = 0; i < document.getElementsByClassName('disponibleResp').length; i++) {
                    document.getElementsByClassName('disponibleResp')[i].style.display = 'none';
                }
            } else {
                document.getElementById("notAvaiableReferent").style.display = "none";
                for (let i = 0; i < document.getElementsByClassName('disponibleResp').length; i++) {
                    document.getElementsByClassName('disponibleResp')[i].style.display = 'block';
                }
            }
        });

        {{-- Per indirizzo email non disponibile --}}
        $('#input_email_not_available').on('select2:select', function (e) {
            let data = e.params.data;
            if (data.text === 'No') {
                document.getElementById("showEmail").style.display = "none";
                document.getElementById("showEmailNotAvaiable").style.display = "block";
            } else {
                document.getElementById("showEmail").style.display = "block";
                document.getElementById("showEmailNotAvaiable").style.display = "none";
            }
        });

        {{-- Per maps in caso di struttura con sede --}}
        $('#input_based_structure').on('select2:select', function (e) {
            let data = e.params.data;
            let tmp = document.getElementsByClassName("showMaps");
            if (data.text === 'Si') {
                setTimeout(function () {
                    map.invalidateSize();
                }, 50)
                for (let i = 0; i < tmp.length; i++) {
                    tmp[i].style.display = 'block';
                }
            } else {
                for (let i = 0; i < tmp.length; i++) {
                    tmp[i].style.display = 'none';
                }
            }
        });

        {{-- Metodo per visualizzare l'indirizzo sulla Mappa --}}
        // Recupero l'indirizzo inserito e lo setto nella mappa
        document.getElementById('btn_viewOnMap').onclick = function () {
            input = document.getElementById('input_address').value;
            $.ajax({
                type: "GET",
                url: 'https://nominatim.openstreetmap.org/search?format=json&limit=3&q=' + input,
                success: function (data) {
                    map.removeLayer(marker);
                    marker = L.marker([data[0].lat, data[0].lon]).addTo(map);
                    map.setView([data[0].lat, data[0].lon], 11);
                    document.getElementById('input_lat').value = data[0].lat;
                    document.getElementById('input_lon').value = data[0].lon;
                }
            });
        };
        {{-- End dinamicizzazione campi form --}}

        // Tabella per la selezione dei Responsabili della struttura
        let responsibles = $('#ajax_responsibles').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Responsabili selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_responsibles',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#responsibles_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($structure['responsibles']))
        @foreach($structure['responsibles'] as $responsible)
        responsibles.patOsAjaxPagination.setValue('{{ $responsible['id'] }}', '{{ (!empty($responsible['title']) ? htmlEscape($responsible['title']).' - ':'').htmlEscape($responsible['full_name']).' - '.htmlEscape($responsible['name']).' - '.(!empty($responsible['email'])?$responsible['email']:'N.D') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione del personale da contattare per la struttura
        let toContact = $('#ajax_to_contacts').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Personale da contattare selezionato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_to_contacts',
            // 'setInputDataValueOnlyId' : false,
            dataParams: {
                model: 2,
                institution_id: institutionId,
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#to_contacts_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($structure['to_contact']))
        @foreach($structure['to_contact'] as $toContact)
        toContact.patOsAjaxPagination.setValue('{{ $toContact['id'] }}', '{{ (!empty($toContact['title']) ? htmlEscape($toContact['title']).' - ':'').htmlEscape($toContact['full_name']).' - '.htmlEscape($toContact['name']).' - '.(!empty($toContact['email'])?$toContact['email']:'N.D') }}', true);
        @endforeach
        @endif

        {{-- Inizio metodi per campi Select --}}
        {{-- Begin Select2 campo "Riferimenti normativi" --}}
        let $dropdownNormatives = $('.select2-normatives');
        $dropdownNormatives.select2({
            allowClear: true,
            // Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 12,
                        institution_id: institutionId,
                        searchTerm: data.term
                    };
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                },
                processResults: function (response) {
                    return {
                        results: response.data.options
                    };
                },
                cache: true
            }
        });
        // Recupero gli elementi gia selezionati e li setto nella select
        @if(in_array($_storageType,['update', 'duplicate']))
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_normativeIds').val(),
                model: 12,
                institution_id: institutionId,
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        }).then(function (data) {
            let item = data.data.selected;
            for (const el of item) {
                // Creo l'opzione e l'appendo alla select
                var option = new Option(String(el.text), el.id, true, true);
                $dropdownNormatives.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Riferimenti normativi" --}}

        // Tabella per la selezione della struttura di appartenenza
        let structureOfBelonging = $('#ajax_structure_of_belonging').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Struttura selezionata',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_structure_of_belonging_id',
            dataParams: {
                model: 1,
                institution_id: institutionId,
                exclude_id: <?php echo ($_storageType == 'insert') ? 0 : $structure['id'] ?>
            },
            columns: config.structure.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.structure.dataSource,
            addRecord: config.structure.addRecord,
            archived: config.structure.archived,
            label: '#structure_of_belonging_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($structure['structure_of_belonging_id']))
        structureOfBelonging.patOsAjaxPagination.setValue('{{ $structure['structure_of_belonging_id'] }}', '{{e: (!empty($structure['structure_of_belonging'])? $structure['structure_of_belonging']['structure_name']:'').(!empty($structure['structure_of_belonging']['reference_email'])?' - '.$structure['structure_of_belonging']['reference_email']:'N.D') }}', true);
        @endif

        {{-- Select2 campo  "Responsabile non disponibile" --}}
        let $dropdownResponsable = $('.select2-responsible_not_available');
        $dropdownResponsable.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Ad interim" --}}
        let $dropdownAdInterim = $('.select2-ad_interim');
        $dropdownAdInterim.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Indirizzo email non disponibile" --}}
        let $dropdownEmailNotAvailable = $('.select2-email_not_available');
        $dropdownEmailNotAvailable.select2({
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Utilizza in Articolazione degli Uffici" --}}
        let $dropdownArticulation = $('.select2-articulation');
        $dropdownArticulation.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Struttura con sede" --}}
        let $dropdownBasedStructure = $('.select2-based_structure');
        $dropdownBasedStructure.select2({
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Tipo di Mappa" --}}
        let $dropdownMapType = $('.select2-mapType');
        $dropdownMapType.select2({
            minimumResultsForSearch: -1
        });
        {{-- Fine metodi per campi Select --}}

        /**
         * Metodo per il salvataggio
         */
                {{-- Begin salvataggio --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            // Aggiorno il valore dei campi CKEDITOR prima che vengono recuperati per l'invio
            beforeSerialize: function ($Form, options) {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
                return true;
            },
            beforeSend: function () {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                let response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message, 'Struttura Organizzativa');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/structure') }}';
                }, 800);

                @else
                {{-- Controllo se sono all'interno di un modale lo chiudo dopo il salvataggio --}}
                setTimeout(function () {
                    window.parent.$('#formModal').modal('hide');
                }, 800);
                @endif

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                //Messaggi di errore del validatore campi e degli allegati
                let msg = [response.data.error_partial_attach, response.errors.error].filter(Boolean).join(", ");

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(msg, 6000);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);

            }
        });
        {{-- End salvataggio --}}

        /**
         * Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form
         */
        {{-- Vedere nel footer --}}
        openModalForm();

        /**
         * Funzione che viene eseguita alla chiusura del modale di aggiunta di nuovi elementi direttamente dal form.
         * Pulisce l'iframe.
         */
        {{-- Vedere nel footer --}}
        closeModalForm();

        /**
         * Controllo per l'uscita dal form se i campi di input sono stati toccati
         */
        $(document).on('focus',
            '.select2-selection.select2-selection--single, .select2-selection.select2-selection--multiple, input',
            function (e) {
                formModified = true;
            });

        @if(!$is_box)
        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });
        @endif

        {{-- Vedere nel footer --}}
        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        checkIfRestore();

    });
</script>
{% endblock %}