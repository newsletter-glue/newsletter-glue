( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, ServerSideRender, RangeControl } = components;

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

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 47 36' },
		el( 'g',
			{ 
				transform: 'translate(-783 -315.935)'
			},
			el( 'rect',
			{
				fill: '#3400FF',
				width: 38,
				height: 27,
				rx: 3,
				transform: 'translate(783 315.935)'
			} ),
			el( 'g',
			{
				transform: 'translate(793 324.935)',
				fill: '#fff',
				stroke: '#3400FF',
				strokeWidth: '2px',
				strokeDasharray: '2 2'
			},
				el( 'rect', {
					width: 37,
					height: 27,
					rx: 3
				} ),
				el( 'rect', {
					width: 35,
					height: 25,
					x: 1,
					y: 1,
					rx: 2
				} )
			)
		)
	);

	registerBlockType( 'newsletterglue/callout', {
		title: 'NG: Callout card',
		description: 'Customise the background and border of this card to help its content stand out.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'card', 'callout' ],
		attributes: {
			border_color: {
				'type' : 'string',
				'default' : '#f9f9f9',
			},
			bg_color: {
				'type' : 'string',
				'default' : '#f9f9f9',
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
				'default' : newsletterglue_block_callout.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : newsletterglue_block_callout.show_in_email ? true : false
			},
		},

		edit: withColors( 'formColor' ) ( function( props ) {

			var formStyles = {
				backgroundColor : props.attributes.bg_color,
				borderColor : props.attributes.border_color,
				borderRadius : props.attributes.border_radius,
				borderStyle : props.attributes.border_style,
				borderWidth : props.attributes.border_size,
				paddingLeft : props.attributes.cta_padding,
				paddingRight : props.attributes.cta_padding,
				marginTop : props.attributes.cta_margin ? props.attributes.cta_margin : 0,
				marginBottom : props.attributes.cta_margin ? props.attributes.cta_margin : 0
			};

			const blockTemplate = [
				[ 'core/paragraph', { }, [] ],
			];

			return (

				el( Fragment, {},

					el( InspectorControls, {},

						el( PanelBody, { title: 'Container options', initialOpen: true },

							el( PanelRow, {},
								el( RangeControl, {
									label: 'Padding (pixels)',
									value: props.attributes.cta_padding,
									initialPosition: 20,
									min: 0,
									max: 100,
									allowReset: true,
									resetFallbackValue: 20,
									onChange: ( value ) => { props.setAttributes( { cta_padding: value } ); },
								} ),
							),

							el( PanelRow, {},
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

							el( PanelRow, {},
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

							el( PanelRow, {},
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

							el( PanelRow, {},
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
							]
						} ),

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
				borderColor : props.attributes.border_color,
				borderRadius : props.attributes.border_radius,
				borderStyle : props.attributes.border_style,
				borderWidth : props.attributes.border_size,
				paddingLeft : props.attributes.cta_padding,
				paddingRight : props.attributes.cta_padding,
				marginTop : props.attributes.cta_margin ? props.attributes.cta_margin : 0,
				marginBottom : props.attributes.cta_margin ? props.attributes.cta_margin : 0
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