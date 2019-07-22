<?php
$attoImportato = prendiAttoImportato(forzaNumero($_GET['ida']), $enteAdmin, $idOggetto);
if($attoImportato['id'] > 0) {
	$attoImportato = true;
}
$atto = caricaDocumentoEAlbo('atti', forzaNumero($_GET['ida']));
$allegatiAtto = caricaAllegatiEAlbo($atto['id_atto_allegati']);
$mapping = $mappingCampiAlbo[$idOggetto];
foreach((array)$mapping as $campoTrasparenza => $campoAlbo) {
	$istanzaOggetto[$campoTrasparenza] = $atto[$campoAlbo];
	//lognormale($campoAlbo,$atto[$campoAlbo]);
}
if(file_exists('app/ealbo/mapping/mapping_live_'.$enteAdmin['id'].'.php')) {
	//sovrascrivere qui dentro, in base alle esigenze, $mappingCampiAlbo
	include('app/ealbo/mapping/mapping_live_'.$enteAdmin['id'].'.php');
}
?>