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
	
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/personale/lettura.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/personale/lettura.php');
} else {
	
	if ($istanzaOggetto['foto'] != '' and $istanzaOggetto['foto'] != 'nessuno') {
		$posPunto = strrpos($istanzaOggetto['foto'], ".");
		$estFile = strtolower(substr($istanzaOggetto['foto'], ($posPunto +1)));

		if ($estFile == 'gif' or $estFile == 'jpg' or $estFile == 'jpeg' or $estFile == 'png' or $estFile == 'bmp') {
			// PUBBLICO UNA IMMAGINE
			echo "<div class=\"trasp_fotoReferente\"><img alt=\"" . $istanzaOggetto['referente'] . "\" src=\"" . $base_url . "moduli/output_media.php?file=" . $documento->tabella . "/" . $istanzaOggetto['foto'] . "&amp;qualita=75&amp;larghezza=120px\" /></div>";
		}
	}
	
	echo "<h3 class=\"trasp_titolo\"><strong>".$istanzaOggetto['tit']." ".$istanzaOggetto['referente']."</strong></h3>";

	if($istanzaOggetto['carica_inizio'] != '' and $istanzaOggetto['carica_inizio'] > 0) {
		echo '<div>In carica da: '.visualizzaData($istanzaOggetto['carica_inizio'], 'd/m/Y').'</div>';
	}
	if($istanzaOggetto['carica_fine'] != '' and $istanzaOggetto['carica_fine'] > 0) {
		echo '<div>In carica fino a: '.visualizzaData($istanzaOggetto['carica_fine'], 'd/m/Y').'</div>';
	}
	
	if($istanzaOggetto['qualifica'] != '') {
		echo '<div>Qualifica: '.$istanzaOggetto['qualifica'].'</div>';
	}
	
	visualizzaRuolo($istanzaOggetto);
	
	visualizzaOrganoPolitico($istanzaOggetto);
	
	if($istanzaOggetto['ruolo_politico'] != '' and !moduloAttivo('agid')) {
		echo '<div>Incarico di stampo politico: '.$istanzaOggetto['ruolo_politico'].'</div>';
	} else if($istanzaOggetto['ruolo_politico'] != '' and moduloAttivo('agid') AND visualizzaOrganoControllo($istanzaOggetto)) {
		echo '<div>Organo di controllo (art.20 d.lgs 30 giugno 2011, n.123): '.$istanzaOggetto['ruolo_politico'].'</div>';
	}
	
	if($istanzaOggetto['delega'] == 1 and $istanzaOggetto['testo_delega'] != '') {
		echo '<div><h4 class="trasp_sottotitolo"> Consigliere con delega a: </div><div>'.$istanzaOggetto['testo_delega'].'</h4></div>';
	}
	
	echo '<div class="reset"></div>';
	
	echo '<h4 class="trasp_sottotitolo">Contatti</h4>';
	
	if($istanzaOggetto['email_non_disponibile'] == 1 and $istanzaOggetto['email_non_disponibile_text'] != '') {
		echo '<div class="">Email: '.$istanzaOggetto['email_non_disponibile_text'].'</div>';
	} else if($istanzaOggetto['email'] != '') {
		echo '<div class="">Email: <a href="mailto:'.$istanzaOggetto['email'].'">'.$istanzaOggetto['email'].'</a></div>';
	}
	if($istanzaOggetto['email_cert'] != '') {
		echo '<div class="">Email certificata: <a href="mailto:'.$istanzaOggetto['email_cert'].'">'.$istanzaOggetto['email_cert'].'</a></div>';
	}
	if($istanzaOggetto['telefono'] != '') {
		echo '<div class="">Telefono: '.$istanzaOggetto['telefono'].'</div>';
	}
	if($istanzaOggetto['mobile'] != '') {
		echo '<div class="">Telefono mobile: '.$istanzaOggetto['mobile'].'</div>';
	}
	if($istanzaOggetto['fax'] != '') {
		echo '<div class="">Fax: '.$istanzaOggetto['fax'].'</div>';
	}
	
	//STRUTTURE
	$docRif = new documento(13);
	$docRiferiti = $docRif->caricaDocumentiCampo('referente', $istanzaOggetto['id']);
	if (count($docRiferiti)) {
		$outputScreen = '<ul>';
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
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		foreach ($struttureAdInt as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default].' - ad interim';
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		/*
		foreach ($docRiferiti as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default];
				if($oggTmp['ad_interim']) {
					$valoreLabel .= ' - ad interim';
				}
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		*/
		$outputScreen .= '</ul>';
		echo '<h4 class="trasp_sottotitolo"> Referente per le strutture </h4>'.$outputScreen;
		unset ($outputScreen);
	}
	unset ($docRiferiti);
	unset ($docRif);
	
	//ALTRE STRUTTURE
	if (trim($istanzaOggetto['uffici']) != '' and $istanzaOggetto['uffici'] != 0) {
		$idOggMulti = explode(',', $istanzaOggetto['uffici']);
		$outputScreen = '';
		foreach ((array)$idOggMulti as $idOggTmp) {
			$istOgg = mostraDatoOggetto($idOggTmp, 13, '*');
			if (trim($istOgg['id']) > 0) {
				$strAncora = $base_url . "index.php?id_oggetto=13&amp;id_cat=" . $istOgg['id_sezione'] . "&amp;id_doc=" . $idOggTmp;
				if ($outputScreen != '') {
					$outputScreen .= ', ';
				}
				$outputScreen .= '<a href="'.$strAncora.'">'.$istOgg['nome_ufficio'].'</a>';
			}
		}
		echo '<div class=""> Strutture organizzative: '.$outputScreen.'</div>';
		unset ($outputScreen);
	}
	
	//PROCEDIMENTI COME RESPONSABILE PROCEDIMENTO
	$docRif = new documento(16);
	$docRiferiti = $docRif->caricaDocumentiCampo('referente_proc', $istanzaOggetto['id']);
	if (count($docRiferiti)) {
		$outputScreen = '<ul>';
		foreach ($docRiferiti as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=16&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default];
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		$outputScreen .= '</ul>';
		echo '<h4 class="trasp_sottotitolo"> Procedimenti seguiti come responsabile di procedimento </h4>'.$outputScreen;
		unset ($outputScreen);
	}
	unset ($docRiferiti);
	unset ($docRif);
	
	//PROCEDIMENTI COME RESPONSABILE PROVVEDIMENTO
	$docRif = new documento(16);
	$docRiferiti = $docRif->caricaDocumentiCampo('referente_prov', $istanzaOggetto['id']);
	if (count($docRiferiti)) {
		$outputScreen = '<ul>';
		foreach ($docRiferiti as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=16&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default];
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		$outputScreen .= '</ul>';
		echo '<h4 class="trasp_sottotitolo"> Procedimenti seguiti come responsabile di provvedimento </h4>'.$outputScreen;
		unset ($outputScreen);
	}
	unset ($docRiferiti);
	unset ($docRif);
	
	//PRESIDENTE/CAPOGRUPPO
	$docRif = new documento(43);
	$docRiferiti = $docRif->caricaDocumentiCampo('presidente', $istanzaOggetto['id']);
	if (count($docRiferiti)) {
		$outputScreen = '<ul>';
		foreach ($docRiferiti as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=43&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default];
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		$outputScreen .= '</ul>';
		echo '<h4 class="trasp_sottotitolo"> Presidente o capogruppo per </h4>'.$outputScreen;
		unset ($outputScreen);
	}
	unset ($docRiferiti);
	unset ($docRif);
	
	//MEMBRO DI
	$docRif = new documento(43);
	$docRiferiti = $docRif->caricaDocumentiCampo('membri', $istanzaOggetto['id']);
	if (count($docRiferiti)) {
		$outputScreen = '<ul>';
		foreach ($docRiferiti as $oggTmp) {
			if (is_array($oggTmp)) {
				$strAncora = $base_url . "index.php?id_oggetto=43&amp;id_cat=" . $oggTmp['id_sezione'] . "&amp;id_doc=" . $oggTmp['id'];
				$valoreLabel = $oggTmp[$docRif->campo_default];
				$outputScreen .= "<li><a href=\"" . $strAncora . "\">" . $valoreLabel . "</a></li>";
			}
		}
		$outputScreen .= '</ul>';
		echo '<h4 class="trasp_sottotitolo"> Membro di </h4>'.$outputScreen;
		unset ($outputScreen);
	}
	unset ($docRiferiti);
	unset ($docRif);
	
	//INCARICHI ASSOCIATI
	if($istanzaOggetto['incarico'] != '') {
		visualizzaOggettoIncarico($istanzaOggetto[incarico]);
	}
	echo '<div class="reset"></div>';
	
	/* inizio altri dati */
	$altriDati = array();
	if($istanzaOggetto['organo'] == '') {
		$altriDati[] = '<div class="">Contratto a tempo determinato: '.($istanzaOggetto['determinato'] == 1 ? 'si' : 'no').'</div>';
	}
	
	if($istanzaOggetto['allegato_nomina'] != '') {
		$posPunto = strrpos($istanzaOggetto['allegato_nomina'], ".");
		$estFile = strtolower(substr($istanzaOggetto['allegato_nomina'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['allegato_nomina']);
		
		if (strpos($istanzaOggetto['allegato_nomina'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['allegato_nomina'], strpos($istanzaOggetto['allegato_nomina'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['allegato_nomina'];
		}
		$altriDati[] = '<div class="trasp_allegato">Atto di nomina o proclamazione: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['allegato_nomina']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['curriculum'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy')))) {
		$posPunto = strrpos($istanzaOggetto['curriculum'], ".");
		$estFile = strtolower(substr($istanzaOggetto['curriculum'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['curriculum']);
		
		if (strpos($istanzaOggetto['curriculum'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['curriculum'], strpos($istanzaOggetto['curriculum'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['curriculum'];
		}
		$altriDati[] = '<div class="trasp_allegato">Curriculum: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['curriculum']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['retribuzione'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy')))) {
		$posPunto = strrpos($istanzaOggetto['retribuzione'], ".");
		$estFile = strtolower(substr($istanzaOggetto['retribuzione'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['retribuzione']);
		
		if (strpos($istanzaOggetto['retribuzione'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['retribuzione'], strpos($istanzaOggetto['retribuzione'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['retribuzione'];
		}
		$altriDati[] = '<div class="trasp_allegato">Ultima dichiarazione dei redditi: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['retribuzione']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['retribuzione1'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy')))) {
		$posPunto = strrpos($istanzaOggetto['retribuzione1'], ".");
		$estFile = strtolower(substr($istanzaOggetto['retribuzione1'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['retribuzione1']);
		
		if (strpos($istanzaOggetto['retribuzione1'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['retribuzione1'], strpos($istanzaOggetto['retribuzione1'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['retribuzione1'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dichiarazione dei redditi anni precedenti: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['retribuzione1']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['retribuzione2'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy')))) {
		$posPunto = strrpos($istanzaOggetto['retribuzione2'], ".");
		$estFile = strtolower(substr($istanzaOggetto['retribuzione2'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['retribuzione2']);
		
		if (strpos($istanzaOggetto['retribuzione2'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['retribuzione2'], strpos($istanzaOggetto['retribuzione2'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['retribuzione2'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dichiarazione dei redditi anni precedenti: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['retribuzione2']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['patrimonio'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy'))) and !$istanzaOggetto['__archiviata']) {
		$posPunto = strrpos($istanzaOggetto['patrimonio'], ".");
		$estFile = strtolower(substr($istanzaOggetto['patrimonio'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['patrimonio']);
		
		if (strpos($istanzaOggetto['patrimonio'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['patrimonio'], strpos($istanzaOggetto['patrimonio'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['patrimonio'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dati patrimoniali: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['patrimonio']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['patrimonio1'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy'))) and !$istanzaOggetto['__archiviata']) {
		$posPunto = strrpos($istanzaOggetto['patrimonio1'], ".");
		$estFile = strtolower(substr($istanzaOggetto['patrimonio1'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['patrimonio1']);
		
		if (strpos($istanzaOggetto['patrimonio1'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['patrimonio1'], strpos($istanzaOggetto['patrimonio1'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['patrimonio1'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dati patrimoniali anni precedenti: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['patrimonio1']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['patrimonio2'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy'))) and !$istanzaOggetto['__archiviata']) {
		$posPunto = strrpos($istanzaOggetto['patrimonio2'], ".");
		$estFile = strtolower(substr($istanzaOggetto['patrimonio2'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['patrimonio2']);
		
		if (strpos($istanzaOggetto['patrimonio2'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['patrimonio2'], strpos($istanzaOggetto['patrimonio2'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['patrimonio2'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dati patrimoniali anni precedenti: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['patrimonio2']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['patrimonio3'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy'))) and !$istanzaOggetto['__archiviata']) {
		$posPunto = strrpos($istanzaOggetto['patrimonio3'], ".");
		$estFile = strtolower(substr($istanzaOggetto['patrimonio3'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['patrimonio3']);
		
		if (strpos($istanzaOggetto['patrimonio3'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['patrimonio3'], strpos($istanzaOggetto['patrimonio3'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['patrimonio3'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dati patrimoniali anni precedenti: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['patrimonio3']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['altre_cariche'] != '') {
		$posPunto = strrpos($istanzaOggetto['altre_cariche'], ".");
		$estFile = strtolower(substr($istanzaOggetto['altre_cariche'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['altre_cariche']);
		
		if (strpos($istanzaOggetto['altre_cariche'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['altre_cariche'], strpos($istanzaOggetto['altre_cariche'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['altre_cariche'];
		}
		$altriDati[] = '<div class="trasp_allegato">Dati su altre cariche: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['altre_cariche']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if(count($altriDati)>0) {
		echo '<h4 class="trasp_sottotitolo">Altri dati</h4>';
	
		foreach((array)$altriDati as $ad) {
			echo $ad;
		}
	}
	/* fine altri dati */
	
	echo '<div class="reset"></div>';
	
	
	if($istanzaOggetto['note'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Documentazione Art. 14  e Art. 47, c. 1, Dlgs n. 33/2013; Art. 1,2,3,4 l. n. 441/1982</h4><div class="">'.$istanzaOggetto['note'].'</div></div>';
	}
	
	if($istanzaOggetto['allegato_art14'] != '') {
		$posPunto = strrpos($istanzaOggetto['allegato_art14'], ".");
		$estFile = strtolower(substr($istanzaOggetto['allegato_art14'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['allegato_art14']);
	
		if (strpos($istanzaOggetto['allegato_art14'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['allegato_art14'], strpos($istanzaOggetto['allegato_art14'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['allegato_art14'];
		}
		echo '<div class="trasp_allegato">Note e dichiarazioni Art. 14: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['allegato_art14']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['allegato2_art14'] != '') {
		$posPunto = strrpos($istanzaOggetto['allegato2_art14'], ".");
		$estFile = strtolower(substr($istanzaOggetto['allegato2_art14'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['allegato2_art14']);
	
		if (strpos($istanzaOggetto['allegato2_art14'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['allegato2_art14'], strpos($istanzaOggetto['allegato2_art14'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['allegato2_art14'];
		}
		echo '<div class="trasp_allegato">Note e dichiarazioni Art. 14: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['allegato2_art14']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['allegato3_art14'] != '') {
		$posPunto = strrpos($istanzaOggetto['allegato3_art14'], ".");
		$estFile = strtolower(substr($istanzaOggetto['allegato3_art14'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['allegato3_art14']);
	
		if (strpos($istanzaOggetto['allegato3_art14'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['allegato3_art14'], strpos($istanzaOggetto['allegato3_art14'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['allegato3_art14'];
		}
		echo '<div class="trasp_allegato">Note e dichiarazioni Art. 14: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['allegato3_art14']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['atto_conferimento'] != '' and (!moduloAttivo('privacy') OR (!$istanzaOggetto['omissis'] AND moduloAttivo('privacy')))) {
		$posPunto = strrpos($istanzaOggetto['atto_conferimento'], ".");
		$estFile = strtolower(substr($istanzaOggetto['atto_conferimento'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['atto_conferimento']);
		
		if (strpos($istanzaOggetto['atto_conferimento'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['atto_conferimento'], strpos($istanzaOggetto['atto_conferimento'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['atto_conferimento'];
		}
		echo '<div class="trasp_allegato">Atto di conferimento: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['atto_conferimento']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	echo '<div class="reset"></div>';
	
	if($istanzaOggetto['estremi_atto_conferimento'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Estremi atto di nomina o proclamazione</h4><div class="">'.$istanzaOggetto['estremi_atto_conferimento'].'</div></div>';
	}
	
	if($istanzaOggetto['compensi'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Compensi connessi alla carica</h4><div class="">'.$istanzaOggetto['compensi'].'</div></div>';
	}
	
	if($istanzaOggetto['importi_viaggi'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Importi di viaggi di servizio e missioni</h4><div class="">'.$istanzaOggetto['importi_viaggi'].'</div></div>';
	}
	
	if($istanzaOggetto['altri_incarichi'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</h4><div class="">'.$istanzaOggetto['altri_incarichi'].'</div></div>';
	}
	
	if($istanzaOggetto['dic_inconferibilita'] != '') {
		$posPunto = strrpos($istanzaOggetto['dic_inconferibilita'], ".");
		$estFile = strtolower(substr($istanzaOggetto['dic_inconferibilita'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['dic_inconferibilita']);
		
		if (strpos($istanzaOggetto['dic_inconferibilita'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['dic_inconferibilita'], strpos($istanzaOggetto['dic_inconferibilita'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['dic_inconferibilita'];
		}
		echo '<div class="trasp_allegato">Dichiarazione insussistenza cause inconferibilità: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['dic_inconferibilita']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	if($istanzaOggetto['dic_incompatibilita'] != '') {
		$posPunto = strrpos($istanzaOggetto['dic_incompatibilita'], ".");
		$estFile = strtolower(substr($istanzaOggetto['dic_incompatibilita'], ($posPunto +1)));
		if (!file_exists("grafica/file/small/" . $estFile . ".gif")) {
			$estFile = "generica";
		}
		$grandezza = @ filesize($uploadPath . $documento->tabella . "/" . $istanzaOggetto['dic_incompatibilita']);
		
		if (strpos($istanzaOggetto['dic_incompatibilita'], "O__O")) {
			$valoreLabel = substr($istanzaOggetto['dic_incompatibilita'], strpos($istanzaOggetto['dic_incompatibilita'], "O__O") + 4);
		} else {
			$valoreLabel = $istanzaOggetto['dic_incompatibilita'];
		}
		echo '<div class="trasp_allegato">Dichiarazione insussistenza cause incompatibilità: <a href="'.$base_url.'moduli/downloadFile.php?file='.$documento->tabella.'/'.urlencode($istanzaOggetto['dic_incompatibilita']).'">'.$valoreLabel.'</a> ('.round($grandezza/1000).' kb) <img style="vertical-align:middle" src="'.$base_url.'grafica/file/small/'.$estFile.'.gif" alt="File con estensione '.$estFile.'" /></div>';
	}
	
	echo '<div class="reset"></div>';
	
	visualizzaAllegatiDinamici($istanzaOggetto, array('classe_titolo' => 'titoloAllegati', 'classe_allegato' => 'fileAllegato'));
	
	echo '<div class="reset"></div>';
	
	if($istanzaOggetto['altre_info'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Altre informazioni</h4><div class="">'.$istanzaOggetto['altre_info'].'</div></div>';
	}
	
	formazionePersonale($istanzaOggetto);
	
	if($istanzaOggetto['__archiviata_descrizione'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Informazioni</h4><div class="">'.$istanzaOggetto['__archiviata_descrizione'].'</div></div>';
	}
	
	if($istanzaOggetto['archivio_informazioni'] != '') {
		echo '<div class=""><h4 class="trasp_sottotitolo">Archivio informazioni</h4><div class="">'.$istanzaOggetto['archivio_informazioni'].'</div></div>';
	}

	echo '<div class="reset"></div>';
	
	visualizzaDataAggiornamento($istanzaOggetto);
	
	echo '<div class="reset"></div>';
	
}
?>