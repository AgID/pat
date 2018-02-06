<?php

$idOggetto = 46;

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

	//////////////////LISTA ENTI INSTALLATI///////

	case "lista" :
		// controllo permessi
		if (!$aclTrasparenza[$menuSecondario]['lettura']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei workflow.');
		} else {
		
			///////////////////// RISPOSTA ALLE AZIONI FORM //////////////////////////////
			$codiceErrore = '';
			
			// aggiunta oggetto
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'aggiungi') {
			
				$operazione = true;
				
				$arrayWf = array();
				$stati = array();
				$utentiIntermedi = array();
				for($i = 0; $i < count($_POST['id_wf']); $i++) {
					$idStatoWf = $_POST['id_wf'][$i];
					$arrayWf[] = array(
						'id' => $idStatoWf,
						'nome' => $_POST['nome_wf'][$i],
						'utenti' => implode(',', $_POST['utenti_wf_'.$idStatoWf])
					);
					$stati[] = $idStatoWf;
					foreach((array)$_POST['utenti_wf_'.$idStatoWf] as $idUtenteIntermedio) {
						$utentiIntermedi[] = $idUtenteIntermedio;
					}
				}
				$_POST['composizione_workflow'] = base64_encode(serialize($arrayWf));
				$_POST['id_stati'] = trim(implode(',', $stati));
				$_POST['id_utenti_intermedi'] = trim(implode(',', $utentiIntermedi));
				
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
			
			// modifica oggetto
			if (isset ($_POST['rispostaForm']) and $azioneSecondaria == 'modifica') {
				
				$arrayWf = array();
				$stati = array();
				$utentiIntermedi = array();
				for($i = 0; $i < count($_POST['id_wf']); $i++) {
					$idStatoWf = $_POST['id_wf'][$i];
					$arrayWf[] = array(
						'id' => $idStatoWf,
						'nome' => $_POST['nome_wf'][$i],
						'utenti' => implode(',', $_POST['utenti_wf_'.$idStatoWf])
					);
					$stati[] = $idStatoWf;
					foreach((array)$_POST['utenti_wf_'.$idStatoWf] as $idUtenteIntermedio) {
						$utentiIntermedi[] = $idUtenteIntermedio;
					}
				}
				$_POST['composizione_workflow'] = base64_encode(serialize($arrayWf));
				$_POST['id_stati'] = trim(implode(',', $stati));
				$_POST['id_utenti_intermedi'] = trim(implode(',', $utentiIntermedi));
				
				// carico ente in modifica
				$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
				$idCategoria = $istanzaOggetto['id_sezione'];
				
				$operazione = true;
				
				// tipo di salvataggio dei campi allegato, per il momento forzo
				$salvaDef = 1;
				
				// proseguo con la registrazione utente
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
			
			///////////////////// RISPOSTA ALLE AZIONI MULTIPLE
			// se e' settato oggetto oggettiSelezionati, vengo da un form e cambio l'azione
			switch ($azioneSecondaria) {

				////////////////// CANCELLA //////////
				case "cancella" :

					// verifico se prendere i dati dal post o dal get
					$cancello = isset($_POST['id_cancello_tabella']) ? $_POST['id_cancello_tabella'] : 0;
					
					$oggOgg->eliminaWorkflow($cancello);
					
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
			
			include ('./pat/admin_template/oggetti/info_tab.tmp');
			
			include ('./pat/admin_template/oggetti/tab_start_ajax.tmp');
			
			include ('./pat/admin_template/oggetti/tab_end.tmp');
		}
	break;

	//////////////////AGGIUNGI///////

	case "aggiungi" :

		// controllo permessi
		if (!$aclTrasparenza[$menuSecondario]['creazione']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per la gestione dei workflow.');
		} else {
			$id = 0;
			// inizializzo le variabili aggiornamento campi form come nulle
			$istanzaOggetto = array ();
			// qui includo la pagina con il form
			include ('./pat/admin_template/oggetti/form/'.$menuSecondario.'.tmp');
		}
	break;

	//////////////////MODIFICA///////

	case "modifica" :

		if ($datiUser['permessi'] != 10 AND !$aclTrasparenza['admin']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari accedere alla gestione dei workflow.');
		} else {
			// carico istanza da modificare
			//$istanzaOggetto = $wf->caricaModulo($id);
			$istanzaOggetto = $oggOgg->caricaOggetto($idIstanza);
			
			// ulteriore controllo permesssi
			if (($aclTrasparenza[$menuSecondario]['modifica'] OR $istanzaOggetto['id_ente'] == $datiUser['id_ente_admin']) AND ($id != 0 OR $datiUser['permessi']==10)) {

				// qui includo la pagina con il form
				include ('./pat/admin_template/oggetti/form/'.$menuSecondario.'.tmp');

			} else {
				motoreLog('permessonegato', 'Non hai i permessi necessari per modificare questo modulo personalizzato.', FALSE);
			}
		}
	break;
}
?>