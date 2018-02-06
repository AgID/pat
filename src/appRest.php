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
	 * appRest.php
	 * 
	 * @Descrizione
	 * Servizi di tipo rest per interoperabilità con PAT
	 *
	 */
 
include ('./inc/config.php');
include ('./inc/inizializzazione.php');
include ('./moduli/rest/restResponse.php');

header('Access-Control-Allow-Origin: *');

//identifico la chiamata rest e prendo i parametri passati
$restResponse = new restResponse($_SERVER['REQUEST_URI']);

switch($restResponse->getParametro(1)) {
	case 'getObjects':
	case 'getFields':
	case 'getRecords':
	case 'getRecordsCriterio':
	case 'getRecord':
	case 'createRecord':
	case 'getTree':
	case 'getContents':
		include 'moduli/rest/methods/'.$restResponse->getParametro(1).'.php';
	break;
	default:
		if(file_exists('codicepers/rest/'.$restResponse->getParametro(1).'.php')) {
			include 'codicepers/rest/'.$restResponse->getParametro(1).'.php';
		}
	break;
}
?>