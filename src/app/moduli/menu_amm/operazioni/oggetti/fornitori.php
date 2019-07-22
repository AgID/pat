<?php
include_once 'app/moduli/menu_amm/operazioni/oggetti/OperazioneDefault.php';
class fornitori extends OperazioneDefault {

	public function __construct() { }
	
	public function postInsert($arrayParametri = array()) {
		global $database, $dati_db, $enteAdmin,$datiUser,$idEnte,$configurazione,$entePubblicato;
	
		if(moduloAttivo('bandigara')) {
			$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetto_elenco_fornitori WHERE id_ente = ".$enteAdmin['id']." AND id_proprietario = ".$datiUser['id']." ORDER BY id DESC LIMIT 1";
			if(!($result = $database->connessioneConReturn($sql))) {}
			$istanzaOggetto = $database->sqlArray($result);
			$this->verificaErroriAnac($istanzaOggetto);
		}
	}
	
	public function postUpdate($arrayParametri = array()) {
		global $database, $dati_db, $enteAdmin,$datiUser,$idEnte,$configurazione,$entePubblicato;
	
		if(moduloAttivo('bandigara')) {
			$istanzaOggetto = mostraDatoOggetto(forzaNumero($_GET['id']), 41, '*');
			$this->verificaErroriAnac($istanzaOggetto);
		}
	}
	
	public function verificaErroriAnac($istanzaOggetto) {
		global $database, $dati_db, $enteAdmin;
	
		if($istanzaOggetto['id'] > 0) {
			include('app/validazioniFornitoriAVCP.php');
				
			$sql = "UPDATE ".$dati_db['prefisso']."oggetto_elenco_fornitori SET __errori_anac = ".$numErrori." WHERE id = ".$istanzaOggetto['id'];
			if(!($result = $database->connessioneConReturn($sql))) {}
		}
	}
}
?>