<?
ob_start();

$_POST['richiedente_ragsociale'] = forzaStringa($_POST['richiedente_ragsociale']);
$_POST['codice_richiesta'] = forzaStringa($_POST['codice_richiesta']);
$_POST['richiesta_riesame'] = forzaStringa($_POST['richiesta_riesame']);
?>

Nuova richiesta di riesame di accesso civico effettuata da <?php echo $ric['richiedente_ragsociale']; ?>.<br />
<br />
Il codice della richiesta &egrave;<br />
<br />
<strong><?php echo $ric['codice_richiesta']; ?></strong><br />
<br />
<strong>Dettagli richiesta riesame</strong><br />
<?php echo nl2br($_POST['richiesta_riesame']); ?><br />
<br />
<br />


<?
$contenutoMail = ob_get_clean();
ob_end_flush();
?>