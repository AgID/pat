<?
// mettere qui funzioni ad hoc per i progetti un pò più personalizzati

if($idDocumento and $idOggetto == 4) {
	$inc = mostraDatoOggetto($idDocumento,4,'*');
	if($inc['dirigente']) {
		$idSezione == $idSezOri;
		$sezioneNavigazione = caricaSezioneDb($idSezOri);
	}
}

//imposto limite per i records restituiti nelle chiamate rest
$configurazione['limite_records_rest'] = 500;

//usato nella visualizzazione dei pagamenti (incarichi)
$configurazione['compenso_incarichi'] = "compenso_erogato != ''";
if($entePubblicato['importi_numerici']) {
	$configurazione['compenso_incarichi'] = "compenso_erogato_valore != ''";
}

if($_POST['cerca_oggetto'] == 4 and $_POST['anno_incarico_mcrt_18'] != 'qualunque' and isset($_POST['anno_incarico_mcrt_18'])) {
	//imposto la ricerca per anno degli incarichi
	$y = $_POST['anno_incarico_mcrt_18'];
	$inizio = mktime(2,0,0,1,1,$y);
	$fine = mktime(2,0,0,12,31,$y);
	
	$_POST['inizio_incarico_mcrt_19_condizione'] = 'maggiore';
	$_POST['inizio_incarico_mcrt_19data'] = '01/01/'.$y;
	$_POST['inizio_incarico_mcrt_19'] = $inizio;
	
	$_POST['inizio_incarico_mcrt_20_condizione'] = 'minore';
	$_POST['inizio_incarico_mcrt_20data'] = '31/12/'.$y;
	$_POST['inizio_incarico_mcrt_20'] = $fine;
}

if($_POST['cerca_oggetto'] == 38 and $_POST['anno_sovvenzione_mcrt_18'] != 'qualunque' and isset($_POST['anno_sovvenzione_mcrt_18'])) {
	//imposto la ricerca per anno delle sovvenzioni
	$y = $_POST['anno_sovvenzione_mcrt_18'];
	$inizio = mktime(2,0,0,1,1,$y);
	$fine = mktime(2,0,0,12,31,$y);

	$_POST['data_mcrt_19_condizione'] = 'maggiore';
	$_POST['data_mcrt_19data'] = '01/01/'.$y;
	$_POST['data_mcrt_19'] = $inizio;

	$_POST['data_mcrt_20_condizione'] = 'minore';
	$_POST['data_mcrt_20data'] = '31/12/'.$y;
	$_POST['data_mcrt_20'] = $fine;
	
}

if($_POST['cerca_oggetto'] == 28 and $_POST['anno_provvedimento_mcrt_18'] != 'qualunque' and isset($_POST['anno_provvedimento_mcrt_18'])) {
	//imposto la ricerca per anno dei provvedimenti
	$y = $_POST['anno_provvedimento_mcrt_18'];
	$inizio = mktime(2,0,0,1,1,$y);
	$fine = mktime(2,0,0,12,31,$y);

	$_POST['data_mcrt_19_condizione'] = 'maggiore';
	$_POST['data_mcrt_19data'] = '01/01/'.$y;
	$_POST['data_mcrt_19'] = $inizio;

	$_POST['data_mcrt_20_condizione'] = 'minore';
	$_POST['data_mcrt_20data'] = '31/12/'.$y;
	$_POST['data_mcrt_20'] = $fine;

}

if($_POST['cerca_oggetto'] == 63 and $_POST['anno_mcrt_20'] != 'qualunque' and isset($_POST['anno_mcrt_20'])) {
	//imposto la ricerca per anno delle sovvenzioni
	$y = $_POST['anno_mcrt_20'];
	$inizio = mktime(2,0,0,1,1,$y);
	$fine = mktime(2,0,0,12,31,$y);

	$_POST['data_mcrt_19_condizione'] = 'maggiore';
	$_POST['data_mcrt_19data'] = '01/01/'.$y;
	$_POST['data_mcrt_19'] = $inizio;

	$_POST['data_mcrt_20_condizione'] = 'minore';
	$_POST['data_mcrt_20data'] = '31/12/'.$y;
	$_POST['data_mcrt_20'] = $fine;

}

function sezioneEsiste($idSezioni) {

	$arraySezioni = explode(',',$idSezioni);
	foreach((array)$arraySezioni as $idSez) {
		if(nomeSezDaId($idSez) != '') {
			return true;
		}		
	}
	return false;
}


function inviaNotificaAppuntamento($idEvento) {
	global $database, $dati_db, $configurazione;

	$idOggetto = 26;
	include_once('classi/admin_oggetti.php');
	$o = new oggettiAdmin($idOggetto,'si');
	$appuntamento = $o->caricaOggetto($idEvento);
	
	$_POST['data_appuntamento'] = date('d/m/Y', $appuntamento['data_appuntamento']);
	$ore = floor($appuntamento['ora_inizio_appuntamento'] / 60);
	$minuti = $appuntamento['ora_inizio_appuntamento'] - ($ore * 60);
	if ($minuti == 0) {
		$minuti = '00';
	}
	if ($minuti < 10 and $minuti !== '00') {
		$minuti = '0'.$minuti;
	}
	$_POST['ora_inizio_appuntamento'] = $ore . ":" . $minuti;
	$_POST['email'] = $appuntamento['email'];
	$_POST['notifica'] = 1;
	
	centroComunicazioni('webapp_modifica',"L'utente segnalato ha modificato istanza <b>".$appuntamento['nome_completo']."</b> di ".$o->nomeOggetto." in modalità web application.",$idOggetto);
	
}

function inviaNotificaAppuntamentoAnnullato($idEvento) {
	global $database, $dati_db, $configurazione;

	$idOggetto = 26;
	include_once('classi/admin_oggetti.php');
	$o = new oggettiAdmin($idOggetto,'si');
	$appuntamento = $o->caricaOggetto($idEvento);
	
	$_POST['email'] = $appuntamento['email'];
	$_POST['notifica'] = 1;
	
	centroComunicazioni('webapp_cancella',"L'utente segnalato ha cancellato istanza <b>".$appuntamento['nome_completo']."</b> di ".$o->nomeOggetto." in modalità web application.",$idOggetto);
	
}

function visualizzaOrganoPolitico($istanzaOggetto, $visEtichetta = true) {
	if($istanzaOggetto['organo'] != '') {
		if($istanzaOggetto['organo']=='segretario generale'){
			echo "<div>".traduciOrgani($istanzaOggetto['organo'])."</div>";
		} else {
			$organo = traduciOrgani($istanzaOggetto['organo']);
			if($organo != '') {
				if(moduloAttivo('agid') and ($istanzaOggetto['organo'] == 'direzione generale' or $istanzaOggetto['organo'] == 'giunta comunale' or $istanzaOggetto['organo'] == 'consiglio comunale')) {
					$eti = 'Organo di indirizzo politico-amministrativo: ';
					if(!$visEtichetta) {
						$eti = '';
					}
					echo "<div>".$eti.$organo."</div>";
				} else {
					$eti = 'Organo politico-amministrativo: ';
					if(!$visEtichetta) {
						$eti = '';
					}
					$organi = explode(', ',$organo);
					sort($organi);
					$organo = implode(', ',$organi);
					echo "<div>".$eti.$organo."</div>";
				}
			}
		}
	}
}

function visualizzaIncaricoPolitico($istanzaOggetto) {
	global $configurazione, $idSezione;
	
	if(moduloAttivo('agid') && $idSezione == 705) {
		echo "<div>Organo di controllo (art.20 d.lgs 30 giugno 2011, n.123): ".$istanzaOggetto['ruolo_politico']."</div>";
	} else {
		if($istanzaOggetto['ruolo_politico']) {
			$eti = "Incarico di stampo politico: ";
			if($configurazione['ometti_etichetta_ruolo_politico']) {
				$eti = '';
			}
			echo "<div>".$eti.$istanzaOggetto['ruolo_politico']."</div>";
		}
	}
}

function totaleSommeLiquidate($istanzaOggetto) {
	global $database, $dati_db, $configurazione, $idEnte;
	$now = strtotime("now");
	//echo $date;
	if($istanzaOggetto['data_scadenza']<=$now){
		if($istanzaOggetto['tipologia'] != 'somme liquidate') {
			$totale = 0;
			$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnte." "
					." AND (bando_collegato = ".$istanzaOggetto['id']." OR id = ".$istanzaOggetto['id']
					.") ORDER BY data_attivazione desc";
			if ( !($result = $database->connessioneConReturn($sql)) ) {}
			$bandi = $database->sqlArrayAss($result);
			foreach ((array)$bandi as $bando) {
				if(floatval($bando['importo_liquidato'])) {
					$totale += floatval($bando['importo_liquidato']);
				}
			}
			if($totale > 0) {
				echo "<div class=\"campoOggetto114\"> Importo delle somme liquidate: &euro; ".number_format($totale, 2, ',', '.')."</div>";
			}
		}
	}
}

function elencoCigMultipli($istanzaOggetto) {
	global $database, $dati_db, $configurazione, $idEnte, $server_url, $base_url;
	
	if($istanzaOggetto['tipologia'] == 'bandi ed inviti') {
		$out = "";
		$totale = 0;
		$lotti = prendiLotti($istanzaOggetto['id_record_cig_principale']);
		foreach ((array)$lotti as $lotto) {
			$outValore = $lotto['valore_base_asta'];
			if(floatval($lotto['valore_base_asta']) > 0) {
				$outValore = number_format($lotto['valore_base_asta'], 2, ',', '.');
			}
			$out .= "<div class=\"campoOggetto114\"> CIG: <a href=\"".$base_url."index.php?id_oggetto=11&amp;id_cat=".$lotto['id_sezione']."&amp;id_doc=".$lotto['id']."\">".$lotto['cig']."</a> - Importo dell'appalto: &euro; ".$outValore."</div>";
			if(floatval($lotto['valore_base_asta']) > 0) {
				$totale += floatval($lotto['valore_base_asta']);
			}
		}
		if(count($lotti) > 0 and $lotti[0]['id'] > 0) {
			echo $out;
			if($totale > 0) {
				echo "<div>&nbsp;</div><div class=\"campoOggetto114\"> Totale importo dell'appalto: &euro; ".number_format($totale, 2, ',', '.')."</div>";
			}
		}
	}
}

