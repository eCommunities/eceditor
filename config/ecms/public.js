CKEDITOR.editorConfig = function( config ) {
	config.toolbar_Custom =
		[
			[ 'Bold', 'Italic', 'Underline', 'Strike' ],
			[ 'NumberedList', 'BulletedList', '-', 'Blockquote' ]
		];

	config.toolbar = 'Custom';
	config.toolbarStartupExpanded = false;
	config.toolbarLocation = 'bottom';

	config.forcePasteAsPlainText = true;
	config.skin = 'moono-lisa';

	config.resize_enabled = false;
	config.width = '100%';
	config.height = '150px';

	config.removePlugins = 'forms,image,elementspath';
	config.extraPlugins = 'autogrow,ecmsMentions';

	config.autoGrow_minHeight = 150;
	config.autoGrow_maxHeight = 350;

	config.coreStyles_bold = { element: 'strong', attributes: { style: 'font-weight:bold;' } };
	config.coreStyles_italic = { element: 'em', attributes: { style: 'font-style:italic;' } };

	config.autoParagraph = true;

	// ECMS backend contracts for custom mentions lookup.
	config.ecms_mentionsEndpoint = '/api/v1.0/ecugm/user/';
	config.ecms_mentionsAuthAttribute = 'mentions-auth';
	config.ecms_mentionsProfileUrl = '/ecms/ECUGM/edit_user.php?user_id={uid}';

	config.protectedSource.push( /<ecms_mention_user>/gi );
	config.protectedSource.push( /<\/ecms_mention_user>/gi );
	config.protectedTags = 'ecms_mention_user';
};
