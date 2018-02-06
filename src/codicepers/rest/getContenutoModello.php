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
// Verifico esistenza del paragrafo
$oraCorrente = time();
$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente = ".$idEnte." AND id_sezione_etrasp = ".$restResponse->getParametro(2)." LIMIT 0,1";

if( !($result = $database->connessioneConReturn($sql)) ) {
	//gestire errore
}
$contenuto = '';
$contenutoModello = $database->sqlArray($result);

if ($contenutoModello['html_generico'] != '' AND $contenutoModello['html_generico'] != '<p></p>' AND $contenutoModello['html_generico'] != '<p>&nbsp;</p>') {
	$contenuto .= $contenutoModello['html_generico'];
	if ($entePubblicato['mostra_data_aggiornamento']) {
		if(($entePubblicato['id']!=23 OR ($entePubblicato['id']==23 AND ($sezioneNavigazione['id']!=725 AND $sezioneNavigazione['id']!=726))) AND
		   ($entePubblicato['id']!=33 OR ($entePubblicato['id']==33 AND ($sezioneNavigazione['id']!=725 AND $sezioneNavigazione['id']!=726)))
		){
			$contenuto .= "<div id=\"dataAggiornamento\">Contenuto aggiornato al ".visualizzaData($contenutoModello['ultima_modifica'],'d-m-Y')."</div>";
		}
	}
	
	$record = array();
	$record['contenuto'] = $contenuto;
	echo $restResponse->restResponse($record, true);
} else {
	$record = array();
	$record['contenuto'] = '';
	echo $restResponse->restResponse($record, true);
}
?> 