<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Componente che la data di creazione e aggiornamento di un elemento --}}

@if(!empty($_institution_info['show_update_date']) && !empty($instance))
    @php
        $instance = $instance ?? null;
        $hideCreatedAt = false;

        $publishingStart = $instance['publishing_start'] ?? $instance['created_at'] ?? null;

        $updatedAt = $instance['updated_at'] ?? $instance['created_at'] ?? null;

        if (!empty($publishingStart) && !empty($updatedAt) && strtotime($publishingStart) > strtotime($updatedAt)) {
            $updatedAt = $publishingStart;
        }
    @endphp
    <p class="data-creazione mt-5" style="font-size: 14px;">
        <span class="icona far fa-clock"></span>
        <strong>

            @if(!empty($hideCreatedAt))
                {{ 'Contenuto aggiornato il ' . date('d/m/Y', strtotime((string)$updatedAt)) }}
            @else
                @if(!empty($instance['publishing_start']))
                    {{ 'Contenuto pubblicato il ' . date('d/m/Y', strtotime((string)$publishingStart))." - " }}
                @elseif(!empty($instance['created_at']))
                    {{ 'Contenuto creato il ' . date('d/m/Y', strtotime((string)$publishingStart))." - " }}
                @endif

                @if(!empty($updatedAt))
                    {{ 'Aggiornato il ' . date('d/m/Y', strtotime((string)$updatedAt)) }}
                @endif
            @endif
        </strong>
    </p>
@endif

