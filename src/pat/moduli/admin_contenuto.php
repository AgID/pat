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
	case "contenuti" :
	case "moduli_personalizzati":
	case "workflow" :
		switch($menuSecondario) {
			case 'workflow':
				include ("./pat/moduli/menu_amm/" . $menuSecondario . ".php");
			break;
			default:
				include ("./pat/moduli/menu_amm/" . $menu . ".php");
			break;
		}
		
	break;
		
	//////////////////////////////////OGGETTI//////////////////////////////////////
	case "pubblicazioni" :
	case "organizzazione" :
	case "documentazione" :
	
		include ("./pat/moduli/menu_amm/oggetti.php");
		
	break;

	//////////////////////////////////DESKTOP//////////////////////////////////////
	case "desktop" :
		include ('pat/admin_template/desktop.tmp');
	break;

}
?>