// struttura_mcrt_14
function selectRicercaStruttura($campo, $etichetta = 'Struttura', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;
	
	$condizioneID = '';
	if($configurazione['id_oggetto_ricerca'] > 0) {
		$or = new documento($configurazione['id_oggetto_ricerca']);
		$campoRicerca = explode('_mcrt_', $campo);
		$campoRicerca = $campoRicerca[0];
		$sql = "SELECT GROUP_CONCAT(DISTINCT(".$campoRicerca.")) AS idExt FROM ".$dati_db['prefisso'].$or->tabella." WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' AND ".$campoRicerca.">0";
		if ( !($result = $database->connessioneConReturn($sql)) ) {}
		$istanza = $database->sqlArray($result);
		if($istanza['idExt'] != '') {
			$condizioneID = " AND id IN (".$istanza['idExt'].")";
		}
	}
	
	$sql = "SELECT id,nome_ufficio FROM ".$dati_db['prefisso']."oggetto_uffici WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' AND (__archiviata != 1 OR __archiviata IS NULL) ".$condizioneID." ORDER BY nome_ufficio";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);				
	foreach ((array)$istanze as $istanza) {
		$stringa = '';
		if ($istanza['id'] == $_POST[$campo]) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$istanza['id']."\"".$stringa." title=\"".$istanza['nome_ufficio']."\">".$istanza['nome_ufficio']."</option>";
	}
	echo '<div class="'.$classCampo.'">
		<span style="white-space: nowrap;">
			<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
			<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}

function ricercaOggettoProvvedimenti($campo, $etichetta = 'Oggetto', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $configurazione, $idEnte;
	
	if($configurazione['piattaforma_at'] == 'pat_aci_ac' or $idEnte == 170) {
		$etichetta = 'Organo';
	}
	
	if($configurazione['arraySelectRicercaOggettoProvvedimenti'] > 0) {
		foreach ((array)$configurazione['arraySelectRicercaOggettoProvvedimenti'] as $istanza) {
			$stringa = '';
			if ($istanza == $_POST[$campo]) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$istanza."\"".$stringa." title=\"".$istanza."\">".$istanza."</option>";
		}
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
					<option value="" title="qualunque">qualunque</option>'.$options.'
				</select>
			</span>
		</div>';
	} else {
		echo '<div class="'.$classCampo.'">
			<div style="white-space: nowrap; display: inline;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<input type="text" class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'" title="'.$campo.'" placeholder="qualunque" value="'.addslashes($_POST[$campo]).'" />
			</div>
		</div>';
	}
	//<input class="stileForm75" type="text" name="oggetto_mcrt_11" title="oggetto_mcrt_11" id="oggetto_mcrt_11" placeholder="qualunque" value=""></div> </div>
}

function selectRicercaStrutturaTestuale($campo, $etichetta = 'Struttura', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;

	$sql = "SELECT DISTINCT(struttura_nome) AS nome FROM ".$dati_db['prefisso']."oggetto_tassi_assenza WHERE (id_ente = ".$idEnte.") ORDER BY struttura_nome";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);
	foreach ((array)$istanze as $istanza) {
		$stringa = '';
		if ($istanza['nome'] == stripslashes($_POST[$campo])) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$istanza['nome']."\"".$stringa." title=\"".$istanza['nome']."\">".$istanza['nome']."</option>";
	}
	echo '<div class="'.$classCampo.'">
		<span style="white-space: nowrap;">
			<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
			<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaAnnoTA($campo, $etichetta = 'Anno', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;

	$sql = "SELECT DISTINCT(anno) AS anno FROM ".$dati_db['prefisso']."oggetto_tassi_assenza WHERE (id_ente = ".$idEnte.") ORDER BY anno";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);
	foreach ((array)$istanze as $istanza) {
		$stringa = '';
		if ($istanza['anno'] == $_POST[$campo]) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$istanza['anno']."\"".$stringa." title=\"".$istanza['anno']."\">".$istanza['anno']."</option>";
	}
	echo '<div class="'.$classCampo.'">
		<span style="white-space: nowrap;">
			<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
			<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaAnnoControlliRilievi($campo, $etichetta = 'Anno', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;

	$sql = "SELECT DISTINCT(DATE_FORMAT(FROM_UNIXTIME(data), '%Y')) AS anno FROM ".$dati_db['prefisso']."oggetto_controlli_rilievi WHERE (id_ente = ".$idEnte.") AND data > 0 ORDER BY anno";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);
	foreach ((array)$istanze as $istanza) {
		$stringa = '';
		if ($istanza['anno'] == $_POST[$campo]) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$istanza['anno']."\"".$stringa." title=\"".$istanza['anno']."\">".$istanza['anno']."</option>";
	}
	echo '<div class="'.$classCampo.'">
		<span style="white-space: nowrap;">
			<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
			<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaQualificaPersonale($campo, $etichetta = 'Qualifica', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;

	$qualifiche = prendiQualifichePersonale(false);
	
	if(count($qualifiche['valori']) > 0 and $qualifiche['valori'][0] != '') {
		for($i=0; $i<count($qualifiche['valori']); $i++) {
			$stringa = '';
			if ($qualifiche['valori'][$i] == $_POST[$campo]) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$qualifiche['valori'][$i]."\"".$stringa." title=\"".$qualifiche['etichette'][$i]."\">".$qualifiche['etichette'][$i]."</option>";
		}
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
					<option value="" title="qualunque">qualunque</option>'.$options.'
				</select>
			</span>
		</div>';
		
		$configurazione['tabella_dirigenti_vis_qualifica'] = true;
	}
}

function selectRicercaArgomentoNormativa($campo, $etichetta = 'Argomento', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;

	//$recs = explode(',', 'Organizzazione dell\'Ente,Sovvenzioni e contributi,Altro');
	
	$rTemp = prendiArgomentoNormativa();
	$recs = $rTemp['valori'];

	if(count($recs) > 0) {
		for($i=0; $i<count($recs); $i++) {
			$stringa = '';
			if ($recs[$i] == $_POST[$campo]) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$recs[$i]."\"".$stringa." title=\"".$recs[$i]."\">".$recs[$i]."</option>";
		}
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
					<option value="" title="qualunque">qualunque</option>'.$options.'
				</select>
			</span>
		</div>';

		$configurazione['tabella_dirigenti_vis_qualifica'] = true;
	}
}



function ricercaNomeUfficio($campo, $etichetta = 'Nome ufficio', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte;
	
	if(moduloAttivo('ricerca_nome_ufficio_select')) {
		$sql = "SELECT id,nome_ufficio FROM ".$dati_db['prefisso']."oggetto_uffici WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' ORDER BY nome_ufficio";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
		}
		$istanze = $database->sqlArrayAss($result);				
		foreach ((array)$istanze as $istanza) {
			$stringa = '';
			if ($istanza['nome_ufficio'] == $_POST[$campo]) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$istanza['nome_ufficio']."\"".$stringa." title=\"".$istanza['nome_ufficio']."\">".$istanza['nome_ufficio']."</option>";
		}
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
					<option value="" title="qualunque">qualunque</option>'.$options.'
				</select>
			</span>
		</div>';
	} else {
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<input class="'.$classForm.'" type="text" name="'.$campo.'" id="'.$campo.'" value="qualunque" onfocus="pulisciQualunque(\''.$campo.'\');" onblur="impostaQualunque(\''.$campo.'\');">
			</span>
		</div>';
	}
	
}

// responsabile_mcrt_15
function selectRicercaResponsabile($campo) {
	global $database, $dati_db, $configurazione, $idEnte;
	
	$condizioneID = '';
	if($configurazione['id_oggetto_ricerca'] > 0) {
		$or = new documento($configurazione['id_oggetto_ricerca']);
		$campoRicerca = explode('_mcrt_', $campo);
		$campoRicerca = $campoRicerca[0];
		$sql = "SELECT GROUP_CONCAT(DISTINCT(".$campoRicerca.")) AS idExt FROM ".$dati_db['prefisso'].$or->tabella." WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' AND ".$campoRicerca.">0";
		if ( !($result = $database->connessioneConReturn($sql)) ) {}
		$istanza = $database->sqlArray($result);
		if($istanza['idExt'] != '') {
			$condizioneID = " AND id IN (".$istanza['idExt'].")";
		}
	}
	
	$sql = "SELECT id,referente FROM ".$dati_db['prefisso']."oggetto_riferimenti WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' ".$condizioneID." ORDER BY referente";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);				
	foreach ((array)$istanze as $istanza) {
		$stringa = '';
		if ($istanza['id'] == $_POST[$campo]) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$istanza['id']."\"".$stringa." title=\"".$istanza['referente']."\">".$istanza['referente']."</option>";
	}
	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="'.$campo.'" class="labelClass">Responsabile </label>
			<select class="stileForm75" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}


function stampaBeneficiario($istanzaOggetto, $link = false){
	if(!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy'))){
		if($link) {
			echo "<div class=\"campoOggetto184\"><a href=\"".$base_url."index.php?id_oggetto=38&amp;id_doc=".$istanzaOggetto['id']."\">".$istanzaOggetto['nominativo']."</a></div>";
		} else {
			echo "<div class=\"campoOggetto184\">".$istanzaOggetto['nominativo']."</div>";
		}
	}else{
		echo "Omissis";
	}
}

function selectRicercaBandi() {
	global $database, $dati_db, $configurazione, $idEnte;
	if(moduloAttivo('bandigara')) {
		$valori = array('bandi ed inviti' => 'bandi di gara','esiti' => 'esiti','delibere e determine a contrarre' => 'determine art. 57 comma 6 dlgs. 163/2006','determina_32' => 'determine art. 32 comma 2 dlgs. 50/2016','affidamenti' => 'affidamenti','avvisi pubblici' => 'avvisi');
	} else {
		$valori = array('bandi ed inviti' => 'bandi ed inviti','esiti' => 'esiti','delibere e determine a contrarre' => 'delibere e determine a contrarre','determina_32' => 'determine art. 32 comma 2 dlgs. 50/2016','affidamenti' => 'affidamenti','avvisi pubblici' => 'avvisi pubblici');
	}
	$options = "<option value=\"qualunque\">qualunque</option>";
	foreach ((array)$valori as $key => $val) {
		$stringa = '';
		if ($key == $_POST['tipologia_mcrt_10']) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$key."\"".$stringa." title=\"".$val."\">".$val."</option>";
	}
	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="tipologia_mcrt_10" class="labelClass">Tipologia </label>
			<select class="stileForm75" id="tipologia_mcrt_10" name="tipologia_mcrt_10">
				'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaIncarichi() {
	global $database, $dati_db, $configurazione, $idEnte,$idSezione;
	
	$y = date('Y');
	$options = "<option value=\"qualunque\">qualunque</option>";
	for($i=0; $i<5; $i++) {
		$stringa = '';
		if (($y-$i) == $_POST['anno_incarico_mcrt_18']) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".($y-$i)."\"".$stringa." title=\"Anno incarico\">".($y-$i)."</option>";
	}
	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="anno_incarico_mcrt_18" class="labelClass">Anno inizio incarico</label>
			<select class="stileForm75" id="anno_incarico_mcrt_18" name="anno_incarico_mcrt_18">
				'.$options.'
			</select>
		</span>
	</div>';
	
	
	//if($idSezione != 59) {
	if(false) {
		$valori = array('incarichi dipendenti altra amministrazione' => 'incarichi retribuiti e non retribuiti altra amministrazione','incarichi dipendenti esterni' => 'incarichi retribuiti e non retribuiti affidati a soggetti esterni');
		$options = "<option value=\"qualunque\">qualunque</option>";
		foreach ((array)$valori as $key => $val) {
			$stringa = '';
			if ($key == $_POST['tipo_incarico_mcrt_13']) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$key."\"".$stringa." title=\"".$val."\">".$val."</option>";
		}
		echo '<div class="campoOggetto71">
			<span style="white-space: nowrap;">
				<label for="tipo_incarico_mcrt_13" class="labelClass">Tipo di incarico </label>
				<select class="stileForm75" id="tipo_incarico_mcrt_13" name="tip_incarico_mcrt_13">
					'.$options.'
				</select>
			</span>
		</div>';
	}
}

