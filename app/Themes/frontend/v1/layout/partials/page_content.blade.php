<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Sezione che contiene eventuali contenuti della pagina: i paragrafi con i relativi richiami --}}
@if(!empty($paragraphs))
    {{-- Ciclo sui paragrafi e mostro il titolo e contenuto --}}
    @foreach($paragraphs as $paragraph)
        @if(!empty($paragraph['content']) || !empty($paragraph['recalls']))
            <div class="paragraph mb-2">
                @if(!empty($paragraph['name']))
                    <h3 class="page-subtitle text-muted">{{e: !empty($paragraph['name']) ? $paragraph['name'] : ''  }}</h3>
                @endif
                <div>
                    {{ $paragraph['content']  }}
                </div>

                {{-- Se il paragrafo ha richiami, li mostro --}}
                @if(!empty($paragraph['recalls']))
                    <div>
                        @foreach($paragraph['recalls'] as $recall)

                            @if(!empty($recall['records']))
                                <h6>{{e: !empty($recall['recall_name']) ? $recall['recall_name'] : $recall['title'] }}</h6>
                                <div class="griglia griglia-2 mb-3">
                                    @foreach($recall['records'] as $r)
                                        <div class="card-allegato">
                                            <span class="fas fa-link text-black"></span>
                                            <a class="text-muted"
                                               href="{{ siteUrl('page/'.$recall['section_fo_id'].'/details/'.$r['id'].'/'.urlTitle(!empty($recall['recall_name']) ? $recall['recall_name'] : $recall['title'])) }}"
                                               data-toggle="tooltip" data-placement="top"
                                               title="{{e: characterLimiter(strip_tags($r['field']), 40) }}">
                                                {{e: characterLimiter($r['field'], 40) }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                {{-- Mostro la data di creazione e di ultima modifica del paragrafo --}}
                @php $paragraph = $paragraph ?? null; @endphp

                @if(!empty($_institution_info['show_update_date']) && !empty($paragraph))
                    <p class="data-creazione" style="font-size: 14px; margin-top: 2rem">
                        <span class="icona far fa-clock"></span>
                        <strong>
                            @if(!empty($paragraph['hideCreatedAt']))
                                {{ !empty($paragraph['update_at']) ? 'Contenuto aggiornato il ' . date('d/m/Y', strtotime((string)$paragraph['update_at'])) : null }}
                            @else
                                {{ !empty($paragraph['create_at']) ? 'Contenuto pubblicato il ' . date('d/m/Y', strtotime((string)$paragraph['create_at'])) : null }}
                                {{ !empty($paragraph['update_at']) ? ' - Aggiornato il ' . date('d/m/Y', strtotime((string)$paragraph['update_at'])) : null }}
                            @endif

                        </strong>
                    </p>
                @endif
            </div>
        @endif
    @endforeach
@endif
