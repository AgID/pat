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
jQuery('#a_fn_bandogara_from_lotto').on('click', function() {
	pr = jQuery('#id_record_cig_principale').val();
	if(pr > 0) {
		jQuery.ajax({
			url: 'ajax.php',
			type: 'get',
			dataType: 'json',
			data: {'azione': 'copiaOggettoPR', 'id': pr},
			success: function(data) {
				jQuery('#oggetto').val(data);
			},
			error: function(xhr, desc, err) {
				console.log(xhr);
				console.log("Details: " + desc + "\nError:" + err);
				alert('ERRORE: Impossibile copiare l\'oggetto dal Bando selezionato.');
			}
		});
	} else {
		alert('Nessun Bando selezionato.');
	}
});
</script>