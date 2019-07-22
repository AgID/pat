<?php

// importo la classe di amministrazione
require_once ('classi/log_azione.php');
$logAzioni = new logAzione();
$txtLog = '';

switch ($azione) {

	//////////////////LISTA LOG///////
	case "lista" :
		// controllo permessi: tutti possono vedere il log
		if (($menuSecondario == 'log_utenti' and ($datiUser['id'] == $enteAdmin['utente_responsabile_trasparenza'] OR in_array($datiUser['id'], $entreAdmin['utenti_notifiche_sistema']))) or $menuSecondario == 'log') {
			
			///////////////////// RISPOSTA ALLE AZIONI MULTIPLE
			// se e' settato oggetto oggettiSelezionati, vengo da un form e cambio l'azione
			switch ($azioneSecondaria) {

				////////////////// CANCELLA //////////
				case "cancella" :
				
					// verifico se prendere i dati dal post o dal get
					$cancello = isset($_POST['id_cancello_tabella']) ? $_POST['id_cancello_tabella'] : 0;
					
					if ($cancello) {
				
						$codiceErrore = '';
						$numCancellate = $logAzioni->cancellaLog('id', $cancello);
						if ($numCancellate) {
							// OPERAZIONE ANDATA A BUON FINE
							$operazione = true;
							$operazioneTesto = "Hai cancellato con successo ".$numCancellate." azioni di log.";
							
						} else {
							// ERRORI NELL'OPERAZIONE
							$operazione = false;
							$operazioneTesto = "Problemi in eliminazione log. Riprovare in seguito.";
							$codiceErrore = '#00 - Generico';
						}

					} 
					
				break;
			}

			// TABELLA DI VISUALIZZAZIONE E GESTIONE
			include ('./app/admin_template/log/tab_start_ajax.tmp');			
			
			include ('./app/admin_template/log/tab_end.tmp');

		} else {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per visualizzare i log.');
		}
	break;

}
?>
