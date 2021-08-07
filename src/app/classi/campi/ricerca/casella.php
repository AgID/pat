<?php
$valore = "";
if ($valoreVero != '') {
	$valore = $valoreVero;
}
creaOggettoFormPers('select', $nome, ',1,0', $valore, 'tutti,si,no');
?>
