<?php

// importo la classe di amministrazione contenuto
require ('./pat/classi/admin_enti.php');
$entiClasse = new enti();

switch ($azione) {

	//////////////////LISTA ENTI INSTALLATI///////

	case "lista" :
		// controllo permessi
		if ($datiUser['permessi'] != 10) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione degli enti.');
		} else {
		
			///////////////////// RISPOSTA ALLE AZIONI FORM //////////////////////////////
			$codiceErrore = '';
			
			// aggiunta ente
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'aggiungi') {

				$idPerAllegati = date('yzGis');
				$prog = 0;	
				$operazione = true;
				
				// prima di proseguire, verifico upload dei file
				if ($_FILES['file_logo_semplice']['name'] != '') {			
					$nomeFileReplace = str_replace("\'", "_", $_FILES['file_logo_semplice']['name']);
					if (!(copy($_FILES['file_logo_semplice']['tmp_name'], $uploadPath."/enti_pat/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$_FILES['file_logo_semplice']['name'].". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';					
					}   				
					$_POST['file_logo_semplice'] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
					$prog++;
				} 	
				if ($_FILES['file_logo_etrasp']['name'] != '') {			
					$nomeFileReplace = str_replace("\'", "_", $_FILES['file_logo_etrasp']['name']);
					if (!(copy($_FILES['file_logo_etrasp']['tmp_name'], $uploadPath."/enti_pat/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$_FILES['file_logo_etrasp']['name'].". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';					
					}   				
					$_POST['file_logo_etrasp'] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
					$prog++;
				}
				if ($_FILES['file_organigramma']['name'] != '') {			
					$nomeFileReplace = str_replace("\'", "_", $_FILES['file_organigramma']['name']);
					if (!(copy($_FILES['file_organigramma']['tmp_name'], $uploadPath."/enti_pat/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$_FILES['file_organigramma']['name'].". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';					
					}   				
					$_POST['file_organigramma'] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
					$prog++;
				}
			
				if ($operazione) {
					if ($entiClasse->aggiungiEnte($_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Aggiunta di un nuovo ente effettuata con successo.";
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta di un nuovo ente. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
				}
			}
			
			// modifica ente
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {

				// carico ente in modifica
				$istanzaOggetto = $entiClasse->caricaEnte($id);
				
				$idPerAllegati = date('yzGis');
				$prog = 0;	
				$operazione = true;
				
				// prima di proseguire, verifico upload dei file
				if ($_FILES['file_logo_semplice']['name'] != '') {			
					$nomeFileReplace = str_replace("\'", "_", $_FILES['file_logo_semplice']['name']);
					if (!(copy($_FILES['file_logo_semplice']['tmp_name'], $uploadPath."/enti_pat/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$_FILES['file_logo_semplice']['name'].". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';					
					}   				
					$_POST['file_logo_semplice'] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
					$prog++;
				} 	
				if ($_FILES['file_logo_etrasp']['name'] != '') {			
					$nomeFileReplace = str_replace("\'", "_", $_FILES['file_logo_etrasp']['name']);
					if (!(copy($_FILES['file_logo_etrasp']['tmp_name'], $uploadPath."/enti_pat/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$_FILES['file_logo_etrasp']['name'].". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';					
					}   				
					$_POST['file_logo_etrasp'] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
					$prog++;
				}
				if ($_FILES['file_organigramma']['name'] != '') {			
					$nomeFileReplace = str_replace("\'", "_", $_FILES['file_organigramma']['name']);
					if (!(copy($_FILES['file_organigramma']['tmp_name'], $uploadPath."/enti_pat/".$idPerAllegati.$prog."O__O".$nomeFileReplace))) {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$_FILES['file_organigramma']['name'].". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';					
					}   				
					$_POST['file_organigramma'] = $idPerAllegati.$prog."O__O".$nomeFileReplace;
					$prog++;
				}
			
				// proseguo con la registrazione utente
				if ($operazione) {
					if ($entiClasse->modificaEnteParziale($id, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Modifica ente effettuata con successo.";
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in modifica ente. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
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
				
						$codiceErrore = '';
						$numCancellate = $entiClasse->cancellaEnti($cancello);
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." enti.";
							
						} else {
							// ERRORI NELL'OPERAZIONE
							$operazione = false;
							$operazioneTesto = "Problemi in eliminazione enti. Riprovare in seguito.";
							$codiceErrore = '#00 - Generico';
						}

					} 
					
					break;

					////////////////// BLOCCA //////////
				case "blocca" :
					if ($_POST['oggettiSelezionati'] != '') {
						$numBloccate = $utenti->bloccaUtenti($_POST['oggettiSelezionati'], 0);
						if ($configurazione['avvisa_utenti_bloccati']) {
							$txtLog = '<p>[' . $numBloccate . ' utenti bloccati con successo. Sono state inviate mail di notifica agli utenti interessati.]</p>';
						} else {
							$txtLog = '<p>[' . $numBloccate . ' utenti bloccati con successo. Il sistema, non ha inviato mail di notifica agli utenti interessati.]</p>';
						}
					} else {
						$txtLog = '<p>[Non hai selezionato nessun utente da bloccare.]</p>';
					}
					break;

					////////////////// ATTIVA //////////
				case "attiva" :
					if ($_POST['oggettiSelezionati'] != '') {
						$numAttivate = $utenti->bloccaUtenti($_POST['oggettiSelezionati'], 1);
						if ($configurazione['avvisa_utenti_bloccati']) {
							$txtLog = '<p>[' . $numAttivate . ' utenti attivati con successo. Sono state inviate mail di notifica agli utenti interessati.]</p>';
						} else {
							$txtLog = '<p>[' . $numAttivate . ' utenti attivati con successo. Il sistema, non ha inviato mail di notifica agli utenti interessati.]</p>';
						}
					} else {
						$txtLog = '<p>[Non hai selezionato nessun utente da attivare.]</p>';
					}
					break;
			}
			
			$listaTabella = $entiClasse->caricaEnti();
			include ('./pat/admin_template/enti/tab_start.tmp');
			$visualizzaInterfaccia = true;
			foreach ($listaTabella as $istanzaOggetto) {
				include ('./pat/admin_template/enti/tab_row.tmp');
			}
			
			include ('./pat/admin_template/enti/tab_end.tmp');
		
		}
		break;

	//////////////////AGGIUNGI///////

	case "aggiungi" :

		if ($datiUser['permessi'] != 10) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione degli enti.');
		} else {

			$id = 0;
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			$istanzaOggetto['nome_completo_ente'] = '';
			$istanzaOggetto['nome_breve_ente'] = '';
			$istanzaOggetto['url_etrasparenza'] = '';
			
			// qui includo la pagina con il form
			include ('./pat/admin_template/enti/form.tmp');

		}
		break;

	//////////////////MODIFICA///////

	case "modifica" :

		if ($datiUser['permessi'] != 10 AND !$aclTrasparenza['admin']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione degli enti.');
		} else {
			// carico l'user da modificare
			$istanzaOggetto = $entiClasse->caricaEnte($id);
			// ulteriore controllo permesssi
			if ($aclTrasparenza['admin'] AND ($datiUser['permessi'] == 10 OR $datiUser['id_ente_admin']==$istanzaOggetto['id']) ) {

				// qui includo la pagina con il form
				include ('./pat/admin_template/enti/form.tmp');

			} else {
				motoreLog('permessonegato', 'Non hai i permessi necessari per modificare questo ente.', FALSE);
			}
		}
		break;
}
?>
