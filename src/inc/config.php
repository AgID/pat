<?

// configurazione dati principali sul database
$dati_db = array(
	'tipo' => 'mysql',
	'host' => 'localhost',
	'user' => '',
	'password' => '',
	'database' => '',
	'database_offline' => '',
	'persistenza' => FALSE,
	'prefisso' => '',
	'like' => 'LIKE'
);

// verifico se usare protezione anti CSRF
$usaCSRF = true;

$dominio = "miodominio.it";
$server_url = "https://www.miodominio.it/";      // nota lo slash finale
$server_s_url = "https://www.miodominio.it/";      // nota lo slash finale

//NOTA: da modificare per il funzionamento del controllo dell'URL chiamato in sessioni.php
$dominio_query_sessioni = 'www.miodominio.it';

$uploadPath = "./download/";
$archiviomedia = "archiviofile/";
$media_su_db = TRUE;
$file_su_db = FALSE;
$utentiCondivisi = FALSE;
$pwd_encrypt = FALSE;

// Identificativi Pagine
define('PAGINA_INDEX', 0);
define('PAGINA_LOGIN', -1);
define('PAGINA_CERCA', -2);
define('PAGINA_REGISTRAZIONE', -3);
define('PAGINA_PROFILO', -4);
define('PAGINA_LISTAUTENTI', -5);
define('PAGINA_REVIEW', -6);

// Identificativi Utenti, non modificare user anonimo e superadmin
define('USER_ANONIMO', -1);
define('USER', 0);
define('USER_POWER', 1);
define('USER_ADMIN', 2);
define('USER_SUPERADMIN', 10);

// costanti transazioni sql
define('BEGIN_TRANSACTION', 1);
define('END_TRANSACTION', 2);

// configurazioni avanzate
define('FILE_ERRORE_MYSQL', "personalizzazioni/erroreDatabase.html");
define('PANNELLO_SISTEMA_SEPARATORE', "<!-- PERSONALIZZATO_SISTEMA -->");

