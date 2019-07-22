<?php 
global $dati_db,$database;

if ($idSezione != 0 AND $idEnte != 0 AND $tipologia != '') {
    $sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente=".$idEnte." AND id_sezione_etrasp=".$idSezione." AND tipologia = '".$tipologia."' ORDER BY ordine DESC LIMIT 0,1";
    if ( !($result = $database->connessioneConReturn($sql)) ) {
        die('Errore durante il recupero del modello trasparenza');
    }
    $riga = $database->sqlArray($result);
    if (is_array($riga)) {
        return $riga['ordine'];
    } else {
        return 0;
    }
}
return 0;
?>