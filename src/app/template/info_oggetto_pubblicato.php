<?
if($datiUser['sessione_loggato'] and ($datiUser['permessi']==10 or $datiUser['permessi']==3 or $datiUser['permessi']==2) and $configurazione['mostra_elenco_oggetto']) {
	
	?>
	<div class="contenitoreInfoOggettoPubblicato">
		In questa pagina &egrave; presente un elenco dell'archivio &quot;<?php echo $documento->nome; ?>&quot;.
	</div>
	<?
}
?>