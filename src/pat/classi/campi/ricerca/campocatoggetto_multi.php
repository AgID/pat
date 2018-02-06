<?
function elaboraSelectRicercaCategorie($tabella, $condizioneAgg, $idCategoriaCorrente) {
	global $dati_db, $database;
	
	if ($condizioneAgg != '') {
		$sql = "SELECT id,nome FROM ".$dati_db['prefisso'].$tabella." WHERE id_riferimento = ".$idCategoriaCorrente." AND ".$condizioneAgg." AND permessi_lettura != 'H'";
	} else {
		$sql = "SELECT id,nome FROM ".$dati_db['prefisso'].$tabella." WHERE id_riferimento = ".$idCategoriaCorrente." AND permessi_lettura != 'H'";
	}
	if (($result = $database->connessioneConReturn($sql)) ) {
		$istanze = $database->sqlArrayAss($result);
		if(count($istanze)) {
			//ho sottocategorie: ricorsione
		} else {
			//non ho sottocategorie
		}
	}
	
}

if ($interfaccia=="semplice") {
	////////////////// INTERFACCIA OGGETTI SEMPLICE

	echo "<select ".$evento.$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
	if ($valoreVero == '') {
		echo "<option value=\"\" selected=\"selected\">qualunque</option>";
	} else {
		echo "<option value=\"\">qualunque</option>";
	}
	// prelevo lista istanze oggetto
	$tabella = nomeCatDaId(datoOggNoGruppo($valore,'id_categoria'),'tabella'); 
	if ($tabella != 'nessuna') {
		// verifico condizione aggiuntiva
		$optionsCategorie = elaboraSelectRicercaCategorie($tabella, $condizioneAgg, 0);
		if ($condizioneAgg != '') {
			$sql = "SELECT id,nome FROM ".$dati_db['prefisso'].$tabella." WHERE ".$condizioneAgg." AND permessi_lettura != 'H'";
		} else {
			$sql = "SELECT id,nome FROM ".$dati_db['prefisso'].$tabella." WHERE permessi_lettura != 'H'";
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
			echo "<option value=\"".$istanza['id']."\"".$stringa.">".$istanza['nome']."</option>";
		}
	}
	echo "</select>";				
} else {
	////////////////// INTERFACCIA OGGETTI COMPLETA
	// inserisco il codice javascript necessario
	echo "<script type=\"text/javascript\">
	function catOggettoScelto".$nome."(valore,nome) {
		// aggiungo nome della sezione
		var selectOggetti = document.getElementById('".$nome."nomi');
		// prima di aggiungere la sezione controllo che non sia già stata associata
		var associato = 0;
		for (var i=0;i<selectOggetti.length;i++) {
			if (selectOggetti.options[i].value == valore) {
				associato =1;
			}
		}
		if (associato) {
			alert('Questa cartella è già presente nella tua selezione');
			navigazione.focus();
			return;					
		} else {
			var testoOggetti = document.getElementById('".$nome."');
			var nuovaOpzione = document.createElement('option');
			nuovaOpzione.text = nome;
	  		nuovaOpzione.value = valore;  
	  		if (selectOggetti.options[0].value == '') {
	  			selectOggetti.options[0]=null;	
	  			testoOggetti.value = valore;
	  		} else {
	  			testoOggetti.value = testoOggetti.value+','+valore;
	  			
	  		}
			try {
				selectOggetti.add(nuovaOpzione, null);
			}
			catch(ex) {
				selectOggetti.add(nuovaOpzione, selectOggetti.length); // IE 
			}
	  		//selectSezioni.add(nuovaOpzione, selectOggetti.length);
			navigazione.close();
		}
	}
	function cancellaCatOggetto".$nome."(valore,nome) {
		// cancello nome della sezione
		var selectOggetti = document.getElementById('".$nome."nomi');
		selectOggetti.options[selectOggetti.selectedIndex]=null;
		// dopo aver cancellato le sezioni, ricreo il campo vero
		var testoOggetti = document.getElementById('".$nome."');
		testoOggetti.value = '';
		for (var i=0;i<selectOggetti.length;i++) {
			if (testoOggetti.value != '') {
				testoOggetti.value = testoSezioni.value+',';
			}
			testoOggetti.value = testoOggetti.value+selectOggetti.options[i].value;
		}
		// controllo se il select è vuoto
		if (selectOggetti.length==0) {
			var nuovaOpzione = document.createElement('option');
			nuovaOpzione.text = 'Tutte le istanze';
	  		nuovaOpzione.value = '';  
	  		selectOggetti.add(nuovaOpzione, null); 	
		}
	}
	function aggiungiCatOggetto".$nome."() {
		navigazione = window.open('navigazione_oggetti.php?id=".$valore."&campo=".$nome."&scelta=cat','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                
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
			echo "<option value=\"\">Tutte le istanze</option>";
		} else {
			for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
				echo "<option value=\"".$arrayScelte[$i]."\">".addslashes(mostraDatoCatOggetto($arrayScelte[$i], $valore))."</option>";
			}  
		}
		echo "</select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />";  
	} else {
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\"><option value=\"\">Tutte le cartelle</option></select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"\" />"; 
	}
	
	// inserisco pulsante di apertura link
	echo "
	        <a class=\"bottoneClassico\" style=\"display:inline;\" title=\"Aggiungi una istanza oggetto alla selezione\" href=\"javascript:aggiungiCatOggetto".$nome."();\">
		<img src=\"grafica/admin_skin/classic/folder.gif\" alt=\"Aggiungi una istanza oggetto alla selezione\" />Aggiungi cartella</a>
	        <a class=\"bottoneClassico\" style=\"display:inline;\" title=\"Togli una istanza oggetto dalla selezione\" href=\"javascript:cancellaCatOggetto".$nome."();\">
		<img src=\"grafica/admin_skin/classic/tag_del.gif\" alt=\"Togli una istanza oggetto dalla selezione\" />Elimina cartella</a>
	";
}
?>
