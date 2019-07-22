<?
/*
$minChars = 3;
$condizioneLimite = ' LIMIT 0,3';
if ($numero != 0) {
	$condizioneLimite = ' LIMIT 0,'.($numero + 1);
}

$condizioneAuth = ' AND stato = 1 ';

//workflow
if ($o->idOggetto > 1) {
	if(!$configurazione['includi_istanze_workflow']) {
		$condizioneAuth .= ' AND stato_workflow=\'finale\' ';
	}
}

//la ricerca la faccio su ogni parola: ricerca in OR
$qRicerca = explode(' ',$q);
//ricerca su ogni campo ricercabile di tipo testo (string,blob,text)
$arrayCampiRicerca = array();
$arrayCampiSelect = array();
foreach((array)$o->struttura as $c) {
	if($c['ordinamento'] and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text') and in_array($c['nomecampo'],$o->campiRicerca)) {
		$arrayCampiSelect[] = $c['nomecampo'];
		//ricerca su ogni parola inserita
		foreach((array)$qRicerca as $qr) {
			if(strlen(stripslashes($qr)) >= $minChars) {
				$arrayCampiRicerca[] = "(".$c['nomecampo']." = '".$qr."' OR ".$c['nomecampo']." LIKE '".$qr."%' OR ".$c['nomecampo']." LIKE '% ".$qr."%' ) ";
			}
		}
	}
}
$campiRicerca = implode(' AND ', $arrayCampiRicerca);
$campiSelect = implode(',',$arrayCampiSelect);

$sql = "SELECT id, ".$campo." AS campoDefaultTitolo, ".$campiSelect." FROM ".$dati_db['prefisso'].$o->tabella." WHERE permessi_lettura = 'N/A' ".$condizioneAuth." AND id_ente = ".$idEnte." AND (";
$sql .= $campiRicerca;
$sql .=") ";
$sql .=" ".$condizioniAggiuntive." ";
if($ordinamentoPersonalizzato != '') {
	$sql .=" ".$ordinamentoPersonalizzato." ";
} else {
	$sql .=" ORDER BY campoDefaultTitolo ";
}
$sql .=$condizioneLimite;

if (!($result = $database -> connessioneConReturn($sql))) {
	$composizione = array();
} else {
	$composizione = $database -> sqlArrayAss($result);
}
*/

$composizione = array();
if ($database->sqlNumRighe($risultato) != 0) {
	$composizione = $database->sqlArrayAss($risultato);
}

$items = array();
$checkItems = array();

/*
foreach((array)$composizione as $res) {
	//per ogni oggetto devo pulire i campi che possono contenere html
	// e quindi verificare se a quel punto la ricerca è andata a buon fine
	for($i = 0; $i < count($o->struttura); $i++) {
		$c = $o->struttura[$i];
		//lognormale('verifico campo '.$c['nomecampo']);
		if($c['ordinamento'] and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text')) {
			//ho un campo su cui è stata effettuata la ricerca
			$contenuto = $res[$c['nomecampo']];
			if($c['tipocampo'] == 'editor') {
				//elimino tags html
				$contenuto = strip_tags($contenuto);
			}
			foreach((array)$qRicerca as $qr) {
				if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
					$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
					$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
					$i = count($o->struttura); //per uscire dal ciclo più interno
				}
			}
		}
	}
}
*/
foreach((array)$composizione as $res) {
	$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
	$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
}

if(count($items) > 0) {
	$response[] = array(
		'label' => $oggetto['nome'],
		'objName' => 1
	);
}
$i = 0;
if($tipoRicercaAuto == 'admin') {
	if (!$aclTrasparenza[$menuSecondario]['modifica'] AND !$aclTrasparenza[$menuSecondario]['creazione']) {
		$permessiModifica = false;
	} else {
		$permessiModifica = true;
	}
}
foreach ((array)$items as $key=>$value) {
	if($i < $numero) {
		$urlLC = $base_url."archivio".$oggetto['id']."_".pulisciNome($o->nome)."_0_".$key.".html";
		if($tipoRicercaAuto == 'admin' and $permessiModifica) {
			if($oggetto['id'] == 11 and moduloAttivo('bandigara')) {
				$tipo = mostraDatoOggetto($key, 11, 'tipologia');
				$value = '['.$tipo.'] '.$value;
				switch($tipo) {
					//bandi ed inviti,esiti,delibere e determine a contrarre,affidamenti,avvisi pubblici,somme liquidate
					case 'bandi ed inviti':
						$link = '&tipo=bando';
					break;
					case 'lotto':
						$link = '&tipo=lotto';
					break;
					case 'esiti':
						$link = '&tipo=esito';
					break;
					case 'delibere e determine a contrarre':
						$link = '&tipo=delibera';
					break;
					case 'affidamenti':
						$link = '&tipo=affidamento';
					break;
					case 'avvisi pubblici':
						$link = '&tipo=avviso';
					break;
					case 'somme liquidate':
						$link = '&tipo=liquidazione';
					break;
				}
				$sottotipo = mostraDatoOggetto($key, 11, 'sottotipo');
				if($sottotipo != '') {
					$link .= '&sottotipo='.$sottotipo;
				}
				$urlLC = "admin__pat.php?menu=".$menu."&menusec=".$menuSecondario."&azione=modifica&id=".$key.$link;
			} else {
				$urlLC = "admin__pat.php?menu=".$menu."&menusec=".$menuSecondario."&azione=modifica&id=".$key;
			}
		}
		/*
		Per il momento commento su segnalazione di frosinone: se si cerca 'scimè' la 'è' finale viene visualizzata male
		Stessa cosa per altri oggetti: verificare se funziona nel tempo
		Questa modifica è stata fatta in data 05/11/2015
		Vedi anche ajax_personalizzato.php
		*/
		/*
		$response[] = array(
			'label' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
			'value' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
			'url' => $urlLC
		);
		*/
		$response[] = array(
			'label' => utf8_encode(htmlentities(tagliaContHtml($value, $limitaCaratteriOutput))),
			'value' => utf8_encode(htmlentities(tagliaContHtml($value, $limitaCaratteriOutput))),
			'url' => $urlLC
		);
	}
	$i++;
}

$urlVediTutti = $base_url."index.php?azione=cerca&amp;ricfil=2&amp;obiettivo=".$oggetto['id']."&amp;id_sezione=".$sezioneNavigazione['id']."&amp;ordineRis=&amp;sezioneRicerca=0&amp;oggettiRicerca=&amp;tipo=".$oggetto['id']."&amp;strcerca=".$q;
if(count($items) >= 3 and $tipoRicercaAuto != 'admin') {
	$response[] = array(
		'label' => 'Vedi tutti i risultati...',
		'objName' => 2,
		'url' => $urlVediTutti
	);
}
?>