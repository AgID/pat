<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Componente che la data di creazione e aggiornamento di un elemento --}}

@if(!empty($_institution_info['show_update_date']) && !empty($instance))
    <p class="data-creazione mt-5" style="font-size: 14px;">
        <span class="icona far fa-clock"></span>
        <strong>{{ !empty($instance['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$instance['created_at'])) : null }}
            {{ !empty($instance['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$instance['updated_at'])) : date('d-m-Y', strtotime((string)$instance['created_at'])) }}</strong>
    </p>
@endif

