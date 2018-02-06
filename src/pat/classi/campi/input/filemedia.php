<?
			if ($classeStr == '') {
				$styleStr = "style=\"width:100px !important;margin:0px 6px 0px 0px;\"";
			} else {
				$styleStr = "";
			}
			// nominativo
			if ($valoreVero == '') {
				echo "<input ".$classeStr." ".$evento." type=\"file\" id=\"".$nome."\" name=\"".$nome."\" value=\"\" ".$styleStr." />";
				echo "<input style=\"width:auto !important;margin-left:100px;\" onClick=\"document.getElementById('".$nome."').value = '';\" type=\"button\" value=\"cancella selezionato\" />";
				echo "<input type=\"hidden\" id=\"".$nome."azione\" name=\"".$nome."azione\" value=\"aggiungi\" />";
			} else {
				if ($classeStr == '') {
					$styleStr = "style=\"width:100px !important;margin:0px 6px 0px 0px;display:none;\"";
				} else {
					$styleStr = "";
				}
				// il file è in modifica, inserisco anche il bottone di eliminazione del file
				echo "<span style=\"padding:0px 10px 0px 0px;\">".$valoreVero."</span>";
				echo "<input ".$classeStr." ".$evento." type=\"file\" id=\"".$nome."\" name=\"".$nome."\" value=\"\" ".$styleStr." style=\"display:none;\" />";
				echo "<input type=\"input\" disabled=\"true\" id=\"".$nome."nome\" name=\"".$nome."nome\" value=\"File da eliminare\" style=\"display:none;width:84px;margin:0px 10px 0px 0px;\" />";
				echo "<input type=\"hidden\" id=\"".$nome."azione\" name=\"".$nome."azione\" value=\"nessuna\" />";
				// inserisco il codice javascript necessario
				echo "<script type=\"text/javascript\">
				function cancellaFile".$nome."() {
					//alert('richiesta di cancellazione per il file');
					filecampo = document.getElementById('".$nome."');
					filecampo.style.display = 'none';
					filenome = document.getElementById('".$nome."nome');
					filenome.style.display = 'inline';
					document.getElementById('".$nome."azione').value = 'elimina';
				}
				function nonCancellaFile".$nome."() {
					//alert('richiesta di modifica per il file');
					// richiesta di cancellazione per il file
					filecampo = document.getElementById('".$nome."');
					filecampo.style.display = 'block';
					filenome = document.getElementById('".$nome."nome');
					filenome.style.display = 'none';
					document.getElementById('".$nome."azione').value = 'modifica';
				}
				</script>";
				echo "
				        <a class=\"bottoneClassico\" id=\"ancora".$nome."\" title=\"Elimina il file\" href=\"javascript:cancellaFile".$nome."();\">
					<img src=\"grafica/admin_skin/classic/file_cancel.gif\" alt=\"Elimina il file\" />Cancella</a>
				        <a class=\"bottoneClassico\" id=\"ancora".$nome."nome\" title=\"Modifica il file\" href=\"javascript:nonCancellaFile".$nome."();\">
					<img src=\"grafica/admin_skin/classic/bozza_piccola.gif\" alt=\"Modifica il file\" />Sostituisci</a>
				";				
			}
?>
