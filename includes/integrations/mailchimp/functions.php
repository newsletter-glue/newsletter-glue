<?php
/**
 * Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Merge Tags.
 */
function newsletterglue_get_mailchimp_tags() {

	$merge_tags = array(
		'personalization'	=> array(
			'title'		=> __( 'Personalization', 'newsletter-glue' ),
			'tags'	=> array(
				'first_name'	=> array(
					'title'		=> __( 'First name', 'newsletter-glue' ),
				),
				'last_name' 	=> array(
					'title'		=> __( 'Last name', 'newsletter-glue' ),
				),
				'email' => array(
					'title'		=> __( 'Email address', 'newsletter-glue' ),
				),
				'address'		=> array(
					'title'		=> __( 'Subscriber address', 'newsletter-glue' ),
				),
				'list' 			=> array(
					'title'		=> __( 'List', 'newsletter-glue' ),
				),
				'update_preferences' => array(
					'title'		=> __( 'Update preferences', 'newsletter-glue' ),
					'default_link_text' => __( 'Update preferences', 'newsletter-glue' ),
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
				),
				'unsubscribe_link' => array(
					'title'	=> __( 'Unsubscribe link', 'newsletter-glue' ),
					'default_link_text'	=> __( 'Unsubscribe', 'newsletter-glue' ),
					'helper' => __( 'Your subscribers click this text to unsubscribe.', 'newsletter-glue' ),
				),
				'rewards' => array(
					'title'			=> __( 'Rewards', 'newsletter-glue' ),
					'uneditable' 	=> true,
					'helper' 		=> __( 'If you are on a <strong>free</strong> Mailchimp account, you must add this to your footer.', 'newsletter-glue' ),
				)
			),
		),
	);

	return apply_filters( 'newsletterglue_get_mailchimp_tags', $merge_tags );
}