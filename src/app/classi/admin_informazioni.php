<?

class infoAdmin {

	var $oggetti = array();
	var $categorie = array();
	var $categoria;
	var $strutturaTipo;
	var $strutturaEtichette;
	var $strutturaDefault;
	var $strutturaValori;
	var $strutturaProprieta;
	var $strutturaStili;
	var $strutturaMaxchar;
	var $strutturaOrdinamento;
	var $proprieta;
	var $amministrazione;
	var $speciale;
	var $specialeCampo;
	var $interfaccia;
	var $upload_multiplo;
	var $campo_default;
	var $ecommerce;
	var $autorizzazione;
	var $tabellaOggetto;
	var $ultimaAggStile = 0;
	var $idOggetto = 0;
        var $idOggettoWS;
	var $ultimaAggStileBanner = 0;
	var $struttura = array();

	// costruttore
	function infoAdmin() {
		global $oggetti,$categorie;

		$this->oggetti = $oggetti;
		$this->categorie = $categorie;

	}

	// funzione di aggiornamento array oggetti, da usare dopo una modifica. Non restituisce nulla
	function refreshOggetti() {
		global $coreInfo;

		$this->oggetti = array();
		$this->oggetti = $coreInfo->loadingOggetti();
		$this->categorie = array();
		$this->categorie = $coreInfo->loadingCatOggetti();
	}

	// funzione di caricamento singolo oggetto, memorizza anche in buffer dell'oggetto la struttura personalizzata
	function caricaOggetto($id,$strutturaCompleta = 'no') {
		foreach($this->oggetti as $oggetto) {
			if ($oggetto['id'] == $id) {
				// memorizzo le impostazioni sulla attuale struttura dell'oggetto
				$this->strutturaTipo = $oggetto['struttura_tipo'];
				$this->idOggetto = $id;
				$this->strutturaValori = $oggetto['struttura_valori'];
				$this->strutturaEtichette = $oggetto['struttura_etichette'];
				$this->strutturaDefault = $oggetto['struttura_default'];
				$this->strutturaProprieta = $oggetto['struttura_proprieta'];
				$this->strutturaMaxchar = $oggetto['struttura_maxchar'];
				$this->strutturaOrdinamento = $oggetto['struttura_ordinamento'];
				$this->tabellaOggetto = $oggetto['tabella'];
				$this->proprieta = $oggetto['proprieta'];
				$this->amministrazione = $oggetto['amministrazione'];
				$this->interfaccia = $oggetto['interfaccia'];
				$this->speciale = $oggetto['int_speciale'];
				$this->specialeCampo = $oggetto['int_speciale_campo'];
				$this->categoria = $oggetto['id_categoria'];
				$this->campo_default = $oggetto['campo_default'];
				$this->autorizzazione = $oggetto['richiesta_autorizzazione'];
				$this->upload_multiplo = $oggetto['upload_multiplo'];

				//TODO NICO: OK! se l'oggetto è webservice non fare il parsing struttura classico ma chiamare il web services.
				if($oggetto['id_server_ws'] != 0 ) {
                    $this->idOggettoWS = $oggetto['id_oggetto_ws'];
					$this->struttura = $this->parsingStruttura($strutturaCompleta, 'webservice');
                    $this->tabellaOggetto = $oggetto['tabella'];
					$oggetto['webservice'] = 'webservice';
				} else {
					$this->struttura = $this->parsingStruttura($strutturaCompleta);
				}
				return $oggetto;
			}
		}
	}


	//////////////////////////FUNZIONE DI PARSING DELLA STRUTTURA DELL'OGGETTO ESCLUDENDO I CAMPI DI DEFAULT////
	//////////////////////////richiamata dal  caricamento di un singolo oggetto///////////////////////////

	function parsingStruttura($completa='no', $webservice = 'no') {
		global $database,$dati_db,$configurazione, $datiUser;

		//TODO NICO: OK!
		if($webservice == 'webservice' and $this->idOggettoWS != -2) {

			$sql = "SELECT struttura FROM ".$dati_db['prefisso']."oggetti_strutture_webservice"
			." WHERE id_oggetto=".$this->idOggetto;
			if( !($result = $database->connessioneConReturn($sql)) ) {
				die("errore in parsingStruttura".$sql);
			}
			$campo = $database->sqlArrayAss($result, MYSQL_BOTH);
			$struttura = unserialize($campo[0][0]);
			return $struttura;
		}

		// prendo i nomi dei campi e il tipo di input dal database
		$sql = "SELECT * FROM ".$dati_db['prefisso'].$this->tabellaOggetto." limit 0,1";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("errore in parsingStruttura".$sql);
		}

		$numeroCampi = $database->sqlNumCampi($result);

