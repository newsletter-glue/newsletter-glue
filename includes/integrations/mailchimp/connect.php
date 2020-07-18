<?php
/**
 * Mailchimp
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-header"><?php esc_html_e( 'Mailchimp', 'newsletter-glue' ); ?></div>

<div class="ngl-fields">

	<form action="" method="get">

		<?php
			newsletterglue_text_field( array(
				'id' 			=> 'ngl_mailchimp_key',
				'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'helper'		=> '<a href="https://us3.admin.mailchimp.com/account/api-key-popup/" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . '</a>',
				'value'			=> isset( $integrations['mailchimp']['api_key'] ) ? $integrations['mailchimp']['api_key'] : '',
			) );
		?>

		<div class="ngl-btn">
			<button class="ui primary button" type="submit"><i class="sync alternate icon"></i><?php esc_html_e( 'Connect', 'newsletter-glue' ); ?></button>
		</div>

	</form>

</div>

<div class="ngl-card-link-end">
	<a href="https://docs.memberhero.pro/article/2-connect" target="_blank" class="ui basic noborder button"><?php esc_html_e( 'Learn more', 'newsletter-glue' ); ?></a>
</div>