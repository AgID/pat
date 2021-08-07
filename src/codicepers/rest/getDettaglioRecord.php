<?php
//inizializzazione
//lognormale('',$restResponse);
//lognormale('http://'.$_SERVER['HTTP_HOST'].'/'.$restResponse->getParametro(2).'.html');

$nomedb = $restResponse->getParametro(2);
$idDocumento = $restResponse->getParametro(3);

if(strpos($nomedb,'__oggetto') !== false) {
	$t = str_replace("__oggetto", "", $nomedb);
	foreach((array)$oggetti as $ogg) {
		if($t == $ogg['id']) {
			$nomedb = $ogg['nomedb'];
		}
	}
}

$sql = "SELECT id FROM ".$dati_db['prefisso']."oggetti WHERE nomedb = '".$nomedb."'";
if ( !($result = $database->connessioneConReturn($sql)) ) {
	echo $restResponse->returnError('Errore in estrazione tabella');
	$restResponse->exitApp();
}
$obj = $database->sqlArray($result);
$idOggetto = $obj['id'];
if(!$idOggetto) {
	echo $restResponse->returnError('Errore estrazione oggetto: tipo \''.$nomedb.'\' non valido');
	$restResponse->exitApp();
}

$linkLC = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?id_oggetto='.$idOggetto.'&id_doc='.$idDocumento;

ob_start();
$titolo = mostraDatoOggetto($idDocumento, $idOggetto);
$rec = file_get_contents($linkLC);
ob_end_clean();

if($nomedb == 'uffici') {
	//rimuovere mappe di google
	$rec = str_replace('nascondiMappaApp"', 'nascondiMappaApp" style="display:none;"', $rec);
}
$rec = explode('<div class="reviewLC"></div>', $rec);
$rec = $rec[1];

$record = array();
$record['titolo'] = $titolo;
$record['contenuto'] = $rec;
$record['link'] = $linkLC;

echo $restResponse->response($record, true);
?>