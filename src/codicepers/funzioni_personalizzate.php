<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015,2017 - AgID Agenzia per l'Italia Digitale
	*
	* Concesso in licenza a norma dell'EUPL, versione 1.1 o
	successive dell'EUPL (la "Licenza")– non appena saranno
	approvate dalla Commissione europea;
	* Non è possibile utilizzare l'opera salvo nel rispetto
	della Licenza.
	* È possibile ottenere una copia della Licenza al seguente
	indirizzo:
	*
	* https://joinup.ec.europa.eu/software/page/eupl
	*
	* Salvo diversamente indicato dalla legge applicabile o
	concordato per iscritto, il software distribuito secondo
	i termini della Licenza è distribuito "TAL QUALE",
	* SENZA GARANZIE O CONDIZIONI DI ALCUN TIPO,
	esplicite o implicite.
	* Si veda la Licenza per la lingua specifica che disciplina
	le autorizzazioni e le limitazioni secondo i termini della
	Licenza.
	*/ 
	/**
	 * @file
	 * codicepers/funzioni_personalizzate.php
	 * 
	 * @Descrizione
	 * File di estensione delle primitive disponibili in ISWEB. Il codice viene richiamato al termine dell'inzializzazione proprietaria dell'ambiente ISWEB.
	 * Nel contesto PAT viene utilizzato anche per inizializzare eventuali implementazione multi-ente della piattaforma.
	 *
	 */

 
/*********************************************PRIMITIVE AGGIUNTIVE PAT*********************************/
/* 
Nuove funzioni PAT di utilita' generica. Aggiungere qui eventuali altre funzioni da rendere disponibili nell'ambiente PAT 
*/  
function sezioneEsiste($idSezioni) {

	$arraySezioni = explode(',',$idSezioni);
	foreach((array)$arraySezioni as $idSez) {
		if(nomeSezDaId($idSez) != '') {
			return true;
		}		
	}
	return false;
}

function visualizzaOrganoPolitico($istanzaOggetto) {
	if($istanzaOggetto['organo'] != '') {
		if($istanzaOggetto['organo']=='segretario generale'){
			echo "<div>".traduciOrgani($istanzaOggetto['organo'])."</div>";
		} else {
			$organo = traduciOrgani($istanzaOggetto['organo']);
			if($organo != '') {
				if(moduloAttivo('agid') and ($istanzaOggetto['organo'] == 'direzione generale' or $istanzaOggetto['organo'] == 'giunta comunale' or $istanzaOggetto['organo'] == 'consiglio comunale')) {
					echo "<div>Organo di indirizzo politico-amministrativo: ".$organo."</div>";
				} else {
					echo "<div>Organo politico-amministrativo: ".$organo."</div>";
				}
			}
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
				if(floatval($bando['importo_liquidato']) > 0) {
					$totale += floatval($bando['importo_liquidato']);
				}
			}
			if($totale > 0) {
				echo "<div class=\"campoOggetto114\"> Totale importo liquidazioni: ".number_format($totale, 2, ',', '.')."</div>";
			}
		}
	}
}

function elencoCigMultipli($istanzaOggetto) {
	global $database, $dati_db, $configurazione, $idEnte;
	
	if($istanzaOggetto['tipologia'] == 'bandi ed inviti') {
		$out = "";
		$totale = 0;
		$lotti = prendiLotti($istanzaOggetto['id_record_cig_principale']);
		foreach ((array)$lotti as $lotto) {
			$out .= "<div class=\"campoOggetto114\"> CIG: <a href=\"".$server_url."index.php?id_oggetto=11&amp;id_cat=".$lotto['id_sezione']."&amp;id_doc=".$lotto['id']."\">".$lotto['cig']."</a> - Importo a base asta: ".$lotto['valore_base_asta']."</div>";
			if(floatval($lotto['valore_base_asta']) > 0) {
				$totale += floatval($lotto['valore_base_asta']);
			}
		}
		if(count($lotti) > 0 and $lotti[0]['id'] > 0) {
			echo $out;
			if($totale > 0) {
				echo "<div>&nbsp;</div><div class=\"campoOggetto114\"> Totale importo a base asta: ".number_format($totale, 2, ',', '.')."</div>";
			}
		}
	}
}

// struttura_mcrt_14
function selectRicercaStruttura($campo) {
	global $database, $dati_db, $configurazione, $idEnte;
	$sql = "SELECT id,nome_ufficio FROM ".$dati_db['prefisso']."oggetto_uffici WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' ORDER BY nome_ufficio";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, � probabile ci sia un errore nella condizione aqggiuntiva.');
	}
	$istanze = $database->sqlArrayAss($result);				
	foreach ((array)$istanze as $istanza) {
		$stringa = '';
		if ($istanza['id'] == $_POST[$campo]) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$istanza['id']."\"".$stringa." title=\"".$istanza['nome_ufficio']."\">".$istanza['nome_ufficio']."</option>";
	}
	echo '<div class="campoOggetto71">
		<span style="white-space: nowrap;">
			<label for="struttura">Struttura </label>
			<select class="stileForm75" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}
