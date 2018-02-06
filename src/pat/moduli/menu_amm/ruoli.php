<?php

// importo la classe di amministrazione contenuto
require ('./pat/classi/admin_ruoli.php');
$ruoliClasse = new ruoli();

// importo la classe di amministrazione sezioni
require_once ('classi/admin_sezioni.php');
$sezAdmin = new sezioniAdmin();

switch ($azione) {

	//////////////////LISTA RUOLI INSTALLATI///////

	case "lista" :
	
		// controllo permessi
		if (!$aclTrasparenza['ruoli']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei Profili ACL.');
		} else {
			///////////////////// RISPOSTA ALLE AZIONI FORM //////////////////////////////
			$codiceErrore = '';
			
			// aggiunta ruolo
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'aggiungi') {

				$operazione = true;				
			
				if ($operazione) {

					if ($ruoliClasse->aggiungiRuolo($_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Aggiunta di un nuovo profilo effettuata con successo.";
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta di un nuovo profilo. Riprovare in seguito.";
						$codiceErrore = '#00 - Generico';
					}
				}
			}
			
			// modifica ruolo
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {
				/*
				echo "<pre>";
				print_r($_POST);
				echo "</pre>";
				*/

				// carico ente in modifica
				$istanzaOggetto = $ruoliClasse->caricaRuolo($id);
				
				$operazione = true; 	
			
				// proseguo con la registrazione utente
				if ($operazione) {
					if ($ruoliClasse->modificaRuolo($id, $_POST)) {
						// OPERAZIONE ANDATA A BUON FINE
						$operazione = true;
						$operazioneTesto = "Modifica profilo effettuata con successo.";
						
					} else {
						// ERRORI NELL'OPERAZIONE
						$operazione = false;
						$operazioneTesto = "Problemi in modifica profilo. Riprovare in seguito.";
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
						$numCancellate = $ruoliClasse->cancellaRuoli($cancello);
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." profili.";
							
						} else {
							// ERRORI NELL'OPERAZIONE
							$operazione = false;
							$operazioneTesto = "Problemi in eliminazione profili. Riprovare in seguito.";
							$codiceErrore = '#00 - Generico';
						}

					} 
					
					break;
			}
			
			$listaTabella = $ruoliClasse->caricaRuoli($idEnteAdmin);
			include ('./pat/admin_template/ruoli/tab_start.tmp');
			$visualizzaInterfaccia = true;
			foreach ($listaTabella as $istanzaOggetto) {
				include ('./pat/admin_template/ruoli/tab_row.tmp');
			}
			
			include ('./pat/admin_template/ruoli/tab_end.tmp');
		
		}
		break;

	//////////////////AGGIUNGI///////

	case "aggiungi" :
		// controllo permessi
		if (!$aclTrasparenza['ruoli']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei Profili ACL.');
		} else {	
			$id = 0;
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			$istanzaOggetto['nome_completo_ente'] = '';
			$istanzaOggetto['nome_breve_ente'] = '';
			$istanzaOggetto['url_etrasparenza'] = '';
			
			// qui includo la pagina con il form
			include ('./pat/admin_template/ruoli/form.tmp');
		}

		break;

	//////////////////MODIFICA///////

	case "modifica" :
		// controllo permessi
		if (!$aclTrasparenza['ruoli']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei Profili ACL.');
		} else {			
			// carico l'user da modificare
			$istanzaOggetto = $ruoliClasse->caricaRuolo($id);

			// ulteriore controllo permesssi, verifico che non ci siano chiamate GET forzate verso altri enti			
			if (($aclTrasparenza['ruoli'] == 2 OR $istanzaOggetto['id_ente'] == $datiUser['id_ente_admin']) AND ($id != 0 OR $datiUser['permessi']==10)) {
				// ora deserializzo i permessi
				foreach ($arrayFunzioniObj as $funzione) {
					$istanzaOggetto['permessiOggetto'][$funzione] = unserialize($istanzaOggetto[$funzione]);
				}
				$istanzaOggetto['permessiSezione'] = unserialize($istanzaOggetto['contenuti']);
				
				/*
				echo "<pre>";
				print_r($istanzaOggetto['permessiOggetto']);
				echo "<pre>";
				*/
				
				// qui includo la pagina con il form
				include ('./pat/admin_template/ruoli/form.tmp');
			} else {
				motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per modificare questo profilo ACL.');
			}
			

		}
		break;
}
?>
