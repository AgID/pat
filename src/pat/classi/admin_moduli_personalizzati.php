<?
class moduliPersonalizzati {

	function caricaModuli() {
		global $database, $dati_db;

		$sql="SELECT * FROM ".$dati_db['prefisso']."etrasp_moduli";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("errore in carica moduli. ".$sql);
		}
		$lista = $database->sqlArrayAss($result);
		return $lista;
	}

	// funzione di caricamento singolo modulo
	function caricaModulo($id) {
		global $database, $dati_db;

		$sql="SELECT * FROM ".$dati_db['prefisso']."etrasp_moduli WHERE id=$id";
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso caricare modulo '.$sql);
		}
		$modulo = $database->sqlArray($result);
		return $modulo;
	}
	
	////////////////////// FUNZIONE DI AGGIUNTA DI UN MODULO//////////////////////////

	function aggiungiModulo($arrayValori) {
		global $dati_db,$database,$datiUser;
		
		// verifico valori
		$arrayValori['id_ente'] = isset($arrayValori['id_ente']) ? $arrayValori['id_ente'] : 0;
		$arrayValori['data_attivazione'] = isset($arrayValori['data_attivazione']) ? $arrayValori['data_attivazione'] : mktime();
		$arrayValori['attivo'] = isset($arrayValori['attivo']) ? $arrayValori['attivo'] : 0;
		
		// creo la query di inserimento
		$sql = "INSERT INTO ".$dati_db['prefisso']."etrasp_moduli (
							id_ente,data_attivazione,attivo,modulo
							) VALUES (
							".$arrayValori['id_ente'].",".$arrayValori['data_attivazione'].",".$arrayValori['attivo'].",'".$arrayValori['modulo']."'
							)";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die('Non posso installare modulo '.$sql);
		} else {
			return true;
		}
	}
	
	////////////////////// FUNZIONE DI MODIFICA//////////////////////////

	function modificaModulo($id, $arrayValori) {
		global $dati_db,$database;
		
		// verifico valori
		$arrayValori['id_ente'] = isset($arrayValori['id_ente']) ? $arrayValori['id_ente'] : 0;
		$arrayValori['data_attivazione'] = isset($arrayValori['data_attivazione']) ? $arrayValori['data_attivazione'] : mktime();
		$arrayValori['attivo'] = isset($arrayValori['attivo']) ? $arrayValori['attivo'] : 0;
		
		// creo array di esclusioni
		$escludi = array(
			'id_ente',
			'rispostaForm',
			'data_attivazioneVis'
		);
		
		$strQuery = "id_ente = ".$arrayValori['id_ente'];
		// creo la query in base all'array inviatomi e non alla struttura
		$chiaviValori = array_keys($arrayValori);		
		foreach ($chiaviValori as $campoTemp) {
			// correzioni dei valori di campi, esludo i campi			
			if (!(in_array($campoTemp,$escludi))) {
				$strQuery .= ",".$campoTemp."='".$arrayValori[$campoTemp]."'";
			}
		}

		// modifica
		$sql = "UPDATE ".$dati_db['prefisso']."etrasp_moduli SET ".$strQuery." WHERE id = ".$id;
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso aggiornare modulo '.$sql);
		} else {
			return TRUE;
		}

	}
	
	// funzione di cancellazione (AZIONE MULTIPLA)
	function cancellaModuli($stringaOggetti) {
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
		$sql = "DELETE FROM ".$dati_db['prefisso']."etrasp_moduli WHERE ".$condizione;
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso cancellare moduli: '.$sql);
		}
		return $numeroOggetti+1;
	}
}

?>
