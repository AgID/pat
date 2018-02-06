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
	 * ajax_campi.php
	 * 
	 * @Descrizione
	 * File di risposta in ajax per il plugin jquery SELECT2 utilizzato nel backoffice PAT
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

$campo = isset ($_GET['campo']) ? forzaStringa($_GET['campo']) : '';
$ricerca = isset ($_GET['q']) ? forzaStringa($_GET['q']) : '';
$idSelezione = isset ($_GET['id_sel']) ? forzaNumero($_GET['id_sel']) : '';
// verifico la eventuale presenza di virgola nel valore passato di idSelezione (campi multipli)
$valoriSelezionati = explode(",",$idSelezione);

// REINIZIALIZZO LA VARIABILE MENU E TROVO I DATI SULLA FUNZIONE SCELTA
$idOggetto = is_numeric($_GET['id_ogg']) ? $_GET['id_ogg'] : 0;

// qui costruisco la pagina
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua e risolvo il bug
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

//pulizia dei valori
$valoriSelezionatiTemp = array();
foreach((array)$valoriSelezionati as $val) {
	if($val != '' and $val != 0 and $val > 0) {
		$valoriSelezionatiTemp[] = $val;
	}
}
$valoriSelezionati = $valoriSelezionatiTemp;
$idSelezione = implode(',', $valoriSelezionati);


// INIZIALIZZO SEMPRE LA SESSIONE PER QUESTIONI LEGATE ALL'EDITOR ED AL CKFINDER
session_start();

// forzo la disabilitazione della cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");    

