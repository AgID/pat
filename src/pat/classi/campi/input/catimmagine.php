<?
			// inserisco il codice javascript necessario
			echo "<script type=\"text/javascript\">
			function catimmagineScelta".$nome."(valore,nome) {
				document.getElementById('".$nome."').value = valore; 
				document.getElementById('".$nome."nome').value = nome; 
				navigazione.close();
				//alert('nome comunicato'+document.getElementById('".$nome."nome').value);
				".$evento.";
			}
			function catseleziona".$nome."() {
				navigazione = window.open('navigazione_immagini.php?campo=".$nome."&mostracat=1','immagini','height=600,width=520,toolbar=no,scrollbars=yes,status=yes');
                                
                                if(window.focus){
                                    navigazione.focus();
                                }
			}

			function refreshCampo".$nome."(valore,nome) {
				document.getElementById('".$nome."').value = 0; 
				document.getElementById('".$nome."nome').value = 'Cartella da selezionare'; 
			}
			</script>";
			// inserisco il campo nascosto
			if ($valoreVero != '' and $valoreVero != 0) {
				echo "<input type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />";   
				// trovo il nome di questa istanza		
				echo "<input ".$classeStr." type=\"text\" disabled=\"disabled\" name=\"".$nome."nome\" id=\"".$nome."nome\" value=\"".addslashes(nomeCatMedia($valoreVero,'nome'))."\" style=\"width:40%;margin:0px 10px 0px 0px;\" />";
			} else {
				echo "<input type=\"hidden\" name=\"".$nome."\" id=\"".$nome."\" value=\"0\" />";
				echo "<input ".$classeStr." type=\"text\" disabled=\"disabled\" name=\"".$nome."nome\" id=\"".$nome."nome\" value=\"Cartella da selezionare\" style=\"width:40%;margin:0px 10px 0px 0px;\" />"; 
			}
			
			// inserisco pulsante di apertura link
			echo "
			        <a class=\"bottoneClassico\" title=\"Scegli una cartella\" href=\"javascript:catseleziona".$nome."();\">
				<img src=\"grafica/admin_skin/classic/folder.gif\" alt=\"Scegli una cartella\" />Scegli una cartella</a>
			        <a class=\"bottoneClassico\" title=\"Cancella la selezione\" href=\"javascript:refreshCampo".$nome."();\">
				<img src=\"grafica/admin_skin/classic/refresh.gif\" alt=\"Cancella la selezione\" />Reset</a>
			";
?>
