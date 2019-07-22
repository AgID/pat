<?php
/*
 * Created on 07/dec/2015
 */

//costruisco il contenuto della modale
?>

<a class="btn btn-rounded cbox_mod_rotazione <? echo $parametri['class']; ?>" style="<? echo $parametri['style']; ?>" id="a_fn_<? echo $parametri['id_campo']; ?>" name="a_fn_<? echo $parametri['id_campo'] ?>" value="<? echo $parametri['etichetta'] ?>" 

onclick="scegliModalitaRotazioneAC('<? echo $parametri['id_campo']; ?>');"

href="#cbox_content_mod_rotazione"

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
if(!$funzione_scegliModalitaRotazioneAC) {
	?>
	<script type="text/javascript">
	function scegliModalitaRotazioneAC(id_campo) {
		campo_editor = id_campo;
		jQuery(".cbox_mod_rotazione").colorbox({inline:true, width: '40%'});
	
		jQuery('.scegli_mod_rotazione_ac').on('click', function() {
			jQuery.colorbox.close();
			CKEDITOR.instances[campo_editor].insertHtml(
				jQuery('#modalitaRotazioneAC'+jQuery(this).attr('data-id')).html().trim()
			);
		});
	}
	</script>
	
	<div id="contenutoModalitaRotazione" style="display:none;">
	
		<div id="cbox_content_mod_rotazione">
			<table class="table table-bordered">
				<thead><tr>
					<th>Dipendente</th>
					<th>Descrizione</th>
					<th></th>
				</tr></thead>
				<?
				$sql = "SELECT id,dipendente,descrizione,rotazione_uffici,rotazione_procedimenti FROM ".$dati_db['prefisso']."oggetto_ac_rotazione WHERE id_ente=".$idEnteAdmin." ORDER BY dipendente";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					die('Errore durante il recupero di tutte le modalità di rotazione '.$sql);
				}
				$records = $database->sqlArrayAss($result);
				foreach((array)$records as $s) {
					?>
					<tr>
						<td id="modalitaRotazioneAC<? echo $s['id']; ?>_dipendente">
						<?
						$dip = '';
						if($s['dipendente'] > 0) {
							$dip = mostraDatoOggetto($s['dipendente'], 3, 'referente');
							if($dip != '') {
								echo $dip;
							}
						}
						?>
						</td>
						<td>
							<? echo $s['descrizione']; ?>
						</td>
						<td>
							<a class="scegli_mod_rotazione_ac btn btn-rounded" data-id="<? echo $s['id']; ?>">Scegli</a>
							<div id="modalitaRotazioneAC<? echo $s['id']; ?>" style="display: none;">
								<p><strong>Dipendente</strong>: <? echo $dip; ?></p>
								<p><strong>Descrizione</strong>: <? echo $s['descrizione']; ?></p>
								<?
								if($s['rotazione_uffici'] != '') {
									$rot = json_decode($s['rotazione_uffici']);
									if(count($rot)) {
										?>
										<p><strong>Rotazione degli uffici</strong></p>
										<?
									}
									foreach((array)$rot as $r) {
										?>
										<p>
										<?
										if($r->ufficio != '') {
											?>
											<strong>Ufficio</strong>: <? echo $r->ufficio; ?><span> </span>
											<?
										}
										if($r->ruolo != '') {
											?>
											<strong>Ruolo</strong>: <? echo $r->ruolo; ?><span> </span>
											<?
										}
										if($r->data_dal != '') {
											?>
											<strong>Dal</strong> <? echo ricavaDataJTable($r->data_dal); ?>
											<?
										}
										if($r->data_al != '') {
											?>
											<strong> al</strong> <? echo ricavaDataJTable($r->data_al); ?>
											<?
										}
										?>
										</p>
										<?
									}
								}
								
								if($s['rotazione_procedimenti'] != '') {
									$rot = json_decode($s['rotazione_procedimenti']);
									if(count($rot)) {
										?>
										<p><strong>Rotazione dei procedimenti</strong></p>
										<?
									}
									foreach((array)$rot as $r) {
										?>
										<p>
										<?
										if($r->procedimento != '') {
											?>
											<strong>Procedimento</strong>: <? echo $r->procedimento; ?><span> </span>
											<?
										}
										if($r->data_dal != '') {
											?>
											<strong>Dal</strong> <? echo ricavaDataJTable($r->data_dal); ?>
											<?
										}
										if($r->data_al != '') {
											?>
											<strong> al</strong> <? echo ricavaDataJTable($r->data_al); ?>
											<?
										}
										?>
										</p>
										<?
									}
								}
								?>
							</div>
						</td>
					</tr>
					<?
				}
				?>
			</table>
		</div>
	
	</div>
	<?
	$funzione_scegliModalitaRotazioneAC = true;
}
?>