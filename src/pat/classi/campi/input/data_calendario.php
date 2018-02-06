<?
			if ($accessibile) {
				// versione accessibile dei campi data
				if ($valoreVero == 1) {
					// massima data possibile
					$valoreVero = visualizzaData($configurazione['data_scadenza_default'],'d/M/Y');	
				} else if (!$valoreVero) {
					// data attuale
					$valoreVero = date("d")."/".date("m")."/".date("Y");	
				} else if ($valoreVero > 1){
					$valoreVero = date("d",$valoreVero)."/".date("m",$valoreVero)."/".date("Y",$valoreVero);
				} else {
					// data da selezionare
					$valoreVero = "gg/mm/aaaa";
				}
				echo "<input ".$classeStr." type=\"text\" name=\"".$nome."\" id=\"".$nome."\" value=\"".$valoreVero."\" />"; 				
			} else {
				// verifico se passare il valore ultimo o quello interno
				if ($valoreVero == 1) {
					$valoreVero = $configurazione['data_scadenza_default'];	
				} else if (!$valoreVero) {
					$valoreVero = mktime (2,0,0,date("m"),date("d"),date("Y"));
	
				}
				pubblicaCalendario($nome,$valoreVero,$classeStr);
			}
?>
