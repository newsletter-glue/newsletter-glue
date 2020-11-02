( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText } = editor;
	const { Fragment } = element;
	const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button, ColorPicker } = components;

	const icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 31.5 31.5' },
		el( 'g', { transform: 'translate(-115 -126)' },
			el( 'path',
				{
					fill: '#3400FF',
					transform: 'translate(115 123.75)',
					d: "M30.984,12.8l.5-2.812A.844.844,0,0,0,30.656,9H25.4l1.028-5.758a.844.844,0,0,0-.831-.992H22.737a.844.844,0,0,0-.831.7L20.825,9H13.89l1.028-5.758a.844.844,0,0,0-.831-.992H11.23a.844.844,0,0,0-.831.7L9.318,9H3.757a.844.844,0,0,0-.831.7l-.5,2.813a.844.844,0,0,0,.831.992h5.26l-1.607,9H1.346a.844.844,0,0,0-.831.7l-.5,2.813A.844.844,0,0,0,.844,27H6.1L5.076,32.758a.844.844,0,0,0,.831.992H8.763a.844.844,0,0,0,.831-.7L10.675,27H17.61l-1.028,5.758a.844.844,0,0,0,.831.992H20.27a.844.844,0,0,0,.831-.7L22.182,27h5.561a.844.844,0,0,0,.831-.7l.5-2.813a.844.844,0,0,0-.831-.992h-5.26l1.607-9h5.561a.844.844,0,0,0,.831-.7Zm-12.57,9.7H11.479l1.607-9h6.935Z"
				}
			)
		)
	);

	registerBlockType( 'newsletterglue/metadata', {
		title: 'NG: Newsletter meta data',
		description: 'Add standard meta data to each post.',
		icon: icon,
		category: 'newsletterglue-blocks',
		attributes: {
			issue_title: {
				type: 'string',
			},
			post_link: {
				type: 'string',
				'default' : 'Read online',
			},
			post_location: {
				type: 'string',
			},
			show_in_blog: {
				'type' : 'boolean',
				'default' : newsletterglue_block_metadata.show_in_blog ? true : false
			},
			show_in_email: {
				'type' : 'boolean',
				'default' : newsletterglue_block_metadata.show_in_email ? true : false
			},
		},
		edit: function( props ) {

			return (

				el( Fragment, {},

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

					),

					// This is how the block is rendered in editor.
					el( 'div', { className: 'ngl-metadata' },
						el( RichText, {
							tagName: 'div',
							format: 'string',
							className: 'ngl-metadata-issue',
							onChange: ( value ) => { props.setAttributes( { issue_title: value } ); },
							value: props.attributes.issue_title,
							placeholder: 'Issue #',
							multiline: '&nbsp;'
						} ),
						el( 'div', { className: 'ngl-metadata-sep' }, '|' ),
						el( 'div', { className: 'ngl-metadata-date' },
							newsletterglue_meta.post_date
						),
						el( 'div', { className: 'ngl-metadata-sep' }, '|' ),
						el( 'img', {
							className: 'ngl-metadata-map-pin',
							src: newsletterglue_block_metadata.assets_uri + 'map-pin.png'
						} ),
						el( RichText, {
							tagName: 'div',
							format: 'string',
							className: 'ngl-metadata-map',
							onChange: ( value ) => { props.setAttributes( { post_location: value } ); },
							value: props.attributes.post_location,
							placeholder: 'Location',
							multiline: '&nbsp;'
						} ),
						el( 'div', { className: 'ngl-metadata-sep' }, '|' ),
						el( RichText, {
							tagName: 'div',
							format: 'string',
							className: 'ngl-metadata-permalink',
							onChange: ( value ) => { props.setAttributes( { post_link: value } ); },
							value: props.attributes.post_link,
							placeholder: 'Read online',
							multiline: '&nbsp;'
						} ),
						el( 'img', {
							className: 'ngl-metadata-permalink-arrow',
							src: newsletterglue_block_metadata.assets_uri + 'arrow.png'
						} ),
					)

				)

			);

		},

		// This is how the block is rendered in frontend.
		save: function( props ) {

			return (

					el( 'div', { className: 'ngl-metadata' },
						el( RichText.Content, {
							tagName: 'div',
							className: 'ngl-metadata-issue',
							value: props.attributes.issue ? props.attributes.issue : ''
						} ),
						el( 'div', { className: 'ngl-metadata-sep' }, '|' ),
						el( 'div', { className: 'ngl-metadata-date' },
							newsletterglue_meta.post_date
						),
						el( 'div', { className: 'ngl-metadata-sep' }, '|' ),
						el( 'img', {
							className: 'ngl-metadata-map-pin',
							src: newsletterglue_block_metadata.assets_uri + 'map-pin.png'
						} ),
						el( RichText.Content, {
							tagName: 'div',
							className: 'ngl-metadata-map',
							value: props.attributes.post_location,
						} ),
						el( 'div', { className: 'ngl-metadata-sep' }, '|' ),
						el( RichText.Content, {
							tagName: 'a',
							className: 'ngl-metadata-permalink',
							value: props.attributes.post_link,
							href: newsletterglue_meta.post_perma
						} ),
						el( 'img', {
							className: 'ngl-metadata-permalink-arrow',
							src: newsletterglue_block_metadata.assets_uri + 'arrow.png'
						} ),
					)

			);

		}

	} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);