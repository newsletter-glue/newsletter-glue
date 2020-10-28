( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const icon = el( 'svg' );

	registerBlockType( 'newsletterglue/metadata', {
		title: 'NG: Newsletter meta data',
		description: 'Add standard meta data to each post.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'meta', 'data' ],
		attributes: {
			show_in_blog: {
				'type' : 'boolean',
				'default' : newsletterglue_block_metadata.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : newsletterglue_block_metadata.show_in_email ? true : false
			},
		},
		edit: function( props ) {

			return (

				el( Fragment, {},

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

					),

					// This is how the block is rendered in editor.
					el( 'div', { className: 'ngl-metadata' },
						
					)

				)

			)

		},

		// This is how the block is rendered in frontend.
		save: function( props, className ) {

			return (

					el( 'div', { className: 'ngl-metadata' },
						
					)

			)

		},

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);