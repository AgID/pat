//aggiornare i dati della stazione appaltante
if(jQuery("#"+id_campo).val() > 0) {
	jQuery('#ajax_processing').show();
	jQuery.ajax({
		url: 'ajax.php',
		type: 'get',
		dataType: 'json',
		data: {'azione': 'getDatiStazioneAppaltante', 'id': jQuery('#'+id_campo).val()},
		success: function(data, abb, bc) {
			try {
				if(data.esito == 'ok') {
					//submit
					if(data.stazione.denominazione_aggiudicatrice) {
						jQuery('#denominazione_aggiudicatrice').val(data.stazione.denominazione_aggiudicatrice);
					}
					if(data.stazione.dati_aggiudicatrice) {
						jQuery('#dati_aggiudicatrice').val(data.stazione.dati_aggiudicatrice);
					}
					if(data.stazione.tipo_amministrazione) {
						jQuery('#tipo_amministrazione').val(data.stazione.tipo_amministrazione).trigger("chosen:updated");
						jQuery("#tipo_amministrazione").trigger("liszt:updated");
					}
					if(data.stazione.sede_provincia) {
						jQuery('#sede_provincia').val(data.stazione.sede_provincia).trigger("chosen:updated");
						jQuery("#sede_provincia").trigger("liszt:updated");
					}
					if(data.stazione.sede_comune) {
						jQuery('#sede_comune').val(data.stazione.sede_comune);
					}
					if(data.stazione.sede_indirizzo) {
						jQuery('#sede_indirizzo').val(data.stazione.sede_indirizzo);
					}
					jQuery('#ajax_processing').hide();
				} else {
					jQuery('#ajax_processing').hide();
				}
			} catch(err) {
				jQuery('#ajax_processing').hide();
			}
		},
		error: function(xhr, desc, err) {
			jQuery('#ajax_processing').hide();
		}
	});
} else if(showAlertStazione) {
	jQuery('<div />').html('Selezionare la Stazione appaltante').dialog({
        title: 'Attenzione',
        modal: true, resizable: false, draggable: false,
        width: '400',
        close: function() {
            jQuery(this).dialog('destroy').remove();
        },
        buttons: [{
            text: "Chiudi",
            class: 'btn btn-primary',
            click: function() {
            	jQuery(this).dialog("close");
            }
        }]
	});
}