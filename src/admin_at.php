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

$menu = isset ($_GET['menu']) ? forzaStringa($_GET['menu']) : 'desktop';
$menuSecondario = isset ($_GET['menusec']) ? forzaStringa($_GET['menusec']) : '';
$azione = isset ($_GET['azione']) ? forzaStringa($_GET['azione']) : 'lista';
$id = is_numeric($_GET['id']) ? forzaNumero($_GET['id']) : 0;
$idOggetto = is_numeric($_GET['id_ogg']) ? forzaNumero($_GET['id_ogg']) : 0;
$func = isset ($_GET['func']) ? forzaStringa($_GET['func']) : '';
$idIstanza = $id;

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
$inAmministrazione = true;
include ('./codicepers/config.php');

$configurazione['__tags_gare'] = prendiTagsGare(false, true);
$configurazione['__tags_concorsi'] = prendiTagsConcorsi(false, true);
$configurazione['__tags_provvedimenti'] = prendiTagsProvvedimenti(false, true);

// INIZIALIZZO SEMPRE LA SESSIONE PER QUESTIONI LEGATE ALL'EDITOR ED AL CKFINDER
session_start();

// forzo la disabilitazione della cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0
header("X-Frame-Options: sameorigin");


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

			// richiamo template
			require('./template/at/template.tmp');
			
		} else {
			die('Non hai i permessi per accedere al pannello di amministrazione');
		}
	} else {

		echo "Non hai i permessi per questa funzione.";
		
	}

} else {
	
	// includo pagina con errore di accesso
	require('./app/admin_template/errore_browser.tmp');
	
}

include('./inc/chiusura.php');
?>