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
jQuery('#a_fn_conferente_pf_codiceFiscale').on('click', function() {
	var sesso=jQuery('#conferente_pf_genere').val();
  	var data=jQuery('#conferente_pf_dataNascita').val();
	data=data.match(/^\s*(\d+).(\d+).(\d+)/)
	var codice= CFisc.calcola_codice(
		jQuery('#conferente_pf_nome').val(),
		jQuery('#conferente_pf_cognome').val(),
		sesso,
		data[1],data[2],data[3],
		jQuery('#conferente_pf_codComune').val()
	);
	jQuery('#conferente_pf_codiceFiscale').val(codice);
});
</script>