<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.0 - AgID release//////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function lognascosto($s, $par = array(), $nascondi = true) {
	global $datiUser;
	if($datiUser['permessi'] != '10' and $nascondi) {
		$style = "display:none;";
	}
	if(!$nascondi){
		$nascondi = 'visibile';
	}
	echo "<pre style='$style'><br/>($nascondi) Log: ";
	echo $s."<br/>";
	if(count($par) > 0) {
		print_r($par);
	}
	echo "</pre>";
}

function lognormale($s,$arr = array()){
	lognascosto($s,$arr,false);
}

function loadingConfigurazione() {
	global $dati_db,$database,$server_url;
	
	$sql = "SELECT * FROM ".$dati_db['prefisso']."configurazione";
	if( !($result = $database->connessioneConReturn($sql)) ) {
		die();
	} else {
		while ( $riga = $database->sqlArray($result) ) {
			$configurazione[$riga['nome']] = $riga['valore'];
		}
	}
	return $configurazione;
}
function loadingOggetti() {		
	global $dati_db,$database,$configurazione;
	$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti ORDER BY nome";
	if( !($result = $database->connessioneConReturn($sql)) ) {
		die("non riesco a prendere le informazioni sugli oggetti in db");
	}
	return $database->sqlArrayAss($result);
}
function mostraCampoOggetto($idOgg, $campo = 'nome') {
	global $oggetti;
	foreach ((array)$oggetti as $oggTemp) {
		if ($oggTemp['id'] == $idOgg) {
			return $oggTemp[$campo];
		}
	}
	return FALSE;
}

function caricaIstanzaOggetto($idDocumento, $tabella, $idEnte, $datiDaCaricare = '*') {
	global $database, $dati_db;
	
	$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db['prefisso'].$tabella." WHERE id = ".$idDocumento." AND id_ente = ".$idEnte." ORDER BY id DESC LIMIT 1";
	if (!($result = $database -> connessioneConReturn($sql))) {
	} else {
		$istanza = $database->sqlArray($result, MYSQL_ASSOC);
		if(!is_array($istanza)) {
			//retrocompatibilità: provare con il vecchio id
			$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db['prefisso'].$tabella." WHERE id_ori = ".$idDocumento." AND id_ente = ".$idEnte." ORDER BY id DESC LIMIT 1";
			if (!($result = $database -> connessioneConReturn($sql))) {
			} else {
				$istanza = $database->sqlArray($result, MYSQL_ASSOC);
			}
		}
		if(count($istanza)) {
			return $istanza;
		} else {
			return array();
		}
	}
	return array();
}

