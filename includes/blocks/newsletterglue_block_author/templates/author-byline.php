<?php
/**
 * Author byline.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="ngl-author">

	<div class="ngl-author-pic">
		<?php
			if ( $profile_pic ) :
				echo '<img alt="" src="' . esc_url( $profile_pic ) . '" class="avatar avatar-80 photo">';
			else :
				echo get_avatar( $user_id, 80 );
			endif;
		?>
	</div>

	<div class="ngl-author-meta">

		<div class="ngl-author-name">

			<span class="ngl-author-name-1"><?php echo esc_html( $name ); ?></span>

			<?php if ( $social_url ) : ?>
			<span class="ngl-author-cta">
				<a href="<?php echo esc_url( $social_url ); ?>" class="ngl-author-<?php echo esc_attr( $social ); ?>" target="_blank"><?php echo $social_icon; ?> <?php _e( 'Follow', 'newsletter-glue' ); ?></a>
			</span>
			<?php endif; ?>

		</div>

		<div class="ngl-author-bio">
			<?php echo esc_html( $bio ); ?>
		</div>

	</div>

</div>