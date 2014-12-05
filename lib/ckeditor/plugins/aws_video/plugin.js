// http://docs.cksource.com/CKEditor_3.x/Tutorials/SimpleLink_Plugin_Part_1
 
var flashFilenameRegex = /\.swf(?:$|\?)/i;

function isFlashEmbed( element ) {
	var attributes = element.attributes;

	return ( attributes.type == 'application/x-shockwave-flash' || flashFilenameRegex.test( attributes.src || '' ) );
}

function createFakeElement( editor, realElement ) {
	return editor.createFakeParserElement( realElement, 'cke_flash', 'flash', true );
}

CKEDITOR.plugins.add( 'aws_video',
{
	requires: 'dialog,fakeobjects',
	lang: 'af,ar,bg,bn,bs,ca,cs,cy,da,de,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,ug,uk,vi,zh,zh-cn', // %REMOVE_LINE_CORE%
	icons: 'flash', // %REMOVE_LINE_CORE%
	onLoad: function() {
		
		
		//$(".FlashPreviewBox").css("display", "none"); 
		
		CKEDITOR.addCss( 'img.cke_flash' +
			'{' +
				'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/placeholder.png' ) + ');' +
				'background-position: center center;' +
				'background-repeat: no-repeat;' +
				'border: 1px solid #a9a9a9;' +
				'width: 80px;' +
				'height: 80px;' +
			'}'
			);

	},
	
	
	/*init: function( editor )
	{
		// Plugin logic goes here...
		
		editor.addCommand( 'aws_videoDialog', new CKEDITOR.dialogCommand( 'aws_videoDialog' ) );
		
		editor.ui.addButton( 'aws_video',
		{
			label: 'Insert a Video',
			command: 'aws_videoDialog',
			icon: this.path + 'images/icon.png'
		}); 
	}*/
	
	init: function( editor ) {
		
		//editor.addCommand( 'aws_videoDialog', new CKEDITOR.dialogCommand( 'aws_videoDialog' ) );
		
		editor.ui.addButton( 'aws_video',
		{
			label: 'Insert a Video',
			command: 'aws_videoDialog',
			icon: this.path + 'images/icon.png'
		}); 
		
		var allowed = 'object[classid,codebase,height,hspace,vspace,width];' +
			'param[name,value];' +
			'embed[height,hspace,pluginspage,src,type,vspace,width]';

		if ( CKEDITOR.dialog.isTabEnabled( editor, 'flash', 'properties' ) )
			allowed += ';object[align]; embed[allowscriptaccess,quality,scale,wmode]';
		if ( CKEDITOR.dialog.isTabEnabled( editor, 'flash', 'advanced' ) )
			allowed += ';object[id]{*}; embed[bgcolor]{*}(*)';

		editor.addCommand( 'aws_videoDialog', new CKEDITOR.dialogCommand( 'aws_videoDialog', {
			allowedContent: allowed,
			requiredContent: 'embed'
		} ) );
		/*editor.ui.addButton && editor.ui.addButton( 'Flash', {
			label: editor.lang.common.flash,
			command: 'flash',
			toolbar: 'insert,20'
		});/*/
		//alert(this.path + 'dialogs/flash.js');
		CKEDITOR.dialog.add( 'aws_videoDialog', this.path + 'dialogs/video.js' );
		
		/*CKEDITOR.dialog.add( 'aws_videoDialog', function( editor )
		{
			
			//CKEDITOR.scriptLoader.load(this.path + "fck_video.php");
			 
			
			
			return {
				title : 'Video Properties',
				minWidth : 400,
				minHeight : 200,
				contents :
				[
					{
						id : 'general',
						label : 'Settings',
						elements :
						[
							{
								type:"button",id:"browse",filebrowser:"info:flash",hidden:!0,style:"display:inline-block;margin-top:10px;",label:"Explorer le serveur"
							}
						]
					}
				]
			};
		});*/

		// If the "menu" plugin is loaded, register the menu items.
		if ( editor.addMenuItems ) {
			editor.addMenuItems({
				flash: {
					label: editor.lang.flash.properties,
					command: 'flash',
					group: 'flash'
				}
			});
		}

		editor.on( 'doubleclick', function( evt ) {
			var element = evt.data.element;

			if ( element.is( 'img' ) && element.data( 'cke-real-element-type' ) == 'flash' )
				evt.data.dialog = 'flash';
		});

		// If the "contextmenu" plugin is loaded, register the listeners.
		if ( editor.contextMenu ) {
			editor.contextMenu.addListener( function( element, selection ) {
				if ( element && element.is( 'img' ) && !element.isReadOnly() && element.data( 'cke-real-element-type' ) == 'flash' )
					return { flash: CKEDITOR.TRISTATE_OFF };
			});
		}
	},
	
	afterInit: function( editor ) {
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter;

			if ( dataFilter ) {
				dataFilter.addRules({
					elements: {
						'cke:object': function( element ) {
							var attributes = element.attributes,
								classId = attributes.classid && String( attributes.classid ).toLowerCase();

							if ( !classId && !isFlashEmbed( element ) ) {
								// Look for the inner <embed>
								for ( var i = 0; i < element.children.length; i++ ) {
									if ( element.children[ i ].name == 'cke:embed' ) {
										if ( !isFlashEmbed( element.children[ i ] ) )
											return null;

										return createFakeElement( editor, element );
									}
								}
								return null;
							}

							return createFakeElement( editor, element );
						},

						'cke:embed': function( element ) {
							if ( !isFlashEmbed( element ) )
								return null;

							return createFakeElement( editor, element );
						}
					}
				}, 5 );
			}
		}
	
	
	
});



/*
CKEDITOR.dialog.add( 'aws_videoDialog', function( editor )
{
	
	//CKEDITOR.scriptLoader.load(this.path + "fck_video.php");
	 
	
	
	return {
		title : 'Video Properties',
		minWidth : 400,
		minHeight : 200,
		contents :
		[
			{
				id : 'general',
				label : 'Settings',
				elements :
				[
				 	{
						type:"button",id:"browse",filebrowser:"info:flash",hidden:!0,style:"display:inline-block;margin-top:10px;",label:"Explorer le serveur"
					}
				]
			}
		]
	};
});
*/
CKEDITOR.tools.extend( CKEDITOR.config, {
	/**
	 * Save as `<embed>` tag only. This tag is unrecommended.
	 *
	 * @cfg {Boolean} [flashEmbedTagOnly=false]
	 * @member CKEDITOR.config
	 */
	flashEmbedTagOnly: false,

	/**
	 * Add `<embed>` tag as alternative: `<object><embed></embed></object>`.
	 *
	 * @cfg {Boolean} [flashAddEmbedTag=false]
	 * @member CKEDITOR.config
	 */
	flashAddEmbedTag: true,

	/**
	 * Use {@link #flashEmbedTagOnly} and {@link #flashAddEmbedTag} values on edit.
	 *
	 * @cfg {Boolean} [flashConvertOnEdit=false]
	 * @member CKEDITOR.config
	 */
	flashConvertOnEdit: false
});