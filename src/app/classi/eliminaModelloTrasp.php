<?php 
if ($idModello != 0) {
    require_once('classi/admin_oggetti.php');
    $oggOgg = new oggettiAdmin(33);
    
    if ($oggOgg->cancellaOggetti($idModello)) {
        return true;
    }
}
return FALSE;
?>