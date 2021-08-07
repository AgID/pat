<?
function getRichiamoSceltaTrasp($campo,$idOgg, $contenutoModello) {
	global $oggetti,$server_url;
	
	foreach ($oggetti as $oggTemp) {
		if ($oggTemp['id'] == $idOgg) {
			$campoDef = $oggTemp['campo_default'];
		}
	}
	
	$res = array();
	if($contenutoModello[$campo] != '') {
		$cs = explode(',', $contenutoModello[$campo]);
		foreach((array)$cs as $c) {
			$is = mostraDatoOggetto($c, $idOgg, '*');
			if($is['id'] > 0) {
				$res[] = '<li><a href="'.$server_url.'archivio'.$idOgg.'_'.$campoDef.'_'.$is['id_sezione'].'_'.$is['id'].'.html">'.$is[$campoDef].'</a></li>';
			}
		}
	}
	if(count($res)>0) {
		return '<div class="contenitoreIstanza1"><div class="titolo2">'.$contenutoModello[$campo.'_tit'].'</div><ul>'.implode('',$res).'</ul></div>';
	} else {
		return '';
	}
}

// Verifico esistenza del paragrafo
$oraCorrente = time();
//$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente = ".$idEnte." AND id_sezione_etrasp = ".$restResponse->getParametro(2)." LIMIT 0,1";
$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente = ".$idEnte." AND id_sezione_etrasp = ".$restResponse->getParametro(2)." AND tipologia = 'contenuto' ORDER BY ordine";

if( !($result = $database->connessioneConReturn($sql)) ) {
	//gestire errore
}
$contenuto = '';
$contenutoModelli = $database->sqlArrayAss($result);

foreach((array)$contenutoModelli as $contenutoModello) {
	if ($contenutoModello['html_generico'] != '' AND $contenutoModello['html_generico'] != '<p></p>' AND $contenutoModello['html_generico'] != '<p>&nbsp;</p>') {
		$contenuto .= $contenutoModello['html_generico'];
		if ($entePubblicato['mostra_data_aggiornamento']) {
			if(($entePubblicato['id']!=23 OR ($entePubblicato['id']==23 AND ($sezioneNavigazione['id']!=725 AND $sezioneNavigazione['id']!=726))) AND
			   ($entePubblicato['id']!=33 OR ($entePubblicato['id']==33 AND ($sezioneNavigazione['id']!=725 AND $sezioneNavigazione['id']!=726)))
			) {
				$contenuto .= "<div id=\"dataAggiornamento\">Contenuto inserito il ".visualizzaData($contenutoModello['data_creazione'],'d-m-Y')." aggiornato al ".visualizzaData($contenutoModello['ultima_modifica'],'d-m-Y')."</div>";
			}
		}
	}
	//verifica di eventuali richiami a scelta
	$contenuto .= getRichiamoSceltaTrasp('modulistica',5, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('normativa',27, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('referenti',3, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('regolamenti',19, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('procedimenti',16, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('provvedimenti',28, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('strutture',13, $contenutoModello);
	$contenuto .= getRichiamoSceltaTrasp('incarichi',4, $contenutoModello);
	 
}

$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente = ".$idEnte." AND id_sezione_etrasp = ".$restResponse->getParametro(2)." AND tipologia = 'pagina' ORDER BY ordine";

if( !($result = $database->connessioneConReturn($sql)) ) {
	//gestire errore
}

$contenutoModelli = $database->sqlArrayAss($result);

foreach((array)$contenutoModelli as $contenutoModello) {
	if ($contenutoModello['html_generico'] != '' AND $contenutoModello['html_generico'] != '<p></p>' AND $contenutoModello['html_generico'] != '<p>&nbsp;</p>') {
		
		$contenuto .= "<div class=\"campoOggetto171 \">";
		$contenuto .= $contenutoModello['titolo'];
		$contenuto .= "</div>";
		
		$contenuto .= $contenutoModello['html_generico'];
		if ($entePubblicato['mostra_data_aggiornamento']) {
			if(($entePubblicato['id']!=23 OR ($entePubblicato['id']==23 AND ($sezioneNavigazione['id']!=725 AND $sezioneNavigazione['id']!=726))) AND
					($entePubblicato['id']!=33 OR ($entePubblicato['id']==33 AND ($sezioneNavigazione['id']!=725 AND $sezioneNavigazione['id']!=726)))
			) {
				$contenuto .= "<div id=\"dataAggiornamento\">Contenuto inserito il ".visualizzaData($contenutoModello['data_creazione'],'d-m-Y')." aggiornato al ".visualizzaData($contenutoModello['ultima_modifica'],'d-m-Y')."</div>";
			}
		}

	}
}


$record = array();
$record['contenuto'] = $contenuto;
echo $restResponse->response($record, true);
?> 