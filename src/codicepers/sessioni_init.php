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
	 * codicepers/sessioni_init.php
	 * 
	 * @Descrizione
	 * File che estende le funzioni del middleware sessioni di ISWEB. Applicazione di alcune correzioni per eventuali installazioni multi-ente
	 * ATTENZIONE: versione ancora alpha. Utilizzare implementazione multiente con attenzione
	 *
	 */


if ($_SERVER['HTTP_HOST'] != $dominio) {
	// effettuo direttamente la query sugli enti per verificare il dominio richiamato	
	$sql="SELECT id,url_etrasparenza,url_etrasparenza_multidominio,cookie_dominio,cookie_nome FROM ".$dati_db['prefisso']."etrasp_enti WHERE url_pat='http://".$_SERVER['HTTP_HOST']."' OR url_pat='".$_SERVER['HTTP_HOST']."' OR url_pat_multidominio LIKE '%".$_SERVER['HTTP_HOST']."%' OR '".$_SERVER['HTTP_HOST']."'=CONCAT(nome_breve_ente,'.dominioprincipale.it')";

	if ( !($risultato = $database->connessioneConReturn($sql)) ) {
		die('Non posso caricare dati ente in analisi dominio.');
	}
	$datEnte = $database->sqlArray($risultato);

	if (is_array($datEnte) and isset($datEnte['id']) and $datEnte['id']) {

		// imposto le variabili poi usate e memorizzate nel cookie
		$_GET['id_ente'] = $datEnte['id'];

		// correggo le variabili
		if($datEnte['url_pat_multidominio']) {
			// SET SERVER URL
			$array_server_url = explode(",",$datEnte['url_pat_multidominio']);
			
			foreach ( (array)$array_server_url as $el_server_url ) {
				if( strpos($el_server_url,$_SERVER['HTTP_HOST'])) {
					$server_url = $el_server_url."/";
					$server_s_url = $el_server_url."/";
				}
			}

			// SET COOKIE DOMINIO
			$array_cookie_dominio = explode(",",$datEnte['cookie_dominio']);
			foreach ( (array)$array_cookie_dominio as $el_cookie_dominio ) {
				if( $el_cookie_dominio==$_SERVER['HTTP_HOST'] ) {
					$configurazione['cookie_dominio'] = $el_cookie_dominio;
				}
			}
		}else {
			$server_url = $datEnte['url_pat']."/";
			$server_s_url = $datEnte['url_pat']."/";
			$configurazione['cookie_dominio'] = $datEnte['cookie_dominio'];
		}
		$configurazione['cookie_nome'] = $datEnte['cookie_nome'];				
	}
} 


?>
