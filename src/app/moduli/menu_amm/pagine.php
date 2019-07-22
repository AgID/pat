<?php
$idOggetto = 33;
require_once('classi/admin_oggetti.php');
$oggOgg = new oggettiAdmin($idOggetto); 

switch ($menuSecondario) {

	//////////////////CONTENUTI NORMALI///////

	case "pagine" :
	
		/*
		echo "Post: <pre>";
		print_r($_POST);
		echo "</pre>";
		*/
		
		////////////////// CONTENUTO EDITABILE
		// creo array delle sezioni da escludere in base al tipo di ente
		$sezioniEsclusione = explode(',',$tipoEnte['sezioni_esclusione']);
		
		if (isset($_POST['contenutoModello'.$id]) and $azione == 'edita') {
		
			// ho inviato richiesta di editing di un modello (solo contenuto html)
			$modelloCaricato = datoModelloTraspById($idEnteAdmin,$id);
			$idSezione = $_POST['id_sezione'];
			
			$contenuto = $_POST['contenutoModello'.$id];
			$contenuto = addslashes(correttoreHtml($contenuto));
			$_POST['html_generico'] = $contenuto;
			
			$titolo = $_POST['titolo'.$id];
			$_POST['titolo'] = $titolo;
			
			$ordine = $_POST['ordine'.$id];
			$_POST['ordine'] = $ordine;
			
			if (!$modelloCaricato) {
			
				// il paragrafo non esiste, effettuo un insert
				if (aggiungiModelloTrasp($idEnteAdmin,$idSezione,$_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del contenuto (nuovo modello) effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del contenuto (nuovo modello). Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
			} else {
			
				// il paragarfo esiste, devo solo effettuare un update
				if (modificaDatoModelloTraspCompleto($idEnteAdmin,$modelloCaricato['id'],$modelloCaricato['id_sezione_etrasp'],$_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del contenuto in ".addslashes(nomeSezDaId($modelloCaricato['id_sezione_etrasp']))." effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del contenuto in ".addslashes(nomeSezDaId($modelloCaricato['id_sezione_etrasp'])).". Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
			}
		}
		
		if (isset($_POST['id_cancello_contenuto']) and $azione == 'cancellacont') {
		
			// ho inviato richiesta di editing di un modello (solo contenuto html)
			$modelloCaricato = datoModelloTraspById($idEnteAdmin,$id);
			
			$contenuto = '';
			
			// il paragarfo esiste, devo solo effettuare un update
			if (modificaDatoModelloTrasp($idEnteAdmin,$modelloCaricato['id'],$modelloCaricato['id_sezione_etrasp'],$contenuto)) {
				// OPERAZIONE ANDATA A BUON FINE
				$operazione = true;
				$operazioneTesto = "Eliminazione del contenuto in ".addslashes(nomeSezDaId($modelloCaricato['id_sezione_etrasp']))." effettuata con successo.";
				
			} else {
				// ERRORI NELL'OPERAZIONE
				$operazione = false;
				$operazioneTesto = "Problemi in eliminazione del contenuto in ".addslashes(nomeSezDaId($modelloCaricato['id_sezione_etrasp'])).". Riprovare in seguito.";
				$codiceErrore = '#00 - Generico';
			}

		}
		
		if (isset($_POST['id_sezione_nuovocont']) and $azione == 'nuovocont') {
		
			$idSezione = $_POST['id_sezione_nuovocont'];
			
			//prendo ultima priorita ed inserisco l'elemento
			$priorita = getLastPrioritaModelloPagina($idEnteAdmin, $idSezione, $_POST['tipologia']) + 1;
			
			if (aggiungiModelloTraspPagina($idEnteAdmin,$idSezione, $priorita, $_POST)) {
				// OPERAZIONE ANDATA A BUON FINE
				$operazione = true;
				$operazioneTesto = "Inserimento del nuovo elemento effettuata con successo.";
					
			} else {
				// ERRORI NELL'OPERAZIONE
				$operazione = false;
				$operazioneTesto = "Problemi in aggiunta del nuovo elemento. Riprovare in seguito.";
				$codiceErrore = '#00 - Generico';
			}
		}
		
		if (isset($_POST['id_modello']) and $azione == 'cancellazioneCompleta') {
			
			$modelloEliminato = datoModelloTraspById($idEnteAdmin,$_POST['id_modello']);
			
			if (eliminaModelloTrasp($_POST['id_modello'])) {
				// OPERAZIONE ANDATA A BUON FINE
				$operazione = true;
				$operazioneTesto = "Eliminazione completa elemento effettuata con successo.";
					
			} else {
				// ERRORI NELL'OPERAZIONE
				$operazione = false;
				$operazioneTesto = "Problemi in eliminazione completa elemento. Riprovare in seguito.";
				$codiceErrore = '#00 - Generico';
			}
		}
		
		////////////////// RICHIAMI
		if (isset($_GET['nome']) AND count($_POST) AND $azione == 'editaobj') {

			// ho inviato richiesta di editing di un modello (un richiamo oggetto)
			$modelloCaricato = datoModelloTraspById($idEnteAdmin,$id);
			$idSezione = $_POST['id_sezione'];
			
			//echo "Ciclo di editing richiami oggetto per sezione: ".$id

			$campi = array();
			$valori = array();
			
			// titolo
			$campi[] = $_GET['nome']."_tit";
			$valori[] = addslashes(htmlentities($_POST[$id."_".$_GET['nome']."_tit"]));
			$_POST[$_GET['nome']."_tit"] = addslashes(htmlentities($_POST[$id."_".$_GET['nome']."_tit"]));

			// richiamo selezione
			$campi[] = $_GET['nome'];
			$valori[] = implode(',',$_POST[$id."_".$_GET['nome']]);
			$_POST[$_GET['nome']] = implode(',',$_POST[$id."_".$_GET['nome']]);

			// opzioni
			$campi[] = $_GET['nome']."_opz";
			$valori[] = $_POST[$id."_".$_GET['nome']."_opz"];
			$_POST[$_GET['nome']."_opz"] = $_POST[$id."_".$_GET['nome']."_opz"];
			
			
			if (!$modelloCaricato) {
				// il paragrafo non esiste, effettuo un update
				if (aggiungiModelloTrasp($idEnteAdmin,$idSezione,$_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del contenuto di ".$_GET['nome']." (nuovo modello) in ".addslashes(nomeSezDaId($id))." effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del richiamo di ".$_GET['nome']." (nuovo modello) in ".addslashes(nomeSezDaId($id)).". Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}		
			} else {
				// il paragarfo esiste, devo solo effettuare un update
				if (modificaDatoModelloTrasp($idEnteAdmin,$modelloCaricato['id'],$modelloCaricato['id_sezione_etrasp'],$valori,$campi)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del richiamo di ".$_GET['nome']." in ".addslashes(nomeSezDaId($id))." effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del richiamo di ".$_GET['nome']." in ".addslashes(nomeSezDaId($id)).". Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
			}
		}
		
		//modifica con workflow - inizio
		if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'editpagina') {
		
			// importo la classe di amministrazione oggetti
			
			$istanzaOggetto = $oggOgg->caricaOggetto($id);
			$idCategoria = $istanzaOggetto['id_sezione'];
		
			$operazione = true;
			
			//workflow
			$_POST['stato_workflow'] = $_POST['stato_workflow_da_assegnare'];
			if( moduloAttivo('workflow')  and $_POST['stato_workflow_attuale'] != $_POST['stato_workflow_da_assegnare']) {
				if($_POST['stato_workflow_attuale'] == 'iniziale' or $_POST['stato_workflow_attuale'] == 'finale') {
					$wf = $oggOgg->caricaWorkflowUtenteIniziale();
				} else if($_POST['stato_workflow_attuale'] != '') {
					$wf = $oggOgg->caricaWorkflowStato($_POST['stato_workflow_attuale']);
				}
				if($wf['id']) {
					$notificaFinale == false;
					if($_POST['stato_workflow_da_assegnare'] == 'iniziale') {
						$statoWf = array('id' => 'iniziale', 'nome' => 'iniziale', 'utenti' => $wf['utenti']);
					} else if($_POST['stato_workflow_da_assegnare'] == 'finale') {
						$statoWf = array('id' => 'finale', 'nome' => 'finale', 'utenti' => $wf['utenti']);
						$utentiIniziali = explode(',', $wf['utenti']);
						if(in_array($datiUser['id'], $utentiIniziali)) {
							$notificaFinale == true;
						}
					} else {
						$statiWf = unserialize(base64_decode($wf['composizione_workflow']));
						foreach((array)$statiWf as $statoWf) {
							if($statoWf['id'] == $_POST['stato_workflow_da_assegnare']) {
								break;
							}
						}
					}
					
					$nomeSezione = nomeSezDaId($istanzaOggetto['id_sezione_etrasp']);
		
					//prendo gli utenti e notifico l'inserimento
					$utentiWf = explode(',', $statoWf['utenti']);
					foreach((array)$utentiWf as $ut) {
						if($ut) {
							$mailDestinatario = nomeUserDaId($ut, 'email');
							$subject = $configurazione['denominazione_trasparenza'].' - Workflow Pagine generiche - Elemento nello stato '.$statoWf['nome'];
							$testo = nomeUserDaId($ut, 'nome').',<br />'."\r\n".'La pagina <strong>'.$nomeSezione.'</strong> &egrave; stata modificata nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_trasparenza'].'</a>' .
									' nel workflow <strong>'.$wf['nome'].'</strong>.<br />'."\r\n".
									'<a href="'.$server_url.'adm_pagina_'.$id.'.html">Accedi al portale per verificare la pagina.</a>';
							$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
						}
					}
					if($notificaFinale) {
						//invio mail di notifica in caso di workflow
						$utentiWfNotifica = explode(',', $wf['id_utenti_finali']);
						foreach((array)$utentiWfNotifica as $ut) {
							if($ut) {
								$mailDestinatario = nomeUserDaId($ut, 'email');
								$subject = $configurazione['denominazione_trasparenza'].' - Pagine generiche - Elemento nello stato finale';
								$testo = nomeUserDaId($ut, 'nome').',<br />'."\r\n".'La pagina <strong>'.$nomeSezione.'</strong> &egrave; stata modificata nel portale <a href="'.$server_url.'">'.$configurazione['denominazione_trasparenza'].'</a>' .
										' nello stato finale.<br />'."\r\n".
										'<a href="'.$server_url.'adm_pagine_'.$id.'.html">Accedi al portale per visualizzare la pagina.</a>';
								$oggOgg->inviaMailWorkflow($mailDestinatario, $subject, $testo);
							}
						}
					}
					//lognormale('',$statoWf);
				}
			}
			
			if(!isset($_POST['data_revisione']) or $_POST['data_revisione'] == '') {
				$_POST['data_revisione'] = $istanzaOggetto['data_revisione'];
				if(!isset($_POST['data_revisione']) or $_POST['data_revisione'] == '') {
					$_POST['data_revisione'] = 'NULL';
				}
			}
			if(!isset($_POST['data_notifica']) or $_POST['data_notifica'] == '') {
				$_POST['data_notifica'] = $istanzaOggetto['data_notifica'];
				if(!isset($_POST['data_notifica']) or $_POST['data_notifica'] == '') {
					$_POST['data_notifica'] = 'NULL';
				}
			}
			
			if ($operazione) {
				if ($oggOgg->modificaOggetto($id, $idCategoria, $_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica effettuata con successo.";
		
					//aggiungo il dettaglio del workflow dell'azione effettuata per questa istanza
					if(count($utentiWf) and $oggOgg->idAzioneLog) {
						include_once('classi/log_azione.php');
						$log = new logAzione();
						$istanzaLog = $log->caricaLog(array('id'=>$oggOgg->idAzioneLog));
						$istanzaLog  = $istanzaLog[0];
						$testoWf = 'L\'elemento '.$_POST[$oggOgg->campo_default].' é stato modificato nel workflow '.$wf['nome'].' nello stato '.$statoWf['nome'];
						$log->aggiungiDettaglioLog($istanzaLog['id'], array(
								'workflow' => $testoWf
						));
					}
		
					if($notificaFinale) {
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
		//modifica con workflow - fine
		
		include ('./app/admin_template/contenuti/pagine.tmp');
	break;
	
	//////////////////ARCHIVIO MEDIA///////

	case "archiviomedia" :	
		if (!$aclTrasparenza['archiviomedia']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per la gestione di immagini ed allegati.');
		} else {
			include ('./app/admin_template/contenuti/archiviomedia.tmp');
		}
	break;
	
	//////////////////CONTENUTI SPECIALI///////

	case "speciali" :
		include ('./app/admin_template/contenuti/speciali.tmp');
	break;
}
?>
