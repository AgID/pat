<?
// configurazione dati principali sul database
$dati_db = array(
	'tipo' => 'mysql',
	'host' => 'localhost',
	'user' => '',
	'password' => '',
	'database' => '',
	'database_offline' => '',
	'persistenza' => FALSE,
	'prefisso' => '',
	'like' => 'LIKE'
);

// verifico se usare protezione anti CSRF
$usaCSRF = true;

$dominio = "miodominio.it";
$server_url = "https://www.miodominio.it/";      // nota lo slash finale
$server_s_url = "https://www.miodominio.it/";      // nota lo slash finale

//NOTA: da modificare per il funzionamento del controllo dell'URL chiamato in sessioni.php
$dominio_query_sessioni = 'www.miodominio.it';

$uploadPath = "./download/";
$archiviomedia = "archiviofile/";

?>
