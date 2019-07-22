<?php
/*
 * Created on 07/dec/2015
 */

//costruisco il contenuto della modale
?>

<a class="btn btn-rounded <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>" 

onclick="copiaEsitoAccessoCivico();"

> <!-- fine tag a -->

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
function copiaEsitoAccessoCivico() {
	CKEDITOR.instances['esito'].updateElement();
	var ec = document.getElementById('esito').value;
	CKEDITOR.instances['esito_registro'].setData(ec);
	//CKEDITOR.instances['esito_registro'].html(ec);
}
</script>