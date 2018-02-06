<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////PAT - Portale Amministrazione Trasparente////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////versione 1.5 - //////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*
	* Copyright 2015 - AgID Agenzia per l'Italia Digitale
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
	 * install.php
	 * 
	 * @Descrizione
	 * File di installazione di PAT su una installazione attiva di ISWEB. Richiesta versione uguale o maggiore alle 3.0. Maggiori informazioni su http://www.isweb.it
	 *
	 */
 
 
/*********************************************FUNZIONI DI UTILITA' GENERALE*********************************/

//Funzione che estrae il contenuto del file archivio e una volta estratto ne sposta il contenuto nella root
function extractArchive() {
	
	global $error;
	
	//ESTRAZIONE ARCHIVIO
	$documentRoot = $_SERVER['SCRIPT_FILENAME'];
	$documentRoot = str_replace("install.php","",$documentRoot);
	/*echo $documentRoot;*/
	$zip = new ZipArchive;
	$res = $zip->open('pat.zip');
	if ($res === TRUE) {
		$zip->extractTo($documentRoot);
		$zip->close();
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#0bb339;color:#FFFFFF;\">Copia dei file avvenuta con successo</div>";
	} else {
		$error = 1;
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#FF3300;color:#FFFFFF;\">Errore durante la copia dei file PAT.</div>";
	}

	//SPOSTAMENTO DEI FILE DALLA CARTELLA ESTRATTA ALLA ROOT
	$dir = $documentRoot."pat-install"; //SORGENTE
	$dirNew = $documentRoot; //DESTINAZIONE
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				//echo '<br>Archivo: '.$file.'<br />';
				if(is_dir($file) && $file!='.' && $file!='..') {
					delTree('./'.$file);
				}
				if (rename($dir.'/'.$file,$dirNew.'/'.$file))
					{
						echo "<div style=\"font-size:10px;padding: 7px;margin:0px 0px 4px 0px;background:#fbfbfb;color:#333333;\">Spostamento file: ".$dirNew.$file."</div>";
					}
					else {
						if($file!='.' && $file!='..') {
							$error = 1;
							echo "<div style=\"font-size:10px;padding: 7px;margin:0px 0px 4px 0px;background:#fbfbfb;color:#FF3300;\">Errore durante lo spostamento del file: ".$dirNew.$file."</div>";
						}
					}
			}
			closedir($dh);
		}
	}
}

//Funzione che imposta i permessi 777 in maniera ricorsiva
function chmod_r($Path,$mod) {
    $dp = opendir($Path);
     while($File = readdir($dp)) {
       if($File != "." AND $File != "..") {
         if(is_dir($File)){
            chmod($File, $mod);
            chmod_r($Path."/".$File);
         }else{
             chmod($Path."/".$File, $mod);
         }
       }
     }
   closedir($dp);
}

//Funzione per cancellare una directory intera
function delTree($dir) { 
   $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
    } 
    return rmdir($dir); 
  } 


  
 
/*********************************************PAGINA INSTALLER*********************************/  
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="title" content="PAT - Installazione" />
<meta name="robots" content="none" />
<meta http-equiv="pragma" content="no-cache" />
<title>PAT - Installazione</title>
<style type="text/css" media="all">
body {font-size:11px;font-family:Tahoma;}
</style>
</head>
<body>
<h1>Installazione PAT - Portale Amministrazione Trasparente</h1>
<?
// forzo la disabilitazione della cache
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");       

$filename 		 = 'isweb.json';
$validCMSVersion = 3;
$iswebValid 	 = false;
$error           = 0;

//VERIFICA ESISTENZA E VALIDITA' DI ISWEB
if (file_exists($filename)) {
	$stringContent = file_get_contents("isweb.json");
	$jsonContent = json_decode($stringContent, true);
	$cmsVersion = $jsonContent[0]['version'];
	if($cmsVersion >= $validCMSVersion){
		$iswebValid = true;
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#0bb339;color:#FFFFFF;\">Verifica installazione ISWEB effettuata con successo.</div>";
	}else{
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#FF3300;color:#FFFFFF;\">ERRORE: non &egrave; possibile installare PAT. Installazione ISWEB in versione inferiore alla 3.0.</div>";
	}
} else {
    echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#FF3300;color:#FFFFFF;\">ERRORE: non &egrave; possibile installare PAT. Installazione ISWEB non presente in questa cartella.</div>";
}

if ($iswebValid) {
	echo "<div style=\"font-size:20px;padding: 7px;margin:0px 0px 4px 0px;background:#FFFFFF;color:#333333;\">Inizio procedura di installazione di PAT</div>";
	require ('./inc/config.php');
	require ('./inc/inizializzazione.php');
	
	extractArchive();

	//IMPOSTARE I PERMESSI 0777 RICORSIVAMENTE SULLA DIRECTORY 7download	
	chmod_r('./download',0777);
	chmod('./download', 0777);
	
	//LEGGERE LE INFORMAZIONI CIRCA IL DB
	$mysql_host = $dati_db['host'];
	$mysql_database = $dati_db['database'];
	$mysql_user = $dati_db['user'];
	$mysql_password = $dati_db['password'];

	$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);

	$query = file_get_contents("pat.sql");

	$stmt = $db->prepare($query);

	if ($stmt->execute()){
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#0bb339;color:#FFFFFF;\">Database istallato con successo.</div>";
	}
	else {
		$error = 1;
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#FF3300;color:#FFFFFF;\">ERRORE: istallazione del Database fallita. E' necessario eseguire un installazione manuale.</div>";
	}	 
	//RIMUOVERE pat.sql e pat.zip	
	unlink('./pat.sql');
	//unlink('./pat.zip');
	
	if($error==0) {
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#0bb339;color:#FFFFFF;\">PAT &egrave; stato istallato con successo!</div>";
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#f5f5f5;color:#333333;\">
				<strong>ISTRUZIONI PER IL PRIMO UTILIZZO</strong><br />
				<p>Effettua il login inserendo username e password all'interno della pagina raggiungibile dal pulsante ACCESSO UTENTE.</p>
				<p>Ad autenticazione avvenuta clicca sul link \"Entra in amministrazione\" situato in alto a destra della testata. Puoi scaricare il manuale operativo accedendo alla sezione \"Help Online\"</p>
              </div>";
	}else {
		echo "<div style=\"font-size:14px;padding: 7px;margin:0px 0px 4px 0px;background:#FF3300;color:#FFFFFF;\">Si &egrave; verificato un errore durante l'installazione di PAT. E' necessario eseguire un installazione manuale.</div>";
	}
}


?>

</body>
</html>