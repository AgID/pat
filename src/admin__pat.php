<?

/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

PAT - Portale Amministrazione Trasparente
Copyright AgID Agenzia per l'Italia Digitale

Concesso in licenza a norma dell'EUPL(la "Licenza"), versione 1.2;
*/

include ('./inc/config.php');
include ('./inc/inizializzazione_admin.php');

// REINIZIALIZZO LA VARIABILE MENU E TROVO I DATI SULLA FUNZIONE SCELTA
$inAmministrazione = true;

$menu = isset ($_GET['menu']) ? forzaStringa($_GET['menu']) : 'desktop';
$menuSecondario = isset ($_GET['menusec']) ? forzaStringa($_GET['menusec']) : '';
$azione = isset ($_GET['azione']) ? forzaStringa($_GET['azione']) : 'lista';
$azioneSecondaria = isset ($_GET['azionesec']) ? forzaStringa($_GET['azionesec']) : '';
$id = is_numeric($_GET['id']) ? forzaNumero($_GET['id']) : 0;
$idOggetto = is_numeric($_GET['id_ogg']) ? forzaNumero($_GET['id_ogg']) : 0;
$idCategoria = is_numeric($_GET['id_cat']) ? forzaNumero($_GET['id_cat']) : 0;
$box = $_GET['box'] ? true : false;
$idIstanza = $id;

/*
if (!$datiUser['user_loggato'] AND $usaCSRF) {
    // INIZIALIZZO SEMPRE LA SESSIONE PER QUESTIONI LEGATE ALLA PROTEZIONE CSRF DEI FORM
    include_once('./inc/nocsrf.php');
    $tokenCSRF = NoCSRF::generateNoSessione( 'csrf_token' );
    session_start();
}
*/

// controllo se e' la risposta al login
if (isset($_POST['login'])) {
	require('./inc/funzioni_user.php');
	
	//eliminazione allegati dinamici temporanei
	eliminaAllegatiTemporanei();
}

// qui costruisco la pagina
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua e risolvo il bug
if ($datiUser['sessione_idlingua'] == 0 or $datiUser['sessione_idlingua'] == '') {
	$datiUser['sessione_idlingua'] = 1;	
}

$lingua = caricaLingua($datiUser['sessione_idlingua']);
$idLingua = $lingua['id'];

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

//questa inclusione deve stare dopo il caricamento delle sezioni e della sezioneNavigazione
if(file_exists('codicepers/ente/'.$configurazione['piattaforma_at'].'/operazioniSuSezioni/'.$configurazione['piattaforma_at'].'.php')) {
	include('codicepers/ente/'.$configurazione['piattaforma_at'].'/operazioniSuSezioni/'.$configurazione['piattaforma_at'].'.php');
}
if(file_exists('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/operazioniSuSezioni/'.$entePubblicato['nome_breve_ente'].'.php')) {
	include('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/operazioniSuSezioni/'.$entePubblicato['nome_breve_ente'].'.php');
}

// carico il tipo di ente in amministrazione
$tipoEnte = datoTipoEnte($enteAdmin['tipo_ente']);

$archiviAdminEsclusi = explode(',', $tipoEnte['archivi_esclusi']);

include ('./app/config_pat.php');

//Configurazione smtp
if($enteAdmin['smtp_host'] != '') {
	$configurazione['usa_smtp'] = 1;
	$configurazione['smtp_host'] = $enteAdmin['smtp_host'];
	$configurazione['smtp_username'] = $enteAdmin['smtp_username'];
	$configurazione['smtp_password'] = $enteAdmin['smtp_password'];
	$configurazione['smtp_port'] = $enteAdmin['smtp_port'];
	$configurazione['smtp_s'] = $enteAdmin['smtp_s'];
	$configurazione['smtp_auth'] = $enteAdmin['smtp_auth'];
}

//////////////// INIZIALIZZAZIONE AGGIUNTIVA ETRASPARENZA (ADMIN)
include ('./codicepers/config.php');

$configurazione['__tags_gare'] = prendiTagsGare(false, true);
$configurazione['__tags_concorsi'] = prendiTagsConcorsi(false, true);
$configurazione['__tags_provvedimenti'] = prendiTagsProvvedimenti(false, true);

