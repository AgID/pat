<?php
$_GET['__tag_concorsi'] = forzaStringa($_GET['__tag_concorsi']);
$_GET['__tag_concorsi_mcrt_10'] = forzaStringa($_GET['__tag_concorsi_mcrt_10']);

if($configurazione['mostra_select_tag_concorsi'] and count($configurazione['__tags_concorsi'])>0) {

	$options = '';
	foreach((array)$configurazione['__tags_concorsi'] as $t) {
		$stringa = '';
		if ($t['__tag_concorsi'] == $_GET['__tag_concorsi']) {
			$stringa = ' selected="selected" ';
		}
		$options .= "<option value=\"".$t['__tag_concorsi']."\"".$stringa." title=\"".$t['nome']."\">".$t['nome']."</option>";
	}
	if($options != '') {
		echo '<div class="campoOggetto71">
			<span style="white-space: nowrap;">
				<label for="__tag_concorsi" class="labelClass">Tipologia informazione </label>
				<select class="stileForm75" id="__tag_concorsi" name="__tag_concorsi">
					<option value="" title="qualunque">qualunque</option>
					'.$options.'
				</select>
			</span>
		</div>';
	}
	
} else {
	
	if($_GET['__tag_concorsi_mcrt_10']) {
		echo '<input type="hidden" name="__tag_concorsi" id="__tag_concorsi" value="'.$_GET['__tag_concorsi'].'" />';
	}
}
?>