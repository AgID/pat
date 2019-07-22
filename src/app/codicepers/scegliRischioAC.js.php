<?php
/*
 * Created on 30/nov/2015
 *
 */
?>
//CKEDITOR.instances.rischio.setData('<p>testo di prova</p>');

jQuery(".cbox_<? echo $parametri['id_campo']; ?>").colorbox({inline:true, width: '80%'});

jQuery('.scegli_desc_rischio_ac').on('click', function() {
	CKEDITOR.instances.rischio.setData('<p>'+jQuery('#descRischioAC'+jQuery(this).attr('data-id')).html().trim()+'</p>');
	jQuery.colorbox.close();
});