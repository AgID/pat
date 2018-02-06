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
	 * appRegistration.php
	 * 
	 * @Descrizione
	 * File per la registrazione e l'invio di notifiche push verso dispositivi mobili che installano l'eventuale APP collegata
	 *
	 */

// inclusione configurazione 
include ('./inc/config.php'); // configurazione ISWEB
 
 
/*********************************************INIZIALIZZO AMBIENTE E VARIABILI*********************************/
/* 
inizializzazione e sanitizzazione di tutte le variabili principali del sistema viene effettuata dal servizio di inizializzazione di ISWEB
*/ 
// eseguo inizializzazione ambiente backoffice ISWEB
include ('./inc/inizializzazione_admin.php');

// sanitizzo variabili dedicate
$_POST['appid'] = forzaNumero($_POST['appid']);
$_POST['id'] = forzaNumero($_POST['id']);
$_POST['tokid'] = forzaNumero($_POST['tokid']);
$_POST['regid'] = forzaNumero($_POST['regid']);
$_POST['type'] = forzaStringa($_POST['type']);
$_POST['appversion'] = forzaStringa($_POST['appversion']);

header('Access-Control-Allow-Origin: *');

// qui costruisco la pagina
$datiUser = refreshSessione($ipUser, $idSezione);
// carico la lingua e risolvo il bug
if ($datiUser['sessione_idlingua'] == 0 or $datiUser['sessione_idlingua'] == '') {
	$datiUser['sessione_idlingua'] = 1;	
}
$lingua = caricaLingua($datiUser['sessione_idlingua']);
$idLingua = $lingua['id'];

/* 
Carico dai dati della sessione utente l'ente selezionato, nel caso di un installazione multi-ente
*/
$idEnte = is_numeric($datiUser['id_ente']) ? $datiUser['id_ente'] : 0; // ente scelto in navigazione
$idEnteAdmin = is_numeric($datiUser['id_ente_admin']) ? $datiUser['id_ente_admin'] : 0; // ente scelto in amministrazione
if ($idEnte) {
	// carico ente scelto nella variabile di sessione
	$entePubblicato = datoEnte($idEnte);
}
if ($idEnteAdmin) {
	// carico ente richiamato
	$enteAdmin = datoEnte($idEnteAdmin);	
}
$tipoEnte = datoTipoEnte($enteAdmin['tipo_ente']);


/*********************************************IMPOSTO CONFIGURAZIONE PER EVENTUALE APP COLLEGATA A PAT*********************************/
/* 
Carico dai dati della sessione utente l'ente selezionato, nel caso di un installazione multi-ente
*/
if(moduloAttivo('notifiche_push')) {
	//caricare le configurazioni delle APP
	if(file_exists("codicepers/apps/".$entePubblicato['nome_breve_ente']."/config.php")) {
		include_once("codicepers/apps/".$entePubblicato['nome_breve_ente']."/config.php");
		$configurazione['eapps'] = $app;
	}
}

