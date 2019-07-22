<?
ob_start();

$_POST['richiedente_ragsociale'] = forzaStringa($_POST['richiedente_ragsociale']);
$_POST['codice_richiesta'] = forzaStringa($_POST['codice_richiesta']);
$_POST['richiedente_cf'] = forzaStringa($_POST['richiedente_cf']);
$_POST['richiedente_email'] = forzaStringa($_POST['richiedente_email']);
$_POST['richiedente_telefono'] = forzaStringa($_POST['richiedente_telefono']);
$_POST['richiedente_dettagli'] = forzaStringa($_POST['richiedente_dettagli']);
$_POST['oggetto'] = forzaStringa($_POST['oggetto']);
$_POST['richiesta'] = forzaStringa($_POST['richiesta']);
?>

Presa in carico della richiesta di accesso civico effettuata da <?php echo $_POST['richiedente_ragsociale']; ?>.<br />
<br />
Il codice della richiesta &egrave;<br />
<br />
<strong><?php echo $_POST['codice_richiesta']; ?></strong><br />
<br />
Di seguito i dati della richiesta<br />
<br />
<strong>Dati del richiedente</strong><br />
Cognome e nome/Ragione sociale: <?php echo $_POST['richiedente_ragsociale']; ?><br />
Codice fiscale/Partita IVA: <?php echo $_POST['richiedente_cf']; ?><br />
Email: <?php echo $_POST['richiedente_email']; ?><br />
Telefono: <?php echo $_POST['richiedente_telefono']; ?><br />
Altri recapiti: <?php echo nl2br($_POST['richiedente_dettagli']); ?><br />
<br />
<strong>Dati della richiesta</strong><br />
Oggetto: <?php echo $_POST['oggetto']; ?><br />
Richiesta: <?php echo nl2br($_POST['richiesta']); ?><br />
<br />
<br />


<?
$contenutoMail = ob_get_clean();
ob_end_flush();
?>