<?php
$idVersioning = is_numeric($_GET['idv']) ? forzaNumero($_GET['idv']) : 0;
$istanzaBackup = $oggOgg->caricaIstanzaVersioning($idVersioning);

if($istanzaBackup['id']) {
	
	//elimino gli allegati dinamici che poi saranno ripresi dal versioning
	$allegatiDinamici = prendiListaAllegati($istanzaOggetto['__id_allegato_istanza']);
	foreach((array)$allegatiDinamici as $a) {
		if($a['file_allegato'] != '') {
			@unlink($uploadPath."oggetto_allegati/".$a['file_allegato']);
		}
		$sql = "DELETE FROM ".$dati_db['prefisso']."oggetto_allegati WHERE id = ".$a['id'];
		if ( !($risultato = $database->connessioneConReturn($sql)) ) {
			die('Non posso cancellare gli allegati. Errore in ripristino versioning');
		}
	}
	
	$oggAllegati = new oggettiAdmin(57);
	$allegatiBackup = prendiListaAllegatiBackup($istanzaOggetto['__id_allegato_istanza'], $oggOgg->idOggetto, $idVersioning);
	foreach((array)$allegatiBackup as $a) {
		if($a['file_allegato'] != '') {
			$file_allegato = explode('U__U', $a['file_allegato']);
			$file_allegato = $file_allegato[1];
			
			copy($uploadPath."oggetto_allegati/temp/".$a['file_allegato'], $uploadPath."oggetto_allegati/".$file_allegato)
				or die('Non posso ripristinare file di backup. Contattare assistenza tecnica.');
			
			// correggere le entità html nei corrispettivi caratteri (es. da &agrave; a à)
			// faccio quindi il contrario di quello che viene fatto nella classe admin_oggetti.php
			$nome = html_entity_decode($a['nome'], ENT_COMPAT, 'ISO-8859-15');
			$nome = str_replace('&euro;', "€", $nome);
			
			$arrayValori = array(
					'id_proprietario' => $a['id_proprietario'],
					'data_creazione' => $a['data_creazione'],
					'id_lingua' => 0,
					'id_ente' => $idEnte,
					'id_oggetto' => $a['id_oggetto'],
					'id_documento' => $istanzaOggetto['id'],
					'__id_allegato_istanza' => $istanzaOggetto['__id_allegato_istanza'],
					'__temporaneo' => 0,
					'nome' => $nome,
					'file_allegato' => $file_allegato,
					'ordine' => $a['ordine'],
					'omissis' => $a['omissis'],
					'categoriaAllegato' => $a['categoriaAllegato'],
					'id_ori' => $a['id_ori']
			);
				
			if(!$oggAllegati->aggiungiOggetto(0, $arrayValori)) {
				die('Non posso ricreare gli allegati. Errore in ripristino versioning');
			}
		}
	}
	
	//copio tutti gli altri campi
	foreach ($oggOgg->struttura as $campoTemp) {
		if (strpos($campoTemp['tipocampo'],'*') !== false) {
			$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);
		}
		if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') {
			//se esiste allegato di backup, lo ripristino (es. foto del personale)
			if($istanzaBackup[$campoTemp['nomecampo']] != '' and file_exists($uploadPath.$oggOgg->tabellaOggetto."/temp/".$istanzaBackup[$campoTemp['nomecampo']])) {
				//lognormale('esiste: '.$uploadPath.$oggOgg->tabellaOggetto."/temp/".$istanzaBackup[$campoTemp['nomecampo']]);
				$nomeFile = explode('U__U', $istanzaBackup[$campoTemp['nomecampo']]);
				$nomeFile = $nomeFile[1];
				copy($uploadPath.$oggOgg->tabellaOggetto."/temp/".$istanzaBackup[$campoTemp['nomecampo']], $uploadPath.$oggOgg->tabellaOggetto."/".$nomeFile)
					or die('Non posso ripristinare file di backup. Contattare assistenza tecnica.');
				//lognormale('copio il file '.$uploadPath.$oggOgg->tabellaOggetto."/temp/".$istanzaBackup[$campoTemp['nomecampo']].' in : '.$uploadPath.$oggOgg->tabellaOggetto."/".$nomeFile);
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = $nomeFile;
				
				//lognormale('file ripristinato sara: '.$nuovaIstanzaOggetto[$campoTemp['nomecampo']]);
				//lognormale('elimino il file '.$uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
				@unlink($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]);
			} else {
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = '';
			}
		} else if ($campoTemp['tipoinput'] == 'string' OR $campoTemp['tipoinput'] == 'blob' OR $campoTemp['tipoinput'] == 'text') {
			if ($campoTemp['tipocampo'] != 'editor') {
				// correggere le entità html nei corrispettivi caratteri (es. da &agrave; a à)
				// faccio quindi il contrario di quello che viene fatto nella classe admin_oggetti.php
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = html_entity_decode ($istanzaBackup[$campoTemp['nomecampo']], ENT_COMPAT, 'ISO-8859-15');
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = addslashes(str_replace( '&euro;', "€", $nuovaIstanzaOggetto[$campoTemp['nomecampo']]));
			} else {
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = $istanzaBackup[$campoTemp['nomecampo']];
			}
		} else {
			if ($campoTemp['tipocampo'] == 'decimale') {
				//ho il valore in db come 10000.00 e deve diventare 10000,00
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = str_replace('.', ',', $istanzaBackup[$campoTemp['nomecampo']]);
			} else {
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = $istanzaBackup[$campoTemp['nomecampo']];
			}
		}
	}
		
	//reset di alcuni valori
	$nuovaIstanzaOggetto['id_proprietario'] = $istanzaOggetto['id_proprietario'];
	$nuovaIstanzaOggetto['id_lingua'] = 0;
	$nuovaIstanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
	$nuovaIstanzaOggetto['ultima_modifica'] = mktime();
	
	if(!isset($istanzaBackup['data_revisione']) or $istanzaBackup['data_revisione'] == '') {
		$nuovaIstanzaOggetto['data_revisione'] = 'NULL';
	}
	if(!isset($istanzaBackup['data_notifica']) or $istanzaBackup['data_notifica'] == '') {
		$nuovaIstanzaOggetto['data_notifica'] = 'NULL';
	}

	if ($oggOgg->modificaOggetto($istanzaOggetto['id'], $istanzaOggetto['id_sezione'], $nuovaIstanzaOggetto)) {
		// OPERAZIONE ANDATA A BUON FINE
		$operazione = true;
		$operazioneTesto = "<br /><strong>Ripristino versione effettuato con successo.</strong><br /><br />Puoi verificare e/o modificare ulteriormente le informazioni ripristinate.<br >Al termine delle operazioni ricordati di salvare i dati.";
			
		if($idOggetto == 11 and moduloAttivo('bandigara')) {
			$tipo = $istanzaBackup['tipologia'];
			switch($tipo) {
				//bandi ed inviti,esiti,delibere e determine a contrarre,affidamenti,avvisi pubblici,somme liquidate
				case 'bandi ed inviti':
					$_GET['tipo'] = 'bando';
					break;
				case 'lotto':
					$_GET['tipo'] = 'lotto';
					break;
				case 'esiti':
					$_GET['tipo'] = 'esito';
					break;
				case 'delibere e determine a contrarre':
					$_GET['tipo'] = 'delibera';
					break;
				case 'determina_32':
					$_GET['tipo'] = 'determina_32';
					break;
				case 'affidamenti':
					$_GET['tipo'] = 'affidamento';
					break;
				case 'avvisi pubblici':
					$_GET['tipo'] = 'avviso';
					break;
				case 'somme liquidate':
					$_GET['tipo'] = 'liquidazione';
					break;
			}
			$sottotipo = $istanzaBackup['sottotipo'];
			if($sottotipo != '') {
				$_GET['sottotipo'] = $sottotipo;
			}
		}
	} else {
		// ERRORI NELL'OPERAZIONE
		$operazione = false;
		$operazioneTesto = "Problemi in ripristino versione. Riprovare in seguito.";
		$codiceErrore = '#00 - Generico';
	}
	
	echo '<script type="text/javascript">
	    				jQuery(document).ready(function(){';
	include_once("./app/admin_template/operazioni_alert.tmp");
	echo '});';
	echo '</script>';
}
?>