/**
 * ECMS Insert Document Plugin for Ckeditor
 * @author eCommunities
 * @version 19.0
 */
( function() {
    CKEDITOR.plugins.add( 'ecmsInsertDocument',
    {
		requires: 'dialog,button',
        init: function( editor ) {
			function resolveDocumentElement( element ) {
				if (!element) { return null; }

				if (element.type === CKEDITOR.NODE_TEXT) {
					element = element.getParent();
				}

				if (element && element.getAscendant) {
					element = element.getAscendant(function(node) {
						return node && node.type === CKEDITOR.NODE_ELEMENT && node.is('div') && node.hasClass('ecmsInsertDocument');
					}, true);
				}

				if (!element || !element.is || !element.is('div') || !element.hasClass('ecmsInsertDocument')) {
					return null;
				}

				return element;
			}

			function getBrowserUrl( type ) {
				var baseUrl = editor.config.ecms_browserUrl || ( CKEDITOR_BASEPATH + 'plugins/ecms/browser.php' ),
					separator = baseUrl.indexOf( '?' ) === -1 ? '?' : '&';

				return baseUrl + separator + 'type=' + encodeURIComponent( type );
			}

			CKEDITOR.dialog.add('ecmsInsertDocumentDialog', function( editor ) {
				return {
					title : 'ECMS Document Properties',
					resizable : 0,
					width : 600,
					height : 200,
					contents : [
						{
							id : 'basic',
							label : 'Details',
							elements : [
									{
										type : 'html',
										style : 'font-size:11px; line-height:1.3em;',
										html : 'Select or update the document details, to select a new document or replace the existing one, use the Browse... button.'
									},
									{
										type : 'vbox',
										padding : 0,
										children : [
												{
													type : 'html',
													html : '<span>URL</span>'
												},
												{
													type : 'hbox',
													widths : [ '400px', '190px' ],
													align : 'right',
													children : [
															{
																id : 'txtDocUrl',
																type : 'text',
																label : '',
																style : 'width:400px;',
																validate : CKEDITOR.dialog.validate.notEmpty('URL must not be empty') /* LANGUAGE (d.lang.ecms_insert_document.validate.url) */
															},
															{
																type : 'button',
																id : 'browse',
																align : 'center',
																label : 'Browse ECMS Media Warehouse',

																onClick : function(){
																	// Get rid of parent window scroll bar.

																	/**
																	 * FIXME: ckeditor insert position error
																	 *
																	 * THE FOLLOWING LINE CAUSES AN ERROR IN FIREFOX AS WELL AS CAUSING THE EDITOR TO LOSE CURSOR
																	 * POSITION RESULTING IN ANY NEW CONTENT BEING ADDED AT THE BEGINNING RATHER THAN THE EXPECTED
																	 * SPOT IN THE EDITOR BODY.
																	 */
																	//$('html').css('overflow', 'hidden');

																	// Create an iframe element
																	$('<iframe />');
																	// Add iframe parameters
																	$('<iframe />', {
																	    name: 'browser',
																	    id: 'ecms_warehouse_browser',
																	    src: getBrowserUrl( 'doc' ),
																	    style: 'position: fixed; z-index: 10100; top: 0px; left: 0px; width:100%; height:100%; margin:0; padding:0; background:#FFFFFF;'
																	}).appendTo('body');
																}
															} ]
												} ]
									},
									{
										id : 'txtTitle',
										type : 'text',
										label : 'Title', /* LANGUAGE label : d.lang.ecms_insert_document.txtTitle */
										style : 'width:400px;',
										validate : CKEDITOR.dialog.validate.notEmpty('Title must not be empty') /* LANGUAGE (d.lang.ecms_insert_document.validate.title) */
									},
									{
										id : 'txtInfo',
										type : 'text',
										style : 'width:400px;',
										label : 'Info' /* LANGUAGE label : d.lang.ecms_insert_document.txtInfo */
									},
									{
										id : 'txtIcon',
										type : 'text',
										style : 'display:none;'
									} ]
						}
					],
					onShow : function() {
						var sel = editor.getSelection(),
							element = editor._.ecmsInsertDocumentElement || ( sel && sel.getSelectedElement() );

						if (!element && sel) {
							element = sel.getStartElement();
						}

						if (!element && sel) {
							var ranges = sel.getRanges();
							if (ranges && ranges.length) {
								element = ranges[0].getCommonAncestor();
							}
						}

						element = resolveDocumentElement(element);

						this._.ecmsDocumentElement = null;
						editor._.ecmsInsertDocumentElement = null;

						if (!element) {
							this.setValueOf('basic', 'txtDocUrl', '');
							this.setValueOf('basic', 'txtTitle', '');
							this.setValueOf('basic', 'txtInfo', '');
							this.setValueOf('basic', 'txtIcon', '');
							return;
						}

						this._.ecmsDocumentElement = element;

						var link = element.findOne('a'),
							icon = link ? link.findOne('img') : null,
							info = element.findOne('span'),
							title = '';

						if (link) {
							title = CKEDITOR.tools.trim(link.getText() || '');
							this.setValueOf('basic', 'txtDocUrl', link.getAttribute('href') || '');
						} else {
							this.setValueOf('basic', 'txtDocUrl', '');
						}

						this.setValueOf('basic', 'txtTitle', title);
						this.setValueOf('basic', 'txtInfo', info ? (info.getText() || '').replace(/^\(/, '').replace(/\)$/, '') : '');
						this.setValueOf('basic', 'txtIcon', icon ? (icon.getAttribute('src') || '') : '');
					},
					onOk : function() {

						var docUrl = this.getValueOf('basic', 'txtDocUrl');
						var docIcon = this.getValueOf('basic', 'txtIcon');
						var docTitle = this.getValueOf('basic', 'txtTitle');
						var docInfo = this.getValueOf('basic', 'txtInfo');


						var content = CKEDITOR.dom.element.createFromHtml(
								'<div class="ecmsInsertDocument">' +
									'<a href="' + docUrl + '" style="display:inline-block; height:16px; margin:2px;">' +
										'<img style="border:none; margin-bottom:-2px;" src="' + docIcon + '" alt="' + docTitle.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;") + '" /> ' +
										docTitle +
									'</a>' +
									' <span style="font-size:10px; color:#888;">(' + docInfo + ')</span>' +
								'</div>'
							);

						if (this._.ecmsDocumentElement) {
							content.replace(this._.ecmsDocumentElement);
						} else {
							editor.insertElement(content);
						}
					}
				};
			});

			editor.addCommand('ecmsInsertDocument', new CKEDITOR.dialogCommand('ecmsInsertDocumentDialog') );

			editor.ui.addButton( 'ecmsInsertDocument', {
				label: 'Insert Document From ECMS Media Warehouse',
				command: 'ecmsInsertDocument',
				icon: this.path + 'images/ECMS.insertDocument.png'
			});

			if (editor.contextMenu) {
				editor.contextMenu.addListener(function( element, selection ) {
					element = resolveDocumentElement(element);
					if (!element) { return null; }
					editor._.ecmsInsertDocumentElement = element;
					return { ecmsDocumentProperties : CKEDITOR.TRISTATE_OFF };
				});
			}

			if (editor.addMenuItems) {
				// Add the document Group
				editor.addMenuGroup('document');

				editor.addMenuItems({
					ecmsDocumentProperties : {
						label : 'Document Properties',
						command : 'ecmsInsertDocument',
						group : 'document',
						icon: this.path + 'images/ECMS.insertDocument.png'
					}
				});
			}
        }
    } );
} )();

function setDocData(url, icon, title, info) {
	var dialog = CKEDITOR.dialog.getCurrent();
	dialog.setValueOf('basic', 'txtDocUrl', url);
	dialog.setValueOf('basic', 'txtTitle', title);
	dialog.setValueOf('basic', 'txtInfo', info);
	dialog.setValueOf('basic', 'txtIcon', icon);

	// Return the parent window scroll bars.
	// See above "ckeditor insert position error"
	//$('html').css('overflow', 'visible');
};

