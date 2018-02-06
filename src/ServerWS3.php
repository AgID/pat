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
	 * ServerWS3.php
	 * 
	 * @Descrizione
	 * Webservices SOAP per PAT
	 *
	 */
 
require_once('inc/config.php');
require_once('inc/inizializzazione.php');

ini_set("soap.wsdl_cache_enabled", "0"); // disabilito WSDL cache

class ServerWS2 {
	
	var $utenteEnte;

	/**
	 * verifica se il servizio è raggiungibile
	 */
    function sayHello($name) {
        return "Ciao ".$name."!!! (ServerWS3)";
    }

    function getOggetti($request) {
        global $dati_db,$database,$configurazione;
        
		$response = new ResponseWS();
        $request = $this->fixRequest($request);
		
        
        $f = fopen("temp/debugFixRequest.html", "w+");
		ob_start();
        echo "<pre>";
        print_r($request);
        echo "</pre>";
        $c = ob_get_contents();
        ob_end_clean();
        fwrite($f, $c."\n");
        fclose($f);
        
        //Controllo autorizzazioni
        if(!$this->isAuthenticated($request->value['token'], $request->value['user'])) {
            return $this->permessiNegatiResponse();
        }
        
        $campiOggetto = $this->getCampiOggetto($request->value['tipo']);
        if($campiOggetto == '') {
        	$response->message = $this->utf8_encode_recursive("Oggetto non valido.");
	        return $response;
        }
        
        //devo prendere la stringa per la query in base ai parametri passati
        if(count($request->campiRequest) > 0) {
        	$condizioneAggiuntiva = true;
        	$condizione = $this->elaboraCondizioni($request->campiRequest, $request->value['tipo']);
        	if($condizione['errore'] == 'campo_non_valido') {
		        $response->message = $this->utf8_encode_recursive("Campo [".$condizione['campo_non_valido']."] non valido nelle condizioni di ricerca.");
		        return $response;
        	}
        }

        $query = "SELECT ".$campiOggetto." FROM ".$dati_db['prefisso'].$this->getTabellaOggetto($request->value['tipo']).
					" WHERE ".$this->getCampoEnte($request->value['tipo'])." = ".$this->utenteEnte['id_ente_admin']." ";
		if($condizioneAggiuntiva) {
			$query .= $condizione['condizione'];
		}

        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "Errore nella selezione dei dati";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        if(!$istanzeDB) {
            $message = "Nessun risultato";
        } else {
            $message = "";
        }

        $ret = $this->utf8_encode_recursive($istanzeDB);
        $response->message = $this->utf8_encode_recursive($message);
        $response->valori[0] = $ret;
        
        $this->logRequest($request, $response, "getOggetti -> ".$query);
        return $response;
    }
    
    
    
    function addOggetto($request) {
        global $dati_db,$database,$configurazione;
        
    	$response = new ResponseWS();
        return $response;
    }
    
    function editOggetto($request) {
        global $dati_db,$database,$configurazione;
        
    	$response = new ResponseWS();
        return $response;
    }

	function deleteOggetto($request) {
        global $dati_db,$database,$configurazione;
        
    	$response = new ResponseWS();
        return $response;
    }

    ////////////////////////////////////////////////////////////////////////////
    //Funzioni non esposte come Web Services (interne)
    ////////////////////////////////////////////////////////////////////////////
    
    
    
    /**
     * verifica se la combinazione token user è corretta
     */
    function isAuthenticated($token, $username, $auth = 1) {
        global $dati_db,$database,$configurazione;

        $query = "SELECT * FROM ".$dati_db['prefisso']."utenti WHERE username = '$username'";
        if( !($result = $database->connessioneConReturn($query)) ) {
            $message = "errore";
        }
        $istanzeDB = $database->sqlArrayAss($result, MYSQL_ASSOC);
        foreach((array)$istanzeDB as $utente) {
        	$query = "SELECT * FROM ".$dati_db['prefisso']."etrasp_enti WHERE id = ".$utente['id_ente_admin'];
	        if( !($result = $database->connessioneConReturn($query)) ) {
	        }
	        $ente = $database->sqlArray($result, MYSQL_ASSOC);
	        if($token == md5($utente['username'].$ente['cookie_dominio'])) {
	        	$this->utenteEnte = $utente;
	            return true;
	        }
        }
        return false;
    }
    
