<?
class ruoli {
    
    var $sezioni = array();
    
    // costruttore
    function ruoli() {
        global $dati_db,$database,$configurazione;
        
        $dati = "*";
        $sql = "SELECT ".$dati." FROM ".$dati_db['prefisso']."sezioni ORDER by priorita";
        if( !($result = $database->connessioneConReturn($sql)) ) {
            die("non riesco a prendere le informazioni sulle sezioni in db.".$sql);
        }
        $this->sezioni=$database->sqlArrayAss($result);
    }
    
    function controllaSezione($id) {
        
        $superCategoria = 0;
        $arraySottosezioni = array();
        foreach ($this->sezioni as $sezione) {
            if ($sezione['id_riferimento'] == $id) {
                $superCategoria = 1;
                $arraySottosezioni[] = $sezione;
            }
        }
        if ($superCategoria) {
            return $arraySottosezioni;
        } else {
            return FALSE;
        }
        
    }

	
	function caricaRuoli($idEnte) {
		global $database, $dati_db;
		
		if ($idEnte) {
			$condizione = " WHERE id_ente=".$idEnte." OR id_ente=0";
		}

		$sql="SELECT * FROM ".$dati_db['prefisso']."etrasp_ruoli".$condizione." ORDER BY id_ente, nome";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("errore in carica ruoli. ".$sql);
		}
		
