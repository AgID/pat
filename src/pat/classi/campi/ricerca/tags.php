<?
if ($interfaccia=="semplice") {
	////////////////// INTERFACCIA TAG SEMPLICE
	echo "<select ".$evento.$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
	echo "<option value=\"\">Tutti i tag</option>";
	foreach ($tags as $tag) {
		$arrayScelte = explode(',',$valoreVero);
                $stringa = '';
		for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
			if ($tag['id'] == $arrayScelte[$i]) {
				$stringa = ' selected="selected" ';
			}
		} 
		echo "<option value=\"".$tag['id']."\"".$stringa.">".$tag['nome']."</option>";
	}
	echo "</select>";				
} else {
	////////////////// INTERFACCIA TAG COMPLETA
	// inserisco il codice javascript necessario
	echo "<script type=\"text/javascript\">
	function tagScelto".$nome."(valore,nome) {
	        var selectTag = document.getElementById('".$nome."nomi');
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
			var testoTag = document.getElementById('".$nome."');
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
		}
		
	}
	function cancellaTag".$nome."(valore,nome) {
		// cancello tag
		var selectTag = document.getElementById('".$nome."nomi');
		selectTag.options[selectTag.selectedIndex]=null;
		// dopo aver cancellato il tag scelto, ricreo il contenuto
		var testoTag = document.getElementById('".$nome."');
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
			nuovaOpzione.text = 'Tutti i tag';
	  		nuovaOpzione.value = '';  
	  		selectTag.add(nuovaOpzione, null); 	
		}
	}
	function aggiungiTag".$nome."() {
		navigazione = window.open('navigazione_tags.php?campo=".$nome."','','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                
                if(window.focus){
                    navigazione.focus();
                }
	}			
	</script>";

	if ($valoreVero != '' and $valoreVero != 0) {
		// pubblico i nomi
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\" style=\"height:40px;\">";
		$arrayScelte = explode(',',$valoreVero);
		if (count($arrayScelte)==0) {
			echo "<option value=\"\">Tutti i tag</option>";
		} else {
			for ($i=0,$tot=count($arrayScelte);$i<$tot;$i++) {
				$tagTemp = caricaTag($arrayScelte[$i]);
				$tagTemp['nome'] = $tagTemp['nome']."(".datoCartellaTag($tagTemp['id_categoria']).")";
				echo "<option value=\"".$arrayScelte[$i]."\">".addslashes($tagTemp['nome'])."</option>";
			} 
		} 
		echo "</select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />";  
	} else {
		echo "<select ".$classeStr." id=\"".$nome."nomi\" name=\"".$nome."nomi\" size=\"3\"><option value=\"\">Tutti i tag</option></select>";
		echo "<input ".$evento." type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"\" />"; 
	}
	echo "
	        <a class=\"bottoneClassico\" style=\"display:inline !important;\" title=\"Aggiungi un tag alla selezione\" href=\"javascript:aggiungiTag".$nome."();\">
		<img src=\"grafica/admin_skin/classic/tag_add.gif\" alt=\"Aggiungi un tag alla selezione\" />Aggiungi</a>
	        <a class=\"bottoneClassico\" style=\"display:inline !important;\" title=\"Togli un tag dalla selezione\" href=\"javascript:cancellaTag".$nome."();\">
		<img src=\"grafica/admin_skin/classic/tag_del.gif\" alt=\"Togli un tag dalla selezione\" />Elimina</a>
	";
}
?>
