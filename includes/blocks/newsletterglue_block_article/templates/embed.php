<?php
/**
 * Post embeds.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$editable = false;

?>

<div class="ngl-articles ngl-articles-<?php echo $table_ratio; ?> <?php echo ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ? 'ngl-articles-admin' : 'ngl-articles-frontend'; ?>" data-date_format="<?php echo esc_attr( $date_format ); ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">

	<?php if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) : ?>
	<?php if ( ! defined( 'NGL_IN_EMAIL' ) ) : ?>
	<?php $editable = 'contenteditable="true"'; ?>
	<div class="components-placeholder wp-block-embed is-large">
		<div class="ngl-articles-add">
			<div class="components-placeholder__label">
				<span class="block-editor-block-icon has-colors" style="color: rgb(29, 161, 242);"><svg width="24" height="24" viewBox="0 0 92.308 75" role="img" aria-hidden="true" focusable="false"><path fill="#DD3714" d="M14.423,61.067H2.885A2.885,2.885,0,0,0,0,63.952V75.49a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V63.952A2.885,2.885,0,0,0,14.423,61.067Zm0-57.692H2.885A2.885,2.885,0,0,0,0,6.26V17.8a2.885,2.885,0,0,0,2.885,2.885H14.423A2.885,2.885,0,0,0,17.308,17.8V6.26A2.885,2.885,0,0,0,14.423,3.375Zm0,28.846H2.885A2.885,2.885,0,0,0,0,35.106V46.644a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V35.106A2.885,2.885,0,0,0,14.423,32.221Zm75,31.731H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V66.837A2.885,2.885,0,0,0,89.423,63.952Zm0-57.692H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769A2.885,2.885,0,0,0,31.731,17.8H89.423a2.885,2.885,0,0,0,2.885-2.885V9.144A2.885,2.885,0,0,0,89.423,6.26Zm0,28.846H31.731a2.885,2.885,0,0,0-2.885,2.885V43.76a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V37.99A2.885,2.885,0,0,0,89.423,35.106Z" transform="translate(0 -3.375)"></path></svg></span><?php _e( 'Post embed', 'newsletter-glue' ); ?>
			</div>
			<div class="components-placeholder__fieldset">
				<form class="ngl-article-add">
					<div class="ngl-article-box">
						<input type="text" class="components-placeholder__input ngl_article_s" data-post="" placeholder="<?php _e( 'Search for a post or enter URL here…', 'newsletter-glue' ); ?>" value="">
						<ul class="ngl-article-suggest">

						</ul>
					</div>
					<button type="submit" class="components-button is-primary"><?php _e( 'Add', 'newsletter-glue' ); ?></button>
				</form>
			</div>
		</div>
	</div>

	<?php
		if ( $border_size ) {
			$padding = '20px';
		} else {
			$padding = '0px';
		}

		if ( ! $border_size ) {
			if ( $background_color != 'transparent' ) {
				$padding = '20px';
			} else {
				$border_radius = 0;
			}
		}

		$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="{permalink}"><img src="{featured_image}" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a></div>' : '';
		$display_tags   	= ( $show_tags ) ? '{tags}' : '';
		$display_title  	= '<div class="ngl-article-title"><a href="{permalink}" style="' . $link_color . '"><span ' . $editable . '>{title}</span></a></div>';
		$display_excerpt 	= '<div class="ngl-article-excerpt" ' . $editable . '>{excerpt}</div>';
		$display_date       = ( $show_date ) ? '<div class="ngl-article-date">{date}</div>' : '';
	?>

	<div class="ngl-article ngl-article-img-<?php echo $image_position; ?> ngl-article-placeholder" data-post-id="{post_id}" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>;">

				<?php
					if ( $table_ratio == 'full' ) :
						echo $display_image;
						echo $display_tags;
						echo $display_title;
						echo $display_excerpt;
						echo $display_date;
					else :
						if ( $image_position == 'left' ) :
							echo '<div class="ngl-article-left">' . $display_image . '</div>';
							echo '<div class="ngl-article-right">';
								echo $display_tags;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
						endif;
						if ( $image_position == 'right' ) :
							echo '<div class="ngl-article-left">';
								echo $display_tags;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
							echo '<div class="ngl-article-right">' . $display_image . '</div>';
						endif;
					endif;
				?>

	</div>
	<?php endif; ?>
	<?php endif; ?>

	<!-- Begin articles display -->
	<?php
	if ( $articles ) :

			foreach( $articles as $key => $article ) :

				// Internal post.
				if ( ! empty( $article[ 'post_id' ] ) ) :

					$thearticle 		= get_post( $article[ 'post_id' ] );
					if ( empty( $thearticle->ID ) ) {
						unset( $articles[ $key ] );
						continue;
					}

					$tags 				= wp_get_post_tags( $thearticle->ID );
					$display_tags		= '';
					if ( $tags && $show_tags ) {
						$display_tags 	= '<div class="ngl-article-tags">';
						foreach( $tags as $tag ) {
							$display_tags .= '<div class="ngl-article-tag">' . $tag->name . '</div>';
						}
						$display_tags .= '</div>';
					}
					$display_image  	= ( has_post_thumbnail( $thearticle->ID ) && $show_image ) ? '<div class="ngl-article-featured"><a href="' . get_permalink( $thearticle->ID ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . wp_get_attachment_url( get_post_thumbnail_id( $thearticle->ID ), 'full' ) . '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a></div>' : '';
					$thecontent 		= apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );
					$display_title 		= '<div class="ngl-article-title"><a href="' . get_permalink( $thearticle->ID ) . '" target="' . $new_window . '" rel="' . $nofollow . '" style="' . $link_color . '">';
					$display_title     .= '<span ' . $editable . '>' . $this->display_title( $thearticle->ID, $thearticle ) . '</span></a></div>';
					$display_excerpt 	= '<div class="ngl-article-excerpt" ' . $editable . '>' . $this->display_excerpt( $thearticle->ID, $thecontent ) . '</div>';
					$display_date    	= ( $show_date ) ? '<div class="ngl-article-date">' . date_i18n( $date_format, strtotime( $thearticle->post_date ) ) . '</div>' : '';

				else :

				endif;

				if ( ! $show_image ) {
					$table_ratio = 'full';
				}

				if ( $border_size ) {
					$padding = '20px';
				} else {
					$padding = '0px';
				}

				if ( ! $border_size ) {
					if ( $background_color != 'transparent' ) {
						$padding = '20px';
					} else {
						$border_radius = 0;
					}
				}
		?>

			<div class="ngl-article ngl-article-img-<?php echo $image_position; ?>" data-key="<?php echo $key; ?>" data-post-id="<?php echo $thearticle->ID; ?>" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>;">

				<?php
					if ( $table_ratio == 'full' ) :
						echo $display_image;
						echo $display_tags;
						echo $display_title;
						echo $display_excerpt;
						echo $display_date;
					else :
						if ( $image_position == 'left' ) :
							echo '<div class="ngl-article-left">' . $display_image . '</div>';
							echo '<div class="ngl-article-right">';
								echo $display_tags;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
						endif;
						if ( $image_position == 'right' ) :
							echo '<div class="ngl-article-left-mobile -emogrifier-keep">' . $display_image . '</div>';
							echo '<div class="ngl-article-left">';
								echo $display_tags;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
							echo '<div class="ngl-article-right">' . $display_image . '</div>';
						endif;
					endif;
				?>

			</div>

		<?php endforeach; ?>

		<?php update_option( 'ngl_articles_' . $block_id, $articles ); ?>

	<?php endif; ?>

</div>