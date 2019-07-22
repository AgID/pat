<?php 
global $dati_db,$database,$datiUser;

if ($id != 0) {
    
    require_once('classi/admin_oggetti.php');
    $oggOgg = new oggettiAdmin(33);
    
    $arrayValori['stato'] = 1;
    $arrayValori['stato_workflow'] = 'finale';
    $arrayValori['id_proprietario'] = $datiUser['id'];
    $arrayValori['permessi_lettura'] = "N/A";
    $arrayValori['tipo_proprietari_lettura'] = "tutti";
    $arrayValori['id_proprietari_lettura'] = "-1";
    $arrayValori['permessi_admin'] = "N/A";
    $arrayValori['tipo_proprietari_admin'] = "tutti";
    $arrayValori['id_proprietari_admin'] = "-1";
    $arrayValori['id_lingua'] = "0";
    
    $arrayValori['id_ente'] = $idEnte;
    $arrayValori['id_sezione_etrasp'] = $idSezione;
    /*
     if($arrayValori['tipologia'] == '') {
     $arrayValori['tipologia'] = 'contenuto';
     }
     */
    
    if (!$campi) {
        $campi = array('html_generico');
        $valori = array($valori);
    }
    for($i=0;$i<count($campi);$i++) {
        $arrayValori[$campi[$i]] = $valori[$i];
    }
    
    if ($oggOgg->modificaOggetto($id, 0, $arrayValori)) {
        return true;
    }
    
    
}

return FALSE;
?>