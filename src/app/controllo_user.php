<?
// profilo di permessi standard
$aclTrasparenza = array();
foreach ($arrayFunzioniObj as $funzione) {
	$aclTrasparenza[$funzione] = array(
		'workflow' => 0,
		'lettura' => 0,
		'creazione' => 0,
		'modifica' => 0,
		'cancellazione' => 0,
		'stato' => 0,
		'permessi' => 0,
		'avanzate' => 0,
		'notifiche_push' => 0
	);	
}

if ($datiUser['permessi'] == 10 OR $datiUser['permessi']==3) {
	$aclTrasparenza['admin']=1;
	$aclTrasparenza['utenti']=2;
	$aclTrasparenza['ruoli']=2;
	$aclTrasparenza['ealbo_import']=1;
	$aclTrasparenza['archiviomedia']=1;
	$aclTrasparenza['revisione_pagina']=1;
	foreach ($arrayFunzioniObj as $funzione) {
		$aclTrasparenza[$funzione] = array(
			'workflow' => 1,
			'lettura' => 1,
			'creazione' => 1,
			'modifica' => 1,
			'cancellazione' => 1,
			'stato' => 1,
			'permessi' => 1,
			'avanzate' => 1,
			'notifiche_push' => 1
		);	
	}	
	foreach ($sezioni as $sezione) { 
		if ($sezione['id_riferimento']==18 AND $sezione['permessi_lettura']!='HM' AND $sezione['permessi_lettura']!='H') { 
		
			// sezioni normali e snodo
			$aclTrasparenza['contenuti'][$sezione['id']]['modifica'] = 1;
			$aclTrasparenza['contenuti'][$sezione['id']]['workflow'] = 1;
			$aclTrasparenza['contenuti'][$sezione['id']]['permessi'] = 1;
			$aclTrasparenza['contenuti'][$sezione['id']]['avanzate'] = 1;
		
			$sottoSezioni = controllaSezione($sezione['id']);
			if ($sottoSezioni) {	
				// sottosezioni snodo
				foreach ($sottoSezioni as $sezioneInterna1) { 
					if ($sezioneInterna1['id_riferimento']==$sezione['id'] AND $sezioneInterna1['permessi_lettura']!='HM' AND $sezioneInterna1['permessi_lettura']!='H') {
						$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['modifica'] = 1;
						$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['workflow'] = 1;
						$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['permessi'] = 1;
						$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['avanzate'] = 1;
					}
					foreach ($sezioni as $sezioneInterna2) { 
						if ($sezioneInterna2['id_riferimento']==$sezioneInterna1['id'] AND $sezioneInterna2['permessi_lettura']!='HM' AND $sezioneInterna2['permessi_lettura']!='H') {
							$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['modifica'] = 1;
							$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['workflow'] = 1;
							$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['permessi'] = 1;
							$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['avanzate'] = 1;
						}
					}
				}
			}
		}
		if($sezione['id'] == 605) {
			//privacy
			// sezioni normali e snodo
			$aclTrasparenza['contenuti'][$sezione['id']]['modifica'] = 1;
			$aclTrasparenza['contenuti'][$sezione['id']]['workflow'] = 1;
			$aclTrasparenza['contenuti'][$sezione['id']]['permessi'] = 1;
			$aclTrasparenza['contenuti'][$sezione['id']]['avanzate'] = 1;
			
		}
	}
} else {
	// carico i profili di questo utente
	$profiliAcl = caricaAcl($datiUser['acl']);
	foreach ((array)$profiliAcl as $profilo) {	
		// ora controllo le funzionalità di sistema
		if ($profilo['admin']==1) {
				$aclTrasparenza['admin']=1;
		}
		if ($profilo['ealbo_import']==1) {
				$aclTrasparenza['ealbo_import']=1;
		}
		if ($profilo['utenti'] AND $aclTrasparenza['utenti'] != 2) {
				$aclTrasparenza['utenti']=$profilo['utenti'];
		}
		if ($profilo['ruoli'] AND $aclTrasparenza['ruoli'] != 2) {
				$aclTrasparenza['ruoli'] = $profilo['ruoli'];
		}
		if ($profilo['archiviomedia'] == 1) {
			$aclTrasparenza['archiviomedia'] = $profilo['archiviomedia'];
		} else if ($profilo['archiviomedia'] == 2 AND $aclTrasparenza['archiviomedia'] < 1) {
			$aclTrasparenza['archiviomedia'] = $profilo['archiviomedia'];
		}
		if($profilo['revisione_pagina']) {
			$aclTrasparenza['revisione_pagina']=1;
		}
		/*
		if ($profilo['archiviomedia'] AND $aclTrasparenza['archiviomedia'] != 2) {
				$aclTrasparenza['archiviomedia'] = $profilo['archiviomedia'];
		}
		*/
		// ora deserializzo i permessi oggetto ed eseguo il controllo su quando sono ad 1 (avranno la meglio sugli altri 0)
		foreach ($arrayFunzioniObj as $funzione) {
			$profilo[$funzione] = unserialize($profilo[$funzione]);			
			if ($profilo[$funzione]['workflow']==1) {
				$aclTrasparenza[$funzione]['workflow'] = 1;
			}
			if ($profilo[$funzione]['creazione']==1) {
				$aclTrasparenza[$funzione]['creazione'] = 1;
			}
			if ($profilo[$funzione]['lettura']==1) {
				$aclTrasparenza[$funzione]['lettura'] = 1;
			}
			if ($profilo[$funzione]['modifica']==1) {
				$aclTrasparenza[$funzione]['modifica'] = 1;
			}
			if ($profilo[$funzione]['cancellazione']==1) {
				$aclTrasparenza[$funzione]['cancellazione'] = 1;
			}
			if ($profilo[$funzione]['stato']==1) {
				$aclTrasparenza[$funzione]['stato'] = 1;
			}
			if ($profilo[$funzione]['permessi']==1) {
				$aclTrasparenza[$funzione]['permessi'] = 1;
			}
			if ($profilo[$funzione]['avanzate']==1) {
				$aclTrasparenza[$funzione]['avanzate'] = 1;
			}		
			if ($profilo[$funzione]['notifiche_push']==1) {
				$aclTrasparenza[$funzione]['notifiche_push'] = 1;
			}
		}
		
		// ora deserializzo i permessi delle sezioni ed eseguo i controlli come sopra
		$profilo['contenuti'] = unserialize($profilo['contenuti']);
		foreach ($sezioni as $sezione) { 
			if ($sezione['id_riferimento']==18 AND $sezione['permessi_lettura']!='HM' AND $sezione['permessi_lettura']!='H') { 		
				$sottoSezioni = controllaSezione($sezione['id']);
				if ($sottoSezioni) {	
					// sezioni snodo
					foreach ($sottoSezioni as $sezioneInterna1) { 
						if ($sezioneInterna1['id_riferimento']==$sezione['id'] AND $sezioneInterna1['permessi_lettura']!='HM' AND $sezioneInterna1['permessi_lettura']!='H') {
							if ($profilo['contenuti'][$sezioneInterna1['id']]['modifica'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['modifica'] = 1;
							}
							if ($profilo['contenuti'][$sezioneInterna1['id']]['workflow'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['workflow'] = 1;
							}
							if ($profilo['contenuti'][$sezioneInterna1['id']]['permessi'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['permessi'] = 1;
							}
							if ($profilo['contenuti'][$sezioneInterna1['id']]['avanzate'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna1['id']]['avanzate'] = 1;
							}
						}
						//////////////
						foreach ($sezioni as $sezioneInterna2) { 
							if ($sezioneInterna2['id_riferimento']==$sezioneInterna1['id'] AND $sezioneInterna2['permessi_lettura']!='HM' AND $sezioneInterna2['permessi_lettura']!='H') {
								if ($profilo['contenuti'][$sezioneInterna2['id']]['modifica'] == 1) {
									$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['modifica'] = 1;
								}
								if ($profilo['contenuti'][$sezioneInterna2['id']]['workflow'] == 1) {
									$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['workflow'] = 1;
								}
								if ($profilo['contenuti'][$sezioneInterna2['id']]['permessi'] == 1) {
									$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['permessi'] = 1;
								}
								if ($profilo['contenuti'][$sezioneInterna2['id']]['avanzate'] == 1) {
									$aclTrasparenza['contenuti'][$sezioneInterna2['id']]['avanzate'] = 1;
								}
							}
						}
						//////////////
					}
				} 
				// sezioni normali
				if ($profilo['contenuti'][$sezione['id']]['modifica'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['modifica'] = 1;
				}
				if ($profilo['contenuti'][$sezione['id']]['workflow'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['workflow'] = 1;
				}
				if ($profilo['contenuti'][$sezione['id']]['permessi'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['permessi'] = 1;
				}
				if ($profilo['contenuti'][$sezione['id']]['avanzate'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['avanzate'] = 1;
				}

			}
			if($sezione['id'] == 605) {
				//privacy
			// sezioni normali
				if ($profilo['contenuti'][$sezione['id']]['modifica'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['modifica'] = 1;
				}
				if ($profilo['contenuti'][$sezione['id']]['workflow'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['workflow'] = 1;
				}
				if ($profilo['contenuti'][$sezione['id']]['permessi'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['permessi'] = 1;
				}
				if ($profilo['contenuti'][$sezione['id']]['avanzate'] == 1) {
					$aclTrasparenza['contenuti'][$sezione['id']]['avanzate'] = 1;
				}
					
			}
		}
		//permessi sui workflow
		if(!isset($aclTrasparenza['workflow']['lettura']) or $profilo['gestione_workflow'] > 0) {
			$aclTrasparenza['workflow']['lettura'] = $profilo['gestione_workflow'];
			$aclTrasparenza['workflow']['creazione'] = $profilo['gestione_workflow'];
			$aclTrasparenza['workflow']['modifica'] = $profilo['gestione_workflow'];
			$aclTrasparenza['workflow']['cancellazione'] = $profilo['gestione_workflow'];
		}
	}
}



////////////////// ARCHIVIO MEDIA (CKFINDER)
if ($aclTrasparenza['archiviomedia']) {
	$percorso = $archiviomedia."app/";
	$_SESSION['KCFINDER'] = array();
	if ($idEnteAdmin) {
		
		$_SESSION['KCFINDER']['id_ente'] = $idEnteAdmin;
		$_SESSION['KCFINDER']['id_utente'] = $datiUser['id'];
		$_SESSION['KCFINDER']['username'] = $datiUser['username'];
		$_SESSION['KCFINDER']['ente'] = $enteAdmin['nome_breve_ente'];
		
		if ($aclTrasparenza['archiviomedia'] != 2) {
			$percorso = $archiviomedia.$enteAdmin['nome_breve_ente']."/";
			$percorso2 = $archiviomedia;

			if (!file_exists($percorso)) {
				//echo "la cartella non esiste, la creo";
				mkdir($percorso);
				
			}
			if (!file_exists($percorso.'.trash/') and $configurazione['tipo_filemanager'] == 'elFinder') {
				mkdir($percorso.'.trash/');
			}
			$_SESSION['KCFINDER']['types'] = array(
				// CKEditor & FCKEditor types
				$enteAdmin['nome_breve_ente']   =>  ""
			);		
			$_SESSION['KCFINDER']['uploadURL'] = "../../".$percorso2;
			/*
			if($configurazione['absolute_path_kcfinder']) {
				$_SESSION['KCFINDER']['uploadURL'] = rtrim($enteAdmin['url_etrasparenza'],"/")."/".$percorso2;
			}
			*/
			$_SESSION['KCFINDER']['uploadDir'] = "";
			/*
			if($configurazione['absolute_path_kcfinder']) {
				$_SESSION['KCFINDER']['uploadDir'] = "../../".$percorso2;
			}
			*/
		} else {
			// devo forzare una cartella personale
			$percorso = $archiviomedia.$enteAdmin['nome_breve_ente']."/utente".$datiUser['id']."/";
			//$percorso2 = $enteAdmin['nome_breve_ente']."/utente".$datiUser['id']."/";

			if (!file_exists($percorso)) {
				//echo "la cartella non esiste, la creo";
				mkdir($percorso);		
			}
			if (!file_exists($percorso.'.trash/') and $configurazione['tipo_filemanager'] == 'elFinder') {
				mkdir($percorso.'.trash/');
			}
			$_SESSION['KCFINDER']['uploadURL'] = "../../".$percorso;
			/*
			if($configurazione['absolute_path_kcfinder']) {
				$_SESSION['KCFINDER']['uploadURL'] = rtrim($enteAdmin['url_etrasparenza'],"/")."/".$percorso;
			}
			*/
			$_SESSION['KCFINDER']['uploadDir'] = "";
			/*
			if($configurazione['absolute_path_kcfinder']) {
				$_SESSION['KCFINDER']['uploadDir'] = "../../".$percorso;
			}
			*/
		}	
	}
	$_SESSION['KCFINDER']['disabled'] = false;	
	
}

////////// CONTROLLO CODICE SORGENTE EDITOR 
//lognormale('',$_SESSION['KCFINDER']);
/*
echo "<pre>";
print_r($aclTrasparenza);
echo "</pre>";
*/

?>
