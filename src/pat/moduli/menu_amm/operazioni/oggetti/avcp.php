<?php
include_once 'pat/moduli/menu_amm/operazioni/oggetti/OperazioneDefault.php';
include_once('pat/funzioniAVCP.php');
class avcp extends OperazioneDefault {
	
	public function __construct() { }
	
	public function postInsert($arrayParametri = array()) {
		global $enteAdmin, $datiUser, $configurazione, $database, $dati_db;
		
		if(!file_exists('avcp/'.$datiUser['id_ente_admin'])) {
			mkdir('avcp/'.$datiUser['id_ente_admin']);
		}
		ob_start();
		if($enteAdmin['id'] == 1 or $enteAdmin['id'] == 15 or $enteAdmin['id'] == 33) {
			$xml = $this->creaXml_2($arrayParametri);
		} else {
			//$xml = $this->creaXml($arrayParametri);
			$xml = $this->creaXml_2($arrayParametri);
		}
		ob_end_flush();
		if($xml) {
			file_put_contents('avcp/'.$datiUser['id_ente_admin'].'/'.$_POST['anno'].'.xml', $xml);
		}
		
	}
	
	public function convertMemory($size) {
	    $unit=array('b','kb','mb','gb','tb','pb');
	    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	public function postUpdate($arrayParametri = array()) {
		global $enteAdmin, $datiUser, $configurazione, $database, $dati_db;
		
		if(!file_exists('avcp/'.$datiUser['id_ente_admin'])) {
			mkdir('avcp/'.$datiUser['id_ente_admin']);
		} else {
			//se esiste il file precedente lo cancello e lo ricreo
			if(file_exists('avcp/'.$datiUser['id_ente_admin'].'/'.$_POST['anno'].'.xml')) {
				@unlink('avcp/'.$datiUser['id_ente_admin'].'/'.$_POST['anno'].'.xml');
			}
			//se esiste il file dell'anno precedente lo elimino se � diverso dall'anno attuale
			if($_POST['anno_precedente'] != $_POST['anno'] and file_exists('avcp/'.$datiUser['id_ente_admin'].'/'.$_POST['anno_precedente'].'.xml')) {
				@unlink('avcp/'.$datiUser['id_ente_admin'].'/'.$_POST['anno_precedente'].'.xml');
			}
		}
		ob_start();
		if($enteAdmin['id'] == 1 or $enteAdmin['id'] == 15 or $enteAdmin['id'] == 33) {
			$xml = $this->creaXml_2($arrayParametri);
		} else {
			//$xml = $this->creaXml($arrayParametri);
			$xml = $this->creaXml_2($arrayParametri);
		}
		ob_end_flush();
		if($xml) {
			file_put_contents('avcp/'.$datiUser['id_ente_admin'].'/'.$_POST['anno'].'.xml', $xml);
		}
		
	}
	
	public function preDelete($arrayParametri = array()) {
		global $enteAdmin, $datiUser, $configurazione, $database, $dati_db;
		
		$cancello = isset($_POST['id_cancello_tabella']) ? $_POST['id_cancello_tabella'] : 0;
		$arrayOggetti = explode(",", $cancello);
		$numeroOggetti = count($arrayOggetti)-1;
		$condizione = 'id='.$arrayOggetti[0];
		for ($i=1;$i<$numeroOggetti+1;$i++) {
			if ($arrayOggetti[$i] != '') {
				$condizione .= ' or id='.$arrayOggetti[$i];
			}
		}
		
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_url_avcp WHERE ".$condizione;
		if($result = $database->connessioneConReturn($sql)) {
			$this->arrayInterno['fileToDelete'] = $database->sqlArrayAss($result);
		}
		
	}
	
	public function postDelete($arrayParametri = array()) {
		global $datiUser;
		
		foreach((array)$this->arrayInterno['fileToDelete'] as $f) {
			if(file_exists('avcp/'.$datiUser['id_ente_admin'].'/'.$f['anno'].'.xml')) {
				@unlink('avcp/'.$datiUser['id_ente_admin'].'/'.$f['anno'].'.xml');
			}
		}
	}


	private function creaXML($arrayParametri = array()) {
		global $enteAdmin, $datiUser, $configurazione, $database, $dati_db, $oggOgg;
		
		$annoRiferimento = $_POST['anno'];
		$entePubblicatore = $enteAdmin['nome_completo_ente'];
		if($_GET['id']) {
			$dataPubblicazioneDataset = date('Y-m-d', mostraDatoOggetto($_GET['id'], $oggOgg->idOggetto, 'data_creazione'));
			
		} else {
			$dataPubblicazioneDataset = date('Y-m-d');
		}
		$dataUltimoAggiornamentoDataset = date('Y-m-d');
		
		//configurazione metadata
		$titolo = "Pubblicazione legge 190/2012";
		$abstract = "Pubblicazione legge 190/2012 anno di riferimento ".$annoRiferimento;
		$urlFile = "http://".$enteAdmin['nome_breve_ente'].".nomedominio/avcp/".$datiUser['id_ente_admin']."/".$annoRiferimento.".xml";
		$licenza = "IODL";
		
		if($annoRiferimento == 2013) {
			$inizio = mktime(0,0,0,12,1,$annoRiferimento-1);
		} else {
			$inizio = mktime(0,0,0,1,1,$annoRiferimento);
		}
		$fine = mktime(23,59,59,12,31,$annoRiferimento);
		$condAnno = " AND data_attivazione >= ".$inizio." AND data_attivazione <= ".$fine." ";
		$condTipologia = " AND (tipologia = 'bandi ed inviti' OR tipologia = 'esiti' OR tipologia = 'delibere e determine a contrarre' OR tipologia = 'affidamenti') ";
		
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente = ".$datiUser['id_ente_admin']." ".$condTipologia." ".$condAnno." ORDER BY data_attivazione";
		if($result = $database->connessioneConReturn($sql)) {
			$result = $database->sqlArrayAss($result);
		}
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = 'UTF-8';
		
		//root
		$root = $xmlDoc->appendChild($xmlDoc->createElement("legge190:pubblicazione"));
		$root->appendChild($xmlDoc->createAttribute("xsi:schemaLocation"))->appendChild($xmlDoc->createTextNode("legge190_1_0 datasetAppaltiL190.xsd"));
		$root->appendChild($xmlDoc->createAttribute("xmlns:xsi"))->appendChild($xmlDoc->createTextNode("http://www.w3.org/2001/XMLSchema-instance"));
		$root->appendChild($xmlDoc->createAttribute("xmlns:legge190"))->appendChild($xmlDoc->createTextNode("legge190_1_0"));
		
		//metadata
		$meta = $root->appendChild($xmlDoc->createElement("metadata"));
		$meta->appendChild($xmlDoc->createElement("titolo"))->appendChild($xmlDoc->createTextNode(utf8_encode($titolo)));
		$meta->appendChild($xmlDoc->createElement("abstract"))->appendChild($xmlDoc->createTextNode(utf8_encode($abstract)));
		$meta->appendChild($xmlDoc->createElement("dataPubbicazioneDataset"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataPubblicazioneDataset)));
		$meta->appendChild($xmlDoc->createElement("entePubblicatore"))->appendChild($xmlDoc->createTextNode(utf8_encode($entePubblicatore)));
		$meta->appendChild($xmlDoc->createElement("dataUltimoAggiornamentoDataset"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataUltimoAggiornamentoDataset)));
		$meta->appendChild($xmlDoc->createElement("annoRiferimento"))->appendChild($xmlDoc->createTextNode(utf8_encode($annoRiferimento)));
		$meta->appendChild($xmlDoc->createElement("urlFile"))->appendChild($xmlDoc->createTextNode(utf8_encode($urlFile)));
		$meta->appendChild($xmlDoc->createElement("licenza"))->appendChild($xmlDoc->createTextNode(utf8_encode($licenza)));
		
