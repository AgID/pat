<?php
/*
 * Created on 6/nov/2017
 */
//inizializzazione
$obiettivo = forzaNumero($restResponse->getParametro(2));
$limite = forzaNumero($restResponse->getParametro(3));
$inizio = forzaNumero($restResponse->getParametro(4));
$ordineRis = urldecode($restResponse->getParametro(5));
$esattamente = forzaNumero($restResponse->getParametro(6));
$stringa = htmlentities(urldecode($restResponse->getParametro(7)), ENT_COMPAT | ENT_HTML401, 'UTF-8', 'ISO-8859-1');
$limite = forzaNumero((isset($limite) and trim($limite) != '') ? $limite : (isset($configurazione['limite_records_rest']) ? $configurazione['limite_records_rest'] : 20));
$inizio = forzaNumero((isset($inizio) and trim($inizio) != '') ? $inizio : '0');
$sezioneRicerca = '';
$oggettiRicerca = '';

//controllo valori ordinamento
if(!is_numeric($limite)) {
	echo $restResponse->returnError('Errore limite ordinamento oggetto: \''.$limite.'\' non valido');
	$restResponse->exitApp();
}
if(!is_numeric($inizio)) {
	echo $restResponse->returnError('Errore valore inizio ordinamento oggetto: \''.$inizio.'\' non valido');
	$restResponse->exitApp();
}

include('moduli/ricercaGenerica.php');

echo $restResponse->restResponseHeader($records, $header);
?>