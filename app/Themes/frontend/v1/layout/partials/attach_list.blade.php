<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Componente che mostra la lista degli allegati --}}
@if(!empty($instance) && !empty($listAttach))
    @php
        if(!empty($anchorsNumber)) {
            $anchorsNumber++;
            }
    @endphp
    <h3 id="allegati" class="anchor testo-blu page-subtitle">Allegati</h3>
    <div class="griglia griglia-2 mb-5">
        @foreach($listAttach as $attach)
            @php
                $name = strlen($attach['label']) >= 1 ? $attach['label'] : $attach['file']['name'];
                $ext = strtolower($attach['file']['ext']);

            @endphp
            <div class="card-allegato">
                @if($ext == '.pdf')
                    <span class="far fa-file-pdf"></span>
                @elseif(in_array($ext, ['.doc','.docm','.docx']))
                    <span class="far fa-file-word"></span>
                @elseif(in_array($ext, ['.xls','.xlsx']))
                    <span class="far fa-file-excel"></span>
                @elseif(in_array($ext, ['.zip','.rar','.gzip','.tar','.7z']))
                    <span class="far fa-file-archive"></span>
                @elseif(in_array($ext, ['.jpeg','.jpg','.png','.svg','.eps']))
                    <span class="far fa-image"></span>
                @else
                    <span class="fas fa-paperclip"></span>
                @endif

                <a class="text-muted" href="{{ siteUrl('download/' . $attach['id'] ) }}" data-toggle="tooltip"
                   data-placement="top"
                   title="{{xss: $name }}">
                    {{xss: characterLimiter($name, 40)}}
                    ({{ str_replace('.','',$attach['file']['ext']) }},
                    {{xss: (!empty($attach['file']['size']) ? $attach['file']['size'] . ' Kb' :  '') }})
                </a>

                    <?php /*
                <span class="fas fa-paperclip"></span>
                <a href="{{ siteUrl('download/' . $attach['id'] ) }}" data-toggle="tooltip"
                   data-placement="top"
                   title="{{ $attach['file']['name'] }}">{{e: characterLimiter($attach['file']['name'], 40) }}
                </a>
                */ ?>
                <div class="ml-3">
                    <div class="data-creazione">
                        <span class="text-black mr-1">Pubblicato il:</span>
                        <span class="text-muted">
                            {{ date('d-m-Y', strtotime((string)$attach['created_at'])) }}
                        </span>
                    </div>

                    <div class="data-creazione">
                        <span class="text-black mr-1">Aggiornato il:</span>
                        <span class="text-muted">
                            {{ date('d-m-Y', strtotime((string)$attach['updated_at'])) }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif