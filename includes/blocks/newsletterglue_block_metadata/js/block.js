( function( blocks, editor, element, components ) {

	const el = element.createElement;
    const { registerBlockType } = blocks;
	const { RichText, InspectorControls, InnerBlocks, PanelColorSettings, withColors, MediaUpload, PlainText, BlockControls, AlignmentToolbar } = editor;
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
			text_color: {
				'type' : 'string',
				'default' : '#666666',
			},
			divider_style: {
				'type' : 'string',
				'default' : 'line',
			},
			alignment: {
				type: 'string',
				'default' : 'center',
			},
		},
		edit: withColors( 'formColor' ) ( function( props ) {

			var metaStyles = {
				color: props.attributes.text_color,
				textAlign: props.attributes.alignment,
			};

			var dividerStyles = [
				{ value: 'line', label: 'Line' },
				{ value: 'dot', label: 'Dot' }
			];

			var divider = props.attributes.divider_style == 'dot' ? '•' : '|';

			var metaPic = '';
			metaPic = [
				el( 'div', { className: 'ngl-metadata-pic' },
					el( 'img', { src: newsletterglue_meta.profile_pic, className: 'avatar avatar-32 photo' },

					)
				),
				el( 'div', { className: 'ngl-metadata-author' },
					newsletterglue_meta.author_name
				),
				el( 'div', { className: 'ngl-metadata-sep' }, divider )
			];

			function onChangeAlignment( newAlignment ) {
				props.setAttributes( { alignment: newAlignment } );
			}

			return (

				el( Fragment, {},

					// This is block settings in sidebar.
					el( InspectorControls, {},

						el( PanelBody, { title: 'General options', initialOpen: true },

							el( PanelRow, {},
								el( SelectControl, {
									label: 'Divider type',
									value: props.attributes.divider_style,
									onChange: ( value ) => { props.setAttributes( { divider_style: value } ); },
									options: dividerStyles,
								} )
							),

						),

						el( PanelColorSettings, {
							initialOpen: true,
							title: 'Color options',
							colorSettings: [
								{
									value: props.attributes.text_color,
									label: 'Font color',
									onChange: ( value ) => props.setAttributes( { text_color: value } ),
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

					el( BlockControls, {},
						el( AlignmentToolbar,
							{
								value: props.attributes.alignment,
								onChange: onChangeAlignment
							}
						)
					),

					// This is how the block is rendered in editor.
					el( 'div', { className: 'ngl-metadata', style: metaStyles },
						metaPic,
						el( RichText, {
							tagName: 'div',
							format: 'string',
							className: 'ngl-metadata-issue',
							onChange: ( value ) => { props.setAttributes( { issue_title: value } ); },
							value: props.attributes.issue_title,
							placeholder: 'Issue #',
							multiline: '&nbsp;'
						} ),
						el( 'div', { className: 'ngl-metadata-sep' }, divider ),
						el( 'div', { className: 'ngl-metadata-date' },
							newsletterglue_meta.post_date
						),
						el( 'div', { className: 'ngl-metadata-sep' }, divider ),
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
						el( 'div', { className: 'ngl-metadata-sep' }, divider ),
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

		} ),

		// This is how the block is rendered in frontend.
		save: function( props ) {

			var divider = props.attributes.divider_style == 'dot' ? '•' : '|';

			var metaStyles = {
				color: props.attributes.text_color,
				textAlign: props.attributes.alignment,
			};

			var metaTitle = '';
			if ( props.attributes.issue_title ) {
				metaTitle = [
					el( RichText.Content, {
						tagName: 'div',
						className: 'ngl-metadata-issue',
						value: props.attributes.issue_title ? props.attributes.issue_title : ''
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaDate = '';
			if ( props ) {
				metaDate = [
					el( 'div', { className: 'ngl-metadata-date' },
						newsletterglue_meta.post_date
					),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaLocation = '';
			if ( props.attributes.post_location ) {
				metaLocation = [
					el( 'img', {
						className: 'ngl-metadata-map-pin',
						src: newsletterglue_block_metadata.assets_uri + 'map-pin.png'
					} ),
					el( RichText.Content, {
						tagName: 'div',
						className: 'ngl-metadata-map',
						value: props.attributes.post_location,
					} ),
					el( 'div', { className: 'ngl-metadata-sep' }, divider )
				];
			}

			var metaPic = '';
			metaPic = [
				el( 'div', { className: 'ngl-metadata-pic' },
					el( 'img', { src: newsletterglue_meta.profile_pic, className: 'avatar avatar-32 photo' },

					)
				),
				el( 'div', { className: 'ngl-metadata-author' },
					newsletterglue_meta.author_name
				),
				el( 'div', { className: 'ngl-metadata-sep' }, divider )
			];

			var metaPermalink = el( RichText.Content, {
				tagName: 'a',
				className: 'ngl-metadata-permalink',
				value: props.attributes.post_link,
				href: newsletterglue_meta.post_perma
			} );

			return (

					el( 'div', { className: 'ngl-metadata', style: metaStyles },
						metaPic,
						metaTitle,
						metaDate,
						metaLocation,
						metaPermalink,
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