function selectRicercaIncarichiTipoConsulenza() {
    global $database, $dati_db, $configurazione, $idEnte,$idSezione;
    
    $tipi = prendiTipoConsulenza($istanzaOggetto['tipo_consulenza']);
    if(count($tipi['valori'])>0 and $tipi['valori'][0] != '') {
        $options = "<option value=\"qualunque\">qualunque</option>";
        for($i=0; $i<count($tipi['valori']); $i++) {
            $stringa = '';
            if ($tipi['valori'][$i] == $_POST['tipo_consulenza_mcrt_18']) {
                $stringa = ' selected="selected" ';
            }
            $options .= "<option value=\"".$tipi['valori'][$i]."\"".$stringa.">".$tipi['etichette'][$i]."</option>";
        }
        echo '<div class="campoOggetto71">
    		<span style="white-space: nowrap;">
    			<label for="tipo_consulenza_mcrt_18" class="labelClass">Tipo consulenza</label>
    			<select class="stileForm75" id="tipo_consulenza_mcrt_18" name="tipo_consulenza_mcrt_18">
    				'.$options.'
    			</select>
    		</span>
    	</div>';
    }
}

function selectRicercaSovvenzioniAnno() {
	global $database, $dati_db, $configurazione, $idEnte,$idSezione;
	
	$options = "<option value=\"qualunque\">qualunque</option>";
	
	$sqlSW = '';
	if(!$configurazione['includi_istanze_workflow']) {
		$sqlSW = ' AND stato_workflow=\'finale\' ';
	}
	
	$sql = "SELECT id,data,DATE_FORMAT(FROM_UNIXTIME(data), '%Y') as anno_incarico FROM ".$dati_db['prefisso']."oggetto_sovvenzioni WHERE
						permessi_lettura != 'H' AND stato != 0  ".$sqlSW." AND permessi_lettura = 'N/A' AND stato_pubblicazione = '100' AND
						id_ente = '".$idEnte."' AND ('".$idEnte."' != '0' AND '".$idEnte."' != '')
	        			AND DATA > 0 AND DATA IS NOT NULL GROUP BY anno_incarico ORDER BY anno_incarico DESC";
	if ( !($result = $database->connessioneConReturn($sql)) ) {}
	$anni = $database->sqlArrayAss($result);
	foreach ((array)$anni as $recAnno) {
		if($recAnno['anno_incarico'] > 1970) {
			$stringa = '';
			if ($recAnno['anno_incarico'] == $_POST['anno_sovvenzione_mcrt_18']) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$recAnno['anno_incarico']."\"".$stringa.">".$recAnno['anno_incarico']."</option>";
		}
	}
	
	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="anno_sovvenzione_mcrt_18" class="labelClass">Anno </label>
			<select class="stileForm75" id="anno_sovvenzione_mcrt_18" name="anno_sovvenzione_mcrt_18">
				'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaSovvenzioniLiqAnno() {
	global $database, $dati_db, $configurazione, $idEnte,$idSezione;

	$options = "<option value=\"qualunque\">qualunque</option>";

	$sqlSW = '';
	if(!$configurazione['includi_istanze_workflow']) {
		$sqlSW = ' AND stato_workflow=\'finale\' ';
	}
	
	$sql = "SELECT data_compenso_erogato FROM ".$dati_db['prefisso']."oggetto_sovvenzioni WHERE
						permessi_lettura != 'H' AND stato != 0  ".$sqlSW." AND permessi_lettura = 'N/A' AND stato_pubblicazione = '100' AND
						id_ente = '".$idEnte."' AND ('".$idEnte."' != '0' AND '".$idEnte."' != '')
	        			AND data_compenso_erogato > 0 AND data_compenso_erogato IS NOT NULL GROUP BY data_compenso_erogato ORDER BY data_compenso_erogato DESC";
	if ( !($result = $database->connessioneConReturn($sql)) ) {}
	$anni = $database->sqlArrayAss($result);
	foreach ((array)$anni as $recAnno) {
		if($recAnno['data_compenso_erogato'] > 1970) {
			$stringa = '';
			if ($recAnno['data_compenso_erogato'] == $_POST['data_compenso_erogato_mcrt_18']) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$recAnno['data_compenso_erogato']."\"".$stringa.">".$recAnno['data_compenso_erogato']."</option>";
		}
	}

	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="data_compenso_erogato_mcrt_18" class="labelClass">Anno </label>
			<select class="stileForm75" id="data_compenso_erogato_mcrt_18" name="data_compenso_erogato_mcrt_18">
				'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaIncarichiLiqAnno() {
	global $database, $dati_db, $configurazione, $idEnte,$idSezione;

	$options = "<option value=\"qualunque\">qualunque</option>";
	
	$sqlSW = '';
	if(!$configurazione['includi_istanze_workflow']) {
		$sqlSW = ' AND stato_workflow=\'finale\' ';
	}

	$sql = "SELECT data_compenso_erogato FROM ".$dati_db['prefisso']."oggetto_incarichi WHERE
						permessi_lettura != 'H' AND stato != 0  ".$sqlSW." AND permessi_lettura = 'N/A' AND stato_pubblicazione = '100' AND
						id_ente = '".$idEnte."' AND ('".$idEnte."' != '0' AND '".$idEnte."' != '')
	        			AND data_compenso_erogato > 0 AND data_compenso_erogato IS NOT NULL GROUP BY data_compenso_erogato ORDER BY data_compenso_erogato DESC";
	if ( !($result = $database->connessioneConReturn($sql)) ) {}
	$anni = $database->sqlArrayAss($result);
	foreach ((array)$anni as $recAnno) {
		if($recAnno['data_compenso_erogato'] > 1970) {
			$stringa = '';
			if ($recAnno['data_compenso_erogato'] == $_POST['data_compenso_erogato_mcrt_18']) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$recAnno['data_compenso_erogato']."\"".$stringa.">".$recAnno['data_compenso_erogato']."</option>";
		}
	}

	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="data_compenso_erogato_mcrt_18" class="labelClass">Anno </label>
			<select class="stileForm75" id="data_compenso_erogato_mcrt_18" name="data_compenso_erogato_mcrt_18">
				'.$options.'
			</select>
		</span>
	</div>';
}

function selectRicercaProvvedimentiAnno() {
	global $database, $dati_db, $configurazione, $idEnte,$idSezione;

	$options = "<option value=\"qualunque\">qualunque</option>";

	$sqlSW = '';
	if(!$configurazione['includi_istanze_workflow']) {
		$sqlSW = ' AND stato_workflow=\'finale\' ';
	}

	$sql = "SELECT id,data,DATE_FORMAT(FROM_UNIXTIME(data), '%Y') as anno_provvedimento FROM ".$dati_db['prefisso']."oggetto_provvedimenti WHERE
						permessi_lettura != 'H' AND stato != 0  ".$sqlSW." AND permessi_lettura = 'N/A' AND stato_pubblicazione = '100' AND
						id_ente = '".$idEnte."' AND ('".$idEnte."' != '0' AND '".$idEnte."' != '')
	        			AND DATA > 0 AND DATA IS NOT NULL GROUP BY anno_provvedimento ORDER BY anno_provvedimento DESC";
	if ( !($result = $database->connessioneConReturn($sql)) ) {}
	$anni = $database->sqlArrayAss($result);
	foreach ((array)$anni as $recAnno) {
		if($recAnno['anno_provvedimento'] > 1970) {
			$stringa = '';
			if ($recAnno['anno_provvedimento'] == $_POST['anno_provvedimento_mcrt_18']) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$recAnno['anno_provvedimento']."\"".$stringa.">".$recAnno['anno_provvedimento']."</option>";
		}
	}

	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="anno_provvedimento_mcrt_18" class="labelClass">Anno </label>
			<select class="stileForm75" id="anno_provvedimento_mcrt_18" name="anno_provvedimento_mcrt_18">
				'.$options.'
			</select>
		</span>
	</div>';
}

function visualizzaOggettoIncarico($incarichi) {
	global $database, $dati_db, $configurazione, $idEnte, $server_url, $base_url;
	$html = "<h4 class=\"campoOggetto86\">Incarichi assegnati</h4>";
	$lista = "";
	$aperturaLista = "<div><ul>";
	$chiusuraLista = "</ul></div>";
	$incarichi = explode(',',$incarichi);
	foreach ((array)$incarichi as $val) {
		//esclude gli incarichi amministrativi di vertice
		//if(mostraDatoOggetto($val,4,'id') and !mostraDatoOggetto($val,4,'dirigente')){
		if(mostraDatoOggetto($val,4,'id')){
			$lista .= "<li>";
			$lista .= "<a href=\"".$base_url."index.php?id_oggetto=4&amp;id_cat=".mostraDatoOggetto($val,4,'id_sezione')."&amp;id_doc=".mostraDatoOggetto($val,4,'id')."\">".mostraDatoOggetto($val,4,'oggetto')."</a>";
			$lista .= "</li>";
		}
	}
	if($lista!=""){
		$html .= $aperturaLista.$lista.$chiusuraLista;
	}else {
		$html="";
	}
	echo $html;
}

function visualizzaIncarichiDiVertice($istanzaOggetto) {
	global $database, $dati_db, $configurazione, $idEnte, $server_url, $base_url,$idSezione;
	
	$incarichi = explode(',',$istanzaOggetto['incarico']);
	foreach ((array)$incarichi as $val) {
		//esclude gli incarichi amministrativi di vertice
		if(mostraDatoOggetto($val,4,'id')){
			echo "<div>";
			echo "Incarico: <a href=\"".$base_url."index.php?id_sezione=".$idSezione."&amp;id_doc=".$val."&amp;id_lcs=4\">".mostraDatoOggetto($val,4,'oggetto')."</a>";
			echo "</div>";
		}
	}
}

function visualizzaRuolo($istanzaOggetto, $visEti = true) {
	global $database, $dati_db, $configurazione, $idEnte, $server_url, $base_url,$idSezione;
	
	if(!moduloAttivo('agid')) {
		echo "<div>";
		if($visEti) {
			echo "Ruolo: ";
		}
		echo "".$istanzaOggetto['ruolo'];
		/*
		if($istanzaOggetto['ad_interim']) {
			echo " - ad interim";
		}
		*/
		echo "</div>";
	}
}

