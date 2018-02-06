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
	 * classi/regole/creaFormTrasp.php
	 * 
	 * @Descrizione
	 * Utility per creazione campi form PAT che estende quella classica di ISWEB. Inclusa da core_functions_extended.php
	 *
	 */

if ($classe == '') {
	$classe = "input-medium";	
}

$stringaAttributi = '';
foreach((array)$attributi as $key => $val) {
	$stringaAttributi .= ' '.$key.'="'.$val.'" ';
}

$label = '<span class="etichettaLabel">'.$label.'</span>';

$testoObb = '';
if ($obbligatorio) {
	$testoObb = "<span class=\"obbliCampo intTooltip\"><a data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"Campo obbligatorio\"><span class=\"icon-ok-circle\"></span></a></span>";
}
if ($help) {
	$testoHelp = "<span class=\"obbliCampo\"><a class=\"tipHelpCont\" data-placement=\"top\" data-rel=\"tooltip\" data-content=\"".htmlentities($help)."\" title=\"Help contestuale\" class=\"btn btn-info btn-circle\"><span class=\"iconfa-question-sign\"></span></a></span>";
}

if ($tipo != 'sistema' and !$escludiHtml) {	
	echo "<div class=\"par control-group\">";
	echo "<label class=\"control-label\" for=\"".$nome."\">".$testoObb.$label.$testoHelp."</label>";
	echo "<div class=\"controls\">";
}
$disabilitatoTxt = "";
$disabilitatoClasse = "";
if ($disabilitato) {
	$disabilitatoTxt = " disabled=\"disabled\"";
	$disabilitatoClasse = "disabled ";
}

