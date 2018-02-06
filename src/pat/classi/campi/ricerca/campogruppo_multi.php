<?
if ($interfaccia=="semplice") {
	////////////////// INTERFACCIA GRUPPI SEMPLICE
	echo "<select ".$evento.$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
	echo "<option value=\"\">Tutti i gruppi</option>";
	// prelevo lista gruppi
	// verifico condizione aggiuntiva
	if ($condizioneAgg != '') {
		$sql = "SELECT id,nome,proprieta FROM ".$dati_db['prefisso']."oggetti_gruppi WHERE (".$condizioneAgg.") ORDER by proprieta";
	} else {
		$sql = "SELECT id,nome,proprieta FROM ".$dati_db['prefisso']."oggetti_gruppi ORDER by proprieta";
	}
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		// pubblico alert
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$sql = "SELECT id,nome,proprieta FROM ".$dati_db['prefisso']."oggetti_gruppi ORDER by proprieta";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		die("Errore in load lista user.".$sql);
	}
	$gruppi = $database->sqlArrayAss($result);				
	foreach ($gruppi as $gruppo) {
		$arrayScelte = explode(',',$valoreVero);
                $stringa = '';
		for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
			if ($gruppo['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		} 
		switch($gruppo['proprieta']) {
			case "utenti";
				$testoP = "[Utenti] ";
			break;
			case "newsletter";
				$testoP = "[Email] ";
			break;
		}
		echo "<option value=\"".$gruppo['id']."\"".$stringa.">".$testoP.$gruppo['nome']."</option>";
	}
	echo "</select>";				
} else {
	////////////////// INTERFACCIA GRUPPI COMPLETA
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
			alert('Questo gruppo è già presente nella tua selezione');
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
	function cancellaGruppo".$nome."(valore,nome) {
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
			nuovaOpzione.text = 'Tutti i gruppi';
	  		nuovaOpzione.value = '';  
	  		selectSezioni.add(nuovaOpzione, null); 	
		}
	}
	function aggiungiGruppo".$nome."() {
		navigazione = window.open('navigazione_gruppi.php?campo=".$nome."','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                
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
			echo "<option value=\"\">Tutti i gruppi</option>";
		} else {
			for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
				echo "<option value=\"".$arrayScelte[$i]."\">".addslashes(datoGruppo($arrayScelte[$i]))."</option>";
			}  
		}
		echo "</select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />";  
	} else {
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\"><option value=\"\">Tutti i gruppi</option></select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"\" />"; 
	}
	
	// inserisco pulsante di apertura link
	echo "
	        <a class=\"bottoneClassico\" title=\"Aggiungi un gruppo alla selezione\" href=\"javascript:aggiungiGruppo".$nome."();\">
		<img src=\"grafica/admin_skin/classic/gruppo_piu.gif\" alt=\"Aggiungi un gruppo alla selezione\" />Aggiungi gruppo</a>
	        <a class=\"bottoneClassico\" title=\"Togli un gruppo dalla selezione\" href=\"javascript:cancellaGruppo".$nome."();\">
		<img src=\"grafica/admin_skin/classic/gruppo_meno.gif\" alt=\"Togli un gruppo dalla selezione\" />Elimina gruppo</a>
	";
}
?>