switch ($_GET['azione']) {
	case 'regPushNotification':
	
		//memorizzare in oggetti_notifiche_push_devices i valori: app, tokenid, regid, type
		
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti_notifiche_push_devices WHERE tokid='".$_POST['tokid']."' AND app='".$_POST['appid']."' AND id_ente=".$idEnteAdmin." LIMIT 1";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("non riesco a verificare se esiste un device registrato per l'app: ".$sql);
		}
		$ris = $database->sqlArray($result);
		if($ris['id']) {
			echo json_encode(array('esit' => 1));
		} else {
			$sql = "INSERT INTO ".$dati_db['prefisso']."oggetti_notifiche_push_devices (app,tokid,regid,type,app_version,id_ente) 
					VALUES ('".$_POST['appid']."','".$_POST['tokid']."','".$_POST['regid']."','".$_POST['type']."','".$_POST['appversion']."',".$idEnteAdmin.")";
			if ( !($risultato = $database->connessioneConReturn($sql)) ) {
				motoreLog('permessonegato','Problemi in aggiunta del record di device per notifica push: '.$sql);
			} else {
				echo json_encode(array('esit' => 1));
			}
		}
	break;
	
	case 'sendPushNotification';
		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti_notifiche_push WHERE id=".$_POST['id']." LIMIT 1";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("non riesco a prelevare la notifica da inviare: ".$sql);
		}
		$notifica = $database->sqlArray($result);
		
		$app = $configurazione['eapps'];
		
		//invio tutte le notifiche per $app
		//GCM

		$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti_notifiche_push_devices WHERE app='".$app['id']."' AND id_ente = ".$idEnteAdmin." AND type='android'";
		if( !($result = $database->connessioneConReturn($sql)) ) {
			die("non riesco a prelevare i devices per l'invio della notifica: ".$sql);
		}
		$devices = $database->sqlArrayAss($result);

		$regids = array();
		foreach((array)$devices as $d) {
			$regids[] = $d['regid'];
		}
		//invio
		$fields = array(
			'registration_ids' => $regids,
			'data' => array(
				"message" => utf8_encode($notifica['testo_notifica_push']), 
				"title" => utf8_encode($app['nome']),
				"route" => $app['route_notifica'][$notifica['id_oggetto']]['route'].$notifica['id_documento'].$app['route_notifica'][$notifica['id_oggetto']]['oggetto']
			)
		);
		
		$headers = array(
			'Authorization: key=' . $app['gcm_api_key'],
			'Content-Type: application/json'
		);
		// Apro connessione
		$ch = curl_init();
		
		// Imposto variabili
		curl_setopt($ch, CURLOPT_URL, $app['gcm_url']);		
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		
		// Eseguo
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('Curl fallito: ' . curl_error($ch). ' - ' . $app['nome']);
		}
		
		// Chiudo connessione
		curl_close($ch);
		
		
		//APN
		if($app['ecomune']) {
			// inzializzo l'oggetto database per le connessioni
			$database_ecomune = new database($dati_db_ecomune['host'], $dati_db_ecomune['user'], $dati_db_ecomune['password'], $dati_db_ecomune['database'], $dati_db_ecomune['persistenza']);
			
			//query su DB eComune
			$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti_notifiche_push_devices WHERE app='".$app['id']."' AND type='ios'";
			if( !($result = $database_ecomune->connessioneConReturn($sql)) ) {
				die("non riesco a prelevare i devices per l'invio della notifica: ".$sql);
			}
			$devices = $database_ecomune->sqlArrayAss($result);
			$database_ecomune->sqlChiudi();
		} else {
			$sql = "SELECT * FROM ".$dati_db['prefisso']."oggetti_notifiche_push_devices WHERE app='".$app['id']."' AND id_ente = ".$idEnteAdmin." AND type='ios'";
			if( !($result = $database->connessioneConReturn($sql)) ) {
				die("non riesco a prelevare i devices per l'invio della notifica: ".$sql);
			}
			$devices = $database->sqlArrayAss($result);
		}
		
		foreach((array)$devices as $d) {
			//file_put_contents('temp/log_push_apns.html','<br>Device: '. $d['regid'] . PHP_EOL, FILE_APPEND);
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', $app['apns_cert_file']);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $app['apns_cert_file_pwd']);
		 
			// Apro connessione con APNS server
			$fp = stream_socket_client(
				$app['apns_gateway'], $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		 
			if (!$fp) {
				//file_put_contents('temp/log_push_apns.html',"<br>Errore connessione: $err $errstr" . PHP_EOL, FILE_APPEND);
			}
		 
			//file_put_contents('temp/log_push_apns.html','<br>Connesso a APNS' . PHP_EOL, FILE_APPEND);
		 
			$body['aps'] = array(
				'alert' => utf8_encode($notifica['testo_notifica_push']),
				'sound' => 'default',
				'route' => $app['route_notifica'][$notifica['id_oggetto']]['route'].$notifica['id_documento'].$app['route_notifica'][$notifica['id_oggetto']]['oggetto'],
				'title' => utf8_encode($app['nome'])
				//'type' => $type,
				//'id_message' => $id_message
			);
		 
			// Encodo come JSON
			$payload = json_encode($body);
		 
			// Costruisco notifica binaria
			$msg = chr(0) . pack('n', 32) . pack('H*', $d['regid']) . pack('n', strlen($payload)) . $payload;
		 
			// Invio al server
			$result = fwrite($fp, $msg, strlen($msg));
		 
			if (!$result) {
				//echo '<br>Messaggio non inviato' . PHP_EOL;
				//file_put_contents('temp/log_push_apns.html','<br>Messaggio non inviato' . PHP_EOL, FILE_APPEND);
			} else {
				//echo '<br>Messaggio inviato con successo' . PHP_EOL;
				//file_put_contents('temp/log_push_apns.html','<br>Messaggio inviato con successo' . PHP_EOL, FILE_APPEND);
				//file_put_contents('temp/log_push_apns.html','<br>Risultato: '. $result . PHP_EOL, FILE_APPEND);
			}
			// Close the connection to the server
			fclose($fp);
		}
		
	break;
}

$database->sqlChiudi();
?>