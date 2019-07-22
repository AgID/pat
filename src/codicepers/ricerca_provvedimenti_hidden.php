<?php
$_GET['__tag_provvedimenti'] = forzaStringa($_GET['__tag_provvedimenti']);
$_GET['__tag_provvedimenti_mcrt_10'] = forzaStringa($_GET['__tag_provvedimenti_mcrt_10']);

if($configurazione['mostra_select_tag_provvedimenti'] and count($configurazione['__tags_provvedimenti'])>0) {

	$options = '';
	foreach((array)$configurazione['__tags_provvedimenti'] as $t) {
		$stringa = '';
		if ($t['__tag_provvedimenti'] == $_GET['__tag_provvedimenti']) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$t['__tag_provvedimenti']."\"".$stringa." title=\"".$t['nome']."\">".$t['nome']."</option>";
	}
	if($options != '') {
		echo '<div class="campoOggetto71">
			<span style="white-space: nowrap;">
				<label for="__tag_provvedimenti" class="labelClass">Tipologia informazione </label>
				<select class="stileForm75" id="__tag_provvedimenti" name="__tag_provvedimenti">
					<option value="" title="qualunque">qualunque</option>
					'.$options.'
				</select>
			</span>
		</div>';
	}
	
} else {
	
	if($_GET['__tag_provvedimenti_mcrt_10']) {
		echo '<input type="hidden" name="__tag_provvedimenti" id="__tag_provvedimenti" value="'.$_GET['__tag_provvedimenti'].'" />';
	}
}
?>