<div class="trasp_istanza_richiamo">
<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/personale/richiamo.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/personale/richiamo.php');
} else {
	/* template standard */
	
	if(moduloAttivo('nome_cognome_responsabile')) {
		$out = $istanzaOggetto['nome'].' '.$istanzaOggetto['cognome'];
		if(trim($out) == '') {
			$out = $istanzaOggetto['referente'];
		}
	} else {
		$out = $istanzaOggetto['referente'];
	}
	
	$anc = 'archivio3_personale-ente_'.$istanzaOggetto['id_sezione'].'_'.$istanzaOggetto['id'].'.html';
	echo '<div class="trasp_titolo_medio"><a href="'.$anc.'">'.$out.'</a></div>';
	
	
	if ($istanzaOggetto['foto'] != '' and $istanzaOggetto['foto'] != 'nessuno') {
		$posPunto = strrpos($istanzaOggetto['foto'], ".");
		$estFile = strtolower(substr($istanzaOggetto['foto'], ($posPunto +1)));
	
		if ($estFile == 'gif' or $estFile == 'jpg' or $estFile == 'jpeg' or $estFile == 'png' or $estFile == 'bmp') {
			// PUBBLICO UNA IMMAGINE
			echo "<div class=\"trasp_foto_referente\"><img alt=\"" . $istanzaOggetto['referente'] . "\" src=\"" . $base_url . "moduli/output_media.php?file=" . $documento->tabella . "/" . $istanzaOggetto['foto'] . "&amp;qualita=75&amp;larghezza=120px\" /></div>";
		}
	}
	
	
	if($istanzaOggetto['telefono'] != '') {
		echo '<div>Telefono: '.$istanzaOggetto['telefono'].'</div>';
	}
	
	if($istanzaOggetto['email'] != '') {
		echo '<div>Email: <a href="mailto:'.$istanzaOggetto['email'].'">'.$istanzaOggetto['email'].'</a></div>';
	}
	
	echo '<div class="reset"></div>';
}
?>
</div>