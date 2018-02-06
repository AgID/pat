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
	 * moduli/rest/methods/*
	 * 
	 * @Descrizione
	 * Metodi rest per interfacciamento con eventuale APP collegata
	 *
	 */	

$root = $restResponse->getParametro(2);
$root = (isset($root) and trim($root) != '') ? $root : '0';
if(!is_numeric($root)) {
	echo $restResponse->returnError('Errore sezione start: \''.$root.'\' non valido');
	$restResponse->exitApp();
}
$tree = getTree($root);
echo $restResponse->restResponse($tree);
$restResponse->exitApp();

function  getTree($id) {
	global $database, $dati_db, $restResponse;
	
	$sql = "SELECT id,id_riferimento,nome,title_code AS title,h1_code AS h1,h2_code AS h2,descrizione,keywords FROM ".$dati_db['prefisso']."sezioni WHERE id_riferimento = ".$id." AND permessi_lettura = 'N/A' AND disponibile = 1 ORDER BY priorita";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		echo $restResponse->returnError('Errore in estrazione sezioni response');
		$restResponse->exitApp();
	}
	$sez = $database->sqlArrayAss($result);
	$return = array();
	foreach((array)$sez as $s) {
		$sezTemp = $s;
		$sezTemp['sezioni'] = getTree($s['id']);
		$return[] = $sezTemp;
	}
	if(count($return) > 0) {
		return $return;
	} else {
		return null;
	}
}
?>