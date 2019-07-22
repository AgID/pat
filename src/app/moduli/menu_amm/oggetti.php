<?
//imposto i seguenti valori in post se non presenti
$_POST['id_atto_albo'] = ($_POST['id_atto_albo'] ? forzaNumero($_POST['id_atto_albo']) : 0);
$_POST['stato_pubblicazione'] = ($_POST['stato_pubblicazione'] ? forzaNumero($_POST['stato_pubblicazione']) : '100');

// verifico di aver richiamato un oggetto 
if ($funzioneSottoMenu['idOggetto']) {
	$idOggetto = $funzioneSottoMenu['idOggetto'];
}

// importo la classe di amministrazione oggetti
require_once('classi/admin_oggetti.php');
$oggOgg = new oggettiAdmin($idOggetto);

if(file_exists('app/moduli/menu_amm/operazioni/oggetti/'.$menuSecondario.'.php')) {
	$opOgg = $menuSecondario;
} else {
	$opOgg = 'OperazioneDefault';
}
require('app/moduli/menu_amm/operazioni/oggetti/'.$opOgg.'.php');
$opOgg = new $opOgg();

switch ($azione) {

	//////////////////LISTA RECORD DI OGGETTO///////

	case "lista" :
		if (!$aclTrasparenza[$menuSecondario]['lettura'] and !$aclTrasparenza[$menuSecondario]['creazione']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per visualizzare questo archivio.');
		} else {
	
			///////////////////// RISPOSTA ALLE AZIONI FORM //////////////////////////////
			$codiceErrore = '';
			
			// aggiunta oggetto
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'aggiungi') {
			
				$idPerAllegati = date('yzGis');
				$prog = 0;	
				$operazione = true;
				
				// controllo se nei campi personalizzati ci sono dei file
				foreach ($oggOgg->struttura as $campoTemp) {
					if (strpos($campoTemp['tipocampo'],'*') !== false) {
						$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
					}			
					if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') {
						//campo di tipo file
						if($_FILES[$campoTemp['nomecampo']]['tmp_name'] != '') {
							// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
							$nomeFileReplace = str_replace("\'", "_", $_FILES[$campoTemp['nomecampo']]['name']);
							$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
							if (!(copy($_FILES[$campoTemp['nomecampo']]['tmp_name'], $uploadPath.$oggOgg->tabellaOggetto."/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
								// ERRORI NELL'OPERAZIONE
								$operazione = false;
								$operazioneTesto = "Problemi in aggiunta del file ".$_FILES[$campoTemp['nomecampo']]['tmp_name'].". Riprovare in seguito.";
								$codiceErrore = '#01 - File Upload';					
							}  
							$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
							$prog++;
						}
					}
				}		
				
				// devo tradurre i campi riferimento
				foreach ($oggOgg->struttura as $campoTemp) {
					if (strpos($campoTemp['tipocampo'],'*') !== false) {
						$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
					}			
					if ($campoTemp['tipocampo'] == 'campoggetto_multi' and is_array($_POST[$campoTemp['nomecampo']])) {
						$_POST[$campoTemp['nomecampo']] = implode(",", $_POST[$campoTemp['nomecampo']]);
					}
				}
				
				if ($operazione) {
					$idCategoriaPasso=$idCategoria;
					if ($scegliCategoria and isset($_POST['scelta_categoria']) and $oggOgg->idCategoria) {
						$idCategoriaPasso=forzaNumero($_POST['scelta_categoria']);
					}
					
					//workflow
					$_POST['stato_workflow'] = forzaStringa($_POST['stato_workflow_da_assegnare']);
					if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow']) {
						//verificare se esiste un workflow iniziale per l'utente loggato
						$wf = $oggOgg->caricaWorkflowUtenteIniziale();
						if($wf['id']) {
							$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
							//sono in inserimento => lo stato corrente è lo stato 0
							foreach((array)$statiWf as $statoWf) {
							    if($statoWf['id'] == forzaStringa($_POST['stato_workflow_da_assegnare'])) {
									//prendo gli utenti e notifico l'inserimento
									$utentiWf = explode(',', $statoWf['utenti']);
									break;
								}
							}
							if($_POST['stato_workflow_da_assegnare'] == 'finale_con_notifica') {
								$_POST['stato_workflow'] = 'finale';
								$utentiIniziali = explode(',', $wf['utenti']);
								if(in_array($datiUser['id'], $utentiIniziali)) {
									//mando le notifiche solo se l'utente che fa l'operazione è un utente iniziale
									$utentiWfNotifica = explode(',', $wf['id_utenti_finali']);
								} else {
									$utentiWfNotifica = array();
								}
							}
						}
					}
			
					$opOgg->preInsert();
					if ($oggOgg->aggiungiOggetto($idCategoriaPasso, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Aggiunta effettuata con successo.";
						$opOgg->postInsert();
						
						if($idOggetto==45 and $configurazione['crea_xml_anac_v3']) {
							include('app/moduli/generaXmlAnac.php');
							$operazioneTesto .= " <strong>Attendere la completa generazione del file XML prima di abbandonare la pagina.</strong> Seguire l\'elaborazione nel box Informazioni di sistema";
						}
						
						$linkAggWF = '';
						if($idOggetto == 11 and moduloAttivo('bandigara')) {
							$linkAggWF = '_'.forzaStringa($_GET['tipo']);
							if($_GET['sottotipo'] != '') {
							    $linkAggWF .= '_'.forzaStringa($_GET['sottotipo']);
							}
						} else if($idOggetto == 4 or $idOggetto == 38) {
						    $linkAggWF = '_'.forzaStringa($_GET['tipo']);
						}
						
						//invio mail in caso di workflow
						foreach((array)$utentiWf as $ut) {
							if($ut) {
								$mailDestinatario = nomeUserDaId($ut, 'email');
								$subject = $configurazione['denominazione_trasparenza'].' - Workflow '.$oggettiTrasparenza[$idOggetto]['nomeMenu'].' - Elemento nello stato '.$statoWf['nome'];
								$testo = nomeUserDaId($ut, 'nome').',<br />'."\r\n".'L\'elemento <strong>'.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].')</strong> &egrave; stato inserito nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_trasparenza'].'</a>' .
										' nel workflow <strong>'.$wf['nome'].'</strong>.<br />'."\r\n".
										'<a href="'.$server_url.'adm_'.$oggettiTrasparenza[$idOggetto]['menu'].'_'.$oggettiTrasparenza[$idOggetto]['menuSec'].'_modifica_'.$oggOgg->lastInsertId.$linkAggWF.'.html">Accedi al portale per verificare l\'elemento.</a>';
								$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
							}
						}
						//invio mail di notifica in caso di workflow
						foreach((array)$utentiWfNotifica as $ut) {
							if($ut) {
								$mailDestinatario = nomeUserDaId($ut, 'email');
								$subject = $configurazione['denominazione_trasparenza'].' - '.$oggettiTrasparenza[$idOggetto]['nomeMenu'].' - Elemento nello stato finale';
								$testo = nomeUserDaId($ut, 'nome').',<br />'."\r\n".'L\'elemento <strong>'.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].')</strong> &egrave; stato inserito nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_trasparenza'].'</a>' .
										' nello stato finale.<br />'."\r\n".
										'<a href="'.$server_url.'adm_'.$oggettiTrasparenza[$idOggetto]['menu'].'_'.$oggettiTrasparenza[$idOggetto]['menuSec'].'_modifica_'.$oggOgg->lastInsertId.$linkAggWF.'.html">Accedi al portale per visualizzare l\'elemento.</a>';
								$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
							}
						}
						//aggiungo il dettaglio del workflow dell'azione effettuata per questa istanza
						if(count($utentiWf) and $oggOgg->idAzioneLog) {
							include_once('classi/log_azione.php');
							$log = new logAzione();
							$istanzaLog = $log->caricaLog(array('id'=>$oggOgg->idAzioneLog));
							$istanzaLog  = $istanzaLog[0];
							$testoWf = 'L\'elemento '.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].') é stato inserito nel workflow '.$wf['nome'].' nello stato '.$statoWf['nome'];
							$log->aggiungiDettaglioLog($istanzaLog['id'], array(
								'workflow' => $testoWf
							));
						}
						if($_POST['stato_workflow'] == 'finale') {
							//inserisco elemento nella tabella dei record che sono stati in workflow ed in pubblicazione almeno una volta
							$el = getIstanzaWorkflow($oggOgg->lastInsertId, $idOggetto);
							if(!$el['id']) {
								setIstanzaWorkflow($oggOgg->lastInsertId, $idOggetto);
							}
						}
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
				}
				
			}
			
			// modifica oggetto
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {
				
				// carico ente in modifica
				$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
				$idCategoria = $istanzaOggetto['id_sezione'];
				
				$idPerAllegati = date('yzGis');
				$prog = 0;	
				$operazione = true;
				
				if($_POST['__archiviata_ripubblica']) {
					$_POST['__archiviata'] = '0';
					$_POST['__archiviata_descrizione'] = '';
					$_POST['__archiviata_data_fine'] = 'NULL';
				}
				
				if($istanzaOggetto['id_atto_albo'] and moduloAttivo('ealbo')) {
					$atto = caricaDocumentoEAlbo('atti', $istanzaOggetto['id_atto_albo']);
					$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
					$_POST['id_atto_albo'] = $istanzaOggetto['id_atto_albo'];
				}
				
				foreach ($oggOgg->struttura as $campoTemp) {
					if (strpos($campoTemp['tipocampo'],'*') !== false) {
						$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
					}
					
					if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia')  {
						
						if($_POST['provenienza-file-'.$campoTemp['nomecampo']] == 'select') {
							//devo prendere l'allegato dall'albo
							if($_POST['import-file-'.$campoTemp['nomecampo']] > 0) {
								foreach((array)$allegatiAtto as $allegato) {
									if($allegato['id'] == $_POST['import-file-'.$campoTemp['nomecampo']]) {
										// prima cancello il vecchio file dal filesystem
										if ($istanzaOggetto[$campoTemp['nomecampo']] != '') {
											@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
										}
									
										$fileContent = file_get_contents($url_ealbo.$uploadPath_ealbo.'oggetto_allegati_atto/'.rawurlencode($allegato['allegato']));
										$nomeFileReplace = str_replace("\'", "_", $allegato['allegato']);
										$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
										$nome = explode("O__O",$nomeFileReplace);
										if(count($nome) == 2 ) {
											$nomeFileReplace = $nome[1];
										}
										if(file_put_contents($uploadPath.$oggOgg->tabellaOggetto."/".$idPerAllegati.$prog."O__O".$nomeFileReplace, $fileContent) === false) {
											// ERRORI NELL'OPERAZIONE
											$operazione = false;
											$operazioneTesto = "Problemi in aggiunta del file allegato (".$allegato['id']."). Riprovare in seguito.";
											$codiceErrore = '#A1 - File Upload';					
										}
										$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
										$prog++;
									}
								}
							}
							
						} else {

							if ($_FILES[$campoTemp['nomecampo']]['tmp_name'] != '' and ($_POST[$campoTemp['nomecampo']."azione"] == 'modifica' or $_POST[$campoTemp['nomecampo']."azione"] == 'aggiungi')) {
								
								// prima cancello il vecchio file dal filesystem
								if ($istanzaOggetto[$campoTemp['nomecampo']] != '') {
									@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
								}
								// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
								$nomeFileReplace = str_replace("\'", "_", $_FILES[$campoTemp['nomecampo']]['name']);
								$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
								copy($_FILES[$campoTemp['nomecampo']]['tmp_name'], $uploadPath.$oggOgg->tabellaOggetto."/".$idPerAllegati.$prog."O__O".$nomeFileReplace)
									or motoreLog("avviso","Errore durante il trasferimento del file <b>".$_FILES[$campoTemp['nomecampo']]['name']."</b>. Riprovare più tardi. <a href=\"javascript:history.back();void(0);\">[Torna indietro]</a>",TRUE);
								$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;

								$prog++;
							} else if (($_POST[$campoTemp['nomecampo']."azione"] == 'nessuna' or ($_FILES[$campoTemp['nomecampo']]['tmp_name'] == '' and $_POST[$campoTemp['nomecampo']."azione"] == 'modifica'))) {
								// ho deciso di non modificare il campo file, o ho scelto di modificarlo ma l'ho lasciato vuoto
								$_POST[$campoTemp['nomecampo']] = $istanzaOggetto[$campoTemp['nomecampo']];
							} else if ($_POST[$campoTemp['nomecampo']."azione"] == 'elimina') {
								
								// devo eliminare il file
								if ($istanzaOggetto[$campoTemp['nomecampo']] != '') {
									@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
								}
													
								$_POST[$campoTemp['nomecampo']] = '';
							} 
						}
					}

				}
				
				//workflow
				//lognormale('',$_POST);
				$_POST['stato_workflow'] = forzaStringa($_POST['stato_workflow_da_assegnare']);
				if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow'] and $_POST['stato_workflow_attuale'] != $_POST['stato_workflow_da_assegnare']) {
					if($_POST['stato_workflow_attuale'] == 'iniziale' or $_POST['stato_workflow_attuale'] == 'finale') {
						$wf = $oggOgg->caricaWorkflowUtenteIniziale();
					} else if($_POST['stato_workflow_attuale'] != '') {
					    $wf = $oggOgg->caricaWorkflowStato(forzaStringa($_POST['stato_workflow_attuale']));
					}
					if($wf['id']) {
						$notificaFinale = false;
						if($_POST['stato_workflow_da_assegnare'] == 'iniziale') {
							$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
						} else if($_POST['stato_workflow_da_assegnare'] == 'finale') {
							$statoWf = array('id' => 'finale', 'nome' => 'finale', 'utenti' => $wf['utenti']);
							$utentiIniziali = explode(',', $wf['utenti']);
							/*
							if(in_array($datiUser['id'], $utentiIniziali)) {
								$notificaFinale == true;
							}
							*/
							//la notifica finale viene inviata a tutti gli id_utenti_finali e quindi nel caso $_POST['stato_workflow_da_assegnare'] == 'finale' va inviata sempre 
							$notificaFinale = true;
						} else {
							$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
							foreach((array)$statiWf as $statoWf) {
								if($statoWf['id'] == $_POST['stato_workflow_da_assegnare']) {
									break;
								}
							}	
						}
						
						$linkAggWF = '';
						if($idOggetto == 11 and moduloAttivo('bandigara')) {
						    $linkAggWF = '_'.forzaStringa($_GET['tipo']);
							if($_GET['sottotipo'] != '') {
							    $linkAggWF .= '_'.forzaStringa($_GET['sottotipo']);
							}
						} else if($idOggetto == 4 or $idOggetto == 38) {
						    $linkAggWF = '_'.forzaStringa($_GET['tipo']);
						}
						
						//prendo gli utenti e notifico l'inserimento
						$utentiWf = explode(',', $statoWf['utenti']);
						foreach((array)$utentiWf as $ut) {
							if($ut) {
								$mailDestinatario = nomeUserDaId($ut, 'email');
								$subject = $configurazione['denominazione_trasparenza'].' - Workflow '.$oggettiTrasparenza[$idOggetto]['nomeMenu'].' - Elemento nello stato '.$statoWf['nome'];
								$testo = nomeUserDaId($ut, 'nome').',<br />'."\r\n".'L\'elemento <strong>'.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].')</strong> &egrave; stato modificato nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_trasparenza'].'</a>' .
										' nel workflow <strong>'.$wf['nome'].'</strong>.<br />'."\r\n".
										'<a href="'.$server_url.'adm_'.$oggettiTrasparenza[$idOggetto]['menu'].'_'.$oggettiTrasparenza[$idOggetto]['menuSec'].'_modifica_'.$idIstanza.$linkAggWF.'.html">Accedi al portale per verificare l\'elemento.</a>';
								$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
							}
						}
						if($notificaFinale) {
							//invio mail di notifica in caso di workflow
							$utentiWfNotifica = explode(',', $wf['id_utenti_finali']);
							foreach((array)$utentiWfNotifica as $ut) {
								if($ut) {
									$mailDestinatario = nomeUserDaId($ut, 'email');
									$subject = $configurazione['denominazione_trasparenza'].' - '.$oggettiTrasparenza[$idOggetto]['nomeMenu'].' - Elemento nello stato finale';
									$testo = nomeUserDaId($ut, 'nome').',<br />'."\r\n".'L\'elemento <strong>'.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].')</strong> &egrave; stato modificato nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_trasparenza'].'</a>' .
											' nello stato finale.<br />'."\r\n".
											'<a href="'.$server_url.'adm_'.$oggettiTrasparenza[$idOggetto]['menu'].'_'.$oggettiTrasparenza[$idOggetto]['menuSec'].'_modifica_'.$oggOgg->lastInsertId.$linkAggWF.'.html">Accedi al portale per visualizzare l\'elemento.</a>';
									$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
								}
							}
						}
						//lognormale('',$statoWf);
					}
				}
			
				if ($operazione) {
					$opOgg->preUpdate();
					if ($oggOgg->modificaOggetto($idIstanza, $idCategoria, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Modifica effettuata con successo.";
						$opOgg->postUpdate();
						
						if($idOggetto==45 and $configurazione['crea_xml_anac_v3']) {
							include('app/moduli/generaXmlAnac.php');
							$operazioneTesto .= " <strong>Attendere la completa generazione del file XML prima di abbandonare la pagina.</strong> Seguire l\'elaborazione nel box Informazioni di sistema";
						}
						
						//aggiungo il dettaglio del workflow dell'azione effettuata per questa istanza
						if(count($utentiWf) and $oggOgg->idAzioneLog) {
							include_once('classi/log_azione.php');
							$log = new logAzione();
							$istanzaLog = $log->caricaLog(array('id'=>$oggOgg->idAzioneLog));
							$istanzaLog  = $istanzaLog[0];
							$testoWf = 'L\'elemento '.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].') é stato modificato nel workflow '.$wf['nome'].' nello stato '.$statoWf['nome'];
							$log->aggiungiDettaglioLog($istanzaLog['id'], array(
								'workflow' => $testoWf
							));
						}
						
						if($notificaFinale or $_POST['stato_workflow'] == 'finale') {
							//inserisco elemento nella tabella dei record che sono stati in workflow ed in pubblicazione almeno una volta
							$el = getIstanzaWorkflow($idIstanza, $idOggetto);
							if(!$el['id']) {
								setIstanzaWorkflow($idIstanza, $idOggetto);
							}
						}
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in modifica. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
				}
			}
			
			//importazione atto da albo online
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'importAtto') {
				
				$operazione = true;
				
				$atto = caricaDocumentoEAlbo('atti', forzaNumero($_POST['id_atto_albo']));
				$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
				
				// controllo se nei campi personalizzati ci sono dei file
				/*
				foreach ($oggOgg->struttura as $campoTemp) {
					if (strpos($campoTemp['tipocampo'],'*') !== false) {
						$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
					}			
					if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') {
						//campo di tipo file
						
						if($_POST['provenienza-file-'.$campoTemp['nomecampo']] == 'select') {
							//devo prendere l'allegato dall'albo
							if($_POST['import-file-'.$campoTemp['nomecampo']] > 0) {
								foreach((array)$allegatiAtto as $allegato) {
									if($allegato['id'] == $_POST['import-file-'.$campoTemp['nomecampo']]) {
										$fileContent = file_get_contents($url_ealbo.$uploadPath_ealbo.'oggetto_allegati_atto/'.rawurlencode($allegato['allegato']));
										$nomeFileReplace = str_replace("\'", "_", $allegato['allegato']);
										$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
										if(file_put_contents($uploadPath.$oggOgg->tabellaOggetto."/".$idPerAllegati.$prog."O__O".$nomeFileReplace, $fileContent) === false) {
											// ERRORI NELL'OPERAZIONE
											$operazione = false;
											$operazioneTesto = "Problemi in aggiunta del file allegato (".$allegato['id']."). Riprovare in seguito.";
											$codiceErrore = '#A1 - File Upload';					
										}
										$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
										$prog++;
									}
								}
							}
							
						} else {
							//nuovo upload di un file
							if($_FILES[$campoTemp['nomecampo']]['tmp_name'] != '') {
								// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
								$nomeFileReplace = str_replace("\'", "_", $_FILES[$campoTemp['nomecampo']]['name']);
								$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
								if (!(copy($_FILES[$campoTemp['nomecampo']]['tmp_name'], $uploadPath.$oggOgg->tabellaOggetto."/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
									// ERRORI NELL'OPERAZIONE
									$operazione = false;
									$operazioneTesto = "Problemi in aggiunta del file ".$_FILES[$campoTemp['nomecampo']]['tmp_name'].". Riprovare in seguito.";
									$codiceErrore = '#01 - File Upload';					
								}  
								$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
								$prog++;
							}
						}
					}
				}
				*/
				
				// devo tradurre i campi riferimento
				foreach ($oggOgg->struttura as $campoTemp) {
					if (strpos($campoTemp['tipocampo'],'*') !== false) {
						$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
					}			
					if ($campoTemp['tipocampo'] == 'campoggetto_multi' and is_array($_POST[$campoTemp['nomecampo']])) {
						$_POST[$campoTemp['nomecampo']] = implode(",", $_POST[$campoTemp['nomecampo']]);
					}
				}
				
				if ($operazione) {
					$idCategoriaPasso=$idCategoria;
					if ($scegliCategoria and isset($_POST['scelta_categoria']) and $oggOgg->idCategoria) {
						$idCategoriaPasso=forzaNumero($_POST['scelta_categoria']);
					}				
			
					$opOgg->preInsert();
					if ($oggOgg->aggiungiOggetto($idCategoriaPasso, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Aggiunta effettuata con successo.";
						$opOgg->postInsert();
						
						//campi allegati: nuova gestione
						$idPerAllegati = date('yzGis');
						$prog = 0;
						$oggAllegati = new oggettiAdmin(57);
						$ord = 1;
						foreach((array)$allegatiAtto as $allegato) {
							//lognormale($_POST['import-file-'.$allegato['id']]);
							if($allegato['id'] == $_POST['import-file-'.$allegato['id']] and $_POST['import-file-'.$allegato['id']] > 0) {
								$fileContent = file_get_contents($url_ealbo.$uploadPath_ealbo.'oggetto_allegati_atto/'.rawurlencode($allegato['allegato']));
								$nomeFileReplace = str_replace("\'", "_", $allegato['allegato']);
								if(strpos($nomeFileReplace, "O__O")){
									$nomeFileReplace = substr($nomeFileReplace, strpos($nomeFileReplace, "O__O") + 4);
								}
								$nomeFileReplace = $idPerAllegati.$prog."O__O".correttoreCaratteriFile($nomeFileReplace);
								if(file_put_contents($uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace, $fileContent) === false) {
									// ERRORI NELL'OPERAZIONE
									$operazioneTesto .= " Problemi nell'aggiunta del file allegato '".$allegato['allegato']."'.";
								} else {
									$arrayValori = array(
											'id_proprietario' => $datiUser['id'],
											'id_lingua' => 0,
											'nome' => ($allegato['descrizione'] != '' ? $allegato['descrizione'] : 'Allegato'),
											'ordine' => $ord,
											'id_ente' => $idEnte,
											'id_oggetto' => $oggOgg->idOggetto,
											'id_documento' => $oggOgg->lastInsertId,
											'__id_allegato_istanza' => forzaStringa($_POST['__id_allegato_istanza']),
											'__temporaneo' => '0',
											'file_allegato' => $nomeFileReplace
									);
									if (!$oggAllegati->aggiungiOggetto(0, $arrayValori)) {
										@unlink($uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
									}
									$ord++;
								}
								$prog++;
							}
						}
						
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
				}
			}
			
			// notifica push
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'notifica_push') {
			
				$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
		
				$notificaSalvata = $oggOgg->inviaNotificaPush($idIstanza, $idOggetto, forzaNumero($_POST['id_ente']));
				if ($notificaSalvata) {
					$operazioneTesto = "Notifica push inviata.";
				} else {
					$operazioneTesto = "Problemi in invio notifica push. Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
				if ($notificaSalvata) {
					//codica ajax asincrono per l'invio delle notifiche
					include('app/admin_template/oggetti/notifica_push_send.tmp');
				}
				
			}
			
			///////////////////// RISPOSTA ALLE AZIONI MULTIPLE
			// se e' settato oggetto oggettiSelezionati, vengo da un form e cambio l'azione
			switch ($azioneSecondaria) {

				////////////////// CANCELLA //////////
				case "cancella" :

					// verifico se prendere i dati dal post o dal get
					$cancello = isset($_POST['id_cancello_tabella']) ? $_POST['id_cancello_tabella'] : 0;
					
					if ($cancello) {
						
						//verifica di tutte le istanze di allegati da eliminare
						$cs = explode(',',$cancello);
						$arrayIdAllegati = array();
						foreach((array)$cs as $c) {
							$ida = mostraDatoOggetto($c, $oggOgg->idOggetto, '__id_allegato_istanza');
							if($ida != '') {
								$arrayIdAllegati[] = " __id_allegato_istanza = '".$ida."' ";
							}
						}
						
						$opOgg->preDelete();
						$codiceErrore = '';
						$numCancellate = $oggOgg->cancellaOggetti($cancello);
						
						if(count($arrayIdAllegati)>0) {
							$oggAllegati = new oggettiAdmin(57);
								
							$ida = implode(' OR ', $arrayIdAllegati);
							$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_allegati WHERE 1=1 AND (".$ida.")";
							if (!($result = $database->connessioneConReturn($sql))) {
								die("Database non installato o non disponibile: errore critico: ".$sql);
							}
							$result = $database->sqlArrayAss($result);
							foreach((array)$result as $a) {
								$oggAllegati->cancellaOggetti($a['id']);
							}
						}
						
						
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." ".($numCancellate > 1 ? 'elementi' : 'elemento').".";
							$opOgg->postDelete();
						} else {
							// ERRORI NELL'OPERAZIONE
							$operazione = false;
							$operazioneTesto = "Problemi in eliminazione. Riprovare in seguito.";
							$codiceErrore = '#00 - Generico';
						}
						
					} 

				break;
			}
			
			if(!$box) {
				// analizzo quali campi visualizzare in amministrazione, prendendoli dalla struttura IsWEB
				$campiVisualizzati = array();
				$struttura = $oggOgg->parsingStruttura('si');
				for ($i=0;$i<count($oggOgg->campiAdmin);$i++) {
					$campiVisualizzati[$i]['campo'] = $oggOgg->campiAdmin[$i]; 
					$campiVisualizzati[$i]['etichetta'] = $oggOgg->campiEtiAdmin[$i]; 
					$campiVisualizzati[$i]['proprieta'] = $oggOgg->campiPropAdmin[$i]; 	
					$campoStr = campoStruttura($campiVisualizzati[$i]['campo'],$struttura);
					if (strpos($campoStr['tipocampo'],'*') !== false) {
						$campoStr['tipocampo'] = substr($campoStr['tipocampo'], 1);	
					}
					$campiVisualizzati[$i]['tipo'] = $campoStr['tipocampo'];
					$campiVisualizzati[$i]['etichette'] = $campoStr['proprieta'];
					$campiVisualizzati[$i]['valore'] = $campoStr['valorecampo'];
				}
				
				include ('./app/admin_template/oggetti/info_tab.tmp');
				include ('./app/admin_template/oggetti/tab_start_ajax.tmp');
				include ('./app/admin_template/oggetti/tab_end.tmp');
				
			} else {
				include ('./app/admin_template/oggetti/salvataggio_box.tmp');
			}
			
		}
	break;

	//////////////////AGGIUNGI///////

	case "aggiungi" :
	case "importAtto" :
		// controllo permessi
		if (!$aclTrasparenza[$menuSecondario]['creazione']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per aggiungere informazioni in questo archivio.');
		} else {
			$id = 0;
			$idAllegatoDinamico = $idEnte.'-'.$datiUser['id'].'-'.$idOggetto.'-'.mktime();
			
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			
			//IMPORT ATTO
			$attoImportato = false;
			if($azione == 'importAtto') {
				require('app/moduli/menu_amm/operazioni/oggetti/__importAtto.php');
			}
				
			//bando2provvedimento
			if($azioneSecondaria == 'bando2provvedimento') {
				require('app/moduli/menu_amm/operazioni/oggetti/__bando2provvedimento.php');
			}
			
			$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
			if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow'] and $azione == 'aggiungi') {
				//verificare se esiste un workflow iniziale per l'utente loggato
				$wf = $oggOgg->caricaWorkflowUtenteIniziale();
				if($wf['id']) {
					$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
					//sono in inserimento => lo stato corrente è lo stato 0
					$statoWf = $statiWf[0];
					$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
					$visualizzaPulsanteSalvaIniziale = true;
					if(count($statiWf) == 0) {
						//workflow di sola notifica?
						$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale_con_notifica';
					}
				}
			}
			if($attoImportato) {
				motoreLogTrasp('permessonegato', 'Atto gi&agrave; importato in questo archivio.');
			} else {
				// qui includo la pagina con il form
				$istanzaOggetto['__id_allegato_istanza'] = $idAllegatoDinamico;
				include ('./app/admin_template/oggetti/form/'.$menuSecondario.'.tmp');
			}
		}
	break;

	//////////////////MODIFICA///////

	case "modifica" :
	case "editpagina" :
		
		// carico elemento da modificare
		$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
		
		if($azione == 'editpagina') {
			//ricalcola permessi
			if($aclTrasparenza['contenuti'][$istanzaOggetto['id_sezione_etrasp']]['modifica']) {
				$aclTrasparenza[$menuSecondario]['modifica'] = true;
			}
		}
		
		//eventuale personalizzazione per permessi aggiuntivi
		if(file_exists('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/oggetti/imposta__bloccato.php')) {
			include ('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/oggetti/imposta__bloccato.php');
		}
		
		$limitaProprietario = false;
		if($datiUser['filtraRecordProprietario'] and $istanzaOggetto['id_proprietario'] != $datiUser['id']) {
			$limitaProprietario = true;			
		}
		
		if ((!$aclTrasparenza[$menuSecondario]['modifica'] AND !$aclTrasparenza[$menuSecondario]['creazione']) OR $istanzaOggetto['__bloccato'] OR $limitaProprietario) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare le informazioni di questo archivio.');
		} else {
			
			//DUPLICAZIONE
			if($azioneSecondaria == 'duplica') {
				require('app/moduli/menu_amm/operazioni/oggetti/__duplica.php');
			}
			
			//RIPRISTINO VERSIONING
			if($azioneSecondaria == 'ripristina_versioning') {
				require('app/moduli/menu_amm/operazioni/oggetti/__ripristina_versioning.php');
			}
			
			// carico istanza da modificare (NON ELIMINARE, necessario per duplicazione)
			$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
			
			if($istanzaOggetto['id_atto_albo'] and moduloAttivo('ealbo')) {
				$atto = caricaDocumentoEAlbo('atti', $istanzaOggetto['id_atto_albo']);
				$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
			}
			
			if(!$istanzaOggetto['__id_allegato_istanza']) {
				$idAllegatoDinamico = $idEnte.'-'.$datiUser['id'].'-'.$idOggetto.'-'.mktime();
				$istanzaOggetto['__id_allegato_istanza'] = $idAllegatoDinamico;
			} else {
				$idAllegatoDinamico = $istanzaOggetto['__id_allegato_istanza'];
			}
			
			//bonifica di eventuali allegati (vecchia gestione)
			$oggAllegati = new oggettiAdmin(57);
			$ord = 1;
			foreach ($oggOgg->struttura as $campoTemp) {
				if (strpos($campoTemp['tipocampo'],'*') !== false) {
					$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);
				}
				if (($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') and $campoTemp['nomecampo'] != 'foto' and $campoTemp['nomecampo'] != 'immagine') {
					if($istanzaOggetto[$campoTemp['nomecampo']] != '' and $istanzaOggetto[$campoTemp['nomecampo']] != '.' and $istanzaOggetto[$campoTemp['nomecampo']] != '..' and file_exists($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']])) {
						
						//lognormale('bonificare il campo '.$campoTemp['nomecampo'].' -> '.$istanzaOggetto[$campoTemp['nomecampo']], $campoTemp);
						copy($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']], $uploadPath.$oggAllegati->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
						
						//creo un backup del file
						if(!file_exists($uploadPath.$oggOgg->tabellaOggetto."/backup")) {
							mkdir($uploadPath.$oggOgg->tabellaOggetto."/backup");
							chmod($uploadPath.$oggOgg->tabellaOggetto."/backup", 0777);
						}
						copy($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']], $uploadPath.$oggOgg->tabellaOggetto."/backup/".$istanzaOggetto[$campoTemp['nomecampo']]);
						file_put_contents($uploadPath.$oggOgg->tabellaOggetto."/backup/".$istanzaOggetto['id'].".bkf", $campoTemp['nomecampo'].'|'.$istanzaOggetto[$campoTemp['nomecampo']].'{', FILE_APPEND);
						
						$arrayValori = array(
								'id_proprietario' => $datiUser['id'],
								'id_lingua' => 0,
								'nome' => $campoTemp['etichetta'],
								'ordine' => $ord,
								'id_ente' => $idEnte,
								'id_oggetto' => $oggOgg->idOggetto,
								'id_documento' => $istanzaOggetto['id'],
								'__id_allegato_istanza' => $idAllegatoDinamico,
								'__temporaneo' => '0',
								'file_allegato' => $istanzaOggetto[$campoTemp['nomecampo']],
								'omissis' => $istanzaOggetto['omissis']
						);
						if (!$oggAllegati->aggiungiOggetto(0, $arrayValori)) {
							@unlink($uploadPath.$oggAllegati->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
						} else {
							$sql = "UPDATE ".$dati_db['prefisso'].$oggOgg->tabellaOggetto." SET ".$campoTemp['nomecampo']." = NULL, __id_allegato_istanza = '".$idAllegatoDinamico."' WHERE id = ".$istanzaOggetto['id'];
							if (($result = $database->connessioneConReturn($sql))) {
								@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
								$istanzaOggetto[$campoTemp['nomecampo']] = '';
							}
						}
						$ord++;
					}
				}
			}
			
			$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
			if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow']) {
				if($istanzaOggetto['stato_workflow'] == 'iniziale') {
					//STATO INIZIALE
					$wf = $oggOgg->caricaWorkflowUtenteIniziale();
					if($wf['id']) {
						//utente che sta modificando è iniziale per questo workflow
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
						$istanzaOggetto['stato_workflow_attuale'] = 'iniziale';
						$istanzaOggetto['stato_workflow_da_assegnare'] = $statiWf[0]['id'];
						$visualizzaPulsanteSalvaIniziale = true;
					} else {
						//verificare se l'utente loggato è intermedio
						$wf = $oggOgg->caricaWorkflowUtenteIntermedio();
						if($wf['id']) {
							//utente che sta modificando è tra quelli intermedi: vede solo il pulsante salva
							$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
							$istanzaOggetto['stato_workflow_attuale'] = 'iniziale';
							$istanzaOggetto['stato_workflow_da_assegnare'] = 'iniziale';
						} else {
							//nè utente iniziale, nè intermedio -> altro utente
							$wf = $oggOgg->caricaWorkflowOggetto();
							if($wf['id']) {
								//esiste ALMENO un WF per questo oggetto che non coinvolge l'utente loggato
								$istanzaOggetto['stato_workflow_attuale'] = 'iniziale';
								$istanzaOggetto['stato_workflow_da_assegnare'] = 'iniziale';
							} else {
								//stato iniziale ma non esiste ALMENO un WF per questo record (wf eliminato) -> resetto a finale
								$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
							}
						}
					}
				} else if($istanzaOggetto['stato_workflow'] != 'finale') {
					//STATO INTERMEDIO
					//verificare se esiste un workflow per questo elemento
					$wf = $oggOgg->caricaWorkflowStato($istanzaOggetto['stato_workflow']);
					if($wf['id']) {
						//esiste WF che cionvolge questo elemento 
						$utentiIniziali = explode(',', $wf['utenti']);
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						$indiceStato = 0;
						for($i = 0; $i < count($statiWf); $i++) {
							$sw = $statiWf[$i];
							if($sw['id'] == $istanzaOggetto['stato_workflow']) {
								//ho trovato lo stato in cui si trova questo elemento
								$statoWf = $sw;
								$indiceStato = $i;
								$i = count($statiWf);  //forzo uscita dal ciclo
							}
						}
						if(in_array($datiUser['id'], $utentiIniziali)) {
							//utente che sta modificando è tra quelli iniziali -> resetto lo stato da assegnare a quello iniziale
							$istanzaOggetto['stato_workflow_attuale'] = $statoWf['id'];
							$statoWf = $statiWf[0];
							$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
							$visualizzaPulsanteSalvaIniziale = true;
						} else {
							$utentiWf = explode(',', $statoWf['utenti']);
							if(in_array($datiUser['id'], $utentiWf)) {
								//caso b: utente che ha in carico questo stato di workflow: può portarlo avanti o farlo tornare indietro o semplicemente salvarlo mantenendo lo stato attuale
								$istanzaOggetto['stato_workflow_attuale'] = $statoWf['id'];
								$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
								//assegnazione stato "indietro" = quello precedente all'attuale. se non esiste, il precedente stato è quello iniziale
								$istanzaOggetto['stato_workflow_precedente'] = ($statiWf[$indiceStato-1]['id'] != '' ? $statiWf[$indiceStato-1]['id'] : 'iniziale');
								if($istanzaOggetto['stato_workflow_precedente'] == 'iniziale') {
									$statoWfPrecedente = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
								} else if($istanzaOggetto['stato_workflow_precedente'] != '') {
									$statoWfPrecedente = $statiWf[$indiceStato-1];
								}
								//assegnazione stato "avanti" = quello successivo all'attuale. se non esiste, il successivo stato è quello finale
								$istanzaOggetto['stato_workflow_successivo'] = ($statiWf[$indiceStato+1]['id'] != '' ? $statiWf[$indiceStato+1]['id'] : 'finale');
								if($istanzaOggetto['stato_workflow_successivo'] == 'finale') {
									$statoWfSuccessivo = array('id' => 'finale', 'nome' => 'finale', 'utenti' => $wf['utenti']);
								} else if($istanzaOggetto['stato_workflow_successivo'] != '') {
									$statoWfSuccessivo = $statiWf[$indiceStato+1];
								}
							} else {
								//utente che sta modificando non è coinvolto nel WF -> ha solo il pulsante salva e lo stato rimane quello attuale
								$istanzaOggetto['stato_workflow_attuale'] = $statoWf['id'];
								$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
							}
						}
					} else {
						//stato intermedio ma non esiste un WF per questo record (wf eliminato) -> resetto a finale
						$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
					}
				} else {
					//stato finale -> se c'è un wf iniziale per l'utente loggato allora lo riavvio
					$wf = $oggOgg->caricaWorkflowUtenteIniziale();
					if($wf['id']) {
						//utente loggato è tra quelli che danno il via al wf per questo elemento nello stato finale -> riavvio wf
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						$statoWf = $statiWf[0];
						$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
						$istanzaOggetto['stato_workflow_attuale'] = 'finale';
						$visualizzaPulsanteSalvaIniziale = true;
					} else {
						//stato finale ma non esiste un WF per l'utente loggato -> rimane finale
						$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
					}
				}
			} //fine verifica WF
			
			$permessiRecordUffici = true;
			$ufficiUtente = explode(',', $datiUser['uffici_selezionabili']);
			if($idOggetto == 11 and $configurazione['fitra_record_strutture'] and count($ufficiUtente) > 0 and $ufficiUtente[0] > 0) {
				$permessiRecordUffici = false;
				$struttura = $oggOgg->parsingStruttura('si');
				foreach((array)$struttura as $campo) {
					if(($campo['tipocampo'] == 'campoggetto' or $campo['tipocampo'] == 'campoggetto_multi') and $campo['valorecampo'] == 13) {
						$strutture = explode(',', $istanzaOggetto[$campo['nomecampo']]);
						foreach((array)$ufficiUtente as $u) {
							if($u>0 and in_array($u, $strutture)) {
								//ho i permessi
								$permessiRecordUffici = true;
							}
						}
					}
				}
			}
			if(!$permessiRecordUffici and $istanzaOggetto['id_proprietario'] != $datiUser['id']) {
				//non posso modificare questa istanza, forzo permessi e proprietario
				$istanzaOggetto['id_proprietario'] = '-100';
				$aclTrasparenza[$menuSecondario]['modifica'] = false;
			}

			// ulteriore controllo permesssi, verifico che non ci siano chiamate GET forzate verso altri enti			
			if (($aclTrasparenza[$menuSecondario]['modifica'] AND $istanzaOggetto['id_ente'] == $datiUser['id_ente_admin'] OR $istanzaOggetto['id_proprietario'] == $datiUser['id']) AND ($id != 0 OR $datiUser['permessi']==10 OR $datiUser['permessi']==3)) {			
				include ('./app/admin_template/oggetti/form/'.$menuSecondario.'.tmp');
			} else {
				if(!$istanzaOggetto['id']) {
					motoreLogTrasp('permessonegato', 'Questa informazione non è più presente nel sistema.');
				} else {
					motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare questa informazione, ma solo quelle a te assegnate.');
				}
			}
		}

	break;
	
	
	//////////////////NOTIFICA PUSH///////

	case "notifica_push" :
		if (!$aclTrasparenza[$menuSecondario]['notifiche_push'] or !moduloAttivo('notifiche_push')) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per inviare notifiche push di questo archivio.');
		} else {
			// carico l'user da modificare
			$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
			
			include ('./app/admin_template/oggetti/form/notifiche_push.tmp');
		}

	break;
		
		
	//////////////////IMPORTAZIONE(STRUMENTI)///////

	case "importa":

		if (!$aclTrasparenza[$menuSecondario]['avanzate']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per accedere agli strumenti avanzati di questo archivio.');
		} else {
		
			// importo la classe di amministrazione contenuto
			require('app/classi/admin_informazioni.php');
			$informazioni = new infoAdmin();
			
			$id=$idOggetto;
		
			$istanzaOggetto = $informazioni->caricaOggetto($id);

			// carico storico importazioni precedenti per questo oggetto
			$storicoImport = $informazioni->caricaImportazioni($id);
			$settingsDefault = $informazioni->settingsImportDefault($id);
			$proprietaDefault = $informazioni->proprietaImportDefault($id);
			$valoriDefault = $informazioni->valoriImportDefault($id);
			
			if (isset($_POST['rispostaForm']) and $_FILES["file"]["name"] != '') {
				// ho inviato i dati da importare nell'oggetto. Analizzo il tipo di file
				$estFile =  substr($_FILES["file"]["name"], (strrpos($_FILES["file"]["name"], ".")+1));
				if ($_POST['utente'] == '') {
					$_POST['utente'] = 0;
				}
				if ($_POST['id_ente'] == '') {
					$_POST['id_ente'] = $datiUser['id_ente_admin'];
				}
				$sqlQuery = '';
				$sqlDati = '';
				$txtMsg = '';
				$txtMsgErr = '';
				$posizionamenti	 = array();
				$proprietaImport = array();
				$valoriImport = array();
				
				// eseguo eventuale reset dati
				if ($_POST['reset_dati'] and $estFile == 'xls') {
					$sql = "DELETE FROM ".$dati_db['prefisso'].$informazioni->tabellaOggetto." WHERE id_ente=".forzaNumero($_POST['id_ente']);
					//echo "Query di reset: ".$sql;
					if ( !($risultato = $database->connessioneConReturn($sql)) ) {
						$txtMsg .= '<b><u>ATTENZIONE</u></b> operazione iniziale di <b>reset dei dati</b> fallita.<br /><br />';
					} else {
						$txtMsg .= '<b>Reset dei dati</b> effettuato con successo.<br /><br />';
					}								
				}
				
				if ($estFile == 'xls') {
					// file excel, provo ad aprirlo con il reader
					require_once ('excel/reader.php');
					$data = new Spreadsheet_Excel_Reader();
					$data->setOutputEncoding('CP1251');
					$data->read($_FILES["file"]['tmp_name']);
					$numImport = 0;
					$numError = 0;
					$idInseriti = '';
					
					//sanatizzo tutto il $_POST
					foreach((array)$_POST as $k => $v) {
					    if(strpos($k, 'campo') !== false) {
					        $_POST[$k] = forzaStringa($v);
					    } else if(strpos($k, 'campoprop') !== false) {
					        $_POST[$k] = forzaStringa($v);
					    } else if(strpos($k, 'valore') !== false) {
					        $_POST[$k] = forzaStringa($v);
					    }
					}
					
					for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++){
						// sono dentro al singolo record nel file, ora analizzo la struttura oggetto ed associo i campi
						$num=0;
						$sqlQuery = '';
						$sqlDati = '';
						$campoRic = $data->sheets[0]['cells'][$i][1];
						foreach ($informazioni->struttura as $campoTemp) {
							
							// elimino l'eventuale asterisco
							if (strpos($campoTemp['tipocampo'],'*') !== false) {
								$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
							}		
							// escludo il campo id_ente dai dati excel
							if ($campoTemp['nomecampo'] != 'id_ente') {						
								if ($_POST['campo'.$num] OR $_POST['valore'.$num] != '') {
									if ($sqlQuery != '') {
										$sqlQuery .= ',';
									}
									$sqlQuery .= $campoTemp['nomecampo'];

									if ($sqlDati != '') {
										$sqlDati .= ',';
									}
									
									if (!$_POST['campo'.$num]) {
										// opzione stringhe normali: correggo le entità html	
										$stringa = addslashes(htmlentities($_POST['valore'.$num]));							
										$sqlDati .= "\"".$stringa."\"";
									} else {
										if ($campoTemp['nomecampo'] == 'stato_pubblicazione') {
											//non faccio nulla
										} else if ($campoTemp['tipocampo'] == 'data_calendario') {
											// controllo se il campo è una data
											// verifico il formato
											if (isset($_POST['campoprop'.$num]) and trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]])!='') {
												if ($_POST['campoprop'.$num] == 'gg/mm/aaaa') {
													list($giorno,$mese,$anno) = explode('/',trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
													if (@checkdate($mese,$giorno,$anno)) {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = mktime(2,0,0,$mese,$giorno,$anno); 
													} else {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = '';
													}
												} else if ($_POST['campoprop'.$num] == 'aaaammgg') {
													$anno = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 0, 4);
													$mese = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 4, 2);
													$giorno = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 6, 2);
													if (@checkdate($mese,$giorno,$anno)) {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = mktime(2,0,0,$mese,$giorno,$anno); 
													} else {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = '';
													}
												} else if ($_POST['campoprop'.$num] == 'gg-mm-aaaa') {	
													list($giorno,$mese,$anno) = explode('-',trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
													if (@checkdate($mese,$giorno,$anno)) {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = mktime(2,0,0,$mese,$giorno,$anno); 
													} else {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = '';
													}												
												} else if ($_POST['campoprop'.$num] == 'timestamp nativo') {
													$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]); 
												}
											}
										} else if ($campoTemp['tipocampo'] == 'editor') {
											if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==2) {
												// correggo il paragrafo HTML con le tidy
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(correttoreHtml($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											} else if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==3) {
												// non opero alcuna correzione al codice
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(html_entity_decode($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											} else if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==1) {
												// non opero alcuna correzione al codice
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
											} else {
												// correggo il paragrafo HTML senza le tidy sostituendo le entita
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(htmlentities($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											}	
										} else if ($campoTemp['tipocampo'] == 'text' or $campoTemp['tipocampo'] == 'textarea' or $campoTemp['tipocampo'] == 'select') {
											// opzione stringhe normali: correggo le entità html	
											if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==2) {
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(html_entity_decode($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											} else if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==1) {
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(htmlentities($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											} else {
												// non correggo le entità html dalle stringhe
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
											}
										}
										
										if ($campoTemp['nomecampo'] == 'stato_pubblicazione') {
											$sqlDati .= "\"100\"";
										} else if ($campoTemp['tipoinput'] == 'string' OR $campoTemp['tipoinput'] == 'blob' OR $campoTemp['tipoinput'] == 'text') {
										//if ($campoTemp['tipoinput'] == 'string') {
											// formato stringa, controllo lunghezza dei caratteri (255 max)
											if ($campoTemp['tipocampo'] != 'editor' and strlen(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]])) > 254) {
												
												$stringa = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 0, 250)."..";
												//echo "accorcio la stringa di ".strlen(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]))." caratteri (".$campoTemp['nomecampo']."): da ".trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]])." a ".$stringa."<br />";
											} else {
												$stringa = trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
											}   
											$sqlDati .= "\"".$stringa."\"";
										} else {
											// formato numerico
											if (trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]) != '') {
												// formato numerico
												if (is_numeric(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]))) {
													$sqlDati .= trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
												} else {
													$sqlDati .= "NULL";
												}
											} else {
												$sqlDati .= "NULL";
											}  
										}
										// ricordo il dato strutturale
										if (!isset($posizionamenti[$campoTemp['nomecampo']])) {
											$posizionamenti[$campoTemp['nomecampo']]=$_POST["campo".$num];
										}
										if (!isset($proprietaImport[$campoTemp['nomecampo']])) {
											$proprietaImport[$campoTemp['nomecampo']]=$_POST["campoprop".$num];
										}
										if (!isset($valoriImport[$campoTemp['nomecampo']])) {
											$valoriImport[$campoTemp['nomecampo']]=$_POST["valore".$num];
										}

									}
								}
								// verifico se è il campo di riconoscimento oggetto da inserire nel report
								if ($campoTemp['nomecampo']==$informazioni->campo_default) {
									$campoRic = trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
								} 
							} 				
							$num++;
						}
						
						if ($sqlQuery != '' and $sqlDati != '') {
							$opOgg->preImport();
							$sql = "INSERT INTO ".$dati_db['prefisso'].$informazioni->tabellaOggetto." (stato,id_proprietario,data_creazione,ultima_modifica,id_ente,".$sqlQuery.") VALUES 
								(1,".forzaNumero($_POST['utente']).",".time().", ".time().",".forzaNumero($_POST['id_ente']).",".$sqlDati.")";
								//echo "<br />Query: ".$sql ;
							if ( !($risultato = $database->connessioneConReturn($sql)) ) {
								$txtMsg .= '<b><u>Errore</u></b> di importazione <b>record '.($i-1).'</b> (<i>'.$campoRic.'</i>).<br />';
								$txtMsgErr .= '<b><u>Errore</u></b> di importazione <b>record '.($i-1).'</b> (<i>'.$campoRic.'</i>).<br />';
								$numError++;
							} else {
								$txtMsg .= 'Importazione <b>record '.($i-1).'</b> (<i>'.$campoRic.'</i>) avvenuta con successo. <br />';
								$numImport++;
								$opOgg->postImport();
							}
							if ($idInseriti != '') {
								$idInseriti .= ',';
							} 
							$idInseriti .= mysql_insert_id($database->db_connect_id);
						} 
					}

				} else {
					$txtLog = "<p>L'estensione del file non è riconosciuta per l'importazione in quetso archivio.</p>";
					$numImport = 0;	
					$numError =0;
				}     
				if ($numImport or $numError) {
					
					$txtLog = "<p>Importati con successo <b>".$numImport." record</b>(con <b>".$numError." errori</b>) in questo archivio.</p>.";
					// ricavo la variabile dei posizionamenti
					$settaggImport = serialize($posizionamenti);
					$campopropImport = serialize($proprietaImport);
					$valImport = serialize($valoriImport);
					// creo lo storico del report
					$arrayValori = array(
						'oggetto' => $id,
						'id_ente' => forzaNumero($_POST['id_ente']),
					    'utente' => forzaNumero($_POST['utente']),
					    'categoria' => forzaNumero($_POST['categoria']),
						'reset_dati' => forzaStringa($_POST['reset_dati']),
					    'default' => forzaStringa($_POST['default']),
					    'reset_dati' => forzaStringa($_POST['reset_dati']),
						'file' => $_FILES["file"]["name"],
						'settings' => $settaggImport,
						'proprieta' => $campopropImport,
						'valori' => $valImport,
						'id_inseriti' => $idInseriti,
						'num_suc' => $numImport,
						'num_err' => $numError,
						'report' => $txtMsg
					);
					if (!isset($_POST['categoria'])) {
						$arrayValori['categoria'] = -1;
					}
					$inserito = $informazioni->aggiungiImport($arrayValori);

					// ora salvo il file nella cartella import
					$nomeFile = $inserito."_".$_FILES["file"]['name'];
					copy($_FILES["file"]['tmp_name'], $uploadPath.$informazioni->tabellaOggetto."/import/".$nomeFile)
						or motoreLog("avviso","Errore durante il salvataggio del file <b>".$_FILES["file"]['name']."</b> usato per l'importazione.",FALSE);
					
				}							
				// ho costruitio la query, la eseguo
				if ($txtLog == '') {
					$txtLog = "<p>Non ho trovato record validi per l'importazione dei dati in questo archivio.</p>";
				}
				
				include ('./app/admin_template/oggetti/importa_log.tmp');
				
			} else {
				// qui includo la pagina con il form
				include ('./app/admin_template/oggetti/importa.tmp');
			}
		}
	break;
		
		
	case 'selectIstanze':
	case 'selectIstanzeAmm':
		
		// ELABORO LISTA OGGETTI NON CATEGORIZZATI
  		if (!$oggOgg->idCategoria) {
  			$listaTabella = $oggOgg->visualizzaListaOggettiNoCat(0, 'tutti', 'ultima_modifica', 'desc', $idEnteAdmin); 
		} else {
			// ELABORO LISTA CATEGORIE OGGETTO
			$lista = $oggOgg->visualizzaLista($idCategoria);
			$numCategorie = count($lista);
			$listaTabella = $oggOgg->visualizzaListaOggetti($idCategoria, $inizio, $limite, $campoOrdine, $ordine); 
		}
		
		$numOggetti = count($listaTabella);
		
		// analizzo quali campi visualizzare in amministrazione, prendendoli dalla struttura IsWEB
		$campiVisualizzati = array();
		$struttura = $oggOgg->parsingStruttura('si');
		for ($i=0;$i<count($oggOgg->campiAdmin);$i++) {
			$campiVisualizzati[$i]['campo'] = $oggOgg->campiAdmin[$i]; 
			$campiVisualizzati[$i]['etichetta'] = $oggOgg->campiEtiAdmin[$i]; 
			$campiVisualizzati[$i]['proprieta'] = $oggOgg->campiPropAdmin[$i]; 	
			$campoStr = campoStruttura($campiVisualizzati[$i]['campo'],$struttura);
			if (strpos($campoStr['tipocampo'],'*') !== false) {
				$campoStr['tipocampo'] = substr($campoStr['tipocampo'], 1);	
			}
			$campiVisualizzati[$i]['tipo'] = $campoStr['tipocampo'];
			$campiVisualizzati[$i]['etichette'] = $campoStr['proprieta'];
			$campiVisualizzati[$i]['valore'] = $campoStr['valorecampo'];
		}		

		include ('./app/admin_template/oggetti/tab_start.tmp');
		$visualizzaInterfacciaSelect = true;
		foreach ($listaTabella as $istanzaOggetto) {
			$checked = '';
			if(in_array($istanzaOggetto['id'], $arrayIstanze) and $istanzaOggetto['id'] > 0 or in_array($istanzaOggetto['id_ori'], $arrayIstanze) and $istanzaOggetto['id_ori'] > 0) {
				$checked = ' checked="checked" ';
			}
			include ('./app/admin_template/oggetti/tab_row.tmp');
		}
		include ('./app/admin_template/oggetti/tab_end.tmp');
	
	break;	
}