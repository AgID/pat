<?
/////////////////////////////////FUNZIONI PER GLI ATTI DALL'ALBO ONLINE////////////////////////////////////////

function prendiAttiImportati($idAtto, $enteAdmin) {
	global $dati_db, $database;
	
	$sql = "SELECT * FROM " . $dati_db['prefisso'] . "etrasp_atti_ealbo WHERE id_atto_albo = ".$idAtto." AND id_ente_albo = ".$enteAdmin['id_ente_albo']." AND id_ente = ".$enteAdmin['id'];
	if (!($result = $database->connessioneConReturn($sql))) {
		die("Database non installato o non disponibile: errore critico.");
	}
	return $database->sqlArrayAss($result);
}
function prendiAttoImportato($idAtto, $enteAdmin, $idOggetto) {
	global $dati_db, $database;
	
	$sql = "SELECT * FROM " . $dati_db['prefisso'] . "etrasp_atti_ealbo WHERE id_atto_albo = ".$idAtto." AND id_oggetto = ".$idOggetto." AND id_ente_albo = ".$enteAdmin['id_ente_albo']." AND id_ente = ".$enteAdmin['id'];
	if (!($result = $database->connessioneConReturn($sql))) {
		die("Database non installato o non disponibile: errore critico.");
	}
	return $database->sqlArray($result);
}

function prendiOggettoImportato($idAtto, $idOggetto, $enteAdmin) {
	global $dati_db, $database;
	
	$sql = "SELECT * FROM " . $dati_db['prefisso'] . "etrasp_atti_ealbo WHERE id_atto_albo = ".$idAtto." AND id_oggetto = ".$idOggetto." AND id_ente_albo = ".$enteAdmin['id_ente_albo']." AND id_ente = ".$enteAdmin['id'];
	if (!($result = $database->connessioneConReturn($sql))) {
		die("Database non installato o non disponibile: errore critico.");
	}
	return $database->sqlArrayAss($result);
}

function caricaDocumentoEAlbo($oggetto, $idDocumento) {
	global $dati_db_albo,$dbAlbo,$tabelleEalbo;
	
	$datiDaCaricare = '*';
	$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db_albo['prefisso'].$tabelleEalbo[$oggetto]." WHERE id=$idDocumento";
	if (!($result = $dbAlbo->connessioneConReturn($sql))) {
		return array();
	}
	$doc = $dbAlbo->sqlArray($result);
	return $doc;
	
}

function escludiAttoEAlbo($atto) {
	global $dati_db_albo,$dbAlbo,$tabelleEalbo;
	$sql = "UPDATE ".$dati_db_albo['prefisso']."oggetto_atto SET eTrasparenza = -1 WHERE id=".$atto['id'];
	if (!($result = $dbAlbo->connessioneConReturn($sql))) {
		return false;
	}
	return true;
}
function includiAttoEAlbo($atto) {
	global $dati_db_albo,$dbAlbo,$tabelleEalbo;
	$sql = "UPDATE ".$dati_db_albo['prefisso']."oggetto_atto SET eTrasparenza = 0 WHERE id=".$atto['id'];
	if (!($result = $dbAlbo->connessioneConReturn($sql))) {
		return false;
	}
	return true;
}

function caricaAllegatiEAlbo($idAttoAllegati) {
	global $dati_db_albo,$dbAlbo;
	
	$datiDaCaricare = '*';
	$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db_albo['prefisso']."oggetto_allegati_atto WHERE id_atto = '".$idAttoAllegati."'";
	if (!($result = $dbAlbo->connessioneConReturn($sql))) {
		return array();
	}
	$doc = $dbAlbo->sqlArrayAss($result);
	return $doc;
	
}

function caricaAllegatoEAlbo($id) {
	global $dati_db_albo,$dbAlbo;
	
	$datiDaCaricare = '*';
	$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db_albo['prefisso']."oggetto_allegati_atto WHERE id = '".$id."'";
	if (!($result = $dbAlbo->connessioneConReturn($sql))) {
		return array();
	}
	$doc = $dbAlbo->sqlArray($result);
	return $doc;
}

