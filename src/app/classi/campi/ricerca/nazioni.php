<?
			include('inc/variabili.php');
                        if ($interfaccia=="semplice") {
                        	// pubblico elenco semplice senza js ma scremato per record
                        	if (!$valore) {
					///////////elenco continenti
					echo "<select ".$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
					foreach($arrayJsContinenti as $continente) {
						$stringa = '';
						if ($continente[0]==$valoreVero) {
							$stringa = ' selected="selected"';	
						}
						if ($continente[0]=='') {
							 $continente[1] = 'qualunque continente';
						}
						echo "<option value=\"".$continente[0]."\"".$stringa.">".$continente[1]."</option>";
					} 
					echo "</select>"; 
                        	} else {
                        		///////////elenco nazioni
					echo "<select ".$classeStr." id=\"".$nome."\" name=\"".$nome."\"><option value=\"\">qualunque nazione</option>";
					foreach($arrayJsNazioni as $nazione) {
							$stringa = '';
							if ($nazione[1]==$valoreVero) {
								$stringa = ' selected="selected"';	
							}
							echo "<option value=\"".$nazione[1]."\"".$stringa.">".$nazione[1]."</option>";
					} 
					echo "</select>";	
                        	}
                        } else {
				// verifico quale interfaccia pubblicare (regioni/province/comuni)
				if (!$valore) {
					///////// SOLO LISTA CONTINENTI, NON SERVE JS
					echo "<select ".$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
					foreach($arrayJsContinenti as $continente) {
						$stringa = '';
						if ($continente[0]==$valoreVero) {
							$stringa = ' selected="selected"';	
						}
						if ($continente[0]=='') {
							 $continente[1] = 'qualunque continente';
						}
						echo "<option value=\"".$continente[0]."\"".$stringa.">".$continente[1]."</option>";
					} 
					echo "</select>"; 					
				} else {				
					///////// LISTA NAZIONI
					// verifico se c'e' un valore 
					if ($valoreVero != '') {
						// trovo la regione di questa provincia
						$valoreRegione = '';
						foreach($arrayJsNazioni as $nazione) {
							if ($nazione[1]==$valoreVero) {
								$valoreContinene = $nazione[0];	
							}							
						}
					}
					$listaNazJs = '';
					foreach($arrayJsNazioni as $nazione) {
						if ($listaNazJs != '') {
							$listaNazJs .= ', ';	
						}
						$listaNazJs .= "[\"".$nazione[0]."\",\"".$nazione[1]."\"]";							
					}
					$listaNazJs = '['.$listaNazJs.']';					
					// codice Js per lista continenti-nazioni
					echo "<script type=\"text/javascript\">
						var nazioni".$nome." = ".$listaNazJs.";
		    				function DropDownList_".$nome."continenti_onchange(v) {
						    //chiave continente
						    var key = v.value;
						    //cancello tutti le nazioni
						    $(\"".$nome."\").options.length = 1;
						    //creo lista nazioni
						    for(var i in nazioni".$nome.") {
						        if(nazioni".$nome."[i][0] == key) {
						            lista_push($(\"".$nome."\"), nazioni".$nome."[i][1], nazioni".$nome."[i][1]);
						        }
						    }
						    $(\"".$nome."\").style.display = 'inline';
						}
					</script>";
					echo "<select ".$classeStr." id=\"".$nome."_LISTACONTINENTI\" name=\"".$nome."_LISTACONTINENTI\"  onchange=\"return DropDownList_".$nome."continenti_onchange(this)\" style=\"display:inline !important;margin:0px 4px 0px 0px;\">";
					foreach($arrayJsContinenti as $continente) {
						$stringa = '';
						if ($continente[0]==$valoreContinene) {
							$stringa = ' selected="selected"';	
						}
						if ($continente[0]=='') {
							 $regione[1] = 'qualunque continente';
						}
						echo "<option value=\"".$continente[0]."\"".$stringa.">".$continente[1]."</option>";
					} 
					echo "</select>"; 
					echo "<select ".$classeStr." id=\"".$nome."\" name=\"".$nome."\" style=\"display:none;\"><option value=\"\">qualunque nazione</option>";
					if ($valoreVero != '') {
						foreach($arrayJsNazioni as $nazione) {
							if ($nazione[0] == $valoreContinene) {
								$stringa = '';
								if ($nazione[1]==$valoreVero) {
									$stringa = ' selected="selected"';	
								}
								echo "<option value=\"".$nazione[1]."\"".$stringa.">".$nazione[1]."</option>";
							}
						} 
					}
					echo "</select>";
					if ($valoreVero != '') {
						echo "<script type=\"text/javascript\">document.getElementById('".$nome."').style.display='inline';</script>";
					}
				} 
			}
?>
