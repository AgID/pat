<?
if ($interfaccia=="semplice") {
	////////////////// INTERFACCIA UTENTI SEMPLICE
	echo "<select ".$evento.$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
	echo "<option value=\"\">Tutti gli utenti</option>";
	// prelevo lista utenti
	// verifico condizione aggiuntiva
	if ($condizioneAgg != '') {
		$sql = "SELECT id,nome,username,permessi FROM ".$dati_db['prefisso']."utenti WHERE id!=-1 AND (".$condizioneAgg.") ORDER by permessi DESC,nome";
	} else {
		$sql = "SELECT id,nome,username,permessi FROM ".$dati_db['prefisso']."utenti WHERE id!=-1 ORDER by permessi DESC,nome";
	}
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		// pubblico alert
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$utenti = $database->sqlArrayAss($result);				
	foreach ($utenti as $utente) {
		$arrayScelte = explode(',',$valoreVero);
                $stringa = '';
		for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
			if ($utente['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		} 

		switch($utente['permessi']) {
			case "10":
				$testoP = "[SuperAdmin] ";
			break;
			case "3":
				$testoP = "[Root] ";
			break;
			case "0":
				$testoP = "[Utente] ";
			break;
			default:
				$testoP = "[Amministratore] ";
		
		}
		echo "<option value=\"".$utente['id']."\"".$stringa.">".$testoP.$utente['nome']."(".$utente['username'].")</option>";
	}
	echo "</select>";				
} else {
	////////////////// INTERFACCIA UTENTI COMPLETA
	// inserisco il codice javascript necessario
	echo "<script type=\"text/javascript\">
	function utenteScelto".$nome."(valore,nome) {
		// aggiungo nome della sezione
		var selectSezioni = document.getElementById('".$nome."nomi');
		// prima di aggiungere la sezione controllo che non sia già stata associata
		var associato = 0;
		for (var i=0;i<selectSezioni.length;i++) {
			if (selectSezioni.options[i].value == valore) {
				associato =1;
			}
		}
		if (associato) {
			alert('Questo utente è già presente nella tua selezione');
			navigazione.focus();
			return;					
		} else {
			var testoSezioni = document.getElementById('".$nome."');
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
		}
	}
	function cancellaUtente".$nome."(valore,nome) {
		// cancello nome della sezione
		var selectSezioni = document.getElementById('".$nome."nomi');
		selectSezioni.options[selectSezioni.selectedIndex]=null;
		// dopo aver cancellato le sezioni, ricreo il campo vero
		var testoSezioni = document.getElementById('".$nome."');
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
			nuovaOpzione.text = 'Tutti gli utenti';
	  		nuovaOpzione.value = '';  
	  		selectSezioni.add(nuovaOpzione, null); 	
		}
	}
	function aggiungiUtente".$nome."() {
		navigazione = window.open('navigazione_utenti.php?campo=".$nome."','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                
                if(window.focus){
                    navigazione.focus();
                }
	}			
	</script>";
	//echo "valore settato:".$valoreVero;
	// inserisco il campo nascosto
	if ($valoreVero != '' and $valoreVero != 0) {
		// pubblico i nomi
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\">";
		$arrayScelte = explode(',',$valoreVero);
		if (count($arrayScelte)==0 OR $valoreVero=='') {
			echo "<option value=\"\">Tutti gli utenti</option>";
		} else {
			for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
				echo "<option value=\"".$arrayScelte[$i]."\">".addslashes(nomeUserDaId($arrayScelte[$i],'nome'))."</option>";
			}  
		}
		echo "</select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />";  
	} else {
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\"><option value=\"\">Tutti gli utenti</option></select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"\" />"; 
	}
	
	// inserisco pulsante di apertura link
	echo "
	        <a class=\"bottoneClassico\" title=\"Aggiungi un utente alla selezione\" href=\"javascript:aggiungiUtente".$nome."();\">
		<img src=\"grafica/admin_skin/classic/user_add.gif\" alt=\"Aggiungi un utente alla selezione\" />Aggiungi utente</a>
	        <a class=\"bottoneClassico\" title=\"Togli un utente dalla selezione\" href=\"javascript:cancellaUtente".$nome."();\">
		<img src=\"grafica/admin_skin/classic/user_canc.gif\" alt=\"Togli un utente dalla selezione\" />Elimina utente</a>
	";
}
?>
