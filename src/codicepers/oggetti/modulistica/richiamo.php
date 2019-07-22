<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/modulistica/richiamo.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/modulistica/richiamo.php');
} else {
	/* template standard */
	
	$anc = $base_url.'archivio5_modulistica_'.$istanzaOggetto['id_sezione'].'_'.$istanzaOggetto['id'].'.html';
	echo '<div class="titoloRichiamoModello"><a href="'.$anc.'">'.mostraDatoOggetto($istanzaOggetto['id'], 5).'</a></div>';
	
	if ($istanzaOggetto['descrizione_mod'] != '') {
		echo '<div>'.tagliaContHtml($istanzaOggetto['descrizione_mod'], 120).'</div>';
	}
}
?>