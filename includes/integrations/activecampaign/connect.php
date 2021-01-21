<?php
/**
 * ActiveCampaign
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-header"><?php esc_html_e( 'ActiveCampaign', 'newsletter-glue' ); ?></div>

<div class="ngl-fields">

	<form action="" method="get">

		<?php
			newsletterglue_text_field( array(
				'id' 			=> 'ngl_activecampaign_url',
				'placeholder' 	=> esc_html__( 'Enter API URL', 'newsletter-glue' ),
				'value'			=> isset( $integrations['activecampaign']['api_url'] ) ? $integrations['activecampaign']['api_url'] : '',
				'class'			=> 'ngl-text-margin',
			) );

			newsletterglue_text_field( array(
				'id' 			=> 'ngl_activecampaign_key',
				'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'helper'		=> '<a href="https://www.activecampaign.com/login" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . '</a>',
				'value'			=> isset( $integrations['activecampaign']['api_key'] ) ? $integrations['activecampaign']['api_key'] : '',
			) );
		?>

		<div class="ngl-btn">
			<button class="ui primary button" type="submit"><i class="sync alternate icon"></i><?php esc_html_e( 'Connect', 'newsletter-glue' ); ?></button>
		</div>

	</form>

</div>

<div class="ngl-card-link-end">
	<a href="https://docs.newsletterglue.com/article/2-connect" target="_blank" class="ui basic noborder button"><?php esc_html_e( 'Learn more', 'newsletter-glue' ); ?></a>
</div>