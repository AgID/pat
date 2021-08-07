<?php
date_default_timezone_set('Europe/Rome');


foreach ((array)$oggetti as $oggetto) {
	if ($oggetto['id'] > 1 and $oggetto['id'] == forzaNumero($_GET['id_oggetto'])) {
		
		echo "<b>Analizzo oggetto</b> ".$oggetto['nome']." (ID: ".$oggetto['id'].") <br />";
		
		$oggOgg = new oggettiAdmin($oggetto['id']);
		
		$sql = "SELECT * FROM ".$dati_db['prefisso'].$oggetto['tabella']." ORDER BY id";
		
		echo "<b>Query</b>: ".$sql."<br />";
		
		if ($risultato = $database->connessioneConReturn($sql)) {
			$records = $database->sqlArrayAss($risultato);
			foreach((array)$records as $r) {
				foreach ($oggOgg->struttura as $campoTemp) {
					if (strpos($campoTemp['tipocampo'],'*') !== false) {
						$campoTemp['tipocampo'] = substr($campoTemp['tipocampo'], 1);	
					}
					if ($campoTemp['tipocampo'] == 'data_calendario') {
						$data = $r[$campoTemp['nomecampo']];
						if($data > 0) {
							$ora = date('H', $data);
							if($ora != '02') {
								echo "Errore nella data per il campo <b>".$campoTemp['nomecampo']."</b> del record <b>".$r['id']."</b> che vale <b>".$ora."</b> (<b>".date('d/m/Y H:i:s', $data)."</b> - Originale ".$data." - Ente: ".$r['id_ente'].")<br />";
								$nuovaData = mktime(2,0,0,date('m', $data)."", date('d', $data)."", date('Y', $data)."");
								echo "Nuova data <b>".date('d/m/Y H:i:s I', $nuovaData)."</b> (Originale ".$nuovaData.")<br />";
								$nuovaOra = date('H', $nuovaData);
								if($nuovaOra != '02') {
									echo "ATTENZIONE! Nuova ora <b>".date('H', $nuovaOra)."</b>???<br />";
								}
							}
						}
					}
				}
			}
		}
	}
	echo "<b>OGGETTO</b> ".$oggetto['nome']." (ID: ".$oggetto['id'].") <br />";
}
?>