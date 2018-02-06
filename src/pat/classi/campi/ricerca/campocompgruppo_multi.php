<?
// questo campo, supporta solamente l'interfaccia semplice
	
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
	if ($valoreVero == '') {
		echo "<option value=\"\" selected=\"selected\">qualunque</option>";
	} else {
		echo "<option value=\"\">qualunque</option>";
	}
	// verifico che gruppo caricare
	$gruppoScelto = datoGruppo($valore, '*');
	if ($gruppoScelto['proprieta']=='utenti') {
		// SCELTA DI UTENTI
		if ($condizioneAgg != '') {
			$sql = "SELECT g.id_documento,u.nome FROM ".$dati_db['prefisso']."oggetti_gruppi_composizione g,".$dati_db['prefisso']."utenti u WHERE g.id_gruppo=".$gruppoScelto['id']." AND g.id_documento=u.id AND (" . $condizioneAgg . ")";
		} else {
			$sql = "SELECT g.id_documento,u.nome FROM ".$dati_db['prefisso']."oggetti_gruppi_composizione g,".$dati_db['prefisso']."utenti u WHERE g.id_gruppo=".$gruppoScelto['id']." AND g.id_documento=u.id";
		}
		if (!($result = $database->connessioneConReturn($sql))) {
			mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
		}

		$istanze = $database->sqlArrayAss($result);
		foreach ($istanze as $istanza) {
			$arrayScelte = explode(',', $valoreVero);
			$stringa = '';
			for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
				if ($istanza['id_documento'] == $arrayScelte[$i]) {
					$stringa = ' selected="selected" ';
				}
			}
			echo "<option value=\"" . $istanza['id_documento'] . "\"" . $stringa . ">" . $istanza['nome'] . "</option>";
		}
	} else if ($gruppoScelto['proprieta']=='anagrafico') {
		// SCELTA DI OGGETTI ANAGRAFICI

	} else if ($gruppoScelto['proprieta']=='newsletter') {
		// SCELTA DI MAIL
		if ($condizioneAgg != '') {
			$sql = "SELECT email FROM ".$dati_db['prefisso']."oggetti_gruppi_newsletter WHERE id_gruppo=".$gruppoScelto['id']." AND (" . $condizioneAgg . ")";
		} else {
			$sql = "SELECT email FROM ".$dati_db['prefisso']."oggetti_gruppi_newsletter WHERE id_gruppo=".$gruppoScelto['id'];
		}
		if (!($result = $database->connessioneConReturn($sql))) {
			die("Errore in load lista composizione gruppo newsletter." . $sql);
		}
		$istanze = $database->sqlArrayAss($result);
		foreach ($istanze as $istanza) {
			$arrayScelte = explode(',', $valoreVero);
			$stringa = '';
			for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
				if ($istanza['email'] == $arrayScelte[$i]) {
					$stringa = ' selected="selected" ';
				}
			}
			echo "<option value=\"" . $istanza['email'] . "\"" . $stringa . ">" . $istanza['email'] . "</option>";
		}
	}

	echo "</select>";			

?>
