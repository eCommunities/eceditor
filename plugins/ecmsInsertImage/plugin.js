/**
 * ECMS Insert Image Plugin for Ckeditor
 * @author Kevin Farley
 * @version 19.0
 */
( function() {
	CKEDITOR.plugins.add( 'ecmsInsertImage', {
		requires: 'dialog,button',
		init: function( editor ) {
			CKEDITOR.dialog.add('ecmsInsertImageDialog', this.path + 'dialogs/ecmsInsertImage.js');

			editor.addCommand('ecmsInsertImage', new CKEDITOR.dialogCommand('ecmsInsertImageDialog'));

			editor.ui.addButton( 'ecmsInsertImage', {
				label: 'Insert Image From ECMS Media Warehouse',
				command: 'ecmsInsertImage',
				icon: this.path + 'images/ECMS.insertImage.png'
			});

			if (editor.addMenuItems) {
				editor.addMenuItems({
					image : {
						label : 'Image Properties',
						command : 'ecmsInsertImage',
						group : 'image'
					}
				});
			}

			if (editor.contextMenu) {
				editor.contextMenu.addListener(function(elm, d) {
					if (!elm || !elm.is('img') || elm.getAttribute('_cke_realelement')) { return null; }
					return { image : CKEDITOR.TRISTATE_OFF };
				});
			}
		}
    } );
} )();

function setImageData(url, alt, title) {
	var dialog = CKEDITOR.dialog.getCurrent();
	dialog.setValueOf('info', 'txtUrl', url);
	if (alt) { dialog.setValueOf('info', 'txtAlt', alt); }
	if (title) { dialog.setValueOf('info', 'txtTitle', title); }

	// Return the parent window scroll bars.
	// See the dialog "ckeditor insert position error"
	//$('html').css('overflow', 'visible');
};
