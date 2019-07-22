<?
switch ($azione) {

	//////////////////LISTA RECORD DI OGGETTO///////

	case "lista" :
	
		if (!$aclTrasparenza['ealbo_import']) {
			motoreLogTrasp('permessonegato', 'Non hai i permessi necessari per visualizzare questo archivio.');
		} else {
		
			///////////////////// RISPOSTA ALLE AZIONI MULTIPLE
			// se e' settato oggetto oggettiSelezionati, vengo da un form e cambio l'azione
			switch ($azioneSecondaria) {
				////////////////// IMPORTA //////////
				case "importAtti" :

					// verifico se prendere i dati dal post o dal get
					$importaAtti = isset($_POST['id_import_atti']) ? $_POST['id_import_atti'] : 0;
					$importaIn = isset($_POST['id_import_destinazione']) ? $_POST['id_import_destinazione'] : 0;
					
					if ($importaAtti and $importaIn) {
						require_once('classi/admin_oggetti.php');
						
						$idAtti = explode(',', $importaAtti);
						$idOggetti = explode(',', $importaIn);
						$_POST['stato_pubblicazione'] = '40';
						
						$operazione = false;
						$operazioneTesto = '';
						$testoImport = '';
						$numImport = 0;
						$testoSaltati = '';
						$testoErrori = '';
						foreach((array)$idOggetti as $idOggetto) {
							$oggOgg = new oggettiAdmin($idOggetto);
							
							if(file_exists('app/moduli/menu_amm/operazioni/oggetti/'.$oggettiTrasparenza[$idOggetto]['menuSec'].'.php')) {
								$opOgg = $oggettiTrasparenza[$idOggetto]['menuSec'];
							} else {
								$opOgg = 'OperazioneDefault';
							}
							require_once('app/moduli/menu_amm/operazioni/oggetti/'.$opOgg.'.php');
							$opOgg = new $opOgg();
							
							foreach((array)$idAtti as $idAtto) {
								$istanzaOggetto = array ();
								$atto = caricaDocumentoEAlbo('atti', $idAtto);
								$attoImportato = prendiOggettoImportato($idAtto, $idOggetto, $enteAdmin);
								if(count($attoImportato) > 0) {
									$operazione = true;
									$testoSaltati .= "<div>L\'atto <strong>".$atto['oggetto'].
											"</strong> è già presente in <strong>".$oggettiTrasparenza[$idOggetto]['nomeMenu'].
											"</strong>.</div>";
								} else {
									$mapping = $mappingCampiAlbo[$idOggetto];
									foreach((array)$mapping as $campoTrasparenza => $campoAlbo) {
										$istanzaOggetto[$campoTrasparenza] = $atto[$campoAlbo];
									}
									if(file_exists('app/ealbo/mapping/mapping_live_'.$enteAdmin['id'].'.php')) {
										//sovrascrivere qui dentro, in base alle esigenze, $mappingCampiAlbo
										include('app/ealbo/mapping/mapping_live_'.$enteAdmin['id'].'.php');
									}
									$_POST['id_atto_albo'] = $idAtto;
									foreach((array)$_POST as $k => $v) {
										$istanzaOggetto[$k] = $v;
									}
									
									$opOgg->preInsert();
									if ($oggOgg->aggiungiOggetto(0, $istanzaOggetto)) {
										// OPERAZIONE ANDATA A BUON FINE
										$operazione = true;
										$testoImport .= "<div>Aggiunta atto <strong>".$atto['oggetto'].
												"</strong> in <strong>".$oggettiTrasparenza[$idOggetto]['nomeMenu'].
												"</strong> effettuata con successo.</div>";
										$numImport++;
										$opOgg->postInsert();
									} else {
										// ERRORI NELL'OPERAZIONE
										$testoErrori = "Problemi in aggiunta. Riprovare in seguito.";
										$codiceErrore = '#00 - Generico';
									}
								}
							}
						}
						if($testoImport != '') {
							$operazioneTesto .= '<div><strong>'.$numImport.' ELEMENTI IMPORTATI</strong></div>';
						}
						if($testoSaltati != '') {
							$operazioneTesto .= '<div><strong>ELEMENTI SALTATI</strong></div>'.$testoSaltati;
						}
						if($testoErrori != '') {
							$operazioneTesto .= '<div><strong>ERRORI</strong></div>'.$testoErrori;
						}
					}

				break;
				////////////////// CANCELLA //////////
				case "cancella" :

					$idOggetto = isset($_POST['id_oggetto_cancello']) ? $_POST['id_oggetto_cancello'] : 0;
					$idDocumento = isset($_POST['id_doc_cancello']) ? $_POST['id_doc_cancello'] : 0;
					
					if ($idOggetto and $idDocumento) {
						$_POST['id_cancello_tabella'] = $idDocumento;
						require_once('classi/admin_oggetti.php');
						$oggOgg = new oggettiAdmin($idOggetto);
							
						if(file_exists('app/moduli/menu_amm/operazioni/oggetti/'.$oggettiTrasparenza[$idOggetto]['menuSec'].'.php')) {
							$opOgg = $oggettiTrasparenza[$idOggetto]['menuSec'];
						} else {
							$opOgg = 'OperazioneDefault';
						}
						require_once('app/moduli/menu_amm/operazioni/oggetti/'.$opOgg.'.php');
						$opOgg = new $opOgg();
						
						
						$ida = mostraDatoOggetto($idDocumento, $idOggetto, '__id_allegato_istanza');
					
						$opOgg->preDelete();
						$codiceErrore = '';
						$numCancellate = $oggOgg->cancellaOggetti($idDocumento);
						
						if($ida != '') {
							$oggAllegati = new oggettiAdmin(57);
							
							$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetto_allegati WHERE 1=1 AND __id_allegato_istanza = '".$ida."'";
							if (!($result = $database->connessioneConReturn($sql))) {
								die("Database non installato o non disponibile: errore critico: ".$sql);
							}
							$result = $database->sqlArrayAss($result);
							foreach((array)$result as $a) {
								$oggAllegati->cancellaOggetti($a['id']);
							}
						}
						
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
				case "escludiAtti":
					$escludiAtti = isset($_POST['id_escludi_atti']) ? $_POST['id_escludi_atti'] : 0;
					
					if($escludiAtti) {
						$idAtti = explode(',', $escludiAtti);
						
						$operazione = false;
						$operazioneTesto = '';
						$testoImport = '';
						$numImport = 0;
						$testoSaltati = '';
						$testoErrori = '';
						
						foreach((array)$idAtti as $idAtto) {
							$istanzaOggetto = array ();
							$atto = caricaDocumentoEAlbo('atti', $idAtto);
							if($atto['eTrasparenza'] == 1) {
								$operazione = true;
								$testoSaltati .= "<div>L\'atto <strong>".$atto['oggetto'].
										"</strong> è stato importato e non è possibile escluderlo dalla lista.</div>";
							} else {
								if(escludiAttoEAlbo($atto)) {
									// OPERAZIONE ANDATA A BUON FINE
									$operazione = true;
									$testoImport .= "<div>Esclusione atto <strong>".$atto['oggetto'].
											"</strong> effettuata con successo.</div>";
									$numImport++;
								} else {
									// ERRORI NELL'OPERAZIONE
									$testoErrori = "Problemi in esclusione atto. Riprovare in seguito.";
									$codiceErrore = '#00 - Generico';
								}
							}
						}
						
						if($testoImport != '') {
							$operazioneTesto .= '<div><strong>'.$numImport.' ELEMENTI ESCLUSI</strong></div>';
						}
						if($testoSaltati != '') {
							$operazioneTesto .= '<div><strong>ELEMENTI SALTATI</strong></div>'.$testoSaltati;
						}
						if($testoErrori != '') {
							$operazioneTesto .= '<div><strong>ERRORI</strong></div>'.$testoErrori;
						}
					}
				break;
				case "includiAtti":
					$includiAtti = isset($_POST['id_includi_atti']) ? $_POST['id_includi_atti'] : 0;
					
					if($includiAtti) {
						$idAtti = explode(',', $includiAtti);
						
						$operazione = false;
						$operazioneTesto = '';
						$testoImport = '';
						$numImport = 0;
						$testoSaltati = '';
						$testoErrori = '';
						
						foreach((array)$idAtti as $idAtto) {
							$istanzaOggetto = array ();
							$atto = caricaDocumentoEAlbo('atti', $idAtto);
							if($atto['eTrasparenza'] == '-1') {
								if(includiAttoEAlbo($atto)) {
									// OPERAZIONE ANDATA A BUON FINE
									$operazione = true;
									$testoImport .= "<div>Inclusione atto <strong>".$atto['oggetto'].
											"</strong> effettuata con successo.</div>";
									$numImport++;
								} else {
									// ERRORI NELL'OPERAZIONE
									$testoErrori = "Problemi in inclusione atto. Riprovare in seguito.";
									$codiceErrore = '#00 - Generico';
								}
							} else {
								$operazione = true;
								$testoSaltati .= "<div>L\'atto <strong>".$atto['oggetto'].
										"</strong> non è un atto escluso dalla lista.</div>";
							}
						}
						
						if($testoImport != '') {
							$operazioneTesto .= '<div><strong>'.$numImport.' ELEMENTI INCLUSI</strong></div>';
						}
						if($testoSaltati != '') {
							$operazioneTesto .= '<div><strong>ELEMENTI SALTATI</strong></div>'.$testoSaltati;
						}
						if($testoErrori != '') {
							$operazioneTesto .= '<div><strong>ERRORI</strong></div>'.$testoErrori;
						}
					}
				break;
			}
			
			$campiVisualizzati = array();
			for ($i=0;$i<count($campiEalbo);$i++) {
				if($campiEalbo[$i]['visualizzaTabella']) {
					$campiVisualizzati[$i]['campo'] = $campiEalbo[$i]['campo']; 
					$campiVisualizzati[$i]['etichetta'] = $campiEalbo[$i]['etichetta']; 
					$campiVisualizzati[$i]['proprieta'] = $campiEalbo[$i]['proprieta']; 	
					$campiVisualizzati[$i]['tipo'] = $campiEalbo[$i]['tipocampo'];
					$campiVisualizzati[$i]['etichette'] = $campiEalbo[$i]['proprieta'];
					$campiVisualizzati[$i]['valore'] = $campiEalbo[$i]['valorecampo'];
				}
			}
			
			include ('./app/admin_template/ealbo/tab_start_ajax.tmp');
			
			include ('./app/admin_template/ealbo/tab_end.tmp');
			
		}
	break;
}
?>