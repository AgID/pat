<?php
if(count($listaDocumenti)==1) {
	include('codicepers/oggetti/personale/lettura.php');
} else {
	include('codicepers/oggetti/personale/richiamo.php');
}
?>