<?php

// importo la classe di amministrazione contenuto
require ('./app/classi/admin_moduli_personalizzati.php');
$moduliPersonalizzati = new moduliPersonalizzati();

switch ($azione) {

	//////////////////LISTA ENTI INSTALLATI///////

	case "lista" :
		// controllo permessi
		if ($datiUser['permessi'] != 10 and $datiUser['permessi'] != 3) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei moduli personalizzati.');
		} else {
		
			///////////////////// RISPOSTA ALLE AZIONI FORM //////////////////////////////
			$codiceErrore = '';
			
			// aggiunta 
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'aggiungi') {

				$operazione = true;
			
				if ($operazione) {
					if ($moduliPersonalizzati->aggiungiModulo($_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Aggiunta di un nuovo modulo personalizzato effettuata con successo.";
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta di un nuovo modulo personalizzato. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
				}
			}
			
			// modifica 
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {

				// carico modulo in modifica
				$istanzaOggetto = $moduliPersonalizzati->caricaModulo($id);
				
				$operazione = true;
				
				// proseguo con la registrazione
				if ($operazione) {
					if ($moduliPersonalizzati->modificaModulo($id, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Modifica modulo personalizzato effettuata con successo.";
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in modifica modulo personalizzato. Riprovare in seguito.";
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
						$numCancellate = $moduliPersonalizzati->cancellaModuli($cancello);
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." moduli personalizzati.";
							
						} else {
							// ERRORI NELL'OPERAZIONE
							$operazione = false;
							$operazioneTesto = "Problemi in eliminazione moduli personalizzati. Riprovare in seguito.";
							$codiceErrore = '#00 - Generico';
						}
					} 
					
				break;

				////////////////// BLOCCA //////////
				case "blocca" :
					if ($_POST['oggettiSelezionati'] != '') {
						$numBloccate = $moduliPersonalizzati->bloccaModuli($_POST['oggettiSelezionati'], 0);
						$operazione = true;
						$operazioneTesto = $numBloccate . ' moduli personalizzati bloccati con successo.';
					} else {
						$operazione = true;
						$operazioneTesto = 'Nessun modulo personalizzato bloccato.';
					}
				break;

				////////////////// ATTIVA //////////
				case "attiva" :
					if ($_POST['oggettiSelezionati'] != '') {
						$numAttivate = $moduliPersonalizzati->bloccaModuli($_POST['oggettiSelezionati'], 1);
						$operazione = true;
						$operazioneTesto = $numBloccate . ' moduli personalizzati attivati con successo.';
					} else {
						$operazione = true;
						$operazioneTesto = 'Nessun modulo personalizzato attivato.';
					}
				break;
			}
			
			$listaTabella = $moduliPersonalizzati->caricaModuli();
			include ('./app/admin_template/moduli_personalizzati/tab_start.tmp');
			$visualizzaInterfaccia = true;
			foreach ($listaTabella as $istanzaOggetto) {
				include ('./app/admin_template/moduli_personalizzati/tab_row.tmp');
			}
			
			include ('./app/admin_template/moduli_personalizzati/tab_end.tmp');
		}
	break;

	//////////////////AGGIUNGI///////

	case "aggiungi" :

		if ($datiUser['permessi'] != 10 and $datiUser['permessi'] != 3) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei moduli personalizzati.');
		} else {

			$id = 0;
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			
			// qui includo la pagina con il form
			include ('./app/admin_template/moduli_personalizzati/form.tmp');

		}
	break;

	//////////////////MODIFICA///////

	case "modifica" :

		if ($datiUser['permessi'] != 10 AND $datiUser['permessi'] != 3 AND !$aclTrasparenza['admin']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei moduli personalizzati.');
		} else {
			// carico istanza da modificare
			$istanzaOggetto = $moduliPersonalizzati->caricaModulo($id);
			// ulteriore controllo permesssi
			if ($aclTrasparenza['admin'] AND ($datiUser['permessi'] == 10 OR $datiUser['permessi'] == 3)) {

				// qui includo la pagina con il form
				include ('./app/admin_template/moduli_personalizzati/form.tmp');

			} else {
				motoreLog('permessonegato', 'Non hai i permessi necessari per modificare questo modulo personalizzato.', FALSE);
			}
		}
	break;
}
?>