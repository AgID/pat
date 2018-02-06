<?php

// ho già importato il codice javascript necessario all'interfaccia

// inserisco il campo nascosto
echo "<div style=\"float:left;display:inline;width:17px;height:17px;border:1px solid #535353;margin:0px 6px 0px 4px;\"><div id=\"" . $nome . "_prev\" style=\"border:1px solid #FFFFFF;background:" . $valoreVero . ";\">&nbsp;</div></div>";
$styleCampo = "none";
if ($valore) {
	$styleCampo = "inline";
}
echo "<input style=\"position:relative;width:60px;display:" . $styleCampo . "\" onLoad=\"anteprimaColore('" . $nome . "_prev');\" onBlur=\"anteprimaColore('" . $nome . "_prev');\" onChange=\"anteprimaColore('" . $nome . "_prev');\" type=\"text\" name=\"" . $nome . "\" id=\"" . $nome . "\" value=\"" . $valoreVero . "\" size=\"15\">";

// inserisco pulsante di apertura finestra link
//  <input type=\"button\" value=\"Scegli un colore\" onclick=\"showColorPicker(this,document.getElementById('".$nome."'))\" />
echo "  
			        <a id=\"scelCol" . $nome . "\" class=\"bottoneClassico\" title=\"Scegli un colore\" href=\"javascript:showColorPicker(this,document.getElementById('" . $nome . "'));\">
				<img src=\"grafica/admin_skin/classic/info_selcolore.gif\" alt=\"Scegli un colore\" />Scegli un colore</a>
			";
?>
