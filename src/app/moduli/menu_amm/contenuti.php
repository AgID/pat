<?php

switch ($menuSecondario) {

	//////////////////CONTENUTI NORMALI///////

	case "normali" :
	
		/*
		echo "Post: <pre>";
		print_r($_POST);
		echo "</pre>";
		*/
		
		////////////////// CONTENUTO EDITABILE
		
		// creo array delle sezioni da escludere in base al tipo di ente
		$sezioniEsclusione = explode(',',$tipoEnte['sezioni_esclusione']);
		
		if (isset($_POST['contenutoSezione'.$id]) and $azione == 'edita') {
		
			// ho inviato richiesta di editing di un modello (solo contenuto html)
			$modelloCaricato = datoModelloTrasp($idEnteAdmin,$id);	
			
			$contenuto = $_POST['contenutoSezione'.$id];
			$contenuto = addslashes(correttoreHtml($contenuto));
			$_POST['html_generico'] = $contenuto;
			if (!$modelloCaricato) {
			
				// il paragrafo non esiste, effettuo un update
				if (aggiungiModelloTrasp($idEnteAdmin,$id,$_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del contenuto (nuovo modello) in ".addslashes(nomeSezDaId($id))." effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del contenuto (nuovo modello) in ".addslashes(nomeSezDaId($id)).". Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}		
			} else {
			
				// il paragarfo esiste, devo solo effettuare un update
				if (modificaDatoModelloTrasp($idEnteAdmin,$modelloCaricato['id'],$id,$contenuto)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del contenuto in ".addslashes(nomeSezDaId($id))." effettuata con successo.";
					
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del contenuto in ".addslashes(nomeSezDaId($id)).". Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
			}
		}
		
		if (isset($_POST['contenutoSezione'.$id]) and $azione == 'editaPagina') {
		
			// ho inviato richiesta di editing di un modello (solo contenuto html)
			$modelloCaricato = datoModelloTraspById($idEnteAdmin,$id);
			
			$contenuto = $_POST['contenutoSezione'.$id];
			$contenuto = addslashes(correttoreHtml($contenuto));
			$_POST['html_generico'] = $contenuto;
			
			if (!$modelloCaricato) {
					
				// il paragrafo non esiste, effettuo un update
				if (aggiungiModelloTrasp($idEnteAdmin,$modelloCaricato['id_sezione_etrasp'],$_POST)) {
					// OPERAZIONE ANDATA A BUON FINE
					$operazione = true;
					$operazioneTesto = "Modifica del contenuto (nuovo modello) in ".addslashes(nomeSezDaId($modelloCaricato['id_sezione_etrasp']))." effettuata con successo.";
						
				} else {
					// ERRORI NELL'OPERAZIONE
					$operazione = false;
					$operazioneTesto = "Problemi in modifica del contenuto (nuovo modello) in ".addslashes(nomeSezDaId($modelloCaricato['id_sezione_etrasp'])).". Riprovare in seguito.";
					$codiceErrore = '#00 - Generico';
				}
			} else {
					
				// il paragarfo esiste, devo solo effettuare un update
				if (modificaDatoModelloTrasp2($idEnteAdmin,$modelloCaricato['id'],$modelloCaricato['id_sezione_etrasp'],$_POST)) {
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
			$modelloCaricato = datoModelloTrasp($idEnteAdmin,$id);	
			
			$contenuto = '';
			
			// il paragarfo esiste, devo solo effettuare un update
			if (modificaDatoModelloTrasp($idEnteAdmin,$modelloCaricato['id'],$id,$contenuto)) {
				// OPERAZIONE ANDATA A BUON FINE
				$operazione = true;
				$operazioneTesto = "Eliminazione del contenuto in ".addslashes(nomeSezDaId($id))." effettuata con successo.";
				
			} else {
				// ERRORI NELL'OPERAZIONE
				$operazione = false;
				$operazioneTesto = "Problemi in eliminazione del contenuto in ".addslashes(nomeSezDaId($id)).". Riprovare in seguito.";
				$codiceErrore = '#00 - Generico';
			}

		}
		
		if (isset($_POST['id_cancello_contenuto']) and $azione == 'cancellacontPagina') {
		
			// ho inviato richiesta di editing di un modello (solo contenuto html)
			$modelloCaricato = datoModelloTraspById($idEnteAdmin,$id);
			
			$_POST['html_generico'] = '';
				
			// il paragarfo esiste, devo solo effettuare un update
			if (modificaDatoModelloTrasp2($idEnteAdmin,$modelloCaricato['id'],$modelloCaricato['id_sezione_etrasp'],$_POST)) {
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
		
		////////////////// RICHIAMI
		if (isset($_GET['nome']) AND count($_POST) AND $azione == 'editaobj') {

			// ho inviato richiesta di editing di un modello (un richiamo oggetto)
			$modelloCaricato = datoModelloTrasp($idEnteAdmin,$id);	
			
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
				if (aggiungiModelloTrasp($idEnteAdmin,$id,$_POST)) {
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
				if (modificaDatoModelloTrasp($idEnteAdmin,$modelloCaricato['id'],$id,$valori,$campi)) {
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
		
		if (isset($_GET['nome']) AND count($_POST) AND $azione == 'editaobjPagina') {
		
			// ho inviato richiesta di editing di un modello (un richiamo oggetto)
			$modelloCaricato = datoModelloTraspById($idEnteAdmin,$id);
				
			//echo "Ciclo di editing richiami oggetto per sezione: ".$id
		
			$campi = array();
			$valori = array();
				
			// titolo
			$campi[] = $_GET['nome']."_tit";
			$valori[] = addslashes(htmlentities($_POST[$modelloCaricato['id']."_".$_GET['nome']."_tit"]));
			$_POST[$_GET['nome']."_tit"] = addslashes(htmlentities($_POST[$modelloCaricato['id']."_".$_GET['nome']."_tit"]));
		
			// richiamo selezione
			$campi[] = $_GET['nome'];
			$valori[] = implode(',',$_POST[$modelloCaricato['id']."_".$_GET['nome']]);
			$_POST[$_GET['nome']] = implode(',',$_POST[$modelloCaricato['id']."_".$_GET['nome']]);
		
			// opzioni
			$campi[] = $_GET['nome']."_opz";
			$valori[] = $_POST[$modelloCaricato['id']."_".$_GET['nome']."_opz"];
			$_POST[$_GET['nome']."_opz"] = $_POST[$modelloCaricato['id']."_".$_GET['nome']."_opz"];
			
			//	TODO
			/*
			if (!$modelloCaricato) {
				// il paragrafo non esiste, effettuo un update
				if (aggiungiModelloTrasp($idEnteAdmin,$id,$_POST)) {
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
				if (modificaDatoModelloTrasp($idEnteAdmin,$modelloCaricato['id'],$id,$valori,$campi)) {
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
			*/
		}
		
	
		include ('./app/admin_template/contenuti/contenuti.tmp');
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
