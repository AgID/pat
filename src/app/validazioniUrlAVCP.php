<?
include_once('app/funzioniAVCP.php');

$visualizzaAlert = false;
$testoTooltip = '';
$numeroErrori = 0;
$verificaFile = false;
$schema = './avcp/datasetAppaltiL190.xsd';
$schemaIndice = './avcp/datasetIndiceAppaltiL190.xsd';

// Enable user error handling 
libxml_use_internal_errors(true); 

$urlStazione = '';
if($istanzaOggetto['id_stazione'] > 0) {
	$urlStazione = 's'.$istanzaOggetto['id_stazione'].'_';
}
$file = './avcp/'.$istanzaOggetto['id_ente'].'/'.$urlStazione.$istanzaOggetto['anno'].'.xml';
if($istanzaOggetto['__personalizzato']) {
	$file = $istanzaOggetto['url'];
	$headers=get_headers($file);
	if(stripos($headers[0],"200 OK")) {
		$verificaFile = true;
	}
	if($istanzaOggetto['__tipoxml'] == 'indice') {
		$schema = $schemaIndice;
	}
} else if(file_exists($file)) {
	$verificaFile = true;
}
if($verificaFile) {

	$xml = new DOMDocument(); 
	$xml->load($file); 

	if (!$xml->schemaValidate($schema)) { 
		$visualizzaAlert = true;
		$numeroErrori += libxml_display_errors();
	}
	//validazione aggiuntiva dei campi data
	$erroriData = xmlValidaDate($file);
	if ($erroriData['num']) {
		$visualizzaAlert = true;
		$numeroErrori += $erroriData['num'];
	}

	if($visualizzaAlert and $numeroErrori > 0) {
		if($numeroErrori > 1) {
			$testoTooltip .= 'Il file xml per la comunicazione all\'ANAC contiene '.$numeroErrori.' errori';
		} else {
			$testoTooltip .= 'Il file xml per la comunicazione all\'ANAC contiene '.$numeroErrori.' errore';
		}
		if($datiUser['permessi'] == 10 OR $datiUser['permessi']==3) {
			$testoTooltip .= ' ('.'./avcp/'.$istanzaOggetto['id_ente'].'/'.$istanzaOggetto['anno'].'.xml'.')';
		}
		$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
	}
} else {
	$testoTooltip .= 'Impossibile verificare il file xml per la comunicazione all\'ANAC. Entrare in modifica e salvare i dati per effettuare una nuova verifica.';
	$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
}
?>