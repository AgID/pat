<script type="text/javascript">
jQuery(document).ready(function(){
	// necessario per i campi select con ricerca
	jQuery(".chzn-select").chosen({no_results_text: "Nessun risultato per",allow_single_deselect: true});

	jQuery('#filtroTipologia').on('change', '', function () {
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
			<span><i class="icon icon-search"></i> &nbsp;Tipologia</span>	
			<select data-placeholder="Seleziona...." name="filtroTipologia" id="filtroTipologia" class="chzn-select input-large">
				<option value="">tutti gli elementi</option>
				<option value="sovvenzione">sovvenzione</option>
				<option value="liquidazione">liquidazione</option>
			</select>
		</div>
	</div>
</div>