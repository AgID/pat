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
$idOggetto = 13;
$documento = new documento($idOggetto,"no");

$criterio = loadingOggettoCriterio(42);	//uffici di primo livello

ob_start();
$tempRecords = $documento->caricaDocumentiCriterio($criterio);
ob_end_clean();

$records = array();
$index = 0;
foreach((array)$tempRecords as $rec) {
	for($i=0; $i < count($documento->struttura); $i++) {
		$records[$index][$documento->struttura[$i]['nomecampo']] = $rec[$documento->struttura[$i]['nomecampo']];
	}
	$records[$index]['id'] = $rec['id'];
	
	$configurazione['struttura_padre'] = $rec['id'];
	$criterio = loadingOggettoCriterio(152);	//uffici della struttura in {configurazione[struttura_padre]}

	ob_start();
	$tempSubRecords = $documento->caricaDocumentiCriterio($criterio);
	ob_end_clean();
	

	$subRecords = array();
	$subIndex = 0;
	foreach((array)$tempSubRecords as $sub) {
		for($j=0; $j < count($documento->struttura); $j++) {
			$subRecords[$subIndex][$documento->struttura[$j]['nomecampo']] = $sub[$documento->struttura[$j]['nomecampo']];
		}
		$subRecords[$subIndex]['id'] = $sub['id'];
		$subRecords[$subIndex]['__nome'] = 'uffici';
		$subIndex++;
	}
	
	$records[$index]['uffici'] = $subRecords;
	$records[$index]['__nome'] = 'uffici';
	$index++;
}

echo $restResponse->restResponse($records);
?>