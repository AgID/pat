<?
$arrayRis = array();

$arrayModelli = array();
$numRisTemp = count($risultatiTemp);
for($i=0;$i<$numRisTemp;$i++) {
	//verifico le sole sezioni
	if(!$risultatiTemp[$i]['id_modello_pagina']) {
		if ($risultatiTemp[$i]['id_sezione'] != $risultatiTemp[$i+1]['id_sezione'] or $risultatiTemp[$i]['tipologia'] != $risultatiTemp[$i+1]['tipologia']) {
			$risTemp = array('id' => $oggetto['id'], 'ogg' => $risultatiTemp[$i]['id_sezione'], 'tipo' => $risultatiTemp[$i]['tipologia']);
			$arrayRis[] = $risTemp;
		}
	} else {
		$arrayModelli[] = $risultatiTemp[$i];
	}
}

for($i=0;$i<count($arrayModelli);$i++) {
	//sezioni personalizzate
	$risTemp = array('id' => $oggetto['id'], 'ogg' => $arrayModelli[$i]['id_sezione'], 'tipo' => $arrayModelli[$i]['tipologia']);
	$risTemp['id_modello_pagina'] = $arrayModelli[$i]['id_modello_pagina'];
	$risTemp['titolo'] = $arrayModelli[$i]['titolo'];
	$arrayRis[] = $risTemp;
}

if(count($arrayRis) > 0) {
	$response[] = array(
			'label' => 'Elemento contenuto',
			'objName' => 1
	);
}

for ($i=0;$i<6 and $i<count($arrayRis);$i++) {
	
	$sezId = $arrayRis[$i]['ogg'];
	$linkSez = nomeSezDaId($sezId, 'link');
	if ($linkSez == '') {
		$nome = $nome = nomeSezDaId($sezId, 'nome');
		$strAncora = $base_url."index.php?id_sezione=" . $sezId;
		if($arrayRis[$i]['tipo'] == 'pagina') {
			$strAncora = $base_url."contenuto".$arrayRis[$i]['id_modello_pagina']."_pagina_".$sezId.".html";
			$nome = $arrayRis[$i]['titolo'];
		}
	} else {
		$nome = nomeSezDaId($sezId, 'nome');
		$strAncora = $linkSez;
	}
	
	
	$response[] = array(
			'label' => utf8_encode(tagliaContHtml($nome, $limitaCaratteriOutput)),
			'value' => utf8_encode(tagliaContHtml($nome, $limitaCaratteriOutput)),
			'url' => $strAncora
	);
	
}

if(count($arrayRis) >= 6 and $tipoRicercaAuto != 'admin') {
	$urlVediTutti = $base_url."index.php?azione=cerca&amp;ricfil=2&amp;obiettivo=".$oggetto['id']."&amp;id_sezione=".$sezioneNavigazione['id']."&amp;ordineRis=&amp;sezioneRicerca=0&amp;oggettiRicerca=&amp;tipo=".$oggetto['id']."&amp;strcerca=".$q;
	$response[] = array(
			'label' => 'Vedi tutti i risultati...',
			'objName' => 2,
			'url' => $urlVediTutti
	);
}

