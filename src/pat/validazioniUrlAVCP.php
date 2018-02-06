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
	 * pat/validazioniUrlAVCP.php
	 * 
	 * @Descrizione
	 * File di utilità per le validazioni sintattiche delle informazioni incluse nelle comunicazioni XML
	 *
	 */	

include_once('pat/funzioniAVCP.php');

$visualizzaAlert = false;
$testoTooltip = '';
$numeroErrori = 0;

// Enable user error handling 
libxml_use_internal_errors(true); 

$file = './avcp/'.$istanzaOggetto['id_ente'].'/'.$istanzaOggetto['anno'].'.xml';
if(file_exists($file)) {

	$xml = new DOMDocument(); 
	$xml->load($file); 

	if (!$xml->schemaValidate('./avcp/datasetAppaltiL190.xsd')) { 
		$visualizzaAlert = true;
		$numeroErrori += libxml_display_errors();
	}
	//validazione aggiuntiva dei campi data
	$erroriData = xmlValidaDate($file);
	if ($erroriData) {
		$visualizzaAlert = true;
		$numeroErrori += $erroriData;
	}

	if($visualizzaAlert and $numeroErrori > 0) {
		if($numeroErrori > 1) {
			$testoTooltip = 'Il file xml per la comunicazione all\'ANAC contiene '.$numeroErrori.' errori';
		} else {
			$testoTooltip = 'Il file xml per la comunicazione all\'ANAC contiene '.$numeroErrori.' errore';
		}
		if($datiUser['permessi'] == 10) {
			$testoTooltip .= ' ('.'./avcp/'.$istanzaOggetto['id_ente'].'/'.$istanzaOggetto['anno'].'.xml'.')';
		}
		$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
	}
}
?>