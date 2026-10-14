{{--  Form store Personale --}}
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
                        Aggiunta Personale
                    @elseif($_storageType === 'update')
                        Modifica Personale
                    @else
                        Duplicazione Personale
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/personnel') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco personale
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </h4>

            <div class="card-body card-primary">


                {{-- Inclusione di librerie esterne --}}

                <div class="row">
                    @if(!empty($personnel['photo']))
                        <div class="col-md-12 text-center mb-3" id="personnel_photo">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" style="width: 60px; height: 60px; object-fit:cover"
                                     src="{{ baseUrl('media/' . instituteDir() . '/assets/images/' .$personnel['photo']) }}"
                                     alt="{{ $personnel['full_name'] ?? getFullName($personnel['lastname'],$personnel['firstname'])}}">
                            </div>
                            @if(!empty($personnel['id']))
                                <div class="mt-1">
                                    <small class="text-muted">
                                        Personale creato in data
                                        <strong>{{ date("d-m-Y", strtotime($personnel['created_at']))}}</strong>
                                        | <a href="#!" class="text-danger" id="__remove_photo"><strong>Rimuovi
                                                foto</strong></a>
                                    </small>
                                </div>
                            @endif
                            <hr class="mb-3"/>
                        </div>
                    @endif

                    <div class="text-muted col-md-9 mb-4">
                        <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} I Campi contrassegnati dal
                        simbolo asterisco (*) sono obbligatori.
                    </div>


                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div>


                            <div class="form-row d-flex align-items-end">
                            </div>
                            <hr class="patos-hr">


                            <div class="form-row d-flex align-items-end">
                                {{-- Inclusione di librerie esterne --}}
                            </div>


                            <div class="form-row d-flex align-items-start">

                                {{-- Campo Nome --}}
                                <div class="form-group col-md-4">
                                    <label for="firstname">Nome *</label>
                                    {{ form_input([
                                    'name' => 'firstname',
                                    'value' => !empty($personnel['firstname']) ? $personnel['firstname'] : $personnel['full_name'] ?? null,
                                    'placeholder' => 'Nome',
                                    'id' => 'input_firstname',
                                    'class' => 'form-control input_firstname'
                                ]) }}

                                    <small class="form-text text-muted" style="display: none;" id="alert_name">
                                        Dato importato, nella nuova versione nome e cognome sono gestiti separatamente!
                                    </small>
                                </div>

                                {{-- Campo Cognome --}}
                                <div class="form-group col-md-4">
                                    <label for="lastname">Cognome *</label>
                                    {{ form_input([
                                        'name' => 'lastname',
                                        'value' => !empty($personnel['lastname']) ? $personnel['lastname'] : null,
                                        'placeholder' => 'Cognome',
                                        'id' => 'input_lastname',
                                        'class' => 'form-control input_lastname'
                                    ]) }}
                                </div>

                                <div class="form-group col-md-4" id="input_title">
                                    <label for="title">Titolo accademico o professionale</label>
                                    {{ form_dropdown(
                                        'title',
                                        ['' => '','arch.' => 'arch.','avv.' => 'avv.','dott.' => 'dott.','dott.ssa' => 'dott.ssa',
                                            'dr.' => 'dr.', 'ing.' => 'ing.', 'on.le' => 'on.le', 'geom.' => 'geom.', 'prof.' => 'prof.',
                                            'prof.ssa' => 'prof.ssa','rag.' => 'rag.','sig.' => 'sig.','sig.ra' => 'sig.ra','per.' => 'per.', 'dott.ing.' => 'dott.ing.'],
                                        @$personnel['title'],
                                        'class="ts-title" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Ruolo --}}
                                <div class="form-group col-md-6" id="input_role">
                                    <label for="role">Ruolo *</label>
                                    {{ form_dropdown(
                                        'role_id',
                                        @$roles,
                                        @$personnel['role_id'],
                                        'class="ts-role" style="width: 100%;" id="role_id"'
                                    ) }}
                                </div>


                                    {{-- Campo Pubblica in --}}
                                    <div class="form-group col-md-6" id="input_contact_personnel">
                                        <label for="contact_personnel">Pubblica in</label>
                                        {{ form_dropdown(
                                            'public_in[]',
                                            @$publicIn,
                                            @$publicInIDs,
                                            'class="ts-public_in" multiple="multiple" style="width: 100%;"'
                                        ) }}
                                    </div>

                            </div>

                            <div class="form-row d-flex align-items-end">

                                <div class="form-group col-md-6">
                                    <label for="organs">Organo politico, di amministrazione o gestione</label>
                                    {{ form_dropdown(
                                        'organs[]',
                                        @$politicalAdministrative,
                                        @$organIds,
                                        'class="ts-organs" multiple="multiple" style="width: 100%;"'
                                    ) }}
                                </div>

                                {{-- Campo Titolo accademico o professionale --}}
                                @if(!empty($qualifications))
                                    <div class="form-group col-md-6" id="input_qualification_id">
                                        <label for="qualification_id">Qualifica</label>
                                        {{ form_dropdown(
                                            'qualification_id',
                                            ['' => ''] + $qualifications,
                                            @$personnel['qualification_id'],
                                            'class="ts-qualification" style="width: 100%;"'
                                        ) }}
                                    </div>
                                @endif
                            </div>


                            <div class="form-row d-flex align-items-start">
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Estremi atto di nomina o proclamazione --}}
                                <label for="extremes_of_conference">Estremi atto di nomina o proclamazione</label>
                                {{form_editor([
                                    'name' => 'extremes_of_conference',
                                    'value' => !empty($personnel['extremes_of_conference']) ? $personnel['extremes_of_conference'] : null,
                                    'id' => 'input_extremes_of_conference',
                                    'class' => 'form-control input_extremes_of_conference'
                                ]) }}
                            </div>

                            {{-- Campo Organo politico-amministrativo --}}
                            <div class="form-row d-flex align-items-end">

                                {{-- Campo Incarico di stampo politico --}}
                                <div class="form-group col-md-6 politicalAssignmentShow">
                                    <label for="political_role">Incarico di stampo politico *</label>
                                    {{ form_input([
                                        'name' => 'political_role',
                                        'value' => !empty($personnel['political_role']) ? $personnel['political_role'] : null,
                                        'placeholder' => 'Incarico di stampo politico',
                                        'id' => 'input_political_role',
                                        'class' => 'form-control input_political_role'
                                    ]) }}
                                </div>
                            </div>


                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Utilizza negli elenchi del personale --}}
                                    <div class="form-group col-md-6" id="input_personnel_lists">
                                        <label for="personnel_lists">Utilizza negli elenchi del personale</label>
                                        <span> ( Si consiglia di selezionare "No" per personale di tipo politico )</span>
                                        {{ form_dropdown(
                                            'personnel_lists',
                                            [1=>'Si',0=>'No'],
                                            @$personnel['personnel_lists'],
                                            'class="ts-personnel_lists" style="width: 100%;"'
                                        ) }}
                                    </div>

                                    {{-- Campo Ordine di visualizzazione --}}
                                    <div class="form-group col-md-6">
                                        <label for="priority">Ordine di visualizzazione *</label>
                                        {{ form_input([
                                            'name' => 'priority',
                                            'type' => 'number',
                                            'value' => !empty($personnel['priority']) ? $personnel['priority'] : 1,
                                            'placeholder' => 'Ordine',
                                            'id' => 'input_priority',
                                            'class' => 'form-control input_priority'
                                        ]) }}
                                    </div>
                                </div>


                            {{generateSeparator('Strutture collegate ed incarichi')}}

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="structures">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structures" id="_structures_label">Referente per le
                                            strutture </label>
                                    </div>
                                    <div id="ajax_structures"></div>
                                    <input type="hidden" value="" name="structures"
                                           id="input_structure"
                                           class="structures">
                                </div>
                            </div>


                                {{-- Campo Incarichi associati --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_assignments" id="_assignments_label">Incarichi
                                                associati</label>
                                        </div>
                                        <div id="ajax_assignments"></div>
                                        <input type="hidden" value="" name="assignments" id="input_assignments"
                                               class="assignments">
                                    </div>
                                </div>

                                {{-- Campo Provvedimenti associati --}}
                                <!--<div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <label for="ajax_measures">Provvedimenti associati</label>
                                    <div id="ajax_measures"></div>
                                    <input type="hidden" value="" name="measures" id="input_measures"
                                           class="measures">
                                </div>
                            </div>-->


                            {{generateSeparator('Informazioni e recapiti')}}

                            {{-- Campo Foto allegata --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-6">
                                    <label for="photo_file">Foto allegata</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="photo_file"
                                               name="photo" accept="image/png, image/gif, image/jpeg"
                                               aria-describedby="photo_file_help">
                                        <label class="custom-file-label" for="photo_file"
                                               id="label_attach_logo">
                                            Foto allegata
                                        </label>
                                    </div>
                                    <small id="photo_file_help" class="form-text text-muted">
                                        Formati ammessi: PNG, GIF, JPG/JPEG. Dimensione massima:
                                        {{ \Helpers\Utility\FileUpload::maxSizeLabel(1024 * 1024) }};
                                        risoluzione minima 50 × 50 pixel.
                                    </small>
                                    <div id="preview-url-logo">
                                        <div class="mt-2">
                                            <img src="" alt="Immagine da visualizzare" id="src-logo-ente"
                                                 class="img-thumbnail attach-image">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-xs btn-outline-danger"
                                                        id="clear-preview-logo">
                                                    <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Campo Contratto a tempo determinato --}}
                                <div class="form-group col-md-6" id="determined_term">
                                    <label for="determined_term">Contratto a tempo determinato</label>
                                    <div id="input_determined_term">
                                        {{ form_dropdown(
                                            'determined_term',
                                            ['' => '',1 => 'Si',0 => 'No'],
                                            @$personnel['determined_term'],
                                            'class="ts-determined_term" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Recapito telefonico fisso --}}
                                <div class="form-group col-md-6">
                                    <label for="phone">Recapito telefonico fisso</label>
                                    {{ form_input([
                                        'name' => 'phone',
                                        'value' => !empty($personnel['phone']) ? $personnel['phone'] : null,
                                        'placeholder' => 'Telefono',
                                        'id' => 'input_phone',
                                        'class' => 'form-control input_phone'
                                    ]) }}
                                </div>

                                <div class="form-group col-md-6">
                                    {{-- Campo Recapito telefonico mobile --}}
                                    <label for="mobile_phone"> Recapito telefonico mobile </label>
                                    {{ form_input([
                                        'name' => 'mobile_phone',
                                        'value' => !empty($personnel['mobile_phone']) ? $personnel['mobile_phone'] : null,
                                        'placeholder' => 'Cellulare',
                                        'id' => 'input_mobile_phone',
                                        'class' => 'form-control input_mobile_phone'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Recapito fax --}}
                                <div class="form-group col-md-6">
                                    <label for="fax">Recapito fax</label>
                                    {{ form_input([
                                        'name' => 'fax',
                                        'value' => !empty($personnel['fax']) ? $personnel['fax'] : null,
                                        'placeholder' => 'Fax',
                                        'id' => 'input_fax',
                                        'class' => 'form-control input_fax'
                                    ]) }}
                                </div>

                                {{-- Campo Indirizzo email certificata --}}
                                <div class="form-group col-md-6">
                                    <label for="certified_email">Indirizzo email certificata</label>
                                    {{ form_input([
                                        'name' => 'certified_email',
                                        'value' => !empty($personnel['certified_email']) ? $personnel['certified_email'] : null,
                                        'placeholder' => 'Indirizzo email certificata',
                                        'id' => 'input_certified_email',
                                        'class' => 'form-control input_certified_email'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Indirizzo email non disponibile --}}
                                <div class="form-group col-md-6">
                                    <label for="not_available_email">Indirizzo email disponibile</label>
                                    <div id="input_not_available_email">
                                        {{ form_dropdown(
                                            'not_available_email',
                                            [1=>'Si', 0=>'No'],
                                            @$personnel['not_available_email'],
                                            'class="ts-email_not_available" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Indirizzo email --}}
                                <div class="form-group col-md-6" id="showEmail">
                                    <label for="email"> Indirizzo email * </label>
                                    {{ form_input([
                                        'name' => 'email',
                                        'value' => !empty($personnel['email']) ? $personnel['email'] : null,
                                        'placeholder' => 'Indirizzo email',
                                        'id' => 'input_email',
                                        'class' => 'form-control input_email'
                                    ]) }}
                                </div>

                                {{-- Campo Note email non disponibile --}}
                                <div class="form-group col-md-6" id="showEmailNotAvaiable">
                                    {{-- Campo Note email non disponibile --}}
                                    <label for="not_available_email_txt">Note email non disponibile *</label>
                                    {{ form_input([
                                        'name' => 'not_available_email_txt',
                                        'value' => !empty($personnel['not_available_email_txt']) ? $personnel['not_available_email_txt'] : null,
                                        'placeholder' => 'Email non disponibile',
                                        'id' => 'input_not_available_email_txt',
                                        'class' => 'form-control input_not_available_email_txt'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- In carica dal --}}
                                <div class="form-group col-md-6">
                                    <label for="in_office_since">In carica dal</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="in_office_since"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$in_office_since }}"
                                               id="in_office_since">
                                    </div>
                                </div>

                                {{-- Campo In carica fino al --}}
                                <div class="form-group col-md-6">
                                    <label for="in_office_until">In carica fino al</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="in_office_until"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$in_office_until }}"
                                               id="in_office_until">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo CIn aspettativa --}}
                                <div class="form-group col-md-6" id="on_leave">
                                    <label for="on_leave">In aspettativa</label>
                                    <div id="input_on_leave">
                                        {{ form_dropdown(
                                            'on_leave',
                                            ['' => '',1 => 'Si',0 => 'No'],
                                            @$personnel['on_leave'],
                                            'class="ts-on_leave" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Compensi connessi all'assunzione della carica --}}
                                <label for="compensations">Compensi connessi all'assunzione della carica</label>
                                {{form_editor([
                                    'name' => 'compensations',
                                    'value' => !empty($personnel['compensations']) ? $personnel['compensations'] : null,
                                    'id' => 'input_compensations',
                                    'class' => 'form-control input_compensations'
                                ]) }}
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Importi di viaggi di servizio e missioni --}}
                                <label for="trips_import">Importi di viaggi di servizio e missioni</label>
                                {{form_editor([
                                    'name' => 'trips_import',
                                    'value' => !empty($personnel['trips_import']) ? $personnel['trips_import'] : null,
                                    'id' => 'input_trips_import',
                                    'class' => 'form-control input_trips_import'
                                ]) }}
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Dati relativi all'assunzione di altre cariche, presso enti pubblici o privati ed i relativi compensi a qualsiasi titolo corrisposti (art. 14 c.1 lett. d), D.lgs. n. 33/2013)--}}
                                <label for="other_assignments_institutions">Dati relativi all'assunzione di altre
                                    cariche, presso enti pubblici o privati ed i relativi compensi a qualsiasi titolo
                                    corrisposti (art. 14 c.1 lett. d), D.lgs. n. 33/2013)
                                </label>
                                {{form_editor([
                                    'name' => 'other_assignments_institutions',
                                    'value' => !empty($personnel['other_assignments_institutions']) ? $personnel['other_assignments_institutions'] : null,
                                    'id' => 'input_other_assignments_institutions',
                                    'class' => 'form-control input_other_assignments_institutions'
                                ]) }}
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Altri incarichi con oneri a carico della finanza pubblica e relativi compensi --}}
                                <label for="other_assignments"> Altri incarichi con oneri a carico della finanza
                                    pubblica
                                    e relativi compensi </label>
                                {{form_editor([
                                    'name' => 'other_assignments',
                                    'value' => !empty($personnel['other_assignments']) ? $personnel['other_assignments'] : null,
                                    'id' => 'input_other_assignments',
                                    'class' => 'form-control input_other_assignments'
                                ]) }}
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Documentazione Art. 14 e Art. 47, c. 1, Dlgs n. 33/2013; Art. 1,2,3,4 l. n. 441/1982 --}}
                                @php
                                    $label = " Documentazione Art. 14 e Art. 47, c. 1, Dlgs n. 33/2013; Art.1,2,3,4l. n. 441/1982 ";
                                    $label = $label ?? null;
                                @endphp

                                <label for="notes"> {{ $label }}</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($personnel['notes']) ? $personnel['notes'] : null,
                                    'id' => 'input_notes',
                                    'class' => 'form-control input_notes'
                                ]) }}
                            </div>

                            <div class="form-group poShow politicalAssignmentShow">
                                {{-- Campo Emolumenti complessivi percepiti a carico della finanza pubblica ((art. 14 c.1ter), D.lgs. n. 33/2013) --}}
                                <label for="total_emoluments"> Emolumenti complessivi percepiti a carico della finanza
                                    pubblica ((art. 14 c.1ter), D.lgs. n. 33/2013) </label>
                                {{form_editor([
                                    'name' => 'total_emoluments',
                                    'value' => !empty($personnel['total_emoluments']) ? $personnel['total_emoluments'] : null,
                                    'id' => 'total_emoluments',
                                    'class' => 'form-control total_emoluments'
                                ]) }}
                            </div>


                                {{generateSeparator('Altre informazioni')}}

                                {{-- Campo Storico Incarichi --}}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="historical">Storico incarichi</label>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Dati sullo storico</h5>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal"
                                                    data-target="#historical-modal" data-storage-type="new"
                                                    id="new-data">
                                                Crea nuovo record
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-12 mt-3 table-responsive">
                                            <table class="table table-hover table-bordered table-striped table-sm"
                                                   id="table-historical">
                                                <thead class="thead-dark">
                                                <tr>
                                                    <th style="width:8%;" class="text-center">Ruolo</th>
                                                    <th style="width:10%;" class="text-center">Struttura</th>
                                                    <th style="width:10%;" class="text-center">Dal</th>
                                                    <th style="width:8%;" class="text-center">Al</th>
                                                    <th style="width:5%;" class="text-center">Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody id="historical-table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {{-- Campo Altre informazioni --}}
                                    <label for="other_info">Altre informazioni</label>
                                    {{form_editor([
                                        'name' => 'other_info',
                                        'value' => !empty($personnel['other_info']) ? $personnel['other_info'] : null,
                                        'id' => 'input_other_info',
                                        'class' => 'form-control input_other_info'
                                    ]) }}
                                </div>

                                <div class="form-group">
                                    {{-- Campo Archivio informazioni --}}
                                    <label for="information_archive">Archivio informazioni</label>
                                    {{form_editor([
                                        'name' => 'information_archive',
                                        'value' => !empty($personnel['information_archive']) ? $personnel['information_archive'] : null,
                                        'id' => 'input_information_archive',
                                        'class' => 'form-control input_information_archive'
                                    ]) }}
                                </div>

                        </div>

                        {{-- Inclusione di librerie esterne --}}

                        {{-- END: Form --}}
                    </div>


                        {{-- ***** BEGIN: include attach**** --}}
                        {% include layout/partials/attach %}
                        {{-- ***** END: include attach**** --}}



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

        {{ form_input([
            'type' => 'hidden',
            'name' => 'test',
            'value' => $institution_type_id,
            'id' => 'test',
            'class' => 'test',
        ]) }}

        @if(!empty($personnel['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $personnel['id'],
                'id' => 'personnel_id',
                'class' => 'personnel_id',
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

        {{ form_input([
            'type' => 'hidden',
            'name' => '_historical',
            'value' => null,
            'id' => '_historical',
            'class' => '_historical',
        ]) }}

        {{ form_hidden('institute_id',checkAlternativeInstitutionId()) }}
        {{ form_close() }}
    </div>
</div>

{% include layout/partials/form_modal %}
{% include personnel/historical_assignments %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 200px;
    }

</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('ckeditor4/ckeditor.js', 'common') }}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}
{{ js(verifyFirstOrLastSlash('admin/get/config.js?box='.$is_box)) }}