function visualizzaListaOggettiEAlbo($inizio = 0,$limite = 0,$campoOrdine = 'nessuno', $ordine = 'nessuno', $idEnte = 0, $condizioneAgg = '') {
	global $dati_db_albo,$dbAlbo;

	// elaboro la stringa
	if ($limite == 'tutti') {
		$limiteStr = '';
	} else {
		$limiteStr = ' limit '.$inizio.','.$limite;
	}
	$datiDaCaricare = "a.*,t.nome_tipo";
	
	// elaboro condizione ente
	$condizione= "";
	if ($idEnte AND is_numeric($idEnte)) {
		$condizione= " WHERE a.ente_id=".$idEnte." ";
	}

	// verifico presenza condizione aggiuntiva
	if ($condizioneAgg != '') {
		if ($idEnte AND is_numeric($idEnte)) {
			$condizione .= ' AND '.$condizioneAgg;
		} else {
			$condizione= " WHERE ".$condizioneAgg;
		}
	}

	if ($campoOrdine == 'nessuno') {
		$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db_albo['prefisso']."oggetto_atto a
			INNER JOIN ".$dati_db_albo['prefisso']."oggetto_tipi_atto t ON a.id_tipoatto = t.id
			".$condizione.$limiteStr;
	} else {
		if ($ordine == 'nessuno' or $ordine == '') {
			$ordine = '';
		} else {
			$ordine = 'DESC';
		}
		// in fase di lista, carico solo i campi di default per ogni oggetto
		$sql = "SELECT ".$datiDaCaricare." FROM ".$dati_db_albo['prefisso']."oggetto_atto a
			INNER JOIN ".$dati_db_albo['prefisso']."oggetto_tipi_atto t ON a.id_tipoatto = t.id
			".$condizione." ORDER BY ".$campoOrdine." ".$ordine.$limiteStr;
	}
	if( !($result = $dbAlbo->connessioneConReturn($sql)) ) {
		die("non riesco a prendere le informazioni oggetto no cat in db".$sql);
	}
	//echo "query: ".$sql;
	if ($dbAlbo->sqlNumRighe($result) != 0) {
		$listaOggetti=$dbAlbo->sqlArrayAss($result);
	} else {
		$listaOggetti = array();
	}
	/*
	ob_start();
	lognormale('',$listaOggetti);
	$content = ob_get_clean();
	ob_end_clean();
	file_put_contents('temp/debugAlbo.html', $content);
	*/
	return $listaOggetti;
}

function collegaAttoEtrasparenza($idAtto, $flag, $enteAdmin) {
	global $dati_db_albo, $dbAlbo, $dati_db, $database;
	
	$esegui = true;
	if($flag == 0) {
		//verificare se ci sono altri record in etrasp_atti_ealbo
		$sql = "SELECT id FROM ".$dati_db['prefisso']."etrasp_atti_ealbo WHERE id_atto_albo = ".$idAtto." AND id_ente_albo = ".$enteAdmin['id_ente_albo']." AND id_ente = ".$enteAdmin['id'];
		if( !($result = $database->connessioneConReturn($sql)) ) {}
		if ($database->sqlNumRighe($result) != 0) {
			$esegui = false;
		}
	}
	if($esegui) {
		$sql = "UPDATE ".$dati_db_albo['prefisso']."oggetto_atto SET eTrasparenza = ".$flag." WHERE id = ".$idAtto;
		if( !($result = $dbAlbo->connessioneConReturn($sql)) ) {
			file_put_contents('temp/debugAlbo.html', 'ERRORE: '.$sql.'<br />');
		}
	}
}

function getIdRecordAtto($idEnte, $idEnteAlbo, $idDocumento, $idOggetto) {
	global $dati_db, $database;

	$sql = "SELECT id_atto_albo FROM ".$dati_db['prefisso']."etrasp_atti_ealbo WHERE id_oggetto = ".$idOggetto." AND id_documento = ".$idDocumento." AND id_ente_albo = ".$idEnteAlbo." AND id_ente = ".$idEnte;
	if( !($result = $database->connessioneConReturn($sql)) ) {}
	if ($database->sqlNumRighe($result) != 0) {
		$res = $database->sqlArray($result);
		return $res['id_atto_albo'];
	}
}

