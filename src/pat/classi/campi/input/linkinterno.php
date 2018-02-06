<?php

// inserisco il codice javascript necessario
echo "<script type=\"text/javascript\">
			function SetUrl" . $nome . "(url) {
				linkCampo = document.getElementById('" . $nome . "');
				linkCampo.value = url; 
			}
			function linkInterno" . $nome . "() {
				navigazione = window.open('link_interno.php?campo=" . $nome . "','immagini','height=300,width=400,toolbar=no,scrollbars=no,status=no');
                                
                                if(window.focus){
                                    navigazione.focus();
                                }
			}</script>";
// inserisco il campo nascosto
if ($valoreVero != '') {
	$valore = $valoreVero;
}
echo "<input " . $classeStr . " " . $evento . " type=\"text\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valore . "\" style=\"width:50%;margin:0px 10px 0px 0px;\" />";
// inserisco pulsante di apertura link
echo "
			        <a class=\"bottoneClassico\" title=\"Scegli un link interno\" href=\"javascript:linkInterno" . $nome . "();\">
				<img src=\"grafica/admin_skin/classic/link.gif\" alt=\"Scegli un link interno\" />Scegli un link interno</a>
			";
?>
