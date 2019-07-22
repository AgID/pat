<script type="text/javascript">
jQuery(document).ready(function(){
	// necessario per i campi select con ricerca
	jQuery(".chzn-select").chosen({no_results_text: "Nessun risultato per",allow_single_deselect: true});

	jQuery('#filtroDataDal').datepicker({
		onSelect: function(data, e) {
			tabellaDinamica.fnFilter( data, 0, false, true, false );
		}
	});
	jQuery('#filtroDataAl').datepicker({
		onSelect: function(data, e) {
			tabellaDinamica.fnFilter( data, 1, false, true, false );
		}
	});
	jQuery('#filtroTipologia').on('change', '', function () {
		tabellaDinamica.fnFilter( jQuery(this).val(), 2, false, true, false );
	});
	jQuery('#filtroStruttura').on('change', '', function () {
		tabellaDinamica.fnFilter( jQuery(this).val(), 3, false, true, false );
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
				<?
				$tipi = prendiTipiProvvedimento();
				$i=0;
				for($i=0; $i<count($tipi['valori']); $i++) {
					echo '<option value="'.$tipi['valori'][$i].'">'.$tipi['etichette'][$i].'</option>';
				}
				?>
				<!-- 
				<option value="provvedimento dirigenziale">provvedimento dirigenziale</option>
				<option value="provvedimento organo politico">provvedimento organo indirizzo-politico</option>
				-->
			</select>
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Data dal</span>		
			<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>
			<input type="text" name="filtroDataDal" id="filtroDataDal" />
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Data al</span>		
			<div class="input-prepend"><span class="add-on" style="height:20px"><span class="iconfa-calendar"></span></span></div>
			<input type="text" name="filtroDataAl" id="filtroDataAl" />
		</div>
		<div class="ricercaTabella">
			<span><i class="icon icon-search"></i> &nbsp;Struttura</span>		
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
	</div>
</div>