function linkLetturaReferente($istanzaOggetto, $stile = 0) {
	global $base_url;
	
	if(moduloAttivo('nome_cognome_responsabile')) {
		$nomeOgg = $istanzaOggetto['nome'].' '.$istanzaOggetto['cognome'];
		if(trim($nomeOgg) == '') {
			$nomeOgg = $istanzaOggetto['referente'];
		}
	} else {
		$nomeOgg = $istanzaOggetto['referente'];
	}
	$strAncora = $base_url . "index.php?id_oggetto=3&amp;id_cat=" . $istanzaOggetto['id_sezione']. "&amp;id_doc=" . $istanzaOggetto['id'];
	echo "<div class=\"campoOggetto".$stile."\"><a href=\"".$strAncora."\"><strong>".$istanzaOggetto['tit']." ".$nomeOgg."</strong></a></div>";
}

function visualizzaResponsabile($istanzaOggetto, $cls = 'campoOggetto78') {
	global $database, $dati_db, $configurazione, $idEnte, $server_url, $base_url,$idSezione;
	
	$referenti = explode(',',$istanzaOggetto['referente']);
	
	if(count($referenti) > 0 and $referenti[0] > 0) {
		
		$out = array();
		foreach((array)$referenti as $ref) {
			$strAncora = $base_url . "index.php?id_oggetto=3&amp;id_cat=" . $istanzaOggetto['id_sezione']. "&amp;id_doc=" . $ref;
			if(moduloAttivo('nome_cognome_responsabile')) {
				$nomeOgg = mostraDatoOggetto($ref, 3,'nome').' '.mostraDatoOggetto($ref, 3,'cognome');
				if(trim($nomeOgg) == '') {
					$nomeOgg = mostraDatoOggetto($ref, 3);
				}
			} else {
				$nomeOgg = mostraDatoOggetto($ref, 3);
			}
			if (trim($nomeOgg) != '') {
				// devo pubblicare il campo di riferimento per l'oggetto collegato
				$eti = '';
				if(moduloAttivo('etichetta_ruolo_responsabile')) {
					$eti = mostraDatoOggetto($ref, 3, 'ruolo').": ";
				}
				$out[] = $eti."<a href=\"" . $strAncora . "\">" . $nomeOgg . "</a>";
			}
		}
		if(count($out)>0) {
			echo "<div class=\"".$cls."\">";
			if(moduloAttivo('etichetta_ruolo_responsabile')) {
				foreach((array)$out as $r) {
					echo '<div>'.$r.($istanzaOggetto['ad_interim'] ? ' - ad interim' : '').'</div>';
				}
			} else {
				$eti = (count($out)>1 ? "Responsabili: " : "Responsabile: ");
				echo $eti.implode(', ', $out);
				if($istanzaOggetto['ad_interim']) {
					echo " (".(count($out)>1 ? "responsabili " : "responsabile ")."ad interim)";
				}
			}
			echo "</div>";
		}
		
	}
}

function visualizzaStruttureResponsabile($istanzaOggetto) {
	$docRif = new documento(13);
	$docRiferiti = $docRif->caricaDocumentiCampo('referente', $istanzaOggetto['id']);
	$outputScreen = '';
	if (count($docRiferiti)) {
		$struttureNorm = array();
		$struttureAdInt = array();
		foreach ($docRiferiti as $oggTmp) {
			if($oggTmp['ad_interim']) {
				$struttureAdInt[] = $oggTmp;
			} else {
				$struttureNorm[] = $oggTmp;
			}
		}
		foreach ($struttureNorm as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default];
				$outputScreen .= "<div><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></div>";
			}
		}
		foreach ($struttureAdInt as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default].' - ad interim';
				$outputScreen .= "<div><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></div>";
			}
		}
		echo $outputScreen;
	}
}

function visualizzaTabellaIndicizzazione($istanzaOggetto) {
	global $base_url;
	
	if($istanzaOggetto['tipologia'] == 'avvisi pubblici') {
		echo '';
	} else if($istanzaOggetto['tipologia'] != 'lotto') {
		echo '<a href="'.$base_url.'index.php?id_sezione=637&amp;id_doc='.$istanzaOggetto['id'].'" title="Tabella delle informazioni d\'indicizzazione">Tabella delle informazioni d\'indicizzazione</a><div style="clear:both;height: 20px;"></div>';
	} else {
		echo 'Il presente lotto fa parte della procedura <a href="'.$base_url.'index.php?id_oggetto=11&amp;id_cat='.mostraDatoOggetto($istanzaOggetto['id_record_cig_principale'],11,'id_sezione').'&amp;id_doc='.$istanzaOggetto['id_record_cig_principale'].'">'.mostraDatoOggetto($istanzaOggetto['id_record_cig_principale'],11,'oggetto').'</a><div style="clear:both;height: 20px;"></div>';
	}
}

function tempiProcedimentali($istanzaOggetto, $visTitolo = true) {
	if($istanzaOggetto['monitoraggio_procedimenti'] != '') {
		$array = json_decode($istanzaOggetto['monitoraggio_procedimenti']);
		if(count($array)> 0) {
			$out = '';
			if($visTitolo) {
				$out .= '<h4 class="campoOggetto86">Monitoraggio tempi procedimentali</h4>';
			}
			$out .= '<div class="oggetto76"><div class="table-responsive"><table class="table table-bordered table-hover vistaTabella"><tr><th>Anno</th><th>Numero procedimenti conclusi</th><th>Giorni medi conclusione</th><th>Percentuale procedimenti conclusi</th></tr>';
			foreach((array)$array as $t) {
				$out .= '<tr><td>'.$t->anno.'</td><td>'.$t->numero_procedimenti.'</td><td>'.$t->numero_giorni.'</td><td>'.$t->percentuale_procedimenti.'</td></tr>';
			}
			$out .= '</table></div></div>';
			echo $out;
		}
	}
}
function formazionePersonale($istanzaOggetto) {
	if($istanzaOggetto['formazione_personale'] != '') {
		$array = json_decode($istanzaOggetto['formazione_personale']);
		if(count($array)> 0) {
			$out = '<h4 class="campoOggetto86">Dati sulla formazione</h4><div class="oggetto76"><div class="table-responsive"><table class="table table-bordered table-hover vistaTabella"><tr><th>Descrizione attività</th><th>Dal</th><th>Al</th></tr>';
			foreach((array)$array as $t) {
				$out .= '<tr><td>'.$t->descrizione.'</td><td>'.ricavaDataJTable($t->data_dal).'</td><td>'.ricavaDataJTable($t->data_al).'</td></tr>';
			}
			$out .= '</table></div></div>';
			echo $out;
		}
	}
}

function tabellaErogatoSovvenzioni($istanzaOggetto) {
	global $database, $dati_db, $configurazione, $idEnte;
	
	if($istanzaOggetto['tipologia'] == 'sovvenzione') {
		$totale = 0;
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_sovvenzioni WHERE id_ente=".$idEnte." "
				." AND id_sovvenzione = ".$istanzaOggetto['id']
				." ORDER BY data_compenso_erogato DESC";
		if ( !($result = $database->connessioneConReturn($sql)) ) {}
		$recs = $database->sqlArrayAss($result);
		if(count($recs)) {
			$out = '<h4 class="campoOggetto86">Importi dei vantaggi economici corrisposti</h4><div class="oggetto76"><div class="table-responsive"><table class="table table-bordered table-hover vistaTabella"><tr><th>Importo</th><th>Data</th><th>Anno</th><th style="width:40px;"></th></tr>';
			foreach((array)$recs as $t) {
				$data = '';
				if($t['data'] > 0) {
					$data = date('d/m/Y', $t['data']);
				}
				$out .= '<tr><td>&euro;&nbsp;'.$t['compenso_erogato'].'</td><td>'.$data.'</td><td>'.$t['data_compenso_erogato'].'</td><td style="text-align:center;"><a href="'.$base_url.'index.php?id_oggetto=38&id_cat=0&id_doc='.$t['id'].'" title="dettagli"><span class="fa fa-search"></span></a></td></tr>';
			}
			$out .= '</table></div></div>';
			echo $out;
		}
	}
}

function tabellaErogatoIncarichi($istanzaOggetto) {
	global $database, $dati_db, $configurazione, $idEnte, $entePubblicato, $base_url;
	
	$campoCompenso = 'compenso_erogato';
	if($entePubblicato['importi_numerici']) {
		$campoCompenso .= '_valore';
	}

	if($istanzaOggetto['tipologia'] == 'incarico') {
		$totale = 0;
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_incarichi WHERE id_ente=".$idEnte." "
				." AND id_incarico = ".$istanzaOggetto['id']
				." ORDER BY data_compenso_erogato DESC";
		if ( !($result = $database->connessioneConReturn($sql)) ) {}
		$recs = $database->sqlArrayAss($result);
		if(count($recs)) {
			$out = '<h4 class="campoOggetto86">Compensi erogati</h4><div class="oggetto76"><div class="table-responsive"><table class="table table-bordered table-hover vistaTabella"><tr><th>Importo</th><th>Data</th><th>Anno</th><th style="width:40px;"></th></tr>';
			foreach((array)$recs as $t) {
				$data = '';
				if($t['data_liquidazione'] > 0) {
					$data = date('d/m/Y', $t['data_liquidazione']);
				}
				$out .= '<tr><td>&euro;&nbsp;'.$t[$campoCompenso].'</td><td>'.$data.'</td><td>'.$t['data_compenso_erogato'].'</td><td style="text-align:center;"><a href="'.$base_url.'index.php?id_oggetto=4&id_cat=0&id_doc='.$t['id'].'" title="dettagli"><span class="fa fa-search"></span></a></td></tr>';
			}
			$out .= '</table></div></div>';
			echo $out;
		}
	}
}

function ricavaDataJTable($d, $formato = 'd/m/Y') {
	if($d != '') {
		$d = explode('/Date(', $d);
		$d = $d[1];
		$d = explode(')/', $d);
		$d = $d[0];
		$d = $d/1000;
		return date($formato, $d);
	}
}

function visualizzaOrganoControllo($istanzaOggetto) {
	if(strpos($istanzaOggetto['organo'], 'consiglio comunale') !== false) {
		return true;
	}
	return false;
}

function prendiTipoOccupazioneImmobile() {
	global $tipoEnte, $enteAdmin;
	
	$elements = array();
	if($tipoEnte['immobili_tipologia'] != '') {
		$valoriElements = $tipoEnte['immobili_tipologia'];
	} else {
		$valoriElements = '';
	}
	$elementsTemp = explode(',',$valoriElements);
	$valElements = array();
	$etiElements = array();
	foreach((array)$elementsTemp as $r) {
		$tr = explode('|',$r);
		$valElements[] = $tr[0];
		if($tr[1] != '') {
			$etiElements[] = $tr[1];
		} else {
			$etiElements[] = $tr[0];
		}
	}
	$elements['valori'] = $valElements;
	$elements['etichette'] = $etiElements;
	return $elements;
}

