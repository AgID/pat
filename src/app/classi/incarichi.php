<?php 
function filtroDateIncarichi() {
	$filtro = '<div class="filtroDataDal"><label>Data inizio dal ';
	$filtro .= '<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>';
	$filtro .= '<input type="text" name="filtroDataDal" id="filtroDataDal" />';
	$filtro .= '</label></div>';
	
	$filtro .= '<div class="filtroDataAl"><label>Data inizio al ';
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

	jQuery('#filtroTipologia').on('change', '', function () {
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
				<option value="incarico">incarico</option>
				<option value="liquidazione">liquidazione</option>
			</select>
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Data inizio dal</span>		
			<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>
			<input type="text" name="filtroDataDal" id="filtroDataDal" />
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Data inizio al</span>		
			<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>
			<input type="text" name="filtroDataAl" id="filtroDataAl" />
		</div>
	</div>
</div>