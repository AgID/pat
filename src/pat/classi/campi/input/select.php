<?

echo "<select " . $classeStr . " " . $evento . "  id=\"" . $nome . "\" name=\"" . $nameForm . "\">";
$valori = explode(",", $valore);
$nomiCampi = explode(",", $nomi);
$num = 0;
foreach ($valori as $val) {
	$stringa = '';
	// ora rimuovo eventuali apostrofi inziali e finali

	//$arrayStringa = str_split($val);
	//$arrayStringa = preg_split('//', $val, -1, PREG_SPLIT_NO_EMPTY);
	//$val = '';
	//foreach($arrayStringa as $carattere) {
	//	if ($carattere != "'") {
	//		$val = $val.$carattere;
	//	}
	//}

	if ($valoreVero == stripslashes($val)) {
		$stringa = ' selected="selected"';
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
