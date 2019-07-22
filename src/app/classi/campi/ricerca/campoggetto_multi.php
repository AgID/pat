<?
// INTERFACCIA DI AUTOCOMPILAZIONE
if ($interfaccia=="autocompilazione") {
	echo "<select ".$evento.$classeStr." style=\"width:200px\" id=\"".$nome."\" name=\"".$nome."\">";
	// se ho comunicato un valore, lo cerco nel riferimento dell'oggetto
	if ($valoreVero == '' or $valoreVero == 'qualunque') {
		echo "<option value=\"\" title=\"qualunque\">qualunque</option>";
	} else {
		$tabella = datoOggNoGruppo($valore, 'tabella');
		$campoNome = datoOggNoGruppo($valore, 'campo_default');
		$sql = "SELECT id," . $campoNome . " FROM " . $dati_db['prefisso'] . $tabella . " WHERE permessi_lettura != 'H' AND id=".$valoreVero;
		if (!($result = $database->connessioneConReturn($sql))) {
			die("Errore in load lista oggetti." . $sql);
		}
		$istanza = $database->sqlArray($result);
		if (is_array($istanza)) {
			echo "<option value=\"" . $istanza['id'] . "\" selected=\"selected\" title=\"" . $istanza[$campoNome] . "\">" . $istanza[$campoNome] . "</option>";
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
	      var z=dhtmlXComboFromSelect(\"".$nome."\");
	      z.enableFilteringMode(true, \"autocompilazione.php?id_oggetto=".$valore."&azione=oggetti&condizione=".urlencode($condizioneAgg)."\", true);
	    </script>
	    ";
	
} else if ($interfaccia=="semplice") {
	////////////////// INTERFACCIA OGGETTI SEMPLICE

	echo "<select ".$evento.$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
	echo "<option value=\"\" title=\"qualunque\">qualunque</option>";
	// prelevo lista istanze oggetto
	$tabella = datoOggNoGruppo($valore,'tabella');
	$campoNome = datoOggNoGruppo($valore,'campo_default');
	// verifico condizione aggiuntiva
	if ($condizioneAgg != '') {
		$sql = "SELECT id,".$campoNome." FROM ".$dati_db['prefisso'].$tabella." WHERE (".$condizioneAgg.") AND permessi_lettura != 'H'";
	} else {
		$sql = "SELECT id,".$campoNome." FROM ".$dati_db['prefisso'].$tabella." WHERE permessi_lettura != 'H'";
	}
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		// pubblico alert
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);				
	foreach ($istanze as $istanza) {
		$arrayScelte = explode(',',$valoreVero);
                $stringa = '';
		for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
			if ($istanza['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		} 
		echo "<option value=\"".$istanza['id']."\"".$stringa." title=\"".$istanza[$campoNome]."\">".$istanza[$campoNome]."</option>";
	}
	echo "</select>";
} else if ($interfaccia=="inputsearch") {
	////////////////// INTERFACCIA OGGETTI CON RICERCA TESTUALE

	if ($valoreVero != '') {
		$valoreTmp = $valoreVero;
	} else {
		$valoreTmp = 'qualunque';
	}
	echo "<input " . $evento . $classeStr . " type=\"text\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreTmp . "\" />";    
	echo "<input type=\"hidden\" name=\"" . $nome . "_id_oggetto\" id=\"" . $nome . "_id_oggetto\" value=\"" . $valore . "\" />";	
					
} else {
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
		echo "<select id=\"".$nome."nomi\" ".$classeStr." name=\"".$nome."nomi\" size=\"3\">";
		$arrayScelte = explode(',',$valoreVero);
		if (count($arrayScelte)==0 OR $valoreVero=='') {
			echo "<option value=\"\" title=\"qualunque\">qualunque</option>";
		} else {
			for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
				echo "<option value=\"".$arrayScelte[$i]."\" title=\"".mostraDatoOggetto($arrayScelte[$i], $valore)."\">".mostraDatoOggetto($arrayScelte[$i], $valore)."</option>";
			}  
		}
		echo "</select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />";  
	} else {
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\"><option value=\"\">qualunque</option></select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"\" />"; 
	}
	
	// inserisco pulsante di apertura link
	echo "
	        <a class=\"bottoneClassico\" style=\"display:inline;\" title=\"Selezione istanze\" href=\"javascript:aggiungiOggetto".$nome."();\">
		<img src=\"grafica/admin_skin/classic/famiglia_oggetto.gif\" alt=\"Selezione istanze\" />Selezione istanze</a>
	";
}
?>
