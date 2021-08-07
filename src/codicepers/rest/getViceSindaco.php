<?php
//inizializzazione
$campoOrdine = $restResponse->getParametro(2);
$tipoOrdine = $restResponse->getParametro(3);
$limite = $restResponse->getParametro(4);
$inizio = $restResponse->getParametro(5);
$campoOrdine = (isset($campoOrdine) and trim($campoOrdine) != '') ? $campoOrdine : 'nessuno';
$tipoOrdine = (isset($tipoOrdine) and trim($tipoOrdine) != '') ? $tipoOrdine : 'nessuno';
$limite = (isset($limite) and trim($limite) != '') ? $limite : '0';
$inizio = (isset($inizio) and trim($inizio) != '') ? $inizio : '0';

$idOggetto = 3;
$documento = new documento($idOggetto,"no");

$criterio = loadingOggettoCriterio(94);

ob_start();
$tempRecords = $documento->caricaDocumenti($criterio, $campoOrdine, $tipoOrdine, $limite, $inizio);
ob_end_clean();

$records = array();
$index = 0;
foreach((array)$tempRecords as $rec) {
	for($i=0; $i < count($documento->struttura); $i++) {
		$records[$index][$documento->struttura[$i]['nomecampo']] = $rec[$documento->struttura[$i]['nomecampo']];
	}
	$records[$index]['id'] = $rec['id'];
	$index++;
}

echo $restResponse->response($records);
?>