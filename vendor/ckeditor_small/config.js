/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
//		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
//		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
//		{ name: 'insert' },
//		{ name: 'forms' },
		{ name: 'tools'},
                // 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates'
		{ name: 'document',	   groups: [ 'mode' ] }
//		{ name: 'others' },
//		'/',
//		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
//		{ name: 'styles' },
//		{ name: 'colors' },
//		{ name: 'about' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar. 
        // http://ckeditor.com/comment/123266#comment-123266
        
        //Source
	config.removeButtons = 'Underline,Subscript,Superscript,RemoveFormat,Cut,Copy,Bold,Italic,Underline,Anchor,About'
         + ',Strike,Undo,Redo,Paste,PasteText,PasteFromWord,Scayt,Table,Image,NumberedList,HorizontalRule'
        +',BulletedList,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl'
        +',Smiley,SpecialChar,PageBreak,Iframe,InsertPre,Styles,Format,Font,FontSize,ShowBlocks,Save,NewPage,Preview,Print';
        
        config.removePlugins = 'elementspath,save,font';
        config.height = '7em';

	// Se the most common block elements.
	config.format_tags = 'h1;h2;h3;pre';
        config.autoParagraph = false;

    config.protectedSource.push( /<script[\s\S]*?script>/g ); /* script tags */
    config.allowedContent = true;
    config.pasteFromWordPromptCleanup = true;
    
     config.resize_enabled = false;
    config.height = 100;
};
CKEDITOR.config.forcePasteAsPlainText = true;
