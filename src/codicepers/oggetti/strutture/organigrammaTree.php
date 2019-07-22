<?php
if (count($docRiferiti)) {
    $pubblicaTree = false;
    foreach((array)$docRiferiti as $doc) {
        if($doc['articolazione']) {
            $pubblicaTree = true;
        }
    }
    if($pubblicaTree) {
        echo '<div class="orgUffici">';
        foreach((array)$docRiferiti as $doc) {
            if($doc['articolazione']) {
                $anc = $base_url.'archivio13_strutture_'.$doc['id_sezione'].'_'.$doc['id'].'.html';
                echo '<div class="titoloOrganigrammaTree"><a href="'.$anc.'">'.mostraDatoOggetto($doc['id'], 13).'</a></div>';
                
                $docRiferiti = $docRif->caricaDocumentiCampo('struttura', $doc['id']);
                include('codicepers/oggetti/strutture/organigrammaTree.php');
            }
            
        }
        echo '</div>';
    }
}
?>