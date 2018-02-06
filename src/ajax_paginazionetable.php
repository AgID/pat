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
	 * ajax_paginazionetable.php
	 * 
	 * @Descrizione
	 * File di risposta ajax alla paginazione del plugin DataTables - ATTENZIONE: versione obsoleta, utilizzare il nuovo ajax_paginazionetable_new
	 *
	 */	

// Queste funzioni possono richiedere molta RAM, in caso di un database particolarmente pesante aumentare il limite impostato 
//ini_set('memory_limit','512M'); 

// inclusione configurazione 
include ('./inc/config.php'); // configurazione ISWEB

// eseguo inizializzazione ambiente backoffice ISWEB
include ('./inc/inizializzazione_admin.php');

/*********************************************INIZIALIZZO AMBIENTE E VARIABILI*********************************/
/* 
inizializzazione e sanitizzazione di tutte le variabili principali del sistema viene effettuata dal servizio di inizializzazione di ISWEB
*/ 

// inizializzo le variabili dedicate a PAT
$menu = isset ($_GET['menu']) ? forzaStringa($_GET['menu']) : 'desktop';
$menuSecondario = isset ($_GET['menusec']) ? forzaStringa($_GET['menusec']) : '';
$azione = isset ($_GET['azione']) ? forzaStringa($_GET['azione']) : 'lista'; // attenzione, sovrascrive eventuale variabile ISWEB
$azioneSecondaria = isset ($_GET['azionesec']) ? forzaStringa($_GET['azionesec']) : ''; // attenzione, sovrascrive eventuale variabile ISWEB
$id = is_numeric($_GET['id']) ? forzaNumero($_GET['id']) : 0; $idIstanza = $id; // attenzione, sovrascrive eventuale variabile ISWEB
$idOggetto = is_numeric($_GET['id_ogg']) ? forzaNumero($_GET['id_ogg']) : 0; // attenzione, sovrascrive eventuale variabile ISWEB
$idCategoria = is_numeric($_GET['id_cat']) ? forzaNumero($_GET['id_cat']) : 0; // attenzione, sovrascrive eventuale variabile ISWEB


// qui costruisco la pagina
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua e risolvo il bug
if ($datiUser['sessione_idlingua'] == 0 or $datiUser['sessione_idlingua'] == '') {
	$datiUser['sessione_idlingua'] = 1;	
}
// le funzioni MULTILINGUA di ISWEB non vengono utilizzate
$idLingua = 1;

include ('./pat/config_pat.php'); // configurazione PAT

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
header("Pragma: no-cache");    

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


// qui pubblico il template html
if ($datiUser['sessione_loggato']) {
	$server_url = $server_s_url;
	if ($datiUser['permessi'] != -1 and $datiUser['permessi'] != 0) {

		// controllo utente
		include('./inc/controllo_user.php');
		include('./pat/controllo_user.php');

		// importo la classe di amministrazione oggetti
		require('classi/admin_oggetti.php');
		$oggOgg = new oggettiAdmin($idOggetto);				
		
		// ELABORO LISTA OGGETTI NON CATEGORIZZATI
  		if (!$oggOgg->idCategoria) {
  			$listaTabella = $oggOgg->visualizzaListaOggettiNoCat(0, 'tutti', 'ultima_modifica', 'desc', $idEnteAdmin); 
		} else {
			// ELABORO LISTA CATEGORIE OGGETTO
			$lista = $oggOgg->visualizzaLista($idCategoria);
			$numCategorie = count($lista);
			$listaTabella = $oggOgg->visualizzaListaOggetti($idCategoria, $inizio, $limite, $campoOrdine, $ordine); 
		}
		
		///////////////////////// NUOVO METODO DI CARICAMENTO DEI DOCUMENTI

		//INIZIALIZZZO VARIABILI
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
		}
		
		$numOggetti = count($listaTabella);
		
		// analizzo quali campi visualizzare in amministrazione, prendendoli dalla struttura IsWEB
		$campiVisualizzati = array();
		$struttura = $oggOgg->parsingStruttura('si');
		for ($i=0;$i<count($oggOgg->campiAdmin);$i++) {
			$campiVisualizzati[$i]['campo'] = $oggOgg->campiAdmin[$i]; 
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
	
		$visualizzaInterfaccia = true;
		$outputArray = array();
		
		/*
		echo "<pre>";
		print_r($listaTabella);
		echo "</pre>";
		*/
		
		
		$numChiave = 0;
		foreach ($listaTabella as $istanzaOggetto) {		
			// includo renderizzazione in array della riga
			include ('./pat/admin_template/oggetti/tab_row_ajax.tmp');
			$numChiave++;
		}	
		
		// encoding array in UTF8
		array_walk_recursive($outputArray, function(&$value, $key) {
			if (is_string($value)) {
				$value = iconv('windows-1252', 'utf-8', $value);
			}
		});
		
		echo json_encode( array("aaData" => $outputArray) );
	
	} 
} 

$database->sqlChiudi();
exit();

?>