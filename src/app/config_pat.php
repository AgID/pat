<? ////////// CONSTRUISCO LE CARATTERISTCIHE PRINCIPALI DELLE FUNZIONI

$configurazione['back_office'] = true;

$funzioniMenu = array();

//elencare le sezioni HM che dovranno essere nascoste/eliminate dal sistema: studiare un meccanismo di pubblicazione migliore con notifica delle sezioni che saranno eliminate
$sezioniNascoste = array(790,765);

$oggettiTrasparenza = array();

$oggettiNotifichePush = array();

if(!is_array($archiviAdminEsclusi)) {
	$archiviAdminEsclusi = array();
}

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
if ($datiUser['permessi']==10 or $datiUser['permessi']==3) {
	$funzioniMenu[] = array(
		'menu' => 'enti',
		'nomePagina' => 'Enti '.$configurazione['denominazione_trasparenza'],
		'nomePercorso' => 'Enti',
		'nomeMenu' => 'Enti '.$configurazione['denominazione_trasparenza'],
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
if(!moduloAttivo('solo_accessocivico')) {
	$funzioniMenu[] = array(
		'menu' => 'ruoli',
		'nomePagina' => 'Gestione Profili ACL',
		'nomePercorso' => 'Profili ACL',
		'nomeMenu' => 'Gestione Profili ACL',
		'iconaPiccola' => 'iconfa-unlock',
		'iconaGrande' => 'iconfa-unlock',
		'descrizione' => 'Gestione dei profili Access Control List per gli amministratori'
	);
}


/////////////////////////////////MODULI PERSONALIZZATI
if ($datiUser['permessi']==10 OR $datiUser['permessi']==3) {
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

if(!moduloAttivo('solo_accessocivico')) {
	/////////////////////////////////ORGANIZZAZIONE (MULTIPLO)
	
	$funzioniSottoMenu = array();
	if(!in_array(13, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[13];
		$oggettiNotifichePush[] = 13;
	}
	
	if(!in_array(3, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[3];
		$oggettiNotifichePush[] = 3;
	}
	
	if(!in_array(59, $archiviAdminEsclusi)) {
		$oggettiTrasparenza[59] = array(
			'menu' => 'organizzazione',
			'menuSec' => 'tassiassenza',
			'nomePagina' => 'Tassi di assenza',
			'nomePercorso' => 'Tassi di assenza',
			'nomeMenu' => 'Tassi di assenza',
			'idOggetto' => 59,
			'azioneNuova' => 'Aggiungi nuovo',
			'azioneCancella' => 'Cancella elementi selezionati',
			'azioneSposta' => 'Sposta elementi selezionati',
			'titTabella' => 'Tassi di assenza inseriti',
			'descrizione' => 'Gestione dei tassi di assenza del personale',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[59];
		$oggettiNotifichePush[] = 59;
	}
	
	
	
	if(!in_array(43, $archiviAdminEsclusi)) {
		$nomeMenu = 'Commissioni e gruppi consiliari';
		if(moduloAttivo('organismi-commissioni')) {
			$nomeMenu = 'Organismi, commissioni e gruppi consiliari';
		}
		$oggettiTrasparenza[43] = array(
			'menu' => 'organizzazione',
			'menuSec' => 'commissioni',
			'nomePagina' => $nomeMenu,
			'nomePercorso' => $nomeMenu,
			'nomeMenu' => $nomeMenu,
			'idOggetto' => 43,
			'azioneNuova' => 'Aggiungi elemento',
			'azioneCancella' => 'Cancella elementi selezionati',
			'azioneSposta' => 'Sposta elementi selezionati',
			'titTabella' => 'Elementi presenti',
			'descrizione' => 'Gestione degli elementi dell\'archivio',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[43];
		$oggettiNotifichePush[] = 43;
	}
	
	if(!in_array(44, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[44];
		$oggettiNotifichePush[] = 44;
	}
	
	if(!in_array(16, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[16];
		$oggettiNotifichePush[] = 16;
	}
	
	if(moduloAttivo('aggiornamenti')) {
		if(!in_array(55, $archiviAdminEsclusi)) {
			$oggettiTrasparenza[55] = array(
				'menu' => 'organizzazione',
				'menuSec' => 'patrimonio_immobiliare',
				'nomeSemplice' => 'patrimonio immobiliare',
				'nomePagina' => 'Patrimonio immobiliare',
				'nomePercorso' => 'Patrimonio immobiliare',
				'nomeMenu' => 'Patrimonio immobiliare',
				'idOggetto' => 55,
				'azioneNuova' => 'Aggiungi un nuovo immobile',
				'azioneCancella' => 'Cancella immobili selezionati',
				'titTabella' => 'Immobili presenti',
				'descrizione' => 'Gestione del Patrimonio immobiliare',
				'importAtto' => false,
				'workflow' => true,
				'versioning' => true
			);
			$funzioniSottoMenu[] = $oggettiTrasparenza[55];
		}
		if(!in_array(56, $archiviAdminEsclusi)) {
			$oggettiTrasparenza[56] = array(
				'menu' => 'organizzazione',
				'menuSec' => 'canoni_locazione',
				'nomeSemplice' => 'canoni di locazione',
				'nomePagina' => 'Canoni di locazione',
				'nomePercorso' => 'Canoni di locazione',
				'nomeMenu' => 'Canoni di locazione',
				'idOggetto' => 56,
				'azioneNuova' => 'Aggiungi un nuovo canone',
				'azioneCancella' => 'Cancella canoni selezionati',
				'titTabella' => 'Canoni presenti',
				'descrizione' => 'Gestione dei Canoni di locazione',
				'importAtto' => false,
				'workflow' => true,
				'versioning' => true
			);
			$funzioniSottoMenu[] = $oggettiTrasparenza[56];
		}
		if(!in_array(63, $archiviAdminEsclusi)) {
			$oggettiTrasparenza[63] = array(
				'menu' => 'organizzazione',
				'menuSec' => 'controlli_rilievi',
				'nomeSemplice' => 'controlli e rilievi',
				'nomePagina' => 'Controlli e rilievi',
				'nomePercorso' => 'Controlli e rilievi',
				'nomeMenu' => 'Controlli e rilievi',
				'idOggetto' => 63,
				'azioneNuova' => 'Aggiungi un nuovo elemento',
				'azioneCancella' => 'Cancella elementi selezionati',
				'titTabella' => 'Elementi presenti',
				'descrizione' => 'Gestione dei Controlli e rilievi sull\'amministrazione',
				'importAtto' => false,
				'workflow' => true,
				'versioning' => true
			);
			$funzioniSottoMenu[] = $oggettiTrasparenza[63];
		}
	}
	
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
	if(!in_array(19, $archiviAdminEsclusi)) {
		$oggettiTrasparenza[19] = array(
			'menu' => 'documentazione',
			'menuSec' => 'regolamenti',
			'nomePagina' => 'Regolamenti e documentazione',
			'nomePercorso' => 'Regolamenti e documentazione',
			'nomeMenu' => 'Regolamenti e documentazione',
			'idOggetto' => 19,
			'azioneNuova' => 'Aggiungi un nuovo documento',
			'azioneCancella' => 'Cancella documenti selezionati',
			'azioneSposta' => 'Sposta documenti selezionati',
			'titTabella' => 'Documenti disponibili',
			'descrizione' => 'Gestione della documentazione dell\'Ente',
			'importAtto' => true,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[19];
		$oggettiNotifichePush[] = 19;
	}
	
	if(!in_array(5, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[5];
		$oggettiNotifichePush[] = 5;
	}
	
	if(!in_array(27, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[27];
		$oggettiNotifichePush[] = 27;
	}
	
	if(!in_array(29, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[29];
		$oggettiNotifichePush[] = 29;
	}
	
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
	
	if(!in_array(41, $archiviAdminEsclusi)) {
		$oggettiTrasparenza[41] = array(
			'menu' => 'pubblicazioni',
			'menuSec' => 'fornitori',
			'nomeSemplice' => 'partecipante/aggiudicatario',
			'nomePagina' => 'Elenco partecipanti/aggiudicatari',
			'nomePercorso' => 'Elenco partecipanti/aggiudicatari',
			'nomeMenu' => 'Elenco partecipanti/aggiudicatari',
			'idOggetto' => 41,
			'azioneNuova' => 'Aggiungi un nuovo partecipante/aggiudicatario',
			'azioneCancella' => 'Cancella partecipanti/aggiudicatari selezionati',
			'azioneSposta' => 'Sposta partecipanti/aggiudicatari selezionati',
			'titTabella' => 'Partecipanti/aggiudicatari presenti',
			'descrizione' => 'Gestione dell\'elenco dei fornitori dell\'Ente.',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[41];
	}
	
	if(!in_array(58, $archiviAdminEsclusi) and moduloAttivo('stazioni_appaltanti')) {
		$oggettiTrasparenza[58] = array(
				'menu' => 'pubblicazioni',
				'menuSec' => 'stazioni',
				'nomeSemplice' => 'stazione',
				'nomePagina' => 'Elenco stazioni appaltanti',
				'nomePercorso' => 'Elenco stazioni appaltanti',
				'nomeMenu' => 'Elenco stazioni appaltanti',
				'idOggetto' => 58,
				'azioneNuova' => 'Aggiungi una nuova stazione',
				'azioneCancella' => 'Cancella stazioni selezionate',
				'azioneSposta' => 'Sposta stazioni selezionate',
				'titTabella' => 'Stazioni appaltanti presenti',
				'descrizione' => 'Gestione dell\'elenco delle stazioni appaltanti.',
				'importAtto' => false,
				'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[58];
	}
	
	if(!in_array(11, $archiviAdminEsclusi)) {
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
			'descrizione' => 'Gestione delle pubblicazioni dei Bandi, Gare e  Contratti',
			'importAtto' => true,
			'openDataAmministrazione' => true,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[11];
		$oggettiNotifichePush[] = 11;
	}
	
	if(!in_array(60, $archiviAdminEsclusi)) {
		$oggettiTrasparenza[60] = array(
				'menu' => 'pubblicazioni',
				'menuSec' => 'bandiatti',
				'nomeSemplice' => 'atto',
				'nomePagina' => 'Atti delle amministrazioni',
				'nomePercorso' => 'Atti delle amministrazioni',
				'nomeMenu' => 'Bandi Gare e Contratti - Atti delle amministrazioni',
				'idOggetto' => 60,
				'azioneNuova' => 'Aggiungi un nuovo atto',
				'azioneCancella' => 'Cancella atti selezionati',
				'titTabella' => 'Atti presenti',
				'descrizione' => 'Gestione degli Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatori',
				'importAtto' => false,
				'workflow' => true,
				'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[60];
	}
	
	if(!in_array(45, $archiviAdminEsclusi)) {
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
	}
	
	if(moduloAttivo('aggiornamenti')) {
		if(!in_array(53, $archiviAdminEsclusi)) {
			$oggettiTrasparenza[53] = array(
				'menu' => 'pubblicazioni',
				'menuSec' => 'atti_programmazione',
				'nomeSemplice' => 'atto di prorgammazione',
				'nomePagina' => 'Atti di programmazione',
				'nomePercorso' => 'Atti di programmazione',
				'nomeMenu' => 'Atti di programmazione',
				'idOggetto' => 53,
				'azioneNuova' => 'Aggiungi un nuovo atto',
				'azioneCancella' => 'Cancella atti selezionati',
				'titTabella' => 'Atti di programmazione presenti',
				'descrizione' => 'Gestione degli Atti di programmazione',
				'importAtto' => false,
				'workflow' => true,
				'versioning' => true
			);
			$funzioniSottoMenu[] = $oggettiTrasparenza[53];
		}
	}
	
	if(!in_array(22, $archiviAdminEsclusi)) {
		$nomeMenu = 'Bandi di Concorso';
		if(moduloAttivo('soc-trasp')) {
			$nomeMenu = 'Selezione del personale';
		}
		$oggettiTrasparenza[22] = array(
			'menu' => 'pubblicazioni',
			'menuSec' => 'bandiconcorso',
			'nomePagina' => $nomeMenu,
			'nomePercorso' => $nomeMenu,
			'nomeMenu' => $nomeMenu,
			'idOggetto' => 22,
			'azioneNuova' => 'Aggiungi un nuovo elemento',
			'azioneCancella' => 'Cancella elementi selezionati',
			'azioneSposta' => 'Sposta elementi selezionati',
			'titTabella' => 'Elementi presenti',
			'descrizione' => 'Gestione delle pubblicazioni di '.$nomeMenu,
			'importAtto' => true,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[22];
		$oggettiNotifichePush[] = 22;
	}
	
	if(!in_array(38, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[38];
		$oggettiNotifichePush[] = 38;
	}
	
	if(!in_array(4, $archiviAdminEsclusi)) {
		$oggettiTrasparenza[4] = array(
			'menu' => 'pubblicazioni',
			'menuSec' => 'incarichi',
			'nomePagina' => 'Incarichi e consulenze',
			'nomePercorso' => 'Incarichi e consulenze',
			'nomeMenu' => 'Incarichi e consulenze',
			'idOggetto' => 4,
			'azioneNuova' => 'Aggiungi un nuovo elemento',
			'azioneCancella' => 'Cancella elementi selezionati',
			'azioneSposta' => 'Sposta elementi selezionati',
			'titTabella' => 'Incarichi e consulenze presenti',
			'descrizione' => 'Gestione degli incarichi e delle consulenze',
			'importAtto' => true,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[4];
		$oggettiNotifichePush[] = 4;
	}
	
	if(!in_array(28, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[28];
		$oggettiNotifichePush[] = 28;
	}
	
	if(!in_array(30, $archiviAdminEsclusi)) {
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
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[30];
		$oggettiNotifichePush[] = 30;
	}
	
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
	
	/////////////////////////////////ATTI DI PROGRAMMAZIONE
	if(moduloAttivo('aggiornamenti') and false) {
		$funzioniSottoMenu = array();
		$oggettiTrasparenza[65] = array(
				'menu' => 'attiprog',
				'menuSec' => 'programmazione3',
				'nomePagina' => 'Programma triennale dei lavori',
				'nomePercorso' => 'Programma triennale dei lavori',
				'nomeMenu' => 'Programma triennale dei lavori',
				'idOggetto' => 65,
				'azioneNuova' => 'Aggiungi nuovo',
				'azioneCancella' => 'Cancella elementi selezionati',
				'azioneSposta' => 'Sposta elementi selezionati',
				'titTabella' => 'Elementi disponibili',
				'descrizione' => 'Gestione dei programmi triennali dei lavori',
				'importAtto' => false,
				'workflow' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[65];
		
		$funzioniMenu[] = array(
				'menu' => 'attiprog',
				'nomePagina' => 'Atti di programmazione',
				'nomePercorso' => 'Atti di programmazione',
				'nomeMenu' => 'Atti di programmazione',
				'iconaPiccola' => 'iconfa-legal',
				'iconaGrande' => 'iconfa-legal',
				'descrizione' => 'Gestione degli atti di programmazione',
				'sottoMenu' => $funzioniSottoMenu
		);
		
	}
	
	/////////////////////////////////ELEZIONI TRASPARENTI
	if(moduloAttivo('elezioni-trasparenti')) {
	    $funzioniSottoMenu = array();
	    $oggettiTrasparenza[72] = array(
	        'menu' => 'elezioni',
	        'menuSec' => 'elezioni',
	        'nomePagina' => 'Elezioni',
	        'nomePercorso' => 'Elezioni',
	        'nomeMenu' => 'Elezioni',
	        'idOggetto' => 72,
	        'azioneNuova' => 'Aggiungi nuovo',
	        'azioneCancella' => 'Cancella elementi selezionati',
	        'azioneSposta' => 'Sposta elementi selezionati',
	        'titTabella' => 'Elementi disponibili',
	        'descrizione' => 'Gestione delle elezioni trasparenti',
	        'importAtto' => false,
	        'workflow' => true,
	        'versioning' => true
	    );
	    $funzioniSottoMenu[] = $oggettiTrasparenza[72];
	    
	    $oggettiTrasparenza[75] = array(
	        'menu' => 'elezioni',
	        'menuSec' => 'elezioni_candidati_sindaci',
	        'nomePagina' => 'Candidati Sindaci/Presidenti',
	        'nomePercorso' => 'Candidati Sindaci/Presidenti',
	        'nomeMenu' => 'Candidati Sindaci/Presidenti',
	        'idOggetto' => 75,
	        'azioneNuova' => 'Aggiungi nuovo',
	        'azioneCancella' => 'Cancella elementi selezionati',
	        'azioneSposta' => 'Sposta elementi selezionati',
	        'titTabella' => 'Elementi disponibili',
	        'descrizione' => 'Gestione dei candidati Sindaci/Presidenti per le elezioni trasparenti',
	        'importAtto' => false,
	        'workflow' => true,
	        'versioning' => true
	    );
	    $funzioniSottoMenu[] = $oggettiTrasparenza[75];
	    
	    $oggettiTrasparenza[73] = array(
	        'menu' => 'elezioni',
	        'menuSec' => 'elezioni_liste',
	        'nomePagina' => 'Liste',
	        'nomePercorso' => 'Liste',
	        'nomeMenu' => 'Liste',
	        'idOggetto' => 73,
	        'azioneNuova' => 'Aggiungi nuovo',
	        'azioneCancella' => 'Cancella elementi selezionati',
	        'azioneSposta' => 'Sposta elementi selezionati',
	        'titTabella' => 'Elementi disponibili',
	        'descrizione' => 'Gestione delle liste elettorali',
	        'importAtto' => false,
	        'workflow' => true,
	        'versioning' => true
	    );
	    $funzioniSottoMenu[] = $oggettiTrasparenza[73];
	    
	    $oggettiTrasparenza[74] = array(
	        'menu' => 'elezioni',
	        'menuSec' => 'elezioni_candidati',
	        'nomePagina' => 'Candidati',
	        'nomePercorso' => 'Candidati',
	        'nomeMenu' => 'Candidati',
	        'idOggetto' => 74,
	        'azioneNuova' => 'Aggiungi nuovo',
	        'azioneCancella' => 'Cancella elementi selezionati',
	        'azioneSposta' => 'Sposta elementi selezionati',
	        'titTabella' => 'Elementi disponibili',
	        'descrizione' => 'Gestione dei candidati per le elezioni trasparenti',
	        'importAtto' => false,
	        'workflow' => true,
	        'versioning' => true
	    );
	    $funzioniSottoMenu[] = $oggettiTrasparenza[74];
	    
	    $funzioniMenu[] = array(
	        'menu' => 'elezioni',
	        'nomePagina' => 'Elezioni trasparenti',
	        'nomePercorso' => 'Elezioni trasparenti',
	        'nomeMenu' => 'Elezioni trasparenti',
	        'iconaPiccola' => 'iconfa-book',
	        'iconaGrande' => 'iconfa-book',
	        'descrizione' => '',
	        'sottoMenu' => $funzioniSottoMenu
	    );
	}
	
	/////////////////////////////////ANTICORRUZIONE
	
	if(moduloAttivo('anticorruzione')) {
		$funzioniSottoMenu = array();
		$oggettiTrasparenza[47] = array(
			'menu' => 'anticorruzione',
			'menuSec' => 'soggetti_esterni',
			'nomePagina' => 'Esterni coinvolti nel piano anticorruzione',
			'nomePercorso' => 'Esterni coinvolti nel piano anticorruzione',
			'nomeMenu' => 'Esterni coinvolti nel piano anticorruzione',
			'idOggetto' => 47,
			'azioneNuova' => 'Aggiungi un nuovo individuo',
			'azioneCancella' => 'Cancella elementi selezionati',
			'azioneSposta' => 'Sposta elementi selezionati',
			'titTabella' => 'Elementi disponibili',
			'descrizione' => 'Gestione dei soggetti esterni coinvolti nel piano anticorruzione',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[47];
		
		$oggettiTrasparenza[48] = array(
			'menu' => 'anticorruzione',
			'menuSec' => 'rischi',
			'nomePagina' => 'Aree di rischio',
			'nomePercorso' => 'Aree di rischio',
			'nomeMenu' => 'Aree di rischio',
			'idOggetto' => 48,
			'azioneNuova' => 'Aggiungi una nuova area di rischio',
			'azioneCancella' => 'Cancella rischi selezionati',
			'azioneSposta' => 'Sposta rischi selezionati',
			'titTabella' => 'Aree di rischio disponibili',
			'descrizione' => 'Gestione delle aree di rischio',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[48];
		
		$oggettiTrasparenza[49] = array(
			'menu' => 'anticorruzione',
			'menuSec' => 'misure',
			'nomePagina' => 'Misure correttive',
			'nomePercorso' => 'Misure correttive',
			'nomeMenu' => 'Misure correttive',
			'idOggetto' => 49,
			'azioneNuova' => 'Aggiungi una nuova misura',
			'azioneCancella' => 'Cancella misure selezionate',
			'azioneSposta' => 'Sposta misure selezionate',
			'titTabella' => 'Misure correttive pubblicate',
			'descrizione' => 'Gestione della misure correttive',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[49];
		
		$oggettiTrasparenza[50] = array(
			'menu' => 'anticorruzione',
			'menuSec' => 'rotazione',
			'nomePagina' => 'Modalità di attuazione della rotazione',
			'nomePercorso' => 'Modalità di attuazione della rotazione',
			'nomeMenu' => 'Modalità di attuazione della rotazione',
			'idOggetto' => 50,
			'azioneNuova' => 'Aggiungi una nuova modalità',
			'azioneCancella' => 'Cancella modalità selezionate',
			'azioneSposta' => 'Sposta modalità selezionate',
			'titTabella' => 'Modalità pubblicate',
			'descrizione' => 'Gestione della modalità di attuazione della rotazione',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[50];
		
		$oggettiTrasparenza[51] = array(
			'menu' => 'anticorruzione',
			'menuSec' => 'direttive',
			'nomePagina' => 'Direttive interne anticorruzione',
			'nomePercorso' => 'Direttive interne anticorruzione',
			'nomeMenu' => 'Direttive interne anticorruzione',
			'idOggetto' => 51,
			'azioneNuova' => 'Aggiungi una nuova direttiva',
			'azioneCancella' => 'Cancella direttive selezionate',
			'azioneSposta' => 'Sposta direttive selezionate',
			'titTabella' => 'Direttive pubblicate',
			'descrizione' => 'Gestione delle direttive interne anticorruzione',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[51];
		
		$oggettiTrasparenza[52] = array(
			'menu' => 'anticorruzione',
			'menuSec' => 'piani',
			'nomePagina' => 'Piano anticorruzione',
			'nomePercorso' => 'Piano anticorruzione',
			'nomeMenu' => 'Piano anticorruzione',
			'idOggetto' => 52,
			'azioneNuova' => 'Aggiungi un nuovo piano',
			'azioneCancella' => 'Cancella piani selezionati',
			'azioneSposta' => 'Sposta piani selezionati',
			'titTabella' => 'Modelli di Piano anticorruzione',
			'descrizione' => 'Gestione dei modelli di piano anticorruzione',
			'importAtto' => false,
			'workflow' => true,
			'versioning' => true
		);
		$funzioniSottoMenu[] = $oggettiTrasparenza[52];
		
		$funzioniMenu[] = array(
			'menu' => 'anticorruzione',
			'nomePagina' => 'Anticorruzione',
			'nomePercorso' => 'Anticorruzione',
			'nomeMenu' => 'Anticorruzione',
			'iconaPiccola' => 'iconfa-book',
			'iconaGrande' => 'iconfa-book',
			'descrizione' => '',
			'sottoMenu' => $funzioniSottoMenu
		);
	}
	
	
	/////////////////////////////////CONTENUTI eALBO
	if(moduloAttivo('ealbo')) {
		$funzioniSottoMenu = array();
		
		//CASSINO
		if($datiUser['id_ente_admin']==139){
			$funzioniSottoMenu[] = array(
				'menu' => 'ealbo',
				'menuSec' => 'ealbo',
				'nomePagina' => 'Lista completa degli atti',
				'nomePercorso' => 'Lista completa degli atti',
				'nomeMenu' => 'Lista completa degli atti',
				'descrizione' => 'Importa gli atti inseriti su Atti Amministrativi all\'interno degli archivi della trasparenza'
			);
		}else {
			$funzioniSottoMenu[] = array(
				'menu' => 'ealbo',
				'menuSec' => 'ealbo',
				'nomePagina' => 'Lista completa degli atti',
				'nomePercorso' => 'Lista completa degli atti',
				'nomeMenu' => 'Lista completa degli atti',
				'descrizione' => 'Importa gli atti inseriti sull\'albo all\'interno degli archivi della trasparenza'
			);
		}	
		
		//CASSINO
		if($datiUser['id_ente_admin']==139){
			$funzioniMenu[] = array(
				'menu' => 'ealbo',
				'nomePagina' => 'Atti Amministrativi',
				'nomePercorso' => 'Atti Amministrativi',
				'nomeMenu' => 'Atti Amministrativi',
				'iconaPiccola' => 'iconfa-file',
				'iconaGrande' => 'iconfa-file',
				'descrizione' => 'Gestione degli atti amministrativi',
				'sottoMenu' => $funzioniSottoMenu
			);
		
		} else {
			$funzioniMenu[] = array(
				'menu' => 'ealbo',
				'nomePagina' => 'Albo Online',
				'nomePercorso' => 'Albo Online',
				'nomeMenu' => 'Albo Online',
				'iconaPiccola' => 'iconfa-file',
				'iconaGrande' => 'iconfa-file',
				'descrizione' => 'Gestione contenuti per provenienti dall\'albo online',
				'sottoMenu' => $funzioniSottoMenu
			);
		}
	}
}
/////////////////////////////////ACCESSO CIVICO
if(moduloAttivo('accessocivico')) {
	$funzioniSottoMenu = array();
	
	$oggettiTrasparenza[61] = array(
			'menu' => 'accessocivico',
			'menuSec' => 'accessocivico',
			'nomeSemplice' => 'accessocivico',
			'nomePagina' => 'Accesso civico',
			'nomePercorso' => 'Accesso civico',
			'nomeMenu' => 'Accesso civico',
			'idOggetto' => 61,
			'azioneNuova' => 'Aggiungi una nuova richeista',
			'azioneCancella' => 'Cancella richieste selezionate',
			'titTabella' => 'Richieste presenti',
			'descrizione' => 'Gestione delle richieste di Accesso Civico',
			'importAtto' => false
	);
	$funzioniSottoMenu[] = $oggettiTrasparenza[61];
	
	$funzioniMenu[] = array(
			'menu' => 'accessocivico',
			'nomePagina' => 'Accesso Civico',
			'nomePercorso' => 'Accesso Civico',
			'nomeMenu' => 'Accesso Civico',
			'iconaPiccola' => 'iconfa-comments',
			'iconaGrande' => 'iconfa-comments',
			'descrizione' => 'Gestione contenuti per provenienti dall\'albo online',
			'sottoMenu' => $funzioniSottoMenu
	);

}

if(!moduloAttivo('solo_accessocivico')) {
	/////////////////////////////////CONTENUTI TRASPARENZA
	$funzioniSottoMenu = array();
	
	if($menuSecondario == 'editpagina' or $menuSecondario == 'pagine') {
		$oggettiTrasparenza[33] = array(
				'menu' => 'contenuti',
				'menuSec' => 'editpagina',
				'nomePagina' => 'Pagine generiche',
				'nomePercorso' => 'Pagine generiche',
				'nomeMenu' => 'Pagine generiche',
				'idOggetto' => 33,
				'descrizione' => 'Gestione dei normali contenuti di pagina',
				'workflow' => true,
				'versioning' => true
		);
	} 
	if($menuSecondario == 'editpagina' and $id and !$_POST['rispostaForm']) {
		$funzioniSottoMenu[] = $oggettiTrasparenza[33];
	} else {
		$funzioniSottoMenu[] = array(
				'menu' => 'contenuti',
				'menuSec' => 'pagine',
				'nomePagina' => 'Pagine generiche',
				'nomePercorso' => 'Pagine generiche',
				'nomeMenu' => 'Pagine generiche',
				'descrizione' => 'Gestione dei normali contenuti di pagina'
		);
	}
	
	$funzioniSottoMenu[] = array(
			'menu' => 'contenuti',
			'menuSec' => 'archiviomedia',
			'nomePagina' => 'Gestione allegati',
			'nomePercorso' => 'Archivio file',
			'nomeMenu' => 'Archivio file',
			'descrizione' => 'Gestione dei file allegati per i contenuti delle pagine'
	);
	/////////////////////////////////WORKFLOW
	if(moduloAttivo('workflow')) {
		$funzioniSottoMenu[] = array(
				'menu' => 'contenuti',
				'menuSec' => 'workflow',
				'nomePagina' => 'Workflow in corso',
				'nomePercorso' => 'Workflow in corso',
				'nomeMenu' => 'Workflow in corso',
				'descrizione' => 'Gestione dei Workflow in corso'
		);
	}
	if ($datiUser['permessi']==10 OR $datiUser['permessi']==3) {
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
}

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
	
	
	$funzioniSottoMenu = array();
	$funzioniSottoMenu[] = array(
			'menu' => 'report',
			'menuSec' => 'log',
			'nomePagina' => 'Log delle attivit&agrave;',
			'nomePercorso' => 'Log delle attivit&agrave;',
			'nomeMenu' => 'Log delle attivit&agrave;',
			'descrizione' => 'Attivit&agrave; effettuate sui contenuti della trasparenza'
	);
	if($datiUser['id'] == $enteAdmin['utente_responsabile_trasparenza'] OR in_array($datiUser['id'], $enteAdmin['utenti_notifiche_sistema'])) {
		$funzioniSottoMenu[] = array(
				'menu' => 'report',
				'menuSec' => 'log_utenti',
				'nomePagina' => 'Log degli utenti',
				'nomePercorso' => 'Log degli utenti',
				'nomeMenu' => 'Log degli utenti',
				'descrizione' => 'Attivit&agrave; effettuate dagli utenti'
		);
	}
	$funzioniMenu[] = array(
			'menu' => 'report',
			'nomePagina' => 'Report e log',
			'nomePercorso' => 'Report e log',
			'nomeMenu' => 'Report e log',
			'iconaPiccola' => 'iconfa-bar-chart',
			'iconaGrande' => 'iconfa-bar-chart',
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
	'strutture','personale','societa','commissioni','procedimenti','regolamenti','modulistica','normativa','bilanci','fornitori','bandigara','avcp','atti_programmazione','bandiconcorso','sovvenzioni','incarichi','provvedimenti','oneri','soggetti_esterni','rischi','misure','rotazione','direttive','piani','patrimonio_immobiliare','canoni_locazione','stazioni','tassiassenza','bandiatti','accessocivico','controlli_rilievi','elezioni','elezioni_liste','elezioni_candidati','elezioni_candidati_sindaci'
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


////attività  e procemnti	
$sezioniObli[] = array(
	'id' => 721,
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
	include('app/config_ealbo.php');
}
?>