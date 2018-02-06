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
$sql = "SELECT nome,nomedb AS object, descrizione FROM ".$dati_db['prefisso']."oggetti WHERE id > 1 AND oggetto_sistema = 0 ORDER BY nome";
if ( !($result = $database->connessioneConReturn($sql)) ) {
	echo $restResponse->returnError('Errore in estrazione oggetti response');
	$restResponse->exitApp();
}
$obj = $database->sqlArrayAss($result);
echo $restResponse->restResponse($obj);
$restResponse->exitApp();
?>