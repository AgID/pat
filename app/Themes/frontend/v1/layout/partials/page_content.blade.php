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
        <div class="mb-2">
            <h3 class="page-subtitle text-muted">{{e: !empty($paragraph['name']) ? $paragraph['name'] : ''  }}</h3>
            <div>
                {{ $paragraph['content']  }}
            </div>

            @if(!empty($paragraph))
                <p class="data-creazione" style="font-size: 14px;">
                    <span class="icona far fa-clock"></span>
                    <strong>
                        {{ !empty($paragraph['create_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$paragraph['create_at'])) : null }}
                        {{ !empty($paragraph['update_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$paragraph['update_at'])) : null }}
                    </strong>
                </p>
            @endif
        </div>
    @endforeach
@endif