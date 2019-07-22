<?php 
global $dati_db,$database,$datiUser,$server_url,$configurazione;

if ($idSezione != 0 AND $idEnte != 0 AND $priorita >= 0) {
    require_once('classi/admin_oggetti.php');
    $oggOgg = new oggettiAdmin(33);
    
    $arrayValori['stato_workflow'] = 'finale';
    $arrayValori['stato'] = 1;
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
    $arrayValori['ordine'] = $priorita;
    $arrayValori['titolo'] = $_POST['nomeNuovoContenuto'];
    /*
     if($arrayValori['tipologia'] == '') {
     $arrayValori['tipologia'] = 'contenuto';
     }
     */
    
    if ($oggOgg->aggiungiOggetto(0, $arrayValori)) {
        $id = $oggOgg->lastInsertId;
    }
    return $id;
}
return FALSE;
?>