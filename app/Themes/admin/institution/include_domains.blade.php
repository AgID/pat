<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">

    let mode = '{{xss: !empty($mode) ? $mode : 'insert' }}';
    let increment = {{ (int) !empty($increment) ? $increment+1 : 1 }};

    {{-- Funzione che continene il template html per la generazione della lista LI --}}
    function tpl(removeTag = false) {
        let template = '';

        if (removeTag === false) {
            template += '<li id="inc_<{data_id}>" class="list-group-item d-flex justify-content-between align-items-center layer-domain">';
        }

        template += '<span>Dominio &nbsp; <strong>"<{host_domain}>"</strong> &nbsp; associato all\'applicativo: <strong>"<{app}>"</strong></span>.';

        template += '<span>';
        template += '<a href="#!" onclick="onEditRecord(this)">';
        template += '<i class="fas fa-edit"></i>';
        template += '</a>';
        template += ' &nbsp;&nbsp; ';
        template += '<a href="#!" onclick="onDeleteRecord(this)">';
        template += '<i class="fas fa-trash text-danger"></i>';
        template += '</a>';
        template += '</span>';
        template += '<input type="hidden" name="host_domain_name[]" class="host_domain_name" value="<{host_domain}>">';
        template += '<input type="hidden" name="host_domain_app_id[]" class="host_domain_app_id" value="<{app_id}>">';
        template += '<input type="hidden" name="host_domain_mode[]" class="host_domain_app_id" value="' + mode + '">';
        template += '<input type="hidden" name="host_domain_id[]" class="host_domain_id" value="<{record_id}>">';

        if (removeTag === false) {
            template += '</li>';
        }

        return template;
    }


    function onDeleteRecord() {

        let element = '#' + $(arguments[0]).parent().parent().attr('id');
        let hostDomainName = $(element).find("input.host_domain_name").val();
        let hostDomainAppId = $(element).find("input.host_domain_app_id").val();
        $(arguments[0]).parent().parent().remove();

        let result = confirm("Sei sicuro di voler eliminare questo elemento?");
        if (result == true) {

            if ($("#___list_domain").find("li").length === 0) {
                $('#no_domains').show();
            }

            if (mode === 'edit') {
                let data = $.param({
                    domain: hostDomainName,
                    app_id: hostDomainAppId,
                });

                $.get('{{ siteUrl('admin/institutions/delete/domain') }}?' + data);
            }
        }
    }

    {{-- Apre la finestra modale per modificare un record  --}}
    function onEditRecord() {
        let element = '#' + $(arguments[0]).parent().parent().attr('id');
        let hostDomainName = $(element).find("input.host_domain_name").val();
        let hostDomainAppId = $(element).find("input.host_domain_app_id").val();
        let recordId = $(element).find("input.host_domain_id").val();

        /* Setto l'identificativo della modalità di operazione da compiere in fase di storage */
        $('#add_domain').empty().text('Modifica');
        $('#manage_mode').val('edit');
        $('#manage_origin').val($(arguments[0]).parent().parent().attr('id'));

        /* Appendo i valori che si trovano all'interno del tag li */
        $('#manage_domain').val(hostDomainName);
        $("#manage_app option[value='" + hostDomainAppId + "']").prop("selected", true);
        $("#record_id ").val(recordId);

        $('#modal_set_domain').modal('show');
    }

    {{-- Template parser --}}
    function buildAndAppendTemplate(data, selector) {
        let $element = $(selector);
        /*let html = template.replace(/<{(.*?)}>/g, function (match, key) {
            return data[key];
        });*/
        let html = templateParse(data, false);
        $element.append(html);
    }

    function templateParse(data, removeTag = false) {

        let template = tpl(removeTag);

        return template.replace(/<{(.*?)}>/g, function (match, key) {
            return data[key];
        });
    }


    $(document).ready(function () {

        @if(empty($showListDomains))
        $('#list_domain_apps').hide();
        @else
        $('#no_domains').hide();
        @endif

        {{-- Pulsate che apre la finestra modale per aggiungere un dominio --}}
        $('#modal_domain, #modal_domain_center').bind('click', function (e) {
            e.preventDefault();
            $('#add_domain').empty().text('Aggiungi');
            $('#manage_mode').val('insert');
            $('#record_id').val('');
            $('#manage_domain').val('');
            $('#manage_origin').val('');
        })

        {{-- Pulsante per aggiungere o modificare un dominio nella --}}
        $('#add_domain').bind('click', function (e) {
            e.preventDefault();

            let domainHost = $('#manage_domain');
            let appOptionValue = $('#manage_app');
            let appOptionText = $('#manage_app option:selected');
            let passed = true;
            let regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
            let recordId = $('#manage_domain').val();

            if (!regex.test(domainHost.val())) {
                passed = false;
                alert('Attenzione, il valore inserito nel campo "Nome dominio" non è una url valida. Assicurati di avere inserito l\'identificativo "https:// oppure http://"');
            }

            if (!/^\d+$/.test(appOptionValue.val().toString())) {
                passed = false;
                alert('Attenzione, il valore selezionato nel campo applicativo non è valido');
            }

            let dataObject = {};

            dataObject['host_domain'] = domainHost.val();
            dataObject['app'] = appOptionText.text();
            dataObject['app_id'] = appOptionValue.val();
            dataObject['data_id'] = increment;

            /* Verifico se il è valorizzato */
            if ($('#record_id').val() && $('#manage_mode').val() === 'edit') {
                dataObject['record_id'] = $('#record_id').val();
                dataObject['domain_mode'] = $('#manage_mode').val();
                dataObject['update'] = true;
            } else {

                if (mode === 'edit') {
                    dataObject['domain_mode'] = 'edit';
                }
            }
            @if(!empty($mode) && !empty($institution['id']))
                dataObject['institute_id'] = {{ $institution['id'] }};
            @endif

            if (passed) {

                $.ajax({
                    type: 'POST',
                    url: '{{ siteUrl('admin/institutions/exists/domain') }}',
                    dataType: "JSON",
                    data: dataObject,
                    beforeSend: () => {
                        showFullModalSpinner()
                    },

                    success: (response) => {

                        $('#modal_set_domain').modal('hide');

                        if (response.data.found === 'N') {
                            setTimeout(() => {
                                $(document).Toasts('create', {
                                    class: 'bg-danger',
                                    title: 'ATTENZIONE',
                                    autohide: true,
                                    delay: 5000,
                                    body: 'Il dominio che provi a modificare già esiste nell\'applicativo'
                                })
                            }, 200);
                        } else {

                            {{-- Modalità: inserisci un nuovo tag li --}}
                            if ($('#manage_mode').val() === 'insert') {
                                if (mode === 'edit') {
                                    console.log(response.data.last_record.domain);
                                    dataObject['host_domain'] = response.data.last_record.domain;
                                    dataObject['app'] = response.data.last_record.name;
                                    dataObject['app_id'] = response.data.last_record.app_id;
                                    dataObject['data_id'] = increment;
                                    dataObject['record_id'] = response.data.last_record.id;
                                    dataObject['domain_mode'] = 'edit';
                                    dataObject['update'] = true;
                                    dataObject['institute_id'] = response.data.last_record.institution_id;
                                    buildAndAppendTemplate(dataObject, '#___list_domain');
                                } else {
                                    buildAndAppendTemplate(dataObject, '#___list_domain');
                                }
                            }

                            {{-- Modalità: modifica record un nuovo tag li --}}
                            if ($('#manage_mode').val() === 'edit') {

                                let html = templateParse(dataObject, true);
                                $('#' + $('#manage_origin').val()).empty().html(html);
                                /*console.log(dataObject);
                                let html = templateParse(dataObject);
                                $element.append(html);*/
                                /*if (dataObject.hasOwnProperty('record_id')) {

                                    alert('Update con ID')

                                } else {

                                    alert('Update senza ID')

                                }*/
                            }

                            increment++;

                        }

                    },

                    complete: () => {
                        hideFullModalSpinner();
                        // $('#list_domain_apps').show();
                    },

                    error: (jqXHR, status) => {
                    }
                });
            }

        })

    })
