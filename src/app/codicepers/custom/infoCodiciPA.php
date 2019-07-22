<?php
/*
 * Created on 07/gen/2016
 */
?>
<a class="btn btn-rounded <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>"> <!-- fine tag a -->

<?
if($parametri['icona']) {
	?>
	<i class="<? echo $parametri['icona']; ?>"></i>
	<?
}
?>

<? echo $parametri['etichetta']; ?>

</a>

</script>
<?
if(!$configurazione['inclusa_funzione_infoCodiciPA']) {
	$configurazione['inclusa_funzione_infoCodiciPA'] = 1;
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.infoCodiciPA').on('click', function() {
		jQuery('#div_infoCodiciPA').dialog({
	        title: 'Informazione',
	        modal: true, resizable: false, draggable: false,
	        width: 600,
	        close: function() {
	            //jQuery(this).dialog('destroy').remove();
	        },
	        buttons: [{
	            text: "OK",
	            class: 'btn btn-primary',
	            click: function() {
	            	jQuery(this).dialog("close");
	            }
	        }]
	    });
	});
});
</script>
<div style="display: none;">
	<div id="div_infoCodiciPA">
		<p>&nbsp;</p>
		<p>
			I codici
			<ul>
				<li><p>Cod. univoco IPA amm. dichiarante</p></li>
				<li><p>Cod. univoco Aree Org. Omogenee</p></li>
				<li><p>Cod. univoco Unità Org.</p></li>
				<li><p>Cod. univoco IPA amm. conferente</p></li>
			</ul>
		</p>
		<p>sono scaricabili dal sito <a target="_blank" href="https://www.indicepa.gov.it/documentale/n-opendata.php">www.indicepa.gov.it</a> alla pagina relativa agli opendata.</p>
		<p>&nbsp;</p>
		<p><strong>Note di compilazione</strong></p>
		<ul>
			<li><p>Il campo <strong>Compenso</strong> deve essere pari a 0,00 se Tipologia compenso &egrave; Gratuito, maggiore di 1,00 se Tipologia compenso &egrave; Previsto o Presunto.</p></li>
			<li><p>Il campo <strong>Incarico saldato</strong> deve essere No se Tipologia compenso &egrave; Gratuito.</p></li>
			<li><p>Il campo <strong>Data di fine incarico</strong> deve essere valorizzato se Incarico saldato &egrave; Si. La data deve essere successiva rispetto alla Data di inizio incarico.</p></li>
			<? if($istanzaOggetto['tipo_incarico'] == 'incarichi dipendenti esterni' or $_GET['tipo'] == 'incarico_cons') { ?>
			<li><p><strong>Allegati</strong>: se Tipologia percettore &egrave; Persona fisica, allegare Curriculum del soggetto e Dichiarazione altri incarichi.</p></li>
			<? } ?>
		</ul>
	</div>
</div>
<? 
}
?>