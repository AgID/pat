<?php
$istanzaOggetto = $oggOgg->caricaOggetto(forzaNumero($_POST['id_duplicazione']));
$nuovaIstanzaOggetto = $istanzaOggetto;

if($istanzaOggetto['id']) {
		
	$idAllegatoDinamico = $idEnte.'-'.$datiUser['id'].'-'.$idOggetto.'-d-'.mktime();
		
	$oggAllegati = new oggettiAdmin(57);
	$ord = 1;
	$idPerAllegati = date('yzGis');
	$prog = 0;
	$operazione = true;
		
	//se ho allegati dinamici duplico solo questi
	$allegatiDinamici = prendiListaAllegati($istanzaOggetto['__id_allegato_istanza']);
		
	if(count($allegatiDinamici) > 0 and forzaStringa($_POST['duplicazione_includi_file'])) {

		foreach((array)$allegatiDinamici as $a) {
			////////////
			if(strpos($a['file_allegato'], "O__O")){
				$nomeFileReplace = substr($a['file_allegato'], strpos($a['file_allegato'], "O__O") + 4);
			} else {
				$nomeFileReplace = $a['file_allegato'];
			}
			// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
			$nomeFileReplace = str_replace("\'", "_", $nomeFileReplace);
			$nomeFileReplace = $idPerAllegati.$prog."O__O".correttoreCaratteriFile($nomeFileReplace);
				
			//lognormale('copio '.$uploadPath.$oggAllegati->tabellaOggetto."/".$a['file_allegato'].' su '.$uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
			copy($uploadPath.$oggAllegati->tabellaOggetto."/".$a['file_allegato'], $uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
				
			$arrayValori = array(
					'id_proprietario' => $datiUser['id'],
					'id_lingua' => 0,
					'nome' => addslashes(html_entity_decode($a['nome'], ENT_COMPAT, 'ISO-8859-1')),
					'ordine' => $ord,
					'id_ente' => $idEnte,
					'id_oggetto' => $oggOgg->idOggetto,
					'id_documento' => 0,
					'__id_allegato_istanza' => $idAllegatoDinamico,
					'__temporaneo' => '1',
					'file_allegato' => $nomeFileReplace,
					'omissis' => $a['omissis']
			);
			if (!$oggAllegati->aggiungiOggetto(0, $arrayValori)) {
				unlink($uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
				$operazione = false;
				$operazioneTesto = "Problemi in aggiunta del file ".$nomeFileReplace.". Riprovare in seguito.";
				$codiceErrore = '#01 - File Upload';
			}
			$ord++;
			$prog++;
			///////////
		}
	} else {
		//vecchia gestione degli allegati
		// controllo se nei campi personalizzati ci sono dei file
		foreach ($oggOgg->struttura as $campoTemp) {
			if (strpos($campoTemp['tipocampo'],'*') !== false) {
				$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);
			}
			if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') {
				//campo di tipo file
				if($istanzaOggetto[$campoTemp['nomecampo']] != '' and file_exists($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']]) and forzaStringa($_POST['duplicazione_includi_file'])) {
						
					if(strpos($istanzaOggetto[$campoTemp['nomecampo']], "O__O")){
						$nomeFileReplace = substr($istanzaOggetto[$campoTemp['nomecampo']], strpos($istanzaOggetto[$campoTemp['nomecampo']], "O__O") + 4);
					} else {
						$nomeFileReplace = $istanzaOggetto[$campoTemp['nomecampo']];
					}
					// questo e' un oggetto file, devo upparlo sul filesystem e sostituire la variabile del nome
					$nomeFileReplace = str_replace("\'", "_", $nomeFileReplace);
					$nomeFileReplace = $idPerAllegati.$prog."O__O".correttoreCaratteriFile($nomeFileReplace);
						
					copy($uploadPath.$oggOgg->tabellaOggetto."/".$istanzaOggetto[$campoTemp['nomecampo']], $uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
						
					$arrayValori = array(
							'id_proprietario' => $datiUser['id'],
							'id_lingua' => 0,
							'nome' => addslashes($campoTemp['etichetta']),
							'ordine' => $ord,
							'id_ente' => $idEnte,
							'id_oggetto' => $oggOgg->idOggetto,
							'id_documento' => 0,
							'__id_allegato_istanza' => $idAllegatoDinamico,
							'__temporaneo' => '1',
							'file_allegato' => $nomeFileReplace,
							'omissis' => $istanzaOggetto['omissis']
					);
					if (!$oggAllegati->aggiungiOggetto(0, $arrayValori)) {
						@unlink($uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
						$operazione = false;
						$operazioneTesto = "Problemi in aggiunta del file ".$nomeFileReplace.". Riprovare in seguito.";
						$codiceErrore = '#01 - File Upload';
					}
					$ord++;
					$prog++;
				}
			}
		}
	}
		
	//copio tuttii gli altri campi
	foreach ($oggOgg->struttura as $campoTemp) {
		if (strpos($campoTemp['tipocampo'],'*') !== false) {
			$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);
		}
		if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') {
			$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = '';
		} else if ($campoTemp['tipoinput'] == 'string' OR $campoTemp['tipoinput'] == 'blob' OR $campoTemp['tipoinput'] == 'text') {
			if ($campoTemp['tipocampo'] != 'editor') {
				// correggere le entità html nei corrispettivi caratteri (es. da &agrave; a à)
				// faccio quindi il contrario di quello che viene fatto nella classe admin_oggetti.php
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = html_entity_decode ($istanzaOggetto[$campoTemp['nomecampo']], ENT_COMPAT, 'ISO-8859-15');
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = str_replace( '&euro;', "€", $nuovaIstanzaOggetto[$campoTemp['nomecampo']]);
			}
		} else {
			if ($campoTemp['tipocampo'] == 'decimale') {
				//ho il valore in db come 10000.00 e deve diventare 10000,00
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = str_replace('.', ',', $istanzaOggetto[$campoTemp['nomecampo']]);
			} else {
				$nuovaIstanzaOggetto[$campoTemp['nomecampo']] = $istanzaOggetto[$campoTemp['nomecampo']];
			}
		}
	}
		
	if($operazione) {
		//reset di alcuni valori
		$nuovaIstanzaOggetto['stato_workflow_da_assegnare'] = 'finale';
		$nuovaIstanzaOggetto['ultima_modifica'] = '0';
		$nuovaIstanzaOggetto['id_proprietario'] = $datiUser['id'];
		$nuovaIstanzaOggetto['__id_allegato_istanza'] = $idAllegatoDinamico;
		if(isset($nuovaIstanzaOggetto['id_ori'])) {
			unset($nuovaIstanzaOggetto['id_ori']);
		}
		if(isset($nuovaIstanzaOggetto['id_atto_albo'])) {
			unset($nuovaIstanzaOggetto['id_atto_albo']);
		}

		if ($oggOgg->aggiungiOggetto(0, $nuovaIstanzaOggetto)) {
			// OPERAZIONE ANDATA A BUON FINE
			$operazione = true;
			$operazioneTesto = "Duplicazione effettuata con successo.";
				
			if($idOggetto == 11 and moduloAttivo('bandigara')) {
				$tipo = $istanzaOggetto['tipologia'];
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
				$sottotipo = $istanzaOggetto['sottotipo'];
				if($sottotipo != '') {
					$_GET['sottotipo'] = $sottotipo;
				}
			}
				
			$idIstanza = $oggOgg->lastInsertId;
			$id = $idIstanza;
				
				
		} else {
			// ERRORI NELL'OPERAZIONE
			$operazione = false;
			$operazioneTesto = "Problemi in duplicazione. Riprovare in seguito.";
			$codiceErrore = '#00 - Generico';
		}
	} else {
		// ERRORI NELL'OPERAZIONE
		$operazione = false;
		$operazioneTesto = "Problemi in duplicazione. Riprovare in seguito.";
		$codiceErrore = '#01 - Generico';
	}
		
}

echo '<script type="text/javascript">
    				jQuery(document).ready(function(){';
include_once("./app/admin_template/operazioni_alert.tmp");
echo '});';
echo '</script>';
?>