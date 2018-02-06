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
$nomedb = isset($nomedb) ? $nomedb : '';

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

$postData = array();
for($i=0; $i < count($documento->struttura); $i++) {
	if(isset($_POST[$documento->struttura[$i]['nomecampo']])) {
		$postData[$documento->struttura[$i]['nomecampo']] = $_POST[$documento->struttura[$i]['nomecampo']];
	}
}

if(count($postData) > 0) {
	$createRecord = false;
	ob_start();
	require('classi/admin_oggetti.php');
	$ogg = new oggettiAdmin($idOggetto);
	$istanzaOggetto['permessi_lettura'] = 'N/A';
	$istanzaOggetto['permessi_admin'] = 'N/A';
	$istanzaOggetto['id_proprietari_lettura'] = '-1';
	$istanzaOggetto['tipo_proprietari_lettura'] = 'tutti';
	$istanzaOggetto['id_proprietari_admin'] = '-1';
	$istanzaOggetto['tipo_proprietari_admin'] = 'tutti';
	$istanzaOggetto['id_proprietario'] = $datiUser['id'];
	$istanzaOggetto['id_lingua'] = 0;
	$istanzaOggetto['template'] = '';
	foreach((array)$postData as $k=>$v) {
		$istanzaOggetto[$k] = $v;
	}
	if ($ogg->aggiungiOggetto(0, $istanzaOggetto)) {
		$createRecord = true;
	}
	ob_end_clean();
	
	if($createRecord) {
		
		echo $restResponse->restResponse('ok');
		$restResponse->exitApp();
	} else {
		echo $restResponse->returnError('Errore creazione record oggetto: errore in fase di inserimento');
		$restResponse->exitApp();
	}
}
?>