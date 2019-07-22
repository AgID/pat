<?php
/*
 * Created on 07/dec/2015
 */

//costruisco il contenuto della modale
?>

<a class="btn btn-rounded cbox_provvedimento <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>" 

onclick="scegliProvvedimentoAC('<? echo $parametri['id_campo']; ?>');"

href="#cbox_content_provvedimento"

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
if(!$funzione_scegliProvvedimentoAC) {
	?>
	<script type="text/javascript">
	function scegliProvvedimentoAC(id_campo) {
		campo_editor = id_campo;
		jQuery(".cbox_provvedimento").colorbox({inline:true, width: '40%'});
	
		jQuery('.scegli_provvedimento_ac').on('click', function() {
			jQuery.colorbox.close();
			CKEDITOR.instances[campo_editor].insertHtml(
				jQuery('#provvedimentoAC'+jQuery(this).attr('data-id')).html().trim() +
				' ('+jQuery('#provvedimentoAC'+jQuery(this).attr('data-id')+'_data').html().trim()+')'
			);
		});
	}
	</script>
	
	<div id="contenutoMisure" style="display:none;">
	
		<div id="cbox_content_provvedimento">
			<table class="table table-bordered">
				<thead><tr>
					<th>Oggetto</th>
					<th>Data</th>
					<th></th>
				</tr></thead>
				<?
				$sql = "SELECT id,oggetto,data FROM ".$dati_db['prefisso']."oggetto_provvedimenti WHERE id_ente=".$idEnteAdmin." ORDER BY oggetto";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					die('Errore durante il recupero di tutte i provvedimenti '.$sql);
				}
				$records = $database->sqlArrayAss($result);
				foreach((array)$records as $s) {
					?>
					<tr>
						<td id="provvedimentoAC<? echo $s['id']; ?>">
							<? echo $s['oggetto']; ?>
						</td>
						<td id="provvedimentoAC<? echo $s['id']; ?>_data">
						<?
						if($s['data'] > 0) {
							echo date('d/m/Y', $s['data']);
						}
						?>
					</td>
						<td><a class="scegli_provvedimento_ac btn btn-rounded" data-id="<? echo $s['id']; ?>">Scegli</a></td>
					</tr>
					<?
				}
				?>
			</table>
		</div>
	
	</div>
	<?
	$funzione_scegliProvvedimentoAC = true;
}
?>