<?

// inserisco il codice javascript necessario
echo "<script type=\"text/javascript\">
function aggiornaOra" . $nome . "() {
	var totaleOre=0;
	var totaleMinuti=0;
	if (document.getElementById('" . $nome . "_ora')) {
		// aggiorno ora
		totaleOre = Number(60*document.getElementById('" . $nome . "_ora').value);
	}
	if (document.getElementById('" . $nome . "_min')) {
		// aggiorno minuti
		totaleMinuti = Number(document.getElementById('" . $nome . "_min').value);
	}
	document.getElementById('" . $nome . "').value = totaleOre+totaleMinuti;
	" . $evento . ";
	//alert('Aggiorno orario con totale minuti: '+document.getElementById('" . $nome . "').value);
}
</script>";
// verifico quale interfaccia pubblicare (ora/minuti)
if ($valore == 1) {
	// solo ora
	$oraIniziale = floor($valoreVero / 60);
	echo "<select " . $classeStr . " onChange=\"aggiornaOra" . $nome . "();\" id=\"" . $nome . "_ora\" name=\"" . $nome . "_ora\" style=\"width:50px !important;display:inline !important;\">";
	for ($i = 0; $i < 24; $i++) {
		$stringa = '';
		if ($i == $oraIniziale) {
			$stringa = ' selected="selected"';
		}
		if ($i < 10) {
			echo "<option value=\"" . $i . "\"" . $stringa . ">0" . $i . "</option>";
		} else {
			echo "<option value=\"" . $i . "\"" . $stringa . ">" . $i . "</option>";
		}
	}
} else
	if (!$valore) {
		// orario completo
		$oraIniziale = floor($valoreVero / 60);
		echo "<select " . $classeStr . " onChange=\"aggiornaOra" . $nome . "();\" id=\"" . $nome . "_ora\" name=\"" . $nome . "_ora\" style=\"width:50px !important;display:inline !important;\">";
		for ($i = 0; $i < 24; $i++) {
			$stringa = '';
			if ($i == $oraIniziale) {
				$stringa = ' selected="selected"';
			}
			if ($i < 10) {
				echo "<option value=\"" . $i . "\"" . $stringa . ">0" . $i . "</option>";
			} else {
				echo "<option value=\"" . $i . "\"" . $stringa . ">" . $i . "</option>";
			}
		}
		$minIniziale = $valoreVero - ($oraIniziale * 60);
		echo "</select> : <select " . $classeStr . " onChange=\"aggiornaOra" . $nome . "();\" id=\"" . $nome . "_min\" name=\"" . $nome . "_min\" style=\"width:50px !important;display:inline !important;\">";
		for ($i = 0; $i < 60; $i++) {
			$stringa = '';
			if ($i == $minIniziale) {
				$stringa = ' selected="selected"';
			}
			if ($i < 10) {
				echo "<option value=\"" . $i . "\"" . $stringa . ">0" . $i . "</option>";
			} else {
				echo "<option value=\"" . $i . "\"" . $stringa . ">" . $i . "</option>";
			}
		}
		echo "</select>";
	} else {
		// solo minuti
		$minIniziale = $valoreVero;
		echo "<select " . $classeStr . " onChange=\"aggiornaOra" . $nome . "();\" id=\"" . $nome . "_min\" name=\"" . $nome . "_min\" style=\"width:50px !important;display:inline !important;\">";
		for ($i = 0; $i < 60; $i++) {
			$stringa = '';
			if ($i == $minIniziale) {
				$stringa = ' selected="selected"';
			}
			if ($i < 10) {
				echo "<option value=\"" . $i . "\"" . $stringa . ">0" . $i . "</option>";
			} else {
				echo "<option value=\"" . $i . "\"" . $stringa . ">" . $i . "</option>";
			}
		}
		echo "</select>";
	}
echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
?>
