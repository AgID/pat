<?php
if ($valoreVero != '') {
	$valore = $valoreVero;
}
if (!$lingua['htmlentities'] AND $lingua['id'] != 1) {
	$valore = html_entity_decode($valore);
}
echo "<textarea " . $classeStr . " " . $evento . " id=\"" . $nome . "\" name=\"" . $nameForm . "\" rows=\"4\" cols=\"20\">" . $valore . "</textarea>";
?>
