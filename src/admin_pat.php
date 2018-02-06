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
	 * admin_pat.php
	 * 
	 * @Descrizione
	 * File di lancio per l'ambiente backoffice di PAT.
	 *
	 */	

// inclusione configurazione 
include ('./inc/config.php'); // configurazione ISWEB
 
 
/*********************************************INIZIALIZZO AMBIENTE E VARIABILI*********************************/
/* 
inizializzazione e sanitizzazione di tutte le variabili principali del sistema viene effettuata dal servizio di inizializzazione di ISWEB
*/ 
// eseguo inizializzazione ambiente backoffice ISWEB
include ('./inc/inizializzazione_admin.php');
// inizializzo le variabili dedicate a PAT
$menu = isset ($_GET['menu']) ? forzaStringa($_GET['menu']) : 'desktop'; // nuova variabile esclusiva PAT
$menuSecondario = isset ($_GET['menusec']) ? forzaStringa($_GET['menusec']) : ''; // nuova variabile esclusiva PAT
$box = $_GET['box'] ? true : false; // nuova variabile esclusiva PAT
$azione = isset ($_GET['azione']) ? forzaStringa($_GET['azione']) : 'lista'; // attenzione, sovrascrive eventuale variabile ISWEB
$azioneSecondaria = isset ($_GET['azionesec']) ? forzaStringa($_GET['azionesec']) : ''; // attenzione, sovrascrive eventuale variabile ISWEB
$id = is_numeric($_GET['id']) ? forzaNumero($_GET['id']) : 0; $idIstanza = $id; // attenzione, sovrascrive eventuale variabile ISWEB
$idOggetto = is_numeric($_GET['id_ogg']) ? forzaNumero($_GET['id_ogg']) : 0; // attenzione, sovrascrive eventuale variabile ISWEB
$idCategoria = is_numeric($_GET['id_cat']) ? forzaNumero($_GET['id_cat']) : 0; // attenzione, sovrascrive eventuale variabile ISWEB


/* 
inizializzo matrice $datiUser: al suo interno è possibile accedere ai dati dell'utente che ha effettuato l'accesso. 
Nel caso l'utente non sia autenticato, i dati saranno dell'utente generico "anonimo"
*/
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua ed applico un bugfix per PAT
if ($datiUser['sessione_idlingua'] == 0 or $datiUser['sessione_idlingua'] == '') {
	$datiUser['sessione_idlingua'] = 1;	
}
$lingua = caricaLingua($datiUser['sessione_idlingua']);
$idLingua = $lingua['id'];


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

/* 
Elaboro le funzioni da rendere disponibili nel menu
*/
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

// INIZIALIZZO SEMPRE LA SESSIONE CLASSICA PHP PER NON LIMITARE ALCUNE FUNZIONI DEL CKEDITOR
session_start();


/*********************************************ELABORO OUTPUT AMBIENTE BACKOFFICE PAT*********************************/

// forzo la disabilitazione della cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                          // HTTP/1.0

// controllo se e' la risposta al login
if (isset($_POST['login'])) {
	// analizzo i permessi aggiuntivi PAT
	require('./inc/funzioni_user.php');
}

// controllo dati sulla sezione
$idSezione = 0;
if ($_GET['menu']=='sezioni') {
   $idSezione = $_GET['id'];
}

/* 
Nel caso l'utente sia stato autenticato, verifico le caratyteristiche del suo client e del suo browser per eventuali 
incompatabilità con l'ambiente backoffice di PAT
*/
$compatibile = true;
if ($datiUser['sessione_loggato']) {
	// carico versione 5 di browscap
	require('./inc/browscap5.php');
	// Inizializzo oggetto puntando alla cache
	$bc = new Browscap('inc/cachebrowscap5');
	// Prelevo informazioni useragents
	$infoBrowser = $bc->getBrowser();
	// inserisco controllo compatibilità BROWSER	
	if ($infoBrowser->Browser == 'IE' AND $infoBrowser->MajorVer <= 8) {
		$compatibile = false;
	}
}

if ($compatibile) {
	// qui pubblico il template html del backoffice
	if ($datiUser['sessione_loggato']) {
		$server_url = $server_s_url;
		if ($datiUser['permessi'] != -1 and $datiUser['permessi'] != 0) {

			// controllo permessi utente 
			include('./inc/controllo_user.php'); // ISWEB
			include('./pat/controllo_user.php'); // PAT

			// richiamo il template
			if($box) {
				require('./template/admin_pat_solo_contenuto.tmp');
			} else {
				require('./template/admin_pat.tmp');
			}			
		} else {		
			echo 'Non hai i permessi per accedere al pannello di amministrazione';			
		}
	} else {
		require('./template/admin_standard/login.tmp');		
	}

} else {
	// includo pagina con errore di compatibilità del browser
	require('./pat/admin_template/errore_browser.tmp');
}

/* 
Includo il file di chiusura da ISWEB
*/
include('./inc/chiusura.php');

?>
