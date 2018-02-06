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
$nomedb = $restResponse->getParametro(2);
$nomedb = isset($nomedb) ? $nomedb : '';

$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetti WHERE nomedb = '".$nomedb."'";
if ( !($result = $database->connessioneConReturn($sql)) ) {
	echo $restResponse->returnError('Errore in estrazione tabella');
	exit();
}
$obj = $database->sqlArray($result);
$idOggetto = $obj['id'];
if(!$idOggetto) {
	echo $restResponse->returnError('Errore estrazione oggetto: tipo \''.$nomedb.'\' non valido');
	exit();
}
$documento = new documento($idOggetto,"no");
$campi = array();
for($i=0; $i < count($documento->struttura); $i++) {
	$campi[] = array(
		'nome' => $documento->struttura[$i]['nomecampo'],
		'etichetta' => $documento->struttura[$i]['etichetta'],
		'tipo' => $documento->struttura[$i]['tipoinput']
	);
}
echo $restResponse->restResponse($campi);
?>