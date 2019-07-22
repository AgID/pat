<?php

/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

PAT - Portale Amministrazione Trasparente
Copyright AgID Agenzia per l'Italia Digitale

Concesso in licenza a norma dell'EUPL(la "Licenza"), versione 1.2;
*/


//ini_set('memory_limit','512M');
include ('./inc/config.php');
include ('./inc/inizializzazione_admin.php');

// REINIZIALIZZO LA VARIABILE MENU E TROVO I DATI SULLA FUNZIONE SCELTA
$menu = isset ($_GET['menu']) ? forzaStringa($_GET['menu']) : 'desktop';
$menuSecondario = isset ($_GET['menusec']) ? forzaStringa($_GET['menusec']) : '';
$azione = isset ($_GET['azione']) ? forzaStringa($_GET['azione']) : 'lista';
$azioneSecondaria = isset ($_GET['azionesec']) ? forzaStringa($_GET['azionesec']) : '';
$id = is_numeric($_GET['id']) ? forzaNumero($_GET['id']) : 0;
$idOggetto = is_numeric($_GET['id_ogg']) ? forzaNumero($_GET['id_ogg']) : 0;
$idCategoria = is_numeric($_GET['id_cat']) ? forzaNumero($_GET['id_cat']) : 0;
$idIstanza = $id;

// qui costruisco la pagina
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua e risolvo il bug
if ($datiUser['sessione_idlingua'] == 0 or $datiUser['sessione_idlingua'] == '') {
	$datiUser['sessione_idlingua'] = 1;	
}
$lingua = caricaLingua($datiUser['sessione_idlingua']);
$idLingua = $lingua['id'];

$archiviAdminEsclusi = explode(',', $tipoEnte['archivi_esclusi']);

include ('./app/config_pat.php');

// ELABORO LE VARIABILI DEL MENU
foreach ((array)$funzioniMenu as $funzione) {
	if ($menu == $funzione['menu']) {
		$funzioneMenu = $funzione;
		if ($menuSecondario != '') {
			foreach ((array)$funzione['sottoMenu'] as $funzioneSotto) {
				if ($menuSecondario == $funzioneSotto['menuSec']) {
					$funzioneSottoMenu = $funzioneSotto;
				}
			}
		}
	}
}

// INIZIALIZZO SEMPRE LA SESSIONE PER QUESTIONI LEGATE ALL'EDITOR ED AL CKFINDER
session_start();

// forzo la disabilitazione della cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("X-Frame-Options: sameorigin");

////////////////// ENTE IN NAVIGAZIONE
$idEnte = is_numeric($datiUser['id_ente']) ? $datiUser['id_ente'] : 0;
if ($idEnte) {
	// carico ente richiamato
	$entePubblicato = datoEnte($idEnte);
}

////////////////// ENTE IN AMMINISTRAZIONE
$idEnteAdmin = is_numeric($datiUser['id_ente_admin']) ? $datiUser['id_ente_admin'] : 0;
if ($idEnteAdmin) {
	// carico ente richiamato
	$enteAdmin = datoEnte($idEnteAdmin);
}
// carico il tipo di ente in amministrazione
$tipoEnte = datoTipoEnte($enteAdmin['tipo_ente']);

//ripeto inclusione di configurazioni personalizzate
include ('./codicepers/config.php');

$configurazione['__tags_concorsi'] = prendiTagsConcorsi(false, true);

