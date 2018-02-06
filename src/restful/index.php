<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.0 - AgID release//////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @file
 * restfull/index.php
 * 
 * @Descrizione
 * Servizi di tipo rest per interoperabilità con PAT
 *
 */
 
 
require('inc/init.php');
require 'Slim/Slim.php';
require 'Slim/Middleware.php';
//require 'Slim/Middleware/HttpBasicAuth.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//$app->add(new \HttpBasicAuth());
$app->db = $database;
$app->oggettiEtrasparenza = $oggettiEtrasparenza;
$app->oggettiRicercabili = $oggettiRicercabili;


$app->get('/get.xml/:ckeck/:idEnte/:idOggetto/:idDocumento', function ($check, $idEnte, $idOggetto, $idDocumento) use ($app) {
	
	if($check != md5('isweb'.$idEnte.$idOggetto.$idDocumento)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Accesso non consentito '.md5('isweb'.$idEnte.$idOggetto.$idDocumento));
		echo $doc->saveXML();
	
	} else if(!in_array($idOggetto, $app->oggettiEtrasparenza)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Operazione non consentita: '.$idOggetto);
		echo $doc->saveXML();
		
	} else {
	
		$doc = new DOMDocument();
		$doc->encoding = 'UTF-8';
		
		//$name = mostraCampoOggetto($idOggetto, 'nomedb');
		
		$root = $doc->createElement('elemento');
		$root = $doc->appendChild($root);
		$link = $doc->createElement('link');
		$link = $root->appendChild($link);
		
		$istanza = caricaIstanzaOggetto($idDocumento, mostraCampoOggetto($idOggetto, 'tabella'), $idEnte, getCampiOggetto($idOggetto));
		if(isset($istanza['id']) && $istanza['id']) {
			$valoreLink = $doc->createTextNode(generaLinkElemento($idEnte, $idOggetto, $istanza['id']));
			$valoreLink = $link->appendChild($valoreLink);
			$elemento = $doc->createElement('dati');
			$elemento = $root->appendChild($elemento);
			foreach((array)$istanza as $campo=>$val) {
				$nodo = $doc->createElement($campo);
				$nodo = $elemento->appendChild($nodo);
				
				$valore = $doc->createTextNode(htmlentities($val));
				$valore = $nodo->appendChild($valore);
			}
		}
		
		
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc->formatOutput = true;
		echo $doc->saveXML();
	}
});

$app->get('/getAdmin.xml/:ckeck/:idEnte/:idOggetto/:idDocumento', function ($check, $idEnte, $idOggetto, $idDocumento) use ($app) {
	
	if($check != md5('isweb'.$idEnte.$idOggetto.$idDocumento)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Accesso non consentito '.md5('isweb'.$idEnte.$idOggetto.$idDocumento));
		echo $doc->saveXML();
	
	} else if(!in_array($idOggetto, $app->oggettiEtrasparenza)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Operazione non consentita: '.$idOggetto);
		echo $doc->saveXML();
		
	} else {
	
		$doc = new DOMDocument();
		$doc->encoding = 'UTF-8';
		
		$root = $doc->createElement('elemento');
		$root = $doc->appendChild($root);
		
		$istanza = caricaIstanzaOggetto($idDocumento, mostraCampoOggetto($idOggetto, 'tabella'), $idEnte, 'id,'.getCampoDefaultOggetto($idOggetto).' AS campoDefault');
		if($istanza['id']) {
			$elemento = $doc->createElement('dati');
			$elemento = $root->appendChild($elemento);
			foreach((array)$istanza as $campo=>$val) {
				$nodo = $doc->createElement($campo);
				$nodo = $elemento->appendChild($nodo);
				
				$valore = $doc->createTextNode(htmlentities($val));
				$valore = $nodo->appendChild($valore);
			}
		}
		
		
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc->formatOutput = true;
		echo $doc->saveXML();
	}
});


/**
 * Funzione di ricerca oggetti
 */
