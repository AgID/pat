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
$idCriterio = $restResponse->getParametro(3);
$limite = $restResponse->getParametro(4);
$inizio = $restResponse->getParametro(5);
$nomedb = isset($nomedb) ? $nomedb : '';
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
$documento->intElenco = 'nessuna';

//controllo valori ordinamento
if(!is_numeric($limite)) {
	echo $restResponse->returnError('Errore limite ordinamento oggetto: \''.$limite.'\' non valido');
	$restResponse->exitApp();
}
if(!is_numeric($inizio)) {
	echo $restResponse->returnError('Errore valore inizio ordinamento oggetto: \''.$inizio.'\' non valido');
	$restResponse->exitApp();
}

$criterio = loadingOggettoCriterio($idCriterio);

ob_start();

if($criterio['query_order'] != '') {
	$tempRecords = $documento->caricaDocumenti($criterio, 'nessuno', 'nessuno', $limite, $inizio);
} else {
	$tempRecords = $documento->caricaDocumenti($criterio, $documento->ordine, $documento->senso, $limite, $inizio);
}
ob_end_clean();

$records = array();
$index = 0;
foreach((array)$tempRecords as $rec) {
	for($i=0; $i < count($documento->struttura); $i++) {
		if($nomedb == 'riferimenti' and $documento->struttura[$i]['nomecampo'] == 'organo') {
			$rec[$documento->struttura[$i]['nomecampo']] = traduciOrgani($rec[$documento->struttura[$i]['nomecampo']]);
		}
		$records[$index][$documento->struttura[$i]['nomecampo']] = $rec[$documento->struttura[$i]['nomecampo']];
	}
	$records[$index]['id'] = $rec['id'];
	$records[$index]['__nome'] = $nomedb;
	$index++;
}

echo $restResponse->restResponse($records);
?>