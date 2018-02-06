<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015,2017 - AgID Agenzia per l'Italia Digitale
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
	 * codicepers/ricercaModelloContenuto.php
	 * 
	 * @Descrizione
	 * Utility per la ricerca sui contenuti del modello di pagina di ISWEB nella sua implementazione PAT
	 *
	 */

$qRicerca = explode(' ',$q);
$limite = $numero;

$minChars = $_GET['minChars'];
if($minChars == 0) {
	$minChars = 3;
}

$sezioniTrovate = "-1";
$arrayCampiRicerca = array();
$arrayCampiSelect = array();
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
foreach((array)$o->struttura as $c) {
	if($c['ordinamento'] and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text') and in_array($c['nomecampo'],$o->campiRicerca)) {
		$arrayCampiSelect[] = 'og.'.$c['nomecampo'];
		//ricerca su ogni parola inserita
		foreach((array)$qRicerca as $qr) {
			if(strlen(stripslashes($qr)) >= $minChars) {
				$arrayCampiRicerca[] = "(LOWER(og.".$c['nomecampo'].") = LOWER('".$qr."') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('".$qr."%') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('% ".$qr."%') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('%\'".$qr."%') OR LOWER(og.".$c['nomecampo'].") LIKE LOWER('%>".$qr."%') ) ";
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
$sql = "SELECT s.nome AS nome_sez, og.id, og.".$campo." AS campoDefaultTitolo, ".$campiSelect." FROM ".$dati_db['prefisso']."sezioni s INNER JOIN ".$dati_db['prefisso'].$o->tabella." og ON s.id = og.id_sezione_etrasp";
$sql .= " WHERE og.permessi_lettura != 'H' AND (og.id_lingua=".$o->id_lingua." or og.id_lingua=0) "." AND og.".$campoIdEnte." = ".$idEnte." AND (";
$sql .= $campiRicerca;
$sql .=") ".$condizioneCriterio." ORDER BY s.nome ".$condizioneLimite;
		
if (!($result = $database -> connessioneConReturn($sql))) {
	$composizione = array();
} else {
	$composizione = $database -> sqlArrayAss($result);
}
/*
$f = fopen('temp/queryModello.html','a+');
fwrite($f, $sql.'<br /><br />');
fclose($f);
*/
$items = array();
foreach((array)$composizione as $res) {
	//per ogni oggetto devo pulire i campi che possono contenere html
	// e quindi verificare se a quel punto la ricerca � andata a buon fine
	for($i = 0; $i < count($o->struttura); $i++) {
		$c = $o->struttura[$i];
		if($c['ordinamento'] and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text')) {
			//ho un campo su cui � stata effettuata la ricerca
			$contenuto = $res[$c['nomecampo']];
			if($c['tipocampo'] == 'editor') {
				//elimino tags html
				$contenuto = strip_tags($contenuto);
			}
			
			foreach((array)$qRicerca as $qr) {
				if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				
					//fix per i nomi di sezione degli organi politici
					if($res['id_sezione_etrasp'] == 702) {
						$res['nome_sez'] = $tipoEnte['org_sindaco'];
					} else if($res['id_sezione_etrasp'] == 792) {
						$res['nome_sez'] = $tipoEnte['org_vicesindaco'];
					} else if($res['id_sezione_etrasp'] == 703) {
						$res['nome_sez'] = $tipoEnte['org_giunta'];
					} else if($res['id_sezione_etrasp'] == 704) {
						$res['nome_sez'] = $tipoEnte['org_presidente'];
					} else if($res['id_sezione_etrasp'] == 705) {
						$res['nome_sez'] = $tipoEnte['org_consiglio'];
					} else if($res['id_sezione_etrasp'] == 706) {
						$res['nome_sez'] = $tipoEnte['org_direzione'];
					} else if($res['id_sezione_etrasp'] == 707) {
						$res['nome_sez'] = $tipoEnte['org_segretario'];
					} else if($res['id_sezione_etrasp'] == 708) {
						$res['nome_sez'] = $tipoEnte['org_commissioni'];
					} else if($res['id_sezione_etrasp'] == 796) {
						$res['nome_sez'] = $tipoEnte['org_commissario'];
					}
				
					$items[$res['id_sezione_etrasp']] = utf8_encode((trim($res['nome_sez'])));
					$sezioniTrovate .= ",".$res['id_sezione_etrasp'];
					$i = count($o->struttura); //per uscire dal ciclo pi� interno
				}
			}
		}
	}
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
			$items[$res['id_sezione_etrasp']] = utf8_encode((trim($contenuto)));
		}
	}
}
//verificare anche eventuali sezioni che non hanno contenuto del 'modello di contenuto'
$arrayCampiRicerca = array();
foreach((array)$qRicerca as $qr) {
	if(strlen(stripslashes($qr)) >= $minChars) {
		$arrayCampiRicerca[] = "(LOWER(s.nome) = LOWER('".$qr."') OR LOWER(s.nome) LIKE LOWER('".$qr."%') OR LOWER(s.nome) LIKE LOWER('% ".$qr."%') OR LOWER(s.nome) LIKE LOWER('%\'".$qr."%') ) ";
	}
	//fix per i nomi di sezione degli organi politici
	if(stripos($tipoEnte['org_sindaco'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 702 ) ";
	} else if(stripos($tipoEnte['org_vicesindaco'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 792 ) ";
	} else if(stripos($tipoEnte['org_giunta'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 703 ) ";
	} else if(stripos($tipoEnte['org_presidente'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 704 ) ";
	} else if(stripos($tipoEnte['org_consiglio'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 705 ) ";
	} else if(stripos($tipoEnte['org_direzione'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 706 ) ";
	} else if(stripos($tipoEnte['org_segretario'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 707 ) ";
	} else if(stripos($tipoEnte['org_commissioni'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 708 ) ";
	} else if(stripos($tipoEnte['org_commissario'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 796 ) ";
	}
}
$campiRicerca = implode(' OR ', $arrayCampiRicerca);

$sql = "SELECT s.id AS id_sezione_etrasp, s.nome AS nome_sez FROM ".$dati_db['prefisso']."sezioni s";
$sql .= " WHERE s.ricercabile = 1 AND (s.permessi_lettura = 'HM' OR s.permessi_lettura = 'N/A') AND (";
$sql .= $campiRicerca;
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
			$items[$res['id_sezione_etrasp']] = utf8_encode((trim($contenuto)));
		}
	}
}

if(count($items) > 0) {
	$response[] = array(
		'label' => $nomeOggetto,
		'objName' => 1
	);
}
$i = 0;
foreach ((array)$items as $key=>$value) {
	if($i < $numero) {
		$response[] = array(
			'label' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
			'value' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
			'url' => "pagina".$key."_".pulisciNome(nomeSezDaId($key)).".html"
		);
	}
	$i++;
}
/*
if(count($items) > $numero) {
	$response[] = array(
		'label' => 'Vedi tutti i risultati',
		'objName' => 2,
		'url' => "pagina0_home.html"
	);
}
*/
?>