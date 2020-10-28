( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 43.403 34.722' }, 
		el( 'path', {
			fill: '#3400ff',
			d: 'M42.063,6H7.34a4.335,4.335,0,0,0-4.319,4.34L3,36.382a4.353,4.353,0,0,0,4.34,4.34H42.063a4.353,4.353,0,0,0,4.34-4.34V10.34A4.353,4.353,0,0,0,42.063,6Zm0,8.681L24.7,25.531,7.34,14.681V10.34L24.7,21.191,42.063,10.34Z',
			transform: 'translate(-3 -6)'
		} )
	);

	registerBlockType( 'newsletterglue/form', {
		title: 'NG: Subscriber form',
		description: 'New subscribers can sign up to your mailing list with this form.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'form', 'subscribe' ],
		attributes: {
			show_in_blog: {
				'type' : 'boolean',
				'default' : newsletterglue_block_form.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : newsletterglue_block_form.show_in_email ? true : false
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
					el( 'div', { className: 'ngl-form' },
						
					)

				)

			)

		},

		// This is how the block is rendered in frontend.
		save: function( props, className ) {

			return (

					el( 'div', { className: 'ngl-form' },
						
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