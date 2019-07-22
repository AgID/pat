<?
ob_start();

$_POST['richiesta_riesame'] = forzaStringa($_POST['richiesta_riesame']);
?>

Gentile <?php echo $ric['richiedente_ragsociale']; ?>,<br />
la tua richiesta di riesame &egrave; stata correttamente inviata. Il codice della tua richiesta &egrave;<br />
<br />
<strong><?php echo $ric['codice_richiesta']; ?></strong><br />
<br />
che potrai utilizzare per verificarne l'esito.<br />
<br />
Di seguito i dati della tua richiesta<br />
<br />
<strong>Dettagli richiesta riesame</strong><br />
<?php echo nl2br($_POST['richiesta_riesame']); ?><br />
<br />
<br />
Cordiali saluti,<br />
<?php echo $entePubblicato['nome_completo_ente']; ?>

<?
$contenutoMail = ob_get_clean();
ob_end_flush();
?>