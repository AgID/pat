<?
if ($accessibile) {
	////////////////// INTERFACCIA UTENTI SEMPLICE
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
	// prelevo lista utenti
	// verifico condizione
	if ($condizioneAgg != '') {
		$sql = "SELECT id,nome,username,permessi FROM " . $dati_db['prefisso'] . "utenti WHERE id!=-1 AND (" . $condizioneAgg . ") ORDER by permessi DESC,nome";
	} else {
		$sql = "SELECT id,nome,username,permessi FROM " . $dati_db['prefisso'] . "utenti WHERE id!=-1 ORDER by permessi DESC,nome";
	}
	if (!($result = $database->connessioneConReturn($sql))) {
		die("Errore in load lista oggetti." . $sql);
	}
	$utenti = $database->sqlArrayAss($result);
	foreach ($utenti as $utente) {
		$arrayScelte = explode(',', $valoreVero);
		$stringa = '';
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			if ($utente['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		}
		switch ($utente['permessi']) {
			case "10";
				$testoP = "[SuperAdmin] ";
				break;
			case "3";
				$testoP = "[Root] ";
				break;
			case "0";
				$testoP = "[Utente] ";
				break;
			case "2";
				$testoP = "[Amministratore] ";
				break;
		}
		echo "<option value=\"" . $utente['id'] . "\"" . $stringa . ">" . $testoP . $utente['nome'] . "(" . $utente['username'] . ")</option>";
	}
	echo "</select>";
	return;
} else {
	// inserisco il codice javascript necessario
	echo "<script type=\"text/javascript\">
	function utenteScelto" . $nome . "(valore,nome) {
		var userId = document.getElementById('" . $nome . "');
		userId.value = valore; 
		var userNome = document.getElementById('" . $nome . "nome');
		userNome.value = nome; 
		navigazione.close();
		" . $evento . ";
	}
	function sceltaUtente" . $nome . "() {
		navigazione = window.open('navigazione_utenti.php?campo=" . $nome . "','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
	        
	        if(window.focus){
	            navigazione.focus();
	        }
	}
	function refreshCampo" . $nome . "(valore,nome) {
		document.getElementById('" . $nome . "').value = 0; 
		document.getElementById('" . $nome . "nome').value = 'Utente da selezionare'; 
	}
	</script>";
	// inserisco il campo nascosto
	if ($valoreVero != '' and $valoreVero != 0) {
		echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
		// trovo il nome di questa istanza		
		echo "<input " . $classeStr . " type=\"text\" disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"" . addslashes(nomeUserDaId($valoreVero, 'nome')) . "\" style=\"width:60%;margin:0px 10px 0px 0px;\" />";
	} else {
		echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"0\" />";
		echo "<input " . $classeStr . " type=\"text\" disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"Utente da selezionare\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
	}
	
	// inserisco pulsante di apertura link
	echo "
	        <a class=\"bottoneClassico\" title=\"Scegli un utente\" href=\"javascript:sceltaUtente" . $nome . "();\">
		<img src=\"grafica/admin_skin/classic/user_add.gif\" alt=\"Scegli un utente\" />Seleziona</a>
	        <a class=\"bottoneClassico\" title=\"Cancella la selezione\" href=\"javascript:refreshCampo" . $nome . "();\">
		<img src=\"grafica/admin_skin/classic/refresh.gif\" alt=\"Cancella la selezione\" />Reset</a>
	";
}
?>