</script>

<div class="modal fade bd-example-modal-lg" id="modal_set_domain" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Gestione dominio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="form-group">
                        <label for="manage_domain" class="col-form-label">Nome dominio</label>
                        {{ form_input([
                            'name' => 'manage_domain',
                            'value' => 'https://www.',
                            'placeholder' => 'https://www.',
                            'id' => 'manage_domain',
                            'class' => 'form-control manage_domain',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="manage_app" class="col-form-label">Applicativo:</label>
                        {{ form_dropdown(
                            'manage_app',
                            !empty($appList) ? $appList : [],
                            null,
                            'class="form-control manage_app" id="manage_app"'
                        ) }}
                    </div>

                    {{ form_input([
                     'type' => 'hidden',
                     'name' => 'manage_mode',
                     'value' => 'insert',
                     'id' => 'manage_mode',
                     'class' => 'manage_mode',
                     ]) }}

                    {{ form_input([
                     'type' => 'hidden',
                     'name' => 'record_id',
                     'value' => null,
                     'id' => 'record_id',
                     'class' => 'record_id',
                     ]) }}

                    {{ form_input([
                     'type' => 'hidden',
                     'name' => 'manage_origin',
                     'value' => null,
                     'id' => 'manage_origin',
                     'class' => 'manage_origin',
                     ]) }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="add_domain">Aggiungi</button>
            </div>
        </div>
    </div>
</div>


<div class="row mt-2 mb-3">
    <div class="col-md-12">
        <a href="#!" class="btn btn-outline-primary" id="modal_domain" data-toggle="modal"
                data-target=".bd-example-modal-lg">
            <i class="fas fa-plus"></i> Aggiungi dominio e tipo di applicativo
        </a>
    </div>
    <div class="col-md-12">
        <hr>
    </div>
</div>

<div class="row" id="no_domains">
    <div class="col-md-12 text-center">
        <h4 class="text-center text-muted"> Al momento non ci sono domini associati.</h4>
        <button type="button" class="btn btn-outline-primary" id="modal_domain_center" data-toggle="modal"
                data-target=".bd-example-modal-lg">
            <i class="fas fa-plus"></i> Associa un dominio ora
        </button>
    </div>
</div>


<div class="row" id="list_domain_apps">
    <div class="col-md-12">
        <ul class="list-group" id="___list_domain">
            @if(!empty($appInstalled))
                @php
                    $i=1
                @endphp
                @foreach($appInstalled AS $app)

                    <li id="inc_{{ $i }}"
                        class="list-group-item d-flex justify-content-between align-items-center layer-domain">
                        <span>
                        Dominio &nbsp; <strong>"{{xss: $app['domain'] }}"</strong> &nbsp; associata all'applicativo: <strong>"{{e: $app['name'] }}"</strong>
                        </span>
                        <span>
                        <a href="#!" onclick="onEditRecord(this)">
                        <i class="fas fa-edit"></i>
                        </a>
                         &nbsp;&nbsp;
                        <a href="#!" onclick="onDeleteRecord(this)">
                        <i class="fas fa-trash text-danger"></i>
                        </a>
                        </span>
                        <input type="hidden" name="host_domain_name[]" class="host_domain_name" data-id="{{ $i }}"
                               value="{{xss: $app['domain'] }}">
                        <input type="hidden" name="host_domain_app_id[]" class="host_domain_app_id" data-id="{{ $i }}"
                               value="{{e: $app['app_id'] }}">
                        <input type="hidden" name="host_domain_mode[]" class="host_domain_mode" data-id="{{ $i }}"
                               value="edit'">
                        <input type="hidden" name="host_domain_id[]" class="host_domain_id" data-id="{{ $i }}"
                               value="{{e: $app['id'] }}">
                    </li>
                    @php
                        $i++
                    @endphp
                @endforeach
            @endif
        </ul>
    </div>
</div>

