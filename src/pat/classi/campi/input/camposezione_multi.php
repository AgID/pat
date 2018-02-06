<?
if ($accessibile) {
	////////////////// INTERFACCIA SEZIONI SEMPLICE
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\" size=\"3\" multiple=\"multiple\">";
	foreach ((array)$sezioni as $sezione) {
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
	// aggiungo nome della sezione
	var selectSezioni = document.getElementById('" . $nome . "nomi');
	// prima di aggiungere la sezione controllo che non sia già stata associata
	var associato = 0;
	for (var i=0;i<selectSezioni.length;i++) {
		if (selectSezioni.options[i].value == valore) {
			associato =1;
		}
	}
	if (associato) {
		alert('Questa sezione è già presente nella tua selezione');
		navigazione.focus();
		return;					
	} else {
		var testoSezioni = document.getElementById('" . $nome . "');
		var nuovaOpzione = document.createElement('option');
		nuovaOpzione.text = nome;
  		nuovaOpzione.value = valore;  
  		if (selectSezioni.options[0].value == '') {
  			selectSezioni.options[0]=null;	
  			testoSezioni.value = valore;
  		} else {
  			testoSezioni.value = testoSezioni.value+','+valore;
  			
  		}
		try {
			selectSezioni.add(nuovaOpzione, null);
		}
		catch(ex) {
			selectSezioni.add(nuovaOpzione, selectSezioni.length); // IE 
		}
  		//selectSezioni.add(nuovaOpzione, selectSezioni.length);
		navigazione.close();
		" . $evento . ";
	}
}
function cancellaSezione" . $nome . "(valore,nome) {
	// cancello nome della sezione
	var selectSezioni = document.getElementById('" . $nome . "nomi');
	selectSezioni.options[selectSezioni.selectedIndex]=null;
	// dopo aver cancellato le sezioni, ricreo il campo vero
	var testoSezioni = document.getElementById('" . $nome . "');
	testoSezioni.value = '';
	for (var i=0;i<selectSezioni.length;i++) {
		if (testoSezioni.value != '') {
			testoSezioni.value = testoSezioni.value+',';
		}
		testoSezioni.value = testoSezioni.value+selectSezioni.options[i].value;
	}
	// controllo se il select è vuoto
	if (selectSezioni.length==0) {
		var nuovaOpzione = document.createElement('option');
		nuovaOpzione.text = 'Nessuna sezione selezionata';
  		nuovaOpzione.value = '';  
  		selectSezioni.add(nuovaOpzione, null); 	
	}
	" . $evento . ";
}
function aggiungiSezione" . $nome . "() {
	navigazione = window.open('navigazione_sezioni.php?campo=" . $nome . "','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
        
        if(window.focus){
            navigazione.focus();
        }
}			
</script>";
//echo "valore settato:".$valoreVero;
// inserisco il campo nascosto
if ($valoreVero != '' and $valoreVero != 0) {
	// pubblico i nomi
	echo "<select " . $classeStr . " id=\"" . $nome . "nomi\" name=\"" . $nome . "nomi\" size=\"6\">";
	$arrayScelte = explode(',', $valoreVero);
	if (count($arrayScelte) == 0 OR $valoreVero == '') {
		echo "<option value=\"\">Nessuna sezione selezionata</option>";
	} else {
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			echo "<option value=\"" . $arrayScelte[$i] . "\">" . addslashes(nomeSezDaId($arrayScelte[$i])) . "</option>";
		}
	}
	echo "</select>";
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
} else {
	echo "<select " . $classeStr . " id=\"" . $nome . "nomi\" name=\"" . $nome . "nomi\" size=\"6\"><option value=\"\">Nessuna sezione selezionata</option></select>";
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"\" />";
}

// inserisco pulsante di apertura link
echo "
        <a class=\"bottoneClassico\" title=\"Aggiungi una sezione alla selezione\" href=\"javascript:aggiungiSezione" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/folder_piu.gif\" alt=\"Aggiungi una sezione alla selezione\" />Aggiungi</a>
        <a class=\"bottoneClassico\" title=\"Togli una sezione dalla selezione\" href=\"javascript:cancellaSezione" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/folder_meno.gif\" alt=\"Togli una sezione dalla selezione\" />Elimina</a>
";
?>
