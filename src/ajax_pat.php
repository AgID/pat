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
	 * ajax_pat.php
	 * 
	 * @Descrizione
	 * File di risposta via ajax ad azioni effettuate dal backoffice dell'ambiente PAT
	 *
	 */

// inclusione configurazione 
require_once ('./inc/config.php');

/*********************************************INIZIALIZZO AMBIENTE E VARIABILI*********************************/
/* 
inizializzo l'ambiente manualmente senza utilizzare il servizio di inizializzazione di ISWEB e quindi 
dichiarando gli oggetti e variabili di interesse
*/ 
require_once ('./classi/database.php');
require_once ('./classi/core.php');
if (version_compare(PHP_VERSION, '5.2.0', 'gt')) {
	include_once ('./webservice/ClientWS.php');
}
require('./codicepers/funzioni_personalizzate.php');
// Attivo oggetto di connessione principale al database
$database = new database($dati_db['host'], $dati_db['user'], $dati_db['password'], $dati_db['database'], $dati_db['persistenza']);
// Inzializzo il core principale e rendo globali alcuni dati 
$coreInfo = new coreFramework();
$configurazione = $coreInfo->loadingConfigurazione();
$oggetti = $coreInfo->loadingOggetti();
$idSezione = is_numeric($_GET['id_sezione']) ? forzaNumero($_GET['id_sezione']) : 0;
if($idSezione === 0) { $idSezione = is_numeric($_POST['id_sezione']) ? forzaNumero($_POST['id_sezione']) : 0;}
$idOggetto = is_numeric($_GET['id_ogg']) ? forzaNumero($_GET['id_ogg']) : 0;
if($idOggetto === 0) { $idOggetto = is_numeric($_POST['id_ogg']) ? forzaNumero($_POST['id_ogg']) : 0;}
$idDocumento = is_numeric($_GET['id_doc']) ? forzaNumero($_GET['id_doc']) : 0;
if($idDocumento === 0) {$idDocumento = is_numeric($_POST['id_doc']) ? forzaNumero($_POST['id_doc']) : 0;}

