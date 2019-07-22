<?php
/*
 * Created on 07/dec/2015
 */

//costruisco il contenuto della modale
?>

<a class="btn btn-rounded cbox_misura <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>" 

onclick="scegliMisuraAC('<? echo $parametri['id_campo']; ?>');"

href="#cbox_content_misura"

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
if(!$funzione_scegliMisuraAC) {
	?>
	<script type="text/javascript">
	function scegliMisuraAC(id_campo) {
		campo_editor = id_campo;
		jQuery(".cbox_misura").colorbox({inline:true, width: '40%'});
	
		jQuery('.scegli_misura_ac').on('click', function() {
			jQuery.colorbox.close();
			CKEDITOR.instances[campo_editor].insertHtml(
				jQuery('#misuraAC'+jQuery(this).attr('data-id')).html().trim() +
				' ('+jQuery('#misuraAC'+jQuery(this).attr('data-id')+'_responsabile').html().trim()+')'
			);
		});
	}
	</script>
	
	<div id="contenutoMisure" style="display:none;">
	
		<div id="cbox_content_misura">
			<table class="table table-bordered">
				<thead><tr>
					<th>Obiettivi</th>
					<th>Responsabile</th>
					<th></th>
				</tr></thead>
				<?
				$sql = "SELECT id,responsabile,obiettivi FROM ".$dati_db['prefisso']."oggetto_ac_misure WHERE id_ente=".$idEnteAdmin." ORDER BY obiettivi";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					die('Errore durante il recupero di tutte le misure '.$sql);
				}
				$records = $database->sqlArrayAss($result);
				foreach((array)$records as $s) {
					?>
					<tr>
						<td id="misuraAC<? echo $s['id']; ?>">
							<? echo $s['obiettivi']; ?>
						</td>
						<td id="misuraAC<? echo $s['id']; ?>_responsabile">
						<?
						if($s['responsabile'] > 0) {
							$n = mostraDatoOggetto($s['responsabile'], 3, 'referente');
							if($n != '') {
								echo $n;
							}
						}
						?>
					</td>
						<td><a class="scegli_misura_ac btn btn-rounded" data-id="<? echo $s['id']; ?>">Scegli</a></td>
					</tr>
					<?
				}
				?>
			</table>
		</div>
	
	</div>
	<?
	$funzione_scegliMisuraAC = true;
}
?>