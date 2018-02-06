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
	 * codicepers/rest/*
	 * 
	 * @Descrizione
	 * Metodi rest per interfacciamento con eventuale APP collegata
	 *
	 */	
$root = $restResponse->getParametro(2);
$root = (isset($root) and trim($root) != '') ? $root : '0';
if(!is_numeric($root)) {
	echo $restResponse->returnError('Errore contenuto sezione: \''.$root.'\' non valido');
	$restResponse->exitApp();
}
$id = nomeSezDaId($root, 'template');
$sql = "SELECT id FROM ".$dati_db['prefisso']."regole_pubblicazione_template WHERE id_template = ".$id." AND (tipo_elemento = 'regola_default' OR tipo_elemento = 'regola') AND posizione = 'centro' ORDER BY priorita";
if ( !($result = $database->connessioneConReturn($sql)) ) {
	echo $restResponse->returnError('Errore in estrazione aree editoriali response');
	$restResponse->exitApp();
}
$regole = array();
foreach((array)$database->sqlArrayAss($result) as $regola) {
	$regole[] = $regola['id'];
}

if(count($regole) > 0) {
	$regole = implode(',',$regole);
	$sql = "SELECT id_elemento as id FROM ".$dati_db['prefisso']."regole_pubblicazione WHERE id_sezione = ".$root." AND (tipo_elemento = 'paragrafo') AND id_regola_template IN (".$regole.") ORDER BY priorita";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		echo $restResponse->returnError('Errore in estrazione regole di pubblicazione response');
		$restResponse->exitApp();
	}
}

$paragrafi = array();
foreach((array)$database->sqlArrayAss($result) as $paragrafo) {
	$sql = "SELECT contenuto FROM ".$dati_db['prefisso']."oggetto_paragrafo WHERE id = ".$paragrafo['id'];
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		echo $restResponse->returnError('Errore in estrazione contenuto sezione response');
		$restResponse->exitApp();
	}
	$p = $database->sqlArray($result);
	if(trim($p['contenuto']) != '') {
		$paragrafi[] = $p['contenuto'];
	}
}

if(count($paragrafi) > 0) {
	echo $restResponse->restResponse($paragrafi);
	$restResponse->exitApp();
}
?>