/* 
Inizializzo la sessione utente e la matrice datiUser
*/ 
if (getenv('HTTP_X_FORWARDED_FOR') != '') {
	$client_ip = (!empty ($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty ($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR);
	if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", getenv('HTTP_X_FORWARDED_FOR'), $ip_list)) {
		$private_ip = array (
			'/^0\./',
			'/^127\.0\.0\.1/',
			'/^192\.168\..*/',
			'/^172\.16\..*/',
			'/^10..*/',
			'/^224..*/',
			'/^240..*/'
		);
		$client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
	}
} else {
	$client_ip = (!empty ($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty ($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR);
}
$ipUser = encode_ip($client_ip);
include ('inc/sessioni.php');
// impostazioni di default
$idLingua = 1;
$linguaMod = 0;
$adminInfo = -1;
$adminGrafica = -1;
// qui costruisco la sessione
if($idSezione > 0) {
	$datiUser = refreshSessione($ipUser, $idSezione);
} else {
	$datiUser = refreshSessione($ipUser, -3);
}

/* 
Carico dai dati della sessione utente l'ente selezionato, nel caso di un installazione multi-ente
*/
$idEnte = is_numeric($datiUser['id_ente']) ? $datiUser['id_ente'] : 0; // ente scelto in navigazione
$idEnteAdmin = is_numeric($datiUser['id_ente_admin']) ? $datiUser['id_ente_admin'] : 0; // ente scelto in amministrazione
if ($idEnte) {
	// carico ente scelto nella variabile di sessione
	$entePubblicato = datoEnte($idEnte);
}
if ($idEnteAdmin) {
	// carico ente richiamato
	$enteAdmin = datoEnte($idEnteAdmin);	
}
$tipoEnte = datoTipoEnte($enteAdmin['tipo_ente']);
include ('./pat/config_pat.php'); // configurazione PAT

// controllo utente
include ('inc/controllo_user.php');

// verifico ulteriormente le altre variabili
$_GET = sanitize($_GET);
$_POST = sanitize($_POST);
$_POST = sanitize($_REQUEST);

/*********************************************VERIFICO QUALE RISPOSTA PER QUALE AZIONE*********************************/
switch ($_GET['azione']) {

	case 'verificaUsername':
		$username = $_GET["username"];
		if ($username != '') {
			$userValido = validaUserName($username);
			if ($userValido) {
				echo "true";
			} else {
				echo "false";
			}
		} else {
			echo "false";
		}
		
	break;
	
	case 'verificaEmail':
		$email = $_REQUEST["email"];
		$emailValida = validaMail($email);
		if ($emailValida) {
			echo "false";
		} else {
			echo "true";
		}
	break;

	case 'review':
		if ($idDocumento != 0 and $idOggetto != 0) {
			
			require_once('./classi/documento.php');
			$documento = new documento($idOggetto);
			
			//////////////////////////////////MODALITA' REVIEW ////////////////////////////
			// costruisco le regole per i campi richiami da visualizzare
			$campiVisualizzati = array();
			$struttura = $documento->parsingStruttura('si');
			if ($documento->campiReview[0] != '') {
				for ($i=0;$i<count($documento->campiReview);$i++) {
					// richiami automatoici e manuali
					$campiVisualizzati[$i]['nome'] = $documento->campiReview[$i]; 
					$campiVisualizzati[$i]['campo'] = $documento->campiReview[$i]; 
					$campiVisualizzati[$i]['titolo'] = $documento->campiEtiReview[$i]; 
					$campiVisualizzati[$i]['proprieta'] = $documento->campiPropReview[$i]; 	
					$campoStr = campoStruttura($documento->campiReview[$i],$struttura);
					if (strpos($campoStr['tipocampo'],'*')!==false) {
						$campoStr['tipocampo'] = substr($campoStr['tipocampo'], 1);
					}
					$campiVisualizzati[$i]['tipo'] = $campoStr['tipocampo'];
					if (!$documento->campiStileReview[$i]) {
						//$campiVisualizzati[$i]['stile'] = $campoStr['stilecampo'];
						$campiVisualizzati[$i]['stile'] = 0;
					} else {
						$campiVisualizzati[$i]['stile'] = $documento->campiStileReview[$i];
					}
					$campiVisualizzati[$i]['valore'] = $campoStr['valorecampo'];
					$campiVisualizzati[$i]['etichette'] = $campoStr['proprieta'];
					$campiVisualizzati[$i]['tipoinput'] = $campoStr['tipoinput'];
				}
			}

			$oggetto = $documento->caricaDocumento($idDocumento);
			$permOggWrite = FALSE; 
			$permOggIstanza = FALSE;
			$pubblicaCont = TRUE;		

			$templateScelto = array();
			$templateScelto['id'] = 1;
			
			$sezioneNavigazione = array();
			$sezioneNavigazione['id'] = 1;
				
			echo "<div id=\"reviewAdmin\">";	
			$sezioneNativa=cercaSezioneNativa($documento->idOggetto);
			include('./template/oggetti/review_oggetto'.$adminStringa.'.tmp');

			echo "</div>";
			$oggettoReview = $oggetto;													 
			
		}
	break;

	case 'getOggettiWebservice' :
		////////////////////////////CARICAMENTO OGGETTI WEBSERVICES DAL SERVER COMUNICATO
		$sql = "SELECT * FROM " . $dati_db['prefisso'] . "configurazione_webservice";
		if (!($result = $database->connessioneConReturn($sql))) {
			die("Database non installato o non disponibile: errore critico.");
		}
		while ($riga = $database->sqlArray($result)) {
			$configurazioneWebservice[$riga['id']] = array (
				'wsdl' => $riga['wsdl'],
				'server_host' => $riga['server_host'],
				'secondi_durata_cache' => $riga['secondi_durata_cache'],
				'nome_server' => $riga['nome_server']
			);
		}

		if (isset ($_GET['idServer'])) {
			$idServer = $_GET['idServer'];
			if ($idServer == 0) {
				echo "<div>Nessun Server selezionato.</div>";
			} else {
				$client = new ClientWS($configurazioneWebservice[$idServer]['wsdl'], $configurazioneWebservice[$idServer]['username_ws'], $configurazioneWebservice[$idServer]['password_ws'], $configurazione['cookie_dominio']);
				$parametri['auth'] = 0; //Forzo l'autorizzazione del server a false perch� non ho $datiUser;
				$response = $client->getOggetti($parametri);
				if ($response->message == 'errore') {
					echo "<div>Errore webservice.</div>";
				} else {
					$result = '<select id="oggetto_ws" name="oggetto_ws">';
					foreach ($response->value as $oggetto) {
						$result .= '<option value="' . $oggetto['id'] . '">' . $oggetto['nome'] . '</option>';
					}
					$result .= '</select>';
					$result .= '<input type="hidden" id="wsdl" name="wsdl" value="' . $configurazioneWebservice[$idServer]['wsdl'] . '" />';
					echo $result;
				}
			}
		}

		break;

	case 'getUrlParlante' :
		$sezioni = $coreInfo->loadingSezioni();
		if ($_GET['tipo'] == 'sezione') {
			// carico sezione
			$sezioneTmp = nomeSezDaId($_GET['valore'],'*');
			if (!$sezioneTmp['id_dominio']) {
				$passoUrl = $configurazione['url_default'];
			} else {
				// carico dominio
				$domTmp = caricaDominio($sezioneTmp['id_dominio']);
				$passoUrl = $domTmp['server_url'];
				//echo "url: ".$sezioneTmp['id_dominio']." ";
			}
			$strTemp = $passoUrl."pagina".$_GET['valore']."_".pulisciNome($sezioneTmp['nome']) . ".html";
			echo $strTemp;
		} else
			if ($_GET['tipo'] == 'oggetto') {
				//Carico le informazioni oggetto
				$sql = "SELECT * FROM " . $dati_db['prefisso'] . "oggetti WHERE id=" . $_GET['id_oggetto'];
				if (!($result = $database->connessioneConReturn($sql))) {
					die("Database non installato o non disponibile: errore critico.");
				}
				while ($riga = $database->sqlArray($result)) {
					$nome = $riga['nome'];
					$tabella = $riga['tabella'];
				}
				$sql = "SELECT * FROM " . $dati_db['prefisso'] . $tabella . " WHERE id=" . $_GET['valore'];
				if (!($result = $database->connessioneConReturn($sql))) {
					die("Database non installato o non disponibile: errore critico.");
				}
				while ($riga = $database->sqlArray($result)) {
					$idCategoria = $riga['id_sezione'];
				}
				
				// cerco la sezione nativa di questo oggetto (integrazione multidominio)
				
				$strTemp = $server_url . "archivio" . $_GET['id_oggetto'] . "_" . pulisciNome($nome) . "_" . $idCategoria . "_" . $_GET['valore'] ."_0_1.html";
				echo $strTemp;
			}
		break;
	
	case 'getNomeFileMedia' :
		$media = caricaMediaParziale($_GET['valore']);
		if ($media['nome_file'] != '') {
			echo urlencode($media['nome_file']);
		} else {
			echo urlencode('nofile');
		}
		break;
	
	case 'getModelloContenuto':
		$id = $_GET['id'];
		$idEnteAdmin = $_GET['ide'];
		$sql = "SELECT * FROM " . $dati_db['prefisso'] . "oggetto_etrasp_modello WHERE id_sezione_etrasp=" . $id . " AND id_ente = " . $idEnteAdmin . " ORDER BY ultima_modifica DESC LIMIT 1";
		if (!($result = $database->connessioneConReturn($sql))) {
			die("Database non installato o non disponibile: errore critico.");
		}
		$riga = $database->sqlArray($result);
		echo json_encode(htmlentities($riga['html_generico']));
		return;
	break;
	
	case 'getBubbleEditor':
		$numMarkers = $_GET['numMarkers'];
		ob_start();
		creaOggettoFormPers("editor", "prop_editor_".$numMarkers, "", "","","");
		$content = ob_get_clean();
		ob_end_flush();
		echo $content;
		return;
		break;
		
	case 'getBubbleEditorObject':
		$numMarkers = $_GET['numMarkers'];
		$nome = $_GET['nome'];
		ob_start();
		creaOggettoFormPers("editor", "prop_editor_".$numMarkers."_".$nome, "", "","","");
		$content = ob_get_clean();
		ob_end_flush();
		echo $content;
		return;
		break;
		
	case 'deleteMapsCache':
		$id = $_GET['id'];
		$nomecampo = $_GET['nomecampo'];
		@unlink('cache/media/'.$id.'_'.$nomecampo.'.gif');
		return;
	break;
		
	case 'deleteRssCache':
		$id = $_GET['id'];
		@unlink('cache/rss_'.$id.'.cache');
		echo 'cache/rss_'.$id.'.cache';
		return;
	break;
}

switch ($_POST['azione']) {
	case 'aggiungiRigaCig':
		$numRigaCig = $_POST['indice'];
		ob_start();
		include('./classi/regole/cig_multipli.php');
		$content = ob_get_clean();
		ob_end_flush();
		echo json_encode($content);
		
	break;
}
$database->sqlChiudi();
?>
