( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks } = editor;
	const { Fragment } = element;
	const { TextControl, ToggleControl, Panel, PanelBody, PanelRow } = components;

    registerBlockType( 'newsletterglue/group', {
        title: 'NG: show/hide content',
		description: 'Use this group block to show/hide content in posts and emails.',
        icon: 'layout',
        category: 'newsletterglue-blocks',
        keywords: [ 'newsletter', 'glue', 'group', 'container' ],
		attributes: {
			showblog: {
				'type': 'boolean',
				'default': newsletterglue_block_show_hide_content.showblog ? true : false,
			},
			showemail: {
				'type': 'boolean',
				'default': newsletterglue_block_show_hide_content.showemail ? true : false,
			}
		},
        edit: function( props ) {
			return (
				el( Fragment, {},
					el( InspectorControls, {},
						el( PanelBody, { title: 'Show/hide - newsletter block', initialOpen: true },

							el( PanelRow, {},
								el( ToggleControl,
									{
										label: 'Show in blog post',
										onChange: ( value ) => {
											props.setAttributes( { showblog: value } );
										},
										checked: props.attributes.showblog,
									}
								)
							),
							el( PanelRow, {},
								el( ToggleControl,
									{
										label: 'Show in email newsletter',
										onChange: ( value ) => {
											props.setAttributes( { showemail: value } );
										},
										checked: props.attributes.showemail,
									}
								)
							)
						),
					),
		 
					/*  
					 * Here will be your block markup 
					 */
					el( 'section', { className: props.className },
						el( InnerBlocks )
					)
				)
			);
		},
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
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);