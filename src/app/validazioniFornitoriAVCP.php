<?
include_once('app/funzioniAVCP.php');

$visualizzaAlert = false;
$testoTooltip = '';
$numErrori = 0;

if(strlen($istanzaOggetto['nominativo']) > 250) {
	$visualizzaAlert = true;
	$testoTooltip .= 'Nominativo (lunghezza maggiore di 250 caratteri), ';
	$numErrori++;
}

if(!validaCfPi($istanzaOggetto['codice_fiscale']) or (trim($istanzaOggetto['codice_fiscale']) == '' and trim($istanzaOggetto['fiscale_estero']) == '')) {
	$visualizzaAlert = true;
	$testoTooltip .= 'codice fiscale, ';
	$numErrori++;
}

if(trim($istanzaOggetto['tipologia']) == 'raggruppamento') {
	$visualizzaAlert = false;
	$testoTooltip = '';
	$numErrori = 0;
	if($istanzaOggetto['mandante'] == '' and $istanzaOggetto['mandataria'] == '' and $istanzaOggetto['associata'] == '' and $istanzaOggetto['capogruppo'] == '' and $istanzaOggetto['consorziata'] == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'dati errati del raggruppamento (nessuna azienda presente nel raggruppamento), ';
		$numErrori++;
	}
	$countAnagrafiche = 0;
	if($istanzaOggetto['mandante'] != '') {
		$countAnagrafiche++;
	}
	if($istanzaOggetto['mandataria'] != '') {
		$countAnagrafiche++;
	}
	if($istanzaOggetto['associata'] != '') {
		$countAnagrafiche++;
	}
	if($istanzaOggetto['capogruppo'] != '') {
		$countAnagrafiche++;
	}
	if($istanzaOggetto['consorziata'] != '') {
		$countAnagrafiche++;
	}
	if($countAnagrafiche < 2) {
		$visualizzaAlert = true;
		$testoTooltip .= 'il raggruppamento deve essere formato da almento 2 fornitori singoli, ';
		$numErrori++;
	}
}

if($visualizzaAlert) {
	$testoTooltip = 'I dati non validi ai fini della comunicazione ANAC sono: '.substr($testoTooltip, 0, strlen($testoTooltip)-2);
	$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"".$testoTooltip."\" class=\"btn\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
}
?>