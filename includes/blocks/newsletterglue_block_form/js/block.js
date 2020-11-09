( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText, AlignmentToolbar, BlockControls } = editor;
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
			message_text: {
				'type' : 'string',
				'default' : 'Thanks for subscribing.',
			},
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
			name_label: {
				'type' : 'string',
				'default' : 'Name',
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
			toggle_success: {
				'type' : 'boolean',
				'default' : false,
			},
			add_name: {
				'type' : 'boolean',
				'default' : false,
			},
			add_heading: {
				'type' : 'boolean',
				'default' : false,
			},
			add_description: {
				'type' : 'boolean',
				'default' : false,
			},
			form_style: {
				'type' : 'string',
				'default' : 'portrait',
			},
			button_fill: {
				'type' : 'string',
				'default' : '#3400FF',
			},
			button_outline: {
				'type' : 'string',
				'default' : '#3400FF',
			},
			button_text_color: {
				'type' : 'string',
				'default' : '#FFFFFF',
			},
			button_radius: {
				'type' : 'number',
			},
			spacing_size: {
				'type' : 'number',
				'default' : 25,
			},
			name_placeholder: {
				'type' : 'string',
			},
			email_placeholder: {
				'type' : 'string',
			},
			list_id: {
				'type' : 'string',
			},
		},
		edit: withColors( 'formColor' ) ( function( props ) {

			var fieldStyle = { marginBottom: props.attributes.spacing_size };
			if ( props.attributes.form_style === 'landscape' ) {
				fieldStyle = { marginRight: props.attributes.spacing_size };
			}

			function changeFormStyle( ev ) {
				let form_style = ev.currentTarget.value;
				props.setAttributes( { form_style } );
			}

			// Show heading.
			if ( props.attributes.add_heading ) {
				var addHeading = el( RichText, {
							tagName: 'h2',
							className: 'ngl-form-header',
							value: props.attributes.form_header,
							format: 'string',
							onChange: ( value ) => { props.setAttributes( { form_header: value } ); },
							placeholder: 'Enter heading...',
						} );
			} else {
				var addHeading = '';
			}

			// Show description.
			if ( props.attributes.add_description ) {
				var addDescription = el( RichText, {
							tagName: 'p',
							className: 'ngl-form-description',
							value: props.attributes.form_description,
							format: 'string',
							onChange: ( value ) => { props.setAttributes( { form_description: value } ); },
							placeholder: 'Enter description...',
						} );
			} else {
				var addDescription = '';
			}

			if ( props.attributes.add_name ) {
				var addName = el( 'div', { className: 'ngl-form-field', style: fieldStyle },
							el( RichText, {
								tagName: 'label',
								className: 'ngl-form-label',
								value: props.attributes.name_label,
								format: 'string',
								onChange: ( value ) => { props.setAttributes( { name_label: value } ); },
								placeholder: 'Name',
							} ),
							el( 'div', { className: 'ngl-form-input' },
								el( RichText, {
									tagName: 'div',
									className: 'ngl-form-input-text',
									value: props.attributes.name_placeholder,
									format: 'string',
									onChange: ( value ) => { props.setAttributes( { name_placeholder: value } ); },
									multiline: '&nbsp;'
								} )
							)
						);
			} else {
				var addName = '';
			}

			var addEmail = el( 'div', { className: 'ngl-form-field', style: fieldStyle },
							el( RichText, {
								tagName: 'label',
								className: 'ngl-form-label',
								value: props.attributes.email_label,
								format: 'string',
								onChange: ( value ) => { props.setAttributes( { email_label: value } ); },
								placeholder: 'Email',
							} ),
							el( 'div', { className: 'ngl-form-input' },
								el( RichText, {
									tagName: 'div',
									className: 'ngl-form-input-text',
									value: props.attributes.email_placeholder,
									format: 'string',
									onChange: ( value ) => { props.setAttributes( { email_placeholder: value } ); },
									multiline: '&nbsp;'
								} )
							)
						);

			var isPortraitclass = props.attributes.form_style === 'portrait' ? 'ngl-portrait' : 'ngl-landscape';

			var buttonStyles = {
				backgroundColor: props.attributes.button_fill,
				borderColor: props.attributes.button_outline,
				borderWidth: '1px',
				borderStyle: 'solid',
				color: props.attributes.button_text_color,
				borderRadius : props.attributes.button_radius,
			};

			var isOverlayshown = '';
			if ( props.attributes.toggle_success ) {
				isOverlayshown = 'ngl-show';
			}

			var app = newsletterglue_meta.app;

			var SelectList = '';

			if ( app == 'campaignmonitor' ) {
				SelectList = el( SelectControl, {
					label: 'Select a list',
					value: props.attributes.list_id,
					onChange: ( value ) => { props.setAttributes( { list_id: value } ); },
					options: newsletterglue_meta.the_lists,
				} );
			}
			if ( app == 'mailerlite' ) {
				SelectList = el( SelectControl, {
					label: 'Select a group',
					value: props.attributes.list_id,
					onChange: ( value ) => { props.setAttributes( { list_id: value } ); },
					options: newsletterglue_meta.the_lists,
				} );
			}

			if (  ! props.attributes.list_id && newsletterglue_meta.the_lists[0]['value'] ) {
				props.setAttributes( { list_id: newsletterglue_meta.the_lists[0]['value'] } );
			}

			return (

				el( Fragment, {},

					// This is block settings in sidebar.
					el( InspectorControls, {},

						el( PanelBody, { title: 'Form options', initialOpen: true },

							el( PanelRow, {},
								el( BaseControl, {
										label: 'Form style',
										className: 'ngl-gutenberg-base--fullwidth',
									},
									el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
										el( Button, {
											value: 'portrait',
											isPrimary: ( props.attributes.form_style === 'portrait' ),
											isSecondary: ( props.attributes.form_style !== 'portrait' ),
											onClick: changeFormStyle,
											label: 'Portrait',
										}, 'Portrait' ),
										el( Button, {
											value: 'landscape',
											isPrimary: ( props.attributes.form_style === 'landscape' ),
											isSecondary: ( props.attributes.form_style !== 'landscape' ),
											onClick: changeFormStyle,
											label: 'Landscape'
										}, 'Landscape' ),
									)
								)
							),

							el( PanelRow, {},
								el( RangeControl, {
									label: 'Padding (pixels)',
									value: props.attributes.spacing_size,
									initialPosition: 25,
									min: 0,
									max: 100,
									allowReset: true,
									resetFallbackValue: 25,
									onChange: ( value ) => { props.setAttributes( { spacing_size: value } ); },
								} ),
							),

							el( PanelRow, {},
								el( ToggleControl, {
									label: 'Add form heading',
									onChange: ( value ) => { props.setAttributes( { add_heading: value } ); },
									checked: props.attributes.add_heading,
								} )
							),

							el( PanelRow, {},
								el( ToggleControl, {
									label: 'Add form description',
									onChange: ( value ) => { props.setAttributes( { add_description: value } ); },
									checked: props.attributes.add_description,
								} )
							),

							el( PanelRow, {},
								el( ToggleControl, {
									label: 'Add name field',
									onChange: ( value ) => { props.setAttributes( { add_name: value } ); },
									checked: props.attributes.add_name,
								} )
							),

							el( PanelRow, {},
								el( ToggleControl, {
									label: 'Edit success message',
									onChange: ( value ) => { props.setAttributes( { toggle_success: value } ); },
									checked: props.attributes.toggle_success,
								} )
							),

						),

						el( PanelBody, { title: 'Button options', initialOpen: true },

							el( PanelRow, {},
								el( RangeControl, {
									label: 'Button radius (pixels)',
									value: props.attributes.button_radius,
									initialPosition: 0,
									min: 0,
									max: 50,
									allowReset: true,
									resetFallbackValue: 0,
									onChange: ( value ) => { props.setAttributes( { button_radius: value } ); },
								} ),
							),

						),

						el( PanelColorSettings, {
							initialOpen: true,
							title: 'Color options',
							colorSettings: [
								{
									value: props.attributes.button_fill,
									label: 'Button fill',
									onChange: ( value ) => props.setAttributes( { button_fill: value } ),
								},
								{
									value: props.attributes.button_outline,
									label: 'Button fill',
									onChange: ( value ) => props.setAttributes( { button_outline: value } ),
								},
								{
									value: props.attributes.button_text_color,
									label: 'Button text color',
									onChange: ( value ) => props.setAttributes( { button_text_color: value } ),
								},
							]
						} ),

						el( PanelBody, { title: newsletterglue_meta.app_name + ' integration', initialOpen: true },
							el( PanelRow, {},

								SelectList

							),
						),

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
									help: 'Only heading, description and button will be displayed. When clicked, button will take user to the form on the page.',
								} )
							)

						),

					),

					// This is how the block is rendered in editor.
					el( 'div', { className: 'ngl-form' + ' ' + isPortraitclass },
						addHeading,
						addDescription,
						el( 'div', { className: 'ngl-form-container' },
							addName,
							addEmail,
							el( RichText, {
								tagName: 'div',
								className: 'ngl-form-button',
								value: props.attributes.button_text,
								format: 'string',
								onChange: ( value ) => { props.setAttributes( { button_text: value } ); },
								placeholder: 'Subscribe',
								multiline: '&nbsp;',
								style: buttonStyles,
							} )
						),
						el( 'div', { className: 'ngl-message-overlay' + ' ' + isOverlayshown },
							el( 'div', { className: 'ngl-message-svg-wrap' },
								el( 'svg', { viewBox: '0 0 24 24', width: '24', height: '24', strokeWidth: 2, stroke: '#fff', fill: 'none' },
									el( 'polyline', {
										points: '20 6 9 17 4 12',
									} )
								)
							),
							el( RichText, {
								tagName: 'div',
								className: 'ngl-message-overlay-text',
								value: props.attributes.message_text,
								format: 'string',
								onChange: ( value ) => { props.setAttributes( { message_text: value } ); },
							} ),
						),
					)

				)

			)

		} ),

		// This is how the block is rendered in frontend.
		save: function( props, className ) {

			var fieldStyle = { marginBottom: props.attributes.spacing_size };
			if ( props.attributes.form_style === 'landscape' ) {
				fieldStyle = { marginRight: props.attributes.spacing_size };
			}

			// Show header.
			if ( props.attributes.form_header && props.attributes.add_heading ) {
				var formHeader = el( RichText.Content, {
							tagName: 'h2',
							className: 'ngl-form-header',
							value: props.attributes.form_header,
						} );
			} else {
				var formHeader = '';
			}

			// Show description.
			if ( props.attributes.form_description && props.attributes.add_description ) {
				var formDescription = el( RichText.Content, {
							tagName: 'p',
							className: 'ngl-form-description',
							value: props.attributes.form_description,
						} );
			} else {
				var formDescription = '';
			}

			// Add name.
			if ( props.attributes.add_name ) {
				var formName = el( 'div', { className: 'ngl-form-field', style: fieldStyle },
							el( RichText.Content, {
								tagName: 'label',
								className: 'ngl-form-label',
								value: props.attributes.name_label ? props.attributes.name_label : 'Name',
								'for' : 'ngl_name'
							} ),
							el( 'div', { className: 'ngl-form-input' },
								el( 'input', { type: 'text', className: 'ngl-form-input-text', name: 'ngl_name', id: 'ngl_name', placeholder: props.attributes.name_placeholder },
								
								)
							)
						);
			} else {
				var formName = '';
			}

			// Email.
			var formEmail = el( 'div', { className: 'ngl-form-field', style: fieldStyle },
							el( RichText.Content, {
								tagName: 'label',
								className: 'ngl-form-label',
								value: props.attributes.email_label ? props.attributes.email_label : 'Email',
								'for' : 'ngl_email'
							} ),
							el( 'div', { className: 'ngl-form-input' },
								el( 'input', { type: 'email', className: 'ngl-form-input-text', name: 'ngl_email', id: 'ngl_email', placeholder: props.attributes.email_placeholder },
								
								)
							)
						);

			var isPortraitclass = props.attributes.form_style === 'portrait' ? 'ngl-portrait' : 'ngl-landscape';

			var buttonStyles = {
				backgroundColor: props.attributes.button_fill,
				borderColor: props.attributes.button_outline,
				borderWidth: '1px',
				borderStyle: 'solid',
				color: props.attributes.button_text_color,
				borderRadius : props.attributes.button_radius,
			};

			return (

					el( 'form', { className: 'ngl-form' + ' ' + isPortraitclass, action: '', method: 'post' },
						formHeader,
						formDescription,
						el( 'div', { className: 'ngl-form-container' },
							formName,
							formEmail,
							el( RichText.Content, {
								tagName: 'button',
								className: 'ngl-form-button',
								value: props.attributes.button_text,
								style: buttonStyles,
							} )
						),
						el( 'div', { className: 'ngl-message-overlay' },
							el( 'div', { className: 'ngl-message-svg-wrap' },
								el( 'svg', { viewBox: '0 0 24 24', width: '24', height: '24', strokeWidth: 2, stroke: '#fff', fill: 'none' },
									el( 'polyline', {
										points: '20 6 9 17 4 12',
									} )
								)
							),
							el( RichText.Content, {
								tagName: 'div',
								className: 'ngl-message-overlay-text',
								value: props.attributes.message_text,
							} ),
						),
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