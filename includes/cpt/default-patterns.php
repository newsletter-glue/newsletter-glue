<?php
/**
 * Default Patterns.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Default_Patterns class.
 */
class NGL_Default_Patterns {

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	/**
	 * Pattern exists?
	 */
	public function post_by_meta_exists( $meta_key, $value ) {
		$posts = get_posts( array(
			'post_type'		=> 'ngl_pattern',
			'post_status'	=> get_post_stati(),
			'meta_key'  	=> $meta_key,
			'meta_value' 	=> $value,
			'number'		=> 1,
		) );

		return ! empty( $posts ) ? $posts[0]->ID : false;
	}

	/**
	 * Create.
	 */
	public function create( $include = false ) {

		$defaults = $this->get_patterns();

		$found_post = 0;

		foreach( $defaults as $key => $pattern ) {

			if ( $include && isset( $defaults[ $include ] ) && $key != $include ) {
				continue;
			}

			$args = array(
				'post_type' 	=> 'ngl_pattern',
				'post_status'	=> 'publish',
				'post_author'	=> 1,
				'post_title'	=> $pattern[ 'title' ],
				'post_content'	=> $pattern[ 'content' ],
			);

			$found_post = $this->post_by_meta_exists( '_ngl_core_pattern', $key );

			if ( $found_post ) {
				wp_update_post( array_merge( array( 'ID' => $found_post ), $args ) );
				continue;
			}

			$post_id = wp_insert_post( $args );

			wp_set_object_terms( $post_id, $pattern[ 'category' ], 'ngl_pattern_category' );

			update_post_meta( $post_id, '_ngl_core_pattern', $key );
		}
	}

	/**
	 * Get patterns list.
	 */
	public function get_patterns() {

		/********************************/
		$patterns[ 'footer_1' ] = array(
			'title'		=> 'Footer #1',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/callout {"font_color":"#707070","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#f9f9f9;color:#707070;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13}}} -->
<p class="has-text-align-center" style="font-size:13px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:13px">If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p>
<!-- /wp:paragraph --></section>
<!-- /wp:newsletterglue/callout -->'
		);

		/********************************/
		$patterns[ 'footer_2' ] = array(
			'title'		=> 'Footer #2',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/callout {"cta_padding":30,"cta_padding2":30} -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:#f9f9f9;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:30px;padding-right:30px;text-align:left;margin-left:0;margin-right:0"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":12},"color":{"text":"#707070"}}} -->
<p class="has-text-color" style="color:#707070;font-size:12px">Published with ♥ by Lesley.<br>Here’s where you can find me online:</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":10} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:newsletterglue/share {"icon_size":18,"icon_shape":"default","icon_color":"grey","add_description":false} -->
<div class="wp-block-newsletterglue-share undefined wp-block-newsletter-share-left ngl-image-size-18" data-image-size="18"><!-- wp:newsletterglue/share-link {"service":"twitter","url":"https://twitter.com/","icon_size":18,"icon_shape":"default","icon_color":"grey"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-twitter" href="https://twitter.com/" target="_blank" rel="noopener"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/default/grey/twitter.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link -->

<!-- wp:newsletterglue/share-link {"service":"instagram","url":"https://instagram.com/","icon_size":18,"icon_shape":"default","icon_color":"grey"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-instagram" href="https://instagram.com/" target="_blank" rel="noopener"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/default/grey/instagram.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link --></div>
<!-- /wp:newsletterglue/share --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":12},"color":{"text":"#707070"}}} -->
<p class="has-text-align-right has-text-color" style="color:#707070;font-size:12px">116 New Montgomery Street, Suite 400 San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":10} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":12}}} -->
<p class="has-text-align-right" style="font-size:12px"><a href="{{ unsubscribe_link }}"><span style="color:#707070" class="has-inline-color">Unsubscribe here.</span></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:newsletterglue/callout -->'
		);

		/********************************/
		$patterns[ 'footer_3' ] = array(
			'title'		=> 'Footer #3',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/callout {"bg_color":"#0d566c","font_color":"#FFFFFF","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#0d566c;color:#FFFFFF;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:newsletterglue/share {"alignment":"right","icon_size":20,"icon_shape":"default","icon_color":"white","add_description":false} -->
<div class="wp-block-newsletterglue-share undefined wp-block-newsletter-share-right ngl-image-size-20" data-image-size="20"><!-- wp:newsletterglue/share-link {"service":"twitter","url":"https://twitter.com/","icon_size":20,"icon_shape":"default","icon_color":"white"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-twitter" href="https://twitter.com/" target="_blank" rel="noopener"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/default/white/twitter.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link -->

<!-- wp:newsletterglue/share-link {"service":"instagram","url":"https://instagram.com/","icon_size":20,"icon_shape":"default","icon_color":"white"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-instagram" href="https://instagram.com/" target="_blank" rel="noopener"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/share/default/white/instagram.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link --></div>
<!-- /wp:newsletterglue/share -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":13}}} -->
<p style="font-size:13px">© 2021 Jane Smith<br>116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":14}},"textColor":"white"} -->
<p class="has-text-align-right has-white-color has-text-color" style="font-size:14px"><a href="{{ unsubscribe_link }}">Unsubscribe</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:newsletterglue/callout -->'
		);

		/********************************/
		$patterns[ 'footer_4' ] = array(
			'title'		=> 'Footer #4',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/callout {"bg_color":"#0d566c","font_color":"#FFFFFF","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#0d566c;color:#FFFFFF;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:image {"sizeSlug":"large","className":"is-style-default"} -->
<figure class="wp-block-image size-large is-style-default"><img src="http://localhost/wp-content/plugins/newsletter-glue-pro/assets/images/email/logo-white.png" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"color":"white","className":"is-style-default"} -->
<hr class="wp-block-separator has-text-color has-background has-white-background-color has-white-color is-style-default"/>
<!-- /wp:separator -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":13}},"textColor":"white"} -->
<p class="has-text-align-right has-white-color has-text-color" style="font-size:13px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":5} -->
<div style="height:5px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":13}},"textColor":"white"} -->
<p class="has-text-align-right has-white-color has-text-color" style="font-size:13px"><a href="http://localhost/wp-admin/%7B%7B%20unsubscribe_link%20%7D%7D">Unsubscribe here</a>.</p>
<!-- /wp:paragraph --></section>
<!-- /wp:newsletterglue/callout -->'
		);

		/********************************/
		$patterns[ 'footer_5' ] = array(
			'title'		=> 'Footer #5',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":14},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:14px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":14},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:14px">If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'footer_6' ] = array(
			'title'		=> 'Footer #6',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"customColor":"#0088a0","className":"is-short is-style-twentytwentyone-separator-thick"} -->
<hr class="wp-block-separator has-text-color has-background is-short is-style-twentytwentyone-separator-thick" style="background-color:#0088a0;color:#0088a0"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"style":{"color":{"text":"#707070"},"typography":{"fontSize":14}}} -->
<p class="has-text-color" style="color:#707070;font-size:14px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#707070"},"typography":{"fontSize":14}}} -->
<p class="has-text-color" style="color:#707070;font-size:14px">If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p>
<!-- /wp:paragraph -->'
		);

		return $patterns;
	}

}