    /**
     * funzione che mi restituisce la tabella del tipo passato nella request
     */
	function getTabellaOggetto($tipo) {
        switch($tipo) {
        	case "strutture":
        		return "oggetto_uffici";
        	break;
        	case "personale":
        		return "oggetto_riferimenti";
        	break;
        	case "commissioni":
        		return "oggetto_commissioni";
        	break;
        	case "societa":
        		return "oggetto_societa";
        	break;
        	case "procedimenti":
        		return "oggetto_procedimenti";
        	break;
        	case "regolamenti":
        		return "oggetto_regolamenti";
        	break;
        	case "modulistica":
        		return "oggetto_modulistica_regolamenti";
        	break;
        	case "normativa":
        		return "oggetto_normativa";
        	break;
        	case "bilanci":
        		return "oggetto_binalci";
        	break;
        	case "fornitori":
        		return "oggetto_elenco_fornitori";
        	break;
        	case "gare":
        		return "oggetto_gare_atti";
        	break;
        	case "requisiti":
        		return "oggetto_bandi_requisiti_qualificazione";
        	break;
        	case "concorsi":
        		return "oggetto_concorsi";
        	break;
        	case "sovvenzioni":
        		return "oggetto_sovvenzioni";
        	break;
        	case "incarichi":
        		return "oggetto_incarichi";
        	break;
        	case "provvedimenti":
        		return "oggetto_provvedimenti";
        	break;
        	case "oneri":
        		return "oggetto_oneri";
        	break;
        }
        return "";
    }
    
    /**
     * funzione che mi restituisce il campo dell'identificativo dell'ente (ente_id o id_ente)
     */
    function getCampoEnte($tipo) {
        switch($tipo) {
        	case "utenti":
        		return "id_ente_admin";
        	break;
        	default:
        		return "id_ente";
        	break;
        }
    }

