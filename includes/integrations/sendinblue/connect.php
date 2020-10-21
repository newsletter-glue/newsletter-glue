<?php
/**
 * Sendinblue.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-header"><?php esc_html_e( 'Sendinblue', 'newsletter-glue' ); ?></div>

<div class="ngl-fields">

	<form action="" method="get">

		<?php
			newsletterglue_text_field( array(
				'id' 			=> 'ngl_sendinblue_key',
				'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
				'value'			=> isset( $integrations['sendinblue']['api_key'] ) ? $integrations['sendinblue']['api_key'] : '',
				'helper'		=> '<a href="https://account.sendinblue.com/advanced/api" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . '</a>',
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