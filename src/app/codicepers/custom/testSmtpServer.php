<a class="btn btn-rounded <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>"> <!-- fine tag a -->

<?
if($parametri['icona']) {
	?>
	<i class="<? echo $parametri['icona']; ?>"></i>&nbsp;
	<?
}
?>

<? echo $parametri['etichetta']; ?>

</a>
<span style="display:none;" id="attendere_smtp_testmail">Attendere...</span>

<script type="text/javascript">
jQuery('#a_fn_smtp_testmail').on('click', function() {
	mail = jQuery('#smtp_testmail').val();
	if(mail == '') {
		jQuery('<div />').html("Non &egrave; stato inserito l'indirizzo email per il test").dialog({
	        title: 'Attenzione',
	        modal: true, resizable: false, draggable: false,
	        width: '600',
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
		return;
	} else {
		jQuery('#a_fn_smtp_testmail').hide();
		jQuery('#attendere_smtp_testmail').show();
		jQuery.ajax({
			url: 'ajax.php',
			type: 'get',
			dataType: 'json',
			data: {'azione': 'testSmtpServer', 'smtp_username': jQuery('#smtp_username').val(), 'smtp_password': jQuery('#smtp_password').val(), 'smtp_host': jQuery('#smtp_host').val(), 'smtp_port': jQuery('#smtp_port').val(), 'smtp_s': jQuery('#smtp_s').val(), 'smtp_auth': jQuery('#smtp_auth').val(), 'smtp_testmail': mail},
			success: function(data) {
				console.log(data);
				if(data.esito == 'ok') {
					jQuery('<div />').html("Invio effettuato: verifica ora la ricezione della mail sull'indirizzo "+mail).dialog({
				        title: 'Messaggio',
				        modal: true, resizable: false, draggable: false,
				        width: '600',
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
				} else {
					jQuery('<div />').html("Errore in fase di invio. Riprovare in seguito.").dialog({
				        title: 'Attenzione',
				        modal: true, resizable: false, draggable: false,
				        width: '600',
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
				jQuery('#a_fn_smtp_testmail').show();
				jQuery('#attendere_smtp_testmail').hide();
			},
			error: function(xhr, desc, err) {
				console.log(xhr);
				console.log("Details: " + desc + "\nError:" + err);
				alert('ERRORE: Impossibile verificare il funzionamento del server smtp.');
				jQuery('#a_fn_smtp_testmail').show();
				jQuery('#attendere_smtp_testmail').hide();
			}
		});
	}
});
</script>