function selectRicercaTipoOccupazione() {
	global $tipoEnte, $enteAdmin;
	
	$elements = prendiTipoOccupazioneImmobile();
	$valElements = implode(',',$elements['valori']);
	$etiElements = implode(',',$elements['etichette']);
	if($valElements != '') {
		$options = '';
		$options .= "<option value=\"qualunque\">qualunque</option>";
		for($i=0; $i<count($elements['valori']); $i++) {
			$val = $elements['valori'][$i];
			$eti = $elements['etichette'][$i];
			if($val != '') {
				$stringa = '';
				if ($val == $_POST['tipo_occupazione_mcrt_18']) {
					$stringa = ' selected="selected" ';
				}
				$options .= "<option value=\"".$val."\"".$stringa.">".$eti."</option>";
			}
		}
		if($options != '') {
			echo '<div class="campoOggetto71">
				<span style="white-space: nowrap;">
					<label for="tipo_occupazione_mcrt_18" class="labelClass">Tipo occupazione</label>
					<select class="stileForm75" id="tipo_occupazione_mcrt_18" name="tipo_occupazione_mcrt_18">
						'.$options.'
					</select>
				</span>
			</div>';
		}
	}
}

function prendiTipoModulistica() {
	global $tipoEnte, $enteAdmin;
	
	$elements = array();
	if($tipoEnte['modulistica_tipologia'] != '') {
		$valoriElements = $tipoEnte['modulistica_tipologia'];
	} else {
		$valoriElements = 'modulo|Modulistica (pubblicazione in Atti generali - Modulistica),dichiarazione sostitutiva|Dichiarazione sostitutiva (pubblicazione in Attivit&agrave; e procedimenti - Dichiarazioni sostitutive e acquisizione d\'ufficio dei dati)';
	}
	$elementsTemp = explode(',',$valoriElements);
	$valElements = array();
	$etiElements = array();
	foreach((array)$elementsTemp as $r) {
		$tr = explode('|',$r);
		$valElements[] = $tr[0];
		if($tr[1] != '') {
			$etiElements[] = $tr[1];
		} else {
			$etiElements[] = $tr[0];
		}
	}
	$elements['valori'] = $valElements;
	$elements['etichette'] = $etiElements;
	return $elements;
}

function prendiTipoRegolamenti() {
	global $tipoEnte, $enteAdmin;

	$elements = array();
	if($tipoEnte['regolamenti_tipologia'] != '') {
		$valoriElements = $tipoEnte['regolamenti_tipologia'];
	} else {
		$valoriElements = 'regolamento|Regolamenti,statuto|Statuti e leggi,codice|Codice disciplinare e codice di condotta,atto amministrativo|Atti amministrativi generali,documento programmazione|Documenti di programmazione';
	}
	$elementsTemp = explode(',',$valoriElements);
	$valElements = array();
	$etiElements = array();
	foreach((array)$elementsTemp as $r) {
		$tr = explode('|',$r);
		$valElements[] = $tr[0];
		if($tr[1] != '') {
			$etiElements[] = $tr[1];
		} else {
			$etiElements[] = $tr[0];
		}
	}
	$elements['valori'] = $valElements;
	$elements['etichette'] = $etiElements;
	return $elements;
}

function prendiTipoCanoneImmobile() {
	global $tipoEnte, $enteAdmin;

	$elements = array();
	if($tipoEnte['canoni_tipologia'] != '') {
		$valoriElements = $tipoEnte['canoni_tipologia'];
	} else {
		$valoriElements = 'Canoni di locazione o di affitto versati,Canoni di locazione o di affitto percepiti';
	}
	$elementsTemp = explode(',',$valoriElements);
	$valElements = array();
	$etiElements = array();
	foreach((array)$elementsTemp as $r) {
		$tr = explode('|',$r);
		$valElements[] = $tr[0];
		if($tr[1] != '') {
			$etiElements[] = $tr[1];
		} else {
			$etiElements[] = $tr[0];
		}
	}
	$elements['valori'] = $valElements;
	$elements['etichette'] = $etiElements;
	return $elements;
}

function prendiPubblicazioneControlliRilievi() {
	global $tipoEnte, $enteAdmin;
	
	$sezioniEscludo = explode(",",$tipoEnte['sezioni_esclusione']);
	$elements = array();
	if($tipoEnte['controlli_rilievi'] != '') {
		$valoriElements = $tipoEnte['controlli_rilievi'];
	} else {
		$valoriElements = 's866s|Organismi indipendenti di valutazione nuclei di valutazione o altri organismi con funzioni analoghe,s774s|Organismi indipendenti... - Attestazione dell\'OIV o di altra struttura analoga nell\'assolvimento degli obblighi di pubblicazione,s779s|Organismi indipendenti... - Documento dell\'OIV di validazione della Relazione sulla Performance,s780s|Organismi indipendenti... - Relazione dell\'OIV sul funzionamento complessivo del Sistema di valutazione trasparenza e integrit&agrave; dei controlli interni,s890s|Organismi indipendenti... - Altri atti degli organismi indipendenti di valutazione Nuclei di valutazione o altri organismi con funzioni analoghe,s867s|Organi di revisione amministrativa e contabile,s868s|Corte dei conti';
	}
	$elementsTemp = explode(',',$valoriElements);
	$valElements = array();
	$etiElements = array();
	foreach((array)$elementsTemp as $r) {
		$tr = explode('|',$r);
		//verifica sulla sezione eventualmente esclusa
		$st = str_replace('s','',$tr[0]);
		if(!in_array($st,$sezioniEscludo)) {
			$valElements[] = $tr[0];
			if($tr[1] != '') {
				$etiElements[] = $tr[1];
			} else {
				$etiElements[] = $tr[0];
			}
		}
	}
	$elements['valori'] = $valElements;
	$elements['etichette'] = $etiElements;
	return $elements;
}

function prendiTipoIncarico($tipoSelezionato = '') {
	global $tipoEnte, $enteAdmin;
	
	$ret = array();
	$valoriRet = "incarichi dipendenti interni|Incarichi retribuiti e non retribuiti dei propri dipendenti,incarichi dipendenti esterni|Incarichi retribuiti e non retribuiti affidati a soggetti esterni";
	
	// prelevo eventuali tipi aggiuntivi
	if ($enteAdmin['incarichi_tipologie'] != '') {
		$valoriRet .= ($valoriRet != '' ? ',':'').htmlentities($enteAdmin['incarichi_tipologie']);
	}
	if($tipoSelezionato != '') {
		if(strpos($valoriRet, $tipoSelezionato) === false) {
			$valoriRet .= ",".$tipoSelezionato;
		}
	}
	$retTemp = explode(',',$valoriRet);
	$valRet = array();
	$etiRet = array();
	foreach((array)$retTemp as $r) {
		$tr = explode('|',$r);
		$valRet[] = $tr[0];
		if($tr[1] != '') {
			$etiRet[] = $tr[1];
		} else {
			$etiRet[] = $tr[0];
		}
	}
	$ret['valori'] = $valRet;
	$ret['etichette'] = $etiRet;
	return $ret;
}

function prendiTipoConsulenza($tipoSelezionato = '') {
    global $tipoEnte, $enteAdmin;
    
    $ret = array();
    $valoriRet = "";
    
    // prelevo eventuali tipi aggiuntivi
    if ($enteAdmin['incarichi_tipologie_consulenza'] != '') {
        $valoriRet .= ($valoriRet != '' ? ',':'').htmlentities($enteAdmin['incarichi_tipologie_consulenza']);
    }
    if($tipoSelezionato != '') {
        if(strpos($valoriRet, $tipoSelezionato) === false) {
            $valoriRet .= ",".$tipoSelezionato;
        }
    }
    $retTemp = explode(',',$valoriRet);
    $valRet = array();
    $etiRet = array();
    foreach((array)$retTemp as $r) {
        $tr = explode('|',$r);
        $valRet[] = $tr[0];
        if($tr[1] != '') {
            $etiRet[] = $tr[1];
        } else {
            $etiRet[] = $tr[0];
        }
    }
    $ret['valori'] = $valRet;
    $ret['etichette'] = $etiRet;
    return $ret;
}

function prendiRuoliPersonale($ruoloSelezionato = '') {
	global $tipoEnte, $enteAdmin;
	
	$ruoli = array();
	if($tipoEnte['personale_ruolo'] == '') {
		$valoriRuoli = "Dipendente|Dipendente,P.O.|P.O.,Funzionario|Funzionario,Dirigente|Dirigente,Incaricato politico|Incaricato politico,Segretario generale|Segretario generale,Commissario|Commissario,Sub Commissario|Sub Commissario";
	} else {
		$valoriRuoli = $tipoEnte['personale_ruolo'];
	}
	// prelevo eventuali ruoli aggiuntivi
	if ($enteAdmin['personale_ruoli'] != '') {
		$valoriRuoli .= ",".htmlentities($enteAdmin['personale_ruoli']);
	}
	if($ruoloSelezionato != '') {
		if(strpos($valoriRuoli, $ruoloSelezionato) === false) {
			$valoriRuoli .= ",".$ruoloSelezionato;
		}
	}
	$ruoliTemp = explode(',',$valoriRuoli);
	$valRuoli = array();
	$etiRuoli = array();
	foreach((array)$ruoliTemp as $r) {
		$tr = explode('|',$r);
		$valRuoli[] = $tr[0];
		if($tr[1] != '') {
			$etiRuoli[] = $tr[1];
		} else {
			$etiRuoli[] = $tr[0];
		}
	}
	$ruoli['valori'] = $valRuoli;
	$ruoli['etichette'] = $etiRuoli;
	return $ruoli;
}

function prendiTipiProvvedimento($selezionato = '') {
	global $tipoEnte, $enteAdmin;

	$array = array();
	if($tipoEnte['provvedimenti_tipi'] == '') {
		$valori = "provvedimento dirigenziale|provvedimento dirigenziale,provvedimento organo politico|provvedimento organo indirizzo-politico";
	} else {
		$valori = $tipoEnte['provvedimenti_tipi'];
	}
	// prelevo eventuali tipi aggiuntivi
	if ($enteAdmin['provvedimenti_tipi'] != '') {
		$valori .= ",".htmlentities($enteAdmin['provvedimenti_tipi']);
	}
	if($selezionato != '') {
		if(strpos($valori, $selezionato) === false) {
			$valori .= ",".$selezionato;
		}
	}
	$arrayTemp = explode(',',$valori);
	$val = array();
	$eti = array();
	foreach((array)$arrayTemp as $r) {
		$tr = explode('|',$r);
		$val[] = $tr[0];
		if($tr[1] != '') {
			$eti[] = $tr[1];
		} else {
			$eti[] = $tr[0];
		}
	}
	$array['valori'] = $val;
	$array['etichette'] = $eti;
	return $array;
}

