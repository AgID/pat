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
$campoOrdine = $restResponse->getParametro(2);
$tipoOrdine = $restResponse->getParametro(3);
$limite = $restResponse->getParametro(4);
$inizio = $restResponse->getParametro(5);
$campoOrdine = (isset($campoOrdine) and trim($campoOrdine) != '') ? $campoOrdine : 'nessuno';
$tipoOrdine = (isset($tipoOrdine) and trim($tipoOrdine) != '') ? $tipoOrdine : 'nessuno';
$limite = (isset($limite) and trim($limite) != '') ? $limite : '0';
$inizio = (isset($inizio) and trim($inizio) != '') ? $inizio : '0';

$idOggetto = 3;
$documento = new documento($idOggetto,"no");

$criterio = loadingOggettoCriterio(71);

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
	$index++;
}

echo $restResponse->restResponse($records);
?>