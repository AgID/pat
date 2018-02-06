<?php
//completare la parte dell'input nascosto. Va testato con jquery 1.6.2
echo "<input type=\"hidden\" id=\"" . $nome . "_selected\" value=\"".$valore."\" />";
echo "<select " . $classeStr . " " . $evento . "  id=\"" . $nome . "\" name=\"" . $nameForm . "[]\" size=\"5\" multiple=\"multiple\">";
$valori = explode(",", $valore);
$nomiCampi = explode(",", $nomi);
$num = 0;
foreach ($valori as $val) {
	$stringa = '';

	$valoreUltimo = explode(",", $valoreVero);

	foreach ($valoreUltimo as $valoreTemp) {
		if (trim($valoreTemp) == trim(stripslashes($val))) {
			$stringa = ' selected="selected"';
		}
	}
	if ($nomi == '') {
		echo "<option value=\"" . stripslashes($val) . "\"" . $stringa . ">" . stripslashes($val) . "</option>";
	} else {
		echo "<option value=\"" . stripslashes($val) . "\"" . $stringa . ">" . $nomiCampi[$num] . "</option>";
	}
	$num++;
}
echo "</select>";
?>