$app->get('/searchOggetti.xml/:ckeck/:idEnte/:idOggetti/:numero/:q', function ($check, $idEnte, $idOggetti, $numero, $q) use ($app) {
	
	if($check != md5('isweb'.$idEnte.$idOggetti.$numero)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Accesso non consentito');
		echo $doc->saveXML();
	
	} else {
		$doc = new DOMDocument();
		$doc->encoding = 'UTF-8';
		
		$root = $doc->createElement('elemento');
		$root = $doc->appendChild($root);
		$oggetti = $doc->createElement('oggetti');
		$oggetti = $root->appendChild($oggetti);
		
		$urlTrasparenza = getUrlTrasparenza($idEnte);
		
		if($idOggetti == 'ricercabili') {
			$arrayOggettiSearch = explode('-',$app->oggettiRicercabili);
		} else {
			$arrayOggettiSearch = explode('-',$idOggetti);
		}
		foreach((array)$arrayOggettiSearch as $idOggetto) {
			if(in_array($idOggetto, $app->oggettiEtrasparenza)) {
				$items = array();
				$items = ricercaOggetto($idEnte, $idOggetto, $numero, $q);
				if(count($items) > 0) {
					$oggetto = $doc->createElement('oggetto');
					$oggetto = $oggetti->appendChild($oggetto);
					$nome = getNomeOggetto($idOggetto);
					$nodoNome = $doc->createElement('nome');
					$nodoNome = $oggetto->appendChild($nodoNome);
					$textNome = $doc->createTextNode($nome);
					$textNome = $nodoNome->appendChild($textNome);
					$nodoSezElenco = $doc->createElement('linkSezioneElenco');
					$nodoSezElenco = $oggetto->appendChild($nodoSezElenco);
					$linkSezioneElenco = getLinkSezioneElenco($idOggetto, $idEnte);
					$sezElenco = $doc->createTextNode($linkSezioneElenco);
					$sezElenco = $nodoSezElenco->appendChild($sezElenco);
					$datiIstanze = $doc->createElement('istanze');
					$datiIstanze = $oggetto->appendChild($datiIstanze);	
					foreach ((array)$items as $chiave => $istanza) {
						$datiIstanza = $doc->createElement('istanza');
						$datiIstanza = $datiIstanze->appendChild($datiIstanza);	
						$link = $doc->createElement('link');
						$link = $datiIstanza->appendChild($link);
						$linkLettura = creaLinkLettura($idOggetto, $nome, $chiave, $istanza['titolo']);
						$valoreLink = $doc->createTextNode($urlTrasparenza . $linkLettura);
						$valoreLink = $link->appendChild($valoreLink);
						$elemento = $doc->createElement('titolo');
						$elemento = $datiIstanza->appendChild($elemento);
						$titolo = $doc->createTextNode($istanza['titolo']);
						$titolo = $elemento->appendChild($titolo);
						$campoData = $doc->createElement('data');
						$campoData = $datiIstanza->appendChild($campoData);
						$data = $doc->createTextNode($istanza['data']);
						$data = $campoData->appendChild($data);
					}
				}
			}
		}
		
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc->formatOutput = true;
		echo $doc->saveXML();
	}
});


/**
 * Funzione di caricamento istanze da criterio
 */
$app->get('/getIstanzeCriterio.xml/:ckeck/:idEnte/:idOggetto/:idCriterio/:numero', function ($check, $idEnte, $idOggetto, $idCriterio, $numero) use ($app) {
	
	if($check != md5('isweb'.$idEnte.$idCriterio.$numero)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Accesso non consentito');
		echo $doc->saveXML();
	
	} else {
		$GLOBALS['idEnte'] = $idEnte;
		$urlTrasparenza = getUrlTrasparenza($idEnte);
		
		$doc = new DOMDocument();
		$doc->encoding = 'UTF-8';
		
		$root = $doc->createElement('elemento');
		$root = $doc->appendChild($root);
		$istanze = $doc->createElement('istanze');
		$istanze = $root->appendChild($istanze);
		
		$items = caricaDocumentiCriterio($idEnte, $idOggetto, $idCriterio, $numero);
		if(count($items) > 0) {
			$nome = getNomeOggetto($idOggetto);
			foreach ((array)$items as $istanza) {
				$datiIstanza = $doc->createElement('istanza');
				$datiIstanza = $istanze->appendChild($datiIstanza);	
				$link = $doc->createElement('etrasparenza_link');
				$link = $datiIstanza->appendChild($link);
				$linkLettura = creaLinkLettura($idOggetto, $nome, $istanza['id'], $istanza);
				$valoreLink = $doc->createTextNode($urlTrasparenza . $linkLettura);
				$valoreLink = $link->appendChild($valoreLink);
				foreach((array)$istanza as $campo=>$val) {
					$nodo = $doc->createElement($campo);
					$nodo = $datiIstanza->appendChild($nodo);
					$valore = $doc->createTextNode(utf8_encode(htmlentities($val)));
					$valore = $nodo->appendChild($valore);
				}
			}
		}
		
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc->formatOutput = true;
		echo $doc->saveXML();
	}
});

