<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>


    {{-- Componente che mostra la lista degli allegati --}}
    @if(!empty($instance) && !empty($listAttach))
        @php
            if(empty($attId)) {
                $attId = '';
            }

            if(!empty($anchorsNumber)) {
                $anchorsNumber++;
            }
        @endphp

        @if(!empty($attId))
            <h5 id="allegati_{{$attId}}" class="anchor testo-blu page-subtitle">Allegati</h5>
        @else
            <h3 id="allegati" class="anchor testo-blu page-subtitle">Allegati</h3>
        @endif

        <div class="griglia griglia-2 mb-5">

            @foreach($listAttach as $attach)
                @php
                    $name = strlen($attach['label']) >= 1 ? $attach['label'] : $attach['file']['name'];
                    $ext = strtolower($attach['file']['ext']);
                    $label = "";

                    $label = $label ?? null; $name = $name ?? null; $attach = $attach ?? null;
                @endphp

                <div class="card-allegato">



                    @if(!empty($label) && !empty($name))
                        <p>{{xss: $label }}</p>
                    @endif


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
                       title="{{xss: (!empty($attach['file']['client_name']) ? $attach['file']['client_name'] :  '') }}">
                        @if((!empty($label) && !empty($name)) || !empty($name))
                            {{xss: characterLimiter($name, 40)}}
                        @else
                            {{xss: characterLimiter($label, 150)}}
                        @endif
                        {{-- xss: characterLimiter((!empty($attach['file']['client_name']) ? $attach['file']['client_name'] :  ''), 40) --}}
                        ({{ str_replace('.','',$attach['file']['ext']) }},
                        {{xss: (!empty($attach['file']['size']) ? $attach['file']['size'] . ' Kb' :  '') }})
                    </a>
                    <br/>
                    <i class="fas fa-link"></i> Url:
                    <a class="text-muted" style="font-size: small; !important;" href="{{ siteUrl('download/' . $attach['id'] ) }}">
                        {{ siteUrl('download/' . $attach['id'] ) }}
                    </a>
                    <div class="date-pubblicazione-allegato">
                        <div class="data-creazione" style="font-size: small; !important;">
                            <span class="text-black mr-1">Pubblicato il: </span>
                            <span class="text-muted">
                            {{ date('d/m/Y', strtotime((string)$attach['created_at'])) }}
                        </span>
                        </div>

                        <div class="data-creazione" style="font-size: small; !important;">
                            <span class="text-black mr-1">Aggiornato il: </span>
                            <span class="text-muted">
                            {{ date('d/m/Y', strtotime((string)$attach['updated_at'])) }}
                        </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

