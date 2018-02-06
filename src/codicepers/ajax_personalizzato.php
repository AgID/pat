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
	 * codicepers/ajax_personalizzato.php
	 * 
	 * @Descrizione
	 * Il file viene incluso dal file principale ajax.php e viene utilizzato dalla tecnoclogia ISWEB per estendere le risposte in ajax con azioni personalizzate. 
	 * Nel contesto PAT viene utilizzato soprattutto per le funzioni di ricerca con suggerimenti automatici.
	 *
	 */

 
// SELEZIONO L'AZIONE DA COMPIERE
switch ($_GET['azione']) {
	
	case 'getFormTrasp':
		$label = $_GET['label'];
		$tipoCampo = $_GET['tipo'];
		$nome = $_GET['nome'];
		$valoreVero = $_GET['valoreVero'];
		
		ob_start();
		creaFormTrasp($label,$tipoCampo, $nome, '', $valoreVero, '','input-xxlarge'); 
		$content = ob_get_clean();
		ob_end_flush();
		echo (($content));
		return;
	break;
	
	case 'cercaAutoAdmin':
		include ('./pat/config_pat.php');
		// controllo utente
		include('./inc/controllo_user.php'); // controllo utente ISWEB
		include('./pat/controllo_user.php'); // controllo utente PAT
		
		$q = htmlentities(utf8_decode(strtolower($_GET["term"])));
		$response = array();
		if (!$q) return;
		$limitaCaratteriOutput = 60;
		$tipoRicercaAuto = 'admin';	//non modificare 
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su tutti gli oggetti
		////////////////////////////////////////////////////////////////////////////////////////
		
		$numero = 10;
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		//file_put_contents('temp/ric.html', 'RICERCA ADMIN<br />');
		foreach((array)$oggettiTrasparenza as $idOggetto => $obj) {
			
			//file_put_contents('temp/ric.html', $nomeOggetto.'<br />', FILE_APPEND);
			
			$o = new documento($idOggetto,'si');
			$idSezioneElenco = 0;
			$nomeOggetto = $obj['nomeMenu'];
			$campo = $o->campo_default;
			$menu = $obj['menu'];
			$menuSecondario = $obj['menuSec'];
			
			if($o->campiRicerca[0] == '') {
				$o->campiRicerca = $o->campiAdmin;
			}
			
			include('codicepers/ricercaOggetti.php');
			/* vecchia versione 0.8
			foreach((array)$response as $res) {
				file_put_contents('temp/ric.html', $res['label'].'<br />', FILE_APPEND);
			}
			*/
		}
		
		if(count($response) == 0) {
			$response[] = array(
				'label' => 'Nessun risultato ',
				'objName' => 1
			);
		}
		$database->sqlChiudi();
		$encoded = json_encode($response);
		header('Content-type: application/json');
		exit($encoded);
		
	
	break;
	
	case 'cercaAuto':
	
		$qSezioni = htmlentities(utf8_decode(strtolower($_GET["term"])));
		$q = htmlentities(utf8_decode(strtolower($_GET["term"])));
		$response = array();
		if (!$q) return;
		if(!$sezioneNavigazione['template']) {
			$sezioneNavigazione['template'] = 1;
		}
		$limitaCaratteriOutput = 60;
		
		$oggi = mktime (00,0,00,date("m"),date("d"),date("Y"));
		$domani = mktime (00,00,00,date("m"),date("d")+1,date("Y"));

		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su bandi di concorso
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 22;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND data_attivazione < ".$domani." AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = " ORDER BY data_scadenza ";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su bandi di gara
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 11;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND data_attivazione < ".$domani." AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = " ORDER BY data_scadenza ";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su bilanci
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 29;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su incarichi e consulenze
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 4;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND stato_pubblicazione = '100' AND dirigente != 1 ";
		$ordinamentoPersonalizzato = "";
		$numero = 30;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su aree modulistica
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 5;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su normativa
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 27;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su oneri informativi
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 30;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su personale ente
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 3;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
				
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su procedimenti
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 16;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su provvedimenti
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 28;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su regolamenti e documentazione
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 19;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su sovvenzioni e vantaggi economici
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 38;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = " AND stato_pubblicazione = '100' ";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su strutture organizzative
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 13;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = $o->nome;
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaOggetti.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//ricerca su modello di contenuto
		////////////////////////////////////////////////////////////////////////////////////////
		
		$idOggetto = 33;
		$o = new documento($idOggetto,'si');
		$idSezioneElenco = 0;
		$nomeOggetto = "Sezioni del sito";
		$campo = $o->campo_default;
		
		$campoIdEnte = 'id_ente';
		$condizioniAggiuntive = "";
		$ordinamentoPersonalizzato = "";
		$numero = 10;
		
		include('codicepers/ricercaModelloContenuto.php');
		
		////////////////////////////////////////////////////////////////////////////////////////
		//operazioni di fine ricerca
		////////////////////////////////////////////////////////////////////////////////////////
		if(count($response) == 0) {
			$response[] = array(
				'label' => 'Nessun risultato',
				'objName' => 1
			);
		}
		$database->sqlChiudi();
		$encoded = json_encode($response);
		header('Content-type: application/json');
		exit($encoded);
		
	break;
}
?>