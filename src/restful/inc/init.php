<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.0 - AgID release//////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

 
require('../inc/config.php');
require('../inc/core_mini.php');
require('../classi/database.php');
require('inc/utility.php');

// inzializzo l'oggetto database per le connessioni
$database = new database($dati_db['host'], $dati_db['user'], $dati_db['password'], $dati_db['database'], $dati_db['persistenza']);
$configurazione = loadingConfigurazione();
$oggetti = loadingOggetti();
$sezioni = loadingSezioni();
$oggettiEtrasparenza = array(22,11,29,41,33,4,5,27,30,3,16,28,19,38,13);
$oggettiRicercabili = "11-22-29-4-5-16-3-19-13-27-28-30-33";
$idOggettoModello = 33;
/*
$oggettiEtrasparenza = array(
	22	=> 'concorsi',
	11	=> 'gare_atti',
	29	=> 'bilanci',
	41	=> 'elenco_fornitori',
	33	=> 'etrasp_modello',
	4	=> 'incarichi',
	5	=> 'modulistica_regolamenti',
	27	=> 'normativa',
	30	=> 'oneri',
	3	=> 'riferimenti',
	16	=> 'procedimenti',
	28	=> 'provvedimenti',
	19	=> 'regolamenti',
	38	=> 'sovvenzioni',
	13	=> 'uffici'
);
*/
?>