<?php
/*
 * Created on 07/gen/2016
 */
?>
<a class="btn btn-rounded <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>"> <!-- fine tag a -->

<?
if($parametri['icona']) {
	?>
	<i class="<? echo $parametri['icona']; ?>"></i>&nbsp;
	<?
}
?>

<? echo $parametri['etichetta']; ?>

</a>

</script>
<script type="text/javascript">
jQuery('#a_fn_nominativo_codice_fiscale').on('click', function() {
	var sesso=jQuery('#nominativo_genere').val();
  	var data=jQuery('#nominativo_data_nascita').val();
	data=data.match(/^\s*(\d+).(\d+).(\d+)/)
	var codice= CFisc.calcola_codice(
		jQuery('#nominativo_nome').val(),
		jQuery('#nominativo_cognome').val(),
		sesso,
		data[1],data[2],data[3],
		jQuery('#nominativo_cod_comune').val()
	);
	jQuery('#nominativo_codice_fiscale').val(codice);
});
</script>