( function( blocks, editor, element, components ) {

const el = wp.element.createElement;
const registerPlugin = wp.plugins.registerPlugin;
const PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
const { withSelect, withDispatch, dispatch, select } = wp.data;
const { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button, IconButton, Toolbar, ToolbarButton, ToolbarGroup, ToolbarDropdownMenu } = components;
const { createHigherOrderComponent } = wp.compose;
const { InspectorControls, BlockControls } = editor;

/**
 * Add a panel to control additional Newsletter settings.
 */
function NewsletterGluePanel() {
	
    if ( ! select('core/editor').getEditedPostAttribute('meta')['_webview']) {
        dispatch('core/editor').editPost({ meta: { _webview: 'blog' } });
    }

	var webView = select('core/editor').getEditedPostAttribute('meta')['_webview'];

	return el(
		PluginDocumentSettingPanel,
		{
			name: 'newsletterglue-doc-plugin',
			className: 'newsletterglue-doc-plugin',
			title: 'Web view',
		},
		el( BaseControl,
			{
				label: 'When reading online, visitors see',
				className: 'ngl-gutenberg-base--fullwidth',
			},
			el( ButtonGroup, { className: 'ngl-gutenberg--fullwidth' },
				el( Button, {
					value: 'email',
					isPrimary: ( webView === 'email' ),
					isSecondary: ( webView !== 'email' ),
					onClick: function( ev ) { wp.data.dispatch('core/editor').editPost( { meta: {_webview: 'email'} } ); jQuery( 'button[value=email]' ).addClass( 'is-primary' ).removeClass( 'is-secondary' ); jQuery( 'button[value=blog]' ).removeClass( 'is-primary' ).addClass( 'is-secondary' ); },
					label: 'Email HTML only',
				}, 'Email HTML only' ),
				el( Button, {
					value: 'blog',
					isPrimary: ( webView === 'blog' ),
					isSecondary: ( webView !== 'blog' ),
					onClick: function( ev ) { wp.data.dispatch('core/editor').editPost( { meta: {_webview: 'blog'} } ); jQuery( 'button[value=blog]' ).addClass( 'is-primary' ).removeClass( 'is-secondary' ); jQuery( 'button[value=email]' ).removeClass( 'is-primary' ).addClass( 'is-secondary' ); },
					label: 'Blog theme'
				}, 'Blog theme' ),
			)
		)
	);
}

registerPlugin( 'newsletterglue-doc-plugin', {
  render: NewsletterGluePanel,
  icon: '',
} );

} ) (
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
);