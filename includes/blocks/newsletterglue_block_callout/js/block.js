( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, ServerSideRender } = components;

	registerBlockType( 'newsletterglue/callout', {
		title: 'NG: callout card',
		description: 'Use this block to add a callout to your newsletter.',
		icon: 'layout',
		category: 'newsletterglue-blocks',
		keywords: [ 'newsletter', 'glue', 'card', 'callout' ],
		attributes: {

		},

		edit: withColors( 'formColor' ) ( function( props ) {
			return (

				el( Fragment, {},

					el( InspectorControls, {},

					),
		 
					/*  
					 * Here will be your block markup 
					 */
					el( 'section', { className: props.className },
						el( InnerBlocks )
					)
				)
			);
		} ),

		save: function( props, className ) {
            return (
                el( 'section',
					{
						className: props.className
					},
					el( InnerBlocks.Content )
                )
            );
        },

	} );

} ) (
	window.wp.blocks,
	window.wp.editor,
	window.wp.element,
	window.wp.components
);