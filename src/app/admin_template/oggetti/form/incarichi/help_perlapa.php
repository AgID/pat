<?
$msg = '';
if($istanzaOggetto['tipo_incarico'] == 'incarichi dipendenti interni' or $_GET['tipo'] == 'incarico_dip') {
	switch($nome) {
		case 'dichiarante_codicePalpa':
		case 'conferente_pa_codicePalpa':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 100 caratteri.';
		break;
		case 'dichiarante_codiceAoolpa':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 100 caratteri. In mutua esclusione con il campo Cod. univoco Unità Org.';
		break;
		case 'dichiarante_codiceUolpa':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 100 caratteri. In mutua esclusione con il campo Cod. univoco Aree Org. Omogenee';
		break;
		case 'nominativo_cognome':
		case 'nominativo_nome':
		case 'conferente_pf_cognome':
		case 'conferente_pf_nome':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 50 caratteri.';
		break;
		case 'dichiarante_codiceFiscalePa':
		case 'nominativo_genere':
		case 'nominativo_data_nascita':
		case 'nominativo_cod_comune':
		case 'nominativo_codice_fiscale':
		case 'nominativo_qualifica':
		case 'tipologia_conferente':
		case 'conferente_pa_codiceFiscalePa':
		case 'conferente_pf_genere':
		case 'conferente_pf_dataNascita':
		case 'doveriUfficio':
		case 'inizio_incarico':
		case 'tipoCompenso':
		case 'incaricoSaldato':
		case 'fine_incarico':
		case 'oggettoPerlaPA':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica.';
		break;
		case 'conferente_pf_codComune':
		case 'conferente_pf_codiceFiscale':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Obbligatorio solo se Tipologia Conferente &egrave; persona fisica con CF rilasciato in Italia';
		break;
		case 'conferente_pg_codiceFiscale':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Obbligatorio solo se Tipologia Conferente &egrave; persona giuridica con CF rilasciato in Italia';
		break;
		case 'conferente_pg_denominazione':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 255 caratteri.';
		break;
		case 'sitoWebTrasparenza':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 500 caratteri.';
		break;
		case 'dataAutConferimento':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Non pu&ograve; essere antecedente al 01/01/2018.';
		break;
		case 'riferimento':
		case 'numero':
		case 'articolo':
		case 'comma':
		case 'dataNorma':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica solo se l\'incarico &egrave; stato conferito in applicazione di una specifica norma.';
		break;
	}
} else if($istanzaOggetto['tipo_incarico'] == 'incarichi dipendenti esterni' or $_GET['tipo'] == 'incarico_cons') {
	switch($nome) {
		case 'dichiarante_codicePalpa':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 100 caratteri.';
		break;
		case 'dichiarante_codiceAoolpa':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 100 caratteri. In mutua esclusione con il campo Cod. univoco Unità Org.';
		break;
		case 'dichiarante_codiceUolpa':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 100 caratteri. In mutua esclusione con il campo Cod. univoco Aree Org. Omogenee';
		break;
		case 'nominativo_cognome':
		case 'nominativo_nome':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 50 caratteri.';
		break;
		case 'nominativo_codice_fiscale':
		case 'nominativo_cod_comune':
		case 'percettore_pg_codiceFiscale':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Obbligatorio solo se Estero &egrave; No.';
		break;
		case 'dichiarante_codiceFiscalePa':
		case 'nominativo_genere':
		case 'nominativo_data_nascita':
		case 'tipologia_percettore':
		case 'inizio_incarico':
		case 'tipoCompenso':
		case 'incaricoSaldato':
		case 'fine_incarico':
		case 'percettore_pg_denominazione':
		case 'percettoreEstero':
		case 'tipoRapporto':
		case 'naturaConferimento':
		case 'perlapa_componentiVariabilCompenso':
		case 'riferimentoRegolamento':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica.';
		break;
		case 'attestazioneVerificaInsussistenza':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Obbligatorio sono se Tipologia percettore &egrave; Persona fisica.';
		break;
		case 'oggetto':
		case 'estremi_atti':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 200 caratteri.';
		break;
		case 'sitoWebTrasparenza':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Max. 500 caratteri.';
		break;
		case 'data_conferimento':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica. Non pu&ograve; essere antecedente al 01/01/2018.';
		break;
		case 'riferimento':
		case 'numero':
		case 'articolo':
		case 'comma':
		case 'dataNorma':
			$msg = 'Obbligatorio ai fini della comunicazione al Dipartimento della Funzione Pubblica solo se l\'incarico &egrave; stato conferito in applicazione di una specifica norma.';
		break;
	}
}



if($msg != '') {
	$testoObb .= '<span class="obbliCampo intTooltip"><a data-placement="right" data-rel="tooltip" data-original-title="'.$msg.'"><span class="icon-info-sign"></span></a></span>';
}
?>