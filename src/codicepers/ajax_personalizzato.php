<?
/*
 * Creato il 22/feb/2011 da Nico
 */
 
// SELEZIONO L'AZIONE DA COMPIERE
switch ($_GET['azione']) {
	
	case 'getFormTrasp':
		$label = $_GET['label'];
		$tipoCampo = $_GET['tipo'];
		$nome = $_GET['nome'];
		$valoreVero = $_GET['valoreVero'];
		
		ob_start();
		creaFormTrasp($label,$tipoCampo, $nome, '', $valoreVero, '','input-xxlarge'); 
		$content = ob_get_clean();
		ob_end_flush();
		echo (($content));
		return;
	break;
	
	case 'getFormTraspRichiamo':
		$label = $_GET['label'];
		$tipoCampo = $_GET['tipo'];
		$nome = $_GET['nome'];
		$valoreVero = $_GET['valoreVero'];
		
		ob_start();
		creaFormTrasp($label,$tipoCampo, $nome, '', $valoreVero, '','input-xxlarge');
		$content = ob_get_clean();
		ob_end_flush();
		echo (($content));
		return;
	break;
	
	case 'cercaAutoAdmin':
		include ('./app/config_pat.php');
		// controllo utente
		include('./inc/controllo_user.php');
		include('./app/controllo_user.php');
		
		$q = htmlentities((strtolower($_GET["term"])));
		$response = array();
		if (!$q) return;
		$limitaCaratteriOutput = 60;
		$tipoRicercaAuto = 'admin';	//non modificare 
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su tutti gli oggetti
		////////////////////////////////////////////////////////////////////////////////////////
		
		$numero = 10;
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		//file_put_contents('temp/ric.html', 'RICERCA ADMIN<br />');
		foreach((array)$oggettiTrasparenza as $idOggetto => $obj) {
			
			//file_put_contents('temp/ric.html', $nomeOggetto.'<br />', FILE_APPEND);
			
			$o = new documento($idOggetto,'si');
			$idSezioneElenco = 0;
			$nomeOggetto = $obj['nomeMenu'];
			$campo = $o->campo_default;
			$menu = $obj['menu'];
			$menuSecondario = $obj['menuSec'];
			
			if($o->campiRicerca[0] == '') {
				$o->campiRicerca = $o->campiAdmin;
			}
			
			include('codicepers/ricercaOggetti.php');
			/*
			foreach((array)$response as $res) {
				file_put_contents('temp/ric.html', $res['label'].'<br />', FILE_APPEND);
			}
			*/
		}
		
		if(count($response) == 0) {
			$response[] = array(
				'label' => 'Nessun risultato ',
				'objName' => 1
			);
		}
		$database->sqlChiudi();
		$encoded = json_encode($response);
		header('Content-type: application/json');
		exit($encoded);
		
	
	break;
	
	case 'cercaAuto':
		
		//numero di risultati
		$numero = 15;
		include('codicepers/ricercaAjax.php');
		
		
	break;
	
	case 'agendaAppuntamenti':
		include('codicepers/agendaAppuntamenti.php');
	break;
	case 'assegnaAppuntamento':
		include('codicepers/assegnaAppuntamento.php');
	break;
	case 'eliminaAppuntamento':
		include('codicepers/eliminaAppuntamento.php');
	break;
}
?>