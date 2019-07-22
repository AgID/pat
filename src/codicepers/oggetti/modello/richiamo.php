<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/modello/richiamo.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/modello/richiamo.php');
} else {
	/* template standard */
	
	$c = 'modulistica';
	$idO = 5;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'normativa';
	$idO = 27;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'referenti';
	$idO = 3;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'regolamenti';
	$idO = 19;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'procedimenti';
	$idO = 16;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'provvedimenti';
	$idO = 28;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'strutture';
	$idO = 13;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
	$c = 'incarichi';
	$idO = 4;
	include('codicepers/oggetti/modello/modello_oggetto.php');
	
}
?>