function erroreXML($errore) {
	$doc = new DOMDocument();
	$doc->encoding = 'UTF-8';
	$root = $doc->createElement('errore');
	$root = $doc->appendChild($root);
	$valore = $doc->createTextNode($errore);
	$valore = $root->appendChild($valore);
	$doc->formatOutput = true;
	return $doc;
}
function generaLinkElemento($idEnte, $idOggetto, $idDocumento) {
	global $database, $dati_db, $configurazione;
	
	$sql = "SELECT url_etrasparenza FROM ".$dati_db['prefisso']."etrasp_enti WHERE id = ".$idEnte;
	if (!($result = $database -> connessioneConReturn($sql))) {
	} else {
		$istanza = $database->sqlArray($result);
		$url = $istanza['url_etrasparenza'];
		require('../classi/documento.php');
		$documento = new documento($idOggetto);
		$nome = pulisciNome($documento->nome);
		$strAncora = $url . "/archivio".$idOggetto."_".$nome."_0_" . $idDocumento . "_0_1.html";
		return $strAncora;
	}
}
function creaLinkLettura($idOggetto, $nome, $chiave, $istanza) {
	global $idOggettoModello;
	if($idOggetto == $idOggettoModello) {
		return "/pagina".$chiave."_".pulisciNome($istanza).".html";
	} else {
		return "/archivio".$idOggetto."_".pulisciNome($nome)."_0_" . $chiave . "_0_1.html";
	}
}
function getUrlTrasparenza($idEnte) {
	global $database, $dati_db, $configurazione;
	if(isset($configurazione['url_trasparenza']) && $configurazione['url_trasparenza'] != '') {
		return $configurazione['url_trasparenza'];
	} else {
		$sql = "SELECT url_etrasparenza FROM ".$dati_db['prefisso']."etrasp_enti WHERE id = ".$idEnte;
		if (!($result = $database -> connessioneConReturn($sql))) {
		} else {
			$istanza = $database->sqlArray($result);
			$configurazione['url_trasparenza'] = $istanza['url_etrasparenza'];
		}
		return $configurazione['url_trasparenza'];
	}
}
function getCampiOggetto($idOggetto) {
	switch($idOggetto) {
		case 3:
			//personale ente
			return 'id,referente,ruolo,mobile,fax,email,email_cert';
		break;
		case 4:
			//incarichi e consulenze
			return '*';
		break;
		case 5:
			//modulistica
			return 'id,titolo,descrizione_mod';
		break;
		case 11:
			//bandi di gara
			return '*';
		break;
		case 12:
			//domande e risposte
			return '*';
		break;
		case 13:
			//strutture organizzative
			return 'id,nome_ufficio,sede,email_riferimento,telefono,desc_att';
		break;
		case 16:
			//procedimenti
			return 'id,nome,descrizione';
		break;
		case 19:
			//regolamenti e documentazione
			return 'id,titolo,descrizione_mod';
		break;
		case 22:
			//bandi di concorsi
			return '*';
		break;
		case 27:
			//normativa
			return '*';
		break;
		case 29:
			//bilanci
			return '*';
		break;
		case 30:
			//oneri informativi
			return '*';
		break;
		case 33:
			//modello di contenuto
			return '*';
		break;
		case 38:
			//sovvenzioni e vantaggi economici
			return '*';
		break;
		case 41:
			//elenco fornitori
			return '*';
		break;
		default:
			return '*';
		break;
	}
}
function getCampoDefaultOggetto($idOggetto) {
	switch($idOggetto) {
		case 3:
			//personale ente
			return 'referente';
		break;
		case 4:
			//incarichi e consulenze
			return 'nominativo';
		break;
		case 5:
			//modulistica
			return 'titolo';
		break;
		case 11:
			//bandi di gara
			return 'oggetto';
		break;
		case 13:
			//strutture organizzative
			return 'nome_ufficio';
		break;
		case 16:
			//procedimenti
			return 'nome';
		break;
		case 19:
			//regolamenti e documentazione
			return 'titolo';
		break;
		case 22:
			//bandi di concorsi
			return 'oggetto';
		break;
		case 27:
			//normativa
			return 'nome';
		break;
		case 29:
			//bilanci
			return 'nome';
		break;
		case 30:
			//oneri informativi
			return 'titolo';
		break;
		case 38:
			//sovvenzioni e vantaggi economici
			return 'oggetto';
		break;
		case 41:
			//elenco fornitori
			return 'nominativo';
		break;
		default:
			return '*';
		break;
	}
}
function getLinkSezioneElenco($idOggetto, $idEnte) {
	switch($idOggetto) {
		case 3:
			//personale ente
			return getUrlTrasparenza($idEnte).'/pagina713_personale.html';
		break;
		case 4:
			//incarichi e consulenze
			return getUrlTrasparenza($idEnte).'/pagina19_consulenti-e-collaboratori.html';
		break;
		case 5:
			//modulistica
			return getUrlTrasparenza($idEnte).'/pagina26_modulistica.html';
		break;
		case 11:
			//bandi di gara
			return getUrlTrasparenza($idEnte).'/pagina566_bandi-di-gara-e-contratti.html';
		break;
		case 13:
			//strutture organizzative
			return getUrlTrasparenza($idEnte).'/pagina25_articolazione-degli-uffici.html';
		break;
		case 16:
			//procedimenti
			return getUrlTrasparenza($idEnte).'/pagina21_attivit-e-procedimenti.html';
		break;
		case 19:
			//regolamenti e documentazione
			return getUrlTrasparenza($idEnte).'/pagina39_regolamenti.html';
		break;
		case 22:
			//bandi di concorso
			return getUrlTrasparenza($idEnte).'/pagina639_bandi-di-concorso.html';
		break;
		case 27:
			//normativa
			return getUrlTrasparenza($idEnte).'/pagina752_normativa.html';
		break;
		case 29:
			//bilanci
			return getUrlTrasparenza($idEnte).'/pagina730_bilanci.html';
		break;
		case 30:
			//oneri informativi
			return getUrlTrasparenza($idEnte).'/pagina700_oneri-informativi-per-cittadini-ed-imprese.html';
		break;
		case 33:
			//modello di contenuto
			return getUrlTrasparenza($idEnte).'/pagina0_home-page.html';
		break;
		case 38:
			//sovvenzioni e vantaggi economici
			return getUrlTrasparenza($idEnte).'/pagina728_sovvenzioni-contributi-sussidi-vantaggi-economici.html';
		break;
		default:
			return getUrlTrasparenza($idEnte).'/pagina0_home-page.html';
		break;
	}
}

