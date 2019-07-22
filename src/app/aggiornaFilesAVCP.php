<?
//aggiornamento automatico del file xml per anac
$dataAttuale = mktime();
$sett = (60 * 60 * 24 * 7);
//disabilitato al momento l'aggiurnamento automatico del dataset per anac
if($dataAttuale - $configurazione['dataUltimoAggiornamentoAVCP'] > $sett and 1==0) {
	//è passata una settimana dall'ultimo aggiornamento
	
	require_once('classi/admin_oggetti.php');
	$oggOgg = new oggettiAdmin(45);

	require('app/moduli/menu_amm/operazioni/oggetti/avcp.php');
	$opOgg = new avcp();
	
	$fileAVCP = array();
	$sql = "SELECT u.* FROM ".$dati_db['prefisso']."oggetto_url_avcp u" . 
			" INNER JOIN ".$dati_db['prefisso']."etrasp_enti e ON u.id_ente = e.id" .
			" WHERE e.aggiorna_avcp = 1" .
			" ORDER BY id";
	if($result = $database->connessioneConReturn($sql)) {
		$fileAVCP = $database->sqlArrayAss($result);
	}
	
	//preservo valori originali
	$enteAdminOriginale = $enteAdmin;
	$datiUserOriginale = $datiUser;
	
	foreach((array)$fileAVCP as $f) {
		$_POST['anno'] = $f['anno'];
		$_GET['id'] = $f['id'];
		$enteAdmin['nome_completo_ente'] = getValoreEnte($f['id_ente'], $campo = 'nome_completo_ente');
		$enteAdmin['nome_breve_ente'] = getValoreEnte($f['id_ente'], $campo = 'nome_breve_ente');
		$datiUser['id_ente_admin'] = $f['id_ente'];
		
		echo "<br />Aggiorno URL ".$f['url'];
		$opOgg->postUpdate();
	}
	
	//recupero valori originali
	$enteAdmin = $enteAdminOriginale;
	$datiUser = $datiUserOriginale;
	
	//l'update della data di ultimo aggiornamento
	$sql = "UPDATE ".$dati_db['prefisso']."configurazione SET valore = '".$dataAttuale."' WHERE nome = 'dataUltimoAggiornamentoAVCP'";
	if(! $result = $database->connessioneConReturn($sql)) {
		echo "<br />ERRORE IN UPDATE 'dataUltimoAggiornamentoAVCP': ".$sql;
	}
}

function getValoreEnte($idEnte, $campo = 'nome_completo_ente') {
	global $database, $dati_db;
	
	$sql = "SELECT ".$campo." FROM ".$dati_db['prefisso']."etrasp_enti WHERE id = ".$idEnte;
	if($result = $database->connessioneConReturn($sql)) {
		$e = $database->sqlArray($result);
	}
	return $e[$campo];
}
?>