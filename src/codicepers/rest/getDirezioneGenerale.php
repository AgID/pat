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

$criterio = loadingOggettoCriterio(75);

ob_start();
$tempRecords = $documento->caricaDocumenti($criterio, $campoOrdine, $tipoOrdine, 1, $inizio);
ob_end_clean();

$records = array();
$index = 0;

$linkLC = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?id_oggetto='.$idOggetto.'&id_doc='.$tempRecords[0]['id'];

ob_start();
$titolo = mostraDatoOggetto($idDocumento, $idOggetto);
$rec = file_get_contents($linkLC);
ob_end_clean();

$rec = explode('<div class="reviewLC"></div>', $rec);
$rec = $rec[1];

$record = array();
$record['titolo'] = $titolo;
$record['contenuto'] = $rec;

echo $restResponse->restResponse($record, true);
?>