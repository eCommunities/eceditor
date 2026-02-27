CKEDITOR.editorConfig = function( config ) {
	config.toolbar_Custom =
		[
			[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'SpellChecker', 'Scayt' ],
			[ 'Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat' ],
			[ 'Link', 'Unlink', 'Anchor' ],
			[ 'ecmsInsertDocument', 'ecmsInsertImage', 'ecmsInsertMedia', '-', 'Table', 'SpecialChar' ],
			[ 'ShowBlocks', '-', 'Maximize', 'Source' ],
			'/',
			[ 'Format', 'Font', 'FontSize' ],
			[ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript', '-', 'TextColor', 'BGColor' ],
			[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent' ],
			[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ]
		];

	config.toolbar = 'Custom';
	config.skin = 'moono-lisa';
	config.contentsCss = [ '/layout/css/wysiwyg.css.php', '/plugins/font-awesome/css/all.min.css' ];
	config.bodyId = 'wysiwyg';

	config.dialog_backgroundCoverColor = '#000';
	config.dialog_backgroundCoverOpacity = '0.8';

	config.removePlugins = 'forms,image';
	config.extraPlugins = 'ecmsInsertDocument,ecmsInsertImage,ecmsInsertMedia,scayt,codemirror';

	// ECMS backend endpoint consumed by custom insert-image/document dialogs.
	config.ecms_browserUrl = '/plugins/eceditor/plugins/ecms/browser.php';

	config.disableObjectResizing = false;
	config.image_removeLinkByEmptyURL = true;
	config.image_previewText = CKEDITOR.tools.repeat(
		'This is placeholder text. If you have set HSpace or VSpace (or margin or padding in the advanced tab), you should see some whitespace between the image and the text. Use the align setting to have text wrap all the way around the image. ',
		10
	);

	config.coreStyles_bold = { element: 'strong', attributes: { style: 'font-weight:bold;' } };
	config.coreStyles_italic = { element: 'em', attributes: { style: 'font-style:italic;' } };

	config.extraAllowedContent = 'div(*)';
	config.allowedContent = true;
	config.autoParagraph = false;

	config.protectedSource.push( /<([a-z0-9:_-]+)\b[^>]*\bclass=(["'])[^"']*\becmsInsertMedia\b[^"']*\2[^>]*>[\s\S]*?<\/\1>/gi );
	config.protectedSource.push( /<\/?ecms\b[^>]*>/gi );
	config.protectedSource.push( /<\/?ecms_function_if\b[^>]*>/gi );
	config.protectedSource.push( /<\/?ecms_function_ifnull\b[^>]*>/gi );
	config.protectedSource.push( /<\/?ecms_function_ifnotnull\b[^>]*>/gi );
	// Protect only Font Awesome icon nodes (e.g. <i class="fa ..."></i>),
	// instead of all <i> tags, to avoid heavy Source<->WYSIWYG reparse costs.
	config.protectedSource.push( /<i\b[^>]*\bclass=(["'])[^"']*\bfa(?:s|r|l|b|d)?\b[^"']*\1[^>]*>\s*<\/i>/gi );

	config.protectedTags = 'ecms|ecms_function_if|ecms_function_ifnull|ecms_function_ifnotnull';
};
