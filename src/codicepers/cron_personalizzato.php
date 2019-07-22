<?


//NON ABILITARE MAI PIU'
//cron per l'esportazione dei dati da comunicare all'avcp (es. 2013.xml)
//include_once('app/aggiornaFilesAVCP.php');

//inserire qui log da far visualizzare in output
$log = '';

//modificare (ore 7)
$orarioCron = 7;

if(date('G', mktime()) == $orarioCron and moduloAttivo('notifica_revisione_pagina')) {
	//verificare le scadenze delle revisioni di pagina modello notifica_revisione_pagina
	
	$log .= '<br />Modulo revisone pagina';
	
	include_once('./classi/costruisci_mail.php');
	$mailAvviso = new costruisciMail('vuota', 'Cron \'notifica_revisione_pagina\' presente su '.$configurazione['denominazione_trasparenza'], $configurazione['mail_reparto_tecnico'], $configurazione['mail_sito']);
	$mailAvviso->assegnaVariabili(array(
			'NOMESITO' => $configurazione['nome_sito'],
			'TESTO' => 'Cron \'notifica_revisione_pagina\' presente su '.$configurazione['denominazione_trasparenza']
	));
	$mailAvviso->invia();
	
	$giorni = 7;
	include('classi/notifiche_revisione_pagina.php');
	
	$giorni = 3;
	include('classi/notifiche_revisione_pagina.php');
	
	$giorni = 0;
	include('classi/notifiche_revisione_pagina.php');
}

if($log != '') {
	echo $log;
	
	include_once 'classi/costruisci_mail.php';
	$mailAvviso = new costruisciMail('vuota', 'Termine cron \'notifica_revisione_pagina\' su '.$configurazione['denominazione_trasparenza'], $configurazione['mail_reparto_tecnico']);
	$mailAvviso->assegnaVariabili(array(
			'NOMESITO' => $configurazione['nome_sito'],
			'TESTO' => $log
	));
	$mailAvviso->invia();
}
?>