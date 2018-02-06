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
	 * codicepers/ricercaOggetti.php
	 * 
	 * @Descrizione
	 * Utility per la ricerca sui contenuti degli oggetti di ISWEB nelle loro implementazioni PAT
	 *
	 */


$minChars = $_GET['minChars'];
if($minChars == 0) {
	$minChars = 3;
}
$condizioneLimite = '';
if ($numero != 0) {
	$condizioneLimite = ' LIMIT 0,'.($numero + 1);
}
// forzatura per le evntuali istanze non autorizzate se attivato il workflow
$condizioneAuth = '';
if (($o->auth != 'nessuna' and $datiUser['permessi'] > 1 and $datiUser['sessione_admininfo']) or $o->proprieta == 'esterna') {
	$condizioneAuth = '';
} else {
	$condizioneAuth = ' AND stato != 0';
}
//workflow
if ($o->idOggetto > 1) {
	$condizioneAuth .= ' AND stato_workflow=\'finale\' ';
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
$campiRicerca = implode(' OR ', $arrayCampiRicerca);
$campiSelect = implode(',',$arrayCampiSelect);

$sql = "SELECT id, ".$campo." AS campoDefaultTitolo, ".$campiSelect." FROM ".$dati_db['prefisso'].$o->tabella." WHERE permessi_lettura != 'H' AND (id_lingua=".$o->id_lingua." or id_lingua=0) ".$condizioneAuth." AND ".$campoIdEnte." = ".$idEnte." AND (";
$sql .= $campiRicerca;
$sql .=") ";
$sql .=" ".$condizioniAggiuntive." ";
if($ordinamentoPersonalizzato != '') {
	$sql .=" ".$ordinamentoPersonalizzato." ";
} else {
	$sql .=" ORDER BY campoDefaultTitolo ";
}
$sql .=$condizioneLimite;

//lognormale($sql);
/*
$f = fopen('temp/test.txt', 'a+');
fwrite($f, $sql."\n\r");
fclose($f);
*/
if (!($result = $database -> connessioneConReturn($sql))) {
	$composizione = array();
} else {
	$composizione = $database -> sqlArrayAss($result);
}
$items = array();
$checkItems = array();

foreach((array)$composizione as $res) {
	//per ogni oggetto devo pulire i campi che possono contenere html
	// e quindi verificare se a quel punto la ricerca � andata a buon fine
	for($i = 0; $i < count($o->struttura); $i++) {
		$c = $o->struttura[$i];
		//lognormale('verifico campo '.$c['nomecampo']);
		if($c['ordinamento'] and ($c['tipoinput'] == 'string' or $c['tipoinput'] == 'blob' or $c['tipoinput'] == 'text')) {
			//ho un campo su cui � stata effettuata la ricerca
			$contenuto = $res[$c['nomecampo']];
			if($c['tipocampo'] == 'editor') {
				//elimino tags html
				$contenuto = strip_tags($contenuto);
			}
			/** altro controllo effettuato sulla ricerca
			 * il controllo � effettuato perch� la sola query potrebbe trovare risultati
			 * nei campi editor html prima di aver pulito i tags con lo striptags
			 */
			foreach((array)$qRicerca as $qr) {
				if(strpos(html_entity_decode(trim(strtolower($contenuto))), html_entity_decode(stripslashes(strtolower($qr)))) !== false) {
					$items[$res['id']] = html_entity_decode(trim($res['campoDefaultTitolo']));
					$checkItems[] = html_entity_decode(trim(strtolower($res['campoDefaultTitolo'])));
					$i = count($o->struttura); //per uscire dal ciclo pi� interno
				}
			}
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
if($tipoRicercaAuto == 'admin') {
	if (!$aclTrasparenza[$menuSecondario]['modifica'] AND !$aclTrasparenza[$menuSecondario]['creazione']) {
		$permessiModifica = false;
	} else {
		$permessiModifica = true;
	}
}
foreach ((array)$items as $key=>$value) {
	if($i < $numero) {
		$urlLC = "archivio".$idOggetto."_".pulisciNome($o->nome)."_0_".$key."_".$sezioneNavigazione['template'].".html";
		if($tipoRicercaAuto == 'admin' and $permessiModifica) {
			if($idOggetto == 11 and moduloAttivo('bandigara')) {
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
				$urlLC = "admin_pat.php?menu=".$menu."&menusec=".$menuSecondario."&azione=modifica&id=".$key.$link;
			} else {
				$urlLC = "admin_pat.php?menu=".$menu."&menusec=".$menuSecondario."&azione=modifica&id=".$key;
			}
		}
		$response[] = array(
			'label' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
			'value' => utf8_encode(tagliaContHtml($value, $limitaCaratteriOutput)),
			'url' => $urlLC
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