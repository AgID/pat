<?
if ($accessibile) {
	////////////////// INTERFACCIA GRUPPI SEMPLICE
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
	// prelevo lista gruppi
	// verifico condizione
	if ($condizioneAgg != '') {
		//$sql = "SELECT id,nome,proprieta FROM ".$dati_db['prefisso'].$tabella." WHERE (".$condizioneAgg.") AND permessi_lettura != \"H\"";
		$sql = "SELECT id,nome,proprieta FROM " . $dati_db['prefisso'] . "oggetti_gruppi WHERE (" . $condizioneAgg . ") ORDER by proprieta";
	} else {
		$sql = "SELECT id,nome,proprieta FROM " . $dati_db['prefisso'] . "oggetti_gruppi ORDER by proprieta";
	}
	if (!($result = $database->connessioneConReturn($sql))) {
		die("Errore in load lista oggetti." . $sql);
	}
	$gruppi = $database->sqlArrayAss($result);
	foreach ($gruppi as $gruppo) {
		$arrayScelte = explode(',', $valoreVero);
		$stringa = '';
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			if ($gruppo['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		}
		switch ($gruppo['proprieta']) {
			case "utenti";
				$testoP = "[Utenti] ";
				break;
			case "newsletter";
				$testoP = "[Email] ";
				break;
		}
		echo "<option value=\"" . $gruppo['id'] . "\"" . $stringa . ">" . $testoP . $gruppo['nome'] . "</option>";
	}
	echo "</select>";
	return;
}
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
function sceltaGruppo" . $nome . "() {
	navigazione = window.open('navigazione_gruppi.php?campo=" . $nome . "','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
        
        if(window.focus){
            navigazione.focus();
        }
}
function refreshCampo" . $nome . "(valore,nome) {
	document.getElementById('" . $nome . "').value = 0; 
	document.getElementById('" . $nome . "nome').value = 'Gruppo da selezionare'; 
}
</script>";
// inserisco il campo nascosto
if ($valoreVero != '' and $valoreVero != 0) {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
	// trovo il nome di questa istanza		
	echo "<input " . $classeStr . " type=\"text\" disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"" . addslashes(datoGruppo($valore)) . "\" style=\"width:60%;margin:0px 10px 0px 0px;\" />";
} else {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"0\" />";
	echo "<input " . $classeStr . " type=\"text\" disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"Gruppo da selezionare\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
}

// inserisco pulsante di apertura link
echo "
        <a class=\"bottoneClassico\" title=\"Scegli il gruppo\" href=\"javascript:sceltaGruppo" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/gruppo_piu.gif\" alt=\"Scegli il gruppo\" />Seleziona</a>
        <a class=\"bottoneClassico\" title=\"Cancella la selezione\" href=\"javascript:refreshCampo" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/refresh.gif\" alt=\"Cancella la selezione\" />Reset</a>
";
?>
