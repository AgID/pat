<?php
$istanzaOggettoBando = mostraDatoOggetto(forzaNumero($_POST['id_copia']), 11, '*');
	
if($istanzaOggettoBando['id']) {
		
	$oggAllegati = new oggettiAdmin(57);
	$ord = 1;
	$idPerAllegati = date('yzGis');
	$prog = 0;
	$operazione = true;
		
	//se ho allegati dinamici duplico solo questi
	$allegatiDinamici = prendiListaAllegati($istanzaOggettoBando['__id_allegato_istanza']);
		
	if(count($allegatiDinamici) > 0) {
			
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

		//vecchia gestione degli allegati sui bandi
		// controllo se nei campi personalizzati ci sono dei file
		$oggBando = new oggettiAdmin(11);
		foreach ($oggBando->struttura as $campoTemp) {
			if (strpos($campoTemp['tipocampo'],'*') !== false) {
				$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);
			}
			if ($campoTemp['tipocampo'] == 'file' OR $campoTemp['tipocampo'] == 'filemedia') {
				//campo di tipo file
				if($istanzaOggettoBando[$campoTemp['nomecampo']] != '' and file_exists($uploadPath.$oggBando->tabellaOggetto."/".$istanzaOggettoBando[$campoTemp['nomecampo']])) {
						
					if(strpos($istanzaOggettoBando[$campoTemp['nomecampo']], "O__O")){
						$nomeFileReplace = substr($istanzaOggettoBando[$campoTemp['nomecampo']], strpos($istanzaOggettoBando[$campoTemp['nomecampo']], "O__O") + 4);
					} else {
						$nomeFileReplace = $istanzaOggettoBando[$campoTemp['nomecampo']];
					}
					$nomeFileReplace = str_replace("\'", "_", $nomeFileReplace);
					$nomeFileReplace = $idPerAllegati.$prog."O__O".correttoreCaratteriFile($nomeFileReplace);
						
					copy($uploadPath.$oggBando->tabellaOggetto."/".$istanzaOggettoBando[$campoTemp['nomecampo']], $uploadPath.$oggAllegati->tabellaOggetto."/".$nomeFileReplace);
						
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
							'omissis' => $istanzaOggettoBando['omissis']
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
		
	//copio tutti gli altri campi
	$istanzaOggetto['oggetto'] = html_entity_decode($istanzaOggettoBando['oggetto'], ENT_COMPAT, 'ISO-8859-1');
	$istanzaOggetto['struttura'] = $istanzaOggettoBando['struttura'];
		
	if($operazione) {
		//nulla
		$operazioneTesto = "La copia delle informazioni &egrave; stata effettuata.<br /><br /><strong>Per pubblicare i dati nell\'archivio dei Provvedimenti, completare le informazioni mancanti e salvare.</strong>";
	} else {
		// ERRORI NELL'OPERAZIONE
		$operazione = false;
		$operazioneTesto = "Problemi in fase di copia. Riprovare in seguito.";
		$codiceErrore = '#01 - Generico';
	}
		
}
	
echo '<script type="text/javascript">
    				jQuery(document).ready(function(){';
include_once("./app/admin_template/operazioni_alert.tmp");
echo '});';
echo '</script>';
?>