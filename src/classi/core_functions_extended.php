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
	 * classi/core_functions_extended.php
	 * 
	 * @Descrizione
	 * Estensione di metodi che vengono utilizzati nel CORE applicativo ISWEB. Contiene funzioni di utilità di PAT
	 *
	 */
 
// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function datoEnte($idEnte = 0, $campo='') {
	global $dati_db,$database;
	
	if ($idEnte != 0) {
	
		if ($campo=='') {
			$campo='*';
		}

		$sql = "SELECT ".$campo." FROM ".$dati_db['prefisso']."etrasp_enti WHERE id=".$idEnte;
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero del dato sugli enti');
		}
		$riga = $database->sqlArray($result);
		
		if ($campo=='*') {
			//prelevo anche i moduli attivi
			$sql = "SELECT modulo,attivo FROM ".$dati_db['prefisso']."etrasp_moduli WHERE id_ente=".$idEnte;
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero del moduli ente ');
			}
			$moduli = array();
			$mod = $database->sqlArrayAss($result);
			foreach((array)$mod as $m) {
				if($m['attivo'] > 0) {
					$moduli[$m['modulo']] = 1;
				}
			}
			$riga['moduli_attivi'] = $moduli;
			return $riga;
		} else {
			return $riga[$campo];
		}

	} 

	return FALSE;
}

function datoTipoEnte($idTipo = 0, $campo = '') {
	global $dati_db,$database;
	
	if ($idTipo != 0) {
	
		if ($campo=='') {
			$campo='*';
		}

		$sql = "SELECT ".$campo." FROM ".$dati_db['prefisso']."oggetto_etrasp_tipoentisemplice WHERE id=".$idTipo;
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero del dato sul tipo di enti');
		}
		$riga = $database->sqlArray($result);
		
		if ($campo=='*') {
			return $riga;
		} else {
			return $riga[$campo];
		}

	} 

	return FALSE;
}

// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function aggiungiModelloTrasp($idEnte,$idSezione,$arrayValori) {
	global $dati_db,$database,$datiUser;
	
	if ($idSezione != 0 AND $idEnte != 0) {

		$oraCorrente = time();

		// creo la query di installazione nuovo listino
		$sql = "INSERT INTO ".$dati_db['prefisso']."oggetto_etrasp_modello (
						stato,id_proprietario,data_creazione,ultima_modifica,id_sezione,id_lingua,
						id_ente,id_sezione_etrasp,html_generico,modulistica_tit,modulistica,modulistica_opz,
						normativa_tit,normativa,normativa_opz,referenti_tit,referenti,referenti_opz,
						regolamenti_tit,regolamenti,regolamenti_opz,procedimenti_tit,procedimenti,procedimenti_opz,
						provvedimenti_tit,provvedimenti,provvedimenti_opz,strutture_tit,strutture,strutture_opz) VALUES (
						1,".$datiUser['id'].",".$oraCorrente.",".$oraCorrente.",0,0,
						".$idEnte.",".$idSezione.",'".$arrayValori['html_generico']."','".$arrayValori['modulistica_tit']."','".$arrayValori['modulistica']."','".$arrayValori['modulistica_opz']."',
						'".$arrayValori['normativa_tit']."','".$arrayValori['normativa']."','".$arrayValori['normativa_opz']."','".$arrayValori['referenti_tit']."','".$arrayValori['referenti']."','".$arrayValori['referenti_opz']."',
						'".$arrayValori['regolamenti_tit']."','".$arrayValori['regolamenti']."','".$arrayValori['regolamenti_opz']."','".$arrayValori['procedimenti_tit']."','".$arrayValori['procedimenti']."','".$arrayValori['procedimenti_opz']."',
						'".$arrayValori['provvedimenti_tit']."','".$arrayValori['provvedimenti']."','".$arrayValori['provvedimenti_opz']."','".$arrayValori['strutture_tit']."','".$arrayValori['strutture']."','".$arrayValori['strutture_opz']."')";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("Errore in installazione nuovo modello contenuto. Rivolgersi all'amministratore.");
		}

		
		return true;

	} 

	return FALSE;
}

// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function modificaDatoModelloTrasp($id,$idSezione,$valori,$campi = 0) {
	global $dati_db,$database,$datiUser;
	
	if ($id != 0) {

		$oraCorrente = time();
	
		if (!$campi) {
			$campi = array('html_generico');
			$valori = array($valori);
		}
	
		// ELABORO LA CONDIZIONE
		$update = '';
		for($i=0;$i<count($campi);$i++) {
			if ($update!='') {
				$update .= ',';
			}
			$update .= $campi[$i]."='".$valori[$i]."'";
		}
	
		$sql = "UPDATE ".$dati_db['prefisso']."oggetto_etrasp_modello SET id_proprietario=".$datiUser['id'].",ultima_modifica=".$oraCorrente.",".$update." WHERE id=".$id;
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante modifica modello PAT');
		}
		
		return TRUE;
	} 

	return FALSE;
}

// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function datoModelloTrasp($idEnte,$idSezione) {
	global $dati_db,$database;
	
	if ($idSezione != 0 AND $idEnte != 0) {

		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente=".$idEnte." AND id_sezione_etrasp=".$idSezione." LIMIT 0,1";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero del modello PAT');
		}
		$riga = $database->sqlArray($result);
		
		if (is_array($riga)) {		
			return $riga;
		} else {
			return FALSE;
		}

	} 

	return FALSE;
}

// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function datoGuidaTrasp($idSezione) {
	global $dati_db,$database;
	
	if ($idSezione != 0) {

		
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_help WHERE id_sezione_etrasp=".$idSezione." LIMIT 0,1";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero della gyuda');
		}
		//echo "test: ".$sql;
		$riga = $database->sqlArray($result);
		
		return $riga;

	} 

	return FALSE;
}

// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function caricaEnti($condizione='') {
	global $dati_db,$database;


	$sql = "SELECT id,nome_completo_ente FROM ".$dati_db['prefisso']."etrasp_enti ".$condizione;
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		die('Errore durante il recupero di tutti gli enti (con condizione)');
	}
	$enti = $database->sqlArrayAss($result);

	return $enti;
}

// mostra dato di un oggetto, dato il suo id e l'id dell'oggetto
function caricaAcl($ruoli) {
	global $dati_db,$database;

	if ($ruoli != '') {
		$acl = explode(',',$ruoli);
		$condizione = '';
		foreach ($acl as $id) {
			if ($condizione != '') {
				$condizione .= ' OR ';
			}
			$condizione .= 'id='.$id;
		}
		$sql = "SELECT * FROM ".$dati_db['prefisso']."etrasp_ruoli WHERE ".$condizione;
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero dei ruoli utente');
		}
		$ruoli = $database->sqlArrayAss($result);

		return $ruoli;
	} else {
		return false;
	}
}

///// FUNZIONE DI RITORNO DEL GIUSTO INPUT PER IL CAMPO (MODIFICA PAT)
function creaFormTrasp($label,$tipo, $nome, $valori='', $valoreVero = '', $etichette = '',$classe='',$prop=0, $etiCampo='',$escludiHtml = 0,$disabilitato=0,$obbligatorio=false,$help=false,$attributi = array()) {
	global $configurazione,$tags,$oggetti,$server_url,$datiUser,$dati_db,$database, $istanzaOggetto,$idEnteAdmin, $idEnte, $menu, $oggOgg, $aclTrasparenza, $menuSecondario, $box;

	include('./classi/regole/creaFormTrasp.php');
}

function controllaSezione($id) {
	global $sezioni;

	$superCategoria = 0;
	$arraySottosezioni = array();
	foreach ($sezioni as $sezione) {
		if ($sezione['id_riferimento'] == $id) {
			$superCategoria = 1;
			$arraySottosezioni[] = $sezione;
		}
	}
	if ($superCategoria) {
		return $arraySottosezioni;
	} else {
		return FALSE;
	}

}

function traduciOrgani($nomi) {
	global $tipoEnte;
	
	$arrayNomi = explode(',',$nomi);
	$ritorno = '';
	foreach ($arrayNomi as $nome) {
		if ($ritorno !='') {
			$ritorno .= ', ';
		}
		if ($nome == 'sindaco') $ritorno .= $tipoEnte['org_sindaco']; 
		if ($nome == 'vicesindaco') $ritorno .= $tipoEnte['org_vicesindaco']; 
		if ($nome == 'giunta comunale') $ritorno .= $tipoEnte['org_giunta']; 
		if ($nome == 'presidente consiglio comunale') $ritorno .= $tipoEnte['org_presidente']; 
		if ($nome == 'consiglio comunale') $ritorno .= $tipoEnte['org_consiglio']; 
		if ($nome == 'direzione generale') $ritorno .= $tipoEnte['org_direzione']; 
		if ($nome == 'segretario generale') $ritorno .= $tipoEnte['org_segretario']; 
		if ($nome == 'commissioni') $ritorno .= $tipoEnte['org_commissioni']; 		
	}
	return $ritorno;
	
}

?>