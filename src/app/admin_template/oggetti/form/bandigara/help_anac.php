<?
/*
 * 
 */
$anac = false;
switch($nome) {
	case 'oggetto':
	case 'cig':
	case 'valore_importo_aggiudicazione':
	case 'elenco_partecipanti':
	case 'elenco_aggiudicatari':
	case 'scelta_contraente':
	case 'denominazione_aggiudicatrice':
	case 'dati_aggiudicatrice':
	case 'data_inizio_lavori':
	case 'data_lavori_fine':
	case 'importo_liquidato':
		$anac = true;		
	break;
}
if($anac and $_GET['tipo'] != 'avviso'and $_GET['tipo'] != 'determina_32') {
	$testoObb .= '<span class="obbliCampo intTooltip"><a data-placement="right" data-rel="tooltip" data-original-title="Campo pubblicato e comunicato ad ANAC ai fini dell\'art.1 comma 32 Legge n. 190/2012"><span class="icon-info-sign"></span></a></span>';
}

$anac = false;
switch($nome) {
	case 'anac_anno':
		$anac = true;
		break;
}
if($anac and $_GET['tipo'] != 'avviso'and $_GET['tipo'] != 'determina_32') {
	$testoObb .= '<span class="obbliCampo intTooltip"><a data-placement="right" data-rel="tooltip" data-original-title="Selezionare l\'anno di riferimento di questa procedura. Questo valore verr&agrave; preso in considerazione per includere o meno l\'informazione nella creazione del file XML per la comunicazione all\'ANAC"><span class="icon-question-sign"></span></a></span>';
}
?>