	/**
	 * funzione usaata nella select per restituire i campi delle tabelle
	 */
    function getCampiOggetto($tipo) {
        $campi = "id";
        switch($tipo) {
        	case "strutture":
        		$campi .= ",nome_ufficio,struttura,referente,referenti_contatti,email_riferimento,email_certificate,telefono,fax,desc_att,orari,articolazione,pres_sede,ordine";
        	break;
        	case "personale":
        		$campi .= ",referente,organo,commissioni,ruolo,ruolo_politico,allegato_nomina,incarico";
        		$campi .= ",determinato,uffici,foto,telefono,mobile,fax,email,email_cert";
        		$campi .= ",note,vis_elenchi,priorita,curriculum,retribuzione,altre_cariche";
        		$campi .= ",patrimonio,archivio,carica_inizio,carica_fine";
        	break;
        	case "commissioni":
        		$campi .= ",nome,tipo,presidente,vicepresidente,segretari,membro,descrizione,membri,note,immagine,email,telefono,fax,indirizzo,ordine";
        	break;
        	case "societa":
        		$campi .= ",ragione,tipologia,misura,durata,oneri_anno,descrizione,rappresentanti,incarichi_trattamento,indirizzo_web,bilancio,bilancio_allegato";
        	break;
        	case "procedimenti":
        		$campi .= ",nome,referente_proc,referente_prov,resp_sost,ufficio_def,personale_proc,contattare,ufficio,descrizione,costi,silenzio_assenzo,dichiarazione,norme,termine,link_servizio,tempi_servizio";
        	break;
        	case "regolamenti":
        		$campi .= ",titolo,tipo,strutture,procedimenti,descrizione_mod,allegato,allegato_2,allegato_3,allegato_4,allegato_5,allegato_6,ordine";
        	break;
        	case "modulistica":
        		$campi .= ",titolo,procedimenti,allegato,allegato_1,descrizione_mod,ordine";
        	break;
        	case "normativa":
        		$campi .= ",nome,uffici,link,desc_cont,allegato_1,allegato_2,allegato_3,allegato_4";
        	break;
        	case "bilanci":
        		$campi .= ",nome,tipologia,anno,descrizione";
    			$campi .= ",allegato1,allegato2,allegato3,allegato4,allegato5,allegato6,allegato7,allegato8,allegato9,allegato10,allegato11,allegato12,allegato13,allegato14,allegato15";
        	break;
        	case "fornitori":
        		$campi .= ",nominativo,codice_fiscale,fiscale_estero,indirizzo,telefono,fax,email";
        	break;
        	case "gare":
        		$campi .= ",tipologia,contratto,denominazione_aggiudicatrice,dati_aggiudicatrice,tipo_amministrazione,sede_provincia,sede_comune,sede_indirizzo,senza_importo";
        		$campi .= ",valore_base_asta,valore_importo_aggiudicazione,importo_liquidato,data_attivazione,data_scadenza,data_scadenza_esito,data_inizio_lavori,data_lavori_fine";
        		$campi .= ",requisiti_qualificazione,codice_cpv,codice_scp,url_scp,cig,bando_collegato,oggetto,dettagli,scelta_contraente,elenco_partecipanti,elenco_aggiudicatari";
        		$campi .= ",allegato1,allegato2,allegato3,allegato4,allegato5,allegato6,allegato7,allegato8,allegato9,allegato10,allegato11,allegato12,allegato13,allegato14";
        		$campi .= ",allegato15,allegato16,allegato17,allegato18,allegato19,allegato20";
        	break;
        	case "requisiti":
        		$campi .= ",codice,denominazione";
        	break;
        	case "concorsi":
        		$campi .= ",oggetto,data_attivazione,data_scadenza,orario_scadenza,dipendenti_assunti,spesa_prevista,spese_fatte,descrizione,allegato1,alelgato2,allegato3,allegato4";
        		$campi .= ",allegato5,allegato6,allegato7,allegato8,allegato9,allegato10,allegato11,allegato12,allegato13,allegato14,allegato15,allegato16,allegato17,allegato18,allegato19,allegato20";
        	break;
        	case "sovvenzioni":
        		$campi .= ",nominativo,dati_fiscali,struttura,responsabile,data,oggetto,compenso,normativa,regolamento,note,modo_individuazione,file_atto,progetto,cv_sogetto";
        	break;
        	case "incarichi":
        		$campi .= ",nominativo,oggetto,tipo_incarico,dirigente,struttura,inizio_incarico,fine_incarico,compenso,compenso_erogato,compenso_variabile,note,estremi_atto";
        		$campi .= ",file,progetto,cv_sogetto,verifica_conflitto";
        	break;
        	case "provvedimenti":
        		$campi .= ",oggetto,tipo,struttura,data,contenuto,spesa,estremi,allegato1,allegato2,allegato3,allegato4";
        	break;
        	case "oneri":
        		$campi .= ",tipo,titolo,descrizione,procedimenti,provvedimenti,normativa,regolamenti,info,allegato1,allegato2,allegato3,allegato4";
        	break;
        	default:
        		//tipo sbagliato
        		return "";
        	break;
        }
        return $campi;
    }
    
    /**
	 * funzione usata per ritornare i campi obbligatori di un determinato archivio
	 */
    function getCampiObbligatori($tipo) {
        $campi = "";
        switch($tipo) {
        	case "strutture":
        		$campi .= "nome_ufficio,referente,email_riferimento,desc_att";
        	break;
        	case "personale":
        		$campi .= "referente,email,priorita";
        	break;
        	case "commissioni":
        		$campi .= "nome,tipo";
        	break;
        	case "societa":
        		$campi .= "ragione,tipologia";
        	break;
        	case "procedimenti":
        		$campi .= "nome,referente_proc,referente_prov,resp_sost,ufficio_def";
        	break;
        	case "regolamenti":
        		$campi .= "titolo,allegato";
        	break;
        	case "modulistica":
        		$campi .= "titolo,allegato";
        	break;
        	case "normativa":
        		$campi .= "nome";
        	break;
        	case "bilanci":
        		$campi .= "nome,tipologia,anno,allegato1";
        	break;
        	case "fornitori":
        		$campi .= "nominativo,codice_fiscale";
        	break;
        	case "gare":
        		$campi .= "tipologia,data_attivazione,oggetto";
        	break;
        	case "requisiti":
        		$campi .= "";
        	break;
        	case "concorsi":
        		$campi .= "oggetto,data_attivazione,data_scadenza";
        	break;
        	case "sovvenzioni":
        		$campi .= "nominativo,dati_fiscali,struttura,responsabile,data,oggetto,compenso,modo_individuazione,file_atto";
        	break;
        	case "incarichi":
        		$campi .= "nominativo,oggetto,tipo_incarico,compenso,file";
        	break;
        	case "provvedimenti":
        		$campi .= "oggetto,tipo,data";
        	break;
        	case "oneri":
        		$campi .= "tipo,titolo,descrizione";
        	break;
        	default:
        		//tipo sbagliato
        		return "";
        	break;
        }
        return $campi;
    }
    