// qui pubblico il template html
if ($datiUser['sessione_loggato']) {

	$server_url = $server_s_url;
	if ($datiUser['permessi'] != -1 and $datiUser['permessi'] != 0) {
	
		$outputArray = array();
		
		//////////////////////////////////////////// PERSONALE
		if ($campo == 'referente') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "referente LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}				
			
			$sql = "SELECT id,referente,ruolo,tit FROM ".$dati_db['prefisso']."oggetto_riferimenti WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati." ORDER BY referente LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i referenti (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				
				if ($rec['ruolo'] != '') {	
					$rec['referente'] = $rec['referente']." (".$rec['ruolo'].")";
				}
				if ($rec['tit'] != '') {	
					$rec['referente'] = $rec['tit']." ".$rec['referente'];
				}
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['referente'],
					'email' => $rec['email'],
					'ruolo' => $rec['ruolo']
				);
			}
		}
		
		//////////////////////////////////////////// STRUTURE
		if ($campo == 'struttura') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "nome_ufficio LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			$orderBy = " ORDER BY nome_ufficio";
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
				$orderBy = '';
			}
			
			$sql = "SELECT id,nome_ufficio FROM ".$dati_db['prefisso']."oggetto_uffici WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati.$orderBy." LIMIT 50";;
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutte le strutture (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['nome_ufficio']
				);
			}
		}
		
		//////////////////////////////////////////// INCARICHI
		if ($campo == 'incarichimulti') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "nominativo LIKE '%".$parola."%'";
					$condizioneRicerca[] = "oggetto LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}				
			
			$sql = "SELECT id,nominativo,oggetto FROM ".$dati_db['prefisso']."oggetto_incarichi WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati." ORDER BY nominativo LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti gli incarichi (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['nominativo'].' - '.$rec['oggetto']
				);
			}
		}
		
		//////////////////////////////////////////// NORMATIVA
		if ($campo == 'normativa') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "nome LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}				
			$sql = "SELECT id,nome FROM ".$dati_db['prefisso']."oggetto_normativa WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati." ORDER BY nome LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutta la normativa (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['nome']
				);
			}
		}
		
		//////////////////////////////////////////// PROCEDIMENTI
		if ($campo == 'procedimenti') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "nome LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}				
			$sql = "SELECT id,nome FROM ".$dati_db['prefisso']."oggetto_procedimenti WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati." ORDER BY nome LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i procedimenti (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['nome']
				);
			}
		}
		
		//////////////////////////////////////////// FORNITORI
		if ($campo == 'fornitore_singolo' or $campo == 'fornitori') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "nominativo LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}
			$condizioneSingolo = '';
			if($campo == 'fornitore_singolo') {
				$condizioneSingolo = " AND (tipologia='fornitore singolo' OR tipologia='')";
			}
			$sql = "SELECT id,nominativo,tipologia FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati.$condizioneSingolo." ORDER BY nominativo LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i fornitori (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				// ulteriori info nel nome
				$tipo = ' [fornitore singolo]';
				if($rec['tipologia'] == 'raggruppamento') {
					$tipo = ' [raggruppamento]';
				}
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['nominativo'].$tipo
				);
			}
		}
		
		//////////////////////////////////////////// BANDI DI CONCORSO
		if ($campo == 'bandiconcorso') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "oggetto LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}
			$condizioneAggiuntiva = " AND tipologia != 'esiti'";
			
			$sql = "SELECT id,oggetto,data_attivazione,data_scadenza FROM ".$dati_db['prefisso']."oggetto_concorsi WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati.$condizioneAggiuntiva." ORDER BY data_attivazione desc LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i fornitori (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				// ulteriori info nel nome
				$testoEti = $rec['oggetto']." [".visualizzaData($rec['data_attivazione'],'d/m/Y')." - ".visualizzaData($rec['data_scadenza'],'d/m/Y')."]";
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $testoEti
				);
			}
		}
		
		//////////////////////////////////////////// REGOLAMENTI
		if ($campo == 'regolamenti') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "titolo LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}
			$condizioneAggiuntiva = '';
			
			$sql = "SELECT id,titolo FROM ".$dati_db['prefisso']."oggetto_regolamenti WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati.$condizioneAggiuntiva." ORDER BY titolo LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i regolamenti (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				// ulteriori info nel nome
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['titolo']
				);
			}
		}
		
		//////////////////////////////////////////// PROVVEDIMENTI
		if ($campo == 'provvedimenti') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "oggetto LIKE '%".$parola."%'";
					$condizioneRicerca[] = "tipo LIKE '%".$parola."%'";
					$condizioneRicerca[] = "numero LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}
			$condizioneAggiuntiva = '';
			
			$sql = "SELECT id,oggetto,tipo,data FROM ".$dati_db['prefisso']."oggetto_provvedimenti WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati.$condizioneAggiuntiva." ORDER BY data DESC LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i provvedimenti (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				// ulteriori info nel nome
				$rec['oggetto'] = $rec['oggetto']." (".$rec['tipo']." del ".visualizzaData($rec['data'],'d-m-Y').")";
				
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['oggetto']
				);
			}
		}
		
		//////////////////////////////////////////// MODULISTICA
		if ($campo == 'modulistica') {
			// carico lista
			$condizioneRicerca = array();
			if ($ricerca != '') {
				// verifico la presenza delle lettere, prima scompongo la query
				if(strpos($ricerca, '"') !== false) {
					//ricerca esatta virgolettata
					$ricerca = str_replace('\"', '', $ricerca);
					$arrayCerca = array();
					$arrayCerca[0] = $ricerca;
				} else {
					$arrayCerca = explode(' ',$ricerca);
				}
				$presente = false;
				
				foreach ((array)$arrayCerca as $parola) {
					$condizioneRicerca[] = "titolo LIKE '%".$parola."%'";
				}
			}
			if(count($condizioneRicerca)) {
				$condizioneRicerca = " AND (".implode(' OR ', $condizioneRicerca).")";
			} else {
				$condizioneRicerca = '';
			}
			
			$condizioneSelezionati = '';
			if($valoriSelezionati[0]) {
				$condizioneSelezionati = " AND id IN (".$idSelezione.") ";
			}
			$condizioneAggiuntiva = '';
			
			$sql = "SELECT id,titolo FROM ".$dati_db['prefisso']."oggetto_modulistica_regolamenti WHERE id_ente=".$idEnteAdmin.$condizioneRicerca.$condizioneSelezionati.$condizioneAggiuntiva." ORDER BY titolo LIMIT 50";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti la modulistica (con condizione)'.$sql);
			}
			$records = $database->sqlArrayAss($result);
			
			foreach ((array)$records as $rec) {
				$outputArray[] = array(
					'id' => $rec['id'],
					'text' => $rec['titolo']
				);
			}
		}
				
		/*
		 * Elaboro output json con i risultati
		 */
		$output = array(			
			"more" => false,
			$campo => array()
		);	
	
		// encoding array in UTF8
		array_walk_recursive($outputArray, function(&$value, $key) {
			if (is_string($value)) {
				$value = iconv('windows-1252', 'utf-8', $value);
			}
		});
		
		if ($idSelezione AND $idSelezione != '') {
			if (count($valoriSelezionati)==1) {
				$output = $outputArray[0];
			} else {
				$output = $outputArray;
			}
		} else {		
			$output[$campo] = $outputArray;		
		}
		
		echo json_encode($output);		
		
	} 
} 


$database->sqlChiudi();
exit();

?>