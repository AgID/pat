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
	global $idEnteAdmin;
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
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	// necessario per i campi select con ricerca
	jQuery(".chzn-select").chosen({no_results_text: "Nessun risultato per",allow_single_deselect: true});

	jQuery('#filtroAnac').on('change', '', function () {
		tabellaDinamica.fnFilter( jQuery(this).val(), 0, false, true, false );
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
			<span><i class="icon icon-search"></i> &nbsp;Visualizza</span>	
			<select data-placeholder="Seleziona...." name="filtroAnac" id="filtroAnac" class="chzn-select input-large">
				<option value="">tutti gli elementi</option>
				<option value="filtro[errori_anac]">elementi con errori per comunicazione ANAC</option>
			</select>
		</div>
	</div>
</div>