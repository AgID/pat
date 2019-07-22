<?

// controllo user
if ($datiUser['sessione_loggato']) {
	if ($datiUser['permessi'] == -1 or $datiUser['permessi'] == 0) {
		die('Non hai i permessi per accedere al pannello di amministrazione');
	}
} else {
	die('Gli utenti non autenticati, non possono accedere al pannello di amministrazione');
}

// analizzo la sezione di amministrazione in cui mi trovo
switch ($menu) {

	//////////////////////////////////FUNZIONI//////////////////////////////////////
	case "enti" :
	case "utenti" :
	case "ruoli" :
	case "configurazione" :
	case "report" :
		switch($menuSecondario) {
			case 'log':
			case 'log_utenti':
				include ("./app/moduli/menu_amm/log.php");
			break;
			case 'workflow':
				include ("./app/moduli/menu_amm/" . $menuSecondario . ".php");
			break;
			default:
				include ("./app/moduli/menu_amm/" . $menu . ".php");
			break;
		}
	break;
	
		
	case "ealbo":
	case "moduli_personalizzati":
	case "contenuti" :
	switch($menuSecondario) {
			case 'workflow':
				include ("./app/moduli/menu_amm/stato_workflow.php");
			break;
			case 'pagine':
			case 'editpagina':
				if($id and !$_POST['rispostaForm'] and !$_POST['id_cancello_contenuto']) {
					include ("./app/moduli/menu_amm/oggetti.php");
				} else {
					$menuSecondario = 'pagine';
					include ("./app/moduli/menu_amm/pagine.php");
				}
			break;
			default:
				include ("./app/moduli/menu_amm/" . $menu . ".php");
			break;
		}
	break;
	
	//////////////////////////////////OGGETTI//////////////////////////////////////
	case "elezioni" :
	case "anticorruzione" :
	case "attiprog" :
	case "pubblicazioni" :
	case "organizzazione" :
	case "documentazione" :
	case "accessocivico" :
	
		include ("./app/moduli/menu_amm/oggetti.php");
		
	break;

	//////////////////////////////////DESKTOP//////////////////////////////////////
	case "desktop" :
		include ('app/admin_template/desktop.tmp');
	break;

}
?>
