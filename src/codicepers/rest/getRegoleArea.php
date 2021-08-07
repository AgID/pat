<?
//variabili statiche
$idArea = 1;
$idSez = $restResponse->getParametro(2);
$idSezione = $idSez;
$records = array();

$elemento['id'] = 1;
$elemento['tipo_elemento'] = 'regola_default';

if ($idSez != -1) {
	// non esiste alcun modello, carico le regole di sezione
	$maxPrioInfo = maxPrioritaRegolaInfo($elemento['id'], $idSez);
	// carico la regola di pubblicazione NORMALE relativa a questa sezione
	$regola = $coreInfo->loadingRegola($idSez, $elemento['id'], 1);
	
	if (!$regola) {
		$regola = array ();
	}
} else if ($elemento['tipo_elemento'] == 'regola_default') {
	// devo caricare solamente il contenuto automatico se mi trovo in area default
	$maxPrioInfo = 0;
	$regola = array ();
	$regola[] = array (
		'id' => 1,
		'id_sezione' => -1,
		'id_regola_template' => $elemento['id'],
		'id_lingua' => 1,
		'tipo_elemento' => 'contenuto_automatico',
		'id_elemento' => 0,
		'id_stile_elemento' => 0,
		'id_stile_elemento_sottofamiglia' => 0,
		'priorita' => 0
	);
} else {
	$regola = array ();
}

if (is_array($regola) and count($regola)) {
	foreach ($regola as $elemento) {
		if (is_null($elemento['id_sezione']) OR $elemento['id_sezione'] == '') {
			$elemento['id_sezione'] = $idSezione;
		}
		if (is_null($elemento['id_elemento']) OR $elemento['id_elemento'] == '') {
			$elemento['id_elemento'] = 0;
		}
		ob_start();
		$elementoRegola = caricaElementoRegola($elemento, 0, array('escludi_ricerca' => true));
		ob_end_clean();
		if($elementoRegola['tipologia'] != 'ricerca') {
			$records[] = $elementoRegola;
		}
	}
}

echo $restResponse->response($records);
$restResponse->exitApp();
?>