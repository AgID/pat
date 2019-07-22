<?php
/*
 * Created on 09/ott/2013
 *
 */

require_once('classi/admin_oggetti.php');
$oggOgg = new oggettiAdmin($ido);

$call = $entePubblicato['url_sitoistituzionale'].'/ajax.php?azione=getRegolaOggetto&id_regola='.$idro;
//$call = 'http://tecnicois.it/cms/ajax.php?azione=getRegolaOggetto&id_regola='.$idro;
$regola = @file_get_contents($call);
if($regola != '') {
	$regola = unserialize(html_entity_decode($regola));
}

//carico tutti i criteri globali e specifici per l'oggetto che mi viene passato
require('classi/admin_criteri.php');
$criteriClass = new criteri();
$criteri = array();
$idCriteri = array();
$labelCriteri = array();
foreach((array)$criteriClass->caricaCriteriCompatibili('generale') as $criterio) {
	$criteri[] = $criterio;
	$idCriteri[] = $criterio['id'];
	$labelCriteri[] = $criterio['nome'];
}
foreach((array)$criteriClass->caricaCriteriOggetto($ido) as $criterio) {
	$criteri[] = $criterio;
	$idCriteri[] = $criterio['id'];
	$labelCriteri[] = $criterio['nome'];
}

$idCriterio = $regola['id_criterio'];
$idCriteri = implode(',',$idCriteri);
$labelCriteri = implode(',',$labelCriteri);
//lognormale('',$regola);
include ('./app/admin_template/richiamiOggettoCMS/form/selezionaCriterio.tmp');

?>