/*
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
	foreach($tipoEnte['traduzioni_organi'] as $trad) {
		if(strpos(strtolower($trad['nome']), strtolower($qr)) !== false) {
			$arrayCampiRicerca[] = "(og.id_sezione_etrasp = ".$trad['id']." ) ";
		}
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
$sql = "SELECT s.nome AS nome_sez, og.id, og.".$campo." AS campoDefaultTitolo, og.tipologia, og.titolo, og.id AS id_modello_pagina, ".$campiSelect." FROM ".$dati_db['prefisso']."sezioni s INNER JOIN ".$dati_db['prefisso'].$o->tabella." og ON s.id = og.id_sezione_etrasp";
$sql .= " WHERE og.permessi_lettura != 'H' AND (og.id_lingua=".$o->id_lingua." or og.id_lingua=0) "." AND og.".$campoIdEnte." = ".$idEnte." AND (";
$sql .= $campiRicerca;
$sql .=") ";
if(!moduloAttivo('accessocivico') and !moduloAttivo('solo_accessocivico')) {
	$sql .= " AND s.id NOT IN (".implode(',', $configurazione['sezioni_escludi_no_accessocivico']).") ";
}
$sql .= $condizioneCriterio." ORDER BY s.nome ".$condizioneLimite;

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
		if($c['ordinamento'] and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text')) {
			//ho un campo su cui è stata effettuata la ricerca
			$contenuto = $res[$c['nomecampo']];
			if($c['tipocampo'] == 'editor') {
				//elimino tags html
				$contenuto = strip_tags($contenuto);
			}
			
			foreach((array)$qRicerca as $qr) {
				if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				
					//fix per i nomi di sezione degli organi politici
					foreach($tipoEnte['traduzioni_organi'] as $trad) {
						if($res['id_sezione_etrasp'] == $trad['id']) {
							$res['nome_sez'] = $trad['nome'];
						}
					}
					
					$items[$res['id_sezione_etrasp']] = utf8_encode((trim($res['nome_sez'])));
					if($res['tipologia'] == 'pagina') {
						$items[$res['id_sezione_etrasp']] = array();
						$items[$res['id_sezione_etrasp']]['tipologia'] = 'pagina';
						$items[$res['id_sezione_etrasp']]['titolo'] = $res['titolo'];
						$items[$res['id_sezione_etrasp']]['id_modello_pagina'] = $res['id_modello_pagina'];
					}
					$sezioniTrovate .= ",".$res['id_sezione_etrasp'];
					$i = count($o->struttura); //per uscire dal ciclo più interno
				}
			}
		}
	}
	//potrebbero essere state esclui i risultati sul nome della sezione
	$contenuto = $res['nome_sez'];
	
	//fix per i nomi di sezione degli organi politici
	if($res['id_sezione_etrasp'] == 702) {
		$contenuto = $tipoEnte['org_sindaco'];
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
	} else if($res['id_sezione_etrasp'] == 792) {
		$contenuto = $tipoEnte['org_vicesindaco'];
	} else if($res['id_sezione_etrasp'] == 793) {
		$contenuto = $tipoEnte['org_gruppi_consiliari'];
	} else if($res['id_sezione_etrasp'] == 796) {
		$contenuto = $tipoEnte['org_commissario'];
	} else if($res['id_sezione_etrasp'] == 809) {
		$contenuto = $tipoEnte['org_ass_sindaci'];
	} else if($res['id_sezione_etrasp'] == 810) {
		$contenuto = $tipoEnte['org_sub_commissario'];
	} else if($res['id_sezione_etrasp'] == 827) {
		$contenuto = $tipoEnte['org_comitato_esecutivo'];
	} else if($res['id_sezione_etrasp'] == 828) {
		$contenuto = $tipoEnte['org_consiglio_sportivo_nazionale'];
	} else if($res['id_sezione_etrasp'] == 829) {
		$contenuto = $tipoEnte['org_giunta_sportiva'];
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
	} else if(stripos($tipoEnte['org_vicesindaco'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 792 ) ";
	} else if(stripos($tipoEnte['org_gruppi_consiliari'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 793 ) ";
	} else if(stripos($tipoEnte['org_commissario'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 796 ) ";
	} else if(stripos($tipoEnte['org_ass_sindaci'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 809 ) ";
	} else if(stripos($tipoEnte['org_sub_commissario'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 810 ) ";
	} else if(stripos($tipoEnte['org_comitato_esecutivo'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 827 ) ";
	} else if(stripos($tipoEnte['org_consiglio_sportivo_nazionale'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 828 ) ";
	} else if(stripos($tipoEnte['org_giunta_sportiva'], $qr) !== false) {
		$arrayCampiRicerca[] = "(s.id = 829 ) ";
	}
}
$campiRicerca = implode(' OR ', $arrayCampiRicerca);

$sql = "SELECT s.id AS id_sezione_etrasp, s.nome AS nome_sez FROM ".$dati_db['prefisso']."sezioni s";
$sql .= " WHERE s.ricercabile = 1 AND (s.permessi_lettura = 'HM' OR s.permessi_lettura = 'N/A') AND (";
$sql .= $campiRicerca;
$sql .=") ";
if(!moduloAttivo('accessocivico') and !moduloAttivo('solo_accessocivico')) {
	$sql .= " AND id_sezione_etrasp NOT IN (".implode(',', $configurazione['sezioni_escludi_no_accessocivico']).") ";
}
$sql .= " AND id NOT IN (".$sezioniTrovate.") ORDER BY s.nome ".$condizioneLimite;

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
	} else if($res['id_sezione_etrasp'] == 792) {
		$contenuto = $tipoEnte['org_vicesindaco'];
	} else if($res['id_sezione_etrasp'] == 793) {
		$contenuto = $tipoEnte['org_gruppi_consiliari'];
	} else if($res['id_sezione_etrasp'] == 796) {
		$contenuto = $tipoEnte['org_commissario'];
	} else if($res['id_sezione_etrasp'] == 809) {
		$contenuto = $tipoEnte['org_ass_sindaci'];
	} else if($res['id_sezione_etrasp'] == 810) {
		$contenuto = $tipoEnte['org_sub_commissario'];
	} else if($res['id_sezione_etrasp'] == 827) {
		$contenuto = $tipoEnte['org_comitato_esecutivo'];
	} else if($res['id_sezione_etrasp'] == 828) {
		$contenuto = $tipoEnte['org_consiglio_sportivo_nazionale'];
	} else if($res['id_sezione_etrasp'] == 829) {
		$contenuto = $tipoEnte['org_giunta_sportiva'];
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
		$includi = 1;
		$sezioniEscludo = explode(",",$tipoEnte['sezioni_esclusione']);				
		if (in_array($key, $sezioniEscludo)) {
			$includi = 0;
		}
		if($includi) {
			if(is_array($value) and $value['tipologia'] == 'pagina') {
				$strAncora = "contenuto".$value['id_modello_pagina']."_".pulisciNome($value['titolo'])."_".$key.".html";
				$response[] = array(
						'label' => utf8_encode(tagliaContHtml($value['titolo'], $limitaCaratteriOutput)),
						'value' => utf8_encode(tagliaContHtml($value['titolo'], $limitaCaratteriOutput)),
						'url' => $strAncora
				);
			} else {
				$response[] = array(
					'label' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
					'value' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
					'url' => "pagina".$key."_".pulisciNome($value).".html"
				);
			}
		}
	}
	$i++;
}
*/
?>