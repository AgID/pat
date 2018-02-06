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
	 * pat/validazioniBandiAVCP.php
	 * 
	 * @Descrizione
	 * File di utilità per le validazioni sintattiche delle informazioni incluse nelle comunicazioni XML
	 *
	 */	
include_once('pat/funzioniAVCP.php');

$visualizzaAlert = false;
$visualizzaAlertErroreAvviso = false;
$testoTooltip = '';

if(moduloAttivo('bandigara')) {
	if($istanzaOggetto['tipologia'] == 'bandi ed inviti') {
		//bandi ed inviti
		if(!validaCIG($istanzaOggetto['cig'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'CIG, ';
		}
		if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
		}
		if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
		}
		if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'procedura di scelta del contraente, ';
		}
	} else if($istanzaOggetto['tipologia'] == 'lotto') {
		//lotti
		if(!validaCIG($istanzaOggetto['cig'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'CIG, ';
		}
	} else if($istanzaOggetto['tipologia'] == 'esiti') {
		//esiti
		if(!validaImporto($istanzaOggetto['valore_importo_aggiudicazione'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'valore importo di aggiudicazione, ';
		}
		if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'valore importo liquidato, ';
		}
		if(!validaData(date('Y-m-d', $istanzaOggetto['data_inizio_lavori']))) {
			$visualizzaAlert = true;
			$testoTooltip .= 'data di effettivo inizio dei lavori o forniture, ';
		}
		if(!validaData(date('Y-m-d', $istanzaOggetto['data_lavori_fine']))) {
			$visualizzaAlert = true;
			$testoTooltip .= 'data di ultimazione dei lavori o forniture, ';
		}
	} else if($istanzaOggetto['tipologia'] == 'delibere e determine a contrarre') {
		/*
		//delibere e determine a contrarre
		if(!validaCIG($istanzaOggetto['cig'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'CIG, ';
		}
		if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
		}
		if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
		}
		if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'procedura di scelta del contraente, ';
		}
		*/
	} else if($istanzaOggetto['tipologia'] == 'affidamenti') {
		//affidamenti
		if(!validaCIG($istanzaOggetto['cig'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'CIG, ';
		}
		if(!validaImporto($istanzaOggetto['valore_importo_aggiudicazione'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'valore importo di aggiudicazione, ';
		}
		if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'valore importo liquidato, ';
		}
		if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'procedura di scelta del contraente, ';
		}
		if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
		}
		if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
		}
	}
} else {

	if($istanzaOggetto['tipologia'] == 'bandi ed inviti' or $istanzaOggetto['tipologia'] == 'esiti'
		or $istanzaOggetto['tipologia'] == 'delibere e determine a contrarre' or $istanzaOggetto['tipologia'] == 'affidamenti') {
		
		if(!validaCIG($istanzaOggetto['cig'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'CIG, ';
		}
		if(!validaCfPi($istanzaOggetto['dati_aggiudicatrice']) OR trim($istanzaOggetto['dati_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'codice fiscale dell\'amministrazione aggiudicatrice, ';
		}
		if(trim($istanzaOggetto['denominazione_aggiudicatrice']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'denominazione dell\'amministrazione aggiudicatrice, ';
		}
		if(!validaSceltaContraente($istanzaOggetto['scelta_contraente'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'procedura di scelta del contraente, ';
		}

		if(!validaData(date('Y-m-d', $istanzaOggetto['data_inizio_lavori']))) {
			$visualizzaAlert = true;
			$testoTooltip .= 'data di effettivo inizio dei lavori o forniture, ';
		}
		if(!validaData(date('Y-m-d', $istanzaOggetto['data_lavori_fine']))) {
			$visualizzaAlert = true;
			$testoTooltip .= 'data di ultimazione dei lavori o forniture, ';
		}

		if(!validaImporto($istanzaOggetto['valore_importo_aggiudicazione'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'valore importo di aggiudicazione, ';
		}
		if(!validaImporto($istanzaOggetto['importo_liquidato'])) {
			$visualizzaAlert = true;
			$testoTooltip .= 'valore importo liquidato, ';
		}
		
	} else if($istanzaOggetto['tipologia'] == 'somme liquidate') {
		if(!validaData(date('Y-m-d', $istanzaOggetto['data_attivazione'])) or $istanzaOggetto['data_attivazione'] == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'data di pubblicazione, ';
		}
		if($istanzaOggetto['bando_collegato'] <= 0) {
			$visualizzaAlert = true;
			$testoTooltip .= 'procedura relativa, ';
		}
	}
	
	if($istanzaOggetto['bando_collegato'] > 0) {
		//verificare se la procedura attuale è collegata ad un avviso
		if(mostraDatoOggetto($istanzaOggetto['bando_collegato'], 11, 'tipologia') == 'avvisi pubblici') {
			$visualizzaAlertErroreAvviso = true;
		}
	}
	
}

if($visualizzaAlert) {
	$testoTooltip = 'I dati non validi e/o mancanti ai fini della comunicazione ANAC sono: '.substr($testoTooltip, 0, strlen($testoTooltip)-2);
	$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
}
if($visualizzaAlertErroreAvviso) {
	$testoTooltip = 'Il presente elemento non verr&agrave; comunicato all\'ANAC perch&egrave; erroneamente associato ad un avviso pubblico. Modificare l\'elemento andando a valorizzare il campo \'Procedura relativa\'';
	$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-remove-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
}
?>