<?php
/**
 * Connect UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-cards">

	<div class="ngl-card">

		<!-- First screen -->
		<div class="ngl-card-add">
			<div class="ngl-big-action">
				<span class="ngl-icon material-icons">add_circle_outline</span>
				<span class="ngl-header"><?php esc_html_e( 'Add new connection', 'newsletter-glue' ); ?></span>
			</div>
		</div>

		<!-- Software selection -->
		<div class="ngl-card-add2 ngl-card-base ngl-hidden">
			<div class="ngl-card-link-start">
				<div class="ui basic noborder button ngl-back" data-screen="ngl-card-add"><i class="arrow left icon"></i><?php esc_html_e( 'Back', 'newsletter-glue' ); ?></div>
			</div>
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
				<div class="ui basic noborder button ngl-request-modal"><i class="bullhorn icon"></i><?php esc_html_e( 'Request new connection', 'newsletter-glue' ); ?></div>
			</div>
		</div>

		<!-- Software forms -->
		<?php foreach( newsletterglue_get_supported_apps() as $app => $value ) : ?>

			<div class="ngl-card-add2 ngl-card-<?php echo esc_attr( $app ); ?> ngl-hidden" data-app="<?php echo esc_attr( $app ); ?>">

				<div class="ngl-card-link-start">
					<div class="ui basic noborder button ngl-back" data-screen="ngl-card-base"><i class="arrow left icon"></i><?php esc_html_e( 'Back', 'newsletter-glue' ); ?></div>
				</div>

				<?php include_once newsletterglue_get_path( $app ) . '/connect.php'; ?>

			</div>

			<?php if ( ! newsletterglue_is_onboarding_page() ) : ?>
			<div class="ngl-card-view ngl-card-view-<?php echo esc_attr( $app ); ?> <?php if ( newsletterglue_inactive_app( $app ) ) echo 'ngl-hidden'; ?>" data-app="<?php echo esc_attr( $app ); ?>">

				<div class="ngl-card-view-logo" style="background-image: url( <?php echo newsletterglue_get_url( $app ) . '/assets/logo.png'; ?> );"></div>

				<div class="ngl-header"><?php echo esc_html( $value ); ?></div>

				<div class="ngl-btn">
					<button class="ui primary button ngl-ajax-test-connection"><i class="sync alternate icon"></i><?php esc_html_e( 'test', 'newsletter-glue' ); ?></button>
				</div>

				<div class="ngl-helper">
					<a href="#" class="ngl-ajax-edit-connection"><i class="pencil alternate icon"></i><?php echo __( 'edit', 'newsletter-glue' ); ?></a>
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
				<a href="https://wordpress.org/support/plugin/newsletter-glue/" class="ui basic noborder button" target="_blank"><i class="question circle outline icon"></i><?php esc_html_e( 'Get help', 'newsletter-glue' ); ?></a>
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

</div>