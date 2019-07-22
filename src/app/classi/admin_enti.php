<?

class enti {

	
	function caricaEnti() {
		global $database, $dati_db;

		$sql="SELECT * FROM ".$dati_db['prefisso']."etrasp_enti";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("errore in carica enti. ".$sql);
		}

		$lista = $database->sqlArrayAss($result);
		return $lista;

	}

	// funzione di caricamento singolo ENTE
	function caricaEnte($id) {
		global $database, $dati_db;

		$sql="SELECT * FROM ".$dati_db['prefisso']."etrasp_enti WHERE id=$id";
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso caricare dati ente'.$sql);
		}

		$ente = $database->sqlArray($result);

		return $ente;

	}
	
	////////////////////// FUNZIONE DI AGGIUNTA DI UN ENTE//////////////////////////

	function aggiungiEnte($arrayValori) {
		global $dati_db,$database,$datiUser;
		
		// verifico valori caselle
		$arrayValori['oggetto_procedimenti'] = isset($arrayValori['oggetto_procedimenti']) ? $arrayValori['oggetto_procedimenti'] : 0;		
		$arrayValori['oggetto_provvedimenti'] = isset($arrayValori['oggetto_provvedimenti']) ? $arrayValori['oggetto_provvedimenti'] : 0;
		$arrayValori['visualizzazione_tabellare_org_ind_pol'] = isset($arrayValori['visualizzazione_tabellare_org_ind_pol']) ? $arrayValori['visualizzazione_tabellare_org_ind_pol'] : 0;
		$arrayValori['mostra_data_aggiornamento'] = isset($arrayValori['mostra_data_aggiornamento']) ? $arrayValori['mostra_data_aggiornamento'] : 0;
		$arrayValori['mostra_normativa_in_struttura'] = isset($arrayValori['mostra_normativa_in_struttura']) ? $arrayValori['mostra_normativa_in_struttura'] : 0;
		$arrayValori['oggetto_bandi_gara'] = isset($arrayValori['oggetto_bandi_gara']) ? $arrayValori['oggetto_bandi_gara'] : 0;
		$arrayValori['oggetto_incarichi'] = isset($arrayValori['oggetto_incarichi']) ? $arrayValori['oggetto_incarichi'] : 0;
		$arrayValori['oggetto_sovvenzioni'] = isset($arrayValori['oggetto_sovvenzioni']) ? $arrayValori['oggetto_sovvenzioni'] : 0;
		$arrayValori['oggetto_normativa'] = isset($arrayValori['oggetto_normativa']) ? $arrayValori['oggetto_normativa'] : 0;
		$arrayValori['oggetto_concorsi'] = isset($arrayValori['oggetto_concorsi']) ? $arrayValori['oggetto_concorsi'] : 0;
		$arrayValori['canale_opendata'] = isset($arrayValori['canale_opendata']) ? $arrayValori['canale_opendata'] : 0;
		$arrayValori['modulo_webservice'] = isset($arrayValori['modulo_webservice']) ? $arrayValori['modulo_webservice'] : 0;
		$arrayValori['aggiorna_avcp'] = isset($arrayValori['aggiorna_avcp']) ? $arrayValori['aggiorna_avcp'] : 0;
		$arrayValori['supporto_ente'] = isset($arrayValori['supporto_ente']) ? $arrayValori['supporto_ente'] : 0;
		$arrayValori['disdetta_ente'] = isset($arrayValori['disdetta_ente']) ? $arrayValori['disdetta_ente'] : 0;
		$arrayValori['indicizzabile'] = isset($arrayValori['indicizzabile']) ? $arrayValori['indicizzabile'] : 0;
		$arrayValori['id_ente_albo'] = $arrayValori['id_ente_albo'] > 0 ? $arrayValori['id_ente_albo'] : 0;
		$arrayValori['condizione_bandi_archiviati'] = isset($arrayValori['condizione_bandi_archiviati']) ? $arrayValori['condizione_bandi_archiviati'] : 0;
		
		$dataCreazione = time();
		$idCreatore = $datiUser['id'];
		
		// verifico caselle
		if ($arrayValori['canale_opendata'] == '' OR !isset($arrayValori['canale_opendata'])) {
				$arrayValori['canale_opendata'] = 0;
		}
		
		// VERIFICO DATA SCADENZA
		if ($arrayValori['data_scadenza'] == '' OR !isset($arrayValori['data_scadenza'])) {
			$arrayValori['data_scadenza'] = 'NULL';
		}
		
		if (is_array($arrayValori['utenti_notifiche_accessocivico'])) {
			$arrayValori['utenti_notifiche_accessocivico'] = implode(",",$arrayValori['utenti_notifiche_accessocivico']);
		}
		if (is_array($arrayValori['utenti_notifiche_sistema'])) {
			$arrayValori['utenti_notifiche_sistema'] = implode(",",$arrayValori['utenti_notifiche_sistema']);
		}
		
		// creo la query di installazione nuovo template
		$sql = "INSERT INTO ".$dati_db['prefisso']."etrasp_enti (
							data_creazione,id_creatore,data_attivazione,data_scadenza,nome_completo_ente,nome_breve_ente,
							tipo_ente,url_etrasparenza,url_sitoistituzionale,url_albopretorio,url_livello_superiore_ente,url_livello_superiore_ente_titolo,
							url_social_facebook,url_social_twitter,url_social_youtube,url_social_google,url_social_flickr,url_social_instagram,
							file_logo_semplice,file_logo_etrasp,cookie_dominio,cookie_nome,url_privacy,
							canale_opendata,oggetto_procedimenti,oggetto_provvedimenti,oggetto_bandi_gara,oggetto_incarichi,oggetto_sovvenzioni,oggetto_normativa,oggetto_concorsi,
							visualizzazione_tabellare_org_ind_pol,mostra_data_aggiornamento,mostra_normativa_in_struttura,personale_ruoli,personale_qualifiche,bandi_gara_amm_agg,bandi_gara_cod_fisc,bandi_gara_tipo_amm,bandi_gara_prov,bandi_gara_comune,bandi_gara_indirizzo,
							indirizzo_via,indirizzo_cap,indirizzo_comune,indirizzo_provincia,telefono,
							email,email_certificata,responsabile_pubblicazione,p_iva,ip_blacklist,
							testo_welcome,testo_footer,modulo_webservice,aggiorna_avcp,id_ente_albo,supporto_ente,disdetta_ente,indicizzabile,
							smtp_host,smtp_username,smtp_password,smtp_port,smtp_s,smtp_auth,email_notifiche,utente_responsabile_trasparenza,utenti_notifiche_accessocivico,utenti_notifiche_sistema,
							condizione_bandi_archiviati
							) VALUES (
							".$dataCreazione.",".$idCreatore.",".$arrayValori['data_attivazione'].",".$arrayValori['data_scadenza'].",'".$arrayValori['nome_completo_ente']."','".$arrayValori['nome_breve_ente']."',
							".$arrayValori['tipo_ente'].",'".$arrayValori['url_etrasparenza']."','".$arrayValori['url_sitoistituzionale']."','".$arrayValori['url_albopretorio']."','".$arrayValori['url_livello_superiore_ente']."','".$arrayValori['url_livello_superiore_ente_titolo']."',
							'".$arrayValori['url_social_facebook']."','".$arrayValori['url_social_twitter']."','".$arrayValori['url_social_youtube']."','".$arrayValori['url_social_google']."','".$arrayValori['url_social_flickr']."','".$arrayValori['url_social_instagram']."',
							'".$arrayValori['file_logo_semplice']."','".$arrayValori['file_logo_etrasp']."','".$arrayValori['cookie_dominio']."','".$arrayValori['cookie_nome']."','".$arrayValori['url_privacy']."',
							".$arrayValori['canale_opendata'].",".$arrayValori['oggetto_procedimenti'].",".$arrayValori['oggetto_provvedimenti'].",".$arrayValori['oggetto_bandi_gara'].",".$arrayValori['oggetto_incarichi'].",".$arrayValori['oggetto_sovvenzioni'].",".$arrayValori['oggetto_normativa'].",".$arrayValori['oggetto_concorsi'].",
							".$arrayValori['visualizzazione_tabellare_org_ind_pol'].",".$arrayValori['mostra_data_aggiornamento'].",".$arrayValori['mostra_normativa_in_struttura'].",'".$arrayValori['personale_ruoli']."','".$arrayValori['personale_qualifiche']."','".$arrayValori['bandi_gara_amm_agg']."','".$arrayValori['bandi_gara_cod_fisc']."','".$arrayValori['bandi_gara_tipo_amm']."',
							'".$arrayValori['bandi_gara_prov']."','".$arrayValori['bandi_gara_comune']."','".$arrayValori['bandi_gara_indirizzo']."',
							'".$arrayValori['indirizzo_via']."','".$arrayValori['indirizzo_cap']."','".$arrayValori['indirizzo_comune']."','".$arrayValori['indirizzo_provincia']."','".$arrayValori['telefono']."',
							'".$arrayValori['email']."','".$arrayValori['email_certificata']."','".$arrayValori['responsabile_pubblicazione']."','".$arrayValori['p_iva']."','".$arrayValori['ip_blacklist']."',
							'".$arrayValori['testo_welcome']."','".$arrayValori['testo_footer']."',".$arrayValori['modulo_webservice'].",".$arrayValori['aggiorna_avcp'].",".$arrayValori['id_ente_albo'].",".$arrayValori['supporto_ente'].",".$arrayValori['disdetta_ente'].",".$arrayValori['indicizzabile'].",
							'".$arrayValori['smtp_host']."','".$arrayValori['smtp_username']."','".$arrayValori['smtp_password']."','".$arrayValori['smtp_port']."','".$arrayValori['smtp_s']."','".$arrayValori['smtp_auth']."','".$arrayValori['email_notifiche']."',
							'".$arrayValori['utente_responsabile_trasparenza']."','".$arrayValori['utenti_notifiche_accessocivico']."','".$arrayValori['utenti_notifiche_sistema']."',
							".$arrayValori['condizione_bandi_archiviati']."
							)";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die('Non posso installare ente'.$sql);
		} else {
			return true;
		}
		
	}
	
	////////////////////// FUNZIONE DI AGGIUNTA DI UN ENTE//////////////////////////

	function modificaEnteParziale($idEnte, $arrayValori) {
		global $dati_db,$database;
		
		/*
		echo "<pre>";
		print_r($arrayValori);
		echo "</pre>";
		*/
		
		// verifico valori caselle
		$arrayValori['oggetto_procedimenti'] = isset($arrayValori['oggetto_procedimenti']) ? $arrayValori['oggetto_procedimenti'] : 0;
		$arrayValori['oggetto_provvedimenti'] = isset($arrayValori['oggetto_provvedimenti']) ? $arrayValori['oggetto_provvedimenti'] : 0;
		$arrayValori['oggetto_bandi_gara'] = isset($arrayValori['oggetto_bandi_gara']) ? $arrayValori['oggetto_bandi_gara'] : 0;
		$arrayValori['oggetto_incarichi'] = isset($arrayValori['oggetto_incarichi']) ? $arrayValori['oggetto_incarichi'] : 0;
		$arrayValori['oggetto_sovvenzioni'] = isset($arrayValori['oggetto_sovvenzioni']) ? $arrayValori['oggetto_sovvenzioni'] : 0;
		$arrayValori['oggetto_normativa'] = isset($arrayValori['oggetto_normativa']) ? $arrayValori['oggetto_normativa'] : 0;
		$arrayValori['oggetto_concorsi'] = isset($arrayValori['oggetto_concorsi']) ? $arrayValori['oggetto_concorsi'] : 0;
		$arrayValori['canale_opendata'] = isset($arrayValori['canale_opendata']) ? $arrayValori['canale_opendata'] : 0;
		$arrayValori['visualizzazione_tabellare_org_ind_pol'] = isset($arrayValori['visualizzazione_tabellare_org_ind_pol']) ? $arrayValori['visualizzazione_tabellare_org_ind_pol'] : 0;
		$arrayValori['mostra_data_aggiornamento'] = isset($arrayValori['mostra_data_aggiornamento']) ? $arrayValori['mostra_data_aggiornamento'] : 0;
		$arrayValori['mostra_normativa_in_struttura'] = isset($arrayValori['mostra_normativa_in_struttura']) ? $arrayValori['mostra_normativa_in_struttura'] : 0;
		$arrayValori['modulo_webservice'] = isset($arrayValori['modulo_webservice']) ? $arrayValori['modulo_webservice'] : 0;
		$arrayValori['aggiorna_avcp'] = isset($arrayValori['aggiorna_avcp']) ? $arrayValori['aggiorna_avcp'] : 0;
		$arrayValori['supporto_ente'] = isset($arrayValori['supporto_ente']) ? $arrayValori['supporto_ente'] : 0;
		$arrayValori['indicizzabile'] = isset($arrayValori['indicizzabile']) ? $arrayValori['indicizzabile'] : 0;
		$arrayValori['disdetta_ente'] = isset($arrayValori['disdetta_ente']) ? $arrayValori['disdetta_ente'] : 0;
		$arrayValori['condizione_bandi_archiviati'] = isset($arrayValori['condizione_bandi_archiviati']) ? $arrayValori['condizione_bandi_archiviati'] : 0;
		
		// creo array di esclusioni
		$escludi = array(
			'stato',
			'rispostaForm',
			'data_attivazioneVis',
			'file_logo_sempliceazione',
			'file_logo_etraspazione',
			'file_organigrammaazione',
			'data_scadenzaVis',
			'smtp_testmail',
			'data_scadenza'
		);
		
		// VERIFICO DATA SCADENZA
		if ($arrayValori['data_scadenza'] == '' OR !isset($arrayValori['data_scadenza'])) {
			$arrayValori['data_scadenza'] = 'NULL';
		}
		
		if (is_array($arrayValori['utenti_notifiche_accessocivico'])) {
			$arrayValori['utenti_notifiche_accessocivico'] = implode(",",$arrayValori['utenti_notifiche_accessocivico']);
		}
		if (is_array($arrayValori['utenti_notifiche_sistema'])) {
			$arrayValori['utenti_notifiche_sistema'] = implode(",",$arrayValori['utenti_notifiche_sistema']);
		}

		$strQuery = "stato = ".$arrayValori['stato'];
		// creo la query in base all'array inviatomi e non alla struttura
		$chiaviValori = array_keys($arrayValori);		
		foreach ($chiaviValori as $campoTemp) {
			
			// correzioni dei valori di campi, esludo i campi			
			if (!(in_array($campoTemp,$escludi))) {
				$strQuery .= ",".$campoTemp."='".$arrayValori[$campoTemp]."'";
			}
		}

		// modifico la sezione scelta
		$sql = "UPDATE ".$dati_db['prefisso']."etrasp_enti SET ".$strQuery." WHERE id = ".$idEnte;
		
		//echo "querymod: ".$sql;
		
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso aggiornare ente'.$sql);
		} else {
			return TRUE;
		}

	}
	
	// funzione di cancellazione rss (AZIONE MULTIPLA)
	function cancellaEnti($stringaOggetti) {
		global $dati_db,$database;

		// parso la stringa che mi e' stata inviata per costruire un array
		$arrayOggetti = explode(",", $stringaOggetti);
		$numeroOggetti = count($arrayOggetti)-1;

		// creo la condizione per la cancellazione
		$condizione = 'id='.$arrayOggetti[0];
		for ($i=1;$i<$numeroOggetti;$i++) {
			$condizione .= ' or id='.$arrayOggetti[$i];
		}
		// cancello gli oggetti segnalati
		$sql = "DELETE FROM ".$dati_db['prefisso']."etrasp_enti WHERE ".$condizione;
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso cancellare enti : '.$sql);
		}
		
		/*
		// cancello gli oggetti segnalati
		$sql = "DELETE FROM ".$dati_db['prefisso']."regole_modelli_applicazioni WHERE ".$condizioneRif;
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso cancellare risposte in cancellazione soindaggio : '.$sql);
		}
		$sql = "DELETE FROM ".$dati_db['prefisso']."regole_pubblicazione_modelli WHERE ".$condizioneRif;
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso cancellare risposte in cancellazione soindaggio : '.$sql);
		}
		*/
		return $numeroOggetti+1;
	}
	
}

?>
