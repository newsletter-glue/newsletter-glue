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
				<div class="ngl-article-status"></div>
				<form class="ngl-article-add">
					<div class="ngl-article-box">
						<input type="text" class="components-placeholder__input ngl_article_s" data-post="" placeholder="<?php _e( 'Search for a post or enter URL here…', 'newsletter-glue' ); ?>" value="">
						<ul class="ngl-article-suggest">

						</ul>
					</div>
					<button type="submit" class="components-button is-primary"><?php _e( 'Add', 'newsletter-glue' ); ?></button>
				</form>
			</div>
			<div class="ngl-article-list">
				<a href="#" class="ngl-article-list-head"><?php _e( 'Reorder, change or remove posts', 'newsletter-glue' ); ?> <span class="material-icons">expand_more</span></a>
				<div class="ngl-article-list-wrap">

					<?php if ( empty( $articles ) ) : ?>
					<div class="ngl-article-list-empty"><?php _e( 'There&rsquo;s nothing here yet. Add your first post above.', 'newsletter-glue' ); ?></div>
					<?php endif; ?>

					<?php
						if ( $articles ) :
							krsort( $articles );
							foreach( $articles as $key => $article ) :

							if ( ! empty( $article[ 'is_remote' ] ) ) {
								$thearticle = $this->get_remote_url( $article[ 'post_id' ] );
							} else {
								$thearticle = get_post( $article[ 'post_id' ] );
								if ( empty( $thearticle->ID ) ) {
									unset( $articles[ $key ] );
									continue;
								}
							}
					?>
					<div class="ngl-article-list-item" data-key="<?php echo $key; ?>" data-post-id="<?php echo $article[ 'post_id' ]; ?>">
						<div class="ngl-article-list-icon"><img src="<?php echo esc_url( $this->get_favicon( $thearticle ) ); ?>" /></div>
						<div class="ngl-article-list-info">
							<div class="ngl-article-list-title"><?php echo $this->display_title( $thearticle->ID, $thearticle ); ?></div>
							<div class="ngl-article-list-url"><?php echo $this->get_permalink( $thearticle ); ?></div>
							<div class="ngl-article-list-action">
								<a href="#" class="ngl-article-list-red"><i class="trash alternate outline icon"></i><?php _e( 'Remove post', 'newsletter-glue' ); ?></a>
								<?php if ( ! empty( $thearticle->is_remote ) ) : ?><a href="#" class="ngl-article-list-refresh"><i class="sync icon"></i><?php _e( 'Refresh', 'newsletter-glue' ); ?></a><?php endif; ?>
							</div>
						</div>
						<div class="ngl-article-list-move">
							<div class="ngl-article-list-move-up"><a href="#"><span class="material-icons">expand_less</span></a></div>
							<div class="ngl-article-list-move-down"><a href="#"><span class="material-icons">expand_more</span></a></div>
						</div>
					</div>
					<?php
							endforeach;
						endif;
					?>

				</div>
			</div>
		</div>
	</div>

	<?php
		$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="{permalink}"><img src="{featured_image}" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a></div>' : '';
		$display_tags   	= ( $show_tags ) ? '{tags}' : '';
		$display_title  	= '<div class="ngl-article-title"><a href="{permalink}" style="font-size: ' . $font_size_title . 'px;' . $link_color . '"><span ' . $editable . '>{title}</span></a></div>';
		$display_excerpt 	= '<div class="ngl-article-excerpt" ' . $editable . '>{excerpt}</div>';
		$display_date       = ( $show_date ) ? '<div class="ngl-article-date">{date}</div>' : '';
	?>

	<div class="ngl-article ngl-article-img-<?php echo $image_position; ?> ngl-article-placeholder" data-key="{key}" data-post-id="{post_id}" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>; font-size: <?php echo $font_size_text; ?>px;">

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
	<div class="ngl-articles-wrap">
	<?php
	if ( ! empty( $articles ) ) :

			krsort( $articles );

			foreach( $articles as $key => $article ) :

				// Internal post.
				if ( ! empty( $article[ 'post_id' ] ) ) :

					if ( ! empty( $article[ 'is_remote' ] ) ) {
						$thearticle = $this->get_remote_url( $article[ 'post_id' ] );
					} else {
						$thearticle = get_post( $article[ 'post_id' ] );
						if ( empty( $thearticle->ID ) ) {
							unset( $articles[ $key ] );
							continue;
						}
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

					if ( ! empty( $thearticle->is_remote ) ) {
						$display_image  	= ( $show_image && ! empty( $thearticle->image_url ) ) ? '<div class="ngl-article-featured"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . $thearticle->image_url . '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a></div>' : '';
					} else {
						$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . $this->get_featured( $thearticle ) . '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a></div>' : '';
					}

					$thecontent 		= apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );
					$display_title 		= '<div class="ngl-article-title"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '" style="font-size: ' . $font_size_title . 'px;' . $link_color . '">';
					$display_title     .= '<span ' . $editable . '>' . $this->display_title( $thearticle->ID, $thearticle ) . '</span></a></div>';
					$display_excerpt 	= '<div class="ngl-article-excerpt" ' . $editable . '>' . $this->display_excerpt( $thearticle->ID, $thecontent ) . '</div>';
					$display_date    	= ( $show_date && ! empty( $thearticle->post_date ) ) ? '<div class="ngl-article-date">' . date_i18n( $date_format, strtotime( $thearticle->post_date ) ) . '</div>' : '';

				else :

				endif;

				if ( ! $show_image ) {
					$table_ratio = 'full';
				}
		?>

			<div class="ngl-article ngl-article-img-<?php echo $image_position; ?>" data-key="<?php echo $key; ?>" data-post-id="<?php echo $thearticle->ID; ?>" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>; font-size: <?php echo $font_size_text; ?>px;">

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

</div>