function prendiQualifichePersonale($inAdmin = true) {
	global $tipoEnte, $enteAdmin, $entePubblicato;
	
	$qualifiche = array();
	if($tipoEnte['personale_qualifica'] == '') {
		$valoriQualifiche = "";
	} else {
		$valoriQualifiche = $tipoEnte['personale_qualifica'];
	} 
	// prelevo eventuali qualifiche aggiuntive
	if($inAdmin) {
		$pq = $enteAdmin['personale_qualifiche'];
	} else {
		$pq = $entePubblicato['personale_qualifiche'];
	}
	if ($pq != '') {
		if($valoriQualifiche != '') {
			$valoriQualifiche .= ",";
		}
		$valoriQualifiche .= htmlentities($pq);
	}
	
	$qualificheTemp = explode(',',$valoriQualifiche);
	$valQualifiche = array();
	$etiQualifiche = array();
	foreach((array)$qualificheTemp as $r) {
		$tr = explode('|',$r);
		$valQualifiche[] = $tr[0];
		if($tr[1] != '') {
			$etiQualifiche[] = $tr[1];
		} else {
			$etiQualifiche[] = $tr[0];
		}
	}
	$qualifiche['valori'] = $valQualifiche;
	$qualifiche['etichette'] = $etiQualifiche;
	return $qualifiche;
}


function prendiPaginePubblicazionePersonale() {
	global $tipoEnte, $enteAdmin;
	
	$ruoli = array();
	if($tipoEnte['personale_pubblica_in'] == '') {
		$pagine = "perdir|Personale - Dirigente,perpo|Personale - Posizioni organizzative";
	} else {
		$pagine = $tipoEnte['personale_pubblica_in'];
	} 
	
	$pagineTemp = explode(',',$pagine);
	$valPagine = array();
	$etiPagine = array();
	foreach((array)$pagineTemp as $r) {
		$tr = explode('|',$r);
		$valPagine[] = $tr[0];
		if($tr[1] != '') {
			$etiPagine[] = $tr[1];
		} else {
			$etiPagine[] = $tr[0];
		}
	}
	$pag['valori'] = $valPagine;
	$pag['etichette'] = $etiPagine;
	return $pag;
}

function prendiPaginePubblicazioneBandiAtti() {
	global $tipoEnte, $enteAdmin;

	$ruoli = array();
	if($enteAdmin['bandiatti_pubblica_in'] == '') {
		$pagine = "provvescl|Provvedimento di esclusione dalla procedura di affidamento e di ammissione all'esito delle valutazioni,compcomm|Composizione della commissione giudicatrice,contrattibando|Contratti (Testo integrale di tutti i contratti di acquisto di beni e di servizi di importo unitario stimato superiore a 1  milione di euro in esecuzione del programma biennale e suoi aggiornamenti),resocgesfin|Resoconti della gestione finanziaria dei contratti al termine della loro esecuzione";
	} else {
		$pagine = $enteAdmin['bandiatti_pubblica_in'];
	}

	$pagineTemp = explode(',',$pagine);
	$valPagine = array();
	$etiPagine = array();
	foreach((array)$pagineTemp as $r) {
		$tr = explode('|',$r);
		$valPagine[] = $tr[0];
		if($tr[1] != '') {
			$etiPagine[] = $tr[1];
		} else {
			$etiPagine[] = $tr[0];
		}
	}
	$pag['valori'] = $valPagine;
	$pag['etichette'] = $etiPagine;
	return $pag;
}

function prendiTipologiaNormativa() {
	global $tipoEnte, $enteAdmin;
	
	$return = array();
	if($tipoEnte['normativa_tipologia'] == '') {
		$valori = "Decreto dirigenziale,Decreto interministeriale,Decreto legge,Decreto legislativo,Decreto ministeriale,Decreto Presidente Consiglio dei Ministri,Decreto Presidente della Repubblica,Legge,Regolamento CEE,Altro";
	} else {
		$valori = $tipoEnte['normativa_tipologia'];
	} 
	// prelevo eventuali qualifiche aggiuntive
	if ($enteAdmin['normativa_tipologia'] != '') {
		if($valori != '') {
			$valori .= ",";
		}
		$valori .= htmlentities($enteAdmin['normativa_tipologia']);
	}
	
	$returnTemp = explode(',',$valori);
	$val = array();
	$eti = array();
	foreach((array)$returnTemp as $r) {
		$tr = explode('|',$r);
		$val[] = $tr[0];
		if($tr[1] != '') {
			$eti[] = $tr[1];
		} else {
			$eti[] = $tr[0];
		}
	}
	$return['valori'] = $val;
	$return['etichette'] = $eti;
	return $return;
}

function prendiArgomentoNormativa() {
	global $tipoEnte, $enteAdmin;

	$return = array();
	
	//valori fissi
	$return['valori'] = array('Organizzazione dell\'Ente','Sovvenzioni e contributi','Altro');
	$return['etichette'] = array('Organizzazione dell\'Ente (pubblica in Riferimenti normativi su organizzazione e attivit&agrave;)','Sovvenzioni e contributi (pubblica in Criteri e modalit&agrave;)','Altro');
	
	$valori = array();
	if ($enteAdmin['normativa_argomento'] != '') {
		$valori = explode(',', $enteAdmin['normativa_argomento']);
	}

	foreach((array)$valori as $r) {
		$tr = explode('|',$r);
		$return['valori'][] = $tr[0];
		if($tr[1] != '') {
			$return['etichette'][] = $tr[1];
		} else {
			$return['etichette'][] = $tr[0];
		}
	}
	return $return;
}

function visualizzaImporto($importo, $eti = '', $idStile = 0) {
	if(trim($importo) != '' and is_numeric($importo)) {
		echo '<div class="campoOggetto"'.$idStile.'>'.$eti;
		echo formattaImporto($importo);
		echo '</div>';
	}
}
function formattaImporto($importo) {
	if(is_numeric($importo)) {
		echo '&euro; '. number_format($importo, 2, ',', '.');
	}
}

function listaUfficiPersonale($istanzaOggetto) {
	global $base_url;
	
	$uffici = array();
	if (trim($istanzaOggetto['uffici']) != '' and $istanzaOggetto['uffici'] != 0) {
		$idOggMulti = explode(',', $istanzaOggetto['uffici']);
		foreach ($idOggMulti as $idOggTmp) {
			$istOgg = mostraDatoOggetto($idOggTmp, 13, '*');
			if ($istOgg['id']) {
				$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $istOgg['id_sezione'] . "&amp;id_doc=" . $idOggTmp;
				$uffici[$istOgg['id']] = "<li><a href=\"" . $strAncora . "\">" . $istOgg['nome_ufficio'] . "</a></li>";
			}
		}
	}
	$docRif = new documento(13);
	$docRiferiti = $docRif->caricaDocumentiCampo('referente', $istanzaOggetto['id']);
	foreach ($docRiferiti as $oggTmp) {
		if ($oggTmp['id']) {
			$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
			$uffici[$oggTmp['id']] = "<li><a href=\"" . $strAncora . "\">" . $oggTmp['nome_ufficio'] . "</a></li>";
		}
	}

	unset ($docRiferiti);
	unset ($docRif);
	
	$out = '';
	if(count($uffici) > 0) {
		$out .= '<ul>';
		foreach((array)$uffici as $u) {
			$out .= $u;
		}
		$out .= '</ul>';
	}
	echo $out;
}

function calcoloCompensoIncarichi(&$istanzaOggetto) {
	global $idEnte,$entePubblicato;
	
	$campoCompenso = 'compenso';
	if($entePubblicato['importi_numerici']) {
		$campoCompenso .= '_valore';
	}
	
	$c = trim($istanzaOggetto[$campoCompenso]);
	if($c != '') {
		if($entePubblicato['importi_numerici']) {
			$istanzaOggetto[$campoCompenso] = '&euro;&nbsp;'.number_format($c, 2, ',', '.');
		} else {
			$istanzaOggetto[$campoCompenso] = $c;
		}
	}
	return true;
}

function compensoIncarico($istanzaOggetto, $campoCompenso, $eti = '') {
	global $idEnte,$entePubblicato;
	
	if($entePubblicato['importi_numerici']) {
		$campoCompenso .= '_valore';
	}
	
	$out = '';
	$c = trim($istanzaOggetto[$campoCompenso]);
	if($c != '') {
		if($entePubblicato['importi_numerici']) {
			$out .= '<div>'.$eti.'&nbsp;&euro;&nbsp;'.number_format($c, 2, ',', '.').'</div>';
		} else {
			if(strpos($c, "&euro;") !== false) {
				$out .= '<div>'.$eti.'&nbsp;'.$c.'</div>';
			} else {
				$out .= '<div>'.$eti.'&nbsp;&euro;&nbsp;'.$c.'</div>';
			}
		}
	}
	echo $out;
}
function compensoSovvenzione($compenso, $eti = '') {
	global $idEnte;
	
	$out = '';
	$c = trim($compenso);
	if($c != '') {
		if($idEnte == 182) {
			//MIT
			//$out .= '<div>'.$eti.number_format($c, 2, ',', '.').'&nbsp;&euro;'.'</div>';	//questa cosa va verificata bene!
			$out .= '<div>'.$eti.'&nbsp;&euro;&nbsp;'.$c.'</div>';
		} else {
			$out .= '<div>'.$eti.'&nbsp;&euro;&nbsp;'.$c.'</div>';
		}
	}
	echo $out;
}

function linkOggettoBando($istanzaOggetto) {
	global $base_url;
	
	$oggetto = $istanzaOggetto['oggetto'];
	if($oggetto == '' and $istanzaOggetto['bando_collegato'] > 0) {
		$bandoPrincipale = mostraDatoOggetto($istanzaOggetto['bando_collegato'],11,'*');
		if($bandoPrincipale['oggetto'] != '') {
			$oggetto = $bandoPrincipale['oggetto'];
		}
	}
	if($oggetto == '') {
		$oggetto = '(Nessun oggetto)';
	}
	echo "<a href=\"".$base_url."index.php?id_oggetto=11&amp;id_cat=".$istanzaOggetto['id_sezione']."&amp;id_doc=".$istanzaOggetto['id']."\">".$oggetto."</a>";
}