function ricercaOggetto($idEnte, $idOggetto, $numero, $q) {
	global $database, $dati_db, $configurazione,$idOggettoModello,$sezioni;
	require_once('../classi/documento.php');
	$o = new documento($idOggetto,'si');
	$campo = $o->campo_default;
	
	$minChars = isset($_GET['minChars']) ? $_GET['minChars'] : 0;
	if($minChars == 0) {
		$minChars = 3;
	}
	$condizioneLimite = '';
	if ($numero != 0) {
		$condizioneLimite = ' LIMIT 0,'.$numero;
	}
	$condizioneEnte = ' AND og.id_ente = '.$idEnte.' ';
	
	$condizioneAggiuntiva = '';
	switch($idOggetto) {
		case '16':
		case '19':
		case '29':
		case '11':
		case '22':
		case '38':
		case '4':
		case '28':
		case '30':
			$condizioneAggiuntiva .= " AND stato_pubblicazione = '100' ";
		break;
	}
	
	$entePubblicato = datoEnte($idEnte);
	$tipoEnte = datoTipoEnte($entePubblicato['tipo_ente']);	
	
	//la ricerca la faccio su ogni parola: ricerca in OR
	//NOTA 20/11/2014: ho applicato anche un 'htmlentities' per i caratteri accentati. Verificare se va bene per tutti
	//$q = utf8_decode($q);
	$q = htmlentities(utf8_decode($q));
	$qRicerca = explode(' ',$q);
	//ricerca su ogni campo ricercabile di tipo testo (string,blob,text)
	$arrayCampiRicerca = array();
	$arrayCampiSelect = array();
	if($idOggetto == $idOggettoModello) {
		$sezioniTrovate = "-1";
		$arrayCampiSelect[] = 'og.id_sezione_etrasp';
		foreach((array)$qRicerca as $qr) {
			if(strlen(stripslashes($qr)) >= $minChars) {
				$arrayCampiRicerca[] = "(LOWER(s.nome) = LOWER('".$qr."') OR LOWER(s.nome) LIKE LOWER('".$qr."%') OR LOWER(s.nome) LIKE LOWER('% ".$qr."%') OR LOWER(s.nome) LIKE LOWER('%\'".$qr."%') ) ";
			}
			//fix per i nomi di sezione degli organi politici
			if(stripos($tipoEnte['org_sindaco'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 702 ) ";
			} else if(stripos($tipoEnte['org_vicesindaco'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 792 ) ";
			} else if(stripos($tipoEnte['org_giunta'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 703 ) ";
			} else if(stripos($tipoEnte['org_presidente'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 704 ) ";
			} else if(stripos($tipoEnte['org_consiglio'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 705 ) ";
			} else if(stripos($tipoEnte['org_direzione'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 706 ) ";
			} else if(stripos($tipoEnte['org_segretario'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 707 ) ";
			} else if(stripos($tipoEnte['org_commissioni'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 708 ) ";
			} else if(stripos($tipoEnte['org_commissario'], $qr) !== false) {
				$arrayCampiRicerca[] = "(og.id_sezione_etrasp = 796 ) ";
			}
		}
	}
	foreach((array)$o->struttura as $c) {
		if(isset($c['ordinamento']) and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text') and in_array($c['nomecampo'],$o->campiRicerca)) {
			$arrayCampiSelect[] = 'og.'.$c['nomecampo'];
			//ricerca su ogni parola inserita
			foreach((array)$qRicerca as $qr) {
				if(strlen(stripslashes($qr)) >= $minChars) {
					$arrayCampiRicerca[] = "(LOWER(og.".$c['nomecampo'].") = LOWER('".$qr."') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('".$qr."%') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('% ".$qr."%') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('%\'".$qr."%') ) ";
				}
			}
		}
	}
	
	$campiRicerca = implode(' OR ', $arrayCampiRicerca);
	$campiSelect = implode(',',$arrayCampiSelect);
	
	$condizioneCriterio = '';
	if($o->criterio_ricerca > 0) {
		$criterio = loadingOggettoCriterio($o->criterio_ricerca);
		$criterio = $o->elaboraCriterio($criterio);
		$condizioneCriterio = $criterio['condizione'];
	}
	
	if($idOggetto == $idOggettoModello) {
		$sql = "SELECT s.nome AS nome_sez, og.id, og.data_creazione AS campo_data_ricerca, og.".$campo." AS campoDefaultTitolo, ".$campiSelect." FROM ".$dati_db['prefisso']."sezioni s INNER JOIN ".$dati_db['prefisso'].$o->tabella." og ON s.id = og.id_sezione_etrasp";
		$sql .= " WHERE og.permessi_lettura != 'H' AND (og.id_lingua=".$o->id_lingua." or og.id_lingua=0) ".$condizioneAggiuntiva.$condizioneEnte." AND (";
		$sql .= $campiRicerca;
		$sql .=") ".$condizioneCriterio." ORDER BY s.nome ".$condizioneLimite;
	} else {
		$sql = "SELECT id, data_creazione AS campo_data_ricerca, ".$campo." AS campoDefaultTitolo, ".$campiSelect." FROM ".$dati_db['prefisso'].$o->tabella." og WHERE permessi_lettura != 'H' AND (id_lingua=".$o->id_lingua." or id_lingua=0) ".$condizioneAggiuntiva.$condizioneEnte." AND (";
		$sql .= $campiRicerca;
		$sql .=") ".$condizioneCriterio." ORDER BY campoDefaultTitolo ".$condizioneLimite;
	}
	
	if (!($result = $database -> connessioneConReturn($sql))) {
		$composizione = array();
	} else {
		$composizione = $database -> sqlArrayAss($result);
	}
	$items = array();
	
	foreach((array)$composizione as $res) {
		//per ogni oggetto devo pulire i campi che possono contenere html
		// e quindi verificare se a quel punto la ricerca è andata a buon fine
		for($i = 0; $i < count($o->struttura); $i++) {
			$c = $o->struttura[$i];
			if(isset($c['ordinamento']) and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text')) {
				//ho un campo su cui è stata effettuata la ricerca
				$contenuto = isset($res[$c['nomecampo']]) ? $res[$c['nomecampo']] : '';
				if($c['tipocampo'] == 'editor') {
					//elimino tags html
					$contenuto = strip_tags($contenuto);
				}
				
				foreach((array)$qRicerca as $qr) {
					if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
						if($idOggetto == $idOggettoModello) {
							
							//fix per i nomi di sezione degli organi politici
							if($res['id_sezione_etrasp'] == 702) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_sindaco'];
							} else if($res['id_sezione_etrasp'] == 792) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_vicesindaco'];
							} else if($res['id_sezione_etrasp'] == 703) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_giunta'];
							} else if($res['id_sezione_etrasp'] == 704) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_presidente'];
							} else if($res['id_sezione_etrasp'] == 705) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_consiglio'];
							} else if($res['id_sezione_etrasp'] == 706) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_direzione'];
							} else if($res['id_sezione_etrasp'] == 707) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_segretario'];
							} else if($res['id_sezione_etrasp'] == 708) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_commissioni'];
							} else if($res['id_sezione_etrasp'] == 796) {
								$sezioni[$res['id_sezione_etrasp']] = $tipoEnte['org_commissario'];
							}
							
							$items[$res['id_sezione_etrasp']] = array('titolo' => utf8_encode(html_entity_decode(trim($sezioni[$res['id_sezione_etrasp']]))), 'data' => $res['campo_data_ricerca']);
							$sezioniTrovate .= ",".$res['id_sezione_etrasp'];
						} else {
							$items[$res['id']] = array('titolo' => utf8_encode(html_entity_decode(trim($res['campoDefaultTitolo']))), 'data' => $res['campo_data_ricerca']);
						}
						$i = count($o->struttura); //per uscire dal ciclo più interno
					}
				}
			}
		}
		if($idOggetto == $idOggettoModello) {
			//potrebbero essere state esclui i risultati sul nome della sezione
			$contenuto = $res['nome_sez'];
			
			//fix per i nomi di sezione degli organi politici
			if($res['id_sezione_etrasp'] == 702) {
				$contenuto = $tipoEnte['org_sindaco'];
			} else if($res['id_sezione_etrasp'] == 792) {
				$contenuto = $tipoEnte['org_vicesindaco'];
			} else if($res['id_sezione_etrasp'] == 703) {
				$contenuto = $tipoEnte['org_giunta'];
			} else if($res['id_sezione_etrasp'] == 704) {
				$contenuto = $tipoEnte['org_presidente'];
			} else if($res['id_sezione_etrasp'] == 705) {
				$contenuto = $tipoEnte['org_consiglio'];
			} else if($res['id_sezione_etrasp'] == 706) {
				$contenuto = $tipoEnte['org_direzione'];
			} else if($res['id_sezione_etrasp'] == 707) {
				$contenuto = $tipoEnte['org_segretario'];
			} else if($res['id_sezione_etrasp'] == 708) {
				$contenuto = $tipoEnte['org_commissioni'];
			} else if($res['id_sezione_etrasp'] == 796) {
				$contenuto = $tipoEnte['org_commissario'];
			}
			
			foreach((array)$qRicerca as $qr) {
				if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				$items[$res['id_sezione_etrasp']] = array('titolo' => utf8_encode(html_entity_decode(trim($contenuto))), 'data' => $res['campo_data_ricerca']);
					$sezioniTrovate .= ",".$res['id_sezione_etrasp'];
				}
			}
		}
	}
	if($idOggetto == $idOggettoModello) {
		
		$arrayCondNome = array();
	
		//verificare anche eventuali sezioni che non hanno contenuto del 'modello di contenuto'
		$sql = "SELECT s.id AS id_sezione_etrasp, s.nome AS nome_sez, s.data_creazione AS campo_data_ricerca FROM ".$dati_db['prefisso']."sezioni s";
		$sql .= " WHERE s.ricercabile = 1 AND (s.permessi_lettura = 'HM' OR s.permessi_lettura = 'N/A') AND (";
		//$sql .= "(LOWER(s.nome) = LOWER('".$qr."') OR LOWER(s.nome) LIKE LOWER('".$qr."%') OR LOWER(s.nome) LIKE LOWER('% ".$qr."%') OR LOWER(s.nome) LIKE LOWER('%\'".$qr."%') ) ";
		//$sql .= " OR (LOWER(s.nome) = LOWER('".$qr."') OR LOWER(s.nome) LIKE LOWER('".$qr."%') OR LOWER(s.nome) LIKE LOWER('% ".$qr."%') OR LOWER(s.nome) LIKE LOWER('%\'".$qr."%') ) ";
		
		foreach((array)$qRicerca as $qr) {
			if(strlen(stripslashes($qr)) >= $minChars) {
				$arrayCondNome[] = "(LOWER(s.nome) = LOWER('".$qr."') OR LOWER(s.nome) LIKE LOWER('".$qr."%') OR LOWER(s.nome) LIKE LOWER('% ".$qr."%') OR LOWER(s.nome) LIKE LOWER('%\'".$qr."%') ) ";
			}
			//fix per i nomi di sezione degli organi politici
			if(stripos($tipoEnte['org_sindaco'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 702 ) ";
			} else if(stripos($tipoEnte['org_vicesindaco'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 792 ) ";
			} else if(stripos($tipoEnte['org_giunta'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 703 ) ";
			} else if(stripos($tipoEnte['org_presidente'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 704 ) ";
			} else if(stripos($tipoEnte['org_consiglio'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 705 ) ";
			} else if(stripos($tipoEnte['org_direzione'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 706 ) ";
			} else if(stripos($tipoEnte['org_segretario'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 707 ) ";
			} else if(stripos($tipoEnte['org_commissioni'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 708 ) ";
			} else if(stripos($tipoEnte['org_commissario'], $qr) !== false) {
				$arrayCondNome[] = "(s.id = 796 ) ";
			}
		}
		
		$sql .= implode(' OR ', $arrayCondNome);
		
		$sql .=") AND id NOT IN (".$sezioniTrovate.") ORDER BY s.nome ".$condizioneLimite;
		
		if (!($result = $database -> connessioneConReturn($sql))) {
			$composizione = array();
		} else {
			$composizione = $database -> sqlArrayAss($result);
		}
		
		foreach((array)$composizione as $res) {
			$contenuto = $res['nome_sez'];
			
			//fix per i nomi di sezione degli organi politici
			if($res['id_sezione_etrasp'] == 702) {
				$contenuto = $tipoEnte['org_sindaco'];
			} else if($res['id_sezione_etrasp'] == 792) {
				$contenuto = $tipoEnte['org_vicesindaco'];
			} else if($res['id_sezione_etrasp'] == 703) {
				$contenuto = $tipoEnte['org_giunta'];
			} else if($res['id_sezione_etrasp'] == 704) {
				$contenuto = $tipoEnte['org_presidente'];
			} else if($res['id_sezione_etrasp'] == 705) {
				$contenuto = $tipoEnte['org_consiglio'];
			} else if($res['id_sezione_etrasp'] == 706) {
				$contenuto = $tipoEnte['org_direzione'];
			} else if($res['id_sezione_etrasp'] == 707) {
				$contenuto = $tipoEnte['org_segretario'];
			} else if($res['id_sezione_etrasp'] == 708) {
				$contenuto = $tipoEnte['org_commissioni'];
			} else if($res['id_sezione_etrasp'] == 796) {
				$contenuto = $tipoEnte['org_commissario'];
			}
			foreach((array)$qRicerca as $qr) {
				if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
					$items[$res['id_sezione_etrasp']] = array('titolo' => utf8_encode(html_entity_decode(trim($contenuto))), 'data' => $res['campo_data_ricerca']);
				}
			}
		}
	}
	
	return $items;
}

// metodo di caricamento delle regole di pubblicazione data le sezione, se le regole non sono settate, restituisce false
function loadingOggettoCriterio($id) {
	global $dati_db,$database, $configurazione;
	
	$sql = "SELECT * FROM ".$dati_db['prefisso']."regole_pubblicazione_oggetti_criteri WHERE id=".$id;
	if( !($result = $database->connessioneConReturn($sql)) ) {
		die("non riesco a prendere il criterio in db: ".$sql);
	}
	$regola=$database->sqlArray($result);
	if (is_array($regola)) {
		return $regola;			
	} else {
		return FALSE;
	}
}

function getNomeOggetto($idOggetto) {
	global $oggetti, $idOggettoModello;
	
	foreach((array)$oggetti as $o) {
		if($o['id'] == $idOggetto) {
			if($idOggetto == $idOggettoModello) {
				return 'Pagine del sito';
			} else {
				return $o['nome'];
			}
		}
	}
}
function loadingSezioni() {
	global $dati_db,$database, $configurazione;
	
	$sql = "SELECT id,nome FROM ".$dati_db['prefisso']."sezioni";
	if( !($result = $database->connessioneConReturn($sql)) ) {
		die("non riesco a prendere le sezioni: ".$sql);
	}
	$sez = $database->sqlArrayAss($result);
	$sezioni = array();
	foreach((array)$sez as $s) {
		$sezioni[$s['id']] = $s['nome'];
	}
	return $sezioni;
}
function caricaDocumentiCriterio($idEnte, $idOggetto, $idCriterio, $numero) {
	global $dati_db,$database, $configurazione;
	$criterio = loadingOggettoCriterio($idCriterio);
	require_once('../classi/documento.php');
	$o = new documento($idOggetto,'si');
	$listaDocumenti = $o->caricaDocumentiCriterio($criterio,$numero);
	return $listaDocumenti;
}
function cercaGruppi($idUtente, $idOgg) {
	global $database,$dati_db;
    
    if($idUtente == '') {
    	$idUtente = -1;
    }
	$sql = "SELECT g.*, gc.id_gruppo FROM ".$dati_db['prefisso']."oggetti_gruppi g, ".$dati_db['prefisso']."oggetti_gruppi_composizione gc WHERE g.id = gc.id_gruppo AND gc.id_oggetto = ".$idOgg." AND gc.id_documento = ".$idUtente;
	if( !($result = $database->connessioneConReturn($sql)) ) {
		die("non riesco a prendere le informazioni sui gruppi in db".$sql);
	}
	if ($database->sqlNumRighe($result) != 0) {
		$gruppi=$database->sqlArrayAss($result);
	} else {
		$gruppi=array();
	}
	return $gruppi;
}
// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function datoEnte($idEnte = 0, $campo='') {
	global $dati_db,$database;
	
	if ($idEnte != 0) {
	
		if ($campo=='') {
			$campo='*';
		}

		$sql = "SELECT ".$campo." FROM ".$dati_db['prefisso']."etrasp_enti WHERE id=".$idEnte;
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero del dato sugli enti'.$sql);
		}
		$riga = $database->sqlArray($result);
		
		if ($campo=='*') {
			//prelevo anche i moduli attivi
			$sql = "SELECT modulo,attivo FROM ".$dati_db['prefisso']."etrasp_moduli WHERE id_ente=".$idEnte;
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero del moduli ente '.$sql);
			}
			$moduli = array();
			$mod = $database->sqlArrayAss($result);
			foreach((array)$mod as $m) {
				if($m['attivo'] > 0) {
					$moduli[$m['modulo']] = 1;
				}
			}
			$riga['moduli_attivi'] = $moduli;
			return $riga;
		} else {
			return $riga[$campo];
		}

	} 

	return FALSE;
}
function datoTipoEnte($idTipo = 0, $campo = '') {
	global $dati_db,$database;
	
	if ($idTipo != 0) {
	
		if ($campo=='') {
			$campo='*';
		}

		$sql = "SELECT ".$campo." FROM ".$dati_db['prefisso']."oggetto_etrasp_tipoentisemplice WHERE id=".$idTipo;
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero del dato sul tipo di enti'.$sql);
		}
		$riga = $database->sqlArray($result);
		
		if ($campo=='*') {
			return $riga;
		} else {
			return $riga[$campo];
		}

	} 

	return FALSE;
}
?>