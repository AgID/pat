<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>



<div id="payment-container">

    <div class="alert alert-info mb-4" id="grant-alert">
        <strong>Si ricorda che: </strong>Non sono oggetto di pubblicazione i pagamenti effettuati per sovvenzioni, contributi, sussidi, ausili
        finanziari e vantaggi economici pubblicati ai sensi degli artt. 26 e 27 del d.lgs. n. 33/2013. Si
        ricorda che da tali vantaggi economici sono esclusi quelli di importo inferiore a 1000 euro nell’anno
        solare.
    </div>

    <div class="form-row d-flex align-items-end">

    </div>

    <div class="form-row d-flex align-items-end">
        <div class="form-group col-md-12">
            <label for="payment_object">Oggetto</label>
            <?php echo  form_input([
                'name' => 'payment_object',
                'value' => !empty($payment['object']) ? ((!empty($_storageType) && $_storageType === 'duplicate') ? 'Copia di ' . $payment['object'] : $payment['object']) : null,
                'placeholder' => 'Oggetto',
                'id' => 'input_payment_object',
                'class' => 'form-control input_payment_object',
            ]); ?>
        </div>
    </div>

    <div class="form-row d-flex align-items-end">

        <!-- Campo introdotto a seguito della delibera 481/2025 -->

        <div class="form-group col-md-3" >
            <label for="payment_supplier">Beneficiario *</label>
            <div id="input_payment_supplier">
                <?php echo  form_dropdown(
                    'payment_supplier',
                    @$paymentSupplierTypology,
                    !empty($payment['supplier']) ? $payment['supplier'] : null,
                    'class="ts-payment_supplier" style="width: 100%;"'
                ); ?>
            </div>
        </div>

        <!-- Fine -->

        <div class="form-group col-md-2">
            <label for="payment_year">Anno *</label>
            <div id="input_payment_year">
                <?php echo  form_dropdown(
                    'payment_year',
                    @$years,
                    !empty($payment['year']) ? $payment['year'] : null,
                    'class="ts-payment_year" style="width: 100%;"'
                ); ?>
            </div>
        </div>

        <div class="form-group col-md-2">
            <label for="payment_trimester">Trimestre *</label>
            <div id="input_payment_trimester">
                <?php echo  form_dropdown(
                    'payment_trimester',
                    [null=>'', 1 => '1', 2=>'2', 3=>'3', 4=>'4'],
                    !empty($payment['trimester']) ? $payment['trimester'] : null,
                    'class="ts-payment_trimester" style="width: 100%;"'
                ); ?>
            </div>
        </div>

        <div class="form-group col-md-3">
            <label for="payment_compensation">Importo *</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                </div>
                <?php echo  form_input([
                'name' => 'payment_compensation',
                'value' => !empty($payment['compensation']) ? $payment['compensation'] : null,
                'placeholder' => 'Importo',
                'id' => 'input_payment_compensation',
                'class' => 'form-control input_payment_compensation a-num-class',
            ]); ?>
            </div>
        </div>

    </div>


    <div class="form-row d-flex align-items-end">

        <div class="form-group col-md-4">
            <label for="payment_category">Categoria di spesa *</label>
            <div id="input_payment_category">
                <?php echo  form_dropdown(
                    'payment_category',
                    [null=>'', 1 => 'Uscite correnti', 2=>'Uscite in conto capitale'],
                    !empty($payment['expense_category']) ? $payment['expense_category'] : null,
                    'class="ts-payment_category" style="width: 100%;"'
                ); ?>
            </div>
        </div>

        <div class="form-group col-md-4" id="typology1_div" style="display: none;">
            <label for="payment_typology1">Tipologia di spesa * <small>uscite correnti</small></label>
            <div id="input_payment_typology1">
                <?php echo  form_dropdown(
                    'payment_typology1',
                    @$paymentTypologyCorrenti,
                    null,
                    'class="ts-payment_typology1" style="width: 100%;"'
                ); ?>
            </div>
        </div>

        <div class="form-group col-md-3" id="typology2_div" style="display: none;">
            <label for="payment_typology2">Tipologia di spesa * <small>uscite in conto capitale</small></label>
            <div id="input_payment_typology2">
                <?php echo  form_dropdown(
                    'payment_typology2',
                    @$paymentTypologyCapitale,
                    null,
                    'class="ts-payment_typology2" style="width: 100%;"'
                ); ?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <label for="payment_notes">Note</label>
        <?php echo form_editor([
            'name' => 'payment_notes',
            'value' => !empty($payment['notes']) ? $payment['notes'] : null,
            'id' => 'input_payment_notes',
            'class' => 'form-control input_payment_notes'
        ]); ?>
    </div>


    @if(!empty($institution_id))
        {{ form_input([
            'type' => 'hidden',
            'name' => 'institution_id',
            'value' => $institution_id,
            'id' => 'institution_id',
            'class' => 'institution_id',
        ]) }}
    @endif

</div>

<script>
    $(document).ready(function () {

       let institutionId = document.getElementById('institution_id').value ?? '';

        CKEDITOR.replace('input_payment_notes', {
            customConfig: '<?php echo  verifyFirstOrLastSlash('assets/common/ckeditor4/config.js'); ?>',
            height: 200,

        });


        new TomSelect('.ts-payment_supplier', {
            ...TS_SINGLE,
            placeholder: 'Seleziona'
        });

        new TomSelect('.ts-payment_year', {
            ...TS_SINGLE,
            placeholder: 'Seleziona'
        });

        new TomSelect('.ts-payment_trimester', {
            ...TS_SINGLE,
            placeholder: 'Seleziona'
        });

        new TomSelect('.ts-payment_typology1', {
            ...TS_SINGLE,
            placeholder: 'Seleziona'
        });

        new TomSelect('.ts-payment_typology2', {
            ...TS_SINGLE,
            placeholder: 'Seleziona'
        });

        new TomSelect('.ts-payment_category', {
            ...TS_SINGLE,
            placeholder: 'Seleziona',
            onChange() {
                manageTypology();
            }
        });

        manageTypology(true);

    });


    function manageTypology(update = false){

        let cat = document.querySelector('.ts-payment_category').tomselect.getValue();
        let typology1 = document.querySelector('.ts-payment_typology1').tomselect;
        let typology2 = document.querySelector('.ts-payment_typology2').tomselect;

        if(cat === '1'){
            $('#typology2_div').hide();
            typology2.clear();
            $('#typology1_div').show();
            if(update){
                typology1.setValue("<?php echo @$payment['expense_typology']; ?>");
            }
        }else if(cat === '2'){
            $('#typology1_div').hide()
            typology1.clear();
            $('#typology2_div').show();
            if(update){
                typology2.setValue("<?php echo @$payment['expense_typology']; ?>");
            }
        }else{
            $('#typology1_div').hide();
            $('#typology2_div').hide();
            typology1.clear();
            typology2.clear();
        }

    }


</script>
