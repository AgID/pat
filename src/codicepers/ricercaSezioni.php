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
	 * codicepers/ricercaSezioni.php
	 * 
	 * @Descrizione
	 * Utility per la ricerca sui contenuti sezione di ISWEB nella sua implementazione PAT
	 *
	 */


$tabella = 'sezioni';
$campo = 'nome';
$campiRicercabili = array('nome', 'descrizione', 'title_code', 'h1_code', 'h2_code');
$q = $qSezioni;
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
	// e quindi verificare se a quel punto la ricerca � andata a buon fine
	for($i = 0; $i < count($campiRicercabili); $i++) {
		$c = $campiRicercabili[$i];
		$contenuto = $res[$c];
		foreach((array)$qRicerca as $qr) {
			/*
			if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
				$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
				$i = count($campiRicercabili); //per uscire dal ciclo pi� interno
			}
			*/
			if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
				$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
				$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
				$i = count($campiRicercabili); //per uscire dal ciclo pi� interno
			} else {
				//beta assonanza
				$items[$res['id']] = "<b>Forse cercavi</b> ".html_entity_decode(trim($res['campoDefaultTitolo']));
				$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
				$i = count($campiRicercabili); //per uscire dal ciclo pi� interno
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
			// inserisco la sezione nei risultati solo se � visibile
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
	$output .= "pagina".$key."_".pulisciNome(nomeSezDaId($key)).".html|$value|e\n";
}
?>