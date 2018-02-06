/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config )
{
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    
    config.docType = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

    //config.forcePasteAsPlainText = false;
    config.menu_subMenuDelay = 80;

    config.protectedSource.push( /<script[\s\S]*?\/script>/gi ) ;	// <SCRIPT> tags.

    config.stylesCombo_stylesSet = 'stili_editor';
    config.defaultLanguage = 'it';
    config.resize_enabled = false;
    config.removePlugins = 'elementspath,font,save,scayt,wsc';
    config.toolbarCanCollapse = false;
    config.width= '100%';
    config.height= '400px';
    config.format_tags = 'p;div;address;h4;h5;h6';

    config.toolbar_Advanced =
        [
        ['Source','-','Save','NewPage','Preview','-','Templates'],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['Bold','Italic','-','Subscript','Superscript'],
        ['NumberedList','BulletedList','-','Outdent','Indent'],
        ['Link','Unlink','Anchor'],
        ['Image','Flash','Table','HorizontalRule','SpecialChar'],
        ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
        ['TextColor','BGColor'],
        ['ShowBlocks'],
        ['Styles','Format']
    ];

    config.toolbar_Default =
        [
        ['NewPage','Templates'],
        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['Bold','Italic'],
        ['NumberedList','BulletedList','-','Outdent','Indent'],
        ['Link','Unlink','Anchor'],
        ['Image','Table','HorizontalRule','SpecialChar'],
        ['TextColor'],
        ['ShowBlocks'],
        ['Styles','Format']
    ];

    config.toolbar_Basic =
        [
        ['NewPage'],
        ['Cut','Copy','Paste','PasteText','-','Print'],
        ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
        ['Bold','Italic'],
        ['NumberedList','BulletedList'],
        ['Link','Unlink','Anchor'],
        ['Image','Table','HorizontalRule','SpecialChar'],
        ['ShowBlocks'],
        ['Styles','Format']
    ];
    
    config.toolbar_None =
        [
        ['NewPage'],
        ['Cut','Copy','PasteText'],
        ['Undo','Redo','-','SelectAll','RemoveFormat'],
        ['Bold','Italic'],
        ['NumberedList','BulletedList'],
        ['HorizontalRule','SpecialChar'],
        ['Link','Unlink','Anchor']
    ];

};