		$struttura = array();

		// analizzo le proprieta' dell'oggetto per vedere quanti sono i campi di default
		switch ($this->proprieta) {
			case "contatto":
				$numTemp = 6;
				break;
			default:
				$numTemp = 16;
				break;
		}
		$numTempVecchio = 0;
		if ($completa != 'no') {
			$numTempVecchio = $numTemp;
			$numTemp = 0;
		}

		for ($i=$numTemp;$i<$numeroCampi;$i++) {
			$struttura[] = array(
				'nomecampo' => $database->sqlNomeCampo($i,$result),
				'tipoinput' => $database->sqlTipoCampo($i,$result)
			);
		}

		// ora parso i valori in stringa sulla struttura
		$arrayValori = explode("{",$this->strutturaValori);
		$arrayEtichette = explode("{",$this->strutturaEtichette);
		$arrayDefault = explode("{",$this->strutturaDefault);
		$arrayTipi = explode("{",$this->strutturaTipo);
		$arrayProprieta = explode("{",$this->strutturaProprieta);
		$arrayOrdinamento = explode("{",$this->strutturaOrdinamento);
		$arrayMaxchar = explode("{",$this->strutturaMaxchar);
		$arrayStili = explode("{",$this->strutturaStili);
		$numeroValori = count($arrayValori)-1;
		$arrayPers = array();
		for ($i=0;$i<($numeroCampi-$numTemp);$i++) {
			if ($numTemp == 0 and $i<$numTempVecchio) {
				// campi di sistema
				// campi persionalizzati
				switch ($struttura[$i]['nomecampo']) {
					case "data_creazione":
						$struttura[$i]['etichetta'] = "CORE.data di inserimento";
						$struttura[$i]['tipocampo'] =  'data_calendario';
						break;
					case "id_proprietario":
						$struttura[$i]['etichetta'] = "CORE.utente proprietario";
						$struttura[$i]['tipocampo'] = 'campoutente';
						break;
					case "ultima_modifica":
						$struttura[$i]['etichetta'] = "CORE.data di ultima modifica";
						$struttura[$i]['tipocampo'] =  'data_calendario';
						break;
					case "id_sezione":
						$struttura[$i]['etichetta'] = "CORE.categoria di appartenenza";
						$struttura[$i]['tipocampo'] = 'campocatoggetto';
						break;
					case "id_lingua":
						$struttura[$i]['etichetta'] = "CORE.lingua di pubblicazione";
						break;
					case "numero_letture":
						$struttura[$i]['etichetta'] = "CORE.numero di letture";
						$struttura[$i]['tipocampo'] =  'numerico';
					case "permessi_lettura":
						$struttura[$i]['etichetta'] = "CORE.permessi di lettura";
						$struttura[$i]['tipocampo'] =  'text';
						break;
					default:
						$struttura[$i]['etichetta'] = "CORE.RISERVATO.".$struttura[$i]['nomecampo'];
						$struttura[$i]['tipocampo'] = 'text';
				}
				$struttura[$i]['default'] =1;
				$struttura[$i]['stile'] =0;
				$struttura[$i]['campo_def'] =1;

			} else {
				// campi persionalizzati
				$struttura[$i]['valorecampo'] = $arrayValori[$i-$numTempVecchio];
				$struttura[$i]['tipocampo'] =$arrayTipi[$i-$numTempVecchio];
				$struttura[$i]['etichetta'] =$arrayEtichette[$i-$numTempVecchio];
				$struttura[$i]['default'] =$arrayDefault[$i-$numTempVecchio];
				$struttura[$i]['proprieta'] =$arrayProprieta[$i-$numTempVecchio];
				$struttura[$i]['maxchar'] =$arrayMaxchar[$i-$numTempVecchio];
				$struttura[$i]['stile'] =$arrayStili[$i-$numTempVecchio];
				$struttura[$i]['ordinamento'] =$arrayOrdinamento[$i-$numTempVecchio];
				$struttura[$i]['campo_def'] =0;
			}
		}

