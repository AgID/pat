<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/strutture/organigramma.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/strutture/organigramma.php');
} else {
	/* template standard */
	
	$anc = $base_url.'archivio13_strutture_'.$istanzaOggetto['id_sezione'].'_'.$istanzaOggetto['id'].'.html';
	echo '<div class="titoloOrganigramma"><a href="'.$anc.'">'.mostraDatoOggetto($istanzaOggetto['id'], 13).'</a></div>';
	
	$docRif = new documento(13);
	$docRiferiti = $docRif->caricaDocumentiCampo('struttura', $istanzaOggetto['id']);
	
	include('codicepers/oggetti/strutture/organigrammaTree.php');
	
	unset ($docRiferiti);
	unset ($docRif);
	
}
?>