		//data
		$data = $root->appendChild($xmlDoc->createElement("data"));
		
		foreach((array)$result as $r) {
			
			$lotto = $data->appendChild($xmlDoc->createElement("lotto"));
			//$cig = utf8_encode(substr($r['cig'], 0, 10));
			$cig = utf8_encode(trim($r['cig']));
			if($cig == '') {
				$cig = '0000000000';
			}
			$lotto->appendChild($xmlDoc->createElement("cig"))->appendChild($xmlDoc->createTextNode($cig));
			
			$strutturaProponente = $lotto->appendChild($xmlDoc->createElement("strutturaProponente"));
			$codiceFiscaleProp = trim($r['dati_aggiudicatrice']);
			if(!validaCfPi($codiceFiscaleProp)) {
				//se non � corretto non lo esporto
				$codiceFiscaleProp = '';
			}
			$strutturaProponente->appendChild($xmlDoc->createElement("codiceFiscaleProp"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($codiceFiscaleProp))));
			$strutturaProponente->appendChild($xmlDoc->createElement("denominazione"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim(substr(html_entity_decode($r['denominazione_aggiudicatrice']), 0, 250)))));
			
			$lotto->appendChild($xmlDoc->createElement("oggetto"))->appendChild($xmlDoc->createTextNode(utf8_encode(substr(html_entity_decode(trim($r['oggetto'])), 0, 250))));
			
			$sceltaContraente = trim($r['scelta_contraente']);
			if(!validaSceltaContraente($sceltaContraente)) {
				//se non � corretta non la esporto
				$sceltaContraente = '';
			} else {
				//nella scelta 14 c'� una virgola che nel DB non viene salvata, altrimenti il sistema non funziona (colpa della select con valori)
				if($sceltaContraente == '14-PROCEDURA SELETTIVA EX ART 238 C.7 D.LGS. 163/2006') {
					$sceltaContraente = '14-PROCEDURA SELETTIVA EX ART 238 C.7, D.LGS. 163/2006';
				}
			}
			$lotto->appendChild($xmlDoc->createElement("sceltaContraente"))->appendChild($xmlDoc->createTextNode(utf8_encode($sceltaContraente)));
			
			$partecipanti = $lotto->appendChild($xmlDoc->createElement("partecipanti"));
			
			//verificare se ci sono pi� partecipanti
			$idPartecipanti = explode(',', $r['elenco_partecipanti']);
			$condPartecipanti = array();
			foreach((array)$idPartecipanti as $idp) {
				if($idp > 0) {
					$condPartecipanti[] = " id = ".$idp." ";
				}
			}
			$dataScript = mktime(0,0,0,date("m"),date("d"),date("Y"));
			if($r['tipologia'] == 'bandi ed inviti' and $r['data_scadenza'] >= $dataScript) {
				//il bando non � ancora scaduto quindi escludo tutti i partecipanti
				$condPartecipanti = array();
			}
			
			if(count($condPartecipanti)) {
				$condPartecipanti = " AND (".implode(' OR ', $condPartecipanti).") ";
				$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$datiUser['id_ente_admin'].$condPartecipanti;
				if($par = $database->connessioneConReturn($sql)) {
					$par = $database->sqlArrayAss($par);
					if(count($par > 0)) {
						$partecipantiSingoli = array();
						$partecipantiRaggruppamenti = array();
						foreach((array)$par as $p) {
							//if($p['tipologia'] == 'raggruppamento') {
							if(trim($p['mandante']) != '' or trim($p['mandataria']) != '' or trim($p['associata']) != '' or trim($p['capogruppo']) != '' or trim($p['consorziata']) != '') {
								$partecipantiRaggruppamenti[] = $p;
							} else {
								$partecipantiSingoli[] = $p;
							}
							
						}
						foreach((array)$partecipantiRaggruppamenti as $p) {
							//raggruppamento
							$raggruppamento = $partecipanti->appendChild($xmlDoc->createElement("raggruppamento"));
							//mandante
							$idParRagg = explode(',', trim($p['mandante']));
							foreach((array)$idParRagg as $idPar) {
								if($idPar > 0) {
									$pRagg = mostraDatoOggetto($idPar, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($pRagg) {
										if(!validaCfPi($pRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$pRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('01-MANDANTE')));
								}
							}
							//mandataria
							$idParRagg = explode(',', trim($p['mandataria']));
							foreach((array)$idParRagg as $idPar) {
								if($idPar > 0) {
									$pRagg = mostraDatoOggetto($idPar, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($pRagg['codice_fiscale'] != '') {
										if(!validaCfPi($pRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$pRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('02-MANDATARIA')));
								}
							}
							//associata
							$idParRagg = explode(',', trim($p['associata']));
							foreach((array)$idParRagg as $idPar) {
								if($idPar > 0) {
									$pRagg = mostraDatoOggetto($idPar, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($pRagg['codice_fiscale'] != '') {
										if(!validaCfPi($pRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$pRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($p['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('03-ASSOCIATA')));
								}
							}
							//capogruppo
							$idParRagg = explode(',', trim($p['capogruppo']));
							foreach((array)$idParRagg as $idPar) {
								if($idPar > 0) {
									$pRagg = mostraDatoOggetto($idPar, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($pRagg['codice_fiscale'] != '') {
										if(!validaCfPi($pRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$pRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($p['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('04-CAPOGRUPPO')));
								}
							}
							//consorziata
							$idParRagg = explode(',', trim($p['consorziata']));
							foreach((array)$idParRagg as $idPar) {
								if($idPar > 0) {
									$pRagg = mostraDatoOggetto($idPar, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($pRagg['codice_fiscale'] != '') {
										if(!validaCfPi($p['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$pRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('05-CONSORZIATA')));
								}
							}
						}
						foreach((array)$partecipantiSingoli as $p) {
							//singolo
							$partecipante = $partecipanti->appendChild($xmlDoc->createElement("partecipante"));
							$inclusoCF = false;
							if($p['codice_fiscale'] != '') {
								if(!validaCfPi($p['codice_fiscale'])) {
									//se non � corretto non lo esporto
									$p['codice_fiscale'] = '';
								} else {
									$partecipante->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($p['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($p['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
								$partecipante->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($p['fiscale_estero']))));
							}
							$partecipante->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($p['nominativo'])))));
						}
					}
				}
			}
			
			$aggiudicatari = $lotto->appendChild($xmlDoc->createElement("aggiudicatari"));
			
			//verificare se ci sono pi� aggiudicatari
			$idAggiudicatari = explode(',', $r['elenco_aggiudicatari']);
			$condAggiudicatari = array();
			foreach((array)$idAggiudicatari as $ida) {
				if($ida > 0) {
					$condAggiudicatari[] = " id = ".$ida." ";
				}
			}
			if($r['tipologia'] == 'bandi ed inviti' and $r['data_scadenza'] >= $dataScript) {
				//il bando non � ancora scaduto quindi escludo tutti i partecipanti
				$condAggiudicatari = array();
			}
			
			if(count($condAggiudicatari)) {
				$condAggiudicatari = " AND (".implode(' OR ', $condAggiudicatari).") ";
				$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$datiUser['id_ente_admin'].$condAggiudicatari;
				if($agg = $database->connessioneConReturn($sql)) {
					$agg = $database->sqlArrayAss($agg);
					if(count($agg > 0)) {
						$aggiudicatariSingoli = array();
						$aggiudicatariRaggruppamenti = array();
						foreach((array)$agg as $a) {
							//if($a['tipologia'] == 'raggruppamento') {
							if(trim($a['mandante']) != '' or trim($a['mandataria']) != '' or trim($a['associata']) != '' or trim($a['capogruppo']) != '' or trim($a['consorziata']) != '') {
								$aggiudicatariRaggruppamenti[] = $a;
							} else {
								$aggiudicatariSingoli[] = $a;
							}
							
						}
						foreach((array)$aggiudicatariRaggruppamenti as $a) {
							//raggruppamento
							$raggruppamento = $aggiudicatari->appendChild($xmlDoc->createElement("aggiudicatarioRaggruppamento"));
							//mandante
							$idAggRagg = explode(',', trim($a['mandante']));
							foreach((array)$idAggRagg as $idAgg) {
								if($idAgg > 0) {
									$aRagg = mostraDatoOggetto($idAgg, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($aRagg['codice_fiscale'] != '') {
										if(!validaCfPi($aRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$aRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('01-MANDANTE')));
								}
							}
							//mandataria
							$idAggRagg = explode(',', trim($a['mandataria']));
							foreach((array)$idAggRagg as $idAgg) {
								if($idAgg > 0) {
									$aRagg = mostraDatoOggetto($idAgg, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($aRagg['codice_fiscale'] != '') {
										if(!validaCfPi($aRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$aRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('02-MANDATARIA')));
								}
							}
							//associata
							$idAggRagg = explode(',', trim($a['associata']));
							foreach((array)$idAggRagg as $idAgg) {
								if($idAgg > 0) {
									$aRagg = mostraDatoOggetto($idAgg, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($aRagg['codice_fiscale'] != '') {
										if(!validaCfPi($aRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$aRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('03-ASSOCIATA')));
								}
							}
							//capogruppo
							$idAggRagg = explode(',', trim($a['capogruppo']));
							foreach((array)$idAggRagg as $idAgg) {
								if($idAgg > 0) {
									$aRagg = mostraDatoOggetto($idAgg, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($aRagg['codice_fiscale'] != '') {
										if(!validaCfPi($aRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$aRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('04-CAPOGRUPPO')));
								}
							}
							//consorziata
							$idAggRagg = explode(',', trim($a['consorziata']));
							foreach((array)$idAggRagg as $idAgg) {
								if($idAgg > 0) {
									$aRagg = mostraDatoOggetto($idAgg, 41, '*');
									$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
									$inclusoCF = false;
									if($aRagg['codice_fiscale'] != '') {
										if(!validaCfPi($aRagg['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$aRagg['codice_fiscale'] = '';
										} else {
											$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
									}
									$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
									$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('05-CONSORZIATA')));
								}
							}
						}
						foreach((array)$aggiudicatariSingoli as $a) {
							//singolo
							$aggiudicatario = $aggiudicatari->appendChild($xmlDoc->createElement("aggiudicatario"));
							$inclusoCF = false;
							if($a['codice_fiscale'] != '') {
								if(!validaCfPi($a['codice_fiscale'])) {
									//se non � corretto non lo esporto
									$a['codice_fiscale'] = '';
								} else {
									$aggiudicatario->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($a['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($a['fiscale_estero'] != '' and !$inclusoCF) {
								$aggiudicatario->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($a['fiscale_estero']))));
							}
							$aggiudicatario->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($a['nominativo'])))));
						}
					}
				}
			}
			
			$importoAggiudicazione = $r['valore_importo_aggiudicazione'];
			if($importoAggiudicazione != '') {
				$importoAggiudicazione = number_format($importoAggiudicazione,2,'.','');
				if($importoAggiudicazione == '') {
					$importoAggiudicazione = 0.00;
				}
			} else {
				$importoAggiudicazione = 0.00;
			}
			$lotto->appendChild($xmlDoc->createElement("importoAggiudicazione"))->appendChild($xmlDoc->createTextNode(utf8_encode($importoAggiudicazione)));
			
			$tempiCompletamento = $lotto->appendChild($xmlDoc->createElement("tempiCompletamento"));
			
			$dataInizio = $r['data_inizio_lavori'];
			if($dataInizio > 0) {
				$dataInizio = date('Y-m-d', $dataInizio);
				$tempiCompletamento->appendChild($xmlDoc->createElement("dataInizio"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataInizio)));
			}
			
			$dataUltimazione = $r['data_lavori_fine'];
			if($dataUltimazione > 0) {
				$dataUltimazione = date('Y-m-d', $dataUltimazione);
				$tempiCompletamento->appendChild($xmlDoc->createElement("dataUltimazione"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataUltimazione)));
			}
			
			$importoSommeLiquidate = $r['importo_liquidato'];
			if($importoSommeLiquidate != '') {
				$importoSommeLiquidate = number_format($importoSommeLiquidate,2,'.','');
				if($importoSommeLiquidate == '') {
					$importoSommeLiquidate = 0.00;
				}
			} else {
				$importoSommeLiquidate = 0.00;
			}
			$lotto->appendChild($xmlDoc->createElement("importoSommeLiquidate"))->appendChild($xmlDoc->createTextNode(utf8_encode($importoSommeLiquidate)));
		
		}
		
		header("Content-Type: text/plain");
		$xmlDoc->formatOutput = true;
		return $xmlDoc->saveXML();
		
	}
	
	private function creaXML_2($arrayParametri = array()) {
		global $enteAdmin, $datiUser, $configurazione, $database, $dati_db, $oggOgg;
		
		ini_set('memory_limit','1024M');
		ini_set('max_execution_time', 120);
		
		$annoRiferimento = $_POST['anno'];
		$entePubblicatore = $enteAdmin['nome_completo_ente'];
		if($_GET['id']) {
			$dataPubblicazioneDataset = date('Y-m-d', mostraDatoOggetto($_GET['id'], $oggOgg->idOggetto, 'data_creazione'));
		} else {
			$dataPubblicazioneDataset = date('Y-m-d');
		}
		$dataUltimoAggiornamentoDataset = date('Y-m-d');
		
		//configurazione metadata
		$titolo = "Pubblicazione legge 190/2012";
		$abstract = "Pubblicazione legge 190/2012 anno di riferimento ".$annoRiferimento;
		$urlFile = "http://".$enteAdmin['nome_breve_ente'].".nomedominio/avcp/".$datiUser['id_ente_admin']."/".$annoRiferimento.".xml";
		$licenza = "IODL";
		
		//esportare le sole procedure a partire dal 01/12/2012 anche se hanno liquidazioni in anni successivi a tale data (es. una procedura del 2011 con liquidazioni dopo 01/12/2012 non va comunicata)
		$dataStart = mktime(0,0,0,12,1,2012);
		
		if($annoRiferimento == 2013) {
			$inizio = mktime(0,0,0,12,1,$annoRiferimento-1);
		} else {
			$inizio = mktime(0,0,0,1,1,$annoRiferimento);
		}
		$fine = mktime(23,59,59,12,31,$annoRiferimento);
		$condAnno = " AND data_attivazione >= ".$inizio." AND data_attivazione <= ".$fine." ";
		$condTipologia = " AND (tipologia = 'bandi ed inviti' OR tipologia = 'esiti' OR tipologia = 'delibere e determine a contrarre' OR tipologia = 'affidamenti' OR tipologia = 'somme liquidate') ";
		$condStato = " AND stato_pubblicazione = '100'";
		
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente = ".$datiUser['id_ente_admin']." ".$condTipologia." ".$condAnno." ".$condStato." ORDER BY data_attivazione";
		if($result = $database->connessioneConReturn($sql)) {
			$recordBandi = $database->sqlArrayAss($result);
		}
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->encoding = 'UTF-8';
		
		//root
		$root = $xmlDoc->appendChild($xmlDoc->createElement("legge190:pubblicazione"));
		$root->appendChild($xmlDoc->createAttribute("xsi:schemaLocation"))->appendChild($xmlDoc->createTextNode("legge190_1_0 datasetAppaltiL190.xsd"));
		$root->appendChild($xmlDoc->createAttribute("xmlns:xsi"))->appendChild($xmlDoc->createTextNode("http://www.w3.org/2001/XMLSchema-instance"));
		$root->appendChild($xmlDoc->createAttribute("xmlns:legge190"))->appendChild($xmlDoc->createTextNode("legge190_1_0"));
		
		//metadata
		$meta = $root->appendChild($xmlDoc->createElement("metadata"));
		$meta->appendChild($xmlDoc->createElement("titolo"))->appendChild($xmlDoc->createTextNode(utf8_encode($titolo)));
		$meta->appendChild($xmlDoc->createElement("abstract"))->appendChild($xmlDoc->createTextNode(utf8_encode($abstract)));
		$meta->appendChild($xmlDoc->createElement("dataPubbicazioneDataset"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataPubblicazioneDataset)));
		$meta->appendChild($xmlDoc->createElement("entePubblicatore"))->appendChild($xmlDoc->createTextNode(utf8_encode($entePubblicatore)));
		$meta->appendChild($xmlDoc->createElement("dataUltimoAggiornamentoDataset"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataUltimoAggiornamentoDataset)));
		$meta->appendChild($xmlDoc->createElement("annoRiferimento"))->appendChild($xmlDoc->createTextNode(utf8_encode($annoRiferimento)));
		$meta->appendChild($xmlDoc->createElement("urlFile"))->appendChild($xmlDoc->createTextNode(utf8_encode($urlFile)));
		$meta->appendChild($xmlDoc->createElement("licenza"))->appendChild($xmlDoc->createTextNode(utf8_encode($licenza)));
		
		//data
		$data = $root->appendChild($xmlDoc->createElement("data"));
		
		//inizializzo array dei lotti analizzati per non includerli 2 o pi� volte
		$arrayLottiAnalizzati = array();
		foreach((array)$recordBandi as $rb) {
		
			if($rb['bando_collegato'] > 0) {
				//parto sempre da chi non ha nulla associato
				$idPartenza = $this->ricavaIdAntenato($rb['bando_collegato']);
			} else {
				$idPartenza = $rb['id'];
			}
			
			//sovrascrivo $rb con il record preso da $idPartenza: � il nodo padre che non ha procedure relative (bando_collegato <=0)
			$rb = mostraDatoOggetto($idPartenza, 11, '*');
			
			//elaboro il record solo se non � un avviso e se non � gi� stato analizzato
			if($rb['tipologia'] != 'avvisi pubblici' and !$arrayLottiAnalizzati[$rb['id']] and $rb['id'] > 0 and $rb['data_attivazione'] > $dataStart) {
				$arrayLottiAnalizzati[$idPartenza] = true;
			
				//creo il lotto a partire da $idPartenza: con questa modalit� riprendo anche lotti di anni precedenti ma che hanno liquidazioni nell'anno in analisi
				$records = array();
				if($rb['tipologia'] == 'bandi ed inviti' and $rb['id'] == $rb['id_record_cig_principale']) {
					//ho un bando composto da pi� lotti/cig
					$sqlLotti = "SELECT * FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente = ".$datiUser['id_ente_admin']." AND id_record_cig_principale = ".$rb['id']." AND id != ".$rb['id']." ORDER BY id";
					if($resLotti = $database->connessioneConReturn($sqlLotti)) {
						$recordsLotti = $database->sqlArrayAss($resLotti);
						foreach((array)$recordsLotti as $l) {
							$rb['__id_lotto'] = $l['id'];
							$rb['cig'] = $l['cig'];
							$rb['valore_base_asta'] = $l['valore_base_asta'];
							$rec = $this->preparaLottoArray($rb, 0, true);
							$records[] = $rec;
						}
					}
				} else {
					//ho un bando con un solo lotto/cig
					$rec = $this->preparaLottoArray($rb);
					$records[] = $rec;
				}
				
				//TODO: ciclare $records
				foreach((array)$records as $r) {
				
					$lotto = $data->appendChild($xmlDoc->createElement("lotto"));
					//$cig = utf8_encode(substr($r['cig'], 0, 10));
					$cig = utf8_encode(trim($r['cig']));
					if($cig == '') {
						$cig = '0000000000';
					}
					$lotto->appendChild($xmlDoc->createElement("cig"))->appendChild($xmlDoc->createTextNode($cig));
					//$lotto->appendChild($xmlDoc->createComment(" ID: ".$r['id']." - Partenza:  ".$idPartenza));
					//$lotto->appendChild($xmlDoc->createComment(" SC: ".$r['scelta_contraente']." "));
					
					$strutturaProponente = $lotto->appendChild($xmlDoc->createElement("strutturaProponente"));
					$codiceFiscaleProp = trim($r['dati_aggiudicatrice']);
					if(!validaCfPi($codiceFiscaleProp)) {
						//se non � corretto non lo esporto
						$codiceFiscaleProp = '';
					}
					$strutturaProponente->appendChild($xmlDoc->createElement("codiceFiscaleProp"))->appendChild($xmlDoc->createTextNode(utf8_encode($codiceFiscaleProp)));
					$strutturaProponente->appendChild($xmlDoc->createElement("denominazione"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim(substr(html_entity_decode($r['denominazione_aggiudicatrice']), 0, 250)))));
					
					$lotto->appendChild($xmlDoc->createElement("oggetto"))->appendChild($xmlDoc->createTextNode(utf8_encode(substr(html_entity_decode($r['oggetto']), 0, 250))));
					
					$sceltaContraente = trim($r['scelta_contraente']);
					if(!validaSceltaContraente($sceltaContraente)) {
						//se non � corretta non la esporto
						$sceltaContraente = '';
					} else {
						//nella scelta 14 c'� una virgola che nel DB non viene salvata, altrimenti il sistema non funziona (colpa della select con valori)
						if($sceltaContraente == '14-PROCEDURA SELETTIVA EX ART 238 C.7 D.LGS. 163/2006') {
							$sceltaContraente = '14-PROCEDURA SELETTIVA EX ART 238 C.7, D.LGS. 163/2006';
						}
					}
					$lotto->appendChild($xmlDoc->createElement("sceltaContraente"))->appendChild($xmlDoc->createTextNode(utf8_encode($sceltaContraente)));
					
					$partecipanti = $lotto->appendChild($xmlDoc->createElement("partecipanti"));
					
					//verificare se ci sono pi� partecipanti
					$idPartecipanti = explode(',', $r['elenco_partecipanti']);
					$condPartecipanti = array();
					foreach((array)$idPartecipanti as $idp) {
						if($idp > 0) {
							$condPartecipanti[] = " id = ".$idp." ";
						}
					}
					$dataScript = mktime(0,0,0,date("m"),date("d"),date("Y"));
					if($r['tipologia'] == 'bandi ed inviti' and $r['data_scadenza'] >= $dataScript) {
						//il bando non � ancora scaduto quindi escludo tutti i partecipanti
						$condPartecipanti = array();
					}
					
					if(count($condPartecipanti)) {
						$condPartecipanti = " AND (".implode(' OR ', $condPartecipanti).") ";
						$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$datiUser['id_ente_admin'].$condPartecipanti;
						if($par = $database->connessioneConReturn($sql)) {
							$par = $database->sqlArrayAss($par);
							if(count($par > 0)) {
								$partecipantiSingoli = array();
								$partecipantiRaggruppamenti = array();
								foreach((array)$par as $p) {
									//if($p['tipologia'] == 'raggruppamento') {
									if(trim($p['mandante']) != '' or trim($p['mandataria']) != '' or trim($p['associata']) != '' or trim($p['capogruppo']) != '' or trim($p['consorziata']) != '') {
										$partecipantiRaggruppamenti[] = $p;
									} else {
										$partecipantiSingoli[] = $p;
									}
								}
								$par = null;
								foreach((array)$partecipantiRaggruppamenti as $p) {
									//raggruppamento
									$raggruppamento = $partecipanti->appendChild($xmlDoc->createElement("raggruppamento"));
									//mandante
									$idParRagg = explode(',', trim($p['mandante']));
									foreach((array)$idParRagg as $idPar) {
										if($idPar > 0) {
											$pRagg = mostraDatoOggetto($idPar, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($pRagg) {
												if(!validaCfPi($pRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$pRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('01-MANDANTE')));
										}
									}
									//mandataria
									$idParRagg = explode(',', trim($p['mandataria']));
									foreach((array)$idParRagg as $idPar) {
										if($idPar > 0) {
											$pRagg = mostraDatoOggetto($idPar, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($pRagg['codice_fiscale'] != '') {
												if(!validaCfPi($pRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$pRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('02-MANDATARIA')));
										}
									}
									//associata
									$idParRagg = explode(',', trim($p['associata']));
									foreach((array)$idParRagg as $idPar) {
										if($idPar > 0) {
											$pRagg = mostraDatoOggetto($idPar, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($pRagg['codice_fiscale'] != '') {
												if(!validaCfPi($pRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$pRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($p['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('03-ASSOCIATA')));
										}
									}
									//capogruppo
									$idParRagg = explode(',', trim($p['capogruppo']));
									foreach((array)$idParRagg as $idPar) {
										if($idPar > 0) {
											$pRagg = mostraDatoOggetto($idPar, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($pRagg['codice_fiscale'] != '') {
												if(!validaCfPi($pRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$pRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($p['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('04-CAPOGRUPPO')));
										}
									}
									//consorziata
									$idParRagg = explode(',', trim($p['consorziata']));
									foreach((array)$idParRagg as $idPar) {
										if($idPar > 0) {
											$pRagg = mostraDatoOggetto($idPar, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($pRagg['codice_fiscale'] != '') {
												if(!validaCfPi($p['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$pRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($pRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('05-CONSORZIATA')));
										}
									}
								}
								$partecipantiRaggruppamenti = null;
								foreach((array)$partecipantiSingoli as $p) {
									//singolo
									$partecipante = $partecipanti->appendChild($xmlDoc->createElement("partecipante"));
									$inclusoCF = false;
									if($p['codice_fiscale'] != '') {
										if(!validaCfPi($p['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$p['codice_fiscale'] = '';
										} else {
											$partecipante->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($p['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($p['fiscale_estero'] != '' and !$inclusoCF) {
										//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
										$partecipante->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($p['fiscale_estero']))));
									}
									$partecipante->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($p['nominativo'])))));
								}
								$partecipantiSingoli = null;
							}
						}
					}
					
					$aggiudicatari = $lotto->appendChild($xmlDoc->createElement("aggiudicatari"));
					
					//verificare se ci sono pi� aggiudicatari
					$idAggiudicatari = explode(',', $r['elenco_aggiudicatari']);
					$condAggiudicatari = array();
					foreach((array)$idAggiudicatari as $ida) {
						if($ida > 0) {
							$condAggiudicatari[] = " id = ".$ida." ";
						}
					}
					if($r['tipologia'] == 'bandi ed inviti' and $r['data_scadenza'] >= $dataScript) {
						//il bando non � ancora scaduto quindi escludo tutti i partecipanti
						$condAggiudicatari = array();
					}
					
					if(count($condAggiudicatari)) {
						$condAggiudicatari = " AND (".implode(' OR ', $condAggiudicatari).") ";
						$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$datiUser['id_ente_admin'].$condAggiudicatari;
						if($agg = $database->connessioneConReturn($sql)) {
							$agg = $database->sqlArrayAss($agg);
							if(count($agg > 0)) {
								$aggiudicatariSingoli = array();
								$aggiudicatariRaggruppamenti = array();
								foreach((array)$agg as $a) {
									//if($a['tipologia'] == 'raggruppamento') {
									if(trim($a['mandante']) != '' or trim($a['mandataria']) != '' or trim($a['associata']) != '' or trim($a['capogruppo']) != '' or trim($a['consorziata']) != '') {
										$aggiudicatariRaggruppamenti[] = $a;
									} else {
										$aggiudicatariSingoli[] = $a;
									}
								}
								$agg = null;
								foreach((array)$aggiudicatariRaggruppamenti as $a) {
									//raggruppamento
									$raggruppamento = $aggiudicatari->appendChild($xmlDoc->createElement("aggiudicatarioRaggruppamento"));
									//mandante
									$idAggRagg = explode(',', trim($a['mandante']));
									foreach((array)$idAggRagg as $idAgg) {
										if($idAgg > 0) {
											$aRagg = mostraDatoOggetto($idAgg, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($aRagg['codice_fiscale'] != '') {
												if(!validaCfPi($aRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$aRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('01-MANDANTE')));
										}
									}
									//mandataria
									$idAggRagg = explode(',', trim($a['mandataria']));
									foreach((array)$idAggRagg as $idAgg) {
										if($idAgg > 0) {
											$aRagg = mostraDatoOggetto($idAgg, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($aRagg['codice_fiscale'] != '') {
												if(!validaCfPi($aRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$aRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('02-MANDATARIA')));
										}
									}
									//associata
									$idAggRagg = explode(',', trim($a['associata']));
									foreach((array)$idAggRagg as $idAgg) {
										if($idAgg > 0) {
											$aRagg = mostraDatoOggetto($idAgg, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($aRagg['codice_fiscale'] != '') {
												if(!validaCfPi($aRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$aRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('03-ASSOCIATA')));
										}
									}
									//capogruppo
									$idAggRagg = explode(',', trim($a['capogruppo']));
									foreach((array)$idAggRagg as $idAgg) {
										if($idAgg > 0) {
											$aRagg = mostraDatoOggetto($idAgg, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($aRagg['codice_fiscale'] != '') {
												if(!validaCfPi($aRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$aRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('04-CAPOGRUPPO')));
										}
									}
									//consorziata
									$idAggRagg = explode(',', trim($a['consorziata']));
									foreach((array)$idAggRagg as $idAgg) {
										if($idAgg > 0) {
											$aRagg = mostraDatoOggetto($idAgg, 41, '*');
											$membro = $raggruppamento->appendChild($xmlDoc->createElement("membro"));
											$inclusoCF = false;
											if($aRagg['codice_fiscale'] != '') {
												if(!validaCfPi($aRagg['codice_fiscale'])) {
													//se non � corretto non lo esporto
													$aRagg['codice_fiscale'] = '';
												} else {
													$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
												}
												$inclusoCF = true;
											}
											if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
												//inserisco identificativo fiscale estero solo se non ho gi� inserito il codice fiscale
												$membro->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['fiscale_estero']))));
											}
											$membro->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($aRagg['nominativo'])))));
											$membro->appendChild($xmlDoc->createElement("ruolo"))->appendChild($xmlDoc->createTextNode(utf8_encode('05-CONSORZIATA')));
										}
									}
								}
								$aggiudicatariRaggruppamenti = null;
								foreach((array)$aggiudicatariSingoli as $a) {
									//singolo
									$aggiudicatario = $aggiudicatari->appendChild($xmlDoc->createElement("aggiudicatario"));
									$inclusoCF = false;
									if($a['codice_fiscale'] != '') {
										if(!validaCfPi($a['codice_fiscale'])) {
											//se non � corretto non lo esporto
											$a['codice_fiscale'] = '';
										} else {
											$aggiudicatario->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($a['codice_fiscale']))));
										}
										$inclusoCF = true;
									}
									if($a['fiscale_estero'] != '' and !$inclusoCF) {
										$aggiudicatario->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($a['fiscale_estero']))));
									}
									$aggiudicatario->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($a['nominativo'])))));
								}
								$aggiudicatariSingoli = null;
							}
						}
					}
					
					$importoAggiudicazione = $r['valore_importo_aggiudicazione'];
					if($importoAggiudicazione != '') {
						$importoAggiudicazione = number_format($importoAggiudicazione,2,'.','');
						if($importoAggiudicazione == '') {
							$importoAggiudicazione = 0.00;
						}
					} else {
						$importoAggiudicazione = 0.00;
					}
					$lotto->appendChild($xmlDoc->createElement("importoAggiudicazione"))->appendChild($xmlDoc->createTextNode(utf8_encode($importoAggiudicazione)));
					
					$tempiCompletamento = $lotto->appendChild($xmlDoc->createElement("tempiCompletamento"));
					
					$dataInizio = $r['data_inizio_lavori'];
					if($dataInizio > 0) {
						$dataInizio = date('Y-m-d', $dataInizio);
						$tempiCompletamento->appendChild($xmlDoc->createElement("dataInizio"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataInizio)));
					}
					
					$dataUltimazione = $r['data_lavori_fine'];
					if($dataUltimazione > 0) {
						$dataUltimazione = date('Y-m-d', $dataUltimazione);
						$tempiCompletamento->appendChild($xmlDoc->createElement("dataUltimazione"))->appendChild($xmlDoc->createTextNode(utf8_encode($dataUltimazione)));
					}
					
					$importoSommeLiquidate = $r['importo_liquidato'];
					if($importoSommeLiquidate != '') {
						$importoSommeLiquidate = number_format($importoSommeLiquidate,2,'.','');
						if($importoSommeLiquidate == '') {
							$importoSommeLiquidate = 0.00;
						}
					} else {
						$importoSommeLiquidate = 0.00;
					}
					$lotto->appendChild($xmlDoc->createElement("importoSommeLiquidate"))->appendChild($xmlDoc->createTextNode(utf8_encode($importoSommeLiquidate)));
				}
			} //fine if di controllo se diverso da avviso e se record non ancora elaborato
		} //fine ciclo for
		
		$recordBandi = null;
		$database->sqlLiberaRisultato($result);
		
		@header("Content-Type: text/plain");
		$xmlDoc->formatOutput = true;
		return $xmlDoc->saveXML();
		
	}
	
	private function ricavaIdAntenato($id) {
		global $configurazione, $database, $dati_db;
		
		$sql = "SELECT id, bando_collegato FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id = ".$id;
		if($result = $database->connessioneConReturn($sql)) {
			$result = $database->sqlArray($result);
		}
		if($result['id'] and $result['bando_collegato'] > 0) {
			$idVerifica = $result['bando_collegato'];
			$result = null;
			return $this->ricavaIdAntenato($idVerifica);
		} else {
			return $result['id'];
		}
	}
	
	private function prendiFigli($idPadre) {
		global $configurazione, $database, $dati_db;
		
		$sql = "SELECT id,tipologia,elenco_partecipanti,elenco_aggiudicatari,valore_importo_aggiudicazione,importo_liquidato,data_inizio_lavori,data_lavori_fine FROM "
			.$dati_db['prefisso']."oggetto_gare_atti WHERE bando_collegato = ".$idPadre;
		if($result = $database->connessioneConReturn($sql)) {
			$result = $database->sqlArrayAss($result);
		}
		if(count($result) > 0) {
			return $result;
		} else {
			return array();
		}
	}
	
	private function preparaLottoArray($lotto, $livello = 0, $isLotto = false) {
		global $enteAdmin, $datiUser, $configurazione, $database, $dati_db, $oggOgg;
		
		if($livello <= 0) {
			$padre = $lotto;
		}
		
		//al primo passaggio ho $lotto che contiene il record padre. Faccio le query per trovare i figli
		if($isLotto) {
			$figli = $this->prendiFigli($lotto['__id_lotto']);
		} else {
			$figli = $this->prendiFigli($lotto['id']);
		}
		foreach((array)$figli as $f) {
			if($f['tipologia'] != 'avvisi pubblici') {
				//ricavare eventuali dati non presenti nel padre: quali? partecipanti, aggiudicatari, importo aggiudicazione, importo somme liquidate, data inizio lavori, data fine lavori
				//partecipanti
				if($f['elenco_partecipanti'] != '') {
					if($lotto['elenco_partecipanti'] != '') {
						$lotto['elenco_partecipanti'] = $lotto['elenco_partecipanti'].",".$f['elenco_partecipanti'];
					} else {
						$lotto['elenco_partecipanti'] = $f['elenco_partecipanti'];
					}
				}
				//aggiudicatari
				if($f['elenco_aggiudicatari'] != '') {
					if($lotto['elenco_aggiudicatari'] != '') {
						$lotto['elenco_aggiudicatari'] = $lotto['elenco_aggiudicatari'].",".$f['elenco_aggiudicatari'];
					} else {
						$lotto['elenco_aggiudicatari'] = $f['elenco_aggiudicatari'];
					}
				}
				//importo aggiudicazione
				if($f['valore_importo_aggiudicazione'] > 0) {
					if($lotto['valore_importo_aggiudicazione'] > 0) {
						$lotto['valore_importo_aggiudicazione'] = $lotto['valore_importo_aggiudicazione'] + $f['valore_importo_aggiudicazione'];
					} else {
						$lotto['valore_importo_aggiudicazione'] = $f['valore_importo_aggiudicazione'];
					}
				}
				//importo somme liquidate 
				if($f['importo_liquidato'] > 0) {
					if($lotto['importo_liquidato'] > 0) {
						$lotto['importo_liquidato'] = $lotto['importo_liquidato'] + $f['importo_liquidato'];
					} else {
						$lotto['importo_liquidato'] = $f['importo_liquidato'];
					}
				}
				//data inizio lavori
				if($f['data_inizio_lavori'] > 0 and $lotto['data_inizio_lavori'] == '') {
					$lotto['data_inizio_lavori'] = $f['data_inizio_lavori'];
				}
				//data fine lavori
				if($f['data_lavori_fine'] > 0 and $lotto['data_lavori_fine'] == '') {
					$lotto['data_lavori_fine'] = $f['data_lavori_fine'];
				}
				
				//ora che ho integrato eventuali dati mancanti faccio partire la ricorsione passando l'attuale figlio
				$idLotto = $lotto['id'];
				$lotto['id'] = $f['id'];
				$lotto = $this->preparaLottoArray($lotto, $livello+1);
				$lotto['id'] = $idLotto;
			}
		} //fine foreach sui figli
		$figli = null;
		
		if($livello > 0) {
			return $lotto;
		} else {
			//imposti i campi eventualmente sovrascritti
			$padre['elenco_partecipanti'] = $lotto['elenco_partecipanti'];
			$padre['elenco_aggiudicatari'] = $lotto['elenco_aggiudicatari'];
			$padre['valore_importo_aggiudicazione'] = $lotto['valore_importo_aggiudicazione'];
			$padre['importo_liquidato'] = $lotto['importo_liquidato'];
			$padre['data_inizio_lavori'] = $lotto['data_inizio_lavori'];
			$padre['data_lavori_fine'] = $lotto['data_lavori_fine'];
			$lotto = null;
			return $padre;
		}
	}

}
?>