/**
 *	Funzione di caricamento istanze per elenco
 */
$app->get('/getElenco.xml/:ckeck/:idEnte/:idOggetto/:idCriterio', function ($check, $idEnte, $idOggetto, $idCriterio) use ($app) {
	if($check != md5('isweb'.$idEnte.$idOggetto.$idCriterio)) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Accesso non consentito');
		echo $doc->saveXML();
	
	} else {
		$GLOBALS['idEnte'] = $idEnte;
		$urlTrasparenza = getUrlTrasparenza($idEnte);
		
		$doc = new DOMDocument();
		$doc->encoding = 'UTF-8';
		
		$root = $doc->createElement('elemento');
		$root = $doc->appendChild($root);
		$istanze = $doc->createElement('istanze');
		$istanze = $root->appendChild($istanze);
		
		$items = caricaDocumentiCriterio($idEnte, $idOggetto, $idCriterio, 0);
		
		$totale = $doc->createElement('totale');
		$totale = $root->appendChild($totale);
		$valoreTotale = $doc->createTextNode(count($items));
		$valoreTotale = $totale->appendChild($valoreTotale);
		
		if(count($items) > 0) {
			$nome = getNomeOggetto($idOggetto);
			foreach ((array)$items as $istanza) {
				$datiIstanza = $doc->createElement('istanza');
				$datiIstanza = $istanze->appendChild($datiIstanza);	
				$link = $doc->createElement('etrasparenza_link');
				$link = $datiIstanza->appendChild($link);
				$linkLettura = creaLinkLettura($idOggetto, $nome, $istanza['id'], $istanza);
				$valoreLink = $doc->createTextNode($urlTrasparenza . $linkLettura);
				$valoreLink = $link->appendChild($valoreLink);
				foreach((array)$istanza as $campo=>$val) {
					$nodo = $doc->createElement($campo);
					$nodo = $datiIstanza->appendChild($nodo);
					$valore = $doc->createTextNode(utf8_encode(htmlentities($val)));
					$valore = $nodo->appendChild($valore);
				}
			}
		}
		
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc->formatOutput = true;
		echo $doc->saveXML();
	}
});

$app->run();



//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
$app->get('/', function () use ($app) {
	echo "Caricamento...";
});

$app->get('/hello.json/:name', function ($name) {
	$data[name] = 'Ciao '.$name;
	echo json_encode($data);
});

$app->get('/hello.xml/:name', function ($name) use ($app) {
	$doc = new DOMDocument();
	$doc->encoding = 'UTF-8';
	
	$root = $doc->createElement('utenti');
	$root = $doc->appendChild($root);
	
	$utente = $doc->createElement('utente');
	$utente = $root->appendChild($utente);
	
	$nome = $doc->createTextNode($name);
	$nome = $utente->appendChild($nome);
	
	$app->response->headers->set('Content-Type', 'text/plain');
	$doc->formatOutput = true;
	echo $doc->saveXML();
});

$app->get('/utenti.xml', function () use ($app) {
	$doc = new DOMDocument();
	$doc->encoding = 'UTF-8';
	
	$root = $doc->createElement('utenti');
	$root = $doc->appendChild($root);
	
	$q = "SELECT * FROM utenti";
	if( !($result = $app->db->connessioneConReturn($q)) ) {
		die("errore in loading newsletter cron: ".$q);
	}
	if ($app->db->sqlNumRighe($result) != 0) {
		$utenti = $app->db->sqlArrayAss($result);
		foreach((array)$utenti as $u) {
			$utente = $doc->createElement('utente');
			$utente = $root->appendChild($utente);
			
			$nome = $doc->createTextNode($u['nome']);
			$nome = $utente->appendChild($nome);
		}
	}
	
	$app->response->headers->set('Content-Type', 'text/plain');
	$doc->formatOutput = true;
	echo $doc->saveXML();
});

$app->get('/logoPiccolo.xml/:idEnte', function ($idEnte) use ($app) {
	
	if($idEnte) {
		$app->response->headers->set('Content-Type', 'text/plain');
		$doc = erroreXML('Operazione non consentita: '.$idOggetto);
		echo $doc->saveXML();
	}
	
});
?>