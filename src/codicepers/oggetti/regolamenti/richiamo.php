<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/regolamenti/richiamo.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/regolamenti/richiamo.php');
} else {
	/* template standard */
	
	$anc = $base_url.'archivio19_regolamenti_'.$istanzaOggetto['id_sezione'].'_'.$istanzaOggetto['id'].'.html';
	echo '<div class="titoloRichiamoModello"><a href="'.$anc.'">'.mostraDatoOggetto($istanzaOggetto['id'], 19).'</a></div>';
	
	if ($istanzaOggetto['descrizione_mod'] != '') {
		echo '<div>'.tagliaContHtml($istanzaOggetto['descrizione_mod'], 120).'</div>';
	}
}
?>