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
	 * pat/config_pat.php
	 * 
	 * @Descrizione
	 * Configurazione del backoffice operativo di PAT
	 *
	 */	


////////// CONSTRUISCO LE CARATTERISTCIHE PRINCIPALI DELLE FUNZIONI

$funzioniMenu = array();

$oggettiTrasparenza = array();

$oggettiNotifichePush = array();

/////////////////////////////////DESKTOP
$funzioniMenu[] = array(
	'menu' => 'desktop',
	'nomePagina' => 'Desktop Amministratore',
	'nomePercorso' => 'Desktop',
	'nomeMenu' => 'Desktop',
	'iconaPiccola' => 'iconfa-laptop',
	'iconaGrande' => 'iconfa-laptop',
	'descrizione' => 'Sommario delle funzioni e notifiche per l\'utente'
);

/////////////////////////////////ENTI
if ($datiUser['permessi']==10) {
$funzioniMenu[] = array(
	'menu' => 'enti',
	'nomePagina' => 'Enti '.$configurazione['denominazione_etrasparenza'],
	'nomePercorso' => 'Enti',
	'nomeMenu' => 'Enti '.$configurazione['denominazione_etrasparenza'],
	'iconaPiccola' => 'iconfa-home',
	'iconaGrande' => 'iconfa-home',
	'descrizione' => 'Gestione degli Enti attivi nella piattaforma',
	'sottoMenu' => array()
);
}

/////////////////////////////////UTENTI

$funzioniMenu[] = array(
	'menu' => 'utenti',
	'nomePagina' => 'Gestione Utenti',
	'nomePercorso' => 'Utenti',
	'nomeMenu' => 'Gestione Utenti',
	'iconaPiccola' => 'iconfa-user',
	'iconaGrande' => 'iconfa-user',
	'descrizione' => 'Gestione degli utenti e degli amministratori'
);

/////////////////////////////////RUOLI (PERMESSI)

$funzioniMenu[] = array(
	'menu' => 'ruoli',
	'nomePagina' => 'Gestione Profili ACL',
	'nomePercorso' => 'Profili ACL',
	'nomeMenu' => 'Gestione Profili ACL',
	'iconaPiccola' => 'iconfa-unlock',
	'iconaGrande' => 'iconfa-unlock',
	'descrizione' => 'Gestione dei profili Access Control List per gli amministratori'
);


/////////////////////////////////MODULI PERSONALIZZATI
if ($datiUser['permessi']==10) {
	$funzioniMenu[] = array(
		'menu' => 'moduli_personalizzati',
		'nomePagina' => 'Gestione Moduli personalizzati',
		'nomePercorso' => 'Moduli personalizzati',
		'nomeMenu' => 'Gestione Moduli personalizzati',
		'iconaPiccola' => 'iconfa-list',
		'iconaGrande' => 'iconfa-list',
		'descrizione' => 'Gestione dei Moduli personalizzati',
		'sottoMenu' => array()
	);
}


/////////////////////////////////ORGANIZZAZIONE (MULTIPLO)

