CKEDITOR.editorConfig = function( config ) {
	config.toolbar_Custom =
		[
			[ 'Format', 'Font', 'FontSize' ],
			[ 'Link', 'Unlink', 'Anchor' ],
			[ 'Maximize', 'Source' ],
			'/',
			[ 'Bold', 'Italic', 'Underline', 'Strike' ],
			[ 'TextColor', 'BGColor' ],
			[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
			[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote' ]
		];

	config.toolbar = 'Custom';
	config.toolbarStartupExpanded = false;
	config.skin = 'moono-lisa';
	config.bodyId = 'wysiwyg';

	config.width = '100%';
	config.height = '100px';

	config.dialog_backgroundCoverColor = '#000';
	config.dialog_backgroundCoverOpacity = '0.8';

	config.removePlugins = 'forms,image';
	config.coreStyles_bold = { element: 'strong', attributes: { style: 'font-weight:bold;' } };
	config.coreStyles_italic = { element: 'em', attributes: { style: 'font-style:italic;' } };

	config.extraAllowedContent = 'div(*)';
	config.allowedContent = true;
	config.autoParagraph = false;
};