<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    $(document).ready(function () {

        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

        let formModified = false;

        let institutionId = document.getElementById('institution_id').value;

        @if(empty($personnel['firstname']) && $_storageType == 'update')
        document.getElementById('alert_name').style.display = '';
        @endif

        document.getElementById('preview-url-logo').style.display = 'none';

        for (let i = 0; i < document.getElementsByClassName("poShow").length; i++) {
            document.getElementsByClassName("poShow")[i].style.display = 'none';
        }
        for (let i = 0; i < document.getElementsByClassName("politicalAssignmentShow").length; i++) {
            document.getElementsByClassName("politicalAssignmentShow")[i].style.display = 'none';
        }

        let roleId = document.getElementById('role_id').value;

        let politicalRoles = <?php echo json_encode(@$political); ?>;// non mettere le virgolette
        let po_dir_fields = <?php echo json_encode(@$po_dir_fields); ?>;// non mettere le virgolette

        if (Object.keys(po_dir_fields).includes(roleId)) {
            for (let i = 0; i < document.getElementsByClassName("poShow").length; i++) {
                document.getElementsByClassName("poShow")[i].style.display = 'block';
            }
        } else if (Object.keys(politicalRoles).includes(roleId)) {
            for (let i = 0; i < document.getElementsByClassName("politicalAssignmentShow").length; i++) {
                document.getElementsByClassName('politicalAssignmentShow')[i].style.display = 'block';
            }
            document.getElementById("determined_term").style.display = 'none';
        }

        {{-- Begin Campi Select--}}
        new TomSelect('.ts-role', TS_SINGLE_CLEAR);

        if (document.querySelector('.ts-public_in')) {
            new TomSelect('.ts-public_in', TS_MULTI_NO_SEARCH);
        }

        new TomSelect('.ts-determined_term', TS_SINGLE_CLEAR);
        new TomSelect('.ts-on_leave', TS_SINGLE_CLEAR);

        if (document.querySelector('.ts-personnel_lists')) {
            new TomSelect('.ts-personnel_lists', TS_SINGLE_PRESELECT);
        }

        new TomSelect('.ts-organs', TS_MULTI_NO_SEARCH);

        new TomSelect('.ts-email_not_available', TS_SINGLE_PRESELECT);
        new TomSelect('.ts-title', TS_SINGLE_CLEAR);

        if (document.querySelector('.ts-qualification')) {
            new TomSelect('.ts-qualification', TS_SINGLE_CLEAR);
        }
        {{-- End Campi Select--}}

        // Tabella per la selezione della struttura di appartenenza
        let structures = $('#ajax_structures').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Strutture selezionate',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            institution_id: institutionId,
            selectedIDs: '<?php echo !empty($personnel['referent_structures']) ? implode(",", array_column($personnel['referent_structures'], 'id')) : ''; ?>',
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_structure',
            dataParams: {
                model: 1,
                institution_id: institutionId
            },
            columns: config.structure.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.structure.dataSource,
            addRecord: config.structure.addRecord,
            archived: config.structure.archived,
            label: '#_structures_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($personnel['referent_structures']))
        @foreach($personnel['referent_structures'] as $structure)
        structures.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']).(!empty($structure['parent_name'])?' - '.htmlEscape($structure['parent_name']):'').(!empty($structure['reference_email'])?' - '.$structure['reference_email']:'') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli Incarichi associati al personale
        let assignments = $('#ajax_assignments').patOsAjaxPagination({
            url: config.assignment.url,
            textLoad: config.assignment.textLoad,
            selectedLabel: 'Incarichi selezionati',
            footerTable: config.assignment.footerTable,
            classTable: config.assignment.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.assignment.hideTable,
            showTable: config.assignment.showTable,
            search_placeholder: config.assignment.search_placeholder,
            setInputDataValue: '#input_assignments',
            dataParams: {
                model: 36,
                institution_id: institutionId,
            },
            dateFormat: config.assignment.dateFormat,
            columns: config.assignment.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.assignment.dataSource,
            addRecord: config.assignment.addRecord,
            label: '#_assignments_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($personnel['assignments']))
        @foreach($personnel['assignments'] as $assignment)
        @php
            $tmpStartDate = !empty($assignment['assignment_start']) ? ' - '.date('d/m/Y', strtotime($assignment['assignment_start'])) : null;
        @endphp
        assignments.patOsAjaxPagination.setValue('{{ $assignment['id'] }}', '{{ htmlEscape($assignment['name']).' - '.htmlEscape($assignment['object']).''.$tmpStartDate }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli Incarichi associati al personale
        let measures = $('#ajax_measures').patOsAjaxPagination({
            url: config.measure.url,
            textLoad: config.measure.textLoad,
            selectedLabel: 'Provvedimenti selezionati',
            footerTable: config.measure.footerTable,
            classTable: config.measure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.measure.hideTable,
            showTable: config.measure.showTable,
            search_placeholder: config.measure.search_placeholder,
            setInputDataValue: '#input_measures',
            dataParams: {
                model: 22,
                institution_id: institutionId,
            },
            dateFormat: config.measure.dateFormat,
            columns: config.measure.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.measure.dataSource,
            addRecord: config.measure.addRecord
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($personnel['measures']))
        @foreach($personnel['measures'] as $measure)
        @php
            $tmpStartDate = !empty($measure['date']) ? ' - '.date('d/m/Y', strtotime($measure['date'])) : null;
        @endphp
        measures.patOsAjaxPagination.setValue('{{ $measure['id'] }}', '{{ htmlEscape(trim($measure['object'])).' - '.htmlEscape(trim($measure['number'])).''.$tmpStartDate }}', true);
        @endforeach
        @endif

        /*
         * Preview Foto
         */
        document.getElementById('photo_file').addEventListener('change', () => {
            const input = document.getElementById('photo_file');
            const file = input.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();

            reader.onload = e => {
                document.getElementById('src-logo-ente').src = e.target.result;
            };

            reader.readAsDataURL(file);

            document.getElementById('preview-url-logo').style.display = '';
        });

        /*
         * Rimuove la preview
         */
        document.getElementById('clear-preview-logo').addEventListener('click', () => {
            document.getElementById('photo_file').value = '';

            document.getElementById('preview-url-logo').style.display = 'none';
        });

        /*
        * Rimozione foto
        * */
        @if(!empty($personnel['photo']) && !empty($personnel['id']))
        document.getElementById('__remove_photo').addEventListener('click', () => {
            document.getElementById('personnel_photo')?.remove();

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_photo';
            input.value = '1';

            document.getElementById('_storage_type').after(input);
        });
        @endif

        {{-- Begin campi EDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });

        CKEDITOR.replace('input_other_info', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_information_archive', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_extremes_of_conference', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_compensations', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_trips_import', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_other_assignments', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_other_assignments_institutions', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('input_notes', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        CKEDITOR.replace('total_emoluments', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        {{-- End campi EDITOR --}}

        {{-- Begin dinamicizzazione campi form --}}
        {{-- In fase di modifica nascondo le note della mail  non disponibile, in caso sia disponibile e viceversa --}}
        @if($_storageType== 'insert' || ( !empty($personnel['not_available_email']) && $personnel['not_available_email'] === 1))
        document.getElementById("showEmailNotAvaiable").style.display = "none";
        @else
        document.getElementById("showEmailNotAvaiable").style.display = "block";
        document.getElementById("showEmail").style.display = "none";
        @endif

        {{-- Per indirizzo email non disponibile --}}
        document.querySelector('.ts-email_not_available').addEventListener('change', function () {
            if (this.value === '0') {
                document.getElementById("showEmail").style.display = "none";
                document.getElementById("showEmailNotAvaiable").style.display = "block";
                @if($_storageType == 'insert')
                document.getElementById("input_not_available_email_txt").value = 'Email non disponibile';
                @endif
            } else {
                document.getElementById("showEmail").style.display = "block";
                document.getElementById("showEmailNotAvaiable").style.display = "none";
            }
        });

        {{-- Mostro i campi del form in base al ruolo selezionato--}}
        document.getElementById('role_id').addEventListener('change', function () {
            let politicalRoles = <?php echo json_encode(@$political); ?>;// non mettere le virgolette
            let po_dir_fields = <?php echo json_encode(@$po_dir_fields); ?>;// non mettere le virgolette

            for (let i = 0; i < document.getElementsByClassName("poShow").length; i++) {
                document.getElementsByClassName("poShow")[i].style.display = 'none';
            }
            for (let i = 0; i < document.getElementsByClassName('politicalAssignmentShow').length; i++) {
                document.getElementsByClassName('politicalAssignmentShow')[i].style.display = 'none';
            }
            document.getElementById("determined_term").style.display = 'block';

            let selectedRoleId = this.value;

            if (Object.keys(po_dir_fields).includes(selectedRoleId)) {
                for (let i = 0; i < document.getElementsByClassName("poShow").length; i++) {
                    document.getElementsByClassName("poShow")[i].style.display = 'block';
                }
            } else if (Object.keys(politicalRoles).includes(selectedRoleId)) {
                for (let i = 0; i < document.getElementsByClassName("politicalAssignmentShow").length; i++) {
                    document.getElementsByClassName('politicalAssignmentShow')[i].style.display = 'block';
                }
                document.getElementById("determined_term").style.display = 'none';
            }
        });
        {{-- End dinamicizzazione campi form --}}

        {{-- Begin Salvataggio --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
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
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", true);
                let response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message, 'Personale');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/personnel') }}';
                }, 800);
                @else
                {{-- Controllo, se sono all'interno di un modale lo chiudo dopo il salvataggio --}}
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
                // Messaggi di errore del validatore campi e degli allegati
                let msg = [response.data.error_partial_attach, response.errors.error].filter(Boolean).join(", ");

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(msg, 6000);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End Salvataggio --}}

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

        {{-- Controllo per l'uscita dal form se i campi di input sono stati toccati --}}
        document.addEventListener("focusin", function (event) {
            if (event.target.matches(".ts-control, input")) {
                formModified = true;
            }
        });

        @if(!$is_box)
        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });
        @endif


    });
</script>
{% endblock %}
