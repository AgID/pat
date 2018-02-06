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
	 * 
	 * @Descrizione
	 * File di configurazione per eventuale APP collegata
	 *
	 */

$app = array(
	'id' => '',	//un ID generico scelto in modo arbitrario per il solo funzionamento interno. Questo valore deve essere presente anche nell'APP che vuole ricevere notifiche e deve essere inviato al CMS in fase di registrazione
	'nome' => 'PAT App',
	
	'gcm_url' => 'https://android.googleapis.com/gcm/send',
	'gcm_sender_id' => '',
	'gcm_api_key' => '',
	
	'apns_cert_file' => 'codicepers/apps/apns-dev-sgv.pem',
	'apns_cert_file_pwd' => '',
	'apns_gateway' => 'ssl://gateway.sandbox.push.apple.com:2195',	//sviluppo
	//'apns_gateway' => 'ssl://gateway.push.apple.com:2195',	//produzione
	
	'route_notifica' => array (
		//coppie id_oggetto => array(route, nomeoggetto)
		3 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/riferimenti'),
		4 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/incarichi'),
		5 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/modulistica_regolamenti'),
		11 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/gare_atti'),
		13 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/uffici'),
		16 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/procedimenti'),
		19 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/regolamenti'),
		22 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/concorsi'),
		27 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/normativa'),
		28 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/provvedimenti'),
		29 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/bilanci'),
		30 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/oneri'),
		38 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/sovvenzioni'),
		43 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/commissioni'),
		44 => array('route' => 'trasparenzaDetail/', 'oggetto' => '/societa')
	)
);
?>