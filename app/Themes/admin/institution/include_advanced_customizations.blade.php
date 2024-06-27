<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-6">
        {{-- Allegato logo ente --}}
        <div class="form-group">
            <label for="custom_css">File css personalizzato</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="custom_css"
                       name="custom_css" accept="text/css" onchange="openCode(this.files)">
                <label class="custom-file-label" for="custom_css"
                       id="label_attach_logo">
                    Allega file css personalizzato
                </label>
            </div>

            {{-- In update se è gia presente mostro il file css dell'ente --}}
            @if(!empty($institution['custom_css']))
                <div id="view_file">
                    <div class="mt-2">
                        <img src="{{ baseUrl('assets/admin/img/word.png') }}" class="attach-image">
                        <h5>index.css</h5>
                        <div class="mt-2">
                            <button type="button" id="edit-file-css" class="btn btn-xs btn-outline-primary">
                                <i class="fas fa-edit"></i> {{ nbs(1) }} Edita file
                            </button>
                            {{ nbs(2) }}
                            <button type="button" class="btn btn-xs btn-outline-danger"
                                    id="clear-file">
                                <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina file
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Anteprima nuovo file css da caricare --}}
            <div id="preview-file-name">
                <div class="mt-2">
                    <img src="{{ baseUrl('assets/admin/img/word.png') }}" class="attach-image">
                    <h5 id="file_name_value"></h5>
                    <div class="mt-2">
                        <button type="button" class="btn btn-xs btn-outline-danger"
                                id="clear-file_new_file">
                            <i class="fas fa-trash"></i> {{ nbs(1) }} Elimina file
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($addons))
        <div class="col-md-6">
            <div class="form-group">
                <label for="addons">Addons</label>
                <div style="margin-bottom: 1rem;">
                    @foreach($addons AS $addon)
                        <span class="mr-2 mb-2" style="display: inline-block;">
                    @php
                        $addonData = [];
                        $addonData['type'] = 'checkbox';
                        $addonData['value'] = $addon->id;
                        $addonData['name'] = 'addons[]';
                        $addonData['id'] = 'addon_' . $addon->id;
                        if(
                            (uri()->segment('3',null) === 'edit' &&
                            !empty($addonsIds) &&
                            is_array($addonsIds) &&
                            in_array($addon->id,$addonsIds)===true)
                        ) {
                            $addonData['checked'] = true;
                        }
                    @endphp
                            {{ form_input($addonData) }}
                    &nbsp; {{ $addon->name }}
                </span>
                        <span class="mr-2 ml-1">
                    /
              </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <hr/>
</div>

<div class="card mt-5">
    <div class="card-header">
        <h3 class="card-title">Stili personalizzati CKEditor</h3>
        @if(!empty($hasEdit))
        <div class="card-tools">
            <ul class="nav nav-pills ml-auto">
                <li class="nav-item">
                    <button type="button" id="btn-CKEditor-file-css" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Nuovo stile
                    </button>
                </li>
            </ul>
        </div>
        @endif
    </div>

    <div class="card-body p-0">
        @if(!empty($hasEdit))
        <table class="table table-striped">
            <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Nome identificativo nell'editor</th>
                <th style="width: 25%">Nome classe</th>
                <th style="width: 15%">Elemento html</th>
                <th class="text-center" style="width: 15%">Azioni</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($customCSSCKEditor) && !empty($hasEdit))
                @foreach($customCSSCKEditor AS $value)
                    <tr>
                        <td>
                            {{ $value['id'] }}
                        </td>

                        <td>
                            {{ $value['title'] }}
                        </td>

                        <td>
                            {{ $value['class_name'] }}
                        </td>

                        <td>
                            <span class="badge badge-primary">
                                {{ $value['element'] }}
                            </span>
                        </td>

                        <td class="text-center">
                            <a href="{{ siteUrl('admin/institution/css/custom/ckeditor/edit/' . $value['id']) }}" 
                                data-id="{{ $value['id'] }}"
                                class="btn btn-light edit_cutom_css_ckeditor" title="Modifica record">
                                <i class="fas fa-edit"></i>
                            </a>
                            &nbsp;
                            <a href="{{ siteUrl('admin/institution/css/custom/ckeditor/delete/' . $value['id']) }}" 
                                data-id="{{ $value['id'] }}"
                                class="btn btn-light delete_cutom_css_ckeditor" title="Elimina record">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="5">
                        <h4>Nessun stile personalizzato in CKEditor</h4>
                    </td>
                </tr>
            @endif
            </tbody>
            <tfoot>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Nome identificativo nell'editor</th>
                <th style="width: 25%">Nome classe</th>
                <th style="width: 15%">Elemento html</th>
                <th class="text-center" style="width: 15%">Azioni</th>
            </tr>
            </tfoot>
        </table>
        @else
            <div class="row">
                <div class="col-md-12 text-center mt-5 mb-5">
                    <h5>
                        Per poter creare gli stili personalizzati è necessario creare prima l'ente.
                    </h5>
                </div>
            </div>
        @endif
    </div>

</div>

