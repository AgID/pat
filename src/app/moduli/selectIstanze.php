<?
// controllo user
if ($datiUser['sessione_loggato']) {
	if ($datiUser['permessi'] == -1 or $datiUser['permessi'] == 0) {
		die('Non hai i permessi per accedere al pannello di amministrazione');
	}
} else if($check != $c) {
	die('Gli utenti non autenticati, non possono accedere al pannello di amministrazione');
}

// analizzo la sezione di amministrazione in cui mi trovo
include ("./app/moduli/menu_amm/oggetti.php");
?>