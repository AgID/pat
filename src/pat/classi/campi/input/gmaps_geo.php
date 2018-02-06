<?php
if ($valoreVero != '') {
	$valore = $valoreVero;
}
echo "<input " . $classeStr . " " . $evento . " type=\"text\" id=\"" . $nome . "\" name=\"" . $nome . "\" value=\"" . $valore . "\" />";
?>
