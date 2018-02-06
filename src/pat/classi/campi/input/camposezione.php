<?
if ($accessibile) {
	////////////////// INTERFACCIA SEZIONI SEMPLICE
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
	foreach ($sezioni as $sezione) {
		$arrayScelte = explode(',', $valoreVero);
		$stringa = '';
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			if ($sezione['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		}
		echo "<option value=\"" . $sezione['id'] . "\"" . $stringa . ">" . parsingSiteMapNoLink($sezione['id']) . "</option>";
	}
	echo "</select>";
	return;
}
// inserisco il codice javascript necessario
echo "<script type=\"text/javascript\">
function sezioneScelta" . $nome . "(valore,nome) {
	var sezioneId = document.getElementById('" . $nome . "');
	sezioneId.value = valore; 
	var sezioneNome = document.getElementById('" . $nome . "nome');
	sezioneNome.value = nome; 
	navigazione.close();
	" . $evento . ";
}
function refreshCampo" . $nome . "(valore,nome) {
	document.getElementById('" . $nome . "').value = 0; 
	document.getElementById('" . $nome . "nome').value = 'Sezione da selezionare'; 
}
function sceltaSezione" . $nome . "() {
	navigazione = window.open('navigazione_sezioni.php?campo=" . $nome . "','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
        
        if(window.focus){
            navigazione.focus();
        }
}
</script>";
// inserisco il campo nascosto
if ($valoreVero != '' and $valoreVero != 0) {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
	// trovo il nome di questa istanza		
	echo "<input " . $classeStr . " type=\"text\" disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"" . addslashes(nomeSezDaId($valoreVero)) . "\" style=\"width:60%;margin:0px 10px 0px 0px;\" />";
} else {
	// inserisco l'inpuit
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"0\" />";
	echo "<input " . $classeStr . " type=\"text\" disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"Sezione da selezionare\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
}

// inserisco pulsante di apertura link
echo "
        <a class=\"bottoneClassico\" title=\"Scegli la sezione\" href=\"javascript:sceltaSezione" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/folder.gif\" alt=\"Scegli la sezione\" />Seleziona</a>
        <a class=\"bottoneClassico\" title=\"Cancella la selezione\" href=\"javascript:refreshCampo" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/refresh.gif\" alt=\"Cancella la selezione\" />Reset</a>
";
?>