function getStatoAtto($istanzaOggetto) {
	if($istanzaOggetto['annullato'] == 'si') {
		return 'Annullato';
	}
	$oggi = mktime (00,0,00,date("m"),date("d"),date("Y"));
	if($istanzaOggetto['data_fine_pubblicazione'] < $oggi AND $istanzaOggetto['stato_pubblicazione'] == '100' AND $istanzaOggetto['proroga_scadenza'] != 'si') {
		return 'Scaduto';
	}
	if($istanzaOggetto['data_proroga_scadenza'] < $oggi AND $istanzaOggetto['stato_pubblicazione'] == '100' AND $istanzaOggetto['proroga_scadenza'] == 'si') {
		return 'Scaduto';
	}
	switch($istanzaOggetto['stato_pubblicazione']) {
		case '10':
			return 'Inserito';
		break;
		case '50':
			return 'Autorizzato';
		break;
		case '80':
			return 'Bozza';
		break;
		case '100':
			return 'Pubblicato';
		break;
	}
}

function pulsanteFiltri() {
	$filtro = '<div class="filtriAtti">';
	$filtro .= '<button type="button" class="btn btn-filtriAtti">Visualizza filtri</button>';
	$filtro .= '</div>';
	return $filtro;
}
function filtroAtti() {
	global $idEnteAdmin;
	$filtro = '<div class="filtroAtti">';
	$filtro .= '<label>Visualizza ';
	$filtro .= '<select size="1" name="filtroAtti" id="filtroAtti">';
	$filtro .= '<option value="">tutti gli atti</option>';
	$filtro .= '<option value="filtro[non_importati]">atti non importati</option>';
	$filtro .= '<option value="filtro[importati]">atti importati</option>';
	$filtro .= '<option value="filtro[esclusi]">atti esclusi</option>';
	$filtro .= '</select>';
	$filtro .= '</label>';
	$filtro .= '</div>';
	return $filtro;
}
function filtroTipologie() {
	global $dbAlbo, $enteAdmin;
	
	$filtro = '<div class="filtroTipologie">';
	$filtro .= '<label>Tipologia ';
	$filtro .= '<select size="1" name="filtroTipologie" id="filtroTipologie">';
	$filtro .= '<option value="">tutte le tipologie</option>';

	$sql = "SELECT id,nome_tipo FROM oggetto_tipi_atto WHERE id_ente = ".$enteAdmin['id_ente_albo']." ORDER BY nome_tipo";
	if( !($result = $dbAlbo->connessioneConReturn($sql)) ) {
	}
	$tipi = $dbAlbo->sqlArrayAss($result);
	foreach((array)$tipi as $t) {
		$filtro .= '<option value="'.$t['id'].'">'.addslashes($t['nome_tipo']).'</option>';
	}
	$filtro .= '</select>';
	$filtro .= '</label>';
	$filtro .= '</div>';
	return $filtro;
}
function filtroStrutture() {
	global $dbAlbo, $enteAdmin;
	
	$sql = "SELECT id,nome FROM oggetto_area_organizzativa WHERE ente_id = ".$enteAdmin['id_ente_albo']." ORDER BY nome";
	if( !($result = $dbAlbo->connessioneConReturn($sql)) ) {
	}
	$aree = $dbAlbo->sqlArrayAss($result);
	if(count($aree) <= 0) {
		return '';
	}
	
	$filtro = '<div class="filtroStrutture">';
	$filtro .= "<label>Area ";
	$filtro .= '<select size="1" name="filtroStrutture" id="filtroStrutture">';
	$filtro .= '<option value="">tutte le aree</option>';

	foreach((array)$aree as $a) {
		$filtro .= '<option value="'.$a['id'].'">'.addslashes($a['nome']).'</option>';
	}
	$filtro .= '</select>';
	$filtro .= '</label>';
	$filtro .= '</div>';
	return $filtro;
}
function filtroDate() {
	global $dbAlbo, $enteAdmin;
	
	$filtro = '<div class="filtroDateDal"><label>Data atto dal ';
	$filtro .= '<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>';
	$filtro .= '<input type="text" name="filtroDataDal" id="filtroDataDal" />';
	$filtro .= '</label></div>';
	
	$filtro .= '<div class="filtroDateAl"><label>Data atto al ';
	$filtro .= '<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>';
	$filtro .= '<input type="text" name="filtroDataAl" id="filtroDataAl" />';
	$filtro .= '</label></div>';
	return $filtro;
}
?>