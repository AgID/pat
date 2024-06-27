<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Dibattito pubblico --}}
<div class="form-group col-md-6">
    <label for="{{$cat['field'].'_check'}}">Stai pubblicando un <u>{{  $cat['title'] }}</u>?</label>
    <div class="select2-blue" id="input_public_debate">
        {{ form_dropdown(
             $cat['field'].'_check',
            [
                0 => 'No',
                1 => 'Si'
            ],
            @$procedure[$cat['field'].'_check'],
            'class="form-control select2-'.$cat['field'].' check_select" id="'.$catId.'" data-dropdown-css-class="select2-blue" style="width: 100%;"'
        ) }}
    </div>
</div>

<div class="col-md-12 px-0" id="{{$catId}}_box" style="display: none;">
    <div class="col-md-12 px-0">
        {{-- ***** BEGIN: include attach**** --}}
        <?php $attId = $catId; $title = $cat['title']; $noSeparator = true; ?>
        <?php $listAttach = $attachs[$attId] ?? []; $labels = $attLabels[$attId]??[];?>

        {% include layout/partials/attach %}
        {{-- ***** END: include attach**** --}}
    </div>

    <div class="col-md-12 mb-3">
        {{-- Campo Testo di descrizione --}}
        <label for="{{$cat['field'].'_notes'}}">Note {{ strtolower($cat['title']) }}</label>
        {{form_editor([
            'name' => $cat['field'].'_notes',
            'value' => !empty($procedure[$cat['field'].'_notes']) ? $procedure[$cat['field'].'_notes'] : null,
            'id' => 'input_'.$cat['field'].'_notes',
            'class' => 'form-control input_'.$cat['field'].'_notes'
        ]) }}
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        CKEDITOR.replace('input_<?php echo $cat['field']?>_notes');

        {{-- Select2 per campo "Dibattito Pubblico" --}}
        let $dropdownTypology = $('.select2-<?php echo $cat['field']?>');
        $dropdownTypology.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
    })
</script>