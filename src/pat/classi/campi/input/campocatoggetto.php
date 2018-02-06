<?
$forzadmin = $condizioneAgg;
if ($accessibile) {
	// VERSIONE ACCESSIBILE DEL FORM
	echo "<select " . $evento . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\" size=\"3\" multiple=\"multiple\">";
	// prelevo lista istanze oggetto
	$tabella = nomeCatDaId(datoOggNoGruppo($valore, 'id_categoria'), 'tabella');
	if ($tabella != 'nessuna') {
		// verifico condizione
		if ($condizioneAgg != '') {
			$sql = "SELECT id,nome FROM " . $dati_db['prefisso'] . $tabella . " WHERE (" . $condizioneAgg . ") AND permessi_lettura != 'H'";
		} else {
			$sql = "SELECT id,nome FROM " . $dati_db['prefisso'] . $tabella . " WHERE permessi_lettura != 'H'";
		}

		if (!($result = $database->connessioneConReturn($sql))) {
			die("Errore in load lista categorie oggetti." . $sql);
		}
		$istanze = $database->sqlArrayAss($result);
		foreach ($istanze as $istanza) {
			$arrayScelte = explode(',', $valoreVero);
			$stringa = '';
			for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
				if ($istanza['id'] == $arrayScelte[$i]) {
					$stringa = ' selected="selected" ';
				}
			}
			echo "<option value=\"" . $istanza['id'] . "\"" . $stringa . ">" . $istanza['nome'] . "</option>";
		}
	}
	echo "</select>";
	return;
}

// inserisco il codice javascript necessario
echo "<script type=\"text/javascript\">
function catOggettoScelto" . $nome . "(valore,nome) {
	var oggettoId = document.getElementById('" . $nome . "');
	oggettoId.value = valore; 
	var oggettoNome = document.getElementById('" . $nome . "nome');
	oggettoNome.value = nome; 
	navigazione.close();
	" . $evento . ";
}
function sceltaCatOggetto" . $nome . "() {
	navigazione = window.open('navigazione_oggetti.php?id=" . $valore . "&campo=" . $nome . "&scelta=cat&forzadmin=".$forzadmin."','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
        
        if(window.focus){
            navigazione.focus();
        }
}
function refreshCampo" . $nome . "(valore,nome) {
	document.getElementById('" . $nome . "').value = ''; 
	document.getElementById('" . $nome . "nome').value = 'Categoria da selezionare'; 
}
</script>";

// inserisco il campo nascosto
if ($valoreVero != '') {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
	// trovo il nome di questa istanza		
	echo "<input type=\"text\" " . $classeStr . " disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"" . addslashes(mostraDatoCatOggetto($valoreVero, $valore)) . "\" style=\"width:60%;margin:0px 10px 0px 0px;\" />";
} else {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"\" />";
	echo "<input type=\"text\" " . $classeStr . " disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"Categoria da selezionare\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
}

// inserisco pulsante di apertura link
echo "
        <a class=\"bottoneClassico\" title=\"Scegli una categoria oggetto\" href=\"javascript:sceltaCatOggetto" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/folder.gif\" alt=\"Scegli una categoria oggetto\" />Seleziona</a>
        <a class=\"bottoneClassico\" title=\"Cancella la selezione\" href=\"javascript:refreshCampo" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/refresh.gif\" alt=\"Cancella la selezione\" />Reset</a>
";
?>
