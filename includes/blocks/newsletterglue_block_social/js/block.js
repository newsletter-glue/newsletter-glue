( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText, AlignmentToolbar, BlockControls } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = components;
	const ServerSideRender = wp.serverSideRender;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 47 47' }, 
		el( 'g', { transform: 'translate(-1547 -1156)' },
			el( 'g', {
				transform: 'translate(1547 1156)',
				fill: '#fff',
				stroke: '#dd3714',
				strokeWidth: '4px'
			},
				el( 'rect', {
					width: 47,
					height: 47
				} ),
				el( 'rect', {
					x: 2,
					y: 2,
					width: 43,
					height: 43
				} )
			),
			el( 'path', {
				fill: '#dd3714',
				d: 'M19.923,24.1l2.892,2.892,9.64-9.64-9.64-9.64L19.923,10.6l6.748,6.748ZM16.067,10.6,13.175,7.712l-9.64,9.64,9.64,9.64L16.067,24.1,9.319,17.352Z',
				transform: 'translate(1552.465 1162.288)'
			} )
		)
	);

	registerBlockType( 'newsletterglue/social', {
		title: 'NG: Social embed',
		description: 'Embed posts from social media by pasting a link.',
		icon: icon,
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'social', 'embed' ],
		attributes: {
			show_in_blog: {
				'type' : 'boolean',
				'default' : newsletterglue_block_social.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : newsletterglue_block_social.show_in_email ? true : false
			},
			block_id: {
				'type' : 'string',
			},
		},
		edit: function( props ) {

			if ( ! props.attributes.block_id ) {
				props.setAttributes( { block_id: props.clientId } );
			}

			return [
	
					el( ServerSideRender, {
						block: 'newsletterglue/social',
						attributes: props.attributes,
					} ),

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

					)

			]

		},

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