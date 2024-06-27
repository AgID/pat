<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<div class="form-group">
    <label for="category_id">Associa Categoria</label>
    <?php echo form_dropdown(
        'category_id',
        [
            0 => 'Categoria 1',
            1 => 'Categoria 2',
        ],
        1,
        'id="category_id" class="form-control category_id"'
    ) ?>
</div>


<div class="form-group">
    <label for="file_media">Nome della categoria</label>
    <?php echo form_input([
        'id' => 'category_name',
        'name' => 'category_name',
        'class' => 'form-control category_name',
        'placeholder' => 'Nome della categoria'
    ]) ?>
</div>



