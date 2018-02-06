<?
if ($accessibile) {
	// VERSIONE ACCESSIBILE DEL FORM
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "[]\" size=\"5\" multiple=\"multiple\">";
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
		// pubblico alert
		mostraAvviso(0, 'La condizione aggiuntiva di questo campo ha generato un errore.');
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


	////////////////// INTERFACCIA OGGETTI COMPLETA
	// inserisco il codice javascript necessario
	echo "<script type=\"text/javascript\">
	function selezioneOggetti" . $nome . "(valori,nomi) {	
		var selectOggetti = document.getElementById('" . $nome . "nomi');
		var testoOggetti = document.getElementById('" . $nome . "');
		
		// prima di tutto, svuoto la selezione
		num_option=selectOggetti.options.length;
		for(a=num_option;a>=0;a--){
			selectOggetti.options[a]=null;
		}
	
		// ora aggiungo valori
		testoOggetti.value = valori;
		var arrayValori =valori.split(',');
		var arrayNomi =nomi.split('|');
		for (i=0; i<arrayValori.length; i++) {
			//alert('analizzo id: '+arrayValori[i]+' con nome: '+arrayNomi[i]);
			var nuovaOpzione = document.createElement('option');
			nuovaOpzione.text = arrayNomi[i];
	  		nuovaOpzione.value = arrayValori[i];  

			try {
				selectOggetti.add(nuovaOpzione, null);
			}
			catch(ex) {
				selectOggetti.add(nuovaOpzione, selectOggetti.length); // IE 
			}		
		}
		navigazione.close();
		
	}
	
	function cancellaOggetto" . $nome . "(valore,nome) {
		// cancello nome oggetto
		var selectOggetti = document.getElementById('" . $nome . "nomi');
		selectOggetti.options[selectOggetti.selectedIndex]=null;
		// dopo aver cancellato gli oggetti, ricreo il campo vero
		var testoOggetti = document.getElementById('" . $nome . "');
		testoOggetti.value = '';
		for (var i=0;i<selectOggetti.length;i++) {
			if (testoOggetti.value != '') {
				testoOggetti.value = testoOggetti.value+',';
			}
			testoOggetti.value = testoOggetti.value+selectOggetti.options[i].value;
		}
		// controllo se il select è vuoto
		if (selectOggetti.length==0) {
			var nuovaOpzione = document.createElement('option');
			nuovaOpzione.text = 'Nessuna istanza selezionata';
	  		nuovaOpzione.value = '';  
	  		selectOggetti.add(nuovaOpzione, null); 	
		}
	}

	function aggiungiOggetto".$nome."() {
		var testoOggetti = document.getElementById('" . $nome . "');
		navigazione = window.open('navigazione_oggetti.php?id=".$valore."&campo=".$nome."&tipo_sel=multi&selezioni='+testoOggetti.value,'','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                
                if(window.focus){
                    navigazione.focus();
                }
	}			
	</script>";
//echo "valore settato:".$valoreVero;
// inserisco il campo nascosto
if ($valoreVero != '' and $valoreVero != 0) {
	// pubblico i nomi
	echo "<select id=\"" . $nome . "nomi\" " . $classeStr . " name=\"" . $nome . "nomi\" size=\"6\">";
	$arrayScelte = explode(',', $valoreVero);
	if (count($arrayScelte) == 0 OR $valoreVero == '') {
		echo "<option value=\"\">Nessuna istanza selezionata</option>";
	} else {
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			echo "<option value=\"" . $arrayScelte[$i] . "\">" . mostraDatoOggetto($arrayScelte[$i], $valore) . "</option>";
		}
	}
	echo "</select>";
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
} else {
	echo "<select " . $classeStr . " id=\"" . $nome . "nomi\" name=\"" . $nome . "nomi\" size=\"6\"><option value=\"\">Nessuna istanza selezionata</option></select>";
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"\" />";
}

// inserisco pulsante di apertura link
echo "
	        <a class=\"bottoneClassico\" style=\"display:inline;\" title=\"Selezione istanze\" href=\"javascript:aggiungiOggetto".$nome."();\">
		<img src=\"grafica/admin_skin/classic/famiglia_oggetto.gif\" alt=\"Selezione istanze\" />Selezione istanze</a>
		<a class=\"bottoneClassico\" title=\"Togli una istanza dalla selezione\" href=\"javascript:cancellaOggetto" . $nome . "();\">
		<img src=\"grafica/admin_skin/classic/tag_del.gif\" alt=\"Togli una istanza dalla selezione\" />Elimina</a>
";
?>
