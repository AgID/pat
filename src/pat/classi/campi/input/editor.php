<?php
include_once ('classi/' . $configurazione['editor'] . '.php');

if ($valoreVero != '') {
	$valore = $valoreVero;
}
// uso la variabile dei nomi come altezza forzata
$editor = new isEditor($nome, $valore, 'automatica', $nomi, $evento);
?>