( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks } = editor;
	const { Fragment } = element;
	const { TextControl, ToggleControl, Panel, PanelBody, PanelRow } = components;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 45 36' },
		el( 'path',
			{
				fill: '#DD3714',
				d: "M22.5,28.125a10.087,10.087,0,0,1-10.048-9.359l-7.376-5.7a23.435,23.435,0,0,0-2.582,3.909,2.275,2.275,0,0,0,0,2.052A22.552,22.552,0,0,0,22.5,31.5a21.84,21.84,0,0,0,5.477-.735l-3.649-2.823a10.134,10.134,0,0,1-1.828.184ZM44.565,32.21,36.792,26.2a23.291,23.291,0,0,0,5.713-7.177,2.275,2.275,0,0,0,0-2.052A22.552,22.552,0,0,0,22.5,4.5,21.667,21.667,0,0,0,12.142,7.151L3.2.237a1.125,1.125,0,0,0-1.579.2L.237,2.211a1.125,1.125,0,0,0,.2,1.579L41.8,35.763a1.125,1.125,0,0,0,1.579-.2l1.381-1.777a1.125,1.125,0,0,0-.2-1.579ZM31.648,22.226,28.884,20.09a6.663,6.663,0,0,0-8.164-8.573,3.35,3.35,0,0,1,.655,1.984,3.279,3.279,0,0,1-.108.7l-5.176-4A10.006,10.006,0,0,1,22.5,7.875,10.119,10.119,0,0,1,32.625,18a9.885,9.885,0,0,1-.977,4.226Z"
			}
		)
	);

	registerBlockType( 'newsletterglue/group', {
		title: 'NG: Show/hide content',
		description: 'Hide selected content from your blog/newsletter.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'group', 'container' ],
		attributes: {
			showblog: {
				'type': 'boolean',
				'default': newsletterglue_block_show_hide_content.showblog ? true : false,
			},
			showemail: {
				'type': 'boolean',
				'default': newsletterglue_block_show_hide_content.showemail ? true : false,
			}
		},
        edit: function( props ) {
			return (
				el( Fragment, {},
					el( InspectorControls, {},
						el( PanelBody, { title: 'Show/hide - newsletter block', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl,
									{
										label: 'Show in blog post',
										onChange: ( value ) => {
											props.setAttributes( { showblog: value } );
										},
										checked: props.attributes.showblog,
									}
								)
							),
								el( ToggleControl,
									{
										label: 'Show in email newsletter',
										onChange: ( value ) => {
											props.setAttributes( { showemail: value } );
										},
										checked: props.attributes.showemail,
									}
								)
							)
						),
					),
		 
					/*  
					 * Here will be your block markup 
					 */
					el( 'section', { className: props.className },
						el( InnerBlocks )
					)
				)
			);
		},
		save: function( props, className ) {
            return (
                el( 'section',
					{
						className: props.className
					},
					el( InnerBlocks.Content )
                )
            );
        },
    } );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);