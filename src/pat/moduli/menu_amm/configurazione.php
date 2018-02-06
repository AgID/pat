<?php

// importo la classe di amministrazione contenuto
require ('./pat/classi/admin_enti.php');
$entiClasse = new enti();

switch ($menuSecondario) {

	//////////////////WIZARD///////

	case "wizard" :
		include ('./pat/admin_template/configurazione/wizard.tmp');
	break;
	//////////////////AVANZATA///////

	case "avanzata" :
	
		// controllo permessi
		if (!$aclTrasparenza['admin']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione della configurazione ente.');
		} else {
	
		$id = $datiUser['id_ente_admin'];
		
		// carico ente in modifica
		$istanzaOggetto = $entiClasse->caricaEnte($id);
		
		// modifica ente
		if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {
			
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
			
			//pulisco il l'eventuale codice Google analitycs
			/*if($_POST['google_analitycs'] != ''){
				$_POST['google_analitycs'] = correttoreHtml($_POST['google_analitycs']);
			}*/
		
			// proseguo con la registrazione utente
			if ($operazione) {
				if ($entiClasse->modificaEnteParziale($id, $_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica configurazione effettuata con successo.";
					// RIcarico ente in modifica
					$istanzaOggetto = $entiClasse->caricaEnte($id);					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica configurazione. Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
			}
		}
		
		include ('./pat/admin_template/enti/form.tmp');
		
		} // fine verifica permessi
		
	break;
}
?>
