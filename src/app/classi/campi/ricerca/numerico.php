<?php

// creo il select della condizione
creaOggettoFormPers('select', $nome . "_condizione", 'uguale,maggiore,minore', $_POST[$nome . "_condizione"], 'uguale a,maggiore di,minore di');
if ($valoreVero != '') {
	$valore = $valoreVero;
} else {
	$valore = 'qualunque';
}
echo "<input " . $evento . $classeStr . " type=\"text\" id=\"" . $nome . "\" name=\"" . $nome . "\" value=\"" . $valore . "\" />";
?>
