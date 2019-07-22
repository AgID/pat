<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/normativa/richiamo.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/normativa/richiamo.php');
} else {
	/* template standard */
	
	$anc = $base_url.'archivio27_normativa_'.$istanzaOggetto['id_sezione'].'_'.$istanzaOggetto['id'].'.html';
	echo '<div class="titoloRichiamoModello"><a href="'.$anc.'">'.mostraDatoOggetto($istanzaOggetto['id'], 27).'</a></div>';
	
	if ($istanzaOggetto['desc_cont'] != '') {
		echo '<div>'.tagliaContHtml($istanzaOggetto['desc_cont'], 120).'</div>';
	}
}
?>