// bugfix per cancellazione delle selezioni autosuggerite
$selDefault='';
if ($valoreVero == '') {
	//$selDefault="selected=\"selected\"";
}
//echo "creo campo con nome: <b>".$nameForm."</b> e id <b>".$nome."</b>";
switch($tipo) {
	case "testo" :
		// pubblico campo testuale
		echo "<input".$disabilitatoTxt." placeholder=\"".$etiCampo."\" type=\"text\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" class=\"".$disabilitatoClasse.$classe."\" ".$stringaAttributi." />";
		
	break;
	
	case "decimale" :
		// pubblico campo testuale con icona euro
		echo "<div class=\"input-prepend\"><span class=\"add-on\"><span class=\"\">&euro;</span></span></div>";
		echo "<input".$disabilitatoTxt." placeholder=\"".$etiCampo."\" type=\"text\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" class=\"".$disabilitatoClasse.$classe."\" ".$stringaAttributi." />";
		
	break;
	
	case "data" :
		include('classi/campipat/data.php');
		
	break;
	
	case "ora" :
	
		// pubblico campo testuale
		echo "<div class=\"input-append bootstrap-timepicker\">";
		//echo "<span class=\"add-on\"><span class=\"iconfa-time\"></span></span>";
		echo "<input".$disabilitatoTxt." type=\"text\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" class=\"".$disabilitatoClasse.$classe."\" ".$stringaAttributi." />";
		echo "<span class=\"add-on\"><i class=\"iconfa-time\"></i> orario</span>";
		echo "</div>";

		
	break;
	
	case "casella" :
		// pubblico campo casella
		$txtSel = "";
		if ($valoreVero != '' AND $valoreVero) {
				$txtSel = " checked=\"checked\"";
		}
		echo "<input".$txtSel.$disabilitatoTxt." type=\"checkbox\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valori."\" class=\"".$disabilitatoClasse."\" ".$stringaAttributi." />";
		
	break;
	
	case "radio" :
		
		echo "<span class=\"formwrapper\">";
	
		$arrayEtichette = array();
		if ($etichette != '') {
			$arrayEtichette = explode(",",$etichette);
		}					
		$arrayValori = array();
		if ($valori != '') {
			$arrayValori = explode(",",$valori);
		}
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		$i = 0;
		foreach ((array)$arrayValori as $valore) {
			$testoEti = $valore;
			if ($arrayEtichette[$i] != '') {
				$testoEti = $arrayEtichette[$i];
			}
			// verifico se � gi� stato selezionato
			if (in_array($valore, $selezionati)) {
				echo "<span style=\"white-space:nowrap;display:inline-block;\"><input checked=\"checked\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valore."\" type=\"radio\" ".$stringaAttributi." > ".$testoEti." &nbsp; &nbsp;</span>";
			} else {
				echo "<span style=\"white-space:nowrap;display:inline-block;\"><input name=\"".$nome."\" id=\"".$nome."\" value=\"".$valore."\" type=\"radio\" ".$stringaAttributi." > ".$testoEti." &nbsp; &nbsp;</span>";
			}
			$i++;
		}
		echo "</span>";
		
	break;
	
	case "selezione" :
		if ($etiCampo == '') {
			$etiCampo = "Seleziona";	
		}
		// pubblico campo selezione generico
		if ($prop) {
			echo "<select".$disabilitatoTxt." data-placeholder=\"".$etiCampo."\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\" ".$selDefault." />";
		} else {
			echo "<select".$disabilitatoTxt." name=\"".$nome."\" id=\"".$nome."\" class=\"".$classe."\" ".$stringaAttributi." ><option value=\"\"/>".$etiCampo;
		}
		$arrayEtichette = array();
		if ($etichette != '') {
			$arrayEtichette = explode(",",$etichette);
		}					
		$arrayValori = array();
		if ($valori != '') {
			$arrayValori = explode(",",$valori);
		}
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		$i = 0;
		foreach ((array)$arrayValori as $valore) {
			$testoEti = $valore;
			if ($arrayEtichette[$i] != '') {
				$testoEti = $arrayEtichette[$i];
			}
			// verifico se � gi� stato selezionato
			if (in_array($valore, $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$valore."\">".$testoEti."</option>";
			} else {
				echo "<option value=\"".$valore."\">".$testoEti."</option>";
			}
			$i++;
		}
		echo "</select>";
		
	break;
	
	case "selezioni" :
		
		if ($etiCampo == '') {
			$etiCampo = "Seleziona";	
		}
		// pubblico campo selezione generico
		if ($prop) {
			echo "<input type=\"hidden\" name=\"".$nome."\" value=\"\" />";
			echo "<select".$disabilitatoTxt." multiple=\"multiple\" data-placeholder=\"".$etiCampo."\" name=\"".$nome."[]\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\" ".$selDefault."/>";
		} else {
			echo "<select".$disabilitatoTxt." multiple=\"multiple\" name=\"".$nome."[]\" id=\"".$nome."\" class=\"".$classe."\" ".$stringaAttributi." ><option value=\"\">".$etiCampo."</option>";
		}
		$arrayEtichette = array();
		if ($etichette != '') {
			$arrayEtichette = explode(",",$etichette);
		}					
		$arrayValori = array();
		if ($valori != '') {
			$arrayValori = explode(",",$valori);
		}
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		$i = 0;
		foreach ((array)$arrayValori as $valore) {
			$testoEti = $valore;
			if ($arrayEtichette[$i] != '') {
				$testoEti = $arrayEtichette[$i];
			}
			// verifico se � gi� stato selezionato
			if (in_array($valore, $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$valore."\">".$testoEti."</option>";
			} else {
				echo "<option value=\"".$valore."\">".$testoEti."</option>";
			}
			$i++;
		}
		echo "</select>";
		
	break;
	
	case "link" :
		// pubblico campo link
		echo "<div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-link\"></span></span></div>";
		echo "<input".$disabilitatoTxt." type=\"text\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" class=\"".$disabilitatoClasse.$classe."\" ".$stringaAttributi."  />";
	break;
	
	case "enti" :
		// pubblico campo selezione enti
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona o cerca un ente....\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\" ".$selDefault."/>";
		// carico lista enti
		$enti = caricaEnti(); 
		foreach ((array)$enti as $ente) {
			if (in_array($ente['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$ente['id']."\">".$ente['nome_completo_ente']."</option>";
			} else {
				echo "<option value=\"".$ente['id']."\">".$ente['nome_completo_ente']."</option>";
			}
			//echo "<option value=\"".$ente['id']."\">".$ente['nome_completo_ente']."</option>";
		}
		echo "</select>";
		
	break;
	
	case "file" :
		// pubblico campo upload file
		
		if ($valoreVero == '') {
			// FORM NORMALE DI AGGIUNTA FILE
			$formInput =  "<div class=\"fileupload fileupload-new\" data-provides=\"fileupload\">
					<div class=\"input-append\">
						<div class=\"uneditable-input span3\">
							<i class=\"iconfa-file fileupload-exists\"></i>
							<span class=\"fileupload-preview\"></span>
						</div>
						<span class=\"btn btn-file\">
							<span class=\"fileupload-new\">Seleziona un file</span>
							<span class=\"fileupload-exists\">Cambia file</span>
							<input type=\"file\" name=\"".$nome."\" id=\"".$nome."\" ".$stringaAttributi."  />
						</span>
						<a href=\"#\" class=\"btn fileupload-exists\" data-dismiss=\"fileupload\">Rimuovi file</a>
					</div>
				</div>					
				<input id=\"".$nome."azione\" type=\"hidden\" name=\"".$nome."azione\" value=\"aggiungi\" />";
			
			if(($_GET['azione'] == 'importAtto' or $_GET['azione'] == 'modifica') and count($GLOBALS['allegatiAtto']) > 0) {
				echo "<div class=\"forzoCampoForm\"><div class=\"contenitore-errore-allegato\">";
				
				echo "<div id=\"upload-file-".$nome."\" style=\"display: none;\">";
				echo $formInput;
				echo "</div>";
				
				echo "<div id=\"select-file-".$nome."\" style=\"display: block;\">";
				echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona un file dell'albo online...\" name=\"import-file-".$nome."\" id=\"import-file-".$nome."\" class=\"".$disabilitatoClasse."chzn-select input-xxlarge\" ".$stringaAttributi." ><option value=\"\" ".$selDefault."/>";
				foreach ((array)$GLOBALS['allegatiAtto'] as $allegato) {
					$nomeFile = explode('O__O', $allegato['allegato']);
					$nomeFile = $nomeFile[1];
					echo "<option value=\"".$allegato['id']."\">".$nomeFile.($allegato['descrizione'] != '' ? ' - '.$allegato['descrizione'] : '')."</option>";
				}
				echo "</select>";
				echo "</div>";
				
				echo "<span id=\"btn-select-file-".$nome."\" style=\"display: none;\"><a onclick=\"selectFile".$nome."()\" style=\"color:#353535;\" class=\"btn btn-rounded\"> <i class=\"iconfa-check\"></i> Seleziona un file dell'albo online</a></span> ";
				echo "<span id=\"btn-upload-file-".$nome."\">oppure &nbsp;<a onclick=\"uploadFile".$nome."()\" style=\"color:#353535;\" class=\"btn btn-rounded\"> <i class=\"iconfa-upload\"></i> Carica un nuovo file</a></span> ";
				
				echo "<input id=\"provenienza-file-".$nome."\" type=\"hidden\" name=\"provenienza-file-".$nome."\" value=\"select\" />";
				
				echo "<script type=\"text/javascript\">
					function selectFile".$nome."() {
						jQuery('#select-file-".$nome."').css('display', 'block');
						jQuery('#upload-file-".$nome."').css('display', 'none');
						jQuery('#btn-select-file-".$nome."').css('display', 'none');
						jQuery('#btn-upload-file-".$nome."').css('display', 'inline');
						jQuery('#provenienza-file-".$nome."').val('select');
					}
					function uploadFile".$nome."() {
						jQuery('#select-file-".$nome."').css('display', 'none');
						jQuery('#upload-file-".$nome."').css('display', 'block');
						jQuery('#btn-select-file-".$nome."').css('display', 'inline');
						jQuery('#btn-upload-file-".$nome."').css('display', 'none');
						jQuery('#provenienza-file-".$nome."').val('upload');
					}
					</script>";
				
				echo "</div></div>";
			} else {
				echo $formInput;
			}
			
		} else {
			// FORM MODIFICA/RIMOZIONE FILE
			// nome vero del file 
			$nomeFile = substr($valoreVero, strpos($valoreVero, "O__O") + 4);
			
			// se � una immagine, ne presento l'anteprima
			$posPunto = strrpos($valoreVero, ".");
			$estFile = strtolower(substr($valoreVero, ($posPunto +1)));
			
			echo "<div class=\"forzoCampoForm\"><div class=\"contenitore-errore-allegato\">";
			
			echo "<div class=\"prevFile\" id=\"fileBoxPrev".$nome."\">";
			if ($estFile == 'jpg' OR $estFile == 'png' OR $estFile == 'gif' OR $estFile == 'jpeg' OR $estFile == 'bmp') {
				// visualizzo anteprima immagine
				if ($menu=='configurazione' OR $menu=='enti') {
					echo "Immagine attuale: <img style=\"margin:3px 10px;vertical-align:middle;border:1px solid #CCCCCC;\" src=\"./download/enti_pat/".$valoreVero."\" width=\"50\" /> <a href=\"./download/".$oggOgg->tabellaOggetto."/".$valoreVero."\">".$nomeFile."</a>";
				} else {
					echo "Immagine attuale: <img style=\"margin:3px 10px;vertical-align:middle;border:1px solid #CCCCCC;\" src=\"./download/".$oggOgg->tabellaOggetto."/".$valoreVero."\" width=\"50\" /> <a href=\"./download/".$oggOgg->tabellaOggetto."/".$valoreVero."\">".$nomeFile."</a>";
				}
			} else {
				echo "File attuale: <span class=\"iconfa-file fileupload-exists\"></span> <a href=\"./download/".$oggOgg->tabellaOggetto."/".$valoreVero."\">".$nomeFile."</a>";
			}
			echo "</div>";
			
			// FORM NORMALE DI MODIFICA FILE
			// inserisco il codice javascript necessario
			echo "<script type=\"text/javascript\">
			function cancellaFile".$nome."() {
				jQuery('#".$nome."azione').val('elimina');
				// se il box upload � aperto, lo chiudo
				if (jQuery('#fileBox".$nome."').css('display') == 'block') {
					jQuery('#fileBox".$nome."').toggle();
				}
				if (jQuery('#cancAlert".$nome."').css('display') != 'block') {
					jQuery('#cancAlert".$nome."').toggle();
					//alert ('pulsante mantieni �: '+jQuery('#mantieni".$nome."').css('display'));
					if (jQuery('#mantieni".$nome."').css('display') != 'inline-block') {
						
						jQuery('#mantieni".$nome."').toggle();
					}
				}
				if (jQuery('#select-file-".$nome."').css('display') == 'block') {
					jQuery('#select-file-".$nome."').toggle();
				}
				jQuery('#provenienza-file-".$nome."').val('upload');
			}
			function selectFile".$nome."() {
				jQuery('#".$nome."azione').val('importAllegato');
				jQuery('#select-file-".$nome."').css('display', 'block');
				jQuery('#fileBox".$nome."').css('display', 'none');
				if (jQuery('#mantieni".$nome."').css('display') != 'inline-block') {
					jQuery('#mantieni".$nome."').toggle();
				}
				if (jQuery('#cancAlert".$nome."').css('display') == 'block') {
					jQuery('#cancAlert".$nome."').toggle();
				}
				if (jQuery('#fileBox".$nome."').css('display') == 'block') {
					jQuery('#fileBox".$nome."').toggle();
				}
				jQuery('#provenienza-file-".$nome."').val('select');
			}
			function modificaFile".$nome."() {
				jQuery('#".$nome."azione').val('modifica');
				// se il box upload non � aperto, lo apro
				if (jQuery('#fileBox".$nome."').css('display') != 'block') {
					jQuery('#fileBox".$nome."').toggle();
					//alert ('pulsante mantieni �: '+jQuery('#mantieni".$nome."').css('display'));
					if (jQuery('#mantieni".$nome."').css('display') != 'inline-block') {
						
						jQuery('#mantieni".$nome."').toggle();
					}
				}
				if (jQuery('#cancAlert".$nome."').css('display') == 'block') {
					jQuery('#cancAlert".$nome."').toggle();
				}
				if (jQuery('#select-file-".$nome."').css('display') == 'block') {
					jQuery('#select-file-".$nome."').toggle();
				}
				jQuery('#provenienza-file-".$nome."').val('upload');
			}
			function mantieniFile".$nome."() {
				jQuery('#".$nome."azione').val('nessuna');
				// chiudo tutti i box
				if (jQuery('#fileBox".$nome."').css('display') == 'block') {
					jQuery('#fileBox".$nome."').toggle();
				}
				if (jQuery('#cancAlert".$nome."').css('display') == 'block') {
					jQuery('#cancAlert".$nome."').toggle();
				}
				if (jQuery('#select-file-".$nome."').css('display') == 'block') {
					jQuery('#select-file-".$nome."').toggle();
				}
				jQuery('#mantieni".$nome."').toggle();
				jQuery('#provenienza-file-".$nome."').val('upload');
			}
			</script>";
			if(count($GLOBALS['allegatiAtto']) > 0) {
				echo "<span id=\"btn-select-file-".$nome."\"><a onclick=\"selectFile".$nome."()\" style=\"color:#353535;\" class=\"btn btn-rounded\"> <i class=\"iconfa-check\"></i> Seleziona un file dell'albo online</a></span> ";
			}
			echo "<a onclick=\"modificaFile".$nome."()\" style=\"color:#353535;\" class=\"btn btn-rounded\"> <i class=\"iconfa-save\"></i> Modifica file attuale</a> ";
			echo "<a onclick=\"cancellaFile".$nome."()\" style=\"color:#353535;\" class=\"btn btn-rounded\"> <i class=\"iconfa-trash\"></i> Rimuovi file attuale</a> ";
			echo "<a id=\"mantieni".$nome."\" onclick=\"mantieniFile".$nome."()\" style=\"color:#353535;display:none;\" class=\"btn btn-rounded\"> Mantieni file attuale</a> ";
			echo "<div style=\"display:none;\" id=\"cancAlert".$nome."\" style=\"hidden\"><strong>Il file attuale verr� rimosso</strong></div>";
			
			echo "<div id=\"fileBox".$nome."\" style=\"display:none;\" class=\"fileupload fileupload-new\" data-provides=\"fileupload\">
					<div class=\"input-append\">
						<div class=\"uneditable-input span3\">
							<i class=\"iconfa-file fileupload-exists\"></i>
							<span class=\"fileupload-preview\"></span>
						</div>
						<span class=\"btn btn-file\">
							<span class=\"fileupload-new\">Seleziona un nuovo file</span>
							<span class=\"fileupload-exists\">Cambia il nuovo file</span>
							<input type=\"file\" name=\"".$nome."\" id=\"".$nome."\" ".$stringaAttributi."  />
						</span>
						<a href=\"#\" class=\"btn fileupload-exists\" data-dismiss=\"fileupload\">Rimuovi il nuovo file</a>
					</div>
				</div>
				<input id=\"".$nome."azione\" type=\"hidden\" name=\"".$nome."azione\" value=\"nessuna\" />";
			if(count($GLOBALS['allegatiAtto']) > 0) {
				echo "<div id=\"select-file-".$nome."\" style=\"display: none;\">";
				echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona un file dell'albo online...\" name=\"import-file-".$nome."\" id=\"import-file-".$nome."\" class=\"".$disabilitatoClasse."chzn-select input-xxlarge\" ".$stringaAttributi." ><option value=\"\" ".$selDefault."/>";
				foreach ((array)$GLOBALS['allegatiAtto'] as $allegato) {
					$nomeFile = explode('O__O', $allegato['allegato']);
					$nomeFile = $nomeFile[1];
					echo "<option value=\"".$allegato['id']."\">".$nomeFile.($allegato['descrizione'] != '' ? ' - '.$allegato['descrizione'] : '')."</option>";
				}
				echo "</select>";
				echo "</div>";
			
				echo "<input id=\"provenienza-file-".$nome."\" type=\"hidden\" name=\"provenienza-file-".$nome."\" value=\"select\" />";
			}
			echo "</div></div>";
		}
		
	break;
	
	case "ruoli" :
		// pubblico campo selezione normative
		echo "<input type=\"hidden\" name=\"".$nome."\" value=\" \" />";
		echo "<select".$disabilitatoTxt." multiple=\"multiple\" data-placeholder=\"Seleziona o cerca tra i ruoli disponibili....\" name=\"".$nome."[]\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\" ".$selDefault." />";
		// carico lista procedimenti
		if ($datiUser['permessi']==10) {
			$sql = "SELECT id,id_ente,nome FROM ".$dati_db['prefisso']."etrasp_ruoli ORDER BY nome";
		} else {
			$sql = "SELECT id,id_ente,nome FROM ".$dati_db['prefisso']."etrasp_ruoli WHERE id_ente=0 OR id_ente=".$idEnteAdmin." ORDER BY nome";
		}
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i ruoli (con condizione)'.$sql);
		}
		$ruoli = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$ruoli as $ruolo) {
			// ulteriori info nel nome
			if ($ruolo['id_ente']) {
				$ruolo['nome'] = $ruolo['nome']." (".datoEnte($ruolo['id_ente'],'nome_completo_ente').")";
			} else {
				$ruolo['nome'] = $ruolo['nome']." [Ruolo di sistema]";
			}						
			// verifico se � gi� stato selezionato
			if (in_array($ruolo['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$ruolo['id']."\">".$ruolo['nome']."</option>";
			} else {
				echo "<option value=\"".$ruolo['id']."\">".$ruolo['nome']."</option>";
			}
		}
		echo "</select>";
		
	break;
	
	case "regolamento" :
	case "regolamenti" :
		$multiplo = false;
		if ($tipo =='regolamenti') {
			// multiplo
			$multiplo = true;			
		}
		// pubblico campo selezione regolamento
		$dataPlaceholder = 'Seleziona o cerca regolamento....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'documentazione';
		$menusecAggiunta = 'regolamenti';
		$campoAjax = 'regolamenti';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "normativa" :
	case "normative" :
		$multiplo = false;
		if ($tipo =='normative') {
			// multiplo
			$multiplo = true;			
		}
		// pubblico campo selezione normativa
		$dataPlaceholder = 'Seleziona o cerca normativa....';
		$testoAggiungi = 'Aggiungi nuova';
		$menuAggiunta = 'documentazione';
		$menusecAggiunta = 'normativa';
		$campoAjax = 'normativa';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "incarico" :
	case "incarichi" :
		$multiplo = false;
		if ($tipo =='incarichi') {
			// multiplo
			$multiplo = true;			
		}
		// pubblico campo selezione normativa
		$dataPlaceholder = 'Seleziona o cerca incarico....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'pubblicazioni';
		$menusecAggiunta = 'incarichi';
		$campoAjax = 'incarichimulti';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "requisitogara" :
		// pubblico campo selezione requisiti
		echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona o cerca un requisito....\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\"  ".$selDefault."/>";
		// carico lista procedimenti
		$sql = "SELECT id,codice,denominazione FROM ".$dati_db['prefisso']."oggetto_bandi_requisiti_qualificazione";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i requisiti (con condizione)'.$sql);
		}
		$requisiti = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$requisiti as $requisitogara) {
			$testoEti = $requisitogara['codice']." - ".$requisitogara['denominazione'];
			// verifico se � gi� stato selezionato
			if (in_array($requisitogara['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$requisitogara['id']."\">".$testoEti."</option>";
			} else {
				echo "<option value=\"".$requisitogara['id']."\">".$testoEti."</option>";
			}
		}
		echo "</select>";
		
	break;
	
	case "bandogara" :
	case "bandogara_from_avviso" :
	case "bandogara_from_esito" :
	case "bandogara_from_liquidazione" :
	
		switch($tipo) {
			case "bandogara" :
				//vecchia versione
				$sql = "SELECT id,oggetto,cig,data_attivazione,data_scadenza,tipologia FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnteAdmin." "
					." AND tipologia != 'somme liquidate' AND tipologia != 'avvisi pubblici' AND (bando_collegato = '' OR bando_collegato IS NULL OR bando_collegato = 0) "
					." AND id != ".$configurazione['bandoInModifica']." "
					."ORDER BY data_attivazione desc LIMIT 4000";
			break;
			case "bandogara_from_avviso" :
				//da un avviso posso selezionare solamente un bando
				$sql = "SELECT id,oggetto,cig,data_attivazione,data_scadenza,tipologia FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnteAdmin." "
					." AND tipologia = 'bandi ed inviti' AND (bando_collegato = '' OR bando_collegato IS NULL OR bando_collegato = 0) "
					."ORDER BY data_attivazione desc";
			break;
			case "bandogara_from_esito" :
				//da un esito posso selezionare o un bando o un lotto
				$sql = "SELECT id,oggetto,cig,data_attivazione,data_scadenza,tipologia FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnteAdmin." "
					." AND ((tipologia = 'bandi ed inviti' AND (id_record_cig_principale = '' OR id_record_cig_principale IS NULL OR id_record_cig_principale = 0)) OR tipologia = 'lotto') AND (bando_collegato = '' OR bando_collegato IS NULL OR bando_collegato = 0) "
					."ORDER BY data_attivazione desc";
			break;
			case "bandogara_from_liquidazione" :
				//da una liquidazione posso selezionare o un esito o un affidamento o una determina
				$sql = "SELECT id,oggetto,cig,data_attivazione,data_scadenza,tipologia FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnteAdmin." "
					." AND (tipologia = 'esiti' OR tipologia = 'affidamenti' OR tipologia = 'delibere e determine a contrarre') "
					."ORDER BY data_attivazione desc";
			break;
		}
	
		// pubblico campo selezione bandogara
		echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona o cerca un bando di gara....\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\"  ".$selDefault."/>";
		// carico lista procedimenti
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i bandi di gara (con condizione)'.$sql);
		}
		$bandi = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$bandi as $bando) {
			if(moduloAttivo('bandigara')) {
				switch($bando['tipologia']) {
					//bandi ed inviti,esiti,delibere e determine a contrarre,affidamenti,avvisi pubblici,somme liquidate
					case 'bandi ed inviti':
						$bando['tipologia'] = 'bando di gara';
					break;
					case 'lotto':
						$bando['tipologia'] = 'lotto';
					break;
					case 'esiti':
						$bando['tipologia'] = 'esito di gara';
					break;
					case 'delibere e determine a contrarre':
						$bando['tipologia'] = 'determina art. 57 comma 6 dlgs. 163/2006';
					break;
					case 'affidamenti':
						$bando['tipologia'] = 'affidamento';
					break;
					case 'avvisi pubblici':
						$bando['tipologia'] = 'avviso';
					break;
					case 'somme liquidate':
						$bando['tipologia'] = 'liquidazione';
					break;
				}
			}
			$testoEti = "[".$bando['tipologia']." - ".$bando['cig']."]".$bando['oggetto']." [".visualizzaData($bando['data_attivazione'],'d/m/Y')." - ".visualizzaData($bando['data_scadenza'],'d/m/Y')."]";
			// verifico se � gi� stato selezionato
			if (in_array($bando['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$bando['id']."\">".$testoEti."</option>";
			} else {
				echo "<option value=\"".$bando['id']."\">".$testoEti."</option>";
			}
		}
		echo "</select>";
		
	break;
	
	case "bandogara_libero" :
		// pubblico campo selezione bandogara
		echo "<select".$disabilitatoTxt." multiple=\"multiple\" data-placeholder=\"Seleziona o cerca altre procedure....\" name=\"".$nome."[]\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\"  ".$selDefault." />";
		
		$sql = "SELECT id,oggetto,cig,data_attivazione,data_scadenza,tipologia FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnteAdmin." "
			." AND (tipologia = 'bandi ed inviti' OR tipologia = 'esiti' OR tipologia = 'avvisi pubblici') "
			."ORDER BY data_attivazione desc";
		
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero delle altre procedure'.$sql);
		}
		$bandi = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$bandi as $bando) {
			$testoEti = "[".$bando['tipologia']." - ".$bando['cig']."]".$bando['oggetto']." [".visualizzaData($bando['data_attivazione'],'d/m/Y')." - ".visualizzaData($bando['data_scadenza'],'d/m/Y')."]";
			// verifico se � gi� stato selezionato
			if (in_array($bando['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$bando['id']."\">".$testoEti."</option>";
			} else {
				echo "<option value=\"".$bando['id']."\">".$testoEti."</option>";
			}
		}
		echo "</select>";
	
	break;	
	
	case "cig_multipli":
		global $numRigaCig, $lotti;
		
		ob_start();
		include('./classi/regole/cig_multipli.php');
		$content = ob_get_clean();
		ob_end_flush();
		echo $content;
		
	break;
	
	case "id_record_cig_principale":
		// pubblico campo selezione bandogara da utilizzare solamente sulla maschera di inserimento/modifica di un lotto
		echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona o cerca un bando di gara....\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\"  ".$selDefault."/>";
		// carico lista procedimenti
		$sql = "SELECT id,oggetto,cig,data_attivazione,data_scadenza FROM ".$dati_db['prefisso']."oggetto_gare_atti WHERE id_ente=".$idEnteAdmin." "
				." AND tipologia = 'bandi ed inviti' AND id = id_record_cig_principale "
				."ORDER BY data_attivazione desc";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i bandi di gara (con condizione)'.$sql);
		}
		$bandi = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$bandi as $bando) {
			
			$testoEti = "[".$bando['cig']."]".$bando['oggetto']." [".visualizzaData($bando['data_attivazione'],'d/m/Y')." - ".visualizzaData($bando['data_scadenza'],'d/m/Y')."]";
			// verifico se � gi� stato selezionato
			if (in_array($bando['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$bando['id']."\">".$testoEti."</option>";
			} else {
				echo "<option value=\"".$bando['id']."\">".$testoEti."</option>";
			}
		}
		echo "</select>";
	break;
	
	case "fornitori" :
	case "fornitore_singolo" :
		$multiplo = true;
		// pubblico campo selezione fornitore
		$dataPlaceholder = 'Seleziona o cerca tra i fornitori....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'pubblicazioni';
		$menusecAggiunta = 'fornitori';
		$campoAjax = $tipo;
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "bandoconcorso" :
		$multiplo = false;
		// pubblico campo selezione bandoconcorso
		$dataPlaceholder = 'Seleziona o cerca un bando di concorso....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'pubblicazioni';
		$menusecAggiunta = 'bandiconcorso';
		$campoAjax = 'bandiconcorso';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;		

	
	case "procedimenti" :
	
		// pubblico campo selezione procedimenti
		$multiplo = true;			
		$dataPlaceholder = 'Seleziona o cerca procedimenti....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'organizzazione';
		$menusecAggiunta = 'procedimenti';
		$campoAjax = 'procedimenti';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "modulistica" :
		// pubblico campo selezione modulistica
		$multiplo = true;			
		$dataPlaceholder = 'Seleziona o cerca modulistica....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'documentazione';
		$menusecAggiunta = 'modulistica';
		$campoAjax = 'modulistica';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "struttura" :
	case "strutture" :
		$multiplo = false;
		if ($tipo =='strutture') {
			// multiplo
			$multiplo = true;			
		}
		// pubblico campo selezione strutture
		$dataPlaceholder = 'Seleziona o cerca una struttura....';
		$testoAggiungi = 'Aggiungi nuova';
		$menuAggiunta = 'organizzazione';
		$menusecAggiunta = 'strutture';
		$campoAjax = 'struttura';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "referente" :
	case "referenti" :
	
		$multiplo = false;
		if ($tipo =='referenti') {
			// multiplo
			$multiplo = true;			
		}
		// pubblico campo selezione referente
		$dataPlaceholder = 'Seleziona o cerca nel personale....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'organizzazione';
		$menusecAggiunta = 'personale';
		$campoAjax = 'referente';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "incarichimulti" :
		
		$multiplo = true;			
		// pubblico campo selezione referente
		$dataPlaceholder = 'Seleziona o cerca tra gli incarichi....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'pubblicazioni';
		$menusecAggiunta = 'incarichi';
		$campoAjax = 'incarichimulti';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;	
	
	case "utente" :
		// pubblico campo selezione referente
		echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona o cerca tra gli utenti....\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\"   ".$selDefault."/>";
		// carico lista utenti
		$condizionEnte='';
		if ($idEnteAdmin) {
			$condizionEnte = "WHERE id_ente_admin=".$idEnteAdmin." "; 
		}
		$sql = "SELECT id,nome,username FROM ".$dati_db['prefisso']."utenti ".$condizionEnte." ORDER BY nome";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i utenti (con condizione)'.$sql);
		}
		$referenti = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$referenti as $referente) {
			// ulteriori info nel nome
			if ($referente['username'] != '') {
				$referente['nome'] = $referente['nome']." (".$referente['username'].")";
			}
		
			// verifico se � gi� stato selezionato
			if (in_array($referente['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$referente['id']."\">".$referente['nome']."</option>";
			} else {
				echo "<option value=\"".$referente['id']."\">".$referente['nome']."</option>";
			}
		}
		echo "</select>";
		
	break;
	
	case "provvedimento" :
	case "provvedimenti" :
		$multiplo = false;
		if ($tipo =='provvedimenti') {
			// multiplo
			$multiplo = true;			
		}
		// pubblico campo selezione referente
		$dataPlaceholder = 'Seleziona o cerca tra i provvedimenti....';
		$testoAggiungi = 'Aggiungi nuovo';
		$menuAggiunta = 'pubblicazioni';
		$menusecAggiunta = 'provvedimenti';
		$campoAjax = 'provvedimenti';
		include('classi/regole/creaFormTrasp/select2.php');
		
	break;
	
	case "utenti" :
		// pubblico campo selezione referenti
		echo "<input type=\"hidden\" name=\"".$nome."\" value=\" \" />";
		echo "<select".$disabilitatoTxt." multiple=\"multiple\" data-placeholder=\"Seleziona o cerca tra gli utenti....\" name=\"".$nome."[]\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\"  ".$selDefault." />";
		// carico lista utenti
		$condizionEnte='';
		if ($idEnteAdmin) {
			$condizionEnte = "WHERE id_ente_admin=".$idEnteAdmin." "; 
		}
		$sql = "SELECT id,nome,username FROM ".$dati_db['prefisso']."utenti ".$condizionEnte." ORDER BY nome";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i utenti (con condizione)'.$sql);
		}
		$referenti = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$referenti as $referente) {
			// ulteriori info nel nome
			if ($referente['username'] != '') {
				$referente['nome'] = $referente['nome']." (".$referente['username'].")";
			}
		
			// verifico se � gi� stato selezionato
			if (in_array($referente['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$referente['id']."\">".$referente['nome']."</option>";
			} else {
				echo "<option value=\"".$referente['id']."\">".$referente['nome']."</option>";
			}
		}
		echo "</select>";
		
	break;
	
	case "tipoente" :
		// pubblico campo selezione referenti
		echo "<select".$disabilitatoTxt." data-placeholder=\"Seleziona o cerca un tipo di ente....\" name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse."chzn-select ".$classe."\" ".$stringaAttributi." ><option value=\"\" ".$selDefault."/>";
		// carico lista tipi ente
		$sql = "SELECT id,nome_tipo FROM ".$dati_db['prefisso']."oggetto_etrasp_tipoentisemplice ORDER BY nome_tipo";
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero di tutti i tipi di enti (semplice).'.$sql);
		}
		$tipologie = $database->sqlArrayAss($result);
		$selezionati = array();
		if ($valoreVero != '') {
			$selezionati = explode(",",$valoreVero);
		}
		foreach ((array)$tipologie as $tipologia) {		
			// verifico se � gi� stato selezionato
			if (in_array($tipologia['id'], $selezionati)) {
				echo "<option selected=\"selected\" value=\"".$tipologia['id']."\">".$tipologia['nome_tipo']."</option>";
			} else {
				echo "<option value=\"".$tipologia['id']."\">".$tipologia['nome_tipo']."</option>";
			}
		}
		echo "</select>";
		
	break;
	
	case "html" :
	
		// debug carattere strano
		if ($valoreVero == '<p>&Acirc;&nbsp;</p>' OR $valoreVero == '<p>&Acirc;&nbsp;</p>\n') {
			$valoreVero = '';
		}
	
		// pubblico area editor html
		echo "<div style=\"margin-left:240px;!important\"><textarea name=\"".$nome."\" id=\"".$nome."\" ".$stringaAttributi." >".$valoreVero."</textarea></div>"; 
	break;
	
	case "areatesto" :
		// pubblico area testo normale
		echo "<textarea".$disabilitatoTxt." name=\"".$nome."\" id=\"".$nome."\" class=\"".$disabilitatoClasse.$classe."\" ".$stringaAttributi." >".$valoreVero."</textarea>"; 
	break;
	
	case "sistema" :
		// pubblico area editor html
		echo "<input type=\"hidden\" name=\"rispostaForm\" value=\"1\" />
			<input type=\"hidden\" id=\"tipo_user\" value=\"nessuno\">	
			<input type=\"hidden\" id=\"id_categoria\" name=\"id_categoria\" value=\"0\">
			<input type=\"hidden\" id=\"id_lingua\" name=\"id_lingua\" value=\"0\">			
			<input type=\"hidden\" id=\"id_proprietari_admin\" name=\"id_proprietari_admin\" value=\"-1\">
			<input type=\"hidden\" id=\"tipo_proprietari_admin\" name=\"tipo_proprietari_admin\" value=\"tutti\">
			<input type=\"hidden\" id=\"permessi_admin\" name=\"permessi_admin\" value=\"N/A\">	
			<input type=\"hidden\" id=\"id_proprietari_lettura\" name=\"id_proprietari_lettura\" value=\"-1\">
			<input type=\"hidden\" id=\"tipo_proprietari_lettura\" name=\"tipo_proprietari_lettura\" value=\"tutti\">
			<input type=\"hidden\" id=\"permessi_lettura\" name=\"permessi_lettura\" value=\"N/A\">";	
		if($_GET['box']) {
			echo "<input type=\"hidden\" id=\"idBtnEdit\" name=\"idBtnEdit\" value=\"".$_GET['idBtnEdit']."\">";	
		}
		if(($datiUser['id_ente_admin']==35 OR $datiUser['id_ente_admin']==142) AND $aclTrasparenza[$menuSecondario]['modifica'] AND $aclTrasparenza[$menuSecondario]['creazione'] AND $_GET['azione'] != 'aggiungi') {
			//creaFormTrasp('Utente proprietario','utente', 'id_proprietario', '', $istanzaOggetto['id_proprietario'], '','input-xlarge'); 				
			creaFormTrasp('Utente proprietario','utente', 'id_proprietario', '', $istanzaOggetto['id_proprietario'], '','input-xlarge',0,'', 0, 0,true); 				
		} else {
			if($istanzaOggetto['id_proprietario'] > 0) {
				echo "<input type=\"hidden\" id=\"id_proprietario\" name=\"id_proprietario\" value=\"".$istanzaOggetto['id_proprietario']."\">";
			} else {
				echo "<input type=\"hidden\" id=\"id_proprietario\" name=\"id_proprietario\" value=\"".$datiUser['id']."\">";
			}
		}		
	break;
	
	case "gmaps" :
		// pubblico campo selezione google maps
		include('./pat/classi/campi/input/gmaps.tmp');
	break;
	
}	
if ($tipo != 'sistema' and !$escludiHtml) {

	echo "</div>";
	echo "</div>";
}
?>