<?php
/*
 * Created on 30/nov/2015
 *
 */
?>
jQuery(".cbox_<? echo $parametri['id_campo']; ?>").colorbox({inline:true, width: '80%'});

var indici_probabilita_risposte = jQuery('#indici_probabilita_risposte').val();
if(indici_probabilita_risposte == '' || indici_probabilita_risposte == undefined) {
	indici_probabilita_risposte = '0|0|0|0|0|0';
}
indici_probabilita_risposte = indici_probabilita_risposte.split('|');

jQuery('.calcolo_indici_probabilita_risposte input[type=radio]').on('change', function() {
	
	indici_probabilita_risposte[jQuery(this).attr('data-id')] = this.value;
	jQuery('#indici_probabilita').val(((parseInt(indici_probabilita_risposte[0]) + parseInt(indici_probabilita_risposte[1]) + parseInt(indici_probabilita_risposte[2]) + parseInt(indici_probabilita_risposte[3]) + parseInt(indici_probabilita_risposte[4]) + parseInt(indici_probabilita_risposte[5])) / 6).toFixed(2));
	
	jQuery('#indici_probabilita_risposte').val(indici_probabilita_risposte.join('|'));
	
	if(jQuery('#indici_impatto').val() == '') {
		jQuery('#indici_impatto').val((0).toFixed(2));
	}
	
	jQuery('#classificazione_rischio').val(
		(jQuery('#indici_probabilita').val() * jQuery('#indici_impatto').val()).toFixed(2)
	);
	
});