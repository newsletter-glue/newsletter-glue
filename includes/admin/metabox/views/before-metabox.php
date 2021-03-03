<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-msgbox-wrap ngl-metabox-flex alt3 alt5 is-hidden">

	<div class="ngl-msg-contain">
		<div class="ngl-top-msg">
			<svg xmlns="http://www.w3.org/2000/svg" width="84.915" height="30.634" viewBox="0 0 84.915 30.634"><defs><style>.aa{fill:#fff;}.aa,.bb,.cc{stroke-linecap:round;}.bb,.cc{fill:none;stroke:#17a4c6;}.cc{stroke-width:1.5px;}.dd,.ee{stroke:none;}.ee{fill:#17a4c6;}</style></defs><g transform="translate(-1853.555 -917)"><g class="aa" transform="translate(1883.125 917)"><path class="dd" d="M 44.14653396606445 30.13400459289551 L 1.79953408241272 30.13400459289551 C 1.416154026985168 30.13400459289551 0.9730140566825867 29.90056419372559 0.6968440413475037 29.55311393737793 C 0.5326249599456787 29.34651947021484 0.452938586473465 29.12885856628418 0.4805371463298798 28.96538734436035 L 9.875864028930664 1.981033325195312 C 10.29516506195068 0.9845572710037231 10.83353710174561 0.5000042319297791 11.52135372161865 0.5000042319297791 L 54.34173583984375 0.5000042319297791 C 54.4747428894043 0.5000042319297791 54.70968246459961 0.51701420545578 54.78880310058594 0.6309542059898376 C 54.87077331542969 0.7490141987800598 54.88469314575195 1.072754144668579 54.67790222167969 1.627304196357727 L 54.67537307739258 1.634084224700928 L 54.67304229736328 1.640934228897095 L 45.47742462158203 28.66435432434082 C 45.07284927368164 29.74922943115234 44.72415161132812 30.13400459289551 44.14653396606445 30.13400459289551 Z"/><path class="ee" d="M 11.52135467529297 1.000003814697266 C 11.35103225708008 1.000003814697266 10.83535766601562 1.000059127807617 10.34272766113281 2.160739898681641 L 0.9790191650390625 29.05428695678711 C 1.023708343505859 29.23724555969238 1.408954620361328 29.63400459289551 1.799522399902344 29.63400459289551 L 44.14653396606445 29.63400459289551 C 44.37565612792969 29.63400459289551 44.61389923095703 29.54671669006348 45.00654602050781 28.49604606628418 L 54.1996955871582 1.479864120483398 L 54.20435333251953 1.466163635253906 L 54.20941543579102 1.452604293823242 C 54.28692626953125 1.24473762512207 54.32093811035156 1.09759521484375 54.33520889282227 1.000003814697266 L 11.52135467529297 1.000003814697266 M 11.52135467529297 3.814697265625e-06 L 54.34173583984375 3.814697265625e-06 C 55.33694458007812 3.814697265625e-06 55.56301498413086 0.6847438812255859 55.14638519287109 1.802003860473633 L 45.94853210449219 28.83200454711914 C 45.58733367919922 29.80260467529297 45.14174270629883 30.63400459289551 44.14653396606445 30.63400459289551 L 1.799522399902344 30.63400459289551 C 0.8043136596679688 30.63400459289551 -0.1876869201660156 29.6317138671875 -0.00246429443359375 28.83200454711914 L 9.40875244140625 1.802003860473633 C 9.856613159179688 0.7298240661621094 10.5261344909668 3.814697265625e-06 11.52135467529297 3.814697265625e-06 Z"/></g><line class="bb" x1="17.119" y1="12.614" transform="translate(1893.484 918.352)"/><line class="bb" x1="27.03" y2="12.614" transform="translate(1910.604 918.352)"/><g transform="translate(1841.805 919.885)"><line class="cc" x2="32" transform="translate(12.5)"/><line class="cc" x2="24" transform="translate(17.5 6.925)"/><line class="cc" x2="12" transform="translate(26.5 15.003)"/><line class="cc" x2="4" transform="translate(32.5 21.928)"/></g></g></svg>
			<span class="ngl-newsletter-sent ngl-muted"><?php _e( 'Your email newsletter is on its way!', 'newsletter-glue' ); ?></span>
		</div>

		<div class="ngl-top-msg-view">
			<a href="#ngl-status-log" data-post-id="<?php echo absint( $post->ID ); ?>"><?php _e( 'View status log', 'newsletter-glue' ); ?></a>
			<a href="#" class="ngl-reset-newsletter" data-post-id="<?php echo absint( $post->ID ); ?>"><?php echo __( 'Send another newsletter', 'newsletter-glue' ); ?></a>
		</div>
	</div>

	<div class="ngl-top-msg-right">
		<?php do_action( 'newsletterglue_common_action_hook' ); ?>
		<?php echo newsletterglue_get_review_button_html( 'post' ); ?>
		<a href="https://docs.newsletterglue.com/article/11-email-delivery" target="_blank" class="ngl-get-help"><i class="question circle outline icon"></i><?php echo __( 'Get help', 'newsletter-glue' ); ?></a>
	</div>

</div>

<div class="ngl-metabox ngl-metabox-flex alt3 alt5 ngl-reset <?php if ( $hide ) echo 'is-hidden'; ?>">

	<div class="ngl-msg-contain">
		<div class="ngl-top-msg">
			<svg xmlns="http://www.w3.org/2000/svg" width="64.006" height="44.253" viewBox="0 0 64.006 44.253"><defs><style>.a,.c{fill:#fff;}.a,.b,.c{stroke:#707070;}.a,.b{stroke-linecap:round;}.b,.f{fill:none;}.d{fill:#0088a0;}.e{stroke:none;}</style></defs><g transform="translate(-1931 -424)"><g class="a" transform="translate(1931 424)"><rect class="e" width="51" height="34" rx="2"/><rect class="f" x="0.5" y="0.5" width="50" height="33" rx="1.5"/></g><line class="b" x1="22.672" y1="19.184" transform="translate(1933.616 426.616)"/><line class="b" y1="19.184" x2="22.672" transform="translate(1956.289 426.616)"/><g transform="translate(1968.15 441.398)"><g class="c" transform="translate(0.85 0.602)"><circle class="e" cx="12.5" cy="12.5" r="12.5"/><circle class="f" cx="12.5" cy="12.5" r="12"/></g><path class="d" d="M27.418,13.99A13.428,13.428,0,1,1,13.99.563,13.428,13.428,0,0,1,27.418,13.99ZM12.437,21.1,22.4,11.138a.866.866,0,0,0,0-1.225L21.174,8.687a.866.866,0,0,0-1.225,0l-8.125,8.125L8.031,13.019a.866.866,0,0,0-1.225,0L5.581,14.244a.866.866,0,0,0,0,1.225L11.212,21.1a.866.866,0,0,0,1.225,0Z" transform="translate(-0.563 -0.563)"/></g></g></svg>
			<?php
				if ( newsletterglue_is_post_scheduled( $post->ID ) ) {
					echo __( 'Your email newsletter is scheduled.', 'newsletter-glue' );
				} else {
					echo __( 'You’ve sent this post as a newsletter before.', 'newsletter-glue' );
				}
			?>
		</div>

		<div class="ngl-top-msg-view">
			<a href="#ngl-status-log" data-post-id="<?php echo absint( $post->ID ); ?>"><?php _e( 'View status log', 'newsletter-glue' ); ?></a>
			<?php
				if ( newsletterglue_is_post_scheduled( $post->ID ) ) {
					echo '<a href="#" class="ngl-reset-newsletter" data-post-id="' . absint( $post->ID ) . '">' . __( 'Unschedule', 'newsletter-glue' ) . '</a>';
				} else {
					echo '<a href="#" class="ngl-reset-newsletter" data-post-id="' . absint( $post->ID ) . '">' . __( 'Send another newsletter', 'newsletter-glue' ) . '</a>';
				}
			?>
		</div>
	</div>

	<div class="ngl-top-msg-right">
		<?php do_action( 'newsletterglue_common_action_hook' ); ?>
		<?php echo newsletterglue_get_review_button_html( 'post' ); ?>
		<a href="https://docs.newsletterglue.com/article/11-email-delivery" target="_blank" class="ngl-get-help"><i class="question circle outline icon"></i><?php echo __( 'Get help', 'newsletter-glue' ); ?></a>
	</div>

</div>

<div class="ngl-metabox ngl-send <?php if ( ! $hide ) echo 'is-hidden'; ?>">

	<input type="hidden" name="ngl_app" id="ngl_app" value="<?php echo esc_attr( $app ); ?>" />

	<div class="ngl-metabox-if-checked">

	<?php $api->show_subject( $settings, $defaults, $post ); ?>