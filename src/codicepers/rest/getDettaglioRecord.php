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
//inizializzazione
//lognormale('',$restResponse);
//lognormale('http://'.$_SERVER['HTTP_HOST'].'/'.$restResponse->getParametro(2).'.html');

$nomedb = $restResponse->getParametro(2);
$idDocumento = $restResponse->getParametro(3);

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

$linkLC = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?id_oggetto='.$idOggetto.'&id_doc='.$idDocumento;

ob_start();
$titolo = mostraDatoOggetto($idDocumento, $idOggetto);
$rec = file_get_contents($linkLC);
ob_end_clean();

if($nomedb == 'uffici') {
	//rimuovere mappe di google
	$rec = str_replace('nascondiMappaApp"', 'nascondiMappaApp" style="display:none;"', $rec);
}
$rec = explode('<div class="reviewLC"></div>', $rec);
$rec = $rec[1];

$record = array();
$record['titolo'] = $titolo;
$record['contenuto'] = $rec;

echo $restResponse->restResponse($record, true);
?>