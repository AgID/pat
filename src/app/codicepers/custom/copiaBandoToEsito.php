<?php
/*
 * Created on 31/oct/2016
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
jQuery('#a_fn_copy_from_bando').on('click', function() {
	pr = jQuery('#bando_collegato option:selected').val();
	if(pr > 0) {
		jQuery.ajax({
			url: 'ajax.php',
			type: 'get',
			dataType: 'json',
			data: {'azione': 'copiaBandoToEsito', 'id': pr},
			success: function(data) {
				//todo
			},
			error: function(xhr, desc, err) {
				console.log(xhr);
				console.log("Details: " + desc + "\nError:" + err);
				alert('ERRORE: Impossibile copiare i dati dal Bando di gara selezionato.');
			}
		});
	} else {
		alert('Nessun Bando di gara selezionato.');
	}
});
</script>