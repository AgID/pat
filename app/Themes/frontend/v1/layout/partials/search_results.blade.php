<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<!-- Modale -->
<div class="modal fade" tabindex="-1" role="dialog" id="modalOpenData" aria-labelledby="modalOpenDataTitle">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalOpenDataTItle">Open Data</h5>
            </div>
            <div class="modal-body mb-4">
                <div id="loading-download-open-data">
                    <div class="form-row mb-4 mt-4 text-center">
                        <div class="col-md-12">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div class="col-md-12">
                            Attendere, elaborazione in corso..
                        </div>
                    </div>
                </div>
                <div id="success-download-open-data">
                    <div class="form-row mb-4 mt-4 text-center">
                        <div class="col-md-12 mb-3">
                            <i class="fas fa-thumbs-up fa-2x color-primary"></i>
                        </div>
                        <div class="col-md-12">
                            <h5>Download open data avvenuto con successo!</h5>
                        </div>
                    </div>
                </div>
                <div id="error-download-open-data">
                    <div class="form-row mb-4 mt-4 text-center">
                        <div class="col-md-12 mb-3">
                            <i class="fas fa-exclamation-triangle fa-2x" style="color:red"></i>
                            <h5>Attenzione!</h5>
                            <div id="error-download-open-data-text" style="display: inline-block;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sezione contenente la tabella con i risultati della ricerca e la paginazione --}}

<section id="risultati-ricerca">
    <div class="container">

        {{-- Tabella generata dal server --}}
        @if(!empty($table))

            <div class="" style="display: flex">
                <div class=""  style="display: inline-flex;">
                    @if(!empty($linkDownloadOpenData) && !empty($instances['data']))
                        <!--Pagine con pubblicazioni doppie-->
                        @if(!empty($doubleIssue))
                            @if( empty($filterUsed) )
                                <button id="btn-open-model-data" type="button" class="btn btn-primary btn-xs open-data-download-btn"
                                        data-bs-toggle="modal" data-bs-target="#modalOpenData" data-type="{{ $openDataPublication }}">
                                    <span class="fas fa-save"></span> Scarica Open data
                                </button>{{nbs(2)}}
                            @else
                                <button id="btn-open-model-search-data" type="button" class="btn btn-primary btn-xs open-data-download-btn"
                                        data-bs-toggle="modal" data-bs-target="#modalOpenData" data-type="{{ $openDataPublication }}">
                                    <span class="fas fa-save"></span> Scarica Open data (Risultati della ricerca)
                                </button>{{nbs(2)}}
                            @endif
                        @else
                            <!--Pagine con pubblicazioni singole-->
                            @if( empty($filterUsed) )
                                <button id="btn-open-model-data" type="button" class="btn btn-primary btn-xs open-data-download-btn" data-bs-toggle="modal" data-bs-target="#modalOpenData">
                                    <span class="fas fa-save"></span> Scarica Open data
                                </button>{{nbs(2)}}
                            @else
                                <button id="btn-open-model-search-data" type="button" class="btn btn-primary btn-xs open-data-download-btn" data-bs-toggle="modal" data-bs-target="#modalOpenData">
                                    <span class="fas fa-save"></span> Scarica Open data (Risultati della ricerca)
                                </button>{{nbs(2)}}
                            @endif
                        @endif
                    @endif
                    @if(!empty($instances['data']) && !empty($linkDownloadOpenData))

                        @if(isset($rangeOpenData,$instances['total']) && $instances['total'] >= ($rangeOpenData+1))
                            <!-- calcolo gli intervalli da mostrare nella select -->
                            @php
                                $valori = array(''=>'Seleziona l\'intervallo di esportazione');
                                $startRange= 1;
                                $endRange = $rangeOpenData;
                                if($endRange > 0){
                                    while($endRange < $instances['total']){
                                        $set = $startRange.'-'.$endRange;
                                        $valori[$set] = $set;
                                        $startRange+=$rangeOpenData;
                                        $endRange+=$rangeOpenData;
                                    }
                                $set = $startRange.'-'.$instances['total'];
                                $valori[$set] = $set;

                                }
                            @endphp


                            @if(!empty($doubleIssue))
                                <div class="col">
                                    {{ form_dropdown(
                                        'open_data_range',
                                        $valori,
                                        '',
                                        'id = "open_data_range_id_'.$openDataPublication.'" class="form-control select2-open-data" data-dropdown-css-class="select2-blue" style="width: 100%; border-width: thin; border-radius: 5px 5px 5px 5px;"'
                                    ) }}
                                </div>
                            @else
                                <div class="col">
                                    {{ form_dropdown(
                                        'open_data_range',
                                        $valori,
                                        '',
                                        'id = "open_data_range_id" class="form-control select2-open-data" data-dropdown-css-class="select2-blue" style="width: 100%; border-width: thin; border-radius: 5px 5px 5px 5px;"'
                                    ) }}
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            {{ $table }}
        @else
            <h5 class="font-weight-bold mb-5">Nessun elemento presente</h5>
        @endif

        {{-- Paginazione della tabella --}}
        @if(!empty($instances))
            {{ paginateBootstrap($instances) }}
        @endif

        @if(!empty($_institution_info['show_update_date']) && !empty($instances) && !empty($latsUpdatedElement))
            <p class="data-creazione mt-5" style="font-size: 14px;">
                <span class="icona far fa-clock"></span>
                <strong>{{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                    {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) }}</strong>
            </p>
        @endif
    </div>
</section>

{{-- Bottom Menu --}}
{% include v1/layout/partials/bottom_menu %}