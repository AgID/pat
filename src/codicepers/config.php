<?php

//AMBIENTE PAT
$configurazione['PAT'] = false;

if($configurazione['piattaforma_pat']) {
	$configurazione['PAT'] = true;
}

$base_url = $server_url;
if(file_exists('codicepers/ente/'.$configurazione['piattaforma_at'].'/baseurl/'.$configurazione['piattaforma_at'].'.tmp')) {
	include('codicepers/ente/'.$configurazione['piattaforma_at'].'/baseurl/'.$configurazione['piattaforma_at'].'.tmp');
}
if(file_exists('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/baseurl/'.$entePubblicato['nome_breve_ente'].'.tmp')) {
	include('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/baseurl/'.$entePubblicato['nome_breve_ente'].'.tmp');
}


//LOGHI ETRASPARENZA & CO.
$configurazione['logo_etrasp'] = 'logo_trasp.png';
$configurazione['logo_etrasp_scuroalpha'] = 'logo_trasp_scuroalpha.png';
$configurazione['logo_etrasp_scuro'] = 'logo_trasp_scuro.png';
$configurazione['logo_etrasp_dialog'] = 'logo_trasp_dialog.png';
$configurazione['logoLoad'] = 'logoLoad';
$configurazione['denominazione_trasparenza'] = 'PAT - Portale Amministrazione Trasparente';
$configurazione['trasparenza_visualizza_credits'] = true;
$configurazione['dominio_file_anac'] = $dominio_query_sessioni; 
$configurazione['dominio_file_anac_completo'] = false;
if($configurazione['PAT']) {
	$configurazione['logo_etrasp'] = 'pat_logo_trasp.png';
	$configurazione['logo_etrasp_scuroalpha'] = 'pat_logo_trasp_scuroalpha.png';
	$configurazione['logo_etrasp_scuro'] = 'pat_logo_trasp_scuro.png';
	$configurazione['logo_etrasp_dialog'] = 'pat_logo_trasp_dialog.png';
	$configurazione['logoLoad'] = 'logoLoadPAT';
	$configurazione['denominazione_trasparenza'] = 'PAT';
	$configurazione['trasparenza_visualizza_credits'] = false;
	$configurazione['dominio_file_anac'] = 'portaleamministrazionetrasparente.it';
	$configurazione['mail_sito'] = 'admin@portaleamministrazionetrasparente.it';
}

if(moduloAttivo('solo_accessocivico')) {
	$configurazione['logo_etrasp'] = 'accessocivico_logo_trasp.png';
	$configurazione['logo_etrasp_scuroalpha'] = 'accessocivico_logo_trasp_scuroalpha.png';
	$configurazione['logo_etrasp_scuro'] = 'accessocivico_logo_trasp_scuro.png';
	$configurazione['logo_etrasp_dialog'] = 'accessocivico_logo_trasp_dialog.png';
	$configurazione['logoLoad'] = 'logoLoadAccessoCivico';
	$configurazione['denominazione_trasparenza'] = 'Accesso Civico';
	$configurazione['trasparenza_visualizza_credits'] = true;
}

if($entePubblicato['condizione_bandi_archiviati']) {
	//archiviazione automatica dei bandi dopo 5 anni dalla data di pubblicazione
	$configurazione['condizione_bandi_archiviati'] = " AND DATE_FORMAT(FROM_UNIXTIME(data_attivazione), '%Y-%m-%d') >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 5 YEAR), '%Y-%m-%d') ";
}
$configurazione['condizione_aggiuntiva_concorsi'] = '';

//altre impostazioni di configuazione
/*
$configurazione['condizioneDefaultRuolo'] = array(
		array('nome'=>'Incaricato politico','politico'=>true),
		array('nome'=>'Commissario','politico'=>true),
		array('nome'=>'Sub Commissario','politico'=>true),
		array('nome'=>'Dirigente','politico'=>false),
		array('nome'=>'Segretario generale','politico'=>false),
		array('nome'=>'P.O.','politico'=>false)
);
*/

$configurazione['tipologie_esito'] = 'appalto aggiudicato per gara sopra soglia comunitaria (pubblicazione su G.U.U.E. + G.U.R.I.),appalto aggiudicato per gara nazionale (pubblicazione su G.U.R.I.)';
$configurazione['tag_h_titolo'] = 'h3';
$configurazione['versione_ckeditor'] = '492';
$configurazione['tipo_filemanager'] = 'elFinder';
$configurazione['versione_filemanager'] = 'elFinder-2.1.49';
$configurazione['escludi_istanze_archiviate'] = true;
$configurazione['titoloColonnaUfficio'] = 'Referente per';
$configurazione['nome_banner_s25'] = 'Settori e servizi';
$configurazione['nome_banner_s65'] = 'Contattaci';

$configurazione['denominazione_albo'] = 'Albo Pretorio On Line';

if(file_exists('codicepers/ente/'.$configurazione['piattaforma_at'].'/config/'.$configurazione['piattaforma_at'].'.php')) {
	include('codicepers/ente/'.$configurazione['piattaforma_at'].'/config/'.$configurazione['piattaforma_at'].'.php');
}
if(file_exists('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/config/'.$entePubblicato['nome_breve_ente'].'.php')) {
	include('codicepers/ente/'.$entePubblicato['nome_breve_ente'].'/config/'.$entePubblicato['nome_breve_ente'].'.php');
}

