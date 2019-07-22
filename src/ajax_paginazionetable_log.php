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

// qui costruisco la pagina
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua e risolvo il bug
if ($datiUser['sessione_idlingua'] == 0 or $datiUser['sessione_idlingua'] == '') {
	$datiUser['sessione_idlingua'] = 1;	
}
$lingua = caricaLingua($datiUser['sessione_idlingua']);
$idLingua = $lingua['id'];

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

// qui pubblico il template html
if ($datiUser['sessione_loggato']) {

	$server_url = $server_s_url;
	if ($datiUser['permessi'] != -1 and $datiUser['permessi'] != 0) {
	
		// controllo utente
		include('./inc/controllo_user.php');
		include('./app/controllo_user.php');

		// importo la classe di amministrazione
		require_once ('classi/log_azione.php');
		$logAzioni = new logAzione();			

		$campiVisualizzati = ($datiUser['permessi'] == 10 ? 8 : 6);
		
		$colonneJs = array();
		$colonneJs[] = 'id_utente';
		$colonneJs[] = 'data_azione';
		$colonneJs[] = 'ip';
		$colonneJs[] = 'area';
		$colonneJs[] = 'azione';
		$colonneJs[] = 'dettagli';
		
		///////////////////////// NUOVO METODO DI CARICAMENTO DEI DOCUMENTI
	
		/* 
		 * Paginazione dei risultati
		 */
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
			$logAzioni->setLimiti(intval($_GET['iDisplayStart']), intval($_GET['iDisplayLength']));
		}
		
		/*
		 * Ordine risultati
		 */
		if (isset( $_GET['iSortCol_0'] )) {
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
					$logAzioni->setOrdine( $colonneJs[intval( $_GET['iSortCol_'.$i] )], $_GET['sSortDir_'.$i]==='asc' ? '' : 'DESC');
				}
			}
		}
		
		$arrayCondizioni = array();
		$condizioneEnte = '';
		if($datiUser['permessi'] != 10) {
			$arrayCondizioni['id_ente'] = $idEnteAdmin;
			$condizioneEnte = ' AND id_ente = '.$idEnteAdmin;
		}
		/* 
		 * Filtro ricerca
		 */
		$condizione = "";
		//CONDIZIONE PER UTENTE 
		if(isset($_GET['sSearch_0']) AND $_GET['sSearch_0'] != 'qualunque' AND $_GET['sSearch_0'] != '' ) {
			$arrayCondizioni['utenti'] = $_GET['sSearch_0'];
			$condizione .= " AND id_utente = '".$_GET['sSearch_0']."'";
		}
		//CONDIZIONE PER date (DOPPIA)
		if(isset($_GET['sSearch_1']) AND $_GET['sSearch_1'] != '' ) {
			// converto formato data start
			$arrDataStart = explode('/',$_GET['sSearch_1']);
			$dataStart = mktime(00, 0, 00, $arrDataStart[1], $arrDataStart[0], $arrDataStart[2]);
			$arrayCondizioni['data_da'] = $dataStart;	
			$condizione .= " AND data_azione >= ".$dataStart; 
		}	
		if(isset($_GET['sSearch_2']) AND $_GET['sSearch_2'] != '') {
			// converto formato data end
			$arrDataEnd = explode('/',$_GET['sSearch_2']);
			$dataEnd = mktime(00, 0, 00, $arrDataEnd[1], $arrDataEnd[0], $arrDataEnd[2]);
			$arrayCondizioni['data_a'] = $dataEnd;
			$condizione .= " AND data_azione <=".$dataEnd; 
		}
		//CONDIZIONE PER ARCHIVIO 
		if((isset($_GET['sSearch_3']) AND $_GET['sSearch_3'] != 'qualunque' AND $_GET['sSearch_3'] != '')) {
			$arrayCondizioni['oggetto'] = $_GET['sSearch_3'];
			$condizione .= " AND id_oggetto = '".$_GET['sSearch_3']."'";
		}
		
		//filtro testo
		if(isset($_GET['sSearch']) AND $_GET['sSearch'] != '' ) {
			$c = " AND (azione LIKE '%".$_GET['sSearch']."%' OR area LIKE '%".$_GET['sSearch']."%' OR sottoarea LIKE '%".$_GET['sSearch']."%' OR altri_valori LIKE '%".$_GET['sSearch']."%' ) ";
			$arrayCondizioni['search'] = $c;
			$condizione .= $c;
		}
		
		if($menuSecondario == 'log_utenti') {
			$condizioneEnte .= ' AND (id_oggetto = -200 OR id_oggetto = -1) ';
			$arrayCondizioni['by_users'] = '1';
		} else if($menuSecondario == 'log') {
			$condizioneEnte .= ' AND (id_oggetto != -200 AND id_oggetto != -1) ';
			$arrayCondizioni['by_users'] = '0';
		}
		
		/*
		 * Eseguo query
		 */
		$listaTabella = $logAzioni->caricaLog($arrayCondizioni); 
		
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
			include ('./app/admin_template/log/tab_row_ajax.tmp');
			$numChiave++;
		}	
		
		// Totale risultati presenti
		$sql = "SELECT COUNT('id') AS totale FROM log_azioni WHERE 1=1 ".$condizioneEnte;
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("errore in caricamento totale ".$sql);
		}
		$campo = $database->sqlArray($result);
		$totale = $campo['totale'];
		// Totale risultati filtrati
		if ( $condizione != "" ) {
			$sql = "SELECT COUNT('id') AS totale FROM log_azioni WHERE 1=1 ".$condizione.$condizioneEnte;
			if( !($result = $database->connessioneConReturn($sql)) ) {
				die("errore in caricamento totale filtrato ".$sql);
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