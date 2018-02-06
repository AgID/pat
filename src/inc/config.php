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
	 * inc/config.php
	 * 
	 * @Descrizione
	 * File di configurazione variabili di connessione DB e di raggiungibilita del servizio. Copia a scopo didattivo della versione standard inclusa in ISWEB.
	 * E' possibile copiare queste impostazioni dalla configurazione attiva sulla piattaforma ISWEB utilizzata per l'installazione di PAT
	 *
	 */


/*********************************************VARIABILI ACCESSO DBMS*********************************/ 
/* 
Editare la seguente matrice con i dati per accedere al DBMS
*/ 
$dati_db = array(
	'tipo' => 'mysql', // tipo di dbms utilizzato. Non editare, al momento supportato solo MySQL
	'host' => 'localhost', // indirizzo del server su cui risiede il servizio DBMS
	'user' => '', // nome utente per l'accesso al DBMS
	'password' => '', // password per l'accesso al DBMS
	'database' => '', // nome database utilizzato
	'database_offline' => '', // NON EDITARE, non utilizzato in PAT
	'persistenza' => FALSE, // utilizzo di connessioni persistenti, consigliato FALSE
	'prefisso' => '', // eventuale previsso delle tabelle ISWEB quando il database ISWEB è condiviso con altri applicativi
	'like' => 'LIKE' // NON EDITARE, non utilizzato in PAT
);


/*********************************************VARIABILI ACCESSO DBMS*********************************/ 
/* 
Editare le seguenti variabili per configurare la piattaforma a rispondere al dominio principale
*/ 
$dominio = "localhost";
$server_url = "http://www.nomesito.it/";      // nota lo slash finale
$server_s_url = "https://www.nomesito.it/";      // nota lo slash finale
$usaCSRF = TRUE; // configurare per attivare protezione anti CSRF

/* 
Variabili da non editare
*/ 
$uploadPath = "./download/";  // non editare
$archiviomedia = "./archiviofile/"; // non editare
$media_su_db = TRUE; // non editare, non utilizzato in PAT
$file_su_db = FALSE; // non editare, non utilizzato in PAT
$utentiCondivisi = FALSE; // non editare, non utilizzato in PAT


/*********************************************COSTANTI DI SISTEMA ISWEB*********************************/ 
/* 
Non editare queste configurazioni
*/ 
define('PAGINA_INDEX', 0);
define('PAGINA_LOGIN', -1);
define('PAGINA_CERCA', -2);
define('PAGINA_REGISTRAZIONE', -3);
define('PAGINA_PROFILO', -4);
define('PAGINA_LISTAUTENTI', -5);
define('PAGINA_REVIEW', -6);

define('USER_ANONIMO', -1);
define('USER', 0);
define('USER_POWER', 1);
define('USER_ADMIN', 2);
define('USER_SUPERADMIN', 10);

define('BEGIN_TRANSACTION', 1);
define('END_TRANSACTION', 2);

define('FILE_ERRORE_MYSQL', "personalizzazioni/erroreDatabase.html");
define('PANNELLO_SISTEMA_SEPARATORE', "<!-- PERSONALIZZATO_SISTEMA -->");

/*********************************************ATTRIBUTI PER FAMIGLIE DI STILI ISWEB*********************************/ 
/* 
Non editare queste configurazioni
*/ 
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
	'menu_auto' => 'Pannello men� di navigazione automatico',
	'menu_normale' => 'Men� del sito'
);


/*********************************************VERIFICHE COMPATIBILITA' PHP*********************************/ 
/* 
Non editare queste configurazioni
*/ 

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
    if (isset ($_COOKIES) && count($_COOKIES))$_COOKIE = array_map("magicSlashes", $_COOKIE);
	
	$HTTP_GET_VARS = $_GET;
	$HTTP_POST_VARS = $_POST;
	$HTTP_COOKIES_VARS = $_COOKIES;
	$HTTP_FILES_VARS = $_FILES;

}

// sanitizzo forzatamente variabile host
$z = strtolower($_SERVER['HTTP_HOST']);
$z = preg_replace('/[^a-z0-9-.]+/', '', $z);
$_SERVER['HTTP_HOST'] = trim($z);

?>