$d = (date('j')%4)+1;
if($configurazione['google_maps_api_key_'.$d] != '') {
	$configurazione['google_maps_api_key'] = $configurazione['google_maps_api_key_'.$d];
}
if($entePubblicato['google_maps_api_key'] != '') {
	$configurazione['google_maps_api_key'] = $entePubblicato['google_maps_api_key'];
}

//Configurazione accesso civico
$configurazione['sezioni_accessocivico'] = array(770,887,888,889);
$configurazione['sezioni_escludi_no_accessocivico'] = array(887,888,889);
$configurazione['id_acl_accessocivico'] = 469;
$tipoEnte['traduzioni_organi'][] = array('id' => 702, 'nome' => $tipoEnte['org_sindaco'], 'tipo' => 'org_sindaco', '__nome' => 'sindaco');
$tipoEnte['traduzioni_organi'][] = array('id' => 703, 'nome' => $tipoEnte['org_giunta'], 'tipo' => 'org_giunta', '__nome' => 'giunta comunale');
$tipoEnte['traduzioni_organi'][] = array('id' => 704, 'nome' => $tipoEnte['org_presidente'], 'tipo' => 'org_presidente', '__nome' => 'presidente consiglio comunale');
$tipoEnte['traduzioni_organi'][] = array('id' => 705, 'nome' => $tipoEnte['org_consiglio'], 'tipo' => 'org_consiglio', '__nome' => 'consiglio comunale');
$tipoEnte['traduzioni_organi'][] = array('id' => 706, 'nome' => $tipoEnte['org_direzione'], 'tipo' => 'org_direzione', '__nome' => 'direzione generale');
$tipoEnte['traduzioni_organi'][] = array('id' => 707, 'nome' => $tipoEnte['org_segretario'], 'tipo' => 'org_segretario', '__nome' => 'segretario generale', 'nascondi_bo' => true);
$tipoEnte['traduzioni_organi'][] = array('id' => 708, 'nome' => $tipoEnte['org_commissioni'], 'tipo' => 'org_commissioni', '__nome' => 'commissioni');
$tipoEnte['traduzioni_organi'][] = array('id' => 792, 'nome' => $tipoEnte['org_vicesindaco'], 'tipo' => 'org_vicesindaco', '__nome' => 'vicesindaco');
$tipoEnte['traduzioni_organi'][] = array('id' => 793, 'nome' => $tipoEnte['org_gruppi_consiliari'], 'tipo' => 'org_gruppi_consiliari', '__nome' => 'gruppi consiliari');
$tipoEnte['traduzioni_organi'][] = array('id' => 796, 'nome' => $tipoEnte['org_commissario'], 'tipo' => 'org_commissario', '__nome' => 'commissario');
$tipoEnte['traduzioni_organi'][] = array('id' => 809, 'nome' => $tipoEnte['org_ass_sindaci'], 'tipo' => 'org_ass_sindaci', '__nome' => 'assemblea dei sindaci');
$tipoEnte['traduzioni_organi'][] = array('id' => 810, 'nome' => $tipoEnte['org_sub_commissario'], 'tipo' => 'org_sub_commissario', '__nome' => 'sub commissario');
$tipoEnte['traduzioni_organi'][] = array('id' => 827, 'nome' => $tipoEnte['org_comitato_esecutivo'], 'tipo' => 'org_comitato_esecutivo', '__nome' => 'comitato esecutivo');
$tipoEnte['traduzioni_organi'][] = array('id' => 828, 'nome' => $tipoEnte['org_consiglio_sportivo_nazionale'], 'tipo' => 'org_consiglio_sportivo_nazionale', '__nome' => 'consiglio sportivo nazionale');
$tipoEnte['traduzioni_organi'][] = array('id' => 829, 'nome' => $tipoEnte['org_giunta_sportiva'], 'tipo' => 'org_giunta_sportiva', '__nome' => 'giunta sportiva');
$tipoEnte['traduzioni_organi'][] = array('id' => 910, 'nome' => $tipoEnte['org_udp'], 'tipo' => 'org_udp', '__nome' => 'ufficio di presidenza');
$tipoEnte['traduzioni_organi'][] = array('id' => 911, 'nome' => $tipoEnte['org_ci'], 'tipo' => 'org_ci', '__nome' => 'commissione interregionale');
$tipoEnte['traduzioni_organi'][] = array('id' => 912, 'nome' => $tipoEnte['org_gect'], 'tipo' => 'org_gect', '__nome' => 'gect');

//elezioni-trasparenti
if(!moduloAttivo('elezioni-trasparenti')) {
    if($tipoEnte['sezioni_esclusione'] != '') {
        $tipoEnte['sezioni_esclusione'] .= ',';
    }
    $tipoEnte['sezioni_esclusione'] .= 925;
}

//temp - nascondere sezione provvedimenti -> scelta contraenteif($tipoEnte['sezioni_esclusione'] != '') {
if($tipoEnte['sezioni_esclusione'] != '') {
    $tipoEnte['sezioni_esclusione'] .= ',';
}
$tipoEnte['sezioni_esclusione'] .= 926;
?>