<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015 - AgID Agenzia per l'Italia Digitale
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
	 * pat/aggiornaFilesAVCP.php
	 * 
	 * @Descrizione
	 * Procedure di aggiornamento del file XML
	 *
	 */	
	 
//aggiornamento automatico del file xml per anac
$dataAttuale = mktime();
$sett = (60 * 60 * 24 * 7);
//disabilitato al momento l'aggiurnamento automatico del dataset per anac
if($dataAttuale - $configurazione['dataUltimoAggiornamentoAVCP'] > $sett and 1==0) {
	//� passata una settimana dall'ultimo aggiornamento
	
	require('classi/admin_oggetti.php');
	$oggOgg = new oggettiAdmin(45);

	require('pat/moduli/menu_amm/operazioni/oggetti/avcp.php');
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