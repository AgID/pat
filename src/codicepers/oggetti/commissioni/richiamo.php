<?
if($istanzaOggetto['stato_workflow'] == 'finale') {
	$elWf = getIstanzaWorkflow($istanzaOggetto['id'], $documento->idOggetto);
	if(!$elWf['id']) {
		setIstanzaWorkflow($istanzaOggetto['id'], $documento->idOggetto);
	}
}
if($istanzaOggetto['stato_workflow'] != 'finale') {
	$elWf = getIstanzaWorkflow($istanzaOggetto['id'], $documento->idOggetto);
	if($elWf['id']) {
		?>
		<div class="elementoInRevisione">L'elemento &egrave; in fase di aggiornamento.</div>
		<?
	}
}

$richiamo = true;

if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/commissioni/lettura.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/commissioni/lettura.php');
} else {
	$tipo = $istanzaOggetto['tipologia'];
	switch($tipo) {
		case 'commissione':
		case 'gruppo consiliare':
			include('codicepers/oggetti/commissioni/lettura_default.php');
			break;
		case 'udp':
			include('codicepers/oggetti/commissioni/organismi_commissioni/udp.php');
			break;
		case 'ci':
			include('codicepers/oggetti/commissioni/organismi_commissioni/ci.php');
			break;
		case 'gect':
			include('codicepers/oggetti/commissioni/organismi_commissioni/gect.php');
			break;
	}
	
}
	
?>