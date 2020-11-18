( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_article;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 42.301 42.301' },
		el( 'path',
			{
				fill: '#DD3714',
				d: "M21.15.563A21.15,21.15,0,1,0,42.3,21.713,21.147,21.147,0,0,0,21.15.563Zm0,8.187a7.5,7.5,0,1,1-7.5,7.5A7.505,7.505,0,0,1,21.15,8.75Zm0,29.338A16.343,16.343,0,0,1,8.656,32.271a9.509,9.509,0,0,1,8.4-5.1,2.087,2.087,0,0,1,.606.094,11.292,11.292,0,0,0,3.488.588,11.249,11.249,0,0,0,3.488-.588,2.087,2.087,0,0,1,.606-.094,9.509,9.509,0,0,1,8.4,5.1A16.343,16.343,0,0,1,21.15,38.087Z"
			}
		)
	);

	registerBlockType( 'newsletterglue/article', {
		title: block.name,
		description: block.description,
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'article', 'embed' ],
		attributes: {
			show_in_blog: {
				'type' : 'boolean',
				'default' : block.show_in_blog ? true : false,
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false,
			},
		},
		edit: withColors( 'formColor' ) ( function( props ) {

			return [
	
					el( ServerSideRender, {
						block: 'newsletterglue/article',
						attributes: props.attributes,
					} ),

					// This is block settings in sidebar.
					el( InspectorControls, {},

						el( PanelBody, { title: 'Show/hide block', initialOpen: true },

							el( PanelRow, {},
								el( ToggleControl, {
									label: 'Show in blog post',
									onChange: ( value ) => { props.setAttributes( { show_in_blog: value } ); },
									checked: props.attributes.show_in_blog,
								} )
							),
							el( PanelRow, {},
								el( ToggleControl, {
									label: 'Show in email newsletter',
									onChange: ( value ) => { props.setAttributes( { show_in_email: value } ); },
									checked: props.attributes.show_in_email,
								} )
							)

						),

					)

			]

		} ),

		// This is how the block is rendered in frontend.
		save: function( props, className ) {
			return null
		},

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);