function prendiListaAllegati($idAllegatoDinamico, $escludiOmissis = false, $includiTemporanei = false) {
	global $database, $dati_db, $configurazione;
	
	$temporaneo = '0';
	if($includiTemporanei) {
		$temporaneo = '1';
	}
	
	$condizione = '';
	if($escludiOmissis) {
		$condizione = ' AND omissis = 0 ';
	}
	$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_allegati WHERE __id_allegato_istanza = '".$idAllegatoDinamico."' AND __temporaneo = '".$temporaneo."' ".$condizione." ORDER BY ordine,nome";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		die('Errore durante il recupero di tutti gli allegati '.$sql);
	}
	return $database->sqlArrayAss($result);
}
function prendiListaAllegatiBackup($idAllegatoDinamico, $idOgg, $idRiferimento, $escludiOmissis = false, $includiTemporanei = false) {
	global $database, $dati_db, $configurazione;

	$temporaneo = '0';
	if($includiTemporanei) {
		$temporaneo = '1';
	}

	$condizione = '';
	if($escludiOmissis) {
		$condizione = ' AND omissis = 0 ';
	}
	$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_allegati_backup WHERE id_oggetto = ".$idOgg." AND id_documento = ".$idRiferimento." AND __id_allegato_istanza = '".$idAllegatoDinamico."' AND __temporaneo = '".$temporaneo."' ".$condizione." ORDER BY ordine,nome";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		die('Errore durante il recupero di tutti gli allegati '.$sql);
	}
	return $database->sqlArrayAss($result);
}

function eliminaAllegatiTemporanei() {
	global $database, $dati_db, $configurazione;
	
	$sql = "SELECT GROUP_CONCAT(id) AS id_allegati FROM ".$dati_db['prefisso']."oggetto_allegati WHERE __temporaneo = 1 AND data_creazione + 43200 < ".mktime();
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		die('Errore durante il recupero di tutti gli allegati '.$sql);
	}
	$ids = $database->sqlArray($result, MYSQL_ASSOC);
	if($ids['id_allegati'] != '') {
		require_once('classi/admin_oggetti.php');
		$oggAllegati = new oggettiAdmin(57);
		$oggAllegati->cancellaOggetti($ids['id_allegati']);
	}
}

function visualizzaAllegatiDinamici($ist, $opzioni = array()) {
	global $uploadPath,$base_url,$configurazione;
	
	if(count($configurazione['opzioni_visualizza_allegati'])) {
		foreach((array)$configurazione['opzioni_visualizza_allegati'] as $k => $v) {
			$opzioni[$k] = $v;
		}
	}
	
	if($opzioni['classe_titolo'] == '') {
		$opzioni['classe_titolo'] = 'campoOggetto86';
	}
	if($opzioni['classe_allegato'] == '') {
		$opzioni['classe_allegato'] = 'campoOggetto48';
	}
	
	$temp = '';
	if($opzioni['allegati_backup']) {
		$lista = prendiListaAllegatiBackup($ist['__id_allegato_istanza'], $opzioni['id_oggetto_backup'], $opzioni['id_backup']);
		$temp = 'temp/';
	} else {
		$lista = prendiListaAllegati($ist['__id_allegato_istanza']);
	}
	if(count($lista)>0) {
		//prendo id_oggetto del primo allegato (saranno tutti dello stesso oggetto) per verificare se ordinarli per categoria
		$ido = $lista[0]['id_oggetto'];
		$listaPerCategoria[0] = $lista;
		if(count($configurazione['categoriaAllegato'][$ido])) {
			$listaPerCategoria = array();
			foreach((array)$lista as $istanzaOggetto) {
				$listaPerCategoria[$istanzaOggetto['categoriaAllegato']][] = $istanzaOggetto;
			}
			ksort($listaPerCategoria);
		}
		$out = '';
		foreach((array)$listaPerCategoria as $cat => $lista) {
			if($cat != '' and is_string($cat)) {
				$out .= '<div class="categoriaAllegato">'.$cat.'</div>';
			}
			foreach((array)$lista as $istanzaOggetto) {
				if((!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy')))) {
					$posPunto = strrpos($istanzaOggetto['file_allegato'], ".");
					$estFile = strtolower(substr($istanzaOggetto['file_allegato'], ($posPunto +1)));
					if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
						$estFile = "generica";
					}
					$grandezza = @filesize($uploadPath . "oggetto_allegati/" . $temp. $istanzaOggetto['file_allegato']);
					
					if (strpos($istanzaOggetto['file_allegato'], "O__O")) {
						$valoreLabel = (substr($istanzaOggetto['file_allegato'], strpos($istanzaOggetto['file_allegato'], "O__O") + 4));
					} else {
						$valoreLabel = ($istanzaOggetto['file_allegato']);
					}
					$um = $istanzaOggetto['data_creazione'];
					if($istanzaOggetto['ultima_modifica']>0) {
					    $um = $istanzaOggetto['ultima_modifica'];
					}
					if($um > 0) {
					    $um = date('d/m/Y',$um).' - ';
					} else {
					    $um = '';
					}
					if($configurazione['allegato_solo_nome'] and $istanzaOggetto['nome'] != '') {
					    $out .= '<div class="'.$opzioni['classe_allegato'].'"><a href="'.$base_url.'moduli/downloadFile.php?file=oggetto_allegati/'.$temp.urlencode($istanzaOggetto['file_allegato']).'">'.$istanzaOggetto['nome'].'</a> ('.$um.round($grandezza/1000).' kb - '.$estFile.') <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
					} else {
    					$out .= '<div class="'.$opzioni['classe_allegato'].'">'.$istanzaOggetto['nome'].': <a href="'.$base_url.'moduli/downloadFile.php?file=oggetto_allegati/'.$temp.urlencode($istanzaOggetto['file_allegato']).'">'.$valoreLabel.'</a> ('.$um.round($grandezza/1000).' kb - '.$estFile.') <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
					}
				}
			}
		}
		if($out != '') {
		    echo '<h4 class="'.$opzioni['classe_titolo'].'">Allegati</h4>';
    		echo $out;
		}
	}
}

function strutturaProponente($istanzaOggetto){
	$o = $istanzaOggetto['denominazione_aggiudicatrice'];
	if($istanzaOggetto['dati_aggiudicatrice'] != '') {
		$o = ($o == '' ? $istanzaOggetto['dati_aggiudicatrice'] : $o.' - '.$istanzaOggetto['dati_aggiudicatrice']);
	}
	if($o != '') {
		echo '<div>Struttura proponente: '.$o.'</div>';
	}
}

function human_filesize($bytes, $decimals = 2) {
	$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' .@$size[$factor];
}

function visualizzaDataAggiornamento($istanzaOggetto) {
	global $inAmministrazione;
	if(!$inAmministrazione) {
		echo '<div id="dataAggiornamento">Contenuto inserito il '.visualizzaData($istanzaOggetto['data_creazione'],'d-m-Y').' aggiornato al '.visualizzaData($istanzaOggetto['ultima_modifica'],'d-m-Y').'</div>';
	}
}

function selectRicercaTipologiaOgg11($campo, $etichetta = 'Tipologia', $classCampo = 'campoOggetto71', $classForm = 'stileForm75') {
	global $database, $dati_db, $configurazione, $idEnte, $idSezione;
	
	$visualizzaSelect = false;
	$istanze = array();
	$istanze[] = array(
			'val' => 'qualunque',
			'eti' => 'qualunque'
	);
	$istanze[] = array(
			'val' => 'bandi ed inviti',
			'eti' => 'bandi di gara'
	);
	
	if($_POST[$campo] != '') {
		$visualizzaSelect = true;
	}
	
	if($configurazione['visualizza_select_ricerca_tipologia_bando']) {
		//forzo la visualizzazione della tipologia
		$visualizzaSelect = true;
		$istanze[] = array(
				'val' => 'avvisi pubblici',
				'eti' => 'avvisi'
		);
		$istanze[] = array(
				'val' => 'esiti',
				'eti' => 'esiti di gara'
		);
		$istanze[] = array(
				'val' => 'affidamenti',
				'eti' => 'esiti/affidamenti'
		);
		$istanze[] = array(
				'val' => 'determina_32',
				'eti' => 'delibera a contrarre o atto equivalente'
		);
		$istanze[] = array(
		    'val' => 'delibere e determine a contrarre',
		    'eti' => 'determina art. 57 comma 6 dlgs. 163/2006'
		);
	}
	
	//evito visualizzazione sulla pagina dei pagamenti
	if($idSezione == 859) {
		$visualizzaSelect = false;
	}
	
	if($visualizzaSelect) {
		foreach ((array)$istanze as $istanza) {
			$stringa = '';
			if ($istanza['val'] == $_POST[$campo]) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$istanza['val']."\"".$stringa." title=\"".$istanza['eti']."\">".$istanza['eti']."</option>";
		}
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="'.$campo.'" class="labelClass">'.$etichetta.' </label>
				<select class="'.$classForm.'" id="'.$campo.'" name="'.$campo.'">
					'.$options.'
				</select>
			</span>
		</div>';
	}
	
	if($configurazione['visualizza_select_ricerca_contratto_bando']) {
		$options = '';
		$istanzeContratto = array();
		$istanzeContratto[] = array(
				'val' => 'qualunque',
				'eti' => 'qualunque'
		);
		$istanzeContratto[] = array(
				'val' => 'lavori',
				'eti' => 'lavori'
		);
		$istanzeContratto[] = array(
				'val' => 'servizi',
				'eti' => 'servizi'
		);
		$istanzeContratto[] = array(
				'val' => 'forniture',
				'eti' => 'forniture'
		);
		foreach ((array)$istanzeContratto as $istanzaContratto) {
			$stringa = '';
			if ($istanzaContratto['val'] == $_POST['contratto_mcrt_21']) {
				$stringa = ' selected="selected" ';
			}
			$options .= "<option value=\"".$istanzaContratto['val']."\"".$stringa." title=\"".$istanzaContratto['eti']."\">".$istanzaContratto['eti']."</option>";
		}
		echo '<div class="'.$classCampo.'">
			<span style="white-space: nowrap;">
				<label for="contratto_mcrt_21" class="labelClass">Contratto </label>
				<select class="'.$classForm.'" id="contratto_mcrt_21" name="contratto_mcrt_21">
					'.$options.'
				</select>
			</span>
		</div>';
	}
}

function ricercaBandiHidden() {
	global $configurazione,$entePubblicato,$database,$dati_db;
	include('codicepers/ricerca_bandi_hidden.php');
}
function ricercaConcorsiHidden() {
	global $configurazione,$entePubblicato,$database,$dati_db;
	include('codicepers/ricerca_concorsi_hidden.php');
}
function ricercaProvvedimentiHidden() {
    global $configurazione,$entePubblicato,$database,$dati_db;
    include('codicepers/ricerca_provvedimenti_hidden.php');
}

function ordinamentoImporto($campoOrdine, $idOggetto) {
	global $idEnte;
	if($campoOrdine == 'compenso_erogato' and $idOggetto == 38 and ($idEnte == 1 or $idEnte == 182)) {
		return true;
	} else if($campoOrdine == 'compenso_erogato' and $idOggetto == 4 and ($idEnte == 1 or $idEnte == 182)) {
		return true;
	} else {
		return false;
	}
}

//Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatori (del bando in lettura)		
function attiDelleAmministrazioni($id) {
	global $base_url;
	
	$out = '';
	$docRif = new documento(60);
	$docRiferiti = $docRif->caricaDocumentiCampo('id_bando', $id);
	if(count($docRiferiti)) {
		$out .= '<div><h4 class="campoOggetto86">Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatori</h4><ul>';
		foreach((array)$docRiferiti as $ist) {
			$strAncora = $base_url . "index.php?id_oggetto=60&amp;id_cat=" . $ist['id_sezione'] . "&amp;id_doc=" . $ist['id'];
			$valoreLabel = $ist[$docRif->campo_default];
			$out .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
		}
		$out .= '</ul></div>';
	}
	echo $out;
}
function selectRicercaStrutturaProvvedimenti($campo) {
	global $configurazione;
	if($configurazione['selectRicercaStrutturaProvvedimenti']) {
		selectRicercaStruttura($campo);
	}
}
function selectRicercaResponsabileProvvedimenti($campo) {
	global $configurazione;
	if($configurazione['selectRicercaResponsabileProvvedimenti']) {
		selectRicercaResponsabile($campo);
	}
}
function visualizzaDataFineIncarico($istanzaOggetto) {
	if($istanzaOggetto['fine_incarico']>0) {
		echo date('d-m-Y',$istanzaOggetto['fine_incarico']);
	} else if($istanzaOggetto['fine_incarico_non_disponibile'] and $istanzaOggetto['fine_incarico_non_disponibile_text'] != '') {
		echo $istanzaOggetto['fine_incarico_non_disponibile_text'];
	}
}
function dataSovvenzione($istanzaOggetto) {
	if($istanzaOggetto['data']>0) {
		$eti = 'Data atto di concessione';
		if($istanzaOggetto['tipologia'] == 'liquidazione') {
			$eti = 'Data';
		}
		echo '<div>'.$eti.': '.date('d-m-Y', $istanzaOggetto['data']).'</div>';
	}
}

function visualizzaDateIncarico($istanzaOggetto) {
	$out = '';
	if($istanzaOggetto['inizio_incarico']>0) {
		$out .= '<div>Inizio: '.date('d-m-Y', $istanzaOggetto['inizio_incarico']).'</div>';
	}
	if($istanzaOggetto['fine_incarico']>0) {
		$out .= '<div>Fine: '.date('d-m-Y',$istanzaOggetto['fine_incarico']).'</div>';
	} else if($istanzaOggetto['fine_incarico_non_disponibile'] and $istanzaOggetto['fine_incarico_non_disponibile_text'] != '') {
		$out .= '<div>Fine: '.$istanzaOggetto['fine_incarico_non_disponibile_text'].'</div>';
	}
	echo $out;
}
function campoOrganoPolAmm($organo) {
	$o = traduciOrgani($istanzaOggetto['organo']);
	if($o != '') {
		echo "<div>Organo politico-amministrativo: ".$o."</div>";
	}
}
function getTipologieEsito() {
	global $configurazione;
	if(isset($configurazione['tipologie_esito'])) {
		$ret = array();
		$ar = explode(',',$configurazione['tipologie_esito']);
		foreach((array)$ar as $v) {
			if($v != '') {
				$ret[$v] = $v;
			}
		}
		return $ret;
	} else {
		return array();
	}
}
function obbligoCampoForm($campo, $regole = array(), $obbligo = true) {
	
	$campoJs = $campo;
	if($regole['campoJs']) {
		$campoJs = $regole['campoJs'];
	}
	if(obbligo) {
		?>
		jQuery( "#<?php echo $campoJs; ?>" ).rules( "add", {
			required: true,
			messages: {
				required: "Campo obbligatorio"
			}
		});
		obbligo = '<span class="obbliCampo intTooltip campo_<?php echo $campo; ?>"><a data-placement="top" data-rel="tooltip" data-original-title="Campo obbligatorio" rel="tooltip"><span class="icon-ok-circle"></span></a></span>';
		jQuery( ".cont_box_<?php echo $campo; ?> > label" ).prepend(obbligo);
		<?
	} else {
		//rimuovo obbligo
		?>
		jQuery( "#<?php echo $campoJs; ?>" ).rules( "remove" );
		jQuery( ".cont_box_<?php echo $campo; ?> > label .campo_<?php echo $campo; ?>" ).remove();
		<?
	}
}
function getAnniLiquidazioni() {
	global $database, $dati_db, $configurazione, $idEnte;
	
	if($idEnte == '170' or $idEnte == '41') {
		$annoStart = 2005;
	} else {
		$annoStart = 2013;
	}
	$valoriAnni = '';
	$annoEnd = date('Y');
	for($a = $annoEnd; $a >= $annoStart; $a--) {
		if($valoriAnni != '') {
			$valoriAnni .= ','.$a;
		} else {
			$valoriAnni .= $a;
		}
	}
	return $valoriAnni;
	
}
function canoniAttiviImmobile($istanzaOggetto) {
	global $base_url;
	
	$out = '';
	$outArc = '';
	$docRif = new documento(56);
	$docRiferiti = $docRif->caricaDocumentiCampo('id_immobile', $istanzaOggetto['id']);
	if(count($docRiferiti)) {
		$oggi = mktime(2,0,0,date("m"),date("d"),date("Y"));
		foreach((array)$docRiferiti as $ist) {
			$date = '';
			if($ist['data_inizio']>0) {
				$date .= 'Data inizio: '.date('d/m/Y', $ist['data_inizio']);
			}
			if($ist['data_fine']>0) {
				if($date != '') {
					$date .= ' - ';
				}
				$date .= 'Data fine: '.date('d/m/Y', $ist['data_fine']);
			}
			if($date != '') {
				$date = '<div>'.$date.'</div>';
			}
			if($oggi>=$ist['data_inizio'] and $oggi<= $ist['data_fine']) {
				$strAncora = $base_url . "index.php?id_oggetto=56&amp;id_cat=" . $ist['id_sezione'] . "&amp;id_doc=" . $ist['id'];
				$valoreLabel = $ist[$docRif->campo_default];
				$out .= "<li><div><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></div>
					<div>Importo: &euro; ".number_format($ist['importo'], 2, ',', '.')."</div>
					".$date."
					</li>";
			} else if($oggi>=$ist['data_inizio'] and $oggi> $ist['data_fine']) {
				$strAncora = $base_url . "index.php?id_oggetto=56&amp;id_cat=" . $ist['id_sezione'] . "&amp;id_doc=" . $ist['id'];
				$valoreLabel = $ist[$docRif->campo_default];
				$outArc .= "<li><div><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></div>
					<div>Importo: &euro; ".number_format($ist['importo'], 2, ',', '.')."</div>
					".$date."
					</li>";
			}
		}
	}
	if($out != '') {
		echo '<div><h4 class="campoOggetto86">Canoni di locazione</h4><ul>'.$out.'</ul></div>';
	}
	if($outArc != '') {
		echo '<div><h4 class="campoOggetto86">Canoni di locazione archiviati</h4><ul>'.$outArc.'</ul></div>';
	}
	
}
function titoloColonnaUfficio() {
	global $configurazione,$idSezione;
	switch ($idSezione) {
		case 68:
			echo $configurazione['titoloColonnaUfficio'];
		break;
		default:
			echo 'Referente per';
		break;
	}
}
function linkGoogleMaps($istanzaOggetto) {
	if($istanzaOggetto['pres_sede']=='si' AND $istanzaOggetto['dett_indirizzo']=='' AND $istanzaOggetto['sede']!='') {
		
		$propMap = explode("|", $istanzaOggetto['sede']);
		$temp = explode("_", $propMap[0]);
		$numPunti = $temp[0];
		$zoommappa = $temp[2];
		
		if($numPunti == '' or $numPunti == 0) {
			$numPunti = 1;
		}
		if($zoommappa == '') {
			$zoommappa = "5";
		}
		
		unset($temp);
		$punti = $propMap[1];
		unset($propMap);
		
		$punti = explode("{",$punti);
		foreach ($punti as $punto) {
			$variabili = explode("}", $punto);
			foreach ($variabili as $variabile) {
				$varTemp = explode("=",$variabile);
				if($varTemp[0] == 'htmlEditor') {
					$proprietaOggetto['prop_'.$varTemp[0]] = (substr($variabile, 11));
				} else {
					$proprietaOggetto['prop_'.$varTemp[0]] = $varTemp[1];
				}
			}
			$indirizzo = $proprietaOggetto['prop_indirizzo'];
			$titolomarker = $proprietaOggetto['prop_titolomarker'];
			$lat = $proprietaOggetto['prop_lat'];
			$lng = $proprietaOggetto['prop_lng'];
		}
		
		if($lat == '' or $lng == '') {
			echo '<a href="http://maps.google.com/maps?z='.$zoommappa.'&q='.urlencode($indirizzo).'" title="Apri indirizzo su Google Maps" target="_blank">Apri indirizzo su Google Maps</a>';
		} else {
			echo '<a href="http://maps.google.com/maps?z='.$zoommappa.'&q='.$lat.','.$lng.'" title="Apri indirizzo su Google Maps" target="_blank">Apri indirizzo su Google Maps</a>';
		}
	}
}
function visualizzaDataAttivazioneBandi($istanzaOggetto, $class='campoOggetto114') {
	$eti = 'Data di pubblicazione';
	if(moduloAttivo('bandigara')) {
		switch($istanzaOggetto['tipologia']) {
			case 'somme liquidate':
				$eti = 'Data liquidazione';
			break;
		}
	}
	echo '<div class="'.$class.'">'.$eti.': <strong>'.date('d-m-Y',$istanzaOggetto['data_attivazione']).'</strong></div>';
}


function scaricaXlsAnac($istanzaOggetto) {
	if($istanzaOggetto['__tipoxml'] != 'indice') {
		echo '<a href="'.$base_url.'downloadXlsAnac.php?xml='.urlencode($istanzaOggetto['url']).'">Scarica XLS</a>
			<br />
			<a href="'.$base_url.'index.php?id_sezione=923&amp;xml='.urlencode($istanzaOggetto['url']).'">Visualizza tabella</a>';
	}
}
function getCodComuni() {
	global $database, $dati_db, $configurazione;
	
	$sql = "SELECT * FROM ".$dati_db['prefisso']."etrasp_comuni ORDER BY comune";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		return '[]';
	}
	$istanze = $database->sqlArrayAss($result);
	$arr = array();
	foreach ((array)$istanze as $ist) {
		$arr[] = '{ value: "'.$ist[codice].'", label: "'.$ist[comune].'"}';
	}
	return '['.implode(',',$arr).']';
}

function visualizzaColScadenzaConcorsi() {
    global $configurazione;
    if($configurazione['visualizzaColScadenzaConcorsi']) {
        return true;
    }
    return false;
}
?>