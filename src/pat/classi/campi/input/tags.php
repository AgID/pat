<?
if ($accessibile) {
	////////////////// INTERFACCIA TAG SEMPLICE
	echo "<select " . $evento . " " . $classeStr . " id=\"" . $nome . "\" name=\"" . $nome . "\" size=\"4\" multiple=\"multiple\">";
	foreach ($tags as $tag) {
		$arrayScelte = explode(',', $valoreVero);
		$stringa = '';
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			if ($tag['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		}
		echo "<option value=\"" . $tag['id'] . "\"" . $stringa . ">" . $tag['nome'] . "</option>";
	}
	echo "</select>";
	return;
}
// inserisco il codice javascript necessario
echo "<script type=\"text/javascript\">
	function selezioneTags" . $nome . "(valori,nomi) {	
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
function tagScelto" . $nome . "(valore,nome) {
        var selectTag = document.getElementById('" . $nome . "nomi');
	// prima di aggiungere il tag controllo che non sia già stato associato
	var associato = 0;
	for (var i=0;i<selectTag.length;i++) {
		if (selectTag.options[i].value == valore) {
			associato =1;
		}
	}
	if (associato) {
		alert('Questo tag è già presente nella selezione');
		navigazione.focus();
		return;					
	} else {
		// aggiungo tag
		var testoTag = document.getElementById('" . $nome . "');
		var nuovaOpzione = document.createElement('option');
		nuovaOpzione.text = nome;
  		nuovaOpzione.value = valore;  
  		if (selectTag.options[0].value == '') {
  			selectTag.options[0]=null;	
  			testoTag.value = valore;
  		} else {
  			testoTag.value = testoTag.value+','+valore;
  		}
		try {
			selectTag.add(nuovaOpzione, null);
		}
		catch(ex) {
			selectTag.add(nuovaOpzione, selectTag.length); // IE 
		}
		//selectTag.add(nuovaOpzione, selectTag.length);
		//selectTag.add(nuovaOpzione, null); 
		navigazione.close();
		" . $evento . ";
	}
	
}
function cancellaTag" . $nome . "(valore,nome) {
	// cancello tag
	var selectTag = document.getElementById('" . $nome . "nomi');
	selectTag.options[selectTag.selectedIndex]=null;
	// dopo aver cancellato il tag scelto, ricreo il contenuto
	var testoTag = document.getElementById('" . $nome . "');
	testoTag.value = '';
	for (var i=0;i<selectTag.length;i++) {
		// controllo 
		if (testoTag.value != '') {
			testoTag.value = testoTag.value+',';
		}
		testoTag.value = testoTag.value+selectTag.options[i].value;	
	}
	// controllo se il select è vuoto
	if (selectTag.length==0) {
		var nuovaOpzione = document.createElement('option');
		nuovaOpzione.text = 'Nessun tag selezionato';
  		nuovaOpzione.value = '';  
  		selectTag.add(nuovaOpzione, null); 	
	}
}
function aggiungiTag" . $nome . "() {
	var testoOggetti = document.getElementById('" . $nome . "');
	navigazione = window.open('navigazione_tags.php?campo=" . $nome . "&tipo_sel=multi&selezioni='+testoOggetti.value,'','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
        
        if(window.focus){
            navigazione.focus();
        }
}			
</script>";

if ($valoreVero != '' and $valoreVero != 0) {
	// pubblico i nomi
	echo "<select " . $classeStr . " id=\"" . $nome . "nomi\" name=\"" . $nome . "nomi\" size=\"6\">";
	$arrayScelte = explode(',', $valoreVero);
	if (count($arrayScelte) == 0 OR $valoreVero == '') {
		echo "<option value=\"\">Nessun tag selezionato</option>";
	} else {
		for ($i = 0, $tot = count($arrayScelte); $i < $tot; $i++) {
			$tagTemp = caricaTag($arrayScelte[$i]);
			$tagTemp['nome'] = $tagTemp['nome'] . "(" . datoCartellaTag($tagTemp['id_categoria']) . ")";
			echo "<option value=\"" . $arrayScelte[$i] . "\">" . addslashes($tagTemp['nome']) . "</option>";
		}
	}
	echo "</select>";
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" />";
} else {
	echo "<select " . $classeStr . " id=\"" . $nome . "nomi\" name=\"" . $nome . "nomi\" size=\"6\"><option value=\"\">Nessun tag selezionato</option></select>";
	echo "<input type=\"hidden\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"\" />";
}
echo "
        <a class=\"bottoneClassico\" style=\"display:inline;\" title=\"Selezione dei tag\" href=\"javascript:aggiungiTag" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/tag_add.gif\" alt=\"Selezione tags\" />Selezione tags</a>
        <a class=\"bottoneClassico\" style=\"display:inline;\" title=\"Togli un tag dalla selezione\" href=\"javascript:cancellaTag" . $nome . "();\">
	<img src=\"grafica/admin_skin/classic/tag_del.gif\" alt=\"Togli un tag dalla selezione\" />Elimina tag</a>
";
?>
