var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
	TextControl = wp.components.TextControl,
	InspectorControls = wp.editor.InspectorControls;

registerBlockType( 'newsletterglue/author', {

	title: 'NG: author byline',
	description: 'Use this block to show author byline in newsletter.',
	icon: 'layout',
	category: 'design', 
	keywords: [ 'newsletter', 'glue', 'group', 'container' ],

	edit: function( props ) {
		return [

			el( ServerSideRender, {
				block: 'newsletterglue/author',
				attributes: props.attributes,
			} ),

			el( InspectorControls, {},
				el( TextControl, {
					label: 'Foo',
					value: props.attributes.foo,
					onChange: ( value ) => { props.setAttributes( { foo: value } ); },
				} )
			),
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return null;
	},

} );