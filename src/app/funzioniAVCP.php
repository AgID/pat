<?
function validaCIG($dato) {

	$dato = trim($dato);
	if($dato == '') {
		return true;
	}
	
	if (preg_match('/^([0-9A-Za-z]{10})?$/', $dato)) {
		return true;
	}
	
	return false;
}

function validaData($dato) {
	$dato = trim($dato);
	if($dato == '') {
		return true;
	}
	if (preg_match('/^([0-9]{4}[-]{1}[0-9]{2}[-]{1}[0-9]{2})?$/', $dato)) {
		return true;
	}
	return false;
}

function validaCfPi($dato) {

	$dato = trim($dato);
	if($dato == '') {
		return true;
	}
	
	if (preg_match('/^[A-Za-z]{6}[0-9]{2}[A-Za-z]{1}[0-9]{2}[A-Za-z]{1}[0-9A-Za-z]{3}[A-Za-z]{1}$/', $dato)) {
		return true;
	}

	if (preg_match('/^[A-Za-z]{6}[0-9LMNPQRSTUV]{2}[A-Za-z]{1}[0-9LMNPQRSTUV]{2}[A-Za-z]{1}[0-9LMNPQRSTUV]{3}[A-Za-z]{1}$/', $dato)) {
		return true;
	}

	if (preg_match('/^[0-9]{11,11}$/', $dato)) {
		return true;
	}
	
	return false;
}

function validaImporto($dato) {
	
	$dato = trim($dato);
	if($dato == '') {
		return true;
	}
	
	if (preg_match('/^(-?([1-9][0-9]*|[0-9])([.]{1}[0-9]{1,2})?)?$/', $dato)) {
		return true;
	}
	
	return false;
}

function validaSceltaContraente($dato) {
	
	$dato = trim($dato);
	switch($dato) {
		case "01-PROCEDURA APERTA":
		case "02-PROCEDURA RISTRETTA":
		case "03-PROCEDURA NEGOZIATA PREVIA PUBBLICAZIONE DEL BANDO":
		case "04-PROCEDURA NEGOZIATA SENZA PREVIA PUBBLICAZIONE DEL BANDO":
		case "05-DIALOGO COMPETITIVO":
		case "06-PROCEDURA NEGOZIATA SENZA PREVIA INDIZIONE DI  GARA ART. 221 D.LGS. 163/2006":
		case "07-SISTEMA DINAMICO DI ACQUISIZIONE":
		case "08-AFFIDAMENTO IN ECONOMIA - COTTIMO FIDUCIARIO":
		case "14-PROCEDURA SELETTIVA EX ART 238 C.7 D.LGS. 163/2006":
		case "17-AFFIDAMENTO DIRETTO EX ART. 5 DELLA LEGGE N.381/91":
		case "21-PROCEDURA RISTRETTA DERIVANTE DA AVVISI CON CUI SI INDICE LA GARA":
		case "22-PROCEDURA NEGOZIATA DERIVANTE DA AVVISI CON CUI SI INDICE LA GARA":
		case "23-AFFIDAMENTO IN ECONOMIA - AFFIDAMENTO DIRETTO":
		case "24-AFFIDAMENTO DIRETTO A SOCIETA' IN HOUSE":
		case "25-AFFIDAMENTO DIRETTO A SOCIETA' RAGGRUPPATE/CONSORZIATE O CONTROLLATE NELLE CONCESSIONI DI LL.PP":
		case "26-AFFIDAMENTO DIRETTO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE":
		case "27-CONFRONTO COMPETITIVO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE":
		case "28-PROCEDURA AI SENSI DEI REGOLAMENTI DEGLI ORGANI COSTITUZIONALI":
		case "29-PROCEDURA RISTRETTA SEMPLIFICATA":
		case "30-PROCEDURA DERIVANTE DA LEGGE REGIONALE":
		case "31-AFFIDAMENTO DIRETTO PER VARIANTE SUPERIORE AL 20% DELL'IMPORTO CONTRATTUALE":
			return true;
		break;
	}
	return false;
}


///////////////////////////////////////
// validazione file xml
///////////////////////////////////////
function libxml_display_errors() {
	$errors = libxml_get_errors(); 
	$return = array(); 
	foreach ($errors as $error) { 
		$return[$error->line] = 1;
	} 
	libxml_clear_errors(); 
	return count($errors);
}

function libxml_display_errors_details() {
	$errors = libxml_get_errors(); 
	$return = array(); 
	foreach ($errors as $error) { 
		lognascosto('ERRORE ANAC',$error);
		switch($error->code) {
			case 1839:
			case 1824:
			case 1840:
				$campo = '';
				$campo = explode("Element '", $error->message);
				$campo = $campo[1];
				$campo = explode("':", $campo);
				$campo = $campo[0];
				$nomeCampo = '';
				$nomeCampo = getNomeCampoAvcp($campo);
				$return[$campo] = '<strong>'.$nomeCampo.'</strong>: uno o più valori sono mancanti e/o errati ['.$campo.'].';
			break;
			default:
				$return[$error->code] = 'Errore generico ['.$error->code.'].';
			break;
		}
	}
	libxml_clear_errors(); 
	$testoErrori = '';
	foreach((array)$return as $c) {
		$testoErrori .= $c.'<br/>';
	}
	return $testoErrori;
}

