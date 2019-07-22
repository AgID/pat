<?php
if ($valoreVero != '') {
	$valore = $valoreVero;
}
if (!$lingua['htmlentities'] AND $lingua['id'] != 1) {
	$valore = html_entity_decode($valore);
}
echo "<input ".$classeStr." ".$evento." type=\"text\" name=\"".$nameForm."\" id=\"".$nome."\" value=\"".$valore."\" />";
?>
