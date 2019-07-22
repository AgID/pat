// necessario per i campi file
//jQuery('.uniform-file').uniform();
// necessario per i campi select con ricerca
jQuery(".chzn-select").chosen({no_results_text: "Nessun risultato per",allow_single_deselect: true});
// necessario per i campi ad aumento con freccia
jQuery(".input-spinner").spinner({min: 0});

//Devo inizializzare il campo editor col nuovo ckEditor
CKEDITOR.replaceClass = 'htmlEditor';

jQuery.validator.addMethod("allegatoCheck", function(value , element) {
	if('<? echo $id; ?>' != '') {
		//modifica
		if(jQuery('#'+element.id+'azione').val() == 'nessuna') {
			return true;
		} else if(jQuery('#'+element.id+'azione').val() == 'elimina') {
			return false;
		} else if(jQuery('#'+element.id+'azione').val() == 'modifica' && !jQuery('#'+element.id).val()) {
			return false;
		} else if(jQuery('#'+element.id+'azione').val() == 'importAllegato' && !jQuery('#import-file-'+element.id).val()) {
			return false;
		} else if(jQuery('#'+element.id+'azione').val() == 'aggiungi' && !jQuery('#'+element.id).val() && !jQuery('#import-file-'+element.id).val()) {
			return false;
		} else {
			return true;
		}
	} else {
		//inserimento
		if(!jQuery('#'+element.id).val() && !jQuery('#import-file-'+element.id).val()) {
			return false;
		} else {
			return true;
		}
	}
}, 'Inserisci il file allegato');


<?php
/*
$regoleValidazioneOggetti = array();
$reg = array();

$regoleValidazioneOggetti[4]
*/
?>