<?
if($istanzaOggetto['stato_workflow'] == 'finale') {
	$elWf = getIstanzaWorkflow($istanzaOggetto['id'], $documento->idOggetto);
	if(!$elWf['id']) {
		setIstanzaWorkflow($istanzaOggetto['id'], $documento->idOggetto);
	}
}
if($istanzaOggetto['stato_workflow'] != 'finale') {
	$elWf = getIstanzaWorkflow($istanzaOggetto['id'], $documento->idOggetto);
	if($elWf['id']) {
		?>
		<div class="elementoInRevisione">L'elemento &egrave; in fase di aggiornamento.</div>
		<?
	}
}
	
if(true) {
    $sorgente = './cache/elezioni_'.$istanzaOggetto['id'].'.tmp';
    
    if(!$datiUser['sessione_loggato'] and file_exists($sorgente) and (mktime()-filemtime($sorgente) < 3600)) {
        $page = file_get_contents($sorgente);
        echo $page;
    } else {
        ob_start();
        
        echo "<h3 class=\"campoOggetto24\"><strong>".$istanzaOggetto['nome']."</strong></h3>";
        
        ?>
    	<div style="position: relative; display: block;" class="oggetto187">
    		<div class="inner_oggetto187">
    			<section>
    				<h3 class="stileTitolo188">
    					<span>Filtri</span>
    				</h3>
    				<div class="oggetto72">
    					<div class="campoOggetto71"><div style="white-space: nowrap; display: inline;"> <label for="eNome" class="labelClass">Nominativo </label><input class="stileForm75" type="text" id="eNome" onkeyup="eNome()" placeholder="qualunque"></div> </div>
                     	<div class="campoOggetto71"><div style="white-space: nowrap; display: inline;"> <label for="eLista" class="labelClass">Lista </label><input class="stileForm75" type="text" id="eLista" onkeyup="eLista()" placeholder="qualunque"></div> </div>
                    </div>
    			</section>
           </div>
    	</div>
    	<script type="text/javascript">
    	function eNome() {
    		// Declare variables
    		var filter, el, e, i, txtValue;
    		filter = document.getElementById('eNome').value.toUpperCase();
    		el = document.getElementsByClassName("candidato_row");
    		// Loop through all list items, and hide those who don't match the search query
    		for (i = 0; i < el.length; i++) {
    			e = el[i].getElementsByClassName('candidato')[0];
    		    txtValue = e.textContent || e.innerText;
    		    if (txtValue.toUpperCase().indexOf(filter) > -1) {
    		    	el[i].style.display = "";
    		    } else {
    		      	el[i].style.display = "none";
    		    }
    		}
    	}
    	function eLista() {
    		// Declare variables
    		var filter, el, e, i, txtValue;
    		filter = document.getElementById('eLista').value.toUpperCase();
    		el = document.getElementsByClassName("lista_div");
    		// Loop through all list items, and hide those who don't match the search query
    		for (i = 0; i < el.length; i++) {
    			e = el[i].getElementsByClassName('lista_name')[0];
    		    txtValue = e.textContent || e.innerText;
    		    if (txtValue.toUpperCase().indexOf(filter) > -1) {
    		    	el[i].style.display = "";
    		    } else {
    		      	el[i].style.display = "none";
    		    }
    		}
    	}
    	</script>
    	<?
    	
    	//Candidati Sindaci/Presidenti
    	$docRif = new documento(75);
    	$criterio = array(
    	    'query' => "id_ente = '{idEnte}' AND ('{idEnte}' != '0' AND '{idEnte}' != '') AND id_elezioni=".$istanzaOggetto['id'],
    	    'query_order' => 'ORDER BY ordine,nome'
    	);
    	$listaDocumenti = $docRif->caricaDocumentiCriterio($criterio);
    	foreach((array)$listaDocumenti as $candidatoPrincipale) {
    	    echo '<div class="candidato_principale_div">';
    	    echo '<h4 class="campoOggetto86 candidato_principale_name"> <strong>'.$candidatoPrincipale['nome'].'</strong> </h4>';
    	    
    	    $um = $candidatoPrincipale['data_creazione'];
    	    if($candidatoPrincipale['ultima_modifica']>0) {
    	        $um = $candidatoPrincipale['ultima_modifica'];
    	    }
    	    echo '<div><i>(ultima modifica: '.date('d/m/Y',$um).')</i></div>';
    	    
    	    $all = prendiListaAllegati($candidatoPrincipale['__id_allegato_istanza']);
    	    foreach((array)$all as $a) {
    	        $posPunto = strrpos($a['file_allegato'], ".");
    	        $estFile = strtolower(substr($a['file_allegato'], ($posPunto +1)));
    	        if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
    	            $estFile = "generica";
    	        }
    	        $grandezza = @filesize($uploadPath."oggetto_allegati/".$a['file_allegato']);
    	        
    	        if (strpos($a['file_allegato'], "O__O")) {
    	            $valoreLabel = (substr($a['file_allegato'], strpos($a['file_allegato'], "O__O") + 4));
    	        } else {
    	            $valoreLabel = ($a['file_allegato']);
    	        }
    	        $um = $a['data_creazione'];
    	        if($a['ultima_modifica']>0) {
    	            $um = $a['ultima_modifica'];
    	        }
    	        echo '<div>'.$a['nome'].': <a href="'.$base_url.'moduli/downloadFile.php?file=oggetto_allegati/'.$temp.urlencode($a['file_allegato']).'">'.$valoreLabel.'</a> ('.date('d/m/Y',$um).' - '.round($grandezza/1000).' kb - '.$estFile.') <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
    	    }
    	    
    	    
    	    //liste
    	    $docRif = new documento(73);
    	    $criterio = array(
    	        'query' => "id_ente = '{idEnte}' AND ('{idEnte}' != '0' AND '{idEnte}' != '') AND id_candidato=".$candidatoPrincipale['id'],
    	        'query_order' => 'ORDER BY ordine,nome'
    	    );
    	    $liste = $docRif->caricaDocumentiCriterio($criterio);
    	    
    	    foreach((array)$liste as $lista) {
    	        echo '<div class="lista_div" style="margin-left: 15px;">';
    	        echo '<h5 class="campoOggetto86 lista_name"> '.$lista['nome'].' </h5>';
    	        
				/********************** ALLEGATI DELLA LISTA ********************/
				$allLista = prendiListaAllegati($lista['__id_allegato_istanza']);
				foreach((array)$allLista as $allegatoLista) {
					$posPunto = strrpos($allegatoLista['file_allegato'], ".");
					$estFile = strtolower(substr($allegatoLista['file_allegato'], ($posPunto +1)));
					if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
						$estFile = "generica";
					}
					$grandezza = @filesize($uploadPath."oggetto_allegati/".$allegatoLista['file_allegato']);
					
					if (strpos($allegatoLista['file_allegato'], "O__O")) {
						$valoreLabel = (substr($allegatoLista['file_allegato'], strpos($allegatoLista['file_allegato'], "O__O") + 4));
					} else {
						$valoreLabel = ($allegatoLista['file_allegato']);
					}
					$um = $allegatoLista['data_creazione'];
					if($a['ultima_modifica']>0) {
						$um = $allegatoLista['ultima_modifica'];
					}
					echo '<div class="campoOggetto48">'.$allegatoLista['nome'].': <a href="'.$base_url.'moduli/downloadFile.php?file=oggetto_allegati/'.$temp.urlencode($allegatoLista['file_allegato']).'">'.$valoreLabel.'</a> ('.date('d/m/Y',$um).' - '.round($grandezza/1000).' kb - '.$estFile.') <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
				}
				
				
    	        echo '<div class="table-responsive"><table class="table table-bordered table-hover vistaTabella" summary="Elezioni trasparenti"><caption>Elezioni trasparenti</caption><tbody>';
    	        echo '<tr><th scope="col">Candidato</th><th scope="col">Allegati</th></tr>';
    	        
    	        //Candidati
    	        $docRif = new documento(74);
    	        $criterio = array(
    	            'query' => "id_ente = '{idEnte}' AND ('{idEnte}' != '0' AND '{idEnte}' != '') AND id_lista=".$lista['id'],
    	            'query_order' => 'ORDER BY ordine,nome'
    	        );
    	        $candidati = $docRif->caricaDocumentiCriterio($criterio);
    	        
    	        
    	        foreach((array)$candidati as $candidato) {
    	            $um = $candidato['data_creazione'];
    	            if($candidato['ultima_modifica']>0) {
    	                $um = $candidato['ultima_modifica'];
    	            }
    	            echo '<tr class="candidato_row"><td class="candidato">'.$candidato['nome'].'<br /><i>(ultima modifica: '.date('d/m/Y',$um).')</i></td><td>';
    	            
    	            $all = prendiListaAllegati($candidato['__id_allegato_istanza']);
    	            foreach((array)$all as $a) {
    	                $posPunto = strrpos($a['file_allegato'], ".");
    	                $estFile = strtolower(substr($a['file_allegato'], ($posPunto +1)));
    	                if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
    	                    $estFile = "generica";
    	                }
    	                $grandezza = @filesize($uploadPath."oggetto_allegati/".$a['file_allegato']);
    	                
    	                if (strpos($a['file_allegato'], "O__O")) {
    	                    $valoreLabel = (substr($a['file_allegato'], strpos($a['file_allegato'], "O__O") + 4));
    	                } else {
    	                    $valoreLabel = ($a['file_allegato']);
    	                }
    	                $um = $a['data_creazione'];
    	                if($a['ultima_modifica']>0) {
    	                    $um = $a['ultima_modifica'];
    	                }
    	                echo '<div>'.$a['nome'].': <a href="'.$base_url.'moduli/downloadFile.php?file=oggetto_allegati/'.$temp.urlencode($a['file_allegato']).'">'.$valoreLabel.'</a> ('.date('d/m/Y',$um).' - '.round($grandezza/1000).' kb - '.$estFile.') <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
    	            }
    	            
    	            echo '</td></tr>';
    	        }
    	        
    	        
    	        echo '</tbody></table></div>';
    	        
    	        echo '</div>';  //chiudo lista_div
    	    }
    	    
    	    
    	    echo '</div';  //chiudo candidato_principale_div
    	}
    
    	/*
    	//Candidati
    	$docRif = new documento(74);
    	$criterio = array(
    	    'query' => "id_ente = '{idEnte}' AND ('{idEnte}' != '0' AND '{idEnte}' != '') AND id_elezioni=".$istanzaOggetto['id'],
    	    'query_order' => 'ORDER BY id_lista,nome'
    	);
    	$listaDocumenti = $docRif->caricaDocumentiCriterio($criterio);
    	
    	$liste = array();
    	$listaCandidati = array();
    	$liste[] = -1;
    	foreach((array)$listaDocumenti as $c) {
    	    if(!in_array($c['id_lista'], $liste)) {
    	        $liste[] = $c['id_lista'];
    	    }
    	    $listaCandidati[$c['id_lista']][] = $c;
    	}
    	
    	//Liste
    	$docRif = new documento(73);
    	$criterio = array(
    	    'query' => "id_ente = '{idEnte}' AND ('{idEnte}' != '0' AND '{idEnte}' != '') AND id IN (".implode(',',$liste).")",
    	    'query_order' => 'ORDER BY nome'
    	);
    	$listaDocumenti = $docRif->caricaDocumentiCriterio($criterio);
    	*/
    	foreach((array)$listaDocumenti as $lista) {
    	    if(count($listaCandidati[$lista['id']]) > 0) {
    	        
    	        echo '<div class="lista_div">';
    	        echo '<h4 class="campoOggetto86 lista_name"> '.$lista['nome'].' </h4>';
    	        
    	        echo '<div class="table-responsive"><table class="table table-bordered table-hover vistaTabella" summary="Elezioni trasparenti"><caption>Elezioni trasparenti</caption><tbody>';
    	        echo '<tr><th scope="col">Candidato</th><th scope="col">Allegati</th></tr>';
    	        
    	        foreach((array)$listaCandidati[$lista['id']] as $candidato) {
    	            $um = $candidato['data_creazione'];
    	            if($candidato['ultima_modifica']>0) {
    	                $um = $candidato['ultima_modifica'];
    	            }
    	            echo '<tr class="candidato_row"><td class="candidato">'.$candidato['nome'].'<br /><i>(ultima modifica: '.date('d/m/Y',$um).')</i></td><td>';
    	            
    	            $all = prendiListaAllegati($candidato['__id_allegato_istanza']);
    	            foreach((array)$all as $a) {
    	                $posPunto = strrpos($a['file_allegato'], ".");
    	                $estFile = strtolower(substr($a['file_allegato'], ($posPunto +1)));
    	                if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
    	                    $estFile = "generica";
    	                }
    	                $grandezza = @filesize($uploadPath."oggetto_allegati/".$a['file_allegato']);
    	                
    	                if (strpos($a['file_allegato'], "O__O")) {
    	                    $valoreLabel = (substr($a['file_allegato'], strpos($a['file_allegato'], "O__O") + 4));
    	                } else {
    	                    $valoreLabel = ($a['file_allegato']);
    	                }
    	                $um = $a['data_creazione'];
    	                if($a['ultima_modifica']>0) {
    	                    $um = $a['ultima_modifica'];
    	                }
    	                echo '<div>'.$a['nome'].': <a href="'.$base_url.'moduli/downloadFile.php?file=oggetto_allegati/'.$temp.urlencode($a['file_allegato']).'">'.$valoreLabel.'</a> ('.date('d/m/Y',$um).' - '.round($grandezza/1000).' kb - '.$estFile.') <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
    	            }
    	            
    	            echo '</td></tr>';
    	        }
    	        
    	        echo '</tbody></table></div></div>';
    	    }
    	}
    	
    	
    	//visualizzaAllegatiDinamici($istanzaOggetto);
    	
    	echo '<div class="reset"></div>';
    	
    	//visualizzaDataAggiornamento($istanzaOggetto);
    	
    	echo '<div class="reset"></div>';
    	
        
    	$page = ob_get_contents();
    	ob_end_clean();
    	
    	file_put_contents($sorgente, $page);
    	echo $page;
        
    }
	
}
?>