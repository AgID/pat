<?php
$rules = array();
switch ($idOggetto) {
	case 56:
		//canoni di locazione
		$rules[] = array('campo' => 'tipo_canone', 'rules' => array(array('nome' => 'required', 'valore' => 'true')), 'messages' => array(array('nome' => 'required', 'valore' => '"Campo obbligatorio"')));
		break;
	case 29:
		//bilanci
		break;
}

foreach((array)$rules as $rule) {
	?>
	jQuery("#<?php echo $rule['campo']; ?>").rules("add", {
		<? foreach((array)$rule['rules'] as $r) { ?>
			<?php echo $r['nome']?> : <?php echo $r['valore']; ?>,
		<? } ?>
		<?
		if(count($rule['messages']) > 0) { ?>
			messages: {
			<? foreach((array)$rule['messages'] as $m) { ?>
				<?php echo $m['nome'] ?> : <?php echo $m['valore']?>,
			<? } ?>
			}
		<? } ?>
		});
	<?
}
?>