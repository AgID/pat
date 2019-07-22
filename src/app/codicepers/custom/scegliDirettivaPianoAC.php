<?php
/*
 * Created on 07/dec/2015
 */

//costruisco il contenuto della modale
?>

<a class="btn btn-rounded cbox_direttiva <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>" 

onclick="scegliDirettivaAC('<? echo $parametri['id_campo']; ?>');"

href="#cbox_content_direttiva"

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

<?
//includo questa parte solo la prima volta
if(!$funzione_scegliDirettivaAC) {
	?>
	<script type="text/javascript">
	function scegliDirettivaAC(id_campo) {
		campo_editor = id_campo;
		jQuery(".cbox_direttiva").colorbox({inline:true, width: '40%'});
	
		jQuery('.scegli_direttiva_ac').on('click', function() {
			jQuery.colorbox.close();
			CKEDITOR.instances[campo_editor].insertText(
				jQuery('#direttivaAC'+jQuery(this).attr('data-id')).html().trim()
			);
		});
	}
	</script>
	
	<div id="contenutoDirettive" style="display:none;">
	
		<div id="cbox_content_direttiva">
			<table class="table table-bordered">
				<thead><tr>
					<th>Direttive interne anticorruzione</th>
					<th></th>
				</tr></thead>
				<?
				$sql = "SELECT id,oggetto FROM ".$dati_db['prefisso']."oggetto_ac_direttive WHERE id_ente=".$idEnteAdmin." ORDER BY oggetto";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					die('Errore durante il recupero di tutte le direttive '.$sql);
				}
				$records = $database->sqlArrayAss($result);
				foreach((array)$records as $s) {
					?>
					<tr>
						<td id="direttivaAC<? echo $s['id']; ?>">
							<? echo $s['oggetto']; ?>
						</td>
						<td><a class="scegli_direttiva_ac btn btn-rounded" data-id="<? echo $s['id']; ?>">Scegli</a></td>
					</tr>
					<?
				}
				?>
			</table>
		</div>
	
	</div>
	<?
	$funzione_scegliDirettivaAC = true;
}
?>