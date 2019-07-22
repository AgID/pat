<?php 
$visualizzaAlert = false;
$visualizzaAlertErroreAvviso = false;
$testoTooltip = '';
$numErrori = 0;

/* INCARICHIE INTERNI */
if($istanzaOggetto['tipo_incarico'] == 'incarichi dipendenti interni' and $istanzaOggetto['__perlapa_bonificato']) {
	/* AMMINISTRAZIONE DICHIARANTE */
	if(strlen($istanzaOggetto['dichiarante_codicePalpa']) > 100) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco IPA amm. dichiarante (lunghezza maggiore di 100 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['dichiarante_codicePalpa']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco IPA amm. dichiarante obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['dichiarante_codiceFiscalePa']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. fiscale amm. dichiarante obbligatorio, ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['dichiarante_codiceAoolpa']) > 100) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco Aree Org. Omogenee (lunghezza maggiore di 100 caratteri), ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['dichiarante_codiceUolpa']) > 100) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco Unità Org. (lunghezza maggiore di 100 caratteri), ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['dichiarante_codiceAoolpa']) == '' and trim($istanzaOggetto['dichiarante_codiceUolpa']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco Aree Org. Omogenee oppure Cod. univoco Unità Org. obbligatorio, ';
		$numErrori++;
	}
	/* PERCETTORE */
	if(strlen($istanzaOggetto['nominativo_cognome']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Cognome (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['nominativo_cognome']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Cognome obbligatorio, ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['nominativo_nome']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Nome (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['nominativo_nome']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Nome obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['nominativo_codice_fiscale']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Codice fiscale obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['nominativo_cod_comune']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Cod. catastale Comune di nascita/stato estero obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['nominativo_qualifica']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Qualifica obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['nominativo_data_nascita']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Data di nascita obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['nominativo_genere']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - Genere obbligatorio, ';
		$numErrori++;
	}
	/* CONFERENTE */
	if(trim($istanzaOggetto['tipologia_conferente']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Conferente - Tipologia obbligatorio, ';
		$numErrori++;
	}
	if($istanzaOggetto['tipologia_conferente'] == '1') {
		/* CONFERENTE PUBBLICO */
		if(strlen($istanzaOggetto['conferente_pa_codicePalpa']) > 100) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Cod. univoco IPA amm. conferente (lunghezza maggiore di 100 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['conferente_pa_codicePalpa']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Cod. univoco IPA amm. conferente obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['conferente_pa_codiceFiscalePa']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Cod. fiscale amm. conferente obbligatorio, ';
			$numErrori++;
		}
	} else if($istanzaOggetto['tipologia_conferente'] == '2' or $istanzaOggetto['tipologia_conferente'] == '3') {
		/* CONFERENTE PF */
		if(strlen($istanzaOggetto['conferente_pf_cognome']) > 50) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Cognome (lunghezza maggiore di 50 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['conferente_pf_cognome']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Cognome obbligatorio, ';
			$numErrori++;
		}
		if(strlen($istanzaOggetto['conferente_pf_nome']) > 50) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Nome (lunghezza maggiore di 50 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['conferente_pf_nome']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Nome obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['conferente_pf_genere']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Genere obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['conferente_pf_dataNascita']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Data di nascita obbligatorio, ';
			$numErrori++;
		}
		if($istanzaOggetto['tipologia_conferente'] == '2') {
			if(trim($istanzaOggetto['conferente_pf_codComune']) == '') {
				$visualizzaAlert = true;
				$testoTooltip .= 'Conferente - Cod. catastale Comune di nascita obbligatorio, ';
				$numErrori++;
			}
			if(trim($istanzaOggetto['conferente_pf_codiceFiscale']) == '') {
				$visualizzaAlert = true;
				$testoTooltip .= 'Conferente - Codice fiscale obbligatorio, ';
				$numErrori++;
			}
		}
	} else if($istanzaOggetto['tipologia_conferente'] == '4' or $istanzaOggetto['tipologia_conferente'] == '5') {
		/* CONFERENTE PG */
		if(strlen($istanzaOggetto['conferente_pg_denominazione']) > 255) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Denominazione (lunghezza maggiore di 255 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['conferente_pg_denominazione']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Conferente - Denominazione obbligatorio, ';
			$numErrori++;
		}
		if($istanzaOggetto['tipologia_conferente'] == '4') {
			if(trim($istanzaOggetto['conferente_pg_codiceFiscale']) == '') {
				$visualizzaAlert = true;
				$testoTooltip .= 'Conferente - Codice fiscale obbligatorio, ';
				$numErrori++;
			}
		}
	}
	/* INCARICO */
	if(trim($istanzaOggetto['oggettoPerlaPA']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Oggetto obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['dataAutConferimento']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Data autorizzazione conferimento obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['doveriUfficio']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Incarico rientrante nei doveri di ufficio obbligatorio, ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['sitoWebTrasparenza']) > 500) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Sito web trasparenza (lunghezza maggiore di 500 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['sitoWebTrasparenza']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Sito web trasparenza obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['inizio_incarico']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Data di inizio incarico obbligatorio, ';
		$numErrori++;
	}
	/* DATI ECONOMICI */
	if(trim($istanzaOggetto['tipoCompenso']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Tipologia compenso obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['tipoCompenso']) == '1' or trim($istanzaOggetto['tipoCompenso']) == '2') {
		/* PREVISTO */
		$campoCompenso = 'compenso';
		if($enteAdmin['importi_numerici']) {
			$campoCompenso .= '_valore';
		}
		if($istanzaOggetto[$campoCompenso] < 1) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Compenso deve essere maggiore di 1,00, ';
			$numErrori++;
		}
	} else if(trim($istanzaOggetto['tipoCompenso']) == '3') {
		if($istanzaOggetto[$campoCompenso] > 0) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Compenso deve essere uguale a 0,00, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['incaricoSaldato']) == '' or trim($istanzaOggetto['incaricoSaldato']) == 'Y') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Incarico saldato deve essere No, ';
			$numErrori++;
		}
	}
	if(trim($istanzaOggetto['incaricoSaldato']) == 'Y' and trim($istanzaOggetto['fine_incarico']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Fine incarico obbligatorio, ';
		$numErrori++;
	}
	/* RIFERIMENTO NORMATIVO */
	if(strlen($istanzaOggetto['numero']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento normativo - Numero (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['articolo']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento normativo - Articolo (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['comma']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento normativo - Comma (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	}
	
}





/* INCARICHIE ESTERNI */
else if($istanzaOggetto['tipo_incarico'] == 'incarichi dipendenti esterni' and $istanzaOggetto['__perlapa_bonificato']) {
	/* AMMINISTRAZIONE DICHIARANTE */
	if(strlen($istanzaOggetto['dichiarante_codicePalpa']) > 100) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco IPA amm. dichiarante (lunghezza maggiore di 100 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['dichiarante_codicePalpa']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco IPA amm. dichiarante obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['dichiarante_codiceFiscalePa']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. fiscale amm. dichiarante obbligatorio, ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['dichiarante_codiceAoolpa']) > 100) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco Aree Org. Omogenee (lunghezza maggiore di 100 caratteri), ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['dichiarante_codiceUolpa']) > 100) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco Unità Org. (lunghezza maggiore di 100 caratteri), ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['dichiarante_codiceAoolpa']) == '' and trim($istanzaOggetto['dichiarante_codiceUolpa']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Cod. univoco Aree Org. Omogenee oppure Cod. univoco Unità Org. obbligatorio, ';
		$numErrori++;
	}
	/* PERCETTORE */
	if(trim($istanzaOggetto['tipologia_percettore']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Percettore - tipologia obbligatorio, ';
		$numErrori++;
	} else if(trim($istanzaOggetto['tipologia_percettore']) == 'F') {
		if(strlen($istanzaOggetto['nominativo_cognome']) > 50) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Cognome (lunghezza maggiore di 50 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['nominativo_cognome']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Cognome obbligatorio, ';
			$numErrori++;
		}
		if(strlen($istanzaOggetto['nominativo_nome']) > 50) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Nome (lunghezza maggiore di 50 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['nominativo_nome']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Nome obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['nominativo_data_nascita']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Data di nascita obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['nominativo_genere']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Genere obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['percettoreEstero']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Estero obbligatorio, ';
			$numErrori++;
		} else if(trim($istanzaOggetto['percettoreEstero']) == 'N') {
			if(trim($istanzaOggetto['nominativo_cod_comune']) == '') {
				$visualizzaAlert = true;
				$testoTooltip .= 'Percettore - Cod. catastale Comune di nascita/stato estero obbligatorio, ';
				$numErrori++;
			}
			if(trim($istanzaOggetto['nominativo_codice_fiscale']) == '') {
				$visualizzaAlert = true;
				$testoTooltip .= 'Percettore - Codice fiscale obbligatorio, ';
				$numErrori++;
			}
		}
	} else if(trim($istanzaOggetto['tipologia_percettore']) == 'G') {
		if(strlen($istanzaOggetto['percettore_pg_denominazione']) > 255) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Denominazione (lunghezza maggiore di 255 caratteri), ';
			$numErrori++;
		} else if(trim($istanzaOggetto['percettore_pg_denominazione']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Denominazione obbligatorio, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['percettoreEstero']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Percettore - Estero obbligatorio, ';
			$numErrori++;
		} else if(trim($istanzaOggetto['percettoreEstero']) == 'N') {
			if(trim($istanzaOggetto['percettore_pg_codiceFiscale']) == '') {
				$visualizzaAlert = true;
				$testoTooltip .= 'Percettore - Codice fiscale obbligatorio, ';
				$numErrori++;
			}
		}
	}
	/* INCARICO */
	if(strlen($istanzaOggetto['oggetto']) > 200) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Oggetto (lunghezza maggiore di 200 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['oggetto']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Oggetto obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['data_conferimento']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Data conferimento obbligatorio, ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['sitoWebTrasparenza']) > 500) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Sito web trasparenza (lunghezza maggiore di 500 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['sitoWebTrasparenza']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Sito web trasparenza obbligatorio, ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['estremi_atti']) > 200) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Estremi atto di conferimento (lunghezza maggiore di 200 caratteri), ';
		$numErrori++;
	} else if(trim($istanzaOggetto['estremi_atti']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Estremi atto di conferimento obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['tipoRapporto']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Tipologia rapporto obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['naturaConferimento']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Natura del conferimento obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['riferimentoRegolamento']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento ad un regolamento adottato dall\'amministrazione obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['tipologia_percettore']) == 'F') {
		if(trim($istanzaOggetto['attestazioneVerificaInsussistenza']) == '') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Attestazione verifica insussistenza obbligatorio, ';
			$numErrori++;
		}
	}
	/* DATI ECONOMICI */
	if(trim($istanzaOggetto['tipoCompenso']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Tipologia compenso obbligatorio, ';
		$numErrori++;
	}
	if(trim($istanzaOggetto['tipoCompenso']) == '1' or trim($istanzaOggetto['tipoCompenso']) == '2') {
		/* PREVISTO */
		$campoCompenso = 'compenso';
		if($enteAdmin['importi_numerici']) {
			$campoCompenso .= '_valore';
		}
		if($istanzaOggetto[$campoCompenso] < 1) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Compenso deve essere maggiore di 1,00, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['perlapa_componentiVariabilCompenso']) == '') {
		    $visualizzaAlert = true;
		    $testoTooltip .= 'Indicare le Componenti variabili del compenso, ';
		    $numErrori++;
		}
	} else if(trim($istanzaOggetto['tipoCompenso']) == '3') {
		if($istanzaOggetto[$campoCompenso] > 0) {
			$visualizzaAlert = true;
			$testoTooltip .= 'Compenso deve essere uguale a 0,00, ';
			$numErrori++;
		}
		if(trim($istanzaOggetto['incaricoSaldato']) == '' or trim($istanzaOggetto['incaricoSaldato']) == 'Y') {
			$visualizzaAlert = true;
			$testoTooltip .= 'Incarico saldato deve essere No, ';
			$numErrori++;
		}
	}
	if(trim($istanzaOggetto['incaricoSaldato']) == 'Y' and trim($istanzaOggetto['fine_incarico']) == '') {
		$visualizzaAlert = true;
		$testoTooltip .= 'Fine incarico obbligatorio, ';
		$numErrori++;
	}
	/* RIFERIMENTO NORMATIVO */
	if(strlen($istanzaOggetto['numero']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento normativo - Numero (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['articolo']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento normativo - Articolo (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	}
	if(strlen($istanzaOggetto['comma']) > 50) {
		$visualizzaAlert = true;
		$testoTooltip .= 'Riferimento normativo - Comma (lunghezza maggiore di 50 caratteri), ';
		$numErrori++;
	}
	/* ALLEGATI */
	if(trim($istanzaOggetto['tipologia_percettore']) == 'F') {
	    $erroreCv = true;
	    $erroreDichiarazione = true;
	    $lista = prendiListaAllegati($istanzaOggetto['__id_allegato_istanza']);
	    foreach((array)$lista as $all) {
	        if($all['perlapa_tipo'] == '2') {
	            //cv
	            $erroreCv = false;
	        } else if($all['perlapa_tipo'] == '3') {
	            //altri incarichi
	            $erroreDichiarazione = false;
	        }
	    }
	    if($erroreCv) {
	        $visualizzaAlert = true;
	        $testoTooltip .= 'Allegato CV obbligatorio, ';
	        $numErrori++;
	    }
	    if($erroreDichiarazione) {
	        $visualizzaAlert = true;
	        $testoTooltip .= 'Allegato Dichiarazione altri incarichi obbligatorio, ';
	        $numErrori++;
	    }
	}
	
}


if($visualizzaAlert) {
	$preTt = 'I dati non validi e/o mancanti ai fini della comunicazione verso PerlaPA sono: ';
	$tt = substr($testoTooltip, 0, strlen($testoTooltip)-2);
	$testoTooltip = $preTt.$tt;
	$strumentiSelezione .= " <span class=\"intTooltip\"><a href=\"#\" data-content=\"data-content-".$istanzaOggetto['id']."\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"Clicca per dettaglio errori\" class=\"btn btn-errori-element\"><span class=\"iconfa-warning-sign\" style=\"color: #D50000; font-size: 80%\"></span></a></span>";
	$strumentiSelezione .= "<div style=\"display:none;\"><div class=\"data-content-".$istanzaOggetto['id']."\">".$preTt."<p>";
	$arrayTt = explode(', ', $tt);
	foreach((array)$arrayTt as $t) {
		$strumentiSelezione .= " - ".$t."<br />";
	}
	$strumentiSelezione .= "</p></div></div>";
}
?>