$funzioniSottoMenu = array();
$oggettiTrasparenza[13] = array(
	'menu' => 'organizzazione',
	'menuSec' => 'strutture',
	'nomePagina' => 'Strutture organizzative',
	'nomePercorso' => 'Strutture',
	'nomeMenu' => 'Strutture organizzative',
	'idOggetto' => 13,
	'azioneNuova' => 'Aggiungi una nuova struttura',
	'azioneCancella' => 'Cancella strutture selezionate',
	'azioneSposta' => 'Sposta strutture selezionate',
	'titTabella' => 'Strutture organizzative presenti',
	'descrizione' => 'Gestione degli uffici e delle strutture dell\'Ente',
	'importAtto' => false,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[13];
$oggettiNotifichePush[] = 13;

$oggettiTrasparenza[3] = array(
	'menu' => 'organizzazione',
	'menuSec' => 'personale',
	'nomePagina' => 'Personale',
	'nomePercorso' => 'Personale',
	'nomeMenu' => 'Personale',
	'idOggetto' => 3,
	'azioneNuova' => 'Aggiungi un nuovo personale',
	'azioneCancella' => 'Cancella personale selezionati',
	'azioneSposta' => 'Sposta personale selezionati',
	'titTabella' => 'Personale inserito',
	'descrizione' => 'Gestione del personale dell\'Ente',
	'importAtto' => false,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[3];
$oggettiNotifichePush[] = 3;

if(!moduloAttivo('agid')) {
	$oggettiTrasparenza[43] = array(
		'menu' => 'organizzazione',
		'menuSec' => 'commissioni',
		'nomePagina' => 'Commissioni e gruppi consiliari',
		'nomePercorso' => 'Commissioni e gruppi consiliari',
		'nomeMenu' => 'Commissioni e gruppi consiliari',
		'idOggetto' => 43,
		'azioneNuova' => 'Aggiungi un nuovo gruppo o commissione',
		'azioneCancella' => 'Cancella commissioni selezionate',
		'azioneSposta' => 'Sposta commissioni selezionati',
		'titTabella' => 'Commissioni e gruppi consiliari inserite',
		'descrizione' => 'Gestione dei gruppi e delle commissioni dell\'ente',
		'importAtto' => false,
		'workflow' => true
	);
	$funzioniSottoMenu[] = $oggettiTrasparenza[43];
	$oggettiNotifichePush[] = 43;
}

$oggettiTrasparenza[44] = array(
	'menu' => 'organizzazione',
	'menuSec' => 'societa',
	'nomePagina' => 'Enti e societ&agrave; controllate',
	'nomePercorso' => 'Enti e societ&agrave; controllate',
	'nomeMenu' => 'Enti e societ&agrave; controllate',
	'idOggetto' => 44,
	'azioneNuova' => 'Aggiungi un nuovo ente o societ&agrave;',
	'azioneCancella' => 'Cancella enti e societ&agrave; selezionate',
	'azioneSposta' => 'Sposta enti e societ&agrave; selezionati',
	'titTabella' => 'Enti e societ&agrave; inserite',
	'descrizione' => 'Gestione degli enti controllati e delle societ&agrave; partecipate',
	'importAtto' => false,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[44];
$oggettiNotifichePush[] = 44;

$oggettiTrasparenza[16] = array(
	'menu' => 'organizzazione',
	'menuSec' => 'procedimenti',
	'nomePagina' => 'Procedimenti dell\'Ente',
	'nomePercorso' => 'Procedimenti',
	'nomeMenu' => 'Procedimenti',
	'idOggetto' => 16,
	'azioneNuova' => 'Aggiungi un nuovo procedimento',
	'azioneCancella' => 'Cancella procedimenti selezionati',
	'azioneSposta' => 'Sposta procedimenti selezionati',
	'titTabella' => 'Procedimenti censiti',
	'descrizione' => 'Gestione dei procedimenti dell\'Ente',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[16];
$oggettiNotifichePush[] = 16;

$funzioniMenu[] = array(
	'menu' => 'organizzazione',
	'nomePagina' => 'Organizzazione dell\'Ente',
	'nomePercorso' => 'Organizzazione dell\'Ente',
	'nomeMenu' => 'Organizzazione dell\'Ente',
	'iconaPiccola' => 'iconfa-briefcase',
	'iconaGrande' => 'iconfa-briefcase',
	'descrizione' => 'Gestione dell\'organizzazione e del personale dell\'Ente',
	'sottoMenu' => $funzioniSottoMenu
);	

/////////////////////////////////DOCUMENTAZIONE E MODULISTICA (MULTIPLO)

$funzioniSottoMenu = array();
$oggettiTrasparenza[19] = array(
	'menu' => 'documentazione',
	'menuSec' => 'regolamenti',
	'nomePagina' => 'Regolamenti statuti e codici',
	'nomePercorso' => 'Regolamenti statuti e codici',
	'nomeMenu' => 'Regolamenti statuti e codici',
	'idOggetto' => 19,
	'azioneNuova' => 'Aggiungi un nuovo documento',
	'azioneCancella' => 'Cancella documenti selezionati',
	'azioneSposta' => 'Sposta documenti selezionati',
	'titTabella' => 'Documenti disponibili',
	'descrizione' => 'Gestione della documentazione dell\'Ente',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[19];
$oggettiNotifichePush[] = 19;

$oggettiTrasparenza[5] = array(
	'menu' => 'documentazione',
	'menuSec' => 'modulistica',
	'nomePagina' => 'Modulistica',
	'nomePercorso' => 'Modulistica',
	'nomeMenu' => 'Modulistica',
	'idOggetto' => 5,
	'azioneNuova' => 'Aggiungi un nuovo modulo',
	'azioneCancella' => 'Cancella moduli selezionati',
	'azioneSposta' => 'Sposta moduli selezionati',
	'titTabella' => 'Moduli disponibili',
	'descrizione' => 'Gestione del personale dell\'Ente',
	'importAtto' => false,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[5];
$oggettiNotifichePush[] = 5;

$oggettiTrasparenza[27] = array(
	'menu' => 'documentazione',
	'menuSec' => 'normativa',
	'nomePagina' => 'Normativa',
	'nomePercorso' => 'Normativa',
	'nomeMenu' => 'Normativa',
	'idOggetto' => 27,
	'azioneNuova' => 'Aggiungi una nuova norma',
	'azioneCancella' => 'Cancella norme selezionate',
	'azioneSposta' => 'Sposta norme selezionate',
	'titTabella' => 'Normativa pubblicata',
	'descrizione' => 'Gestione della normativa dell\'Ente',
	'importAtto' => false,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[27];
$oggettiNotifichePush[] = 27;

$oggettiTrasparenza[29] = array(
	'menu' => 'documentazione',
	'menuSec' => 'bilanci',
	'nomePagina' => 'Bilanci',
	'nomePercorso' => 'Bilanci',
	'nomeMenu' => 'Bilanci',
	'idOggetto' => 29,
	'azioneNuova' => 'Aggiungi un nuovo bilancio',
	'azioneCancella' => 'Cancella bilanci selezionate',
	'azioneSposta' => 'Sposta bilanci selezionate',
	'titTabella' => 'Bilanci pubblicati',
	'descrizione' => 'Gestione dei bilanci dell\'Ente',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[29];
$oggettiNotifichePush[] = 29;

$funzioniMenu[] = array(
	'menu' => 'documentazione',
	'nomePagina' => 'Documenti e Moduli',
	'nomePercorso' => 'Documenti e Moduli',
	'nomeMenu' => 'Documenti e Moduli',
	'iconaPiccola' => 'iconfa-download',
	'iconaGrande' => 'iconfa-download',
	'descrizione' => '',
	'sottoMenu' => $funzioniSottoMenu
);	

/////////////////////////////////ATTI E PUBBLICAZIONI (MULTIPLO)

$funzioniSottoMenu = array();

$oggettiTrasparenza[41] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'fornitori',
	'nomeSemplice' => 'fornitore',
	'nomePagina' => 'Elenco fornitori',
	'nomePercorso' => 'Elenco fornitori',
	'nomeMenu' => 'Elenco fornitori',
	'idOggetto' => 41,
	'azioneNuova' => 'Aggiungi un nuovo fornitore',
	'azioneCancella' => 'Cancella fonritori selezionati',
	'azioneSposta' => 'Sposta fornitori selezionati',
	'titTabella' => 'Fornitori presenti',
	'descrizione' => 'Gestione dell\elenco dei fornitori dell\'Ente.',
	'importAtto' => false
);
$funzioniSottoMenu[] = $oggettiTrasparenza[41];

$oggettiTrasparenza[11] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'bandigara',
	'nomeSemplice' => 'bando gara o contratto',
	'nomePagina' => 'Bandi Gare e Contratti',
	'nomePercorso' => 'Bandi Gare e Contratti',
	'nomeMenu' => 'Bandi Gare e Contratti',
	'idOggetto' => 11,
	'azioneNuova' => 'Aggiungi nuovo',
	'azioneCancella' => 'Cancella selezionati',
	'azioneSposta' => 'Sposta selezionati',
	'titTabella' => 'Bandi e contratti presenti',
	'descrizione' => 'Gestione delle pubblicazioni dei Bandi, Contratti e Delibere a contrarre',
	'importAtto' => true,
	'openDataAmministrazione' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[11];
$oggettiNotifichePush[] = 11;

$oggettiTrasparenza[45] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'avcp',
	'nomeSemplice' => 'nome semplice',
	'nomePagina' => 'URL per ANAC',
	'nomePercorso' => 'URL per ANAC',
	'nomeMenu' => 'URL per ANAC',
	'idOggetto' => 45,
	'azioneNuova' => 'Aggiungi un nuovo URL',
	'azioneCancella' => 'Cancella URL selezionati',
	'titTabella' => 'URL per ANAC presenti',
	'descrizione' => 'Gestione degli URL per ANAC',
	'importAtto' => false
);
$funzioniSottoMenu[] = $oggettiTrasparenza[45];

$oggettiTrasparenza[22] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'bandiconcorso',
	'nomePagina' => 'Bandi di Concorso',
	'nomePercorso' => 'Bandi di Concorso',
	'nomeMenu' => 'Bandi di Concorso',
	'idOggetto' => 22,
	'azioneNuova' => 'Aggiungi un nuovo bando di concorso',
	'azioneCancella' => 'Cancella bandi di concorso selezionati',
	'azioneSposta' => 'Sposta bandi di concorso selezionati',
	'titTabella' => 'Bandi di concorso presenti',
	'descrizione' => 'Gestione delle pubblicazioni dei Bandi di Concorso',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[22];
$oggettiNotifichePush[] = 22;

$oggettiTrasparenza[38] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'sovvenzioni',
	'nomePagina' => 'Sovvenzioni e vantaggi',
	'nomePercorso' => 'Sovvenzioni e vantaggi economici',
	'nomeMenu' => 'Sovvenzioni e vantaggi economici',
	'idOggetto' => 38,
	'azioneNuova' => 'Aggiungi una nuova sovvenzione',
	'azioneCancella' => 'Cancella sovvenzioni selezionate',
	'azioneSposta' => 'Sposta sovvenzioni selezionate',
	'titTabella' => 'Sovvenzioni presenti',
	'descrizione' => 'Gestione delle sovvenzioni e dei vantaggi economici',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[38];
$oggettiNotifichePush[] = 38;

$oggettiTrasparenza[4] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'incarichi',
	'nomePagina' => 'Incarichi e consulenze',
	'nomePercorso' => 'Incarichi e consulenze',
	'nomeMenu' => 'Incarichi e consulenze',
	'idOggetto' => 4,
	'azioneNuova' => 'Aggiungi un nuovo incarico o consulenza',
	'azioneCancella' => 'Cancella incarici o consulenze selezionate',
	'azioneSposta' => 'Sposta incarichi e consulenze selezionate',
	'titTabella' => 'Incarichi e consulenze presenti',
	'descrizione' => 'Gestione degli incarichi e delle consulenze',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[4];
$oggettiNotifichePush[] = 4;

$oggettiTrasparenza[28] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'provvedimenti',
	'nomePagina' => 'Provvedimenti Amministrativi',
	'nomePercorso' => 'Provvedimenti Amministrativi',
	'nomeMenu' => 'Provvedimenti Amministrativi',
	'idOggetto' => 28,
	'azioneNuova' => 'Aggiungi un nuovo provvedimento',
	'azioneCancella' => 'Cancella provvedimenti selezionati',
	'azioneSposta' => 'Sposta provvedimenti selezionati',
	'titTabella' => 'Provvedimenti pubblicati',
	'descrizione' => 'Gestione dei provvedimenti politici e dirigenziali',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[28];
$oggettiNotifichePush[] = 28;

$oggettiTrasparenza[30] = array(
	'menu' => 'pubblicazioni',
	'menuSec' => 'oneri',
	'nomePagina' => 'Oneri informativi e obblighi',
	'nomePercorso' => 'Oneri informativi e obblighi',
	'nomeMenu' => 'Oneri informativi e obblighi',
	'idOggetto' => 30,
	'azioneNuova' => 'Aggiungi una nuovo onere o obbligo',
	'azioneCancella' => 'Cancella oneri e obblighi selezionati',
	'azioneSposta' => 'Sposta oneri selezionati',
	'titTabella' => 'Oneri informativi e obblighi amministrativi pubblicati',
	'descrizione' => 'Gestione degli oneri informativi e degli obblighi amministrativi',
	'importAtto' => true,
	'workflow' => true
);
$funzioniSottoMenu[] = $oggettiTrasparenza[30];
$oggettiNotifichePush[] = 30;

$funzioniMenu[] = array(
	'menu' => 'pubblicazioni',
	'nomePagina' => 'Atti e altre pubblicazioni',
	'nomePercorso' => 'Atti e pubblicazioni',
	'nomeMenu' => 'Atti e pubblicazioni',
	'iconaPiccola' => 'iconfa-legal',
	'iconaGrande' => 'iconfa-legal',
	'descrizione' => 'Gestione degli atti e degli Albi',
	'sottoMenu' => $funzioniSottoMenu
);	


/////////////////////////////////CONTENUTI TRASPARENZA
$funzioniSottoMenu = array();

$funzioniSottoMenu[] = array(
	'menu' => 'contenuti',
	'menuSec' => 'normali',
	'nomePagina' => 'Pagine generiche',
	'nomePercorso' => 'Pagine generiche',
	'nomeMenu' => 'Pagine generiche',
	'descrizione' => 'Gestione dei normali contenuti di pagina'
);

$funzioniSottoMenu[] = array(
	'menu' => 'contenuti',
	'menuSec' => 'archiviomedia',
	'nomePagina' => 'Gestione immagini ed allegati',
	'nomePercorso' => 'Immagini ed allegati',
	'nomeMenu' => 'Immagini ed allegati',
	'descrizione' => 'Gestione dei file allegati per i contenuti delle pagine'
);
if ($datiUser['permessi']==10) {
$funzioniSottoMenu[] = array(
	'menu' => 'contenuti',
	'menuSec' => 'speciali',
	'nomePagina' => 'Contenuti speciali',
	'nomePercorso' => 'Contenuti speciali',
	'nomeMenu' => 'Contenuti speciali',
	'descrizione' => 'Gestione di speciali contenuti obbligatori per la trasparenza'
);
}

$funzioniMenu[] = array(
	'menu' => 'contenuti',
	'nomePagina' => 'Contenuti Amministrazione Trasparente',
	'nomePercorso' => 'Contenuti Trasparenza',
	'nomeMenu' => 'Contenuti Trasparenza',
	'iconaPiccola' => 'iconfa-edit',
	'iconaGrande' => 'iconfa-edit',
	'descrizione' => 'Gestione contenuti per l\'Amministrazione Trasparente',
	'sottoMenu' => $funzioniSottoMenu
);


/////////////////////////////////CONFIGURAZIONE (MULTIPLO)
if ($datiUser['id_ente_admin']) {
	$funzioniSottoMenu = array();
	$funzioniSottoMenu[] = array(
		'menu' => 'configurazione',
		'menuSec' => 'avanzata',
		'nomePagina' => 'Configurazione avanzata',
		'nomePercorso' => 'Configurazione avanzata',
		'nomeMenu' => 'Configurazione avanzata',
		'descrizione' => 'Opzioni di configurazione avanzate del portale'
	);
	/////////////////////////////////WORKFLOW
	if(moduloAttivo('workflow')) {
		$funzioniSottoMenu[] = array(
			'menu' => 'configurazione',
			'menuSec' => 'workflow',
			'nomePagina' => 'Gestione dei Workflow',
			'nomePercorso' => 'Workflow',
			'nomeMenu' => 'Gestione dei Workflow',
			'idOggetto' => 46,
			'iconaPiccola' => 'iconfa-random',
			'iconaGrande' => 'iconfa-random',
			'azioneNuova' => 'Aggiungi un nuovo workflow',
			'azioneCancella' => 'Cancella workflow selezionati',
			'azioneSposta' => 'Sposta',
			'titTabella' => 'Workflow presenti',
			'descrizione' => 'Gestione dei Workflow',
			'importAtto' => false
		);
	}
	/*
	$funzioniSottoMenu[] = array(
		'menu' => 'configurazione',
		'menuSec' => 'wizard',
		'nomePagina' => 'Configurazione guidata',
		'nomePercorso' => 'Configurazione guidata',
		'nomeMenu' => 'Configurazione guidata',
		'descrizione' => 'Procedura guidata di configurazione del portale'
	);
	*/
	$funzioniMenu[] = array(
		'menu' => 'configurazione',
		'nomePagina' => 'Configurazione Portale',
		'nomePercorso' => 'Configurazione',
		'nomeMenu' => 'Configurazione',
		'iconaPiccola' => 'iconfa-cogs',
		'iconaGrande' => 'iconfa-cogs',
		'descrizione' => '',
		'sottoMenu' => $funzioniSottoMenu
	);
}

/*
/////////////////////////////////HELP ON LINE
$funzioniMenu[] = array(
	'menu' => 'help',
	'nomePagina' => 'Help On Line',
	'nomePercorso' => 'Help On Line',
	'nomeMenu' => 'Help On Line',
	'iconaPiccola' => 'iconfa-info-sign',
	'iconaGrande' => 'iconfa-info-sign',
	'descrizione' => 'Guida in linea all\'utilizzo della piattaforma'
);
*/

/////////////////////////////////////////// FUNZIONI OGGETTO - UTILIZZATA DAI RUOLI UTENTE
$arrayFunzioniObj = array(
	'strutture','personale','societa','commissioni','procedimenti','regolamenti','modulistica','normativa','bilanci','fornitori','bandigara','avcp','bandiconcorso','sovvenzioni','incarichi','provvedimenti','oneri'
);


////////////////////////////////////////// CONTENUTI DI SEZIONI OBBLIGATORIE
$sezioniObli = array();

//// disposizioni generali
$sezioniObli[] = array(
	'id' => 43,
	'tipo' => 'contenuto'
);

//// organizzazione
$sezioniObli[] = array(
	'id' => 709,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 710,
	'tipo' => 'contenuto'
);

//// personale
$sezioniObli[] = array(
	'id' => 54,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 609,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 63,
	'tipo' => 'contenuto'
);	
$sezioniObli[] = array(
	'id' => 53,
	'tipo' => 'contenuto'
);

//// performance
$sezioniObli[] = array(
	'id' => 44,
	'tipo' => 'contenuto'
);			
$sezioniObli[] = array(
	'id' => 715,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 56,
	'tipo' => 'contenuto'
);	
$sezioniObli[] = array(
	'id' => 57,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 716,
	'tipo' => 'contenuto'
);	

///// enti controllati
$sezioniObli[] = array(
	'id' => 718,
	'tipo' => 'contenuto'
);

$sezioniObli[] = array(
	'id' => 64,
	'tipo' => 'contenuto'
);			
$sezioniObli[] = array(
	'id' => 719,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 720,
	'tipo' => 'contenuto'
);	


////attività e procemnti	
$sezioniObli[] = array(
	'id' => 721,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 722,
	'tipo' => 'contenuto'
);	
$sezioniObli[] = array(
	'id' => 723,
	'tipo' => 'speciale'
);

///// controllli sulle imprese
$sezioniObli[] = array(
	'id' => 727,
	'tipo' => 'contenuto'
);

///// beni immobili
$sezioniObli[] = array(
	'id' => 734,
	'tipo' => 'contenuto'
);			
$sezioniObli[] = array(
	'id' => 735,
	'tipo' => 'contenuto'
);

///// controlli e rilievi
$sezioniObli[] = array(
	'id' => 736,
	'tipo' => 'contenuto'
);	


///// servizi erogati
$sezioniObli[] = array(
	'id' => 632,
	'tipo' => 'contenuto'
);
$sezioniObli[] = array(
	'id' => 62,
	'tipo' => 'contenuto'
);			
$sezioniObli[] = array(
	'id' => 738,
	'tipo' => 'contenuto'
);


///// pagamenti dell'amministrazione
$sezioniObli[] = array(
	'id' => 739,
	'tipo' => 'contenuto'
);		
$sezioniObli[] = array(
	'id' => 740,
	'tipo' => 'contenuto'
);		

///// opere pubbliche
$sezioniObli[] = array(
	'id' => 741,
	'tipo' => 'contenuto'
);		

///// pinificazione
$sezioniObli[] = array(
	'id' => 742,
	'tipo' => 'contenuto'
);		

///// info ambientali
$sezioniObli[] = array(
	'id' => 743,
	'tipo' => 'contenuto'
);		

///// strutture sanitarie
$sezioniObli[] = array(
	'id' => 744,
	'tipo' => 'contenuto'
);	

///// interventi straordinari
$sezioniObli[] = array(
	'id' => 745,
	'tipo' => 'contenuto'
);			


if(moduloAttivo('ealbo')) {
	include('pat/config_ealbo.php');
}
?>