// ELABORO LE VARIABILI DEL MENU
foreach ($funzioniMenu as $funzione) {
	if ($menu == $funzione['menu']) {
		$funzioneMenu = $funzione;
		if ($menuSecondario != '') {
			foreach ($funzione['sottoMenu'] as $funzioneSotto) {
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
header("Pragma: no-cache");                          // HTTP/1.0
header("X-Frame-Options: sameorigin");


// controllo se e' la risposta al login
if (isset($_POST['login'])) {
	require('./inc/funzioni_user.php');
}

//verifica password scaduta
$configurazione['msg_password_scaduta'] = false;
$dataCheck = DateTime::createFromFormat('U',time());
$ultimoCambio = DateTime::createFromFormat('U',$datiUser['refresh_password']);
$diffDate = $dataCheck->diff($ultimoCambio)->format("%a");	//giorni dall'ultimo cambio

if($datiUser['scadenza_password_giorni']) {
	$configurazione['scadenza_password_giorni'] = $datiUser['scadenza_password_giorni'];
}

$primoAccesso = false;
$primoAccessoInScadenza = false;
if($configurazione['password_primo_accesso']) {
	$sql = "SELECT * FROM ".$dati_db['prefisso']."utenti_password WHERE id_utente=".$datiUser['id']." LIMIT 1";
	if ( !($result = $database->connessioneConReturn($sql)) ) { }
	$rec = $database->sqlArray($result);
	if(!$rec['id']) {
		//nessun cambio password: questa password sarà valida un solo giorno dalla data di creazione
		$dataCheck = DateTime::createFromFormat('U',time());
		$reg = DateTime::createFromFormat('U',$datiUser['data_registrazione']);
		$diffDate = $dataCheck->diff($reg)->format("%a");	//giorni dall'ultimo cambio
		if($diffDate>=1) {
			$primoAccesso = true;
		} else if($diffDate == 0) {
			$primoAccessoInScadenza = true;
		}
	}
}

//if (($datiUser['refresh_password'] AND ($oraCorrente-$datiUser['refresh_password'])>$configurazione['scadenza_password']) OR (!$datiUser['refresh_password'] AND ($oraCorrente-$datiUser['data_registrazione'])>$configurazione['scadenza_password'])) {
if($diffDate >= $configurazione['scadenza_password_giorni'] OR $primoAccesso) {
	if ($datiUser['permessi']!=10 and $datiUser['permessi']!=3) {
		$configurazione['msg_password_scaduta'] = true;
		//reset menu di amministrazione (forzo desktop, unica eccezione modifica utente)
		if($menu != 'utenti' or ($azione != 'modifica' and $azioneSecondaria != 'modifica') or !$id) {
			$menu = 'desktop';
			$menuSecondario = '';
			$azione = 'lista';
			$azioneSecondaria = '';
		}
	}
}

if ($configurazione['scadenza_password_giorni'] - $diffDate <= 15) {
	$configurazione['msg_password_in_scadenza'] = $configurazione['scadenza_password_giorni'] - $diffDate;
} else if($primoAccessoInScadenza) {
	$configurazione['msg_password_in_scadenza'] = 1;
}

// controllo dati sulla sezione
$idSezione = 0;
if ($_GET['menu']=='sezioni') {
    $idSezione = $_GET['id'];
}
	

/*
echo "<br />Ente Navigazione: ".$idEnte;
echo "<br />Ente Amministrazione: ".$idEnteAdmin;
*/

/*
/////////////////////////////////////////////////VERIFICA CARATTERISTICHE DEL CLIENT///////////////////////////////////////
if ($datiUser['sessione_loggato'] and $datiUser['permessi']>1) {

}
*/
// QUI DEVO INSERIRE IL CONTROLLO SULLE CAPACITA DEL BROWSER.
$compatibile = true;
/*
if ($datiUser['sessione_loggato']) {

	// carico versione 5
	require('./inc/browscap5.php');
	// Inizializzo oggetto puntando alla cache
	$bc = new Browscap('inc/cachebrowscap5');
	// Prelevo informazioni useragents
	$infoBrowser = $bc->getBrowser();

	// inserisco controllo	
	if ($infoBrowser->Browser == 'IE' AND $infoBrowser->MajorVer <= 8) {
		$compatibile = false;
	}
}
*/

if ($compatibile) {

	// qui pubblico il template html
	if ($datiUser['sessione_loggato'] and !$enteAdmin['disdetta_ente']) {
		$server_url = $server_s_url;
		if ($datiUser['permessi'] != -1 and $datiUser['permessi'] != 0) {

			// controllo utente
			include('./inc/controllo_user.php');
			include('./app/controllo_user.php');

			// richiamo il template
			if($box) {
				require('./template/admin_trasparenza_solo_contenuto.tmp');
			} else {
				require('./template/admin_trasparenza.tmp');
			}
			
		} else {
		
			die('Non hai i permessi per accedere al pannello di amministrazione');
			
		}
	} else {

		require('./app/admin_template/login.tmp');
		//die('Gli utenti non autenticati, non possono accedere al pannello di amministrazione');
		
	}

} else {
	
	ob_start();
	echo '<pre>'; print_r($infoBrowser); echo '</pre>';
	$content = ob_get_contents();
	ob_end_clean();
	file_put_contents('temp/infobrowser'.mktime().'.html', $content);
	
	// includo pagina con errore di accesso
	require('./app/admin_template/errore_browser.tmp');
	
}

include('./inc/chiusura.php');

?>
