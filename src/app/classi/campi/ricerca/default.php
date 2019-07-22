<?php
if ($valoreVero != '') {
	$valore = $valoreVero;
} else {
	$valore = 'qualunque';
}
echo "<input " . $evento . $classeStr . " type=\"text\" name=\"" . $nome . "\" title=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . htmlspecialchars($valore) . "\" onfocus=\"pulisciQualunque('".$nome."');\" onblur=\"impostaQualunque('".$nome."');\" />";
?>
