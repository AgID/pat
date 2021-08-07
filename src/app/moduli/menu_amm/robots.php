<?   
function getFileEnte( $path = '', $level = 0 ){ 
	global $elencoFile, $percorso, $dati_db,$database,$idEnteAdmin;

	if ($path != '') {
		$level = 0;
		$ignore = array('.quarantine','.thumbs','.tmb', '.trash', '.', '..'); 
		$dh = @opendir( $path ); 
		 
		while( false !== ( $file = readdir( $dh ) ) ){ 
			
			if( !in_array( $file, $ignore ) ){ 
									
				if( is_dir( "$path/$file" ) ){ 	
					getFileEnte( "$path/$file", ($level+1) ); 				 
				} else { 
					$arrayFileTmp = array();
					//echo "<br />Path: ".$path;
					$arrayFileTmp['path'] =  str_replace($percorso, '' , $path);
					//echo "<br />Path(POST): ".$arrayFileTmp['path'];
					$arrayFileTmp['file'] =  $file;
					
					// ora verifico la presenza in tabella
					$sql = "SELECT id FROM ".$dati_db['prefisso']."etrasp_archiviofile_robots WHERE id_ente_admin=".$idEnteAdmin." AND pathfile='".$arrayFileTmp['path']."/".addslashes($file)."'";
					if( !($result = $database->connessioneConReturn($sql)) ) {
						die("non riesco a prendere i criteri in db: ".$sql);
					}
					$robotsFile=$database->sqlArray($result);
					$arrayFileTmp['escluso'] = 0;					
					if ( isset($robotsFile['id']) AND $robotsFile['id']) {
						$arrayFileTmp['escluso'] = 1;	
					}		
					
					$elencoFile[] = $arrayFileTmp;
				} 				
			} 		 
		} 		 
		closedir( $dh ); 
	}
}


// controllo permessi
if (!$aclTrasparenza['robots']) {
	motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per gestire le esclusioni di indicizzazione.');
} else {
	
	///////////////////// RISPOSTA ALLE AZIONI MULTIPLE
	// se e' settato oggetto oggettiSelezionati, vengo da un form e cambio l'azione
	switch ($azioneSecondaria) {

		////////////////// ESCLUDI //////////
		case "blocca" :
			$blocca = isset($_POST['id_blocca_tabella']) ? $_POST['id_blocca_tabella'] : 0;
			//lognormale($blocca);
			if ($blocca) {
				
				// parso la stringa che mi e' stata inviata per costruire un array
				$arrayOggetti = explode(",", $blocca);
				$numeroOggetti = count($arrayOggetti);
				//lognormale('ArrayOggetti('.$blocca.'): ',$arrayOggetti);
				for ($i=0;$i<$numeroOggetti;$i++) {		
					$strFile = urldecode($arrayOggetti[$i]);
					if ($strFile AND $strFile != '') {
						
						// verifico la presenza del record
						$sql = "SELECT id FROM ".$dati_db['prefisso']."etrasp_archiviofile_robots WHERE id_ente_admin=".$idEnteAdmin." AND pathfile='".addslashes($strFile)."'";
						if( !($result = $database->connessioneConReturn($sql)) ) {
							die("non riesco a prendere i robots in db: ".$sql);
						}
						$robotsFile=$database->sqlArray($result);
						if ( isset($robotsFile['id']) AND $robotsFile['id']) {
							// esclusione presente, al momento non serve fare nulla
						} else {
							$sql = "INSERT INTO ".$dati_db['prefisso']."etrasp_archiviofile_robots (id_ente_admin,pathfile) VALUES (".$idEnteAdmin.",'".addslashes($strFile)."')";
							if ( !($result = $database->connessioneConReturn($sql, BEGIN_TRANSACTION)) ) {
								die('Errore durante aggiunta esclusioni robots.'.$sql);
							}
						}
					}
				}		
				/* /// TODO -- DEVO LOGGARE L'OPERAZIONE
				include_once('classi/log_azione.php');
				$logAzione = new logAzione();
				$listaUsers = '';
				foreach((array) $listaMail as $utLog) {
					$logAzione->aggiungiLog(array('id_utente' => $datiUser['id'], 'azione' => 'bloccoUtente', 'id' => $utLog['id'], 'nomeUtente' => $utLog['nome']));
					$listaUsers .= '<div>['.$utLog['id'].'] '.$utLog['nome'].'</div>';
				}
				if($datiUser['id'] != $enteAdmin['utente_responsabile_trasparenza']) {
					$this->notificaOpUser(($attiva ? 'Attivazione' : 'Blocco'), array('text' => $listaUsers));
				}
				*/				
				
				$operazione = true;
				$operazioneTesto = "Hai escluso con successo ".($numeroOggetti-1)." files.";
			} else {
				$operazione = false;
				$operazioneTesto = "Non hai selezionato nessun file da escludere.";
				$codiceErrore = '#00 - Generico';
			}
			break;

			////////////////// ATTIVA //////////
		case "attiva" :
			$attiva = isset($_POST['id_attiva_tabella']) ? $_POST['id_attiva_tabella'] : 0;
			//lognormale($attiva);
			if ($attiva) {
				
				// parso la stringa che mi e' stata inviata per costruire un array
				$arrayOggetti = explode(",", $attiva);
				$numeroOggetti = count($arrayOggetti);
				for ($i=0;$i<$numeroOggetti;$i++) {		
					$strFile = urldecode($arrayOggetti[$i]);
					if ($strFile AND $strFile != '') {				
						$sql = "DELETE FROM ".$dati_db['prefisso']."etrasp_archiviofile_robots WHERE id_ente_admin=".$idEnteAdmin." AND pathfile ='".addslashes($strFile)."'";
						if ( !($result = $database->connessioneConReturn($sql)) ) { }
						//echo "Errore ".$sql;
					}
				}		
				/* /// TODO -- DEVO LOGGARE L'OPERAZIONE
				include_once('classi/log_azione.php');
				$logAzione = new logAzione();
				$listaUsers = '';
				foreach((array) $listaMail as $utLog) {
					$logAzione->aggiungiLog(array('id_utente' => $datiUser['id'], 'azione' => 'bloccoUtente', 'id' => $utLog['id'], 'nomeUtente' => $utLog['nome']));
					$listaUsers .= '<div>['.$utLog['id'].'] '.$utLog['nome'].'</div>';
				}
				if($datiUser['id'] != $enteAdmin['utente_responsabile_trasparenza']) {
					$this->notificaOpUser(($attiva ? 'Attivazione' : 'Blocco'), array('text' => $listaUsers));
				}
				*/	
				
				$operazione = true;
				$operazioneTesto = "Hai incluso normalmente con successo ".($numeroOggetti-1)." files.";
			} else {
				$operazione = false;
				$operazioneTesto = "Non hai selezionato nessun file da includere.";
				$codiceErrore = '#00 - Generico';
			}
			break;
	}	
	
	$elencoFile = array();
	getFileEnte($percorso,4);
	
	$listaTabella = $elencoFile;
	include ('./app/admin_template/robots/tab_start.tmp');				
	foreach ($listaTabella as $istanzaOggetto) {
		include ('./app/admin_template/robots/tab_row.tmp');
	}
	include ('./app/admin_template/robots/tab_end.tmp');	
	
}



//lognormale('Elenco file',$elencoFile);

?>