<?php

/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

PAT - Portale Amministrazione Trasparente
Copyright AgID Agenzia per l'Italia Digitale

Concesso in licenza a norma dell'EUPL(la "Licenza"), versione 1.2;
*/

include ('./inc/config.php');
include ('./inc/inizializzazione.php');

// forzo la disabilitazione della cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("X-Frame-Options: sameorigin");

$fileSorgente = $_GET['xml'];

$src = new DOMDocument('1.0', 'utf-8');
$src->load($fileSorgente);
$lotti = $src->getElementsByTagName('lotto');

$i = 0;
$istanze = array();
foreach($lotti as $lotto ){
	$figli = $lotto->childNodes;
	$istanza = array(
			'codiceFiscaleProp' => '',
			'denominazione' => '',
			'cig' => '',
			'oggetto' => '',
			'sceltaContraente' => '',
			'partecipanti' => '',
			'aggiudicatari' => '',
			'dataInizio' => '',
			'dataUltimazione' => '',
			'importoAggiudicazione' => '',
			'importoSommeLiquidate' => ''
	);
	for($i = 0; $i < $figli->length; $i++) {
		switch($figli->item($i)->nodeName) {
			case "cig":
			case "oggetto":
			case "sceltaContraente":
			case "importoAggiudicazione":
			case "importoSommeLiquidate":
				$istanza[$figli->item($i)->nodeName] = utf8_decode(trim($figli->item($i)->nodeValue));
				break;
			case "partecipanti":
				$partecipanti = $figli->item($i)->childNodes;
				$arrayPartecipanti = array();
				for($z = 0; $z < $partecipanti->length; $z++) {
					$partecipante = $partecipanti->item($z);
					$par = array();
					$figliPar = $partecipante->childNodes;
					for($j = 0; $j < $figliPar->length; $j++) {
						switch($figliPar->item($j)->nodeName) {
							case "ragioneSociale":
							case "codiceFiscale":
							case "identificativoFiscaleEstero":
								$par[$figliPar->item($j)->nodeName] = trim($figliPar->item($j)->nodeValue);
							break;
						}
					}
					if(trim($par['ragioneSociale']) != '' and (trim($par['codiceFiscale'] != '') or trim($par['identificativoFiscaleEstero']) != '')) {
						$arrayPartecipanti[] = $par['ragioneSociale'].' ['.$par['codiceFiscale'].$par['identificativoFiscaleEstero'].']';
					}
				}
				$istanza['partecipanti'] = implode(', ', $arrayPartecipanti);
				break;
			case "aggiudicatari":
				$aggiudicatari = $figli->item($i)->childNodes;
				$arrayAggiudicatari = array();
				for($z = 0; $z < $aggiudicatari->length; $z++) {
					$aggiudicatario = $aggiudicatari->item($z);
					$par = array();
					$figliPar = $aggiudicatario->childNodes;
					for($j = 0; $j < $figliPar->length; $j++) {
						switch($figliPar->item($j)->nodeName) {
							case "ragioneSociale":
							case "codiceFiscale":
							case "identificativoFiscaleEstero":
								$par[$figliPar->item($j)->nodeName] = trim($figliPar->item($j)->nodeValue);
								break;
						}
					}
					if(trim($par['ragioneSociale']) != '' and (trim($par['codiceFiscale'] != '') or trim($par['identificativoFiscaleEstero']) != '')) {
						$arrayAggiudicatari[] = $par['ragioneSociale'].' ['.$par['codiceFiscale'].$par['identificativoFiscaleEstero'].']';
					}
				}
				$istanza['aggiudicatari'] = implode(', ', $arrayAggiudicatari);
				break;
			case "strutturaProponente":
				$figliSt = $figli->item($i)->childNodes;
				for($j = 0; $j < $figliSt->length; $j++) {
					switch($figliSt->item($j)->nodeName) {
						case "codiceFiscaleProp":
						case "denominazione":
							$istanza[$figliSt->item($j)->nodeName] = utf8_decode(trim($figliSt->item($j)->nodeValue));
							break;
					}
				}
				break;
			case "tempiCompletamento":
				$figliSt = $figli->item($i)->childNodes;
				for($j = 0; $j < $figliSt->length; $j++) {
					switch($figliSt->item($j)->nodeName) {
						case "dataInizio":
						case "dataUltimazione":
							$dataTemp = explode("-", trim($figliSt->item($j)->nodeValue));
							$istanza[$figliSt->item($j)->nodeName] = $dataTemp[2].'/'.$dataTemp[1].'/'.$dataTemp[0];
							break;
					}
				}
				break;
		}
	}
	//lognormale('',$istanza);
	$istanze[] = $istanza;
}


$fileName = "export_data" . rand(1,100) . ".xls";


function filterData(&$str) {
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

// headers for download
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Content-Type: application/vnd.ms-excel");

$flag = false;
foreach($istanze as $row) {
	if(!$flag) {
		// display column names as first row
		echo implode("\t", array_keys($row)) . "\n";
		$flag = true;
	}
	// filter data
	array_walk($row, 'filterData');
	echo implode("\t", array_values($row)) . "\n";
}
exit;
?>