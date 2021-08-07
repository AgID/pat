<?php
//inizializzazione
$idOggetto = 13;
$documento = new documento($idOggetto,"no");

$criterio = loadingOggettoCriterio(42);	//uffici di primo livello

ob_start();
$tempRecords = $documento->caricaDocumentiCriterio($criterio);
ob_end_clean();

$records = array();
$index = 0;
foreach((array)$tempRecords as $rec) {
	for($i=0; $i < count($documento->struttura); $i++) {
		$records[$index][$documento->struttura[$i]['nomecampo']] = $rec[$documento->struttura[$i]['nomecampo']];
	}
	$records[$index]['id'] = $rec['id'];
	
	$configurazione['struttura_padre'] = $rec['id'];
	$criterio = loadingOggettoCriterio(152);	//uffici della struttura in {configurazione[struttura_padre]}

	ob_start();
	$tempSubRecords = $documento->caricaDocumentiCriterio($criterio);
	ob_end_clean();
	

	$subRecords = array();
	$subIndex = 0;
	foreach((array)$tempSubRecords as $sub) {
		for($j=0; $j < count($documento->struttura); $j++) {
			$subRecords[$subIndex][$documento->struttura[$j]['nomecampo']] = $sub[$documento->struttura[$j]['nomecampo']];
		}
		$subRecords[$subIndex]['id'] = $sub['id'];
		$subRecords[$subIndex]['__nome'] = 'uffici';
		$subIndex++;
	}
	
	$records[$index]['uffici'] = $subRecords;
	$records[$index]['__nome'] = 'uffici';
	$index++;
}

echo $restResponse->response($records);
?>