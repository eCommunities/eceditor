/**
 * ECMS Insert Media Plugin for Ckeditor
 * @author eCommunities
 * @version 19.0
 */
( function() {
    CKEDITOR.plugins.add( 'ecmsInsertMedia',
    {
        init: function( editor ) {
           CKEDITOR.dialog.add( 'ecmsInsertMediaDialog', function ( editor ) {
                return {
                    title : 'ECMS Media Properties',
                    minWidth : 600,
                    minHeight : 200,
                    contents : [
                         {
                            id : 'basic',
                            label : 'Details',
                            expand : true,
                            elements : [
                                {
									type : 'html',
									style : 'font-size:11px; line-height:1.3em;',
									html : 'Paste any supplied 3rd party code into the space below and click OK when you\'re done.'		
								},
								{
                                    type : 'textarea',
                                    label : 'Paste Embed Code Here:',
                                    id : 'txtMedia',
                                    rows : 10,
                                    cols : 40,
									validate : CKEDITOR.dialog.validate.notEmpty('Nothing to insert.')
                                }
							]
                        }
                    ],
                    onShow : function() {
						
						// FIXME: NEED TO ALLOW THE CONTEXT BASED DIALOG ACCESS TO ANY EXISTING VALUES
						
					},
					onOk : function() {
						
		                var mediaContent = this.getValueOf('basic', 'txtMedia');
		                
		                var content = CKEDITOR.dom.element.createFromHtml(
								'<div class="ecmsInsertMedia">' + 
									mediaContent +
								'</div>'
							);
						
						// Add the block element to the editor.
						editor.insertElement(content);
		               
	                }
	            };
	        });
           
            editor.addCommand( 'ecmsInsertMedia', new CKEDITOR.dialogCommand( 'ecmsInsertMediaDialog' ) );

            editor.ui.addButton( 'ecmsInsertMedia', {
                label: 'Insert Media From 3rd Party',
                command: 'ecmsInsertMedia',
                icon: this.path + 'images/icon.gif'
            });
            
            /*
            if (editor.contextMenu) {
				editor.contextMenu.addListener(function( element, selection ) {
					if (!element || !element.is('div') || !element.hasClass('ecmsInsertMedia')) { return null; }
					return { mediaEmbedProperties : CKEDITOR.TRISTATE_OFF };
				});
			}
            
            if (editor.addMenuItems) {
				// Add the document Group
				editor.addMenuGroup('media');
				
				editor.addMenuItems({
					mediaEmbedProperties : {
						label : 'Media Properties',
						command : 'ecmsInsertMedia',
						group : 'media',
						icon: this.path + 'images/icon.gif'
					}
				});
			}
			*/
        }
    });
})();