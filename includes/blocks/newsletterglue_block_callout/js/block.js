( function( blocks, editor, element, components ) {

	const block = newsletterglue_block_callout;
	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, BlockControls, AlignmentToolbar } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, ServerSideRender, RangeControl, BaseControl } = components;

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

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 24 24' },
		el( 'path',
			{ 
				d: 'M21 15V18H24V20H21V23H19V20H16V18H19V15H21M14 18H3V6H19V13H21V6C21 4.89 20.11 4 19 4H3C1.9 4 1 4.89 1 6V18C1 19.11 1.9 20 3 20H14V18Z',
				fill: '#DD3714'
			}
		)
	);

	registerBlockType( 'newsletterglue/callout', {
		title: 'NG: Callout card',
		description: 'Customise the background and border of this card to help its content stand out.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'card', 'callout' ],
		attributes: {
			alignment: {
				'type' : 'string',
				'default' : 'left',
			},
			border_color: {
				'type' : 'string',
				'default' : '#f9f9f9',
			},
			bg_color: {
				'type' : 'string',
				'default' : '#f9f9f9',
			},
			font_color: {
				'type' : 'string',
			},
			border_radius: {
				'type' : 'number',
			},
			border_size: {
				'type' : 'number',
				'default' : 1,
			},
			border_style: {
				'type' : 'string',
				'default' : 'solid',
			},
			cta_padding: {
				'type' : 'number',
				'default' : 20,
			},
			cta_margin: {
				'type' : 'number',
				'default' : 25,
			},
			show_in_blog: {
				'type' : 'boolean',
				'default' : block.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : block.show_in_email ? true : false
			},
		},

		edit: withColors( 'formColor' ) ( function( props ) {

			var formStyles = {
				backgroundColor : props.attributes.bg_color,
				color: props.attributes.font_color,
				borderColor : props.attributes.border_color,
				borderRadius : props.attributes.border_radius,
				borderStyle : props.attributes.border_style,
				borderWidth : props.attributes.border_size,
				paddingLeft : props.attributes.cta_padding,
				paddingRight : props.attributes.cta_padding,
				marginTop : props.attributes.cta_margin ? props.attributes.cta_margin : 0,
				marginBottom : props.attributes.cta_margin ? props.attributes.cta_margin : 0,
				textAlign: props.attributes.alignment
			};

			function onChangeAlignment( newAlignment ) {
				props.setAttributes( { alignment: newAlignment } );
			}

			const blockTemplate = [
				[ 'core/paragraph', { }, [] ],
			];

			return (

				el( Fragment, {},

					el( InspectorControls, {},

						el( PanelBody, { title: 'Container options', initialOpen: true },

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Padding width (pixels)',
									value: props.attributes.cta_padding,
									initialPosition: 20,
									min: 0,
									max: 100,
									allowReset: true,
									resetFallbackValue: 20,
									onChange: ( value ) => { props.setAttributes( { cta_padding: value } ); },
								} ),
							),

							el( BaseControl, {},
								el( RangeControl, {
									label: 'Margin (pixels)',
									value: props.attributes.cta_margin,
									initialPosition: 25,
									min: 0,
									max: 100,
									allowReset: true,
									resetFallbackValue: 25,
									onChange: ( value ) => { props.setAttributes( { cta_margin: value } ); },
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
									initialPosition: 1,
									min: 1,
									max: 20,
									allowReset: true,
									resetFallbackValue: 1,
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
									value: props.attributes.bg_color,
									label: 'Background color',
									onChange: ( value ) => props.setAttributes( { bg_color: value } ),
								},
								{
									value: props.attributes.font_color,
									label: 'Font color',
									onChange: ( value ) => props.setAttributes( { font_color: value } ),
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
						),

					),

					el( BlockControls, {},
						el( AlignmentToolbar,
							{
								value: props.attributes.alignment,
								onChange: onChangeAlignment
							}
						)
					),

					/*  
					 * Here will be your block markup 
					 */
					el( 'section', {
							className: props.className,
							style: formStyles
						},
						el( InnerBlocks, { template: blockTemplate }
						
						)
					)
				)
			);
		} ),

		save: function( props, className ) {

			var formStyles = {
				backgroundColor : props.attributes.bg_color,
				color: props.attributes.font_color,
				borderColor : props.attributes.border_color,
				borderRadius : props.attributes.border_radius,
				borderStyle : props.attributes.border_style,
				borderWidth : props.attributes.border_size,
				paddingLeft : props.attributes.cta_padding,
				paddingRight : props.attributes.cta_padding,
				marginTop : props.attributes.cta_margin ? props.attributes.cta_margin : 0,
				marginBottom : props.attributes.cta_margin ? props.attributes.cta_margin : 0,
				textAlign: props.attributes.alignment
			};

            return (
                el( 'section',
					{
						className: props.className,
						style: formStyles
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