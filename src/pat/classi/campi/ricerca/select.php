<?
echo "<select ".$evento.$classeStr." id=\"".$nome."\" name=\"".$nome."\">";
if ($valoreVero == 'qualunque') {
    echo "<option value=\"qualunque\" title=\"qualunque\" selected=\"selected\" >qualunque</option>";
} else {
    echo "<option value=\"qualunque\" title=\"qualunque\" >qualunque</option>";
}
$valori = explode(",", $valore);
$nomiCampi = explode(",", $nomi);
$num = 0;
foreach ($valori as $val) {
	$stringa = '';
	if ($valoreVero == stripslashes($val)) {
		$stringa = ' selected="selected" ';
	}
	if ($val == '') {
		echo "<option value=\"\"".$stringa." title=\"(nessun valore)\">(nessun valore)</option>";
	} else {
		if ($nomi == '') {
            echo "<option value=\"".stripslashes($val)."\"".$stringa." title=\"".stripslashes($val)."\" >".stripslashes($val)."</option>";
        } else {
            echo "<option value=\"".stripslashes($val)."\"".$stringa." title=\"".$nomiCampi[$num]."\" >".$nomiCampi[$num]."</option>";
        }  
	}
	$num++;
}
echo "</select>";
?>