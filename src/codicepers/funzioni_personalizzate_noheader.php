<?php
/*
 * Created on 21/set/2016
 */

if ($_SERVER['HTTP_HOST'] != $dominio) {
	$sql="SELECT id,url_etrasparenza,url_etrasparenza_multidominio,cookie_dominio,cookie_nome FROM ".$dati_db['prefisso']."etrasp_enti WHERE url_etrasparenza='http://".$_SERVER['HTTP_HOST']."' OR url_etrasparenza='".$_SERVER['HTTP_HOST']."' OR url_etrasparenza_multidominio LIKE 'http://".$_SERVER['HTTP_HOST']."%' OR url_etrasparenza_multidominio LIKE '%,http://".$_SERVER['HTTP_HOST']."%' OR '".$_SERVER['HTTP_HOST']."'=CONCAT(nome_breve_ente,'.".$dominio_query_sessioni."')";
	if ( !($risultato = $database->connessioneConReturn($sql)) ) {
		//errore
	}
	$datEnteTemp = $database->sqlArray($risultato);
	if ($datEnteTemp['id']) {
		
		if($datEnteTemp['id'] == 182 and $idSezione == 19) {
			//MIT
			header("location: ".$datEnteTemp['url_etrasparenza']."/pagina61_incarichi-retribuiti-e-non-retribuiti-affidati-a-soggetti-esterni.html");
			// Chiudo la connessione al database
			$database->sqlChiudi();
			exit; 
		}
		
	}
}
?>