<?php
if(file_exists('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/strutture/richiamo.php')) {
	include('codicepers/template/'.$entePubblicato['nome_breve_ente'].'/oggetti/strutture/richiamo.php');
} else {
	/* template standard */
	
	$anc = 'archivio13_strutture_'.$istanzaOggetto['id_sezione'].'_'.$istanzaOggetto['id'].'.html';
	echo '<div class="titoloElenco"><a href="'.$anc.'">'.mostraDatoOggetto($istanzaOggetto['id'], 13).'</a></div>';
	
	visualizzaResponsabile($istanzaOggetto, 'grassetto');
	
	if($istanzaOggetto['pres_sede'] == 'si' and $istanzaOggetto['dett_indirizzo'] == '') {
		if($istanzaOggetto['sede'] != '') {
			$valore = $istanzaOggetto['sede'];
			// devo verificare se il punto è salvato secondo la versione v2 delle API
			if($valore[0] == '(') {
				$oldAPI = true;
				// vecchia versione, devo convertire l'indirizzo
				$valore = substr($valore, 1);
				$posInd = strrpos($valore, "|");
				if ($posInd !== false) {
					$indirizzo = substr($valore, $posInd +1);
					$valore = substr($valore, 0, $posInd);
				} else {
					//non ho l'indirizzo
					$indirizzo = $configurazione['googlemaps_default'];
				}
				// creo stringa senza zoom
				$posZoom = strrpos($valore, ",");
				if ($posZoom !== false) {
					$punto = substr($valore, 0, $posZoom);
					$posZoom = trim(substr($valore, $posZoom+1));
				}
				$posPar = strpos($punto, ")");
				if($posPar !== false) {
					$punto = substr($punto , 0, $posPar);
					$punto = explode(",", $punto);
				}
				$valore = "1_ROADMAP_".$posZoom."_0_0|lat=".trim($punto[0])."}lng=".trim($punto[1])."}titolomarker=}icona=}htmlEditor=";
			}
			$propMap = explode("|", $valore);
			$temp = explode("_", $propMap[0]);
			$numPunti = $temp[0];
			$tipomappa = $temp[1];
			$zoommappa = $temp[2];
			$indicazioni = $temp[3];
			$idStileIndicazioni = "0";	//fisso a 0 non serve nel caso di oggetti
			if($numPunti == '' or $numPunti == 0) {
				$numPunti = 1;
			}
			if($tipomappa == '') {
				$tipomappa = "ROADMAP";
			}
			if($zoommappa == '') {
				$zoommappa = "5";
			}
			if($indicazioni == '') {
				$indicazioni = "0";
			}
			if($idStileIndicazioni == '') {
				$idStileIndicazioni = "0";
			}
			$vuoto = false;
			if($numPunti == 1 and $tipomappa == 'ROADMAP' and $zoommappa == '5' and $indicazioni == '0' and $idStileIndicazioni == '0') {
				// il campo sarebbe vuoto, cioè non valorizzato in amministrazione
				$vuoto = true;
			}
			unset($temp);
			$punti = $propMap[1];
			unset($propMap);
			$addMarkers = '';
			$n = 0;
			$punti = explode("{",$punti);
		} else {
			$vuoto = true;
		}
		
		if ($vuoto == false) {
			$indirizzo = '';
			foreach ($punti as $punto) {
				$variabili = explode("}", $punto);
				foreach ($variabili as $variabile) {
					$varTemp = explode("=",$variabile);
					if($varTemp[0] == 'indirizzo') {
						if($indirizzo != '') {
							$indirizzo .= "<br />";
						}
						$indirizzo .= $varTemp[1];
					}
				}
			}
			if($indirizzo != $configurazione['googlemaps_default']) {
				echo '<div>'.$indirizzo.'</div>';
			}
			
		}
	}
	
	if($istanzaOggetto['telefono'] != '') {
		echo '<div>Telefono: '.$istanzaOggetto['telefono'].'</div>';
	}
	
	if($istanzaOggetto['email_riferimento'] != '') {
		echo '<div>Email: <a href="mailto:'.$istanzaOggetto['email_riferimento'].'">'.$istanzaOggetto['email_riferimento'].'</a></div>';
	}
	
}
?>