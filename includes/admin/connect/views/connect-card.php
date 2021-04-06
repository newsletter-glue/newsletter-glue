<?php
/**
 * Connect UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$url = NGL_PLUGIN_URL . 'assets/images/iconset/';

?>

<div class="ngl-cards <?php if ( newsletterglue_is_free_version() ) echo 'ngl-cards-free'; ?>">

	<div class="ngl-card">

		<!-- Software selection -->
		<div class="ngl-card-add ngl-card-base">

			<div class="ngl-header"><?php esc_html_e( 'Add new connection', 'newsletter-glue' ); ?></div>
			<?php
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_app',
					'class'			=> 'ngl-app',
					'options'		=> newsletterglue_get_supported_apps(),
					'placeholder' 	=> esc_html__( 'Select email software...', 'newsletter-glue' ),
					'has_icons'		=> true,
				) );
			?>
			<div class="ngl-card-link-end">
				<a href="#" class="ui basic noborder button ngl-request-modal"><i class="bullhorn icon"></i><?php esc_html_e( 'Request new connection', 'newsletter-glue' ); ?></a>
			</div>
		</div>

		<!-- Software forms -->
		<?php foreach( newsletterglue_get_supported_apps() as $app => $value ) : ?>

			<?php if ( apply_filters( 'newsletterglue_allow_connection_edit', true, $app ) ) { ?>
			<div class="ngl-card-add2 ngl-card-<?php echo esc_attr( $app ); ?> <?php if ( ( $app != 'mailchimp' && newsletterglue_is_free_version() ) || ( ! newsletterglue_is_free_version() ) ) echo 'ngl-hidden'; ?>" data-app="<?php echo esc_attr( $app ); ?>">

				<?php if ( ! apply_filters( 'newsletterglue_allow_connection_edit', true, $app ) ) { ?>
				<div class="ngl-card-link-start">
					<div class="ui basic noborder button ngl-back" data-screen="ngl-card-base"><i class="arrow left icon"></i><?php esc_html_e( 'Back', 'newsletter-glue' ); ?></div>
				</div>
				<?php } else { ?>
				<div class="ngl-card-link-start" style="display: none">
					<div class="ui basic noborder button ngl-back" data-screen="ngl-card-base"><i class="arrow left icon"></i><?php esc_html_e( 'Back', 'newsletter-glue' ); ?></div>
				</div>
				<?php } ?>

				<?php include( 'connect-settings.php' ); ?>

			</div>
			<?php } ?>

			<?php if ( ! newsletterglue_is_onboarding_page() ) : ?>
			<div class="ngl-card-view ngl-card-view-<?php echo esc_attr( $app ); ?> <?php if ( newsletterglue_inactive_app( $app ) ) echo 'ngl-hidden'; ?>" data-app="<?php echo esc_attr( $app ); ?>">

				<div class="ngl-card-view-logo" style="background-image: url( <?php echo newsletterglue_get_url( $app ) . '/assets/logo.png'; ?> );"></div>

				<div class="ngl-header"><?php echo esc_html( $value ); ?></div>

				<div class="ngl-btn">
					<?php if ( apply_filters( 'newsletterglue_allow_connection_edit', true, $app ) ) { ?>
					<button class="ui primary button ngl-ajax-test-connection"><i class="sync alternate icon"></i><?php esc_html_e( 'test', 'newsletter-glue' ); ?></button>
					<?php } else { ?>
					<a href="https://newsletterglue.com/pricing/?discount=REPO25" target="_blank" class="ui primary button"><svg xmlns="http://www.w3.org/2000/svg" width="14.676" height="16.14" viewBox="0 0 14.676 16.14"><g transform="translate(-3.75 -2.25)"><path d="M5.964,16.5H16.212a1.464,1.464,0,0,1,1.464,1.464v5.124a1.464,1.464,0,0,1-1.464,1.464H5.964A1.464,1.464,0,0,1,4.5,23.088V17.964A1.464,1.464,0,0,1,5.964,16.5Z" transform="translate(0 -6.912)"></path><path d="M10.5,9.588V6.66a3.66,3.66,0,1,1,7.32,0V9.588" transform="translate(-3.072 0)"></path></g></svg><?php esc_html_e( 'Upgrade to edit &rarr;', 'newsletter-glue' ); ?></a>
					<?php } ?>
				</div>

				<div class="ngl-helper">
					<?php if ( apply_filters( 'newsletterglue_allow_connection_edit', true, $app ) ) { ?>
					<a href="#" class="ngl-ajax-edit-connection"><i class="pencil alternate icon"></i><?php echo __( 'edit', 'newsletter-glue' ); ?></a>
					<?php } ?>
					<a href="#" class="ngl-ajax-remove-connection"><i class="trash alternate icon"></i><?php echo __( 'remove', 'newsletter-glue' ); ?></a>
				</div>

			</div>
			<?php endif; ?>

		<?php endforeach; ?>

		<!-- Testing connection -->
		<div class="ngl-card-state is-testing ngl-hidden">
			<div class="ngl-card-state-wrap">
				<div class="ngl-card-state-icon"><i class="sync alternate icon"></i></div>
				<div class="ngl-card-state-text"><?php esc_html_e( 'Testing connection...', 'newsletter-glue' ); ?></div>
			</div>
			<div class="ngl-card-state-alt ngl-helper">
				<a href="#" class="ngl-ajax-stop-test"><?php echo __( 'Stop test', 'newsletter-glue' ); ?></a>
			</div>
		</div>

		<!-- Connection working -->
		<div class="ngl-card-state is-working ngl-hidden">
			<div class="ngl-card-state-wrap">
				<div class="ngl-card-state-icon"><i class="check circle icon"></i></div>
				<div class="ngl-card-state-text"><?php esc_html_e( 'Connected!', 'newsletter-glue' ); ?></div>
			</div>
		</div>

		<!-- Connection not working -->
		<div class="ngl-card-state is-invalid ngl-hidden">
			<div class="ngl-card-link-start is-right">
				<a href="#" class="ui basic noborder button ngl-ajax-test-close"><i class="times circle outline icon"></i><?php esc_html_e( 'Close', 'newsletter-glue' ); ?></a>
			</div>
			<div class="ngl-card-state-wrap">
				<div class="ngl-card-state-icon"><i class="material-icons">error_outline</i></div>
				<div class="ngl-card-state-text"><?php esc_html_e( 'Not connected', 'newsletter-glue' ); ?></div>
			</div>
			<div class="ngl-card-state-alt ngl-helper">
				<a href="#" class="ngl-ajax-test-again"><?php echo __( 'Test again', 'newsletter-glue' ); ?></a>
				<a href="#" class="ngl-ajax-edit-connection"><?php echo __( 'Edit connection details', 'newsletter-glue' ); ?></a>
			</div>
			<div class="ngl-card-link-end">
				<a href="https://docs.newsletterglue.com/article/2-connect" class="ui basic noborder button" target="_blank"><i class="question circle outline icon"></i><?php esc_html_e( 'Get help', 'newsletter-glue' ); ?></a>
			</div>
		</div>

		<!-- Connection removed -->
		<div class="ngl-card-state is-removed ngl-hidden">
			<div class="ngl-card-state-wrap">
				<div class="ngl-card-state-icon"><i class="material-icons">delete_forever</i></div>
				<div class="ngl-card-state-text"><?php esc_html_e( 'Connection removed', 'newsletter-glue' ); ?></div>
			</div>
		</div>

		<!-- Remove connection -->
		<div class="ngl-card-state confirm-remove ngl-hidden">
			<div class="ngl-card-state-wrap">
				<div class="ngl-card-state-icon"><i class="trash alternate icon"></i></div>
				<div class="ngl-card-state-text"><?php esc_html_e( 'Remove connection?', 'newsletter-glue' ); ?></div>
			</div>
			<div class="ngl-card-state-alt ngl-helper">
				<a href="#" class="ngl-ajax-remove ngl-helper-alert"><?php echo __( 'Confirm', 'newsletter-glue' ); ?></a>
				<a href="#" class="ngl-back"><?php echo __( 'Go back', 'newsletter-glue' ); ?></a>
			</div>
		</div>

	</div>

	<?php if ( ! newsletterglue_is_onboarding_page() ) : ?>
	<div class="ngl-card-upgrade <?php if ( newsletterglue_is_free_version() ) echo 'ngl-is-free'; ?>" style="display: <?php if ( newsletterglue_is_free_version() && ( newsletterglue_default_connection() == 'mailchimp' || ! newsletterglue_default_connection() ) ) { echo 'block'; } else { echo 'none'; } ?>">
		<h3>Want more integrations?</h3>
		<div class="ngl-upgrade-lists">
			<div class="ngl-upgrade-list">
				<div class="ngl-upgrade-item"><span style="background:#356AE6;"><img src="<?php echo $url; ?>activecampaign.png" alt="" style="width: 14px;height: 21px;" /></span>ActiveCampaign</div>
				<div class="ngl-upgrade-item"><span style="background:#7856FF;"><img src="<?php echo $url; ?>campaignmonitor.png" alt="" style="width: 21px;height: 14px;" /></span>Campaign Monitor</div>
				<div class="ngl-upgrade-item"><span style="background:#00A1ED;"><img src="<?php echo $url; ?>getresponse.png" alt="" style="width: 22px;height: 14px;" /></span>GetResponse</div>
			</div>
			<div class="ngl-upgrade-list">
				<div class="ngl-upgrade-item"><span style="background:#21C16C;"><img src="<?php echo $url; ?>mailerlite.png" alt="" style="width: 20px;height: 16px;" /></span>MailerLite</div>
				<div class="ngl-upgrade-item"><span style="background:#0092FF;"><img src="<?php echo $url; ?>sendinblue.png" alt="" style="width: 18px;height: 21px;" /></span>Sendinblue</div>
				<div class="ngl-upgrade-item"><span style="background: transparent;"><img src="<?php echo $url; ?>sendy.png" alt="" /></span>Sendy</div>
			</div>
		</div>
		<p>Upgrade to Newsletter Glue Pro and use these integrations today.</p>
		<div class="ngl-upgrade-cta"><a href="#">Learn more <i class="arrow right icon"></i></a></div>
	</div>
	<?php endif; ?>

</div>