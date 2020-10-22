( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const colorSamples = [
		{
			name: 'Default',
			slug: 'default',
			color: '#3400FF'
		},
		{
			name: 'Black',
			slug: 'black',
			color: '#000000'
		},
		{
			name: 'Coral',
			slug: 'coral',
			color: '#FF7F50'
		},
	];

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 42.301 42.301' },
		el( 'path',
			{
				fill: '#3400FF',
				d: "M21.15.563A21.15,21.15,0,1,0,42.3,21.713,21.147,21.147,0,0,0,21.15.563Zm0,8.187a7.5,7.5,0,1,1-7.5,7.5A7.505,7.505,0,0,1,21.15,8.75Zm0,29.338A16.343,16.343,0,0,1,8.656,32.271a9.509,9.509,0,0,1,8.4-5.1,2.087,2.087,0,0,1,.606.094,11.292,11.292,0,0,0,3.488.588,11.249,11.249,0,0,0,3.488-.588,2.087,2.087,0,0,1,.606-.094,9.509,9.509,0,0,1,8.4,5.1A16.343,16.343,0,0,1,21.15,38.087Z"
			}
		)
	);

	registerBlockType( 'newsletterglue/author', {
		title: 'NG: Author byline',
		description: 'Add an author byline and follow button to your newsletter.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'byline', 'author' ],
		attributes: {
			social: {
				'type': 'string',
				'default' : 'twitter',
			},
			social_user: {
				'type': 'string',
			},
			author_name: {
				'type': 'string',
			},
			author_bio: {
				'type': 'string',
			},
			show_in_blog: {
				'type' : 'boolean',
				'default' : newsletterglue_block_author.show_in_blog ? true : false,
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : newsletterglue_block_author.show_in_email ? true : false,
			},
    		profile_pic: {
    			type: 'string',
    		},
			button_text: {
				'type' : 'string',
				'default' : 'Follow',
			},
			border_radius: {
				'type' : 'number',
				'default' : 5,
			},
			button_style: {
				'type' : 'string',
				'default' : 'solid',
			},
		},
		edit: withColors( 'formColor' ) ( function( props ) {

			var onSelectImage = function( media ) {
				return props.setAttributes( {
					profile_pic: media.url
				} );
			};

			var removeImage = function() {
				props.setAttributes( {
					profile_pic: ''
				} );
			};

			function changeButtonStyle( ev ) {
				let button_style = ev.currentTarget.value;
				props.setAttributes( { button_style } );
			}

			return [

				el( ServerSideRender, {
					block: 'newsletterglue/author',
					attributes: props.attributes,
				} ),

				el( Fragment, {},
					el( InspectorControls, {},

						el( PanelBody, { title: 'Profile settings', initialOpen: true },

							el( PanelRow, {},
								el( MediaUpload, {
									onSelect: onSelectImage,
									type: 'image',
									help: 'test',
									render: function( obj ) {
										return [

											el( 'a', {
													href: '#',
													className: 'ngl-gutenberg-btn',
													onClick: obj.open
												},
												el( 'svg', { className: '', width: '20', height: '20', viewBox: '0 0 24 24' },
													el( 'path', { d: "M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 4.5h14c.3 0 .5.2.5.5v8.4l-3-2.9c-.3-.3-.8-.3-1 0L11.9 14 9 12c-.3-.2-.6-.2-.8 0l-3.6 2.6V5c-.1-.3.1-.5.4-.5zm14 15H5c-.3 0-.5-.2-.5-.5v-2.4l4.1-3 3 1.9c.3.2.7.2.9-.1L16 12l3.5 3.4V19c0 .3-.2.5-.5.5z" } )
												),
												el( 'span', {},
													'Change profile image'
												),
											),

											el( 'a', { href: '#', onClick: removeImage },
												'Reset'
											)

										];
									}
								} )
							),

							el( PanelRow, { className: 'ngl-gutenberg-help' },
								'Ideal image size 100x100 pixels.'
							),

							el( PanelRow, {},
								el( TextControl, {
									label: 'Name',
									value: props.attributes.author_name,
									onChange: ( value ) => { props.setAttributes( { author_name: value } ); },
								} )
							),

							el( PanelRow, {},
								el( TextControl, {
									label: 'Short bio',
									value: props.attributes.author_bio,
									onChange: ( value ) => { props.setAttributes( { author_bio: value } ); },
								} )
							)

						),

						el( PanelBody, { title: 'Follow button', initialOpen: true },

							el( PanelRow, {},
								el( SelectControl, {
									label: 'Social media platform',
									value: props.attributes.social,
									onChange: ( value ) => { props.setAttributes( { social: value } ); },
									options: [
										{ value: 'instagram', label: 'Instagram' },
										{ value: 'twitter', label: 'Twitter' },
										{ value: 'facebook', label: 'Facebook' },
										{ value: 'twitch', label: 'Twitch' },
										{ value: 'tiktok', label: 'Tiktok' },
										{ value: 'youtube', label: 'YouTube' },
									],
								} )
							),
							el( PanelRow, {},
								el( TextControl, {
									label: 'Username',
									value: props.attributes.social_user,
									onChange: ( value ) => { props.setAttributes( { social_user: value } ); },
								} )
							),
							el( PanelRow, {},
								el( TextControl, {
									label: 'Button text',
									value: props.attributes.button_text,
									onChange: ( value ) => { props.setAttributes( { button_text: value } ); },
								} )
							),

							el( PanelRow, {},
								el( BaseControl, {
										label: 'Button style',
										className: 'ngl-gutenberg-base--fullwidth',
									},
									el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
										el( Button, {
											value: 'solid',
											isPrimary: ( props.attributes.button_style === 'solid' ),
											isSecondary: ( props.attributes.button_style !== 'solid' ),
											onClick: changeButtonStyle,
											label: 'Solid',
										}, 'Solid' ),
										el( Button, {
											value: 'outlined',
											isPrimary: ( props.attributes.button_style === 'outlined' ),
											isSecondary: ( props.attributes.button_style !== 'outlined' ),
											onClick: changeButtonStyle,
											label: 'Outlined'
										}, 'Outlined' ),
									)
								)
							),

							el( PanelRow, {},
								el( RangeControl, {
									label: 'Border radius (pixels)',
									value: props.attributes.border_radius,
									initialPosition: 5,
									min: 0,
									max: 50,
									onChange: ( value ) => { props.setAttributes( { border_radius: value } ); },
								} ),
							),

						),

						el( PanelBody, { title: 'Show/hide - author byline block', initialOpen: true },

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
				)
			];
		} ),

		// We're going to be rendering in PHP, so save() can just return null.
		save: function() {
			return null;
		},

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);