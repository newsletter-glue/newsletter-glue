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
	public function create() {

		$defaults = $this->get_patterns();

		$found_post = 0;

		foreach( $defaults as $key => $pattern ) {

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
			'content'	=> '<!-- wp:newsletterglue/callout {"font_color":"#707070"} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#f9f9f9;color:#707070;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;margin-left:0;margin-right:0"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13}}} -->
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
			'content'	=> '<!-- wp:newsletterglue/callout -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:#f9f9f9;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;text-align:left;margin-left:0;margin-right:0"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":12},"color":{"text":"#707070"}}} -->
<p class="has-text-color" style="color:#707070;font-size:12px">Published with ♥ by Lesley. Here’s where you can find me online:</p>
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

		return $patterns;
	}

}