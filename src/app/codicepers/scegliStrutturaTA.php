<?php
/*
 * Created on 27/nov/2015
 */

//costruisco il contenuto della modale
?>
<div id="contenutoStrutturePredefinite" style="display:none;">

	<div id="cbox_content_<? echo $parametri['id_campo']; ?>">
		<table class="table table-bordered">
			<thead><tr>
				<th>Struttura organizzativa</th>
				<th></th>
			</tr></thead>
			<?
			$sql = "SELECT id,permessi_lettura,nome_ufficio,__archiviata FROM ".$dati_db['prefisso']."oggetto_uffici WHERE id_ente=".$idEnteAdmin." ORDER BY nome_ufficio";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutte le strutture '.$sql);
			}
			$records = $database->sqlArrayAss($result);
			foreach((array)$records as $r) {
				?>
				<tr>
					<td id="strutturaTA<?php echo $r['id']; ?>">
						<?php echo $r['nome_ufficio']; ?>
					</td>
					<td><a class="scegli_struttura_ac btn btn-rounded" data-id="<?php echo $r['id']; ?>">Scegli</a></td>
				</tr>
				<?
			}
			?>
		</table>
	</div>

</div>