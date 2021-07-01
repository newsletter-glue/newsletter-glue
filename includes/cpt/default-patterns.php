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

		global $current_user;

		$defaults = $this->get_patterns();

		$found_post = 0;

		foreach( $defaults as $key => $pattern ) {

			if ( $include && isset( $defaults[ $include ] ) && $key != $include ) {
				continue;
			}

			$args = array(
				'post_type' 	=> 'ngl_pattern',
				'post_status'	=> 'publish',
				'post_author'	=> $current_user->ID,
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

		global $current_user;

		$email_bg 		= newsletterglue_get_theme_option( 'email_bg' );
		$container_bg 	= newsletterglue_get_theme_option( 'container_bg' );

		$logo_url = NGL_PLUGIN_URL . 'assets/images/email/logo-placeholder.png';
		$ratio = 614 / 186;
		$logo_s_w = 135;
		$logo_s_h = ceil( 135 / $ratio );

		// Use current logo.
		$id = get_option( 'newsletterglue_logo_id' );
		if ( $id && wp_get_attachment_url( $id ) ) {

			$logo_url 	= wp_get_attachment_url( $id );
			$data  		= wp_get_attachment_image_src( $id, 'full' );
			$width 		= $data[1];
			$height		= $data[2];

			$w_1 = 135;

			if ( $width > $w_1 ) {
				$ratio = $width / $height;
				$logo_s_w = $w_1;
				$logo_s_h = ceil( $w_1 / $ratio );
			}
		}

		/********************************/
		$patterns[ 'header_1' ] = array(
			'title'		=> 'Header with Banner + Description',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/callout {"cta_padding":0,"cta_padding2":0} -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:' . $email_bg . ';border-color:' . $email_bg . ';border-style:none;border-width:0;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;text-align:left;margin-left:0;margin-right:0"><!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"30%"} -->
<div class="wp-block-column" style="flex-basis:30%"><!-- wp:image {"align":"left","width":' . $logo_s_w . ',"height":' . $logo_s_h . ',"sizeSlug":"large"} -->
<div class="wp-block-image"><figure class="alignleft size-large is-resized"><img src="' . $logo_url . '" alt="" width="' . $logo_s_w . '" height="' . $logo_s_h . '"/></figure></div>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%"><!-- wp:paragraph {"align":"right","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-right has-text-color" style="color:#707070;font-size:13px">A weekly newsletter to help you create better products, and understand the broader impact of technology on our work and our lives.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"right","style":{"color":{"text":"#707070"},"typography":{"fontSize":13}}} -->
<p class="has-text-align-right has-text-color" style="color:#707070;font-size:13px"><a href="http://{{ webversion }}">Read online</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:image {"sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="' . NGL_PLUGIN_URL . 'assets/images/email/asset-3.png" alt=""/></figure>
<!-- /wp:image --></section>
<!-- /wp:newsletterglue/callout -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'header_2' ] = array(
			'title'		=> 'Header with Banner',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/callout {"cta_padding":0,"cta_padding2":0} -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:' . $email_bg . ';border-color:' . $email_bg . ';border-style:none;border-width:0;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;text-align:left;margin-left:0;margin-right:0"><!-- wp:paragraph {"align":"right","style":{"color":{"text":"#707070"},"typography":{"fontSize":14}}} -->
<p class="has-text-align-right has-text-color" style="color:#707070;font-size:14px"><a href="{{ webversion }}">Read online</a></p>
<!-- /wp:paragraph -->

<!-- wp:image {"sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="' . NGL_PLUGIN_URL . 'assets/images/email/asset-2.png" alt=""/></figure>
<!-- /wp:image --></section>
<!-- /wp:newsletterglue/callout -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:newsletterglue/metadata {"alignment":"left","date_format":"l, j M Y","show_date":false,"show_location":false,"show_readonline":false,"post_id":"0","readingtime":"0 mins","post_date":"Saturday, 19 Jun 2021"} -->
<div class="wp-block-newsletterglue-metadata ngl-metadata" style="color:#666666;text-align:left"><div class="ngl-metadata-pic"><img src="' . get_avatar_url( $current_user->user_email ) . '" class="avatar avatar-32 photo"/></div><div class="ngl-metadata-author">' . $current_user->display_name . '</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-issue">Issue #</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-readtime">Reading time:</div><div class="ngl-metadata-readtime-ajax">0 mins</div><div class="ngl-metadata-sep">|</div></div>
<!-- /wp:newsletterglue/metadata -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'header_3' ] = array(
			'title'		=> 'Header Minimal with Logo',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/callout {"cta_padding":40,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:' . $email_bg . ';border-color:' . $email_bg . ';border-style:none;border-width:0;padding-top:40px;padding-bottom:40px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:image {"align":"center","width":' . $logo_s_w . ',"height":' . $logo_s_h . ',"sizeSlug":"large"} -->
<div class="wp-block-image"><figure class="aligncenter size-large is-resized"><img src="' . $logo_url . '" alt="" width="' . $logo_s_w . '" height="' . $logo_s_h . '"/></figure></div>
<!-- /wp:image --></section>
<!-- /wp:newsletterglue/callout -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:newsletterglue/metadata {"alignment":"right","date_format":"l, j M Y","show_author":false,"show_date":false,"show_location":false,"post_id":"0","readingtime":"0 mins","read_online_link":"email","post_date":"Saturday, 19 Jun 2021"} -->
<div class="wp-block-newsletterglue-metadata ngl-metadata" style="color:#666666;text-align:right"><div class="ngl-metadata-issue">Issue #</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-readtime">Reading time:</div><div class="ngl-metadata-readtime-ajax">0 mins</div><div class="ngl-metadata-sep">|</div><a class="ngl-metadata-permalink" href="{{ webversion }}">Read online</a><img class="ngl-metadata-permalink-arrow" src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/img/arrow.png"/></div>
<!-- /wp:newsletterglue/metadata -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'header_4' ] = array(
			'title'		=> 'Header with Coloured Bar',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/callout {"bg_color":"#0088a0","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:#0088a0;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%"><!-- wp:image {"align":"left","width":135,"height":41,"sizeSlug":"large"} -->
<div class="wp-block-image"><figure class="alignleft size-large is-resized"><img src="' . NGL_PLUGIN_URL . 'assets/images/email/logo-placeholder.png" alt="" width="135" height="41"/></figure></div>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%"><!-- wp:newsletterglue/metadata {"text_color":"#FFFFFF","alignment":"right","date_format":"l, j M Y","show_author":false,"show_date":false,"show_location":false,"post_id":"406","readingtime":"1 mins","read_online_link":"email","post_date":"Friday, 25 Jun 2021"} -->
<div class="wp-block-newsletterglue-metadata ngl-metadata" style="color:#FFFFFF;text-align:right"><div class="ngl-metadata-issue">Issue #</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-readtime">Reading time:</div><div class="ngl-metadata-readtime-ajax">1 mins</div><div class="ngl-metadata-sep">|</div><a class="ngl-metadata-permalink" href="{{ webversion }}">Read online</a><img class="ngl-metadata-permalink-arrow" src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/img/arrow.png"/></div>
<!-- /wp:newsletterglue/metadata --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:newsletterglue/callout -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'header_5' ] = array(
			'title'		=> 'Header aligned Center',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:spacer {"height":40} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:image {"align":"center","width":' . $logo_s_w . ',"height":' . $logo_s_h . ',"sizeSlug":"large"} -->
<div class="wp-block-image"><figure class="aligncenter size-large is-resized"><img src="' . $logo_url . '" alt="" width="' . $logo_s_w . '" height="' . $logo_s_h . '"/></figure></div>
<!-- /wp:image -->

<!-- wp:spacer {"height":10} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:newsletterglue/metadata {"date_format":"l, j M Y","show_author":false,"show_date":false,"show_location":false,"post_id":"0","readingtime":"1 mins","post_date":"Saturday, 19 Jun 2021"} -->
<div class="wp-block-newsletterglue-metadata ngl-metadata" style="color:#666666;text-align:center"><div class="ngl-metadata-issue">Issue #</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-readtime">Reading time:</div><div class="ngl-metadata-readtime-ajax">1 mins</div><div class="ngl-metadata-sep">|</div><a class="ngl-metadata-permalink" href="{{ blog_post }}">Read online</a><img class="ngl-metadata-permalink-arrow" src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/img/arrow.png"/></div>
<!-- /wp:newsletterglue/metadata -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"customColor":"#eeeeee","className":"is-style-default"} -->
<hr class="wp-block-separator has-text-color has-background is-style-default" style="background-color:#eeeeee;color:#eeeeee"/>
<!-- /wp:separator -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'header_6' ] = array(
			'title'		=> 'Header Minimal with Separator',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:image {"align":"left","width":135,"height":41,"sizeSlug":"large"} -->
<div class="wp-block-image"><figure class="alignleft size-large is-resized"><img src="' . NGL_PLUGIN_URL . 'assets/images/email/logo-placeholder.png" alt="" width="135" height="41"/></figure></div>
<!-- /wp:image -->

<!-- wp:newsletterglue/metadata {"alignment":"left","date_format":"l, j M Y","show_author":false,"show_date":false,"show_location":false,"post_id":"408","readingtime":"1 mins","post_date":"Friday, 25 Jun 2021"} -->
<div class="wp-block-newsletterglue-metadata ngl-metadata" style="color:#666666;text-align:left"><div class="ngl-metadata-issue">Issue #</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-readtime">Reading time:</div><div class="ngl-metadata-readtime-ajax">1 mins</div><div class="ngl-metadata-sep">|</div><a class="ngl-metadata-permalink" href="{{ blog_post }}">Read online</a><img class="ngl-metadata-permalink-arrow" src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/img/arrow.png"/></div>
<!-- /wp:newsletterglue/metadata -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"customColor":"#0088a0","className":"is-short is-style-twentytwentyone-separator-thick"} -->
<hr class="wp-block-separator has-text-color has-background is-short is-style-twentytwentyone-separator-thick" style="background-color:#0088a0;color:#0088a0"/>
<!-- /wp:separator -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'header_7' ] = array(
			'title'		=> 'Header inside Coloured Block',
			'category' 	=> 'ngl_headers',
			'content'	=> '<!-- wp:newsletterglue/callout {"bg_color":"#0d566c","font_color":"#FFFFFF","cta_padding2":30} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#0d566c;color:#FFFFFF;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:20px;padding-bottom:20px;padding-left:30px;padding-right:30px;text-align:left;margin-left:0;margin-right:0"><!-- wp:image {"align":"right","width":135,"height":41,"sizeSlug":"large"} -->
<div class="wp-block-image"><figure class="alignright size-large is-resized"><img src="' . NGL_PLUGIN_URL . 'assets/images/email/logo-placeholder.png" alt="" width="135" height="41"/></figure></div>
<!-- /wp:image -->

<!-- wp:heading -->
<h2>Add title</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":5} -->
<h5>Subheading</h5>
<!-- /wp:heading -->

<!-- wp:newsletterglue/metadata {"text_color":"#FFFFFF","alignment":"left","date_format":"l, j M Y","show_author":false,"show_date":false,"show_location":false,"post_id":"409","readingtime":"1 mins","post_date":"Friday, 25 Jun 2021"} -->
<div class="wp-block-newsletterglue-metadata ngl-metadata" style="color:#FFFFFF;text-align:left"><div class="ngl-metadata-issue">Issue #</div><div class="ngl-metadata-sep">|</div><div class="ngl-metadata-readtime">Reading time:</div><div class="ngl-metadata-readtime-ajax">1 mins</div><div class="ngl-metadata-sep">|</div><a class="ngl-metadata-permalink" href="{{ blog_post }}">Read online</a><img class="ngl-metadata-permalink-arrow" src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/img/arrow.png"/></div>
<!-- /wp:newsletterglue/metadata --></section>
<!-- /wp:newsletterglue/callout -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->'
		);

		/********************************/
		$patterns[ 'footer_1' ] = array(
			'title'		=> 'Footer Minimal outside Container',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/callout {"font_color":"#707070","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#f9f9f9;color:#707070;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:13px">Built with <a rel="noreferrer noopener" href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">Newsletter Glue</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":15} -->
<div style="height:15px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13}}} -->
<p class="has-text-align-center" style="font-size:13px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:13px">If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p>
<!-- /wp:paragraph --></section>
<!-- /wp:newsletterglue/callout -->'
		);

		/********************************/
		$patterns[ 'footer_2' ] = array(
			'title'		=> 'Footer Minimal with Social Sharing',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:newsletterglue/callout {"cta_padding":30,"cta_padding2":30} -->
<section class="wp-block-newsletterglue-callout undefined not-color-set" style="background-color:#f9f9f9;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:30px;padding-right:30px;text-align:left;margin-left:0;margin-right:0"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:13px">Built with <a rel="noreferrer noopener" href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">Newsletter Glue</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":12},"color":{"text":"#707070"}}} -->
<p class="has-text-color" style="color:#707070;font-size:12px">Published with ♥ by Lesley.<br>Here’s where you can find me online:</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":10} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:newsletterglue/share {"icon_size":18,"icon_shape":"default","icon_color":"grey","add_description":false} -->
<div class="wp-block-newsletterglue-share undefined wp-block-newsletter-share-left ngl-image-size-18" data-image-size="18"><!-- wp:newsletterglue/share-link {"service":"twitter","url":"https://twitter.com/","icon_size":18,"icon_shape":"default","icon_color":"grey"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-twitter" href="https://twitter.com/" target="_blank" rel="noopener"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/grey/twitter.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link -->

<!-- wp:newsletterglue/share-link {"service":"instagram","url":"https://instagram.com/","icon_size":18,"icon_shape":"default","icon_color":"grey"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-instagram" href="https://instagram.com/" target="_blank" rel="noopener"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/grey/instagram.png" width="18" height="18" style="width:18px;height:18px" class="ngl-inline-image"/></a>
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
			'title'		=> 'Footer inside Coloured Block',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:13px">Built with <a rel="noreferrer noopener" href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">Newsletter Glue</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:newsletterglue/callout {"bg_color":"#0d566c","font_color":"#FFFFFF","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#0d566c;color:#FFFFFF;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:newsletterglue/share {"alignment":"right","icon_size":20,"icon_shape":"default","icon_color":"white","add_description":false} -->
<div class="wp-block-newsletterglue-share undefined wp-block-newsletter-share-right ngl-image-size-20" data-image-size="20"><!-- wp:newsletterglue/share-link {"service":"twitter","url":"https://twitter.com/","icon_size":20,"icon_shape":"default","icon_color":"white"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-twitter" href="https://twitter.com/" target="_blank" rel="noopener"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/white/twitter.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a>
<!-- /wp:newsletterglue/share-link -->

<!-- wp:newsletterglue/share-link {"service":"instagram","url":"https://instagram.com/","icon_size":20,"icon_shape":"default","icon_color":"white"} -->
<a class="wp-block-newsletterglue-share-link ngl-social-link ngl-social-link-instagram" href="https://instagram.com/" target="_blank" rel="noopener"><img src="' . NGL_PLUGIN_URL . 'assets/images/share/default/white/instagram.png" width="20" height="20" style="width:20px;height:20px" class="ngl-inline-image"/></a>
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
			'title'		=> 'Footer inside Coloured Block with logo',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":13},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:13px">Built with <a rel="noreferrer noopener" href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">Newsletter Glue</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:newsletterglue/callout {"bg_color":"#0d566c","font_color":"#FFFFFF","cta_padding":30,"cta_padding2":40} -->
<section class="wp-block-newsletterglue-callout undefined is-color-set" style="background-color:#0d566c;color:#FFFFFF;border-color:#f9f9f9;border-style:none;border-width:0;padding-top:30px;padding-bottom:30px;padding-left:40px;padding-right:40px;text-align:left;margin-left:0;margin-right:0"><!-- wp:image {"align":"left","width":135,"height":41,"sizeSlug":"large","className":"is-style-default"} -->
<div class="wp-block-image is-style-default"><figure class="alignleft size-large is-resized"><img src="' . NGL_PLUGIN_URL . 'assets/images/email/logo-placeholder.png" alt="" width="135" height="41"/></figure></div>
<!-- /wp:image -->

<!-- wp:spacer {"height":30} -->
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
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
<p class="has-text-align-right has-white-color has-text-color" style="font-size:13px"><a href="{{ unsubscribe_link }}">Unsubscribe here</a>.</p>
<!-- /wp:paragraph --></section>
<!-- /wp:newsletterglue/callout -->'
		);

		/********************************/
		$patterns[ 'footer_5' ] = array(
			'title'		=> 'Footer Minimal inside Container',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":14},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:14px">Built with <a rel="noreferrer noopener" href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">Newsletter Glue</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":14},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:14px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":14},"color":{"text":"#707070"}}} -->
<p class="has-text-align-center has-text-color" style="color:#707070;font-size:14px">If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->'
		);

		/********************************/
		$patterns[ 'footer_6' ] = array(
			'title'		=> 'Footer Minimal with Separator',
			'category' 	=> 'ngl_footers',
			'content'	=> '<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":14},"color":{"text":"#707070"}}} -->
<p class="has-text-color" style="color:#707070;font-size:14px">Built with <a rel="noreferrer noopener" href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">Newsletter Glue</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"customColor":"#0088a0","className":"is-short is-style-twentytwentyone-separator-thick"} -->
<hr class="wp-block-separator has-text-color has-background is-short is-style-twentytwentyone-separator-thick" style="background-color:#0088a0;color:#0088a0"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"style":{"color":{"text":"#707070"},"typography":{"fontSize":14}}} -->
<p class="has-text-color" style="color:#707070;font-size:14px">116 New Montgomery Street, Suite 400<br>San Francisco, CA, 94105</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#707070"},"typography":{"fontSize":14}}} -->
<p class="has-text-color" style="color:#707070;font-size:14px">If you’d no longer like to receive emails from me, you can <a href="{{ unsubscribe_link }}">unsubscribe here</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":20} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->'
		);

		return $patterns;
	}

}