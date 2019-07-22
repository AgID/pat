<?
$tabella = 'sezioni';
$campo = 'nome';
$campiRicercabili = array('nome', 'descrizione', 'title_code', 'h1_code', 'h2_code');
$qRicerca = explode(' ',$q);
$limite = 20;

$minChars = $_GET['minChars'];
if($minChars == 0) {
	$minChars = 3;
}

$arrayCampiRicerca = array();
$arrayCampiSelect = array();
foreach((array)$campiRicercabili as $c) {
	$arrayCampiSelect[] = $c;
	//ricerca su ogni parola inserita
	foreach((array)$qRicerca as $qr) {
		if(strlen(stripslashes($qr)) >= $minChars) {
			$arrayCampiRicerca[] = "(".$c." = '".$qr."' OR ".$c." SOUNDS LIKE '".$qr."%' OR ".$c." SOUNDS LIKE '% ".$qr."%' OR ".$c." LIKE '".$qr."%' OR ".$c." LIKE '% ".$qr."%' ) ";
		}
	}
}
$campiRicerca = implode(' OR ', $arrayCampiRicerca);
$campiSelect = implode(',',$arrayCampiSelect);

$sql = "SELECT id, ".$campo." AS campoDefaultTitolo, ".$campiSelect." FROM ".$dati_db['prefisso'].$tabella." WHERE (permessi_lettura = 'N/A' OR permessi_lettura = 'HM') AND (";
$sql .= $campiRicerca;
$sql .=") ORDER BY ".$campo." LIMIT ".$limite;

if (!($result = $database -> connessioneConReturn($sql))) {
	$composizione = array();
} else {
	$composizione = $database -> sqlArrayAss($result);
}
/*
$f = fopen('temp/testSezioni.txt', 'a+');
fwrite($f, $sql."\n\r");
fclose($f);
*/
$items = array();
$checkItems = array();
foreach((array)$composizione as $res) {
	//per ogni oggetto devo pulire i campi che possono contenere html
	// e quindi verificare se a quel punto la ricerca è andata a buon fine
	for($i = 0; $i < count($campiRicercabili); $i++) {
		$c = $campiRicercabili[$i];
		$contenuto = $res[$c];
		foreach((array)$qRicerca as $qr) {
			/*
			if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
				$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
				$i = count($campiRicercabili); //per uscire dal ciclo più interno
			}
			*/
			if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
				$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
				$i = count($campiRicercabili); //per uscire dal ciclo più interno
			} else {
				//beta assonanza
				$items[$res['id']] = "<b>Forse cercavi</b> ".html_entity_decode(trim($res['campoDefaultTitolo']));
				$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
				$i = count($campiRicercabili); //per uscire dal ciclo più interno
			}
			
		}
	}
}

//HO CERCATO SUI CAMPI DELLA TABELLA SEZIONI, ORA DEVO CERCARE SU PARAGRAFI E AREE DI TESTO
$arrayCampiRicerca = array();
foreach((array)$qRicerca as $qr) {
	if(strlen(stripslashes($qr)) >= $minChars) {
		$arrayCampiRicerca[] = "(par.contenuto = '".$qr."' OR par.contenuto LIKE '".$qr."%' OR par.contenuto LIKE '% ".$qr."%' ) ";
	}
}
$condizioneCont = implode(' OR ', $arrayCampiRicerca);
// CERCO TUTTE LE REGOLE DI CONTENUTO IN CUI ESEGUIRE LA QUERY DI RICERCA
$sql = "SELECT id_elemento FROM ".$dati_db['prefisso']."regole_pubblicazione WHERE id_sezione!=-1 AND id_elemento!=0 AND (tipo_elemento='paragrafo' OR tipo_elemento='area_testo')";
if( !($risultato = $database->connessioneConReturn($sql)) ) {
	//niente
}
if ($database->sqlNumRighe($risultato) != 0) {
	$risultatiRegole = $database->sqlArrayAss($risultato);
	$condizioneEle = " AND (";
	foreach ($risultatiRegole as $elemento) {
		if ($condizioneEle != " AND (") {
			$condizioneEle .= " OR ";
		}
		$condizioneEle .= "par.id=".$elemento['id_elemento'];
	}   
	$condizioneEle .= ")";
}   		
$condizione = " ( ".$condizioneCont." ) ".$condizioneEle;
	
// creo la query per l'oggetto in esame
$sql = "SELECT par.id, reg.id_sezione, reg.id_sezione AS idSezioneCheck FROM ".$dati_db['prefisso']."oggetto_paragrafo par, ".$dati_db['prefisso']."regole_pubblicazione reg WHERE 
	par.id=reg.id_elemento AND par.stato=1
	AND (reg.tipo_elemento='paragrafo' OR reg.tipo_elemento='area_testo')
	AND ".$condizione;
if( !($risultato = $database->connessioneConReturn($sql)) ) {
	//niente
}   

if ($database->sqlNumRighe($risultato) != 0) {
	$risultatiTempParagrafo = $database->sqlArrayAss($risultato);
	/*
	foreach((array)$risultatiTempPar as $ris) {
		if(!isset($items[$ris['idSezioneCheck']) {
			$items[$ris['idSezioneCheck']] = html_entity_decode(trim('nome sezione'));
		}
	}
	*/
	foreach((array)$risultatiTempParagrafo as $ris) {
		$permessiLettura = nomeSezDaId($ris['idSezioneCheck'],'permessi_lettura');
		if ($permessiLettura == 'N/A' or $permessiLettura == 'HM') {
			// inserisco la sezione nei risultati solo se è visibile
			if(!isset($items[$ris['idSezioneCheck']])) {
				$items[$ris['idSezioneCheck']] = html_entity_decode(trim(nomeSezDaId($ris['idSezioneCheck'])));
			}
		}
	}
}

if(count($items) > 0) {
	$output .= "|<div class=\"".$classeTitolo."\">Pagine del sito</div>|t\n";
}
foreach ((array)$items as $key=>$value) {
	foreach($tipoEnte['traduzioni_organi'] as $trad) {
		if($key == $trad['id']) {
			$value = $trad['nome'];
		}
	}
	/*
	if ($key==702) {
		$value = $tipoEnte['org_sindaco'];			
	}
	if ($key==703) {
		$value = $tipoEnte['org_giunta'];			
	}							
	if ($key==704) {
		$value = $tipoEnte['org_presidente'];	
	}
	if ($key==705) {
		$value = $tipoEnte['org_consiglio'];			
	}
	if ($key==706) {
		$value = $tipoEnte['org_direzione'];			
	}
	if ($key==707) {
		$value = $tipoEnte['org_segretario'];				
	}	
	if ($key==708) {
		$value = $tipoEnte['org_commissioni'];			
	}
	if ($key==792) {
		$value = $tipoEnte['org_vicesindaco'];
	}
	if ($key==793) {
		$value = $tipoEnte['org_gruppi_consiliari'];
	}				
	if ($key==796) {
		$value = $tipoEnte['org_commissario'];
	}
	if ($key==809) {
		$value = $tipoEnte['org_ass_sindaci'];
	}
	if ($key==810) {
		$value = $tipoEnte['org_sub_commissario'];
	}
	if ($key==827) {
		$value = $tipoEnte['org_comitato_esecutivo'];
	}
	if ($key==828) {
		$value = $tipoEnte['org_consiglio_sportivo_nazionale'];
	}
	if ($key==829) {
		$value = $tipoEnte['org_giunta_sportiva'];
	}
	*/
	$output .= "pagina".$key."_".pulisciNome($value).".html|$value|e\n";
}
?>