<?
if ($interfaccia == "semplice") {
	// verifico se creare il select della condizione
	if ($condizioneAgg == 'maggiore') {
		echo "<input type=\"hidden\" value=\"maggiore\" name=\"" . $nome . "_condizione\" id=\"" . $nome . "_condizione\" />";
	} else
		if ($condizioneAgg == 'minore') {
			echo "<input type=\"hidden\" value=\"minore\" name=\"" . $nome . "_condizione\" id=\"" . $nome . "_condizione\" />";
		} else
			if ($condizioneAgg == 'uguale') {
				echo "<input type=\"hidden\" value=\"uguale\" name=\"" . $nome . "_condizione\" id=\"" . $nome . "_condizione\" />";
			} else {
				creaOggettoFormPers('select', $nome . "_condizione", 'uguale,maggiore,minore', $_POST[$nome . "_condizione"], 'data esatta,dal giorno,al giorno', '', $classe);
			}
	if ($valoreVero != '') {
		// controllo il valore che mi è stato passato
		if (is_numeric($valoreVero)) {
			// traduco il numero in una stringa
			$valore = visualizzaData($valoreVero, 'd/m/Y');
		} else {
			$valore = $valoreVero;
		}
	} else {
		$valore = 'gg/mm/aaaa';
	}
	echo "<input " . $evento . $classeStr . " type=\"text\" id=\"" . $nome . "\" name=\"" . $nome . "\" value=\"" . $valore . "\" />";
} else {
	if ($condizioneAgg == 'maggiore') {
		echo "<input type=\"hidden\" value=\"maggiore\" name=\"" . $nome . "_condizione\" id=\"" . $nome . "_condizione\" />";
	} else
		if ($condizioneAgg == 'minore') {
			echo "<input type=\"hidden\" value=\"minore\" name=\"" . $nome . "_condizione\" id=\"" . $nome . "_condizione\" />";
		} else
			if ($condizioneAgg == 'uguale') {
				echo "<input type=\"hidden\" value=\"uguale\" name=\"" . $nome . "_condizione\" id=\"" . $nome . "_condizione\" />";
			} else {
				creaOggettoFormPers('select', $nome . "_condizione", 'uguale,maggiore,minore', $_POST[$nome . "_condizione"], 'data esatta,dal giorno,al giorno', '', $classe);
			}
	// verifico se passare il valore ultimo o quello interno
	if ($valoreVero != '') {
		//echo "valore: ".$valoreVero;
		// controllo il valore che mi è stato passato
		if (is_numeric($valoreVero) and $valoreVero != 1 and $valoreVero != 0) {
			// traduco il numero in una stringa
			$valore = $valoreVero;
		} else {
			$valore = 'gg/mm/aaaa';
		}
	} else {
		$valore = 'gg/mm/aaaa';

	}
	pubblicaCalendario($nome, $valore,'',$classeStr);
}
?>
