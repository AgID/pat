<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015,2017 - AgID Agenzia per l'Italia Digitale
	*
	* Concesso in licenza a norma dell'EUPL, versione 1.1 o
	successive dell'EUPL (la "Licenza")– non appena saranno
	approvate dalla Commissione europea;
	* Non è possibile utilizzare l'opera salvo nel rispetto
	della Licenza.
	* È possibile ottenere una copia della Licenza al seguente
	indirizzo:
	*
	* https://joinup.ec.europa.eu/software/page/eupl
	*
	* Salvo diversamente indicato dalla legge applicabile o
	concordato per iscritto, il software distribuito secondo
	i termini della Licenza è distribuito "TAL QUALE",
	* SENZA GARANZIE O CONDIZIONI DI ALCUN TIPO,
	esplicite o implicite.
	* Si veda la Licenza per la lingua specifica che disciplina
	le autorizzazioni e le limitazioni secondo i termini della
	Licenza.
	*/ 
	/**
	 * @file
	 * pat/controllo_user.php
	 * 
	 * @Descrizione
	 * File di controllo dei profili ACL utente PAT
	 *
	 */	
	 
	 
// funzione di utilita per creazione utenti
function gen_rand_string($hash) {
	
	$chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
	$max_chars = count($chars) - 1;
	srand( (double) microtime()*1000000);
	$rand_str = '';
	for($i = 0; $i < 8; $i++) {
		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
	}
	return ( $hash ) ? md5($rand_str) : $rand_str;
}


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

if ($datiUser['permessi'] == 10) {
	$aclTrasparenza['admin']=1;
	$aclTrasparenza['utenti']=2;
	$aclTrasparenza['ruoli']=2;
	$aclTrasparenza['ealbo_import']=1;
	$aclTrasparenza['archiviomedia']=1;
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
				foreach ($sottoSezioni as $sezioneInterna) { 
					if ($sezioneInterna['id_riferimento']==$sezione['id'] AND $sezioneInterna['permessi_lettura']!='HM' AND $sezioneInterna['permessi_lettura']!='H') {
						$aclTrasparenza['contenuti'][$sezioneInterna['id']]['modifica'] = 1;
						$aclTrasparenza['contenuti'][$sezioneInterna['id']]['workflow'] = 1;
						$aclTrasparenza['contenuti'][$sezioneInterna['id']]['permessi'] = 1;
						$aclTrasparenza['contenuti'][$sezioneInterna['id']]['avanzate'] = 1;
					}
				}
			}
		}
	}
} else {
	// carico i profili di questo utente
	$profiliAcl = caricaAcl($datiUser['acl']);
	foreach ((array)$profiliAcl as $profilo) {	
		// ora controllo le funzionalit� di sistema
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
					foreach ($sottoSezioni as $sezioneInterna) { 
						if ($sezioneInterna['id_riferimento']==$sezione['id'] AND $sezioneInterna['permessi_lettura']!='HM' AND $sezioneInterna['permessi_lettura']!='H') {
							if ($profilo['contenuti'][$sezioneInterna['id']]['modifica'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna['id']]['modifica'] = 1;
							}
							if ($profilo['contenuti'][$sezioneInterna['id']]['workflow'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna['id']]['workflow'] = 1;
							}
							if ($profilo['contenuti'][$sezioneInterna['id']]['permessi'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna['id']]['permessi'] = 1;
							}
							if ($profilo['contenuti'][$sezioneInterna['id']]['avanzate'] == 1) {
								$aclTrasparenza['contenuti'][$sezioneInterna['id']]['avanzate'] = 1;
							}
						}
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
		}
		//permessi sui workflow
		$aclTrasparenza['workflow']['lettura'] = $profilo['gestione_workflow'];
		$aclTrasparenza['workflow']['creazione'] = $profilo['gestione_workflow'];
		$aclTrasparenza['workflow']['modifica'] = $profilo['gestione_workflow'];
		$aclTrasparenza['workflow']['cancellazione'] = $profilo['gestione_workflow'];
	}
}



////////////////// ARCHIVIO MEDIA (CKFINDER)
if ($aclTrasparenza['archiviomedia']) {
	$percorso = $archiviomedia."pat/";
	$_SESSION['KCFINDER'] = array();
	if ($idEnteAdmin) {
		
		if ($aclTrasparenza['archiviomedia'] != 2) {
			$percorso = $archiviomedia.$enteAdmin['nome_breve_ente']."/";
			$percorso2 = $archiviomedia;

			if (!file_exists($percorso)) {
				//echo "la cartella non esiste, la creo";
				mkdir($percorso);		
			}
			$_SESSION['KCFINDER']['types'] = array(
				// CKEditor & FCKEditor types
				$enteAdmin['nome_breve_ente']   =>  ""
			);		
			$_SESSION['KCFINDER']['uploadURL'] = "../../".$percorso2;		
		} else {
			// devo forzare una cartella personale
			$percorso = $archiviomedia.$enteAdmin['nome_breve_ente']."/utente".$datiUser['id']."/";
			//$percorso2 = $enteAdmin['nome_breve_ente']."/utente".$datiUser['id']."/";

			if (!file_exists($percorso)) {
				//echo "la cartella non esiste, la creo";
				mkdir($percorso);		
			}			
			$_SESSION['KCFINDER']['uploadURL'] = "../../".$percorso;		
		}	
	}
	$_SESSION['KCFINDER']['disabled'] = false;	
	$_SESSION['KCFINDER']['uploadDir'] = "";
}

////////// CONTROLLO CODICE SORGENTE EDITOR 

/*
echo "<pre>";
print_r($aclTrasparenza);
echo "</pre>";
*/

?>
