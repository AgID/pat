<?php
$executeSql = false;
$verificaApprofondita = false;
$index = $_GET['index'] > 0 ? forzaNumero($_GET['index']) : 0;

$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$idEnte
	." AND (tipologia = '' OR tipologia = 'fornitore singolo') ORDER BY id LIMIT $index,5000";
		

if ( !($result = $database->connessioneConReturn($sql)) ) {
	mostraAvviso(0,'Errore (1): '.$sql);
}
$log = '';

$istanze = $database->sqlArrayAss($result);
$numUpdate = 0;
foreach((array)$istanze as $ist) {
	
	$istanzaOggetto = mostraDatoOggetto($ist['id'], 41, '*');

	if($istanzaOggetto['id'] > 0 and $istanzaOggetto['codice_fiscale'] != '') {
		
		
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$idEnte
			." AND (tipologia = '' OR tipologia = 'fornitore singolo') AND codice_fiscale = '".$istanzaOggetto['codice_fiscale']."' AND id != ".$istanzaOggetto['id']
			." ORDER BY id LIMIT $index,5000";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			mostraAvviso(0,'Errore (1): '.$sql);
		}
		$dups = $database->sqlArrayAss($result);
		
		$i = 0;
		foreach((array)$dups as $dup) {
			
			if($i == 0) {
				$log .= "<br /><br /><strong>Verifica duplicazione per $istanzaOggetto[codice_fiscale] - $istanzaOggetto[nominativo] - $istanzaOggetto[id]</strong> <br />";
				$log .= "[01] - $istanzaOggetto[codice_fiscale] duplicato presente ".(count($dups)+1)." volte<br />";
			}
			if($istanzaOggetto['nominativo'] == $dup['nominativo'] and $verificaApprofondita) {
				$log .= "[01.1] - $istanzaOggetto[codice_fiscale] duplicato anche con ragione sociale: da bonificare. ID = $dup[id].<br />";
				
				/////////
				//VERIFICA PARTECIPANTI
				/////////
				$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente = ".$idEnte
				." AND (elenco_partecipanti = '".$dup['id']."' OR elenco_partecipanti LIKE '".$dup['id'].",%' OR elenco_partecipanti LIKE '%,".$dup['id'].",%' OR elenco_partecipanti LIKE '%,".$dup['id']."')";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					mostraAvviso(0,'Errore (1): '.$sql);
				}
				$partecipanti = $database->sqlArrayAss($result);
				if(count($partecipanti)>0) {
					$log .= "[03] - Ci sono ".count($partecipanti)." gare con il partecipante $dup[nominativo] duplicato presente. ID = $dup[id]<br />";
					foreach((array)$partecipanti as $gara) {
						$log .= "[03.1] - Gara $gara[id] =  $gara[elenco_partecipanti]<br />";
						
						$pars = explode(',',$gara['elenco_partecipanti']);
						$newPars = array();
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
							} else {
								$newPars[] = $r;
							}
						}
						$newPars = implode(',', $newPars);
						$log .= "[03.2] - Gara $gara[id] =  $newPars (bonifica in corso...)<br />";
						$sql = "UPDATE ".$dati_db['prefisso']."oggetto_gare_atti SET elenco_partecipanti = '$newPars' WHERE id = $gara[id]";
						$log .= "[03.3] - $sql<br />";
						if($executeSql) {
							if ( !($result = $database->connessioneConReturn($sql)) ) {
								mostraAvviso(0,'Errore (1): '.$sql);
							}
						}
						$log .= "[03.4] - Gara $gara[id] =  $newPars (bonificata con successo)<br />";
					}
				}
				
				/////////
				//VERIFICA AGGIUDICATARI
				/////////
				$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente = ".$idEnte
				." AND (elenco_aggiudicatari = '".$dup['id']."' OR elenco_aggiudicatari LIKE '".$dup['id'].",%' OR elenco_aggiudicatari LIKE '%,".$dup['id'].",%' OR elenco_aggiudicatari LIKE '%,".$dup['id']."')";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					mostraAvviso(0,'Errore (1): '.$sql);
				}
				$aggiudicatari = $database->sqlArrayAss($result);
				if(count($aggiudicatari)>0) {
					$log .= "[04] - Ci sono ".count($aggiudicatari)." gare con l'aggiudicatario $dup[nominativo] duplicato presente. ID = $dup[id]<br />";
					foreach((array)$aggiudicatari as $gara) {
						$log .= "[04.1] - Gara $gara[id] =  $gara[elenco_aggiudicatari]<br />";
						
						$pars = explode(',',$gara['elenco_aggiudicatari']);
						$newPars = array();
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
							} else {
								$newPars[] = $r;
							}
						}
						$newPars = implode(',', $newPars);
						$log .= "[04.2] - Gara $gara[id] =  $newPars (bonifica in corso...)<br />";
						$sql = "UPDATE ".$dati_db['prefisso']."oggetto_gare_atti SET elenco_aggiudicatari = '$newPars' WHERE id = $gara[id]";
						$log .= "[04.3] - $sql<br />";
						if($executeSql) {
							if ( !($result = $database->connessioneConReturn($sql)) ) {
								mostraAvviso(0,'Errore (1): '.$sql);
							}
						}
						$log .= "[04.4] - Gara $gara[id] =  $newPars (bonificata con successo)<br />";
					}
				}
				
				/////////
				//VERIFICA RTI
				/////////
				$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$idEnte
					." AND tipologia = 'raggruppamento' AND ("
					."  mandante = '".$dup['id']."' OR mandante LIKE '".$dup['id'].",%' OR mandante LIKE '%,".$dup['id'].",%' OR mandante LIKE '%,".$dup['id']."' OR "
					."  mandataria = '".$dup['id']."' OR mandataria LIKE '".$dup['id'].",%' OR mandataria LIKE '%,".$dup['id'].",%' OR mandataria LIKE '%,".$dup['id']."' OR "
					."  associata = '".$dup['id']."' OR associata LIKE '".$dup['id'].",%' OR associata LIKE '%,".$dup['id'].",%' OR associata LIKE '%,".$dup['id']."' OR "
					."  capogruppo = '".$dup['id']."' OR capogruppo LIKE '".$dup['id'].",%' OR capogruppo LIKE '%,".$dup['id'].",%' OR capogruppo LIKE '%,".$dup['id']."' OR "
					."  consorziata = '".$dup['id']."' OR consorziata LIKE '".$dup['id'].",%' OR consorziata LIKE '%,".$dup['id'].",%' OR consorziata LIKE '%,".$dup['id']."'"
					.")";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					mostraAvviso(0,'Errore (1): '.$sql);
				}
				$rtis = $database->sqlArrayAss($result);
				if(count($rtis)>0) {
					$log .= "[05] - Ci sono ".count($rtis)." RTI con il fornitore $dup[nominativo] duplicato presente. ID = $dup[id]<br />";
					foreach((array)$rtis as $rti) {
						$log .= "[05.1] - RTI $rti[id]<br />";
				
						//mandante
						$pars = explode(',',$rti['mandante']);
						$newPars = array();
						$sost = false;
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
								$sost = true;
							} else {
								$newPars[] = $r;
							}
						}
						if($sost) {
							$newPars = implode(',', $newPars);
							$log .= "[05.2] - RTI $rti[id] = $newPars (bonifica in corso...)<br />";
							$sql = "UPDATE ".$dati_db['prefisso']."oggetto_elenco_fornitori SET mandante = '$newPars' WHERE id = $rti[id]";
							$log .= "[05.3] - $sql<br />";
							if($executeSql) {
								if ( !($result = $database->connessioneConReturn($sql)) ) {
									mostraAvviso(0,'Errore (1): '.$sql);
								}
							}
							$log .= "[05.4] - RTI $rti[id] =  $newPars (bonificata con successo)<br />";
						}
						
						//mandataria
						$pars = explode(',',$rti['mandataria']);
						$newPars = array();
						$sost = false;
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
								$sost = true;
							} else {
								$newPars[] = $r;
							}
						}
						if($sost) {
							$newPars = implode(',', $newPars);
							$log .= "[05.5] - RTI $rti[id] = $newPars (bonifica in corso...)<br />";
							$sql = "UPDATE ".$dati_db['prefisso']."oggetto_elenco_fornitori SET mandataria = '$newPars' WHERE id = $rti[id]";
							$log .= "[05.6] - $sql<br />";
							if($executeSql) {
								if ( !($result = $database->connessioneConReturn($sql)) ) {
									mostraAvviso(0,'Errore (1): '.$sql);
								}
							}
							$log .= "[05.7] - RTI $rti[id] =  $newPars (bonificata con successo)<br />";
						}
						
						//associata
						$pars = explode(',',$rti['associata']);
						$newPars = array();
						$sost = false;
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
								$sost = true;
							} else {
								$newPars[] = $r;
							}
						}
						if($sost) {
							$newPars = implode(',', $newPars);
							$log .= "[05.8] - RTI $rti[id] = $newPars (bonifica in corso...)<br />";
							$sql = "UPDATE ".$dati_db['prefisso']."oggetto_elenco_fornitori SET associata = '$newPars' WHERE id = $rti[id]";
							$log .= "[05.9] - $sql<br />";
							if($executeSql) {
								if ( !($result = $database->connessioneConReturn($sql)) ) {
									mostraAvviso(0,'Errore (1): '.$sql);
								}
							}
							$log .= "[05.10] - RTI $rti[id] =  $newPars (bonificata con successo)<br />";
						}
						
						//capogruppo
						$pars = explode(',',$rti['capogruppo']);
						$newPars = array();
						$sost = false;
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
								$sost = true;
							} else {
								$newPars[] = $r;
							}
						}
						if($sost) {
							$newPars = implode(',', $newPars);
							$log .= "[05.11] - RTI $rti[id] = $newPars (bonifica in corso...)<br />";
							$sql = "UPDATE ".$dati_db['prefisso']."oggetto_elenco_fornitori SET capogruppo = '$newPars' WHERE id = $rti[id]";
							$log .= "[05.12] - $sql<br />";
							if($executeSql) {
								if ( !($result = $database->connessioneConReturn($sql)) ) {
									mostraAvviso(0,'Errore (1): '.$sql);
								}
							}
							$log .= "[05.13] - RTI $rti[id] =  $newPars (bonificata con successo)<br />";
						}
						
						//consorziata
						$pars = explode(',',$rti['consorziata']);
						$newPars = array();
						$sost = false;
						foreach((array)$pars as $r) {
							if($r == $dup['id']) {
								$newPars[] = $istanzaOggetto['id'];
								$sost = true;
							} else {
								$newPars[] = $r;
							}
						}
						if($sost) {
							$newPars = implode(',', $newPars);
							$log .= "[05.14] - RTI $rti[id] = $newPars (bonifica in corso...)<br />";
							$sql = "UPDATE ".$dati_db['prefisso']."oggetto_elenco_fornitori SET consorziata = '$newPars' WHERE id = $rti[id]";
							$log .= "[05.15] - $sql<br />";
							if($executeSql) {
								if ( !($result = $database->connessioneConReturn($sql)) ) {
									mostraAvviso(0,'Errore (1): '.$sql);
								}
							}
							$log .= "[05.16] - RTI $rti[id] =  $newPars (bonificata con successo)<br />";
						}
					}
				}
				
					
				//ELIMINAZIONE ELEMENTO BONIFICATO
				$log .= "[06.1] - Eliminazione duplicato ID = $dup[id]<br />";
				$sql = "DELETE FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id = $dup[id]";
				$log .= "[06.2] - $sql<br />";
				if($executeSql) {
					if ( !($result = $database->connessioneConReturn($sql)) ) {
						mostraAvviso(0,'Errore (1): '.$sql);
					}
				}
				$numUpdate++;
				$log .= "[06.3] - Eliminazione duplicato ID = $dup[id] effettuata!<br />";
				
			} else {
				if($i == 0) {
					$log .= "[02] - $istanzaOggetto[codice_fiscale] duplicato ma ragione sociale differente. Non e' possibile bonificare automaticamente, modificare gli elementi e riprovare<br />";
					$log .= "[02.1] - <a target=\"_blank\" href=\"".$server_url."admin__pat.php?menu=pubblicazioni&menusec=fornitori&azione=modifica&id=".$istanzaOggetto[id]."\">[ORIGINALE] Modifica ragione sociale per $istanzaOggetto[nominativo] - $istanzaOggetto[codice_fiscale] - $istanzaOggetto[id]</a><br />";
				}
				$log .= "[02.1] - <a target=\"_blank\" href=\"".$server_url."admin__pat.php?menu=pubblicazioni&menusec=fornitori&azione=modifica&id=".$dup[id]."\">[DUPLICATO] Modifica ragione sociale per $dup[nominativo] - $dup[codice_fiscale] - $dup[id]</a><br />";
				$log .= "[02.2] - <a target=\"_blank\" href=\"".$server_url."__bonifica.php?bonifica=copiaRagSocialeFornitore&from=".$istanzaOggetto[id]."&to=".$dup[id]."\">Copia ORIGINALE su DUPLICATO</a><br />";
				$log .= "[02.2] - <a target=\"_blank\" href=\"".$server_url."__bonifica.php?bonifica=copiaRagSocialeFornitore&from=".$dup[id]."&to=".$istanzaOggetto[id]."\">Copia DUPLICATO su ORIGINALE</a><br />";
			}
			$i++;
		}
		
	}

}
echo $log;

echo "Operazione completata a partire dal record n. $index su $numUpdate/5000 records. <a href=\"__bonifica.php?bonifica=fornitoriDuplicati&index=".($index+5000)."\">Continua con i prossimi 5000</a>";
if(count($istanze) == 0) {
	echo '<div style="color:red;font-weight:bold;margin:10px 0;">Records terminati</div>';
}
?>