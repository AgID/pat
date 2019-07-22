<?php
if($istanzaOggetto[$c] != '') {
	echo '<div class="contenitoreIstanza1">';
	echo '<div class="titolo2">'.$istanzaOggetto[$c.'_tit'].'</div>';
	$ist = explode(',', $istanzaOggetto[$c]);
	foreach((array)$ist as $is) {
		$i = mostraDatoOggetto($is, $idO, '*');
		$anc = $base_url.'archivio'.$idO.'_'.$c.'_'.$i['id_sezione'].'_'.$i['id'].'.html';
		echo '<div class="titoloRichiamoModello"><a href="'.$anc.'">'.mostraDatoOggetto($is, $idO).'</a></div>';
	}
	echo '</div>';
}
?>