		return $struttura;

	}

	// funzione di importazione  oggetto da file
	function importOggetto($arrayDati,$arrayValori) {
		global $database,$dati_db,$uploadPath;

		// forzo lettere minuscole sul nome del db
		$arrayValori['nomedb'] = strtolower($arrayValori['nomedb']);

		//DUPLICAZIONE STRUTTURALE E DATI DELLE TABELLE
		$queryTabella = eregi_replace($arrayDati['tabella'],"oggetto_".$arrayValori['nomedb'],$arrayDati['sqltabella']);
		//echo "Tabella: ".$queryTabella."<br />";
		if ( !($result = $database->connessioneConReturn($queryTabella)) ) {
			die("Errore in importazione nuova tabella. Rivolgersi all'amministratore.".$queryTabella);
		}
		if ($arrayDati['proprieta'] != 'contatto') {
			$queryTabellaBack = eregi_replace($arrayDati['tabella']."_backup","oggetto_".$arrayValori['nomedb']."_backup",$arrayDati['sqltabellabackup']);
			//echo "Tabella backup: ".$queryTabellaBack."<br />";
			if ( !($result = $database->connessioneConReturn($queryTabellaBack)) ) {
				die("Errore in duplicazione nuova tabella backup. Rivolgersi all'amministratore.".$queryTabellaBack);
			}
		}
		// ora creo la query di inserimento
		$sql = "INSERT INTO ".$dati_db['prefisso']."oggetti (";
		$nomiCampi = "";
		$valoriCampi = "";
		foreach ($arrayDati as $chiave => $valore) {
			//echo "Analizzo chiave: ".$chiave." con valore: ".$valore."<BR />";
			if ($chiave != 'sqltabella' and $chiave != 'sqltabellabackup' and is_string($chiave)) {
				if ($nomiCampi != '') {
					$nomiCampi .= ",";
				}
				if ($valoriCampi != '') {
					$valoriCampi .= ",";
				}
				$nomiCampi .= $chiave;
				if ($chiave == 'nome' OR $chiave == 'nomedb') {
					// stringhe
					$valoriCampi .= "'".$arrayValori[$chiave]."'";
				} else if ($chiave == 'commenti' OR $chiave == 'id_proprietario' OR $chiave == 'versioning' OR $chiave == 'id_categoria') {
					// NUMERI
					$valoriCampi .= $valore;
				} else if ($chiave == 'tabella') {
					$valoriCampi .= "'oggetto_".$arrayValori['nomedb']."'";
				} else if ($chiave == 'ricercabile') {
					$valoriCampi .= $valore;
				} else {
					if (is_string($valore)) {
						$valoriCampi .= "'".addslashes($valore)."'";
					} else {
						$valoriCampi .= $valore;
					}
				}
			}
		}
		$sql = $sql.$nomiCampi.") VALUES (".$valoriCampi.")";
		//print "Query: ".$sql."<br />";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("Errore in installazione nuovo oggetto. Rivolgersi all'amministratore.".$sql);
		}
		// per la memorizzazione degli allegati creo la cartella nel download con lo stesso nome della tabella oggetto ed una sottocartlela temporanea per il workflow
		mkdir($uploadPath."oggetto_".$arrayValori['nomedb']);
		mkdir($uploadPath."oggetto_".$arrayValori['nomedb']."/temp");
		mkdir($uploadPath."oggetto_".$arrayValori['nomedb']."/import");
		// impostazione permessi completi
		chmod($uploadPath."oggetto_".$arrayValori['nomedb'], 0777);
		chmod($uploadPath."oggetto_".$arrayValori['nomedb']."/temp", 0777);
		chmod($uploadPath."oggetto_".$arrayValori['nomedb']."/import", 0777);

		return mysql_insert_id($database->db_connect_id);

	}

	// funzione di caricamento proprietà oggetto
	function caricaImportazioni($idOggetto) {
		global $dati_db, $database, $datiUser;

		if ($idOggetto) {
		
			$condEnte = "";
			// personalizzazione enti trasparenza
			if ($datiUser['id_ente_admin']) {
				$condEnte = " AND id_ente=".$datiUser['id_ente_admin'];
			}
			
			$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti_import WHERE id_oggetto=".$idOggetto." ".$condEnte." ORDER BY data_importazione DESC";
			if( !($result = $database->connessioneConReturn($sql)) ) {
				die("Errore in caricamento campi di default oggetto. Rivolgersi all'amministratore.".$sql);
			}
			return $database->sqlArrayAss($result);
		}
		return array();
	}

	// funzione di caricamento proprietà oggetto
	function settingsImportDefault($idOggetto) {
		global $dati_db, $database, $datiUser;

		if ($idOggetto) {
		
			$condEnte = "";
			// personalizzazione enti trasparenza
			if ($datiUser['id_ente_admin']) {
				$condEnte = " AND id_ente=".$datiUser['id_ente_admin'];
			}
		
			$sql = "SELECT settings FROM ".$dati_db['prefisso']."oggetti_import WHERE id_oggetto=".$idOggetto." ".$condEnte." AND dati_default=1";
			if(!($result = $database->connessioneConReturn($sql)) ) {
				die("Errore in caricamento campi di default oggetto. Rivolgersi all'amministratore.".$sql);
			}
			$risultato = $database->sqlArray($result);
			if (is_array($risultato)) {
				return unserialize($risultato['settings']);
			}
		}
		return false;
	}
	function proprietaImportDefault($idOggetto) {
		global $dati_db, $database, $datiUser;
	
		if ($idOggetto) {
	
			$condEnte = "";
			// personalizzazione enti trasparenza
			if ($datiUser['id_ente_admin']) {
				$condEnte = " AND id_ente=".$datiUser['id_ente_admin'];
			}
	
			$sql = "SELECT proprieta FROM ".$dati_db['prefisso']."oggetti_import WHERE id_oggetto=".$idOggetto." ".$condEnte." AND dati_default=1";
			if(!($result = $database->connessioneConReturn($sql)) ) {
				die("Errore in caricamento campi di default oggetto. Rivolgersi all'amministratore.".$sql);
			}
			$risultato = $database->sqlArray($result);
			if (is_array($risultato)) {
				return unserialize($risultato['proprieta']);
			}
		}
		return false;
	}
	function valoriImportDefault($idOggetto) {
		global $dati_db, $database, $datiUser;
	
		if ($idOggetto) {
	
			$condEnte = "";
			// personalizzazione enti trasparenza
			if ($datiUser['id_ente_admin']) {
				$condEnte = " AND id_ente=".$datiUser['id_ente_admin'];
			}
	
			$sql = "SELECT valori FROM ".$dati_db['prefisso']."oggetti_import WHERE id_oggetto=".$idOggetto." ".$condEnte." AND dati_default=1";
			if(!($result = $database->connessioneConReturn($sql)) ) {
				die("Errore in caricamento campi di default oggetto. Rivolgersi all'amministratore.".$sql);
			}
			$risultato = $database->sqlArray($result);
			if (is_array($risultato)) {
				return unserialize($risultato['valori']);
			}
		}
		return false;
	}

	// funzione di caricamento proprietà oggetto
	function importUndo($idOggetto,$dataImport) {
		global $dati_db, $database, $datiUser;


		//echo "data: ".$dataImport." oggetto: ".$idOggetto;
		if ($dataImport and $idOggetto) {
		
			$condEnte = "";
			// personalizzazione enti trasparenza
			if ($datiUser['id_ente_admin']) {
				$condEnte = " AND id_ente=".$datiUser['id_ente_admin'];
			}
		
			// verifico se dopo questo import sono presenti dei reset
			$sql = "SELECT count(*) as presente FROM ".$dati_db['prefisso']."oggetti_import WHERE data_importazione>".$dataImport." ".$condEnte." AND reset_import=1 AND id_oggetto=".$idOggetto;
			if(!($result = $database->connessioneConReturn($sql)) ) {
				die("Errore in importUndo. Rivolgersi all'amministratore.".$sql);
			}
			$risultato = $database->sqlArray($result);
				
			if ($risultato['presente']) {
				return false;
			}
			return true;
		}
		return false;
	}

	// funzione di creazione categoria dato il nome dell'oggetto
	function aggiungiImport($arrayValori) {
		global $dati_db, $database;

		// se è un default, resetto i precedenti
		if ($arrayValori['default']==1) {
			$sql = "UPDATE ".$dati_db['prefisso']."oggetti_import SET dati_default=0 WHERE dati_default=1 AND id_ente=".$arrayValori['id_ente']." AND id_oggetto=".$arrayValori['oggetto'];
			if( !($result = $database->connessioneConReturn($sql)) ) {
				die("Errore in rest default import oggetto. Rivolgersi all'amministratore.".$sql);
			}
		} else {
			$arrayValori['default']=0;
		}
		// installo l'IMPORT
		$sql = "INSERT INTO ".$dati_db['prefisso']."oggetti_import (id_utente,id_oggetto,id_categoria,id_ente,num_suc,num_err,reset_import,dati_default,data_importazione,file,settings,proprieta,valori,id_inseriti,report) VALUES 
				(".$arrayValori['utente'].",".$arrayValori['oggetto'].",".$arrayValori['categoria'].",".$arrayValori['id_ente'].",".$arrayValori['num_suc'].",".$arrayValori['num_err'].",".$arrayValori['reset_dati'].",".$arrayValori['default'].",".time().",\"".addslashes($arrayValori['file'])."\",\"".addslashes(stripslashes(stripslashes($arrayValori['settings'])))."\",\"".addslashes(stripslashes(stripslashes($arrayValori['proprieta'])))."\",\"".addslashes(stripslashes(stripslashes($arrayValori['valori'])))."\",\"".$arrayValori['id_inseriti']."\",\"".addslashes(stripslashes(stripslashes($arrayValori['report'])))."\")";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("Errore in installazione storico import oggetto. Rivolgersi all'amministratore.".$sql);
		}
		return mysql_insert_id($database->db_connect_id);
	}

}

?>
