<?php
/*
 * Created on 27/nov/2015
 */

//costruisco il contenuto della modale
?>
<div id="contenutoAttivita" style="display:none;">

	<div id="cbox_content_<? echo $parametri['id_campo']; ?>">
		<table class="table table-bordered">
			<thead><tr>
				<th>Attività/Procedimento</th>
				<th></th>
			</tr></thead>
			<?
			$sql = "SELECT id,nome FROM ".$dati_db['prefisso']."oggetto_procedimenti WHERE id_ente=".$idEnteAdmin." ORDER BY nome";
			if ( !($result = $database->connessioneConReturn($sql)) ) {
				die('Errore durante il recupero di tutti i procedimenti '.$sql);
			}
			$records = $database->sqlArrayAss($result);
			foreach((array)$records as $s) {
				?>
				<tr>
					<td id="attivitaAC<? echo $s['id']; ?>">
						<? echo $s['nome']; ?>
					</td>
					<td><a class="scegli_attivita_ac btn btn-rounded" data-id="<? echo $s['id']; ?>">Scegli</a></td>
				</tr>
				<?
			}
			?>
		</table>
	</div>

</div>