		$lista = $database->sqlArrayAss($result);
		return $lista;

	}

	// funzione di caricamento singolo RUOLO
	function caricaRuolo($id) {
		global $database, $dati_db;

		$sql="SELECT * FROM ".$dati_db['prefisso']."etrasp_ruoli WHERE id=$id";
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso caricare dati ruoli'.$sql);
		}

		$ente = $database->sqlArray($result);

		return $ente;

	}
	
	
	////////////////////// FUNZIONE DI AGGIUNTA DI UN RUOLO//////////////////////////

	function aggiungiRuolo($arrayValori) {
		global $dati_db,$database,$datiUser,$sezioni,$arrayFunzioniObj;
		
		// devo analizzare e mettere insieme i dati relativi ad i permessi (OGGETTI)
		$permessiOggetto = array();
		foreach ($arrayFunzioniObj as $funzione) {
		
			$permessiOggetto[$funzione] = array(
				'workflow' => $arrayValori[$funzione."_workflow"]== 1 ? $arrayValori[$funzione."_workflow"] : 0,
				'lettura' => $arrayValori[$funzione."_lettura"] == 1 ? $arrayValori[$funzione."_lettura"] : 0,
				'creazione' => $arrayValori[$funzione."_creazione"]== 1 ? $arrayValori[$funzione."_creazione"] : 0,
				'modifica' => $arrayValori[$funzione."_modifica"]== 1 ? $arrayValori[$funzione."_modifica"] : 0,
				'cancellazione' => $arrayValori[$funzione."_cancellazione"]== 1 ? $arrayValori[$funzione."_cancellazione"] : 0,
				'stato' => $arrayValori[$funzione."_stato"]== 1 ? $arrayValori[$funzione."_stato"] : 0,
				'permessi' => $arrayValori[$funzione."_permessi"]== 1 ? $arrayValori[$funzione."_permessi"] : 0,
				'avanzate' => $arrayValori[$funzione."_avanzate"]== 1 ? $arrayValori[$funzione."_avanzate"] : 0,
				'notifiche_push' => $arrayValori[$funzione."_notifiche_push"]== 1 ? $arrayValori[$funzione."_notifiche_push"] : 0
			);		
			
			// serializzo gli array del permesso di funzione specifico		
			$arrayValori[$funzione]	= addslashes(serialize($permessiOggetto[$funzione]));		
		}
		/*
		echo "<pre>";
		print_r($arrayValori);
		echo "</pre>";
		*/		
		// devo analizzare e mettere insieme i dati relativi alle sezioni (CONTENUTI)
		$permessiSezione = array();
		foreach ($sezioni as $sezione) { 
			if ($sezione['id_riferimento']==18 AND $sezione['permessi_lettura']!='HM' AND $sezione['permessi_lettura']!='H') {
			
				$permessiSezione[$sezione['id']] = array(
					'modifica' => $arrayValori["sez_modifica_".$sezione['id']] == 1 ? $arrayValori["sez_modifica_".$sezione['id']] : 0,
					'workflow' => $arrayValori["sez_workflow_".$sezione['id']] == 1 ? $arrayValori["sez_workflow_".$sezione['id']] : 0,
					'permessi' => $arrayValori["sez_permessi_".$sezione['id']] == 1 ? $arrayValori["sez_permessi_".$sezione['id']] : 0,
					'avanzate' => $arrayValori["sez_avanzate_".$sezione['id']] == 1 ? $arrayValori["sez_avanzate_".$sezione['id']] : 0
				);
			
				if ($this->controllaSezione($sezione['id'])) {
					// elaboro sotosezioni
					foreach ($sezioni as $sezioneInterna1) {
						if ($sezioneInterna1['id_riferimento']==$sezione['id'] AND $sezioneInterna1['permessi_lettura']!='HM' AND $sezioneInterna1['permessi_lettura']!='H') {
							$permessiSezione[$sezioneInterna1['id']] = array(
								'modifica' => $arrayValori["sez_modifica_".$sezioneInterna1['id']] == 1 ? $arrayValori["sez_modifica_".$sezioneInterna1['id']] : 0,
								'workflow' => $arrayValori["sez_workflow_".$sezioneInterna1['id']] == 1 ? $arrayValori["sez_workflow_".$sezioneInterna1['id']] : 0,
								'permessi' => $arrayValori["sez_permessi_".$sezioneInterna1['id']] == 1 ? $arrayValori["sez_permessi_".$sezioneInterna1['id']] : 0,
								'avanzate' => $arrayValori["sez_avanzate_".$sezioneInterna1['id']] == 1 ? $arrayValori["sez_avanzate_".$sezioneInterna1['id']] : 0
							);
						}
						//////////////////
						if ($this->controllaSezione($sezioneInterna1['id'])) {
							// elaboro sotosezioni
							foreach ($sezioni as $sezioneInterna2) { 
								if ($sezioneInterna2['id_riferimento']==$sezioneInterna1['id'] AND $sezioneInterna2['permessi_lettura']!='HM' AND $sezioneInterna2['permessi_lettura']!='H') {
									$permessiSezione[$sezioneInterna2['id']] = array(
										'modifica' => $arrayValori["sez_modifica_".$sezioneInterna2['id']] == 1 ? $arrayValori["sez_modifica_".$sezioneInterna2['id']] : 0,
										'workflow' => $arrayValori["sez_workflow_".$sezioneInterna2['id']] == 1 ? $arrayValori["sez_workflow_".$sezioneInterna2['id']] : 0,
										'permessi' => $arrayValori["sez_permessi_".$sezioneInterna2['id']] == 1 ? $arrayValori["sez_permessi_".$sezioneInterna2['id']] : 0,
										'avanzate' => $arrayValori["sez_avanzate_".$sezioneInterna2['id']] == 1 ? $arrayValori["sez_avanzate_".$sezioneInterna2['id']] : 0
									);
								
								}
							}
						
						} 
						//////////////////
					}
				} 
			}
		}
		/*
		echo "<pre>";
		print_r($permessiSezione);
		echo "</pre>";
		*/
		// serializzo gli array del permesso di funzione specifico		
		$arrayValori['contenuti']	= addslashes(serialize($permessiSezione));	
		
		$arrayValori['admin'] = $arrayValori['admin'] == 1 ? $arrayValori['admin'] : 0;
		$arrayValori['ealbo_import'] = $arrayValori['ealbo_import'] == 1 ? $arrayValori['ealbo_import'] : 0;
		$arrayValori['gestione_workflow'] = $arrayValori['gestione_workflow'] == 1 ? $arrayValori['gestione_workflow'] : 0;
		
		if (isset($arrayValori['ruolo_sistema']) and $arrayValori['ruolo_sistema']==1) {
			$arrayValori['id_ente'] = 0;
		}
		
		// creo la query di installazione nuovo ruolo
		$sql = "INSERT INTO ".$dati_db['prefisso']."etrasp_ruoli (
					id_ente,nome,descrizione,admin,utenti,ruoli,archiviomedia,gestione_workflow,accessocivico,
					strutture,personale,tassiassenza,commissioni,societa,procedimenti,elezioni,elezioni_liste,elezioni_candidati,elezioni_candidati_sindaci,
					regolamenti,modulistica,normativa,bilanci,fornitori,stazioni,bandigara,avcp,atti_programmazione,bandiatti,
					bandiconcorso,sovvenzioni,incarichi,provvedimenti,oneri,patrimonio_immobiliare,canoni_locazione,controlli_rilievi,
					soggetti_esterni,rischi,misure,rotazione,direttive,piani,programmazione3,revisione_pagina,
					contenuti,speciali,ealbo_import
					) VALUES (
					".$arrayValori['id_ente'].",'".addslashes($arrayValori['nome'])."','".addslashes($arrayValori['descrizione'])."',".$arrayValori['admin'].",".$arrayValori['utenti'].",".$arrayValori['ruoli'].",".$arrayValori['archiviomedia'].",".$arrayValori['gestione_workflow'].",'".$arrayValori['accessocivico']."',
					'".$arrayValori['strutture']."','".$arrayValori['personale']."','".$arrayValori['tassiassenza']."','".$arrayValori['commissioni']."','".$arrayValori['societa']."','".$arrayValori['procedimenti']."','".$arrayValori['elezioni']."','".$arrayValori['elezioni_liste']."','".$arrayValori['elezioni_candidati']."','".$arrayValori['elezioni_candidati_sindaci']."',
					'".$arrayValori['regolamenti']."','".$arrayValori['modulistica']."','".$arrayValori['normativa']."','".$arrayValori['bilanci']."','".$arrayValori['fornitori']."','".$arrayValori['stazioni']."','".$arrayValori['bandigara']."','".$arrayValori['avcp']."','".$arrayValori['atti_programmazione']."','".$arrayValori['bandiatti']."',
					'".$arrayValori['bandiconcorso']."','".$arrayValori['sovvenzioni']."','".$arrayValori['incarichi']."','".$arrayValori['provvedimenti']."','".$arrayValori['oneri']."','".$arrayValori['patrimonio_immobiliare']."','".$arrayValori['canoni_locazione']."','".$arrayValori['controlli_rilievi']."',
					'".$arrayValori['soggetti_esterni']."','".$arrayValori['rischi']."','".$arrayValori['misure']."','".$arrayValori['rotazione']."','".$arrayValori['direttive']."','".$arrayValori['piani']."','".$arrayValori['programmazione3']."',".$arrayValori['revisione_pagina'].",
					'".$arrayValori['contenuti']."','".$arrayValori['speciali']."',".$arrayValori['ealbo_import']."
					)";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die('Non posso installare ruolo'.$sql);
		} else {
			return TRUE;
		}
		
	}
	
	////////////////////// FUNZIONE DI MODIFICA DI UN RUOLO//////////////////////////

	function modificaRuolo($idRuolo, $arrayValori) {
		global $dati_db,$database,$datiUser,$sezioni,$arrayFunzioniObj;
		
		// devo analizzare e mettere insieme i dati relativi ad i permessi (OGGETTI)
		$permessiOggetto = array();
		foreach ($arrayFunzioniObj as $funzione) {
		
			$permessiOggetto[$funzione] = array(
				'workflow' => $arrayValori[$funzione."_workflow"]== 1 ? $arrayValori[$funzione."_workflow"] : 0,
				'lettura' => $arrayValori[$funzione."_lettura"]== 1 ? $arrayValori[$funzione."_lettura"] : 0,
				'creazione' => $arrayValori[$funzione."_creazione"]== 1 ? $arrayValori[$funzione."_creazione"] : 0,
				'modifica' => $arrayValori[$funzione."_modifica"]== 1 ? $arrayValori[$funzione."_modifica"] : 0,
				'cancellazione' => $arrayValori[$funzione."_cancellazione"]== 1 ? $arrayValori[$funzione."_cancellazione"] : 0,
				'stato' => $arrayValori[$funzione."_stato"]== 1 ? $arrayValori[$funzione."_stato"] : 0,
				'permessi' => $arrayValori[$funzione."_permessi"]== 1 ? $arrayValori[$funzione."_permessi"] : 0,
				'avanzate' => $arrayValori[$funzione."_avanzate"]== 1 ? $arrayValori[$funzione."_avanzate"] : 0,
				'notifiche_push' => $arrayValori[$funzione."_notifiche_push"]== 1 ? $arrayValori[$funzione."_notifiche_push"] : 0
			);		
			
			// serializzo gli array del permesso di funzione specifico		
			$arrayValori[$funzione]	= addslashes(serialize($permessiOggetto[$funzione]));		
		}
		
		// devo analizzare e mettere insieme i dati relativi alle sezioni (CONTENUTI)
		$permessiSezione = array();
		foreach ($sezioni as $sezione) { 
			if ($sezione['id_riferimento']==18 AND $sezione['permessi_lettura']!='HM' AND $sezione['permessi_lettura']!='H') { 	

				$permessiSezione[$sezione['id']] = array(
					'modifica' => $arrayValori["sez_modifica_".$sezione['id']]== 1 ? $arrayValori["sez_modifica_".$sezione['id']] : 0,
					'workflow' => $arrayValori["sez_workflow_".$sezione['id']]== 1 ? $arrayValori["sez_workflow_".$sezione['id']] : 0,
					'permessi' => $arrayValori["sez_permessi_".$sezione['id']]== 1 ? $arrayValori["sez_permessi_".$sezione['id']] : 0,
					'avanzate' => $arrayValori["sez_avanzate_".$sezione['id']]== 1 ? $arrayValori["sez_avanzate_".$sezione['id']] : 0
				);
			
				if ($this->controllaSezione($sezione['id'])) {
				
					foreach ($sezioni as $sezioneInterna1) { 
						if ($sezioneInterna1['id_riferimento']==$sezione['id'] AND $sezioneInterna1['permessi_lettura']!='HM' AND $sezioneInterna1['permessi_lettura']!='H') {
							$permessiSezione[$sezioneInterna1['id']] = array(
								'modifica' => $arrayValori["sez_modifica_".$sezioneInterna1['id']]== 1 ? $arrayValori["sez_modifica_".$sezioneInterna1['id']] : 0,
								'workflow' => $arrayValori["sez_workflow_".$sezioneInterna1['id']]== 1 ? $arrayValori["sez_workflow_".$sezioneInterna1['id']] : 0,
								'permessi' => $arrayValori["sez_permessi_".$sezioneInterna1['id']]== 1 ? $arrayValori["sez_permessi_".$sezioneInterna1['id']] : 0,
								'avanzate' => $arrayValori["sez_avanzate_".$sezioneInterna1['id']]== 1 ? $arrayValori["sez_avanzate_".$sezioneInterna1['id']] : 0
							);
						
						}
						//////////////////////
						if ($this->controllaSezione($sezioneInterna1['id'])) {
						
							foreach ($sezioni as $sezioneInterna2) { 
								if ($sezioneInterna2['id_riferimento']==$sezioneInterna1['id'] AND $sezioneInterna2['permessi_lettura']!='HM' AND $sezioneInterna2['permessi_lettura']!='H') {
									$permessiSezione[$sezioneInterna2['id']] = array(
										'modifica' => $arrayValori["sez_modifica_".$sezioneInterna2['id']]== 1 ? $arrayValori["sez_modifica_".$sezioneInterna2['id']] : 0,
										'workflow' => $arrayValori["sez_workflow_".$sezioneInterna2['id']]== 1 ? $arrayValori["sez_workflow_".$sezioneInterna2['id']] : 0,
										'permessi' => $arrayValori["sez_permessi_".$sezioneInterna2['id']]== 1 ? $arrayValori["sez_permessi_".$sezioneInterna2['id']] : 0,
										'avanzate' => $arrayValori["sez_avanzate_".$sezioneInterna2['id']]== 1 ? $arrayValori["sez_avanzate_".$sezioneInterna2['id']] : 0
									);
								
								}
							}
						
						} 
						//////////////////////
					}
				
				} 
			}
			if($sezione['id'] == 605) {
				//privacy
				$permessiSezione[$sezione['id']] = array(
						'modifica' => $arrayValori["sez_modifica_".$sezione['id']]== 1 ? $arrayValori["sez_modifica_".$sezione['id']] : 0,
						'workflow' => $arrayValori["sez_workflow_".$sezione['id']]== 1 ? $arrayValori["sez_workflow_".$sezione['id']] : 0,
						'permessi' => $arrayValori["sez_permessi_".$sezione['id']]== 1 ? $arrayValori["sez_permessi_".$sezione['id']] : 0,
						'avanzate' => $arrayValori["sez_avanzate_".$sezione['id']]== 1 ? $arrayValori["sez_avanzate_".$sezione['id']] : 0
				);
			}
		}
		
		/*
		echo "<pre>";
		print_r($permessiOggetto);
		echo "</pre>";
		*/
		
		// serializzo gli array del permesso di funzione specifico		
		$arrayValori['contenuti']	= addslashes(serialize($permessiSezione));	
		$arrayValori['admin'] = $arrayValori['admin'] == 1 ? $arrayValori['admin'] : 0;
		$arrayValori['ealbo_import'] = $arrayValori['ealbo_import'] == 1 ? $arrayValori['ealbo_import'] : 0;
		$arrayValori['gestione_workflow'] = $arrayValori['gestione_workflow'] == 1 ? $arrayValori['gestione_workflow'] : 0;
		
		// modifico il ruolo scelto
		$strQuery = "nome='".addslashes($arrayValori['nome'])."',descrizione='".addslashes($arrayValori['descrizione'])."',admin=".$arrayValori['admin'].",utenti=".$arrayValori['utenti'].",accessocivico='".$arrayValori['accessocivico']."',
				ruoli=".$arrayValori['ruoli'].",archiviomedia=".$arrayValori['archiviomedia'].",gestione_workflow=".$arrayValori['gestione_workflow'].",strutture='".$arrayValori['strutture']."',personale='".$arrayValori['personale']."',tassiassenza='".$arrayValori['tassiassenza']."',commissioni='".$arrayValori['commissioni']."',societa='".$arrayValori['societa']."',
				procedimenti='".$arrayValori['procedimenti']."',regolamenti='".$arrayValori['regolamenti']."',modulistica='".$arrayValori['modulistica']."',
                elezioni='".$arrayValori['elezioni']."',elezioni_liste='".$arrayValori['elezioni_liste']."',elezioni_candidati='".$arrayValori['elezioni_candidati']."',elezioni_candidati_sindaci='".$arrayValori['elezioni_candidati_sindaci']."',
				normativa='".$arrayValori['normativa']."',bilanci='".$arrayValori['bilanci']."',fornitori='".$arrayValori['fornitori']."',stazioni='".$arrayValori['stazioni']."',bandigara='".$arrayValori['bandigara']."',avcp='".$arrayValori['avcp']."',atti_programmazione='".$arrayValori['atti_programmazione']."',bandiatti='".$arrayValori['bandiatti']."',
				bandiconcorso='".$arrayValori['bandiconcorso']."',sovvenzioni='".$arrayValori['sovvenzioni']."',incarichi='".$arrayValori['incarichi']."',
				provvedimenti='".$arrayValori['provvedimenti']."',oneri='".$arrayValori['oneri']."',patrimonio_immobiliare='".$arrayValori['patrimonio_immobiliare']."',canoni_locazione='".$arrayValori['canoni_locazione']."',controlli_rilievi='".$arrayValori['controlli_rilievi']."',
				soggetti_esterni='".$arrayValori['soggetti_esterni']."',rischi='".$arrayValori['rischi']."',misure='".$arrayValori['misure']."',rotazione='".$arrayValori['rotazione']."',direttive='".$arrayValori['direttive']."',piani='".$arrayValori['piani']."',programmazione3='".$arrayValori['programmazione3']."',
				contenuti='".$arrayValori['contenuti']."',speciali='".$arrayValori['speciali']."',ealbo_import=".$arrayValori['ealbo_import'].",revisione_pagina=".$arrayValori['revisione_pagina'];

		
		$sql = "UPDATE ".$dati_db['prefisso']."etrasp_ruoli SET ".$strQuery." WHERE id = ".$idRuolo;
		
		//echo "Modifica ruolo: ".$sql;
		
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso aggiornare ruolo'.$sql);
		} else {
			return TRUE;
		}
	
	}
	
	// funzione di cancellazione rss (AZIONE MULTIPLA)
	function cancellaRuoli($stringaOggetti) {
		global $dati_db,$database;

		// parso la stringa che mi e' stata inviata per costruire un array
		$arrayOggetti = explode(",", $stringaOggetti);
		$numeroOggetti = count($arrayOggetti);

		// creo la condizione per la cancellazione
		$condizione = 'id='.$arrayOggetti[0];
		for ($i=1;$i<$numeroOggetti;$i++) {
			$condizione .= ' or id='.$arrayOggetti[$i];
		}
		// cancello gli oggetti segnalati
		$sql = "DELETE FROM ".$dati_db['prefisso']."etrasp_ruoli WHERE ".$condizione;
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso cancellare ruoli : '.$sql);
		}
		return $numeroOggetti;
	}
}
?>