function libxml_display_errors_log($error) {
	$return = "<br />\n"; 
	switch ($error->level) { 
		case LIBXML_ERR_WARNING: 
			$return .= "<b>Warning $error->code</b>: "; 
		break; 
		case LIBXML_ERR_ERROR: 
			$return .= "<b>Error $error->code</b>: "; 
		break; 
		case LIBXML_ERR_FATAL: 
			$return .= "<b>Fatal Error $error->code</b>: "; 
		break; 
	} 
	$return .= trim($error->message); 
	$return .= " on line <b>$error->line</b>\n"; 
	
	return $return; 
}

function getNomeCampoAvcp($campo) {
	switch($campo) {
		case 'codiceFiscaleProp':
			return "Codice Fiscale Amministrazione aggiudicatrice";
		break;
		case 'sceltaContraente':
			return "Procedura di scelta del contraente";
		break;
		case 'codiceFiscale':
			return "Codice Fiscale Partecipante/Aggiudicatario";
		break;
		case 'dataInizio':
			return "Data di effettivo inizio dei lavori, servizi o forniture";
		break;
		case 'dataUltimazione':
			return "Data di ultimazione dei lavori, servizi o forniture";
		break;
		case 'importoAggiudicazione':
			return "Valore Importo di aggiudicazione";
		break;
		case 'importoSommeLiquidate':
			return "Valore Importo liquidato";
		break;
		case 'cig':
			return "CIG";
		break;
		default:
			return $campo;
		break;
	}
}

function visualizzaDettaglioErroriAvcp($istanzaOggetto) {
	
	$visualizzaAlert = false;
	$testoErrori = '';
	$urlStazione = '';
	if($istanzaOggetto['id_stazione'] > 0) {
		$urlStazione = 's'.$istanzaOggetto['id_stazione'].'_';
	}
	$file = './avcp/'.$istanzaOggetto['id_ente'].'/'.$urlStazione.$istanzaOggetto['anno'].'.xml';

	// Enable user error handling 
	libxml_use_internal_errors(true); 

	$xml = new DOMDocument(); 
	$xml->load($file); 
	
	if (!$xml->schemaValidate('./avcp/datasetAppaltiL190.xsd')) { 
		$visualizzaAlert = true;
		$testoErrori .= libxml_display_errors_details();
	}
	//validazione aggiuntiva dei campi data
	$erroriData = xmlValidaDate($file);
	if ($erroriData['num']) {
		$visualizzaAlert = true;
		if($erroriData['num'] > 1) {
			$testoErrori .= '<strong>Campi di tipo \'Data\'</strong>: ci sono '.$erroriData['num'].' date non valide ('.$erroriData['errori'].')';
		} else {
			$testoErrori .= '<strong>Campo di tipo \'Data\'</strong>: è presente una data non valida ('.$erroriData['errori'].')';
		}
	}

	if($visualizzaAlert and $testoErrori != '') {
		return $testoErrori;
	}
}

function visualizzaLogCompletoErroriAvcp($istanzaOggetto) {
	
	$visualizzaAlert = false;
	$testoErrori = '';
	
	// Enable user error handling 
	libxml_use_internal_errors(true); 
	
	$urlStazione = '';
	if($istanzaOggetto['id_stazione'] > 0) {
		$urlStazione = 's'.$istanzaOggetto['id_stazione'].'_';
	}
	$file = './avcp/'.$istanzaOggetto['id_ente'].'/'.$urlStazione.$istanzaOggetto['anno'].'.xml';

	$xml = new DOMDocument(); 
	$xml->load('./avcp/'.$istanzaOggetto['id_ente'].'/'.$urlStazione.$istanzaOggetto['anno'].'.xml'); 

	if (!$xml->schemaValidate('./avcp/datasetAppaltiL190.xsd')) { 
		$visualizzaAlert = true;
		$errors = libxml_get_errors(); 
		foreach ($errors as $error) { 
			$testoErrori .= libxml_display_errors_log($error);
		} 
		libxml_clear_errors(); 
	}

	if($visualizzaAlert and $testoErrori != '') {
		return $testoErrori."<br />";
	}
}

function xmlValidaDate($file) {
	
	$errors = 0;
	$dateErrate = array();
	if(file_exists($file)) {
		$xml = new DOMDocument(); 
		$xml->load($file); 
		$data = $xml->getElementsByTagName('dataPubbicazioneDataset');
		foreach ($data as $d) {
			if (!validaData($d->nodeValue)) {
				$errors++;
				$dateErrate[] = $d->nodeValue;
			}
		}
		$data = $xml->getElementsByTagName('dataUltimoAggiornamentoDataset');
		foreach ($data as $d) {
			if (!validaData($d->nodeValue)) {
				$errors++;
				$dateErrate[] = $d->nodeValue;
			}
		}
		$data = $xml->getElementsByTagName('dataInizio');
		foreach ($data as $d) {
			if (!validaData($d->nodeValue)) {
				$errors++;
				$dateErrate[] = $d->nodeValue;
			}
		}
		$data = $xml->getElementsByTagName('dataUltimazione');
		foreach ($data as $d) {
			if (!validaData($d->nodeValue)) {
				$errors++;
				$dateErrate[] = $d->nodeValue;
			}
		}
	}
	return array('num' => $errors, 'errori' => implode(', ', $dateErrate));
}
?>