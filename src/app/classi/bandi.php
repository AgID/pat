<?php
/*
 * Created on 07/ott/2016
 */

function pulsanteFiltriBandi() {
	$filtro = '<div class="filtriBandi">';
	$filtro .= '<button type="button" class="btn btn-filtriBandi">Visualizza filtri</button>';
	$filtro .= '</div>';
	return $filtro;
}
function filtroBandi() {
	$filtro = '<div class="filtroBandi">';
	$filtro .= '<label>Visualizza ';
	$filtro .= '<select size="1" name="filtroAnac" id="filtroAnac">';
	$filtro .= '<option value="">tutti gli elementi</option>';
	$filtro .= '<option value="filtro[errori_anac]">elementi con errori per comunicazione ANAC</option>';
	$filtro .= '</select>';
	$filtro .= '</label>';
	$filtro .= '</div>';
	return $filtro;
}
function filtroDateBandi() {
	
	$filtro = '<div class="filtroBandiDateDal"><label>Data pubblicazione dal ';
	$filtro .= '<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>';
	$filtro .= '<input type="text" name="filtroDataDal" id="filtroDataDal" />';
	$filtro .= '</label></div>';
	
	$filtro .= '<div class="filtroBandiDateAl"><label>Data pubblicazione al ';
	$filtro .= '<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>';
	$filtro .= '<input type="text" name="filtroDataAl" id="filtroDataAl" />';
	$filtro .= '</label></div>';
	return $filtro;
}
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	// necessario per i campi select con ricerca
	jQuery(".chzn-select").chosen({no_results_text: "Nessun risultato per",allow_single_deselect: true});

	jQuery('#filtroAnac').on('change', '', function () {
		tabellaDinamica.fnFilter( jQuery(this).val(), 0, false, true, false );
	});
	jQuery('#filtroDataDal').datepicker({
		onSelect: function(data, e) {
			tabellaDinamica.fnFilter( data, 1, false, true, false );
		}
	});
	jQuery('#filtroDataAl').datepicker({
		onSelect: function(data, e) {
			tabellaDinamica.fnFilter( data, 2, false, true, false );
		}
	});
	jQuery('#filtroTipologia').on('change', '', function () {
		tabellaDinamica.fnFilter( jQuery(this).val(), 3, false, true, false );
	});
	jQuery('#filtroStruttura').on('change', '', function () {
		tabellaDinamica.fnFilter( jQuery(this).val(), 4, false, true, false );
	});
});		
function visualizzaCercaTabella()  { 
	jQuery('#cercainattivo').toggle('fast', function ()  { 
		jQuery('#cercattivo').toggle('fast', function ()  { 
		});	
	});	
}
</script>
<div class="boxricercaTabella navbar-inner">
	<div id="cercainattivo" style="display: block;">
		<a onclick="visualizzaCercaTabella()" style="" class="btn"><span><i class="icon icon-search"></i> &nbsp;Visualizza filtri</span></a>
	</div>
	<div id="cercattivo" style="display: none;">
		<button onclick="visualizzaCercaTabella()" class="close" type="button">×</button>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Tipologia</span>	
			<select data-placeholder="Seleziona...." name="filtroTipologia" id="filtroTipologia" class="chzn-select input-large">
				<option value="">tutti gli elementi</option>
				<option value="bandi ed inviti">bando di gara</option>
				<option value="lotto">lotto</option>
				<option value="esiti">esito di gara</option>
				<option value="avvisi pubblici">avviso</option>
				<option value="affidamenti">esito/affidamento</option>
				<option value="determina_32">delibera a contrarre o atto equivalente</option>
				<option value="delibere e determine a contrarre">determina art. 57 comma 6 dlgs. 163/2006</option>
				<option value="somme liquidate">liquidazione</option>
			</select>
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Ufficio</span>	
			<select data-placeholder="Seleziona...." name="filtroStruttura" id="filtroStruttura" class="chzn-select input-large">
				<option value="">tutti gli elementi</option>
				<?
				$sql = "SELECT id,nome_ufficio FROM ".$dati_db['prefisso']."oggetto_uffici WHERE (id_ente = ".$idEnte.") AND permessi_lettura != 'H' ORDER BY nome_ufficio";
				if ( !($result = $database->connessioneConReturn($sql)) ) {
					mostraAvviso(0,'Errore in questo campo: se presente, è probabile ci sia un errore nella condizione aqggiuntiva.');
				}
				$istanze = $database->sqlArrayAss($result);
				foreach ((array)$istanze as $istanza) {
					$stringa = '';
					$options .= "<option value=\"".$istanza['id']."\">".$istanza['nome_ufficio']."</option>";
				}
				echo $options;
				?>
			</select>
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Visualizza</span>	
			<select data-placeholder="Seleziona...." name="filtroAnac" id="filtroAnac" class="chzn-select input-large">
				<option value="">tutti gli elementi</option>
				<option value="filtro[errori_anac]">elementi con errori per comunicazione ANAC</option>
			</select>
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Data pubblicazione dal</span>		
			<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>
			<input type="text" name="filtroDataDal" id="filtroDataDal" />
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Data pubblicazione al</span>		
			<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>
			<input type="text" name="filtroDataAl" id="filtroDataAl" />
		</div>
	</div>
</div>