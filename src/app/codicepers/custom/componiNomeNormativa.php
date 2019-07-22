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

<script type="text/javascript">
jQuery('#a_fn_componi_nome_normativa').on('click', function() {
	var t = jQuery('#tipologia_atto option:selected').val();
	if(jQuery('#numero').val() != '') {
		t += (t=='' ? '':' ')+'Numero '+jQuery('#numero').val();
	}
	if(jQuery('#protocollo').val() != '') {
		t += (t=='' ? '':' ')+'Protocollo '+jQuery('#protocollo').val();
	}
	if(jQuery('#data_emissioneVis').val() != '') {
		t += (t=='' ? '':' ')+'del '+jQuery('#data_emissioneVis').val();
	}
	if(t!= '') {
		jQuery('#nome').val(t);
	}
});
</script>