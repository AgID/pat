<?php

//echo "valoreVero: ".$valoreVero." e valore: ".$valore;

$strSelect = "";

if ($valoreVero and $valoreVero != '') {
	$strSelect = "checked=\"checked\" ";
}
echo "<input style=\"width:auto;border:none;\" " . $evento . " " . $classeStr . " type=\"checkbox\" id=\"" . $nome . "\" name=\"" . $nameForm . "\" value=\"1\" " . $strSelect . "/>";
?>
