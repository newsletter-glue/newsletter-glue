<?php
/**
 * ConvertKit
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-header"><?php esc_html_e( 'ConvertKit', 'newsletter-glue' ); ?></div>

<div class="ngl-fields">

	<form action="" method="get">

		<?php
			newsletterglue_text_field( array(
				'id' 			=> 'ngl_convertkit_key',
				'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'value'			=> isset( $integrations['convertkit']['api_key'] ) ? $integrations['convertkit']['api_key'] : '',
				'helper'		=> '<a href="https://app.convertkit.com/account/edit/" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . '</a>',
			) );
		?>

		<div style="height: 14px;"></div>

		<?php
			newsletterglue_text_field( array(
				'id' 			=> 'ngl_convertkit_secret',
				'placeholder' 	=> esc_html__( 'Enter API Secret', 'newsletter-glue' ),
				'value'			=> isset( $integrations['convertkit']['api_secret'] ) ? $integrations['convertkit']['api_secret'] : '',
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