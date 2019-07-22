<?php
/*
 * Created on 30/nov/2015
 *
 */
?>
jQuery(".cbox_<? echo $parametri['id_campo']; ?>").colorbox({inline:true, width: '80%'});

var indici_impatto_risposte = jQuery('#indici_impatto_risposte').val();
if(indici_impatto_risposte == '' || indici_impatto_risposte == undefined) {
	indici_impatto_risposte = '0|0|0|0';
}
indici_impatto_risposte = indici_impatto_risposte.split('|');

jQuery('.calcolo_indici_impatto_risposte input[type=radio]').on('change', function() {
	
	indici_impatto_risposte[jQuery(this).attr('data-id')-6] = this.value;
	jQuery('#indici_impatto').val(((parseInt(indici_impatto_risposte[0]) + parseInt(indici_impatto_risposte[1]) + parseInt(indici_impatto_risposte[2]) + parseInt(indici_impatto_risposte[3])) / 4).toFixed(2));
	
	jQuery('#indici_impatto_risposte').val(indici_impatto_risposte.join('|'));
	
	if(jQuery('#indici_probabilita').val() == '') {
		jQuery('#indici_probabilita').val((0).toFixed(2));
	}
	
	jQuery('#classificazione_rischio').val(
		(jQuery('#indici_probabilita').val() * jQuery('#indici_impatto').val()).toFixed(2)
	);
	
});