<?

class controlli {

	var $controlli = array();

	// costruttore
	function controlli() {
		global $configurazione,$database,$dati_db,$datiUser,$sezioniObli;
			
		// Comincio controllando gli oggetti		
		
		// bilanci
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_bilanci WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);
		
		$this->controlli[] = array(
			'contenuto' => 'bilanci',
			'menu' => 'documentazione',
			'menuSec' => 'bilanci',
			'icona' => 'iconfa-download',
			'numero' => $controllo['totale']
		); 
		
		// incarichi
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_incarichi WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'incarichi',
			'menu' => 'pubblicazioni',
			'menuSec' => 'incarichi',
			'icona' => 'iconfa-legal',
			'numero' => $controllo['totale']
		); 
		
		// normativa
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_normativa WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'normativa',
			'menu' => 'documentazione',
			'menuSec' => 'normativa',
			'icona' => 'iconfa-download',
			'numero' => $controllo['totale']
		); 
		
		// modulistica
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_modulistica_regolamenti WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'modulistica',
			'menu' => 'documentazione',
			'menuSec' => 'modulistica',
			'icona' => 'iconfa-download',
			'numero' => $controllo['totale']
		); 
		
		// oneri
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_oneri WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'oneri informativi',
			'menu' => 'pubblicazioni',
			'menuSec' => 'oneri',
			'icona' => 'iconfa-legal',
			'numero' => $controllo['totale']
		); 
		
		// procedimenti
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_procedimenti WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'procedimenti',
			'menu' => 'organizzazione',
			'menuSec' => 'procedimenti',
			'icona' => 'iconfa-briefcase',
			'numero' => $controllo['totale']
		); 
		
		// provvedimenti
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_provvedimenti WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'provvedimenti amministrativi',
			'icona' => 'iconfa-legal',
			'menu' => 'pubblicazioni',
			'menuSec' => 'provvedimenti',
			'numero' => $controllo['totale']
		); 
		
		// personale
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_riferimenti WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'personale',
			'menu' => 'organizzazione',
			'menuSec' => 'personale',
			'icona' => 'iconfa-briefcase',
			'numero' => $controllo['totale']
		); 
		
		// strutture
		$sql = "SELECT count(id) AS totale FROM ".$dati_db['prefisso']."oggetto_uffici WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArray($result);		
		$this->controlli[] = array(
			'contenuto' => 'strutture organizzative',
			'menu' => 'organizzazione',
			'menuSec' => 'strutture',
			'icona' => 'iconfa-briefcase',
			'numero' => $controllo['totale']
		); 
		
		// sezioni di contenuto, verifico presenza del modello
		$sql = "SELECT id,id_sezione_etrasp,html_generico FROM ".$dati_db['prefisso']."oggetto_etrasp_modello WHERE id_ente=".$datiUser['id_ente_admin'];
		if ( !($result = $database->connessioneConReturn($sql)) ) {
			die('Errore durante il recupero controlli. '.$sql);
		}
		$controllo = $database->sqlArrayAss($result);
		$sezioniControllate = array();

		foreach ($sezioniObli as $sezioneObli) {
		
			$presenzaModello = false;
			foreach ((array)$controllo as $modello) {
			
				if ($sezioneObli['id'] == $modello['id_sezione_etrasp'] and !$sezioniControllate[$sezioneObli['id']]) {
					$sezioniControllate[$sezioneObli['id']] = true;
					// ho trovato un modello corrispondente
					$presenzaModello = true;
					
					$html = true;
					$modulistica = true;
					$normativa = true;
					$referenti = true;
					$regolamenti = true;
					$procedimenti = true;
					$provvedimenti = true;
					$strutture = true;
					
					if ($modello['html_generico'] == '' OR $modello['html_generico'] == '<p></p>'  OR $modello['html_generico'] == '<p>&nbsp;</p>') {
						$html = false;
					}
					
					$this->controlli[] = array(
						'contenuto' => 'sezione',
						'idSezione' => $sezioneObli['id'],
						'menu' => 'contenuti',
						'menuSec' => 'normali',
						'icona' => 'iconfa-folder-close',
						'html' => $html,
						'modulistica' => $modulistica,
						'normativa' => $normativa,
						'referenti' => $referenti,
						'regolamenti' => $regolamenti,
						'procedimenti' => $procedimenti,
						'provvedimenti' => $provvedimenti,
						'strutture' => $strutture
					); 		
				}
				
			} 
			if (!$presenzaModello) {
				// il modello non è presente, inserisco il controllo
				$this->controlli[] = array(
					'contenuto' => 'sezione',
					'idSezione' => $sezioneObli['id'],
					'menu' => 'contenuti',
					'menuSec' => 'normali',
					'icona' => 'iconfa-folder-close',
					'html' => false,
					'modulistica' => false,
					'normativa' => false,
					'referenti' => false,
					'regolamenti' => false,
					'procedimenti' => false,
					'provvedimenti' => false,
					'strutture' => false
				); 	
			}
		}
		
		
			
	}	
	
	function controllaBase($nome,$numero) {
		if (!$numero) {
			return "Nessuna informazione ancora inserita per <strong>".$nome."</strong>.";
		}
	}
	
}

?>
