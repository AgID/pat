<?php
/*
 * Created on 03/ott/2016
 */

/*
segnalazione di frosinone: ricerca di 'scimè' (personale ente) -> Sul DB la 'è' è salvata come '&egrave;'
con utf8_decode non funziona: per il momento tolgo la funzione utf8_decode
//$q = htmlentities((strtolower($_GET["term"])));

in ricercaOggetti.php stessa cosa: se c'è la utf8_encode viene visualizzato male il risultato
l'ho commentata anche lì

Questa modifica è stata fatta in data 05/11/2015
*/
if (isset($_GET['term'])) $_GET['term'] = forzaStringa($_GET['term']);
$_GET['term'] = strip_tags($_GET['term']);
$q = htmlentities(strtolower($_GET['term']),ENT_COMPAT,'ISO-8859-1');
$q = addslashes($q);

$response = array();
if (!$q) return;
if(!$sezioneNavigazione['template']) {
	$sezioneNavigazione['template'] = 1;
}
$limitaCaratteriOutput = 60;

$arrayCerca = array();
$arrayCerca = explode (" ", $q);
$nuovoCerca = array();
foreach($arrayCerca as $parola) {
	if (strlen($parola)>2) {
		$nuovoCerca[] = $parola;
	}
}
$arrayCerca = $nuovoCerca;

$condizioneEnte = " AND id_ente = ".$idEnte." ";
$orderBy = ' ORDER BY campo_data_ricerca DESC ';
$limit = ' LIMIT 0,3 ';

foreach ($oggetti as $oggetto) {
	if ($oggetto['id'] > 1 and $oggetto['id'] != 33 and $oggetto['ricercabile'] and $oggetto['proprieta'] != 'contatto' and $oggetto['campi_ricerca'] != '') {
		
		include('moduli/ricercaGenerica/ricercaGenericaOggetti.php');
		
		include('codicepers/ricercaOggetti.php');
		
	} else if ($oggetto['id'] == 1) {
		include('moduli/ricercaGenerica/ricercaGenericaSezioni.php');
		
		include('codicepers/ricercaModelloContenuto.php');
	}
}
/*
////////////////////////////////////////////////////////////////////////////////////////
//ricerca su bandi di concorso
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 22;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND data_attivazione < ".$domani." AND stato_pubblicazione = '100' ";
$ordinamentoPersonalizzato = " ORDER BY data_attivazione DESC ";

include('codicepers/ricercaOggetti.php');



////////////////////////////////////////////////////////////////////////////////////////
//ricerca su bandi di gara
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 11;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND data_attivazione < ".$domani." AND stato_pubblicazione = '100' ".$configurazione['condizione_bandi_archiviati'];
$ordinamentoPersonalizzato = " ORDER BY data_attivazione DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su bilanci
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 29;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = "AND stato_pubblicazione = '100' ";
$ordinamentoPersonalizzato = "";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su incarichi e consulenze
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 4;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND stato_pubblicazione = '100' AND dirigente != 1 ";
$ordinamentoPersonalizzato = " ORDER BY inizio_incarico DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su aree modulistica
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 5;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = "";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su normativa
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 27;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = "";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su oneri informativi
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 30;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su personale ente
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 3;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND (__archiviata != 1 OR __archiviata IS NULL) ";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');
		
////////////////////////////////////////////////////////////////////////////////////////
//ricerca su procedimenti
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 16;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND stato_pubblicazione = '100' AND (__archiviata != 1 OR __archiviata IS NULL) ";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su provvedimenti
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 28;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
$ordinamentoPersonalizzato = " ORDER BY data DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su regolamenti e documentazione
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 19;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su sovvenzioni e vantaggi economici
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 38;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
$ordinamentoPersonalizzato = " ORDER BY data DESC ";

include('codicepers/ricercaOggetti.php');

////////////////////////////////////////////////////////////////////////////////////////
//ricerca su strutture organizzative
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 13;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = $o->nome;
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = " AND (__archiviata != 1 OR __archiviata IS NULL) ";
$ordinamentoPersonalizzato = " ORDER BY ultima_modifica DESC ";

include('codicepers/ricercaOggetti.php');
////////////////////////////////////////////////////////////////////////////////////////
//ricerca su modello di contenuto
////////////////////////////////////////////////////////////////////////////////////////

$idOggetto = 33;
$o = new documento($idOggetto,'si');
$idSezioneElenco = 0;
$nomeOggetto = "Sezioni del sito";
$campo = $o->campo_default;

$campoIdEnte = 'id_ente';
$condizioniAggiuntive = "";
$ordinamentoPersonalizzato = "";

include('codicepers/ricercaModelloContenuto.php');
*/

////////////////////////////////////////////////////////////////////////////////////////
//operazioni di fine ricerca
////////////////////////////////////////////////////////////////////////////////////////
if(count($response) == 0) {
	$response[] = array(
		'label' => 'Nessun risultato',
		'objName' => 1
	);
}
$database->sqlChiudi();
$encoded = json_encode($response);
header('Content-type: application/json');
header("X-Frame-Options: sameorigin");
exit($encoded);

?>