    /**
	 * funzione usata per ritornare l'id di un oggetto dato il tipo
	 */
    function getIdOggetto($tipo) {
        switch($tipo) {
        	case "strutture":
        		return 13;
        	break;
        	case "personale":
        		return 3;
        	break;
        	case "commissioni":
        		return 43;
        	break;
        	case "societa":
        		return 44;
        	break;
        	case "procedimenti":
        		return 16;
        	break;
        	case "regolamenti":
        		return 19;
        	break;
        	case "modulistica":
        		return 5;
        	break;
        	case "normativa":
        		return 27;
        	break;
        	case "bilanci":
        		return 29;
        	break;
        	case "fornitori":
        		return 41;
        	break;
        	case "gare":
        		return 11;
        	break;
        	case "requisiti":
        		return 21;
        	break;
        	case "concorsi":
        		return 22;
        	break;
        	case "sovvenzioni":
        		return 38;
        	break;
        	case "incarichi":
        		return 4;
        	break;
        	case "provvedimenti":
        		return 28;
        	break;
        	case "oneri":
        		return 30;
        	break;
        }
    }
    
    /**
     * passando il $tipo = oggetto da verificare e il $campo = campo vero e proprio
     *  restituisce il suo tipo di dato (string/int)
     */
    function getTipoCampo($campo, $tipo) {
        switch($tipo) {
        	case "strutture":
        		switch($campo) {
        			case "nome_ufficio":
        			case "referenti_contatti":
        			case "email_riferimento":
        			case "email_certificate":
        			case "telefono":
        			case "fax":
        			case "desc_att":
        			case "orari":
        			case "pres_sede":
        				return "string";
        			break;
        		}
        	break;
        	case "tipologie":
        		switch($campo) {
        			case "nome_tipo":
        			case "archivio_pubblico":
        				return "string";
        			break;
        		}
        	break;
        	case "atti":
        		switch($campo) {
    				case "id_libero":
    				case "numero_pubblicazione":
        			case "mittente":
					case "provenienza_atto":
        			case "area_organizzativa":
        			case "nominativo_visualizzato":
        			case "nominativo_responsabile":
        			case "nominativo_dirigente":
					case "proroga_scadenza":
        			case "motivazioni_proroga":
        			case "oggetto":
        			case "contenuto":
					case "forma":
        			case "omissione":
					case "annullato":
        			case "motivi_annullamento":
        			case "archivio_pubblico":
        				return "string";
        			break;
        		}
        	break;
        	case "responsabili_procedimento":
        		switch($campo) {
        			case "nome":
        			case "note":
        			case "dirigente":
        				return "string";
        			break;
        		}
        	break;
        	case "responsabili_pubblicazione":
        		switch($campo) {
        			case "nome":
        			case "note":
        				return "string";
        			break;
        		}
        	break;
        	case "ente_richiedente":
        		switch($campo) {
        			case "nome":
        			case "tipo_ente":
        			case "note":
        				return "string";
        			break;
        		}
        	break;
        	case "area_organizzativa":
        		switch($campo) {
        			case "nome":
        			case "note":
        				return "string";
        			break;
        		}
        	break;
        	case "allegati":
        		switch($campo) {
        			case "contenuto":
        			case "nome_allegato":
        			case "tipo_allegato":
        			case "id_atto":
        			case "descrizione":
        				return "string";
        			break;
        		}
        	break;
        }
        //non è stringa non deve fare la LIKE
        return "int";
    }
    