// qui pubblico il template html
if ($datiUser['sessione_loggato']) {

	$server_url = $server_s_url;
	if ($datiUser['permessi'] != -1 and $datiUser['permessi'] != 0) {
	
		// controllo utente
		include('./inc/controllo_user.php');
		include('./app/controllo_user.php');

		// importo la classe di amministrazione oggetti
		require('classi/admin_oggetti.php');
		$oggOgg = new oggettiAdmin($idOggetto);
		
		// analizzo quali campi visualizzare in amministrazione, prendendoli dalla struttura IsWEB
		$campiVisualizzati = array();
		$struttura = $oggOgg->parsingStruttura('si');
		
		//filtri per le strutture
		$ufficiFiltro = array();
		$filtraRecordUffici = false;
		if($idOggetto == 11) {
			$ufficiUtente = explode(',', $datiUser['uffici_selezionabili']);
			foreach((array)$struttura as $campo) {
				if(($campo['tipocampo'] == 'campoggetto' or $campo['tipocampo'] == 'campoggetto_multi') and $campo['valorecampo'] == 13 and $configurazione['fitra_record_strutture']) {
					//filtro gli elementi in base alla struttura/strutture
					$ufficiFiltro[$campo['nomecampo']] = array();
					$ufficiFiltro[$campo['nomecampo']]['__tipo'] = $campo['tipocampo'];
					foreach((array)$ufficiUtente as $u) {
						if($u>0) {
							$ufficiFiltro[$campo['nomecampo']][$u] = $u;
							$filtraRecordUffici = true;
						}
					}
				}
			}
		}
		
		$colonneJs = array('id');
		if ($datiUser['permessi']==10) {
			$colonneJs[] = 'id_ente';
		}
		for ($i=0;$i<count($oggOgg->campiAdmin);$i++) {
			$campiVisualizzati[$i]['campo'] = $oggOgg->campiAdmin[$i]; 
			$colonneJs[] = $oggOgg->campiAdmin[$i]; 
			$campiVisualizzati[$i]['etichetta'] = $oggOgg->campiEtiAdmin[$i]; 
			$campiVisualizzati[$i]['proprieta'] = $oggOgg->campiPropAdmin[$i]; 	
			$campoStr = campoStruttura($campiVisualizzati[$i]['campo'],$struttura);
			if (strpos($campoStr['tipocampo'],'*') !== false) {
				$campoStr['tipocampo'] = substr($campoStr['tipocampo'], 1);	
			}
			$campiVisualizzati[$i]['tipo'] = $campoStr['tipocampo'];
			$campiVisualizzati[$i]['etichette'] = $campoStr['proprieta'];
			$campiVisualizzati[$i]['valore'] = $campoStr['valorecampo'];
		}				
		$colonneJs[] = '';
		
		///////////////////////// NUOVO METODO DI CARICAMENTO DEI DOCUMENTI
	
	
		/* 
		 * Paginazione dei risultati
		 */
		//$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
			$inizio = intval($_GET['iDisplayStart']);
			$limite = intval($_GET['iDisplayLength']);
			/*
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
				intval( $_GET['iDisplayLength'] );
			*/
		}
		
		/*
		 * Ordine risultati
		 */
		//$sOrder = "";
		$ordine = "nessuno";
		$campoOrdine = "nessuno";
		if (isset( $_GET['iSortCol_0'] )) {
			//$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
					$campoOrdine = $colonneJs[intval( $_GET['iSortCol_'.$i] )];
					$ordine = $_GET['sSortDir_'.$i]==='asc' ? '' : 'DESC';
					/*
					$sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
					*/
				}
			}
			/*
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
			*/
		}
	
		/* 
		 * Filtro ricerca
		 */
		$condizione = "";
		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$_GET['sSearch'] = htmlentities((strtolower($_GET['sSearch'])));
			$condizione = "(";
			for ( $i=0 ; $i<count($colonneJs) ; $i++ ) {
				if ($colonneJs[$i] != '') {
				
					// prima di elaborare la condizione, verifico il tipo di campo
					if ($idEnteAdmin AND is_numeric($idEnteAdmin)) {
						$num = $i-1;
						$condizioneEnteCerca = " AND id_ente=".$idEnteAdmin." ";
						$condizioneEnteCercaUtenti = " AND id_ente_admin=".$idEnteAdmin." ";
					} else {
						$num = $i-2;
						$condizioneEnteCerca = "";
						$condizioneEnteCercaUtenti = "";
					}
					$nomeCampo = $campiVisualizzati[$num]['campo'];
					$tipoCampo = $campiVisualizzati[$num]['tipo'];
					$oggettoAss = $campiVisualizzati[$num]['valore'];
					
					if ($tipoCampo == 'campoggetto_multi' OR $tipoCampo == 'campoggetto') {
						/////////////////// devo risalire agli ID Giusti
						// prima il nome della tabella ed il campo riferimento
						if ($oggettoAss != '' AND $oggettoAss) {
							foreach ((array)$oggetti as $oggTemp) {
								if ($oggTemp['id'] == $oggettoAss) {
									$tabellaOgg = $oggTemp['tabella'];
									$campoDefault = $oggTemp['campo_default'];
								}
							}							
						}
						// ora trovo gli id 
						$sql = "SELECT id FROM ".$tabellaOgg." WHERE ".$campoDefault." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' ".$condizioneEnteCerca;
						if( !($result = $database->connessioneConReturn($sql)) ) {
							die("errore in caricamento campo associato filtrato");
						}
						$arrayId = $database->sqlArrayAss($result);						
						//in questo caso, la condizione è multipla
						foreach ((array)$arrayId as $associato) {
							$condizione .= $colonneJs[$i]." = '".$associato['id']."' OR ".$colonneJs[$i]." LIKE '".$associato['id'].",%' OR ".$colonneJs[$i]." LIKE '%,".$associato['id'].",%' OR ".$colonneJs[$i]." LIKE '%,".$associato['id']."' OR ";
						}						
					} else if ($tipoCampo == 'camposezione') {
						$sql = "SELECT id FROM ".$dati_db['prefisso']."sezioni WHERE nome LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' ";
						if( !($result = $database->connessioneConReturn($sql)) ) {
							die("errore in caricamento campo associato filtrato");
						}
						$arrayId = $database->sqlArrayAss($result);
						//in questo caso, la condizione e' multipla
						foreach ((array)$arrayId as $associato) {
							$condizione .= $colonneJs[$i]." = '".$associato['id']."' OR ".$colonneJs[$i]." LIKE '".$associato['id'].",%' OR ".$colonneJs[$i]." LIKE '%,".$associato['id'].",%' OR ".$colonneJs[$i]." LIKE '%,".$associato['id']."' OR ";
						}
					} else if ($tipoCampo == 'data_calendario') {
						//////////////// devo trasformare la data in valore numerico
					} else if ($tipoCampo == 'campoutente') {
						//////////////// devo cercare un utente
						$sql = "SELECT id FROM utenti WHERE nome LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' ".$condizioneEnteCercaUtenti;
						if( !($result = $database->connessioneConReturn($sql)) ) {
							die("errore in caricamento campo utente filtrato");
						}
						$arrayId = $database->sqlArrayAss($result);						
						//in questo caso, la condizione è multipla
						foreach ((array)$arrayId as $associato) {
							$condizione .= $colonneJs[$i]." = '".$associato['id']."' OR ".$colonneJs[$i]." LIKE '".$associato['id'].",%' OR ".$colonneJs[$i]." LIKE '%,".$associato['id'].",%' OR ".$colonneJs[$i]." LIKE '%,".$associato['id']."' OR ";
						}							
					} else {
						$condizione .= $colonneJs[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
					}
				}
			}
			$condizione = substr_replace( $condizione, "", -3 );
			$condizione .= ')';
		}
	
		/*
		// filtro individuale
		for ( $i=0 ; $i<count($colonneJs); $i++ ) {
			if ($colonneJs[$i] != '') {
				if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ) {
					if ( $condizione == "" ) {
						$condizione = " ( ";
					} else {
						$condizione .= " AND ";
					}
					$condizione .= $colonneJs[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				}
			}
		}
		if ( $condizione != "" ) {
			$condizione .= " ) ";
		}	
		*/
		
		if(moduloAttivo('bandigara') and $idOggetto == 11) {
			//////////////FILTRI BANDI
			$condizioneFiltri = '';
			if($_GET['sSearch_0'] != '') {
				$fil = $_GET['sSearch_0'];
				if($fil == 'filtro[errori_anac]') {
					$condizioneFiltri = ' __errori_anac > 0 ';
				}
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
			
			if($_GET['sSearch_1'] != '') {
				$dataDal = explode('/',$_GET['sSearch_1']);
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').' data_attivazione >= '.mktime(2,0,0,$dataDal[1], $dataDal[0], $dataDal[2]);
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_2'] != '') {
				$dataDal = explode('/',$_GET['sSearch_2']);
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').' data_attivazione <= '.mktime(2,0,0,$dataDal[1], $dataDal[0], $dataDal[2]);
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_3'] != '') {
				$fil = $_GET['sSearch_3'];
				$condizioneFiltri = " tipologia = '".$fil."' ";
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_4'] != '') {
				$fil = $_GET['sSearch_4'];
				$condizioneFiltri = " struttura = ".$fil." ";
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
			//file_put_contents('temp/queryBandi.html', $condizione.' - '.$condizioneFiltri.' - '.$_GET['sSearch_1'].' - '.$_GET['sSearch_2'].'<br />');
		} else if(moduloAttivo('bandigara') and $idOggetto == 41) {
			//////////////FILTRI FORNITORI
			$condizioneFiltri = '';
			if($_GET['sSearch_0'] != '') {
				$fil = $_GET['sSearch_0'];
				if($fil == 'filtro[errori_anac]') {
					$condizioneFiltri = ' __errori_anac > 0 ';
				}
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
			
		} else if($idOggetto == 4) {
			//////////////FILTRI INCARICHI
			$condizioneFiltri = '';
			if($_GET['sSearch_0'] != '') {
				$fil = $_GET['sSearch_0'];
				$condizioneFiltri = " tipologia = '".$fil."' ";
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_1'] != '') {
				$dataDal = explode('/',$_GET['sSearch_1']);
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').' inizio_incarico >= '.mktime(2,0,0,$dataDal[1], $dataDal[0], $dataDal[2]);
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_2'] != '') {
				$dataDal = explode('/',$_GET['sSearch_2']);
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').' inizio_incarico <= '.mktime(2,0,0,$dataDal[1], $dataDal[0], $dataDal[2]);
				$condizione .= $condizioneFiltri;
			}
		} else if($idOggetto == 38) {
			//////////////FILTRI SOVVENZIONI
			$condizioneFiltri = '';
			if($_GET['sSearch_0'] != '') {
				$fil = $_GET['sSearch_0'];
				$condizioneFiltri = " tipologia = '".$fil."' ";
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
		} else if($idOggetto == 28) {
			//////////////FILTRI PROVVEDIMENTI
			$condizioneFiltri = '';
			if($_GET['sSearch_0'] != '') {
				$dataDal = explode('/',$_GET['sSearch_0']);
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').' data >= '.mktime(2,0,0,$dataDal[1], $dataDal[0], $dataDal[2]);
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_1'] != '') {
				$dataDal = explode('/',$_GET['sSearch_1']);
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').' data <= '.mktime(2,0,0,$dataDal[1], $dataDal[0], $dataDal[2]);
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_2'] != '') {
				$fil = $_GET['sSearch_2'];
				$condizioneFiltri = " tipo = '".$fil."' ";
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
			if($_GET['sSearch_3'] != '') {
				$fil = $_GET['sSearch_3'];
				$condizioneFiltri = " ( struttura = '".$fil."' OR struttura LIKE '".$fil.",%' OR struttura LIKE '%,".$fil.",%' OR struttura LIKE '%,".$fil."' )";
				$condizioneFiltri = (trim($condizione) != '' ? ' AND ' : '').$condizioneFiltri;
				$condizione .= $condizioneFiltri;
			}
		}
		
		if($filtraRecordUffici) {
			//applicare la query di ricerca con $ufficiFiltro
			//lognormale('',$ufficiFiltro);die();
			$condsUffici = array();
			
			foreach((array)$ufficiFiltro as $nomecampo => $ids) {
				$tipocampo = $ids['__tipo'];
				foreach((array)$ids as $idUfficio) {
					if($idUfficio>0) {
						if($tipocampo == 'campoggetto_multi') {
							$condsUffici[] = " ".$nomecampo." = '".$idUfficio."' OR ".$nomecampo." LIKE '".$idUfficio.",%' OR ".$nomecampo." LIKE '%,".$idUfficio.",%' OR ".$nomecampo." LIKE '%,".$idUfficio."' ";
						} else {
							$condsUffici[] = " ".$nomecampo." = ".$idUfficio." ";
						}
					}
				}
			}
			$cu  = '';
			if(count($condsUffici)) {
				$cu = implode(' OR ', $condsUffici);
				$cu .= ' OR id_proprietario='.$datiUser['id'].' ';
			}
			$condizione .= (trim($condizione) != '' ? ' AND ' : '').$cu;
		}
		
		if($datiUser['filtraRecordProprietario']) {
			$condizione .= (trim($condizione) != '' ? ' AND ' : '').' (id_proprietario='.$datiUser['id'].') ';
		}

		/*
		 * Eseguo query
		 */
  		if (!$oggOgg->idCategoria) {
  			$listaTabella = $oggOgg->visualizzaListaOggettiNoCat($inizio, $limite, $campoOrdine, $ordine, $idEnteAdmin, $condizione); 
		} 
		
		$numOggetti = count($listaTabella);
	
		$visualizzaInterfaccia = true;
		$outputArray = array();
		
		/*
		echo "<pre>";
		print_r($campiVisualizzati);
		echo "</pre>";
		*/
		
		$numChiave = 0;
		foreach ((array)$listaTabella as $istanzaOggetto) {		
			// includo renderizzazione in array della riga
			include ('./app/admin_template/oggetti/tab_row_ajax.tmp');
			$numChiave++;
		}	
		
		// Totale risultati presenti per questo ente
		$condizioneEnte = "";
		if ($idEnteAdmin AND is_numeric($idEnteAdmin)) {
			$condizioneEnte = " WHERE id_ente=".$idEnteAdmin." ";
		}
		$sql = "SELECT COUNT('id') AS totale FROM ".$oggOgg->tabellaOggetto.$condizioneEnte;
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("errore in caricamento totale");
		}
		$campo = $database->sqlArray($result);
		$totale = $campo['totale'];		

		// Totale risultati filtrati
		if ( $condizione != "" ) {
			$condizioneEnte = "";
			if ($idEnteAdmin AND is_numeric($idEnteAdmin)) {
				$condizioneEnte = " AND id_ente=".$idEnteAdmin." ";
			}
			$sql = "SELECT COUNT('id') AS totale FROM ".$oggOgg->tabellaOggetto." WHERE ".$condizione.$condizioneEnte;
			if( !($result = $database->connessioneConReturn($sql)) ) {
				die("errore in caricamento totale filtrato");
			}
			$campo = $database->sqlArray($result);
			$totaleTemp = $campo['totale'];		
		} else {
			$totaleTemp = $totale;
		}
		
		/*
		 * Output
		 */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $totale,
			"iTotalDisplayRecords" => $totaleTemp,
			"aaData" => array()
		);	
	
		// encoding array in UTF8
		array_walk_recursive($outputArray, function(&$value, $key) {
			if (is_string($value)) {
				$value = iconv('windows-1252', 'utf-8', $value);
			}
		});
		
		$output["aaData"] = $outputArray;		
		echo json_encode($output);
		
		// echo json_encode( array("aaData" => $outputArray) );
	
	} 
} 

$database->sqlChiudi();
exit();
?>