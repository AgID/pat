<?php
/*
 * Created on 27/nov/2015
 */
?>
jQuery(".cbox_<? echo $parametri['id_campo']; ?>").colorbox({inline:true});

jQuery('.scegli_area_ac').on('click', function() {
	jQuery('#<? echo $parametri['id_campo']; ?>').val(
		jQuery('#areaAC'+jQuery(this).attr('data-id')).html().trim()
	);
	jQuery.colorbox.close();
});