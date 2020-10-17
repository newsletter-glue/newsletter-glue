( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, ServerSideRender } = components;

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

	registerBlockType( 'newsletterglue/author', {
		title: 'NG: author byline',
		description: 'Use this block to show author byline in newsletter.',
		icon: 'layout',
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'byline', 'author' ],
		attributes: {
			social: {
				'type': 'string',
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
			button_border: {
				'type' : 'string',
				'default' : '#3400FF'
			}
		},
		edit: withColors( 'formColor' ) ( function( props ) {
			return [

				el( ServerSideRender, {
					block: 'newsletterglue/author',
					attributes: props.attributes,
				} ),

				el( Fragment, {},
					el( InspectorControls, {},

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

						),

						el( PanelBody, { title: 'Customize data', initialOpen: true },

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

						el( PanelColorSettings, {
							title: 'Color options',
							colorSettings: [

								{
									value: props.attributes.button_border,
									label: 'Button border',
									onChange: ( colorValue ) => props.setAttributes( { button_border: colorValue } ),
									colors: colorSamples,
								}
							]
						} ),

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
	window.wp.editor,
	window.wp.element,
	window.wp.components
);