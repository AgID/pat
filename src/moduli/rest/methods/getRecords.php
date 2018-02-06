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

//inizializzazione
$nomedb = $restResponse->getParametro(2);
$campoOrdine = $restResponse->getParametro(3);
$tipoOrdine = $restResponse->getParametro(4);
$limite = $restResponse->getParametro(5);
$inizio = $restResponse->getParametro(6);
$nomedb = isset($nomedb) ? $nomedb : '';
$campoOrdine = (isset($campoOrdine) and trim($campoOrdine) != '') ? $campoOrdine : 'data_creazione';
$tipoOrdine = (isset($tipoOrdine) and trim($tipoOrdine) != '') ? strtoupper($tipoOrdine) : 'DESC';
$limite = (isset($limite) and trim($limite) != '') ? $limite : '20';
$inizio = (isset($inizio) and trim($inizio) != '') ? $inizio : '0';

$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetti WHERE nomedb = '".$nomedb."'";
if ( !($result = $database->connessioneConReturn($sql)) ) {
	echo $restResponse->returnError('Errore in estrazione tabella');
	$restResponse->exitApp();
}
$obj = $database->sqlArray($result);
$idOggetto = $obj['id'];
if(!$idOggetto) {
	echo $restResponse->returnError('Errore estrazione oggetto: tipo \''.$nomedb.'\' non valido');
	$restResponse->exitApp();
}
$documento = new documento($idOggetto,"no");

//controllo campo ordinamento
$campoOrdinamento = false;
for($i=0; $i < count($documento->struttura); $i++) {
	if($documento->struttura[$i]['nomecampo'] == $campoOrdine) {
		$campoOrdinamento = true;
		$i = count($documento->struttura);
	}
}
if(!$campoOrdinamento) {
	//posso ordinare anche per numero_letture, data_creazione e ultima_modifica
	switch($campoOrdine) {
		case 'numero_letture':
		case 'data_creazione':
		case 'ultima_modifica':
			$campoOrdinamento = true;
		break;
	}
}
if(!$campoOrdinamento) {
	echo $restResponse->returnError('Errore campo ordinamento oggetto: campo \''.$campoOrdine.'\' non valido');
	$restResponse->exitApp();
}

//controllo tipo ordinamento
if(!($tipoOrdine == 'DESC' or $tipoOrdine == 'ASC')) {
	echo $restResponse->returnError('Errore tipo ordinamento oggetto: tipo \''.$tipoOrdine.'\' non valido');
	$restResponse->exitApp();
}

//controllo valori ordinamento
if(!is_numeric($limite)) {
	echo $restResponse->returnError('Errore limite ordinamento oggetto: \''.$limite.'\' non valido');
	$restResponse->exitApp();
}
if(!is_numeric($inizio)) {
	echo $restResponse->returnError('Errore valore inizio ordinamento oggetto: \''.$inizio.'\' non valido');
	$restResponse->exitApp();
}

$criterio = array('query' => '', 'query_order' => $campoOrdine.' '.$tipoOrdine);

ob_start();
$tempRecords = $documento->caricaDocumenti($criterio, $campoOrdine, $tipoOrdine, $limite, $inizio);
ob_end_clean();

$records = array();
$index = 0;
foreach((array)$tempRecords as $rec) {
	for($i=0; $i < count($documento->struttura); $i++) {
		$records[$index][$documento->struttura[$i]['nomecampo']] = $rec[$documento->struttura[$i]['nomecampo']];
	}
	$records[$index]['id'] = $rec['id'];
	$records[$index]['__nome'] = $nomedb;
	$index++;
}

echo $restResponse->restResponse($records);
?>