// responsabile_mcrt_15
function selectRicercaResponsabile($campo) {
	global $database, $dati_db, $configurazione, $idEnte;
	$sql = "SELECT id,referente FROM ".$dati_db['prefisso']."oggetto_riferimenti WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' ORDER BY referente";
	if ( !($result = $database->connessioneConReturn($sql)) ) {
		mostraAvviso(0,'Errore in questo campo: se presente, � probabile ci sia un errore nella condizione aqggiuntiva.');
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
			<label for="struttura">Responsabile </label>
			<select class="stileForm75" id="'.$campo.'" name="'.$campo.'">
				<option value="" title="qualunque">qualunque</option>'.$options.'
			</select>
		</span>
	</div>';
}


function stampaBeneficiario($istanzaOggetto){
	if(!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy'))){
		echo $istanzaOggetto['nominativo'];
	}else{
		echo "Omissis";
	}
}

function selectRicercaBandi() {
	global $database, $dati_db, $configurazione, $idEnte;
	if(moduloAttivo('bandigara')) {
		$valori = array('bandi ed inviti' => 'bandi di gara','esiti' => 'esiti di gara','delibere e determine a contrarre' => 'determine art. 57 comma 6 dlgs. 163/2006','affidamenti' => 'affidamenti','avvisi pubblici' => 'avvisi','somme liquidate' => 'liquidazioni');
	} else {
		$valori = array('bandi ed inviti' => 'bandi ed inviti','esiti' => 'esiti','delibere e determine a contrarre' => 'delibere e determine a contrarre','affidamenti' => 'affidamenti','avvisi pubblici' => 'avvisi pubblici','somme liquidate' => 'somme liquidate');
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
			<label for="tipologia_mcrt_10">Tipologia </label>
			<select class="stileForm75" id="tipologia_mcrt_10" name="tipologia_mcrt_10">
				'.$options.'
			</select>
		</span>
	</div>';
}

function visualizzaOggettoIncarico($incarichi) {
	global $database, $dati_db, $configurazione, $idEnte, $server_url;
	$html = "<div class=\"campoOggetto86\">Incarichi assegnati</div>";
	$lista = "";
	$aperturaLista = "<div><ul>";
	$chiusuraLista = "</ul></div>";
	$incarichi = explode(',',$incarichi);
	foreach ((array)$incarichi as $val) {
		//esclude gli incarichi amministrativi di vertice
		if(mostraDatoOggetto($val,4,'id') and !mostraDatoOggetto($val,4,'dirigente')){
			$lista .= "<li>";
			$lista .= "<a href=\"".$server_url."index.php?id_oggetto=4&amp;id_cat=".mostraDatoOggetto($val,4,'id_sezione')."&amp;id_doc=".mostraDatoOggetto($val,4,'id')."\">".mostraDatoOggetto($val,4,'oggetto')."</a>";
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

function visualizzaTabellaIndicizzazione($istanzaOggetto) {
	global $server_url;
	
	if($istanzaOggetto['tipologia'] == 'avvisi pubblici') {
		echo '';
	} else if($istanzaOggetto['tipologia'] != 'lotto') {
		echo '<a href="'.$server_url.'index.php?id_sezione=637&id_doc='.$istanzaOggetto['id'].'" title="Tabella delle informazioni d\'indicizzazione">Tabella delle informazioni d\'indicizzazione</a><div style="clear:both;height: 20px;"></div>';
	} else {
		echo 'Il presente lotto fa parte della procedura <a href="'.$server_url.'index.php?id_oggetto=11&amp;id_cat='.mostraDatoOggetto($istanzaOggetto['id_record_cig_principale'],11,'id_sezione').'&amp;id_doc='.$istanzaOggetto['id_record_cig_principale'].'">'.mostraDatoOggetto($istanzaOggetto['id_record_cig_principale'],11,'oggetto').'</a><div style="clear:both;height: 20px;"></div>';
	}
}

/*********************************************ESTENDO INIZIALIZZAZIONI AMBIENTE E VARIABILI*********************************/
/* 
Carico dai dati della sessione utente l'ente selezionato, nel caso di un installazione multi-ente
*/
$idEnte = is_numeric($datiUser['id_ente']) ? $datiUser['id_ente'] : 0; // ente scelto in navigazione
$idEnteAdmin = is_numeric($datiUser['id_ente_admin']) ? $datiUser['id_ente_admin'] : 0; // ente scelto in amministrazione
if ($idEnte) {
	// carico ente scelto nella variabile di sessione
	$entePubblicato = datoEnte($idEnte);
}
if ($idEnteAdmin) {
	// carico ente richiamato
	$enteAdmin = datoEnte($idEnteAdmin);	
}
$tipoEnte = datoTipoEnte($enteAdmin['tipo_ente']);
	
// modifico i parametri di configurazione in rapporto ai dati dell'ente richiesto
$GLOBALS['titoloSito'] = "Portale Trasparenza ".$entePubblicato['nome_completo_ente'];

// VERIFICO LE LETTURE COMPLETE DEGLI ELEMENTI IN RAPPORTO ALL'ENTE
if (count($oggettoReview)) {
	// sono in lettura completa, verifico se l'informazione ha l'ente giusto
	if ($idEnte != $oggettoReview['id_ente']) {
		// pagina da non visualizzare
		$contPer= new contenutoPers('messaggio','Questa pagina non � al momento disponibile.');
	}
}

?>