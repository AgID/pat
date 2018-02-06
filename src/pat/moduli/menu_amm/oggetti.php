<?
//imposto i seguenti valori in post se non presenti
$_POST['id_atto_albo'] = ($_POST['id_atto_albo'] ? $_POST['id_atto_albo'] : 0);
$_POST['stato_pubblicazione'] = ($_POST['stato_pubblicazione'] ? $_POST['stato_pubblicazione'] : '100');

// verifico di aver richiamato un oggetto 
if ($funzioneSottoMenu['idOggetto']) {
	$idOggetto = $funzioneSottoMenu['idOggetto'];
}

// importo la classe di amministrazione oggetti
require('classi/admin_oggetti.php');
$oggOgg = new oggettiAdmin($idOggetto);

if(file_exists('pat/moduli/menu_amm/operazioni/oggetti/'.$menuSecondario.'.php')) {
	$opOgg = $menuSecondario;
} else {
	$opOgg = 'OperazioneDefault';
}
require('pat/moduli/menu_amm/operazioni/oggetti/'.$opOgg.'.php');
$opOgg = new $opOgg();

switch ($azione) {

	//////////////////LISTA RECORD DI OGGETTO///////

	case "lista" :
	
		if (!$aclTrasparenza[$menuSecondario]['lettura']) {
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
						$idCategoriaPasso=$_POST['scelta_categoria'];
					}
					
					//workflow
					$_POST['stato_workflow'] = $_POST['stato_workflow_da_assegnare'];
					if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow']) {
						//verificare se esiste un workflow per l'utente loggato
						$wf = $oggOgg->caricaWorkflowUtenteIniziale();
						if($wf['id']) {
							$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
							//sono in inserimento => lo stato corrente � lo stato 0
							foreach((array)$statiWf as $statoWf) {
								if($statoWf['id'] == $_POST['stato_workflow_da_assegnare']) {
									//prendo gli utenti e notifico l'inserimento
									$utentiWf = explode(',', $statoWf['utenti']);
									break;
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
						
						//invio mail in caso di workflow
						foreach((array)$utentiWf as $ut) {
							if($ut) {
								$mailDestinatario = nomeUserDaId($ut, 'email');
								$subject = $configurazione['denominazione_etrasparenza'].' - Workflow '.$oggettiTrasparenza[$idOggetto]['nomeMenu'].' - Elemento nello stato '.$statoWf['nome'];
								$testo = nomeUserDaId($ut).'L\'elemento <strong>'.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].')</strong> &egrave; stato inserito nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_etrasparenza'].'</a>' .
										' nel workflow <strong>'.$wf['nome'].'</strong>.<br />' .
										'<a href="'.$server_url.'admin_pat.php?menu='.$oggettiTrasparenza[$idOggetto]['menu'].'&amp;menusec='.$oggettiTrasparenza[$idOggetto]['menuSec'].'&amp;azione=modifica&amp;id='.$oggOgg->lastInsertId.'">Accedi al portale per verificare l\'elemento.</a>';
								$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
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
				
				// tipo di salvataggio dei campi allegato, per il momento forzo
				$salvaDef = 1;
				
				if($istanzaOggetto['id_atto_albo']) {
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
								if ($salvaDef) {
									// prima cancello il vecchio file dal filesystem
									if ($istanzaOggetto[$campoTemp['nomecampo']] != '') {
										@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
									}
									// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
									$nomeFileReplace = str_replace("\'", "_", $_FILES[$campoTemp['nomecampo']]['name']);
									$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
									copy($_FILES[$campoTemp['nomecampo']]['tmp_name'], $uploadPath.$oggOgg->tabellaOggetto."/".$idPerAllegati.$prog."O__O".$nomeFileReplace)
										or motoreLog("avviso","Errore durante il trasferimento del file <b>".$_FILES[$campoTemp['nomecampo']]['name']."</b>. Riprovare pi� tardi. <a href=\"javascript:history.back();void(0);\">[Torna indietro]</a>",TRUE);
									$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;

								} else {
									// salvo il file nella cartella temporanea  senza elminare l'altro
									// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
									$nomeFileReplace = str_replace("\'", "_", $_FILES[$campoTemp['nomecampo']]['name']);
									$nomeFileReplace = correttoreCaratteriFile($nomeFileReplace);
									copy($_FILES[$campoTemp['nomecampo']]['tmp_name'], $uploadPath.$oggOgg->tabellaOggetto."/temp/".$idPerAllegati.$prog."O__O".$nomeFileReplace)
										or motoreLog("avviso","Errore durante il trasferimento del file <b>".$_FILES[$campoTemp['nomecampo']]['name']."</b>. Riprovare pi� tardi. <a href=\"javascript:history.back();void(0);\">[Torna indietro]</a>",TRUE);
									$_POST[$campoTemp['nomecampo']] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
								}
								$prog++;
							} else if (($_POST[$campoTemp['nomecampo']."azione"] == 'nessuna' or ($_FILES[$campoTemp['nomecampo']]['tmp_name'] == '' and $_POST[$campoTemp['nomecampo']."azione"] == 'modifica'))) {
								// ho deciso di non modificare il campo file, o ho scelto di modificarlo ma l'ho lasciato vuoto
								$_POST[$campoTemp['nomecampo']] = $istanzaOggetto[$campoTemp['nomecampo']];
							} else if ($_POST[$campoTemp['nomecampo']."azione"] == 'elimina') {
								if ($salvaDef) {
									// devo eliminare il file
									if ($istanzaOggetto[$campoTemp['nomecampo']] != '') {
										@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
									}
								}					
								$_POST[$campoTemp['nomecampo']] = '';
							} 
						}
					}

				}
				
				//workflow
				//lognormale('',$_POST);
				$_POST['stato_workflow'] = $_POST['stato_workflow_da_assegnare'];
				if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow'] and $_POST['stato_workflow_attuale'] != $_POST['stato_workflow_da_assegnare']) {
					if($_POST['stato_workflow_attuale'] == 'iniziale' or $_POST['stato_workflow_attuale'] == 'finale') {
						$wf = $oggOgg->caricaWorkflowUtenteIniziale();
					} else {
						$wf = $oggOgg->caricaWorkflowStato($_POST['stato_workflow_attuale']);
					}
					if($wf['id']) {
						if($_POST['stato_workflow_da_assegnare'] == 'iniziale') {
							$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
						} else if($_POST['stato_workflow_da_assegnare'] == 'finale') {
							$statoWf = array('id' => 'finale', 'nome' => 'finale', 'utenti' => $wf['utenti']);
						} else {
							$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
							foreach((array)$statiWf as $statoWf) {
								if($statoWf['id'] == $_POST['stato_workflow_da_assegnare']) {
									break;
								}
							}	
						}
						//prendo gli utenti e notifico l'inserimento
						$utentiWf = explode(',', $statoWf['utenti']);
						foreach((array)$utentiWf as $ut) {
							if($ut) {
								$mailDestinatario = nomeUserDaId($ut, 'email');
								$subject = $configurazione['denominazione_etrasparenza'].' - Workflow '.$oggettiTrasparenza[$idOggetto]['nomeMenu'].' - Elemento nello stato '.$statoWf['nome'];
								$testo = nomeUserDaId($ut).'L\'elemento <strong>'.$_POST[$oggOgg->campo_default].' ('.$oggettiTrasparenza[$idOggetto]['nomeMenu'].')</strong> &egrave; stato modificato nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_etrasparenza'].'</a>' .
										' nel workflow <strong>'.$wf['nome'].'</strong>.<br />' .
										'<a href="'.$server_url.'admin_pat.php?menu='.$oggettiTrasparenza[$idOggetto]['menu'].'&amp;menusec='.$oggettiTrasparenza[$idOggetto]['menuSec'].'&amp;azione=modifica&amp;id='.$idIstanza.'">Accedi al portale per verificare l\'elemento.</a>';
								$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
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
				
				$idPerAllegati = date('yzGis');
				$prog = 0;	
				$operazione = true;
				
				$atto = caricaDocumentoEAlbo('atti', $_POST['id_atto_albo']);
				$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
				
				// controllo se nei campi personalizzati ci sono dei file
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
						$idCategoriaPasso=$_POST['scelta_categoria'];
					}				
			
					$opOgg->preInsert();
					if ($oggOgg->aggiungiOggetto($idCategoriaPasso, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Aggiunta effettuata con successo.";
						$opOgg->postInsert();
						
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
		
				$notificaSalvata = $oggOgg->inviaNotificaPush($idIstanza, $idOggetto, $_POST['id_ente']);
				if ($notificaSalvata) {
					$operazioneTesto = "Notifica push inviata.";
				} else {
					$operazioneTesto = "Problemi in invio notifica push. Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
				if ($notificaSalvata) {
					//codica ajax asincrono per l'invio delle notifiche
					include('pat/admin_template/oggetti/notifica_push_send.tmp');
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
						$opOgg->preDelete();
						$codiceErrore = '';
						$numCancellate = $oggOgg->cancellaOggetti($cancello);
						
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." elementi.";
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
				/*
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
				*/
				// analizzo quali campi visualizzare in amministrazione, prendendoli dalla struttura IsWEB
				$campiVisualizzati = array();
				$struttura = $oggOgg->parsingStruttura('si');
				if ($oggOgg->proprieta == 'contatto') {
					$arrayStat = $oggOgg->statContatto($struttura,$condizioneStat);
					include('template/admin_standard/oggetti/stat_contatto.tmp');
				}
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
				
				include ('./pat/admin_template/oggetti/info_tab.tmp');
				
				include ('./pat/admin_template/oggetti/tab_start_ajax.tmp');
				
				/*
				$visualizzaInterfaccia = true;
				foreach ($listaTabella as $istanzaOggetto) {
					include ('./pat/admin_template/oggetti/tab_row.tmp');
				}
				*/
				include ('./pat/admin_template/oggetti/tab_end.tmp');
			} else {
				include ('./pat/admin_template/oggetti/salvataggio_box.tmp');
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
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			if($azione == 'importAtto') {
				$atto = caricaDocumentoEAlbo('atti', $_GET['ida']);
				$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
				$mapping = $mappingCampiAlbo[$idOggetto];
				foreach((array)$mapping as $campoTrasparenza => $campoAlbo) {
					$istanzaOggetto[$campoTrasparenza] = $atto[$campoAlbo];
					//lognormale($campoAlbo,$atto[$campoAlbo]);
				}
				if(file_exists('pat/ealbo/mapping/mapping_live_'.$enteAdmin['id'].'.php')) {
					//sovrascrivere qui dentro, in base alle esigenze, $mappingCampiAlbo
					include('pat/ealbo/mapping/mapping_live_'.$enteAdmin['id'].'.php');
				}
			}
			
			$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
			if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow'] and $azione == 'aggiungi') {
				//verificare se esiste un workflow per l'utente loggato
				$wf = $oggOgg->caricaWorkflowUtenteIniziale();
				if($wf['id']) {
					$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
					//sono in inserimento => lo stato corrente � lo stato 0
					$statoWf = $statiWf[0];
					$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
				}
			}
			
			// qui includo la pagina con il form
			include ('./pat/admin_template/oggetti/form/'.$menuSecondario.'.tmp');
		}
	break;

	//////////////////MODIFICA///////

	case "modifica" :
		if (!$aclTrasparenza[$menuSecondario]['modifica'] AND !$aclTrasparenza[$menuSecondario]['creazione']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare le informazioni di questo archivio.');
		} else {
			// carico l'user da modificare
			$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
			
			if($istanzaOggetto['id_atto_albo']) {
				$atto = caricaDocumentoEAlbo('atti', $istanzaOggetto['id_atto_albo']);
				$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
			}
			
			$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
			if( moduloAttivo('workflow') and $oggettiTrasparenza[$idOggetto]['workflow']) {
				if($istanzaOggetto['stato_workflow'] == 'iniziale') {
					$wf = $oggOgg->caricaWorkflowUtenteIniziale();
					if($wf['id']) {
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
						$istanzaOggetto['stato_workflow_attuale'] = 'iniziale';
						$istanzaOggetto['stato_workflow_da_assegnare'] = $statiWf[0]['id'];
					} else {
						//verificare se l'utente loggato � intermedio
						$wf = $oggOgg->caricaWorkflowUtenteIntermedio();
						if($wf['id']) {
							$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
							$istanzaOggetto['stato_workflow_attuale'] = 'iniziale';
							$istanzaOggetto['stato_workflow_da_assegnare'] = 'iniziale';
						} else {
							$wf = $oggOgg->caricaWorkflowOggetto();
							if($wf['id']) {
								//esiste almeno un WF per questo oggetto che non coinvolge l'utente loggato
								$istanzaOggetto['stato_workflow_attuale'] = 'iniziale';
								$istanzaOggetto['stato_workflow_da_assegnare'] = 'iniziale';
							} else {
								//stato iniziale ma non esiste un WF per questo record (wf eliminato)
								$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
							}
						}
					}
				} else if($istanzaOggetto['stato_workflow'] != 'finale') {
					//verificare se esiste un workflow per questo elemento
					$wf = $oggOgg->caricaWorkflowStato($istanzaOggetto['stato_workflow']);
					if($wf['id']) {
						$utentiIniziali = explode(',', $wf['utenti']);
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						$indiceStato = 0;
						for($i = 0; $i < count($statiWf); $i++) {
							$sw = $statiWf[$i];
							if($sw['id'] == $istanzaOggetto['stato_workflow']) {
								$statoWf = $sw;
								$indiceStato = $i;
								$i = count($statiWf);  //forzo uscita dal ciclo
							}
						}
						if(in_array($datiUser['id'], $utentiIniziali)) {
							//caso a: modifica di un utente che ha dato il via al wf
							$istanzaOggetto['stato_workflow_attuale'] = $statoWf['id'];
							//rimetto il prossimo stato a quello iniziale
							$statoWf = $statiWf[0];
							$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
						} else {
							$utentiWf = explode(',', $statoWf['utenti']);
							if(in_array($datiUser['id'], $utentiWf)) {
								//caso b: utente che ha in carico questo stato di workflow: pu� portarlo avanti o farlo tornare indietro o semplicemente salvarlo mantenendo lo stato attuale
								$istanzaOggetto['stato_workflow_attuale'] = $statoWf['id'];
								$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
								$istanzaOggetto['stato_workflow_precedente'] = ($statiWf[$indiceStato-1]['id'] != '' ? $statiWf[$indiceStato-1]['id'] : 'iniziale');
								if($istanzaOggetto['stato_workflow_precedente'] == 'iniziale') {
									$statoWfPrecedente = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
								} else if($istanzaOggetto['stato_workflow_precedente'] != '') {
									$statoWfPrecedente = $statiWf[$indiceStato-1];
								}
								$istanzaOggetto['stato_workflow_successivo'] = ($statiWf[$indiceStato+1]['id'] != '' ? $statiWf[$indiceStato+1]['id'] : 'finale');
								if($istanzaOggetto['stato_workflow_successivo'] == 'finale') {
									$statoWfSuccessivo = array('id' => 'finale', 'nome' => 'finale', 'utenti' => $wf['utenti']);
								} else if($istanzaOggetto['stato_workflow_successivo'] != '') {
									$statoWfSuccessivo = $statiWf[$indiceStato+1];
								}
							} else {
								$istanzaOggetto['stato_workflow_attuale'] = $statoWf['id'];
								$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
							}
						}
					} else {
						//stato intermedio ma non esiste un WF per questo record (wf eliminato)
						$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
					}
				} else {
					//todo: finale -> se c'� un wf per l'utente loggato allora lo riavvio
					$wf = $oggOgg->caricaWorkflowUtenteIniziale();
					if($wf['id']) {
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						/*
						$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
						$istanzaOggetto['stato_workflow_attuale'] = 'finale';
						$istanzaOggetto['stato_workflow_da_assegnare'] = $statiWf[0]['id'];
						*/
						
						$statoWf = $statiWf[0];
						$istanzaOggetto['stato_workflow_da_assegnare'] = $statoWf['id'];
						$istanzaOggetto['stato_workflow_attuale'] = 'finale';
					} else {
						//stato finale ma non esiste un WF per l'utente loggato
						$istanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
					}
				}
				
			}

			// ulteriore controllo permesssi, verifico che non ci siano chiamate GET forzate verso altri enti			
			if (($aclTrasparenza[$menuSecondario]['modifica'] OR $istanzaOggetto['id_ente'] == $datiUser['id_ente_admin']) AND ($id != 0 OR $datiUser['permessi']==10)) {			
				include ('./pat/admin_template/oggetti/form/'.$menuSecondario.'.tmp');
			} else {
				motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare questa informazione, ma solo quelle a te assegnate.');
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
			
			include ('./pat/admin_template/oggetti/form/notifiche_push.tmp');
		}

	break;
		
		
	//////////////////IMPORTAZIONE(STRUMENTI)///////

	case "importa":

		if (!$aclTrasparenza[$menuSecondario]['avanzate']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per accedere agli strumenti avanzati di questo archivio.');
		} else {
		
			// importo la classe di amministrazione contenuto
			require('pat/classi/admin_informazioni.php');
			$informazioni = new infoAdmin();
			
			$id=$idOggetto;
		
			$istanzaOggetto = $informazioni->caricaOggetto($id);

			// carico storico importazioni precedenti per questo oggetto
			$storicoImport = $informazioni->caricaImportazioni($id);
			$settingsDefault = $informazioni->settingsImportDefault($id);
			
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
				
				// eseguo eventuale reset dati
				if ($_POST['reset_dati'] and $estFile == 'xls') {
					$sql = "DELETE FROM ".$dati_db['prefisso'].$informazioni->tabellaOggetto." WHERE id_ente=".$_POST['id_ente'];
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
										// opzione stringhe normali: correggo le entit� html	
										$stringa = addslashes(htmlentities($_POST['valore'.$num]));							
										$sqlDati .= "\"".$stringa."\"";
									} else {
										// controllo se il campo � una data
										if ($campoTemp['tipocampo'] == 'data_calendario') {
											// verifico il formato
											if (isset($_POST['campoprop'.$num]) and trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]])!='') {
												if ($_POST['campoprop'.$num] == 'gg/mm/aaaa') {
													list($giorno,$mese,$anno) = explode('/',trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
													if (@checkdate($mese,$giorno,$anno)) {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = mktime(0,0,0,$mese,$giorno,$anno); 
													} else {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = '';
													}
												} else if ($_POST['campoprop'.$num] == 'aaaammgg') {
													$anno = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 0, 4);
													$mese = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 4, 2);
													$giorno = substr(trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]), 6, 2);
													if (@checkdate($mese,$giorno,$anno)) {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = mktime(0,0,0,$mese,$giorno,$anno); 
													} else {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = '';
													}
												} else if ($_POST['campoprop'.$num] == 'gg-mm-aaaa') {	
													list($giorno,$mese,$anno) = explode('-',trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
													if (@checkdate($mese,$giorno,$anno)) {
														$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = mktime(0,0,0,$mese,$giorno,$anno); 
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
											// opzione stringhe normali: correggo le entit� html	
											if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==2) {
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(html_entity_decode($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											} else if (isset($_POST['campoprop'.$num]) and $_POST['campoprop'.$num]==1) {
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes(htmlentities($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]));
											} else {
												// non correggo le entit� html dalle stringhe
												$data->sheets[0]['cells'][$i][$_POST['campo'.$num]] = addslashes($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
											}
										}									
										if ($campoTemp['tipoinput'] == 'string' OR $campoTemp['tipoinput'] == 'blob' OR $campoTemp['tipoinput'] == 'text') {
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

									}
								}
								// verifico se � il campo di riconoscimento oggetto da inserire nel report
								if ($campoTemp['nomecampo']==$informazioni->campo_default) {
									$campoRic = trim($data->sheets[0]['cells'][$i][$_POST['campo'.$num]]);
								} 
							} 				
							$num++;
						}

						if ($sqlQuery != '' and $sqlDati != '') {
							$opOgg->preImport();
							$sql = "INSERT INTO ".$dati_db['prefisso'].$informazioni->tabellaOggetto." (stato,id_proprietario,data_creazione,ultima_modifica,id_ente,".$sqlQuery.") VALUES 
								(1,".$_POST['utente'].",".time().", ".time().",".$_POST['id_ente'].",".$sqlDati.")";
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
							$idInseriti .= mysql_insert_id();
						} 
					}

				} else {
					$txtLog = "<p>L'estensione del file non � riconosciuta per l'importazione in quetso archivio.</p>";
					$numImport = 0;	
					$numError =0;
				}     
				if ($numImport or $numError) {
					
					$txtLog = "<p>Importati con successo <b>".$numImport." record</b>(con <b>".$numError." errori</b>) in questo archivio.</p>.";
					// ricavo la variabile dei posizionamenti
					$settaggImport = serialize($posizionamenti);
					// creo lo storico del report
					$arrayValori = array(
						'oggetto' => $id,
						'id_ente' => $_POST['id_ente'],
						'utente' => $_POST['utente'],
						'categoria' => $_POST['categoria'],
						'reset_dati' => $_POST['reset_dati'],
						'default' => $_POST['default'],
						'reset_dati' => $_POST['reset_dati'],
						'file' => $_FILES["file"]["name"],
						'settings' => $settaggImport,
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
				
				include ('./pat/admin_template/oggetti/importa_log.tmp');
				
			} else {
				// qui includo la pagina con il form
				include ('./pat/admin_template/oggetti/importa.tmp');
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

		include ('./pat/admin_template/oggetti/tab_start.tmp');
		$visualizzaInterfacciaSelect = true;
		foreach ($listaTabella as $istanzaOggetto) {
			$checked = '';
			if(in_array($istanzaOggetto['id'], $arrayIstanze) and $istanzaOggetto['id'] > 0 or in_array($istanzaOggetto['id_ori'], $arrayIstanze) and $istanzaOggetto['id_ori'] > 0) {
				$checked = ' checked="checked" ';
			}
			include ('./pat/admin_template/oggetti/tab_row.tmp');
		}
		include ('./pat/admin_template/oggetti/tab_end.tmp');
	
	break;	
}