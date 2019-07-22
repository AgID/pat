<?php

// importo la classe di amministrazione contenuto
require ('classi/admin_utenti.php');
$utenti = new utente();
$txtLog = '';


switch ($azione) {

	//////////////////LISTA UTENTI///////

	case "lista" :
		// controllo permessi
		if (!$aclTrasparenza['utenti'] and $id != $datiUser['id']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per gestire gli utenti.');
		} else {
		
			///////////////////// RISPOSTA ALLE AZIONI FORM //////////////////////////////
			
			// aggiunta utente
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'aggiungi') {
			
				// proseguo con la registrazione utente
				$codiceErrore = '';
				if ($utenti->aggiungiUtente($_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Aggiunta di un nuovo utente effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in aggiunta di un nuovo utente. Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
					
			}
			
			// modifica utente
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {
				
				// proseguo con la registrazione utente
				$codiceErrore = '';
				if ($utenti->modificaUtente($id, $_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica utente effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica utente. Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
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
						$numCancellate = $utenti->cancellaUtenti($cancello);
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." utenti.";
							
						} else {
							// ERRORI NELL'OPERAZIONE
							$operazione = false;
							$operazioneTesto = "Problemi in eliminazione utenti. Riprovare in seguito.";
							$codiceErrore = '#00 - Generico';
						}

					} 
					
					break;

					////////////////// BLOCCA //////////
				case "blocca" :
					$blocca = isset($_POST['id_blocca_tabella']) ? $_POST['id_blocca_tabella'] : 0;
					if ($blocca) {
						$numBloccate = $utenti->bloccaUtenti($blocca, 0);
						$operazione = true;
						$operazioneTesto = "Hai bloccato con successo ".$numBloccate." utenti.";
					} else {
						$operazione = false;
						$operazioneTesto = "Non hai selezionato nessun utente da bloccare.";
						$codiceErrore = '#00 - Generico';
					}
					break;

					////////////////// ATTIVA //////////
				case "attiva" :
					$attiva = isset($_POST['id_attiva_tabella']) ? $_POST['id_attiva_tabella'] : 0;
					if ($attiva) {
						$numAttivate = $utenti->bloccaUtenti($attiva, 1);
						$operazione = true;
						$operazioneTesto = "Hai attivato con successo ".$numAttivate." utenti.";
					} else {
						$operazione = false;
						$operazioneTesto = "Non hai selezionato nessun utente da attivare.";
						$codiceErrore = '#00 - Generico';
					}
					break;
			}

			// TABELLA DI VISUALIZZAZIONE E GESTIONE
			$condizione = '';
			//echo "<br />ente: ".$idEnteAdmin;
			if ($datiUser['permessi']!=10 AND $datiUser['permessi']!=3 AND $idEnteAdmin) {
				$condizione = 'WHERE id_ente_admin='.$idEnteAdmin;
			}
			$listaTabella = $utenti->visualizzaLista(0, 'tutti', 'data_registrazione', 'desc',$condizione);
			include ('./app/admin_template/utenti/tab_start.tmp');			
			
			if (!$aclTrasparenza['utenti'] and $id == $datiUser['id']) {
			} else {
				foreach ($listaTabella as $istanzaOggetto) {
					if ($istanzaOggetto['id'] != -1) {
						// verifico permessi
						$visualizzaInterfaccia = true;
						if ($istanzaOggetto['permessi']==10 OR $istanzaOggetto['id'] == $datiUser['id']) {
							$visualizzaInterfaccia = false;
						}
					
						include ('./app/admin_template/utenti/tab_row.tmp');
					}
				}
			}
			include ('./app/admin_template/utenti/tab_end.tmp');

		} // fine permessi
		break;

	//////////////////AGGIUNGI///////

	case "aggiungi" :

		// controllo permessi
		if (!$aclTrasparenza['utenti']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per aggiungere utenti.');
		} else {
			// controllo il totale degli amministratori
			$totaleAdmin = totaleAmministratori();

			$id = 0;
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			$istanzaOggetto['nome'] = '';
			$istanzaOggetto['password'] = '';
			$istanzaOggetto['permessi'] = 0;
			$istanzaOggetto['confermapassword'] = '';
			$istanzaOggetto['email'] = '';
			$istanzaOggetto['tipo'] = 0;
			$istanzaOggetto['username'] = '';
			$istanzaOggetto['admin_skin'] = 'classic';
			$istanzaOggetto['admin_accessibile'] = 0;
			$istanzaOggetto['admin_interfaccia'] = 'scomparsa';
			$istanzaOggetto['admin_avvisi'] = 'visualizza';
			
			// qui includo la pagina con il form
			include ('./app/admin_template/utenti/form.tmp');

		}
		break;

	//////////////////MODIFICA///////

	case "modifica" :

		// controllo permessi
		if (!$aclTrasparenza['utenti'] AND $id != $datiUser['id']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare gli utenti.');
		} else {
			// carico l'user da modificare
			$istanzaOggetto = $utenti->caricaUser($id);
			// ulteriore controllo permesssi, verifico che non ci siano chiamate GET forzate verso altri enti			
			if (($aclTrasparenza['utenti'] == 2 OR $istanzaOggetto['id_ente_admin'] == $datiUser['id_ente_admin']) AND ($id != 0 OR $datiUser['permessi']==10 OR $datiUser['permessi']==3)) {
				// qui includo la pagina con il form
				include ('./app/admin_template/utenti/form.tmp');
			} else {
				motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare questo utente.');
			}
		}
		break;

	//////////////////REVIEW///////

	case "review" :

		// carico l'user da visualizzare
		$istanzaUtente = $utenti->caricaUser($id);
		$txtLog = '<p>[Visualizzo utente <b>' . $istanzaUtente['username'] . '</b>]</p>';
		// includo la testata
		include ('template/admin_standard/utenti/contenuto_listautenti_campi.tmp');

		// qui includo la pagina con il form
		include ('template/admin_standard/utenti/review.tmp');

		break;

}
?>
