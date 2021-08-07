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

$criterio = loadingOggettoCriterio(75);

ob_start();
$tempRecords = $documento->caricaDocumenti($criterio, $campoOrdine, $tipoOrdine, 1, $inizio);
ob_end_clean();

$records = array();
$index = 0;

$linkLC = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?id_oggetto='.$idOggetto.'&id_doc='.$tempRecords[0]['id'];

ob_start();
$titolo = mostraDatoOggetto($idDocumento, $idOggetto);
$rec = file_get_contents($linkLC);
ob_end_clean();

$rec = explode('<div class="reviewLC"></div>', $rec);
$rec = $rec[1];

$record = array();
$record['titolo'] = $titolo;
$record['contenuto'] = $rec;

echo $restResponse->response($record, true);
?>