( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_article;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 92.308 75' },
		el( 'path',
			{
				fill: '#DD3714',
				d: "M14.423,61.067H2.885A2.885,2.885,0,0,0,0,63.952V75.49a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V63.952A2.885,2.885,0,0,0,14.423,61.067Zm0-57.692H2.885A2.885,2.885,0,0,0,0,6.26V17.8a2.885,2.885,0,0,0,2.885,2.885H14.423A2.885,2.885,0,0,0,17.308,17.8V6.26A2.885,2.885,0,0,0,14.423,3.375Zm0,28.846H2.885A2.885,2.885,0,0,0,0,35.106V46.644a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V35.106A2.885,2.885,0,0,0,14.423,32.221Zm75,31.731H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V66.837A2.885,2.885,0,0,0,89.423,63.952Zm0-57.692H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769A2.885,2.885,0,0,0,31.731,17.8H89.423a2.885,2.885,0,0,0,2.885-2.885V9.144A2.885,2.885,0,0,0,89.423,6.26Zm0,28.846H31.731a2.885,2.885,0,0,0-2.885,2.885V43.76a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V37.99A2.885,2.885,0,0,0,89.423,35.106Z",
				transform: 'translate(0 -3.375)'
			}
		)
	);

	var borderStyles = [
		{ value: 'dotted', label: 'dotted' },
		{ value: 'dashed', label: 'dashed' },
		{ value: 'solid', label: 'solid' },
		{ value: 'double', label: 'double' },
		{ value: 'groove', label: 'groove' },
		{ value: 'ridge', label: 'ridge' },
		{ value: 'inset', label: 'inset' },
		{ value: 'outset', label: 'outset' },
		{ value: 'none', label: 'none' },
		{ value: 'hidden', label: 'hidden' },
	];

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
			border_color: {
				'type' : 'string',
			},
			background_color: {
				'type' : 'string',
			},
			border_radius: {
				'type' : 'number',
				'default' : 0,
			},
			border_size: {
				'type' : 'number',
				'default' : 0,
			},
			border_style: {
				'type' : 'string',
				'default' : 'solid',
			},
			show_image: {
				'type' : 'boolean',
				'default' : true,
			},
			show_date: {
				'type' : 'boolean',
				'default' : true,
			},
			show_tags: {
				'type' : 'boolean',
				'default' : true,
			},
			image_radius: {
				'type' : 'number',
				'default' : 0,
			},
			date_format: {
				'type' : 'string',
			},
			new_window: {
				'type' : 'boolean',
				'default' : false,
			},
			nofollow: {
				'type' : 'boolean',
				'default' : false,
			},
			image_position: {
				'type' : 'string',
				'default' : 'left',
			},
			table_ratio: {
				'type' : 'string',
				'default' : 'full',
			},
			block_id: {
				'type' : 'string',
			},
		},
		edit: withColors( 'formColor' ) ( function( props ) {

			if ( ! props.attributes.block_id ) {
				props.setAttributes( { block_id: props.clientId } );
			}

			function changeImagePosition( ev ) {
				let image_position = ev.currentTarget.value;
				props.setAttributes( { image_position } );
			}

			function changeTableRatio( ev ) {
				let table_ratio = ev.currentTarget.value;
				props.setAttributes( { table_ratio } );
			}

			var dateFormats = block.date_formats;
			var placementclass = props.attributes.table_ratio === 'full' || ! props.attributes.show_image ? 'ngl-gutenberg-greyed' : '';

			return [

					el( ServerSideRender, {
						block: 'newsletterglue/article',
						attributes: props.attributes,
					} ),

					// This is block settings in sidebar.
					el( InspectorControls, {},
					
						el( PanelBody, { title: 'Container options', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Open links in new window',
									onChange: ( value ) => { props.setAttributes( { new_window: value } ); },
									checked: props.attributes.new_window,
								} )
							),

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Nofollow links',
									onChange: ( value ) => { props.setAttributes( { nofollow: value } ); },
									checked: props.attributes.nofollow,
								} )
							),

							el( BaseControl, {
									label: 'Table width ratio',
									className: 'ngl-gutenberg-base--fullwidth',
								},
								el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
									el( Button, {
										value: 'full',
										isPrimary: ( props.attributes.table_ratio === 'full' ),
										isSecondary: ( props.attributes.table_ratio !== 'full' ),
										onClick: changeTableRatio,
										label: 'Full',
									}, 'Full' ),
									el( Button, {
										value: '50_50',
										isPrimary: ( props.attributes.table_ratio === '50_50' ),
										isSecondary: ( props.attributes.table_ratio !== '50_50' ),
										onClick: changeTableRatio,
										label: '50:50'
									}, '50:50' ),
									el( Button, {
										value: '30_70',
										isPrimary: ( props.attributes.table_ratio === '30_70' ),
										isSecondary: ( props.attributes.table_ratio !== '30_70' ),
										onClick: changeTableRatio,
										label: '30:70'
									}, '30:70' ),
									el( Button, {
										value: '70_30',
										isPrimary: ( props.attributes.table_ratio === '70_30' ),
										isSecondary: ( props.attributes.table_ratio !== '70_30' ),
										onClick: changeTableRatio,
										label: '70:30'
									}, '70:30' ),
								)
							),

							el( BaseControl, {
									label: 'Image placement',
									className: 'ngl-gutenberg-base--fullwidth' + ' ' + placementclass,
								},
								el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
									el( Button, {
										value: 'left',
										isPrimary: ( props.attributes.image_position === 'left' ),
										isSecondary: ( props.attributes.image_position !== 'left' ),
										onClick: changeImagePosition,
										label: 'Left',
									}, 'Left' ),
									el( Button, {
										value: 'right',
										isPrimary: ( props.attributes.image_position === 'right' ),
										isSecondary: ( props.attributes.image_position !== 'right' ),
										onClick: changeImagePosition,
										label: 'Right'
									}, 'Right' ),
								)
							),

						),

						el( PanelBody, { title: 'Article embed options', initialOpen: true },

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show image',
									onChange: ( value ) => { props.setAttributes( { show_image: value } ); },
									checked: props.attributes.show_image,
								} )
							),

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show date',
									onChange: ( value ) => { props.setAttributes( { show_date: value } ); },
									checked: props.attributes.show_date,
								} )
							),

							el( BaseControl, {},
								el( ToggleControl, {
									label: 'Show tag(s)',
									onChange: ( value ) => { props.setAttributes( { show_tags: value } ); },
									checked: props.attributes.show_tags,
								} )
							),

							el( BaseControl, {},
								el( SelectControl, {
									label: 'Date format',
									value: props.attributes.date_format,
									onChange: ( value ) => { props.setAttributes( { date_format: value } ); },
									options: dateFormats,
								} )
							),

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Image border radius',
									value: props.attributes.image_radius,
									initialPosition: 0,
									min: 0,
									max: 50,
									allowReset: true,
									resetFallbackValue: 0,
									onChange: ( value ) => { props.setAttributes( { image_radius: value } ); },
								} ),
							),

						),

						el( PanelBody, { title: 'Border options', initialOpen: true },

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Border radius (pixels)',
									value: props.attributes.border_radius,
									initialPosition: 0,
									min: 0,
									max: 50,
									allowReset: true,
									resetFallbackValue: 0,
									onChange: ( value ) => { props.setAttributes( { border_radius: value } ); },
								} ),
							),

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Border thickness (pixels)',
									value: props.attributes.border_size,
									initialPosition: 0,
									min: 0,
									max: 20,
									allowReset: true,
									resetFallbackValue: 0,
									onChange: ( value ) => { props.setAttributes( { border_size: value } ); },
								} ),
							),

							el( BaseControl, {},
								el( SelectControl, {
									label: 'Border style',
									value: props.attributes.border_style,
									onChange: ( value ) => { props.setAttributes( { border_style: value } ); },
									options: borderStyles,
								} )
							),

						),

						el( PanelColorSettings, {
							initialOpen: true,
							title: 'Color options',
							colorSettings: [
								{
									value: props.attributes.border_color,
									label: 'Border color',
									onChange: ( value ) => props.setAttributes( { border_color: value } ),
								},
								{
									value: props.attributes.background_color,
									label: 'Background color',
									onChange: ( value ) => props.setAttributes( { background_color: value } ),
								},
							]
						} ),

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

						)

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