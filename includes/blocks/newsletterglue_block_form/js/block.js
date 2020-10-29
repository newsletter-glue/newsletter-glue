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
			form_header: {
				'type' : 'string',
			},
			form_description: {
				'type' : 'string',
			},
			email_label: {
				'type' : 'string',
				'default' : 'Email',
			},
			button_text: {
				'type' : 'string',
				'default' : 'Subscribe',
			},
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
					el( 'div', { className: 'ngl-form', 'data-app' : newsletterglue_meta.app },
						el( RichText, {
							tagName: 'h2',
							className: 'ngl-form-header',
							value: props.attributes.form_header,
							format: 'string',
							onChange: ( value ) => { props.setAttributes( { form_header: value } ); },
							placeholder: 'Enter heading...',
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'ngl-form-description',
							value: props.attributes.form_description,
							format: 'string',
							onChange: ( value ) => { props.setAttributes( { form_description: value } ); },
							placeholder: 'Enter description...',
						} ),
						el( 'div', { className: 'ngl-form-field' },
							el( RichText, {
								tagName: 'label',
								className: 'ngl-form-label',
								value: props.attributes.email_label,
								format: 'string',
								onChange: ( value ) => { props.setAttributes( { email_label: value } ); },
								placeholder: 'Email',
							} ),
							el( 'div', { className: 'ngl-form-input' },
								el( 'div', { className: 'ngl-form-input-text' },
								
								)
							)
						),
						el( RichText, {
							tagName: 'div',
							className: 'ngl-form-button',
							value: props.attributes.button_text,
							format: 'string',
							onChange: ( value ) => { props.setAttributes( { button_text: value } ); },
							placeholder: 'Subscribe',
							multiline: '&nbsp;'
						} ),
					)

				)

			)

		},

		// This is how the block is rendered in frontend.
		save: function( props, className ) {

			return (

					el( 'form', { className: 'ngl-form', 'data-app' : newsletterglue_meta.app, action: '', method: 'post' },
						el( RichText.Content, {
							tagName: 'h2',
							className: 'ngl-form-header',
							value: props.attributes.form_header,
						} ),
						el( RichText.Content, {
							tagName: 'p',
							className: 'ngl-form-description',
							value: props.attributes.form_description,
						} ),
						el( 'div', { className: 'ngl-form-field' },
							el( RichText.Content, {
								tagName: 'label',
								className: 'ngl-form-label',
								value: props.attributes.email_label ? props.attributes.email_label : 'Email',
								'for' : 'ngl_email'
							} ),
							el( 'div', { className: 'ngl-form-input' },
								el( 'input', { type: 'email', className: 'ngl-form-input-text', name: 'ngl_email', id: 'ngl_email' },
								
								)
							)
						),
						el( RichText.Content, {
							tagName: 'button',
							className: 'ngl-form-button',
							value: props.attributes.button_text,
						} ),
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