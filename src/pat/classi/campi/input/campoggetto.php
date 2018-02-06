<?
if ($accessibile==2) {
	echo "<select ".$evento.$classeStr." style=\"width:240px\" id=\"".$nome."\" name=\"".$nome."\">";
	
	// se ho comunicato un valore, lo cerco nel riferimento dell'oggetto
	if ($valoreVero == '') {
		echo "<option value=\"\"></option>";
	} else {
		$tabella = datoOggNoGruppo($valore, 'tabella');
		$campoNome = datoOggNoGruppo($valore, 'campo_default');
		$sql = "SELECT id," . $campoNome . " FROM " . $dati_db['prefisso'] . $tabella . " WHERE permessi_lettura != 'H' AND id=".$valoreVero;
		if (!($result = $database->connessioneConReturn($sql))) {
			die("Errore in load lista oggetti." . $sql);
		}
		$istanza = $database->sqlArray($result);
		if (is_array($istanza)) {
			echo "<option value=\"" . $istanza['id'] . "\" selected=\"selected\">" . $istanza[$campoNome] . "</option>";
		}
	 }
	
	
	echo "</select>";
	
	echo "
	    <script src=\"grafica/dhtmlx/dhtmlxCombo/codebase/dhtmlxcommon.js\" type=\"text/javascript\"></script>
	    <script src=\"grafica/dhtmlx/dhtmlxCombo/codebase/dhtmlxcombo.js\" type=\"text/javascript\"></script>
	    <script type=\"text/javascript\">
	      window.dhx_globalImgPath=\"grafica/dhtmlx/dhtmlxCombo/codebase/imgs/\";
	    </script>
	    <script type=\"text/javascript\">
	      var ".$nome."=dhtmlXComboFromSelect(\"".$nome."\");
	      ".$nome.".enableFilteringMode(true, \"autocompilazione.php?id_oggetto=".$valore."&amp;azione=oggetti&amp;condizione=".urlencode($condizioneAgg)."\", true);
	    </script>
	    "; 
	

	return;
}
if ($accessibile==1) {
	// VERSIONE ACCESSIBILE DEL FORM
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\">";
	if ($valoreVero == '') {
		echo "<option value=\"\" selected=\"selected\"></option>";
	} else {
		echo "<option value=\"\"></option>";
	}
	// prelevo lista istanze oggetto
	$tabella = datoOggNoGruppo($valore, 'tabella');
	$campoNome = datoOggNoGruppo($valore, 'campo_default');
	// verifico condizione
	if ($condizioneAgg != '') {
		$sql = "SELECT id," . $campoNome . " FROM " . $dati_db['prefisso'] . $tabella . " WHERE (" . $condizioneAgg . ") AND permessi_lettura != 'H'";
	} else {
		$sql = "SELECT id," . $campoNome . " FROM " . $dati_db['prefisso'] . $tabella . " WHERE permessi_lettura != 'H'";
	}
	if (!($result = $database->connessioneConReturn($sql))) {
		die("Errore in load lista oggetti." . $sql);
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
		echo "<option value=\"" . $istanza['id'] . "\"" . $stringa . ">" . $istanza[$campoNome] . "</option>";
	}
	echo "</select>";
	return;
}
// inserisco il codice javascript necessario
echo "<script type=\"text/javascript\">
function oggettoScelto" . $nome . "(valore,nome) {
	var oggettoId = document.getElementById('" . $nome . "');
	oggettoId.value = valore; 
	var oggettoNome = document.getElementById('" . $nome . "nome');
	oggettoNome.value = nome; 
	navigazione.close();
	" . $evento . ";
}
function sceltaOggetto" . $nome . "() {
	navigazione = window.open('navigazione_oggetti.php?id=" . $valore . "&campo=" . $nome . "&tipo_sel=singola','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
        
        if(window.focus){
            navigazione.focus();
        }
}
function refreshCampo" . $nome . "(valore,nome) {
	document.getElementById('" . $nome . "').value = 0; 
	document.getElementById('" . $nome . "nome').value = 'Istanza da selezionare'; 
}
</script>";

// inserisco il campo nascosto
if ($valoreVero != '' and $valoreVero != 0) {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
	// trovo il nome di questa istanza		
	echo "<input type=\"text\" " . $classeStr . " disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"" . addslashes(mostraDatoOggetto($valoreVero, $valore)) . "\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
} else {
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"0\" />";
	echo "<input type=\"text\" " . $classeStr . " disabled=\"disabled\" name=\"" . $nome . "nome\" id=\"" . $nome . "nome\" value=\"Istanza da selezionare\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
}

// inserisco pulsante di apertura link
echo "
        <a class=\"bottoneClassico\" title=\"Scegli un oggetto\" href=\"javascript:sceltaOggetto" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/famiglia_oggetto.gif\" alt=\"Scegli un oggetto\" />Seleziona</a>   
        <a class=\"bottoneClassico\" title=\"Cancella la selezione\" href=\"javascript:refreshCampo" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/refresh.gif\" alt=\"Cancella la selezione\" />Reset</a>
";
?>