	/**
	 * funzione usata per aggiungere eventuali condizioni sulle select
	 */    
    function elaboraCondizioni($arrayCondizioni, $tipo) {
    	
    	$condizione = array();
    	$campi = $this->getCampiOggetto($tipo);
    	$campi = explode(",", $campi);

    	foreach((array)$arrayCondizioni as $cond) {
    		$campo = $cond->nome;
    		$valore = $cond->valore;
			if($campo != '') {
				if(in_array($campo, $campi)) {
					if($this->getTipoCampo($campo, $tipo) == 'string') {
						$condizione['condizione'] .= " AND (".$campo." LIKE '%".addslashes($valore)."%') ";
					} else {
						$condizione['condizione'] .= " AND (".$campo." = '".addslashes($valore)."') ";
					}
				} else {
					$condizione['errore'] = "campo_non_valido";
					$condizione['campo_non_valido'] = $campo;
					$condizione['condizione'] = "";
				}
			}
    	}
    	return $condizione;
    }
    
    /**
     * messaggio restituito quando non si hanno i permessi per una certa operazione richiesta
     */
    function permessiNegatiResponse($message = "Non si hanno i permessi. Autorizzazione negata.") {
        $response = new ResponseWS();
        $response->message = $message;
        return $response;
    }

	/**
	 * funzione applicata a tutti i dati restituiti dal webservice
	 */
    function utf8_encode_recursive($input, $encode_keys=true) {
        if(is_array($input)) {
            $result = array();
            foreach($input as $k => $v) {
                $key = ($encode_keys)? utf8_encode($k) : $k;
                $result[$key] = $this->utf8_encode_recursive( $v, $encode_keys);
            }
        }
        else {
            $result = utf8_encode($input);
        }
        return $result;
    }
	
    /**
     * sistema i valori passati alla request in modo che la stessa abbia sempre la stessa struttura
     */
    function fixRequest($request) {
	
    	$request->value = array();
        $request->campiRequest = array();
        
        if(!is_array($request->valori)) {
        	$valori = $request->valori;
        	$request->valori = array();
        	$request->valori[0] = $valori;
        }
        foreach((array)$request->valori as $valore) {
        	$request->value[$valore->chiave] = $valore->valore;
        }

        if(!is_array($request->campi)) {
        	$campi = $request->campi;
        	$request->campi = array();
        	$request->campi[0] = $campi;
        }
        foreach((array)$request->campi as $valore) {
			//$valore->valore = ($valore->valore);
        	$request->campiRequest[] = $valore;
        }
        
    	return $request;
    }
    
    function logRequest($request, $response, $metodoWS) {
    	global $dati_db,$database,$configurazione;
    	
    	try {
    		$tabella = mostraDatoOggetto($this->utenteEnte['ente_id'], 2, "tabella");
			if($tabella == '') {
    			$tabella = 'errore';
    		}
			if($this->utenteEnte['ente_id'] == '') {
    			$this->utenteEnte['ente_id'] = 0;
				$this->utenteEnte['id'] = 0;
    		}
        		
    		$sql = "SELECT * FROM ".$dati_db['prefisso']."logws_".$tabella." LIMIT 1";
			if ( !($risultato = $database->connessioneConReturn($sql)) ) {
				//creare la tabella
				$sql= "CREATE TABLE logws_".$tabella." (" .
						" id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT," .
						" ente_id VARCHAR(255)," .
						" id_utente INTEGER(11) UNSIGNED," .
						" data_azione INTEGER UNSIGNED," .
						" tipo_operazione VARCHAR(255)," .
						" request TEXT," .
						" response TEXT," .
						" PRIMARY KEY (id)) TYPE=MyISAM";
				$result = $database->connessioneConReturn($sql);
			}
			$sql = "INSERT INTO logws_".$tabella." (" .
					" ente_id," .
					" id_utente," .
					" data_azione," .
					" tipo_operazione," .
					" request," .
					" response" .
					" ) VALUES (" .
					" '".$this->utenteEnte['ente_id']."'," .
					" ".$this->utenteEnte['id']."," .
					" ".mktime()."," .
					" '".addslashes($metodoWS)."'," .
					" '".addslashes(serialize($request))."'," .
					" '".addslashes(serialize($response))."'" .
					" )";
			if ( !($risultato = $database->connessioneConReturn($sql)) ) {
				$fileReport = fopen("temp/reportWebServices.log", "a+");
				fwrite($fileReport, $sql."/n/r");
				fclose($fileReport);
			}
			
		} catch( Exception $e ) {}
    	return;
    }

}


ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer($server_url."webservice/webservice2.wsdl");
$server->setClass("ServerWS2");
$server->handle();

// Chiudo la connessione al database
$database->sqlChiudi();

?>