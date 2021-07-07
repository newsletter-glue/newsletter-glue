<?php
/**
 * Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Merge Tags.
 */
function newsletterglue_get_sendy_tags() {

	$merge_tags = array(
		'personalization'	=> array(
			'title'		=> __( 'Personalization', 'newsletter-glue' ),
			'tags'	=> array(
				'name'	=> array(
					'title'		=> __( 'Name', 'newsletter-glue' ),
				),
				'email' => array(
					'title'		=> __( 'Email address', 'newsletter-glue' ),
					'uneditable' => true,
				),
			),
		),
		'read_online'		=> array(
			'title'			=> __( 'Read online', 'newsletter-glue' ),
			'tags'			=> array(
				'blog_post' => array(
					'title'		=> __( 'Blog post', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Read online', 'newsletter-glue' ),
				),
				'webversion' => array(
					'title'		=> __( 'Email HTML', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Read online', 'newsletter-glue' ),
				),
			),
		),
		'footer'			=> array(
			'title'			=> __( 'Footer', 'newsletter-glue' ),
			'tags'			=> array(
				'admin_address' => array(
					'title'	=> __( 'Admin address', 'newsletter-glue' ),
					'require_fallback' => 'yes',
				),
				'unsubscribe_link' => array(
					'title'	=> __( 'Unsubscribe link', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Unsubscribe', 'newsletter-glue' ),
					'helper' => __( 'Your subscribers click this text to unsubscribe.', 'newsletter-glue' ),
				),
			),
		),
	);

	return apply_filters( 'newsletterglue_get_sendy_tags', $merge_tags );
}