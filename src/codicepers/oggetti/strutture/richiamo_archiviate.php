<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/strutture/richiamo_archiviate.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/strutture/richiamo_archiviate.php');
} else {
	/* template standard */
	
	echo '<div class="titoloElenco">'.mostraDatoOggetto($istanzaOggetto['id'], 13).'</div>';
	
	if($istanzaOggetto['__archiviata_descrizione'] != '') {
		echo '<div>'.$istanzaOggetto['__archiviata_descrizione'].'</div>';
	}
	
}
?>