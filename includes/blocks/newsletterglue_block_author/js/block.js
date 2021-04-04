( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_author;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const colorSamples = [
		{
			name: 'Default',
			slug: 'default',
			color: '#0088A0'
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
				fill: '#0088A0',
				d: "M21.15.563A21.15,21.15,0,1,0,42.3,21.713,21.147,21.147,0,0,0,21.15.563Zm0,8.187a7.5,7.5,0,1,1-7.5,7.5A7.505,7.505,0,0,1,21.15,8.75Zm0,29.338A16.343,16.343,0,0,1,8.656,32.271a9.509,9.509,0,0,1,8.4-5.1,2.087,2.087,0,0,1,.606.094,11.292,11.292,0,0,0,3.488.588,11.249,11.249,0,0,0,3.488-.588,2.087,2.087,0,0,1,.606-.094,9.509,9.509,0,0,1,8.4,5.1A16.343,16.343,0,0,1,21.15,38.087Z"
			}
		)
	);

	registerBlockType( 'newsletterglue/author', {
		title: block.name,
		description: block.description,
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
				'default' : block.show_in_blog ? true : false,
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false,
			},
			show_button: {
				'type' : 'boolean',
				'default' : 1,
			},
    		profile_pic: {
    			'type' : 'string',
    		},
			button_text: {
				'type' : 'string',
				'default' : block.button_text,
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

			function onChangeBio( value ) {
				props.setAttributes( { author_bio: value } );
			}

			function onChangeName( value ) {
				props.setAttributes( { author_name: value } );
			}

			function onChangeButtonText( value ) {
				props.setAttributes( { button_text: value } );
			}

			var platform = props.attributes.social ? props.attributes.social : 'twitter';
			var username = props.attributes.social_user ? props.attributes.social_user : '';
			var userImage = props.attributes.profile_pic ? props.attributes.profile_pic : newsletterglue_meta.profile_pic;
			var showName = props.attributes.author_name ? props.attributes.author_name : '';
			var showBio  = props.attributes.author_bio ? props.attributes.author_bio : '';
			var outline = props.attributes.button_style === 'solid' ? '' : '-fill';

			if ( platform == 'twitter' ) {
				var followURL = 'https://twitter.com/' + username;
			} else if ( platform == 'instagram' ) {
				var followURL = 'https://instagram.com/' + username;
			} else if ( platform == 'youtube' ) {
				var followURL = 'https://youtube.com/channel/' + username;
			} else if ( platform == 'facebook' ) {
				var followURL = 'https://facebook.com/' + username;
			} else if ( platform == 'tiktok' ) {
				var followURL = 'https://www.tiktok.com/@' + username;
			} else if ( platform == 'twitch' ) {
				var followURL = 'https://twitch.tv/' + username;
			}

			var show_cta = '';
			if ( props.attributes.show_button ) {
				show_cta = 	el( 'div', { className: 'ngl-author-cta' },
								el( 'span', {
										className: 'ngl-author-btn ngl-author-btn-' + props.attributes.button_style + ' ngl-author-' + platform,
										style: { borderRadius: props.attributes.border_radius },
									},
									el( 'img', {
										src: block.assets_uri + platform + outline + '.png'
									} ),
									el( RichText, {
										tagName: 'span',
										value: props.attributes.button_text,
										className: 'ngl-author-btn-text',
										format: 'string',
										onChange: onChangeButtonText,
										placeholder: 'Enter button text...',
									} )
								)
							);
			}

			var show_button_options = '';
			if ( props.attributes.show_button ) {
				show_button_options = [
							el( BaseControl, {},
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
							el( BaseControl, {},
								el( TextControl, {
									label: 'Username',
									value: props.attributes.social_user,
									onChange: ( value ) => { props.setAttributes( { social_user: value } ); },
								} )
							),

							el( BaseControl, {},
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

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Border radius (pixels)',
									value: props.attributes.border_radius,
									initialPosition: 5,
									min: 0,
									max: 50,
									allowReset: true,
									resetFallbackValue: 5,
									onChange: ( value ) => { props.setAttributes( { border_radius: value } ); },
								} ),
							)
					];
			}

			return (

				el( Fragment, {},

					// This is block settings in sidebar.
					el( InspectorControls, {},

						el( PanelBody, { title: 'Profile settings', initialOpen: true },

							el( BaseControl, {},
								el( MediaUpload, {
									onSelect: onSelectImage,
									type: 'image',
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
												props.attributes.profile_pic ? 'Reset' : ''
											)

										];
									}
								} )
							),

							el( BaseControl, { className: 'ngl-gutenberg-help' },
								'Ideal image size 100x100 pixels.'
							)

						),

						el( PanelBody, { title: 'Follow button', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show Follow button',
									onChange: ( value ) => { props.setAttributes( { show_button: value } ); },
									checked: props.attributes.show_button,
								} )
							),

							show_button_options

						),

						el( PanelBody, { title: 'Show/hide block', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show in blog post',
									onChange: ( value ) => { props.setAttributes( { show_in_blog: value } ); },
									checked: props.attributes.show_in_blog,
								} )
							),
							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show in email newsletter',
									onChange: ( value ) => { props.setAttributes( { show_in_email: value } ); },
									checked: props.attributes.show_in_email,
								} )
							)
						),

					),

					// This is how the block is rendered in editor.
					el( 'div', { className: 'ngl-author' },
						el( 'div', { className: 'ngl-author-pic' },
							el( 'img', { src: userImage, className: 'avatar avatar-80 photo' },
							
							)
						),
						el( 'div', { className: 'ngl-author-meta' },
							el( RichText, {
								tagName: 'div',
								className: 'ngl-author-name',
								value: showName,
								format: 'string',
								onChange: onChangeName,
								placeholder: newsletterglue_meta.author_name ? newsletterglue_meta.author_name : 'Enter name...',
								multiline: '&nbsp;',
							} ),
							el( 'div', { className: 'ngl-author-bio' },
								el( RichText, {
									tagName: 'span',
									value: showBio,
									className: 'ngl-author-bio-content',
									format: 'string',
									onChange: onChangeBio,
									placeholder: newsletterglue_meta.author_bio ? newsletterglue_meta.author_bio : 'Enter user description...',
								} )
							),
							show_cta
						)
					)

				)

			)

		} ),

		// This is how the block is rendered in frontend.
		save: function( props, className ) {
			
			var platform = props.attributes.social ? props.attributes.social : 'twitter';
			var username = props.attributes.social_user ? props.attributes.social_user : '';
			var userImage = props.attributes.profile_pic ? props.attributes.profile_pic : newsletterglue_meta.profile_pic;
			var showName = props.attributes.author_name ? props.attributes.author_name : '';
			var showBio  = props.attributes.author_bio ? props.attributes.author_bio : '';
			var outline = props.attributes.button_style === 'solid' ? '' : '-fill';

			if ( platform == 'twitter' ) {
				var followURL = 'https://twitter.com/' + username;
			} else if ( platform == 'instagram' ) {
				var followURL = 'https://instagram.com/' + username;
			} else if ( platform == 'youtube' ) {
				var followURL = 'https://youtube.com/channel/' + username;
			} else if ( platform == 'facebook' ) {
				var followURL = 'https://facebook.com/' + username;
			} else if ( platform == 'tiktok' ) {
				var followURL = 'https://www.tiktok.com/@' + username;
			} else if ( platform == 'twitch' ) {
				var followURL = 'https://twitch.tv/' + username;
			}

			var show_cta = '';
			if ( props.attributes.show_button ) {
				show_cta = 	el( 'div', { className: 'ngl-author-cta' },
								el( 'a', {
										className: 'ngl-author-btn ngl-author-btn-' + props.attributes.button_style + ' ngl-author-' + platform,
										style: { borderRadius: props.attributes.border_radius },
										href: followURL,
										target: '_blank',
										rel: 'noopener noreferrer'
									},
									el( 'img', {
										src: block.assets_uri + platform + outline + '.png'
									} ),
									el( RichText.Content, {
										tagName: 'span',
										value: props.attributes.button_text,
										className: 'ngl-author-btn-text',
									} )
								)
							);
			}

			return (

					el( 'div', { className: 'ngl-author' },
						el( 'div', { className: 'ngl-author-pic' },
							el( 'img', { src: userImage, className: 'avatar avatar-80 photo' },
							
							)
						),
						el( 'div', { className: 'ngl-author-meta' },
							el( RichText.Content, {
								tagName: 'div',
								className: 'ngl-author-name',
								value: showName? showName : newsletterglue_meta.author_name,
							} ),
							el( 'div', { className: 'ngl-author-bio' },
								el( RichText.Content, {
									tagName: 'span',
									value: showBio ? showBio : newsletterglue_meta.author_bio,
									className: 'ngl-author-bio-content',
								} )
							),
							show_cta
						)
					)

			)

		},

		// Example.
		example: function() {

		},

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);