/////////////////////////////////////////CONFIGURAZIONE FAMIGLIA STILI///////////////////////////////
$stiliInibiti = array();
$stiliInibiti[] = array(
	'famiglia' => 'zona',
	'tipo' => 'testata',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,allineamento,impedisci_allineamento,contenuto_testuale'
);
$stiliInibiti[] = array(
	'famiglia' => 'zona',
	'tipo' => 'colonna_sx',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,allineamento,impedisci_allineamento,distanza,contenuto_testuale'
);
$stiliInibiti[] = array(
	'famiglia' => 'zona',
	'tipo' => 'colonna_dx',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,allineamento,impedisci_allineamento,distanza,contenuto_testuale'
);
$stiliInibiti[] = array(
	'famiglia' => 'zona',
	'tipo' => 'centro',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,allineamento,impedisci_allineamento,contenuto_testuale'
);
$stiliInibiti[] = array(
	'famiglia' => 'zona',
	'tipo' => 'chiusura',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,allineamento,impedisci_allineamento,contenuto_testuale'
);
//////// PANNELLI
$stiliInibiti[] = array(
	'famiglia' => 'pannello',
	'tipo' => 'normale',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,position,pos_alto,pos_dx,pos_basso,pos_sx,testo_acapo'
);
////// MEDIA
$stiliInibiti[] = array(
	'famiglia' => 'media',
	'tipo' => 'normale',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,testo_acapo,testo_colore,link_colore,testo_font,testo_size,testo_lineheight,testo_effetti'
);
$stiliInibiti[] = array(
	'famiglia' => 'media',
	'tipo' => 'normale',
	'sottofamiglia' => 'immagine',
	'inibiti' => 'visualizza_titolo,visualizza_foot,testo_acapo,form,scroll,bgimg,bgimg_repeat,bgimg_position,testo_allineamento,testo_colore,link_colore,testo_font,testo_size,testo_spessore,testo_lineheight,testo_effetti'
);
////// MENU
$stiliInibiti[] = array(
	'famiglia' => 'menu',
	'tipo' => 'normale',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,testo_acapo,form,position,pos_alto,pos_dx,pos_basso,pos_sx,testo_colore,link_colore'
);
$stiliInibiti[] = array(
	'famiglia' => 'menu',
	'tipo' => 'normale',
	'sottofamiglia' => 'sottomenu',
	'inibiti' => 'display,form,position,testo_acapo,pos_alto,pos_dx,pos_basso,pos_sx,testo_colore,link_colore'
);
$stiliInibiti[] = array(
	'famiglia' => 'menu',
	'tipo' => 'normale',
	'sottofamiglia' => 'bottone',
	'inibiti' => 'link,display,form,position,pos_alto,pos_dx,pos_basso,pos_sx,visualizza_titolo,visualizza_foot,scroll,link_colore,testo_allineamento,link_colore,testo_font,testo_size,testo_lineheight,testo_effetti'
);
$stiliInibiti[] = array(
	'famiglia' => 'menu',
	'tipo' => 'normale',
	'sottofamiglia' => 'rollover',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,testo_acapo,position,pos_alto,pos_dx,pos_basso,pos_sx,scroll,testo_allineamento,link_colore,testo_font,testo_size,testo_lineheight,testo_effetti'
);
////// sottomenu
$stiliInibiti[] = array(
	'famiglia' => 'menu',
	'tipo' => 'normale',
	'sottofamiglia' => 'sottobottone',
	'inibiti' => 'link,display,form,position,pos_alto,pos_dx,pos_basso,pos_sx,visualizza_titolo,visualizza_foot,scroll,link_colore,testo_allineamento,link_colore,testo_font,testo_size,testo_lineheight,testo_effetti'
);
$stiliInibiti[] = array(
	'famiglia' => 'menu',
	'tipo' => 'normale',
	'sottofamiglia' => 'sottorollover',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,testo_acapo,position,pos_alto,pos_dx,pos_basso,pos_sx,scroll,testo_allineamento,link_colore,testo_font,testo_size,testo_lineheight,testo_effetti'
);
////// titoli
$stiliInibiti[] = array(
	'famiglia' => 'misto',
	'tipo' => 'normale',
	'sottofamiglia' => 'titolo',
	'inibiti' => 'link,display,form,visualizza_titolo,visualizza_foot,testo_acapo,position,pos_alto,pos_dx,pos_basso,pos_sx,visualizza_titolo,scroll,link_colore'
);
$stiliInibiti[] = array(
	'famiglia' => 'misto',
	'tipo' => 'normale',
	'sottofamiglia' => 'etichetta',
	'inibiti' => 'link,form,visualizza_titolo,visualizza_foot,position,testo_acapo,pos_alto,pos_dx,pos_basso,pos_sx,visualizza_titolo,scroll,link_colore'
);
////// contenuti ed editor
$stiliInibiti[] = array(
	'famiglia' => 'contenuto',
	'tipo' => 'normale',
	'sottofamiglia' => 'editor',
	'inibiti' => 'link,form,testo_acapo,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx'
);
$stiliInibiti[] = array(
	'famiglia' => 'misto',
	'tipo' => 'normale',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,testo_acapo,position,pos_alto,pos_dx,pos_basso,pos_sx'
);
$stiliInibiti[] = array(
	'famiglia' => 'contenuto',
	'tipo' => 'normale',
	'sottofamiglia' => 'nessuna',
	'inibiti' => 'link,display,form,testo_acapo,position,pos_alto,pos_dx,pos_basso,pos_sx'
);
$stiliInibiti[] = array(
	'famiglia' => 'misto',
	'tipo' => 'normale',
	'sottofamiglia' => 'contenuto_automatico',
	'inibiti' => 'link,display,testo_acapo,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx'
);
////// oggetti
$stiliInibiti[] = array(
	'famiglia' => 'oggetto',
	'tipo' => 'normale',
	'sottofamiglia' => 'campo',
	'inibiti' => 'link,position,pos_alto,pos_dx,pos_basso,pos_sx'
);
$stiliInibiti[] = array(
	'famiglia' => 'oggetto',
	'tipo' => 'normale',
	'sottofamiglia' => 'istanza',
	'inibiti' => 'link,display,testo_acapo,position,visualizza_titolo,visualizza_foot,pos_alto,pos_dx,pos_basso,pos_sx'
);
$stiliInibiti[] = array(
	'famiglia' => 'oggetto',
	'tipo' => 'normale',
	'sottofamiglia' => 'interfaccia',
	'inibiti' => 'link,display,testo_acapo,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx'
);
////// form
$stiliInibiti[] = array(
	'famiglia' => 'misto',
	'tipo' => 'normale',
	'sottofamiglia' => 'form',
	'inibiti' => 'link,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,scroll,link_colore'
);
$stiliInibiti[] = array(
	'famiglia' => 'misto',
	'tipo' => 'normale',
	'sottofamiglia' => 'formbottoni',
	'inibiti' => 'link,form,visualizza_titolo,visualizza_foot,position,pos_alto,pos_dx,pos_basso,pos_sx,scroll,link_colore'
);

$arrayElementiTitoloBreve = array(
	'breadcrumbs' => 'Pannello Breadcrumbs',
	'titolo_pagina' => 'Pannello titolo della sezione',
	'menu_auto' => 'Pannello menù di navigazione automatico',
	'menu_normale' => 'Menù del sito'
);


//////////////////////
// OPZIONI SPECIALI //
//////////////////////
$option['ip-zone']=0;   // 1 usa il database degli IP per il riconoscimento dei paesi.
// DEVE ESSERE INSTALLATO A PARTE!

// forzature configurazione PHP
//ini_set("magic_quotes_gpc",1);

// se nel php.ini non è abilitato il "magic_quotes_gpc"
if (!get_magic_quotes_gpc() AND !function_exists('magicSlashes')) {
    // funzione ricorsiva per l'aggiunta degli slashes ad un array
    function magicSlashes($element) {
        if (is_array($element))
            return array_map("magicSlashes", $element);
        else
            return addslashes($element);
    }
    // Aggiungo gli slashes a tutti i dati GET/POST/COOKIE
    if (isset ($_GET)     && count($_GET))    $_GET    = array_map("magicSlashes", $_GET);
    if (isset ($_POST)    && count($_POST))   $_POST   = array_map("magicSlashes", $_POST);
    if (isset ($_COOKIE) && count($_COOKIE))$_COOKIE = array_map("magicSlashes", $_COOKIE);
	
	$HTTP_GET_VARS = $_GET;
	$HTTP_POST_VARS = $_POST;
	$HTTP_COOKIES_VARS = $_COOKIE;
	$HTTP_FILES_VARS = $_FILES;

}
function htmlentities_54($string, $ent=ENT_COMPAT, $charset='ISO-8859-1') {
	return htmlentities($string, $ent, $charset);
}

function forzaStringa($string, $preg = '/[^a-zA-Z0-9-.:,_&=\' \/]+/') {
	$string = html_entity_decode($string, ENT_COMPAT, 'ISO-8859-1');
	$string = strip_tags($string);
	$string = preg_replace($preg, '', $string);
	return htmlentities($string, ENT_COMPAT, 'ISO-8859-1');
}
function forzaPercorso($string) {
	// verifico presenza di doppio punto per tornare indietro
	if (stripos($string, "..") !== false){
		//echo "<p>!!!!!! CORRISPONDENZA ".$input."</p>";
		return '';
	}	
	$string = html_entity_decode($string, ENT_COMPAT, 'ISO-8859-1');
	$string = strip_tags($string);
	$string = preg_replace('/[^a-zA-Z0-9-_.,\/]+/', '', $string);	
	return htmlentities($string, ENT_COMPAT, 'ISO-8859-1');
}
function forzaNumero($string) {
	$string = html_entity_decode($string, ENT_COMPAT, 'ISO-8859-1');
	$string = strip_tags($string);
	$string = preg_replace('/[^0-9-]+/', '', $string);	
	return htmlentities($string, ENT_COMPAT, 'ISO-8859-1');
}

// verifico variabili server
$z = strtolower($_SERVER['HTTP_HOST']);
$z = preg_replace('/[^a-z0-9-.]+/', '', $z);
$_SERVER['HTTP_HOST'] = trim($z);

?>
