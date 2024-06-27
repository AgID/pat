<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Sezione che viene mostrata quando una pagina non ha contenuto --}}

@if(empty($paragraphs) && @$snodo)
    <div class="mt-5 col-md-12">
        <div class="mb-3" style="font-size: 20px;">
            <p>
                Questa è una sezione di snodo che non pubblica alcun contenuto. <br>
                Prosegui la navigazione accedendo, se disponibili, a una delle voci del menu di navigazione della pagina.
            </p>

        </div>
    </div>
@endif