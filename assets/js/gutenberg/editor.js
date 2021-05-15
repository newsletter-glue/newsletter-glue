var el = wp.element.createElement;
var registerPlugin = wp.plugins.registerPlugin;
var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;

const { withSelect, withDispatch, dispatch, select } = wp.data;

var { TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, RangeControl, BaseControl, ButtonGroup, Button } = wp.components;

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
			title: 'Newsletter Glue',
		},
		el( BaseControl,
			{
				label: 'Web view',
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