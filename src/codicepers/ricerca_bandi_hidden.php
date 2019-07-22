<?php
$_GET['__tag_gare'] = forzaStringa($_GET['__tag_gare']);
$_GET['__tag_gare_mcrt_10'] = forzaStringa($_GET['__tag_gare_mcrt_10']);

if($configurazione['mostra_select_tag_gare'] and count($configurazione['__tags_gare'])>0) {

	$options = '';
	foreach((array)$configurazione['__tags_gare'] as $t) {
		$stringa = '';
		if ($t['__tag_gare'] == forzaStringa($_GET['__tag_gare'])) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$t['__tag_gare']."\"".$stringa." title=\"".$t['nome']."\">".$t['nome']."</option>";
	}
	if($options != '') {
		echo '<div class="campoOggetto71">
			<span style="white-space: nowrap;">
				<label for="__tag_gare" class="labelClass">Tipologia informazione </label>
				<select class="stileForm75" id="__tag_gare" name="__tag_gare">
					<option value="" title="qualunque">qualunque</option>
					'.$options.'
				</select>
			</span>
		</div>';
	}
	
} else {
	
	if($_GET['__tag_gare_mcrt_10']) {
		echo '<input type="hidden" name="__tag_gare" id="__tag_gare" value="'.$_GET['__tag_gare'].'" />';
		
		
		
		//bandi in corso data_inizio <= oggi AND data_fine >= oggi
		
		//bandi in aggiudicazione data_fine < oggi
		
		//bandi aggiudicati data_fine < oggi
		 
	}
}
?>