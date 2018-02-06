<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015 - AgID Agenzia per l'Italia Digitale
	*
	* Concesso in licenza a norma dell'EUPL, versione 1.1 o
	successive dell'EUPL (la "Licenza")– non appena saranno
	approvate dalla Commissione europea;
	* Non è possibile utilizzare l'opera salvo nel rispetto
	della Licenza.
	* È possibile ottenere una copia della Licenza al seguente
	indirizzo:
	*
	* https://joinup.ec.europa.eu/software/page/eupl
	*
	* Salvo diversamente indicato dalla legge applicabile o
	concordato per iscritto, il software distribuito secondo
	i termini della Licenza è distribuito "TAL QUALE",
	* SENZA GARANZIE O CONDIZIONI DI ALCUN TIPO,
	esplicite o implicite.
	* Si veda la Licenza per la lingua specifica che disciplina
	le autorizzazioni e le limitazioni secondo i termini della
	Licenza.
	*/ 
/**
 * @file
 * creaXML.php
 * 
 * @Descrizione
 * File eseguibile per creazione statica di un file XML di comunicazione all'ANAC. CONFIGURARE I PARAMETRI INIZIALI E RICHIAMARE LO SCRIPT PER CREARE UN FILE
 *
 */
 
// inclusione configurazione 
include ('./inc/config.php'); // configurazione ISWEB

include('inc/inizializzazione.php');
include_once('pat/funzioniAVCP.php');

$dominioTest = '.nopmedominio.it'; // inserire dominio principale
$enteAdmin['nome_breve_ente'] = ''; // inserire nome breve ente da configurazione
$enteAdmin['nome_completo_ente'] = ''; // inserire nome completo ente da configurazione
$datiUser['id_ente_admin'] = 0; // inserire ID dell'ente da amministrare
$annoRiferimento = 2014; // editare anno di cui si vuole creare file

$entePubblicatore = $enteAdmin['nome_completo_ente'];

$dataPubblicazioneDataset = date('Y-m-d');
$dataUltimoAggiornamentoDataset = date('Y-m-d');

//configurazione metadata
$titolo = "Pubblicazione legge 190/2012";
$abstract = "Pubblicazione legge 190/2012 anno di riferimento ".$annoRiferimento;
$urlFile = "http://".$enteAdmin['nome_breve_ente'].$dominioTest."/avcp/".$datiUser['id_ente_admin']."/".$annoRiferimento.".xml";
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
		//se non è corretto non lo esporto
		$codiceFiscaleProp = '';
	}
	$strutturaProponente->appendChild($xmlDoc->createElement("codiceFiscaleProp"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($codiceFiscaleProp))));
	$strutturaProponente->appendChild($xmlDoc->createElement("denominazione"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim(substr(html_entity_decode($r['denominazione_aggiudicatrice']), 0, 250)))));
	
	$lotto->appendChild($xmlDoc->createElement("oggetto"))->appendChild($xmlDoc->createTextNode(utf8_encode(substr(html_entity_decode(trim($r['oggetto'])), 0, 250))));
	
	$sceltaContraente = trim($r['scelta_contraente']);
	if(!validaSceltaContraente($sceltaContraente)) {
		//se non è corretta non la esporto
		$sceltaContraente = '';
	} else {
		//nella scelta 14 c'è una virgola che nel DB non viene salvata, altrimenti il sistema non funziona (colpa della select con valori)
		if($sceltaContraente == '14-PROCEDURA SELETTIVA EX ART 238 C.7 D.LGS. 163/2006') {
			$sceltaContraente = '14-PROCEDURA SELETTIVA EX ART 238 C.7, D.LGS. 163/2006';
		}
	}
	$lotto->appendChild($xmlDoc->createElement("sceltaContraente"))->appendChild($xmlDoc->createTextNode(utf8_encode($sceltaContraente)));
	
	$partecipanti = $lotto->appendChild($xmlDoc->createElement("partecipanti"));
	
	//verificare se ci sono più partecipanti
	$idPartecipanti = explode(',', $r['elenco_partecipanti']);
	$condPartecipanti = array();
	foreach((array)$idPartecipanti as $idp) {
		if($idp > 0) {
			$condPartecipanti[] = " id = ".$idp." ";
		}
	}
	$dataScript = mktime(0,0,0,date("m"),date("d"),date("Y"));
	if($r['tipologia'] == 'bandi ed inviti' and $r['data_scadenza'] >= $dataScript) {
		//il bando non è ancora scaduto quindi escludo tutti i partecipanti
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
									//se non è corretto non lo esporto
									$pRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$pRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$pRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($p['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$pRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($p['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$pRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($pRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($pRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
							//se non è corretto non lo esporto
							$p['codice_fiscale'] = '';
						} else {
							$partecipante->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($p['codice_fiscale']))));
						}
						$inclusoCF = true;
					}
					if($p['fiscale_estero'] != '' and !$inclusoCF) {
						//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
						$partecipante->appendChild($xmlDoc->createElement("identificativoFiscaleEstero"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($p['fiscale_estero']))));
					}
					$partecipante->appendChild($xmlDoc->createElement("ragioneSociale"))->appendChild($xmlDoc->createTextNode(utf8_encode(html_entity_decode(trim($p['nominativo'])))));
				}
			}
		}
	}
	
	$aggiudicatari = $lotto->appendChild($xmlDoc->createElement("aggiudicatari"));
	
	//verificare se ci sono più aggiudicatari
	$idAggiudicatari = explode(',', $r['elenco_aggiudicatari']);
	$condAggiudicatari = array();
	foreach((array)$idAggiudicatari as $ida) {
		if($ida > 0) {
			$condAggiudicatari[] = " id = ".$ida." ";
		}
	}
	if($r['tipologia'] == 'bandi ed inviti' and $r['data_scadenza'] >= $dataScript) {
		//il bando non è ancora scaduto quindi escludo tutti i partecipanti
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
									//se non è corretto non lo esporto
									$aRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$aRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$aRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$aRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
									//se non è corretto non lo esporto
									$aRagg['codice_fiscale'] = '';
								} else {
									$membro->appendChild($xmlDoc->createElement("codiceFiscale"))->appendChild($xmlDoc->createTextNode(utf8_encode(trim($aRagg['codice_fiscale']))));
								}
								$inclusoCF = true;
							}
							if($aRagg['fiscale_estero'] != '' and !$inclusoCF) {
								//inserisco identificativo fiscale estero solo se non ho già inserito il codice fiscale
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
							//se non è corretto non lo esporto
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
echo $xmlDoc->saveXML();

?>