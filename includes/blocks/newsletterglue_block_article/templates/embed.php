<?php
/**
 * Post embeds.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$editable = false;
$show_edit_controls = false;

?>

<div class="ngl-articles ngl-articles-<?php echo $table_ratio; ?> <?php echo ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ? 'ngl-articles-admin' : 'ngl-articles-frontend'; ?>" data-date_format="<?php echo esc_attr( $date_format ); ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">

	<?php if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) : ?>
	<?php if ( ! defined( 'NGL_IN_EMAIL' ) ) : ?>
	<?php
		$editable = 'contenteditable="true"';
		$show_edit_controls = '<span class="ngl-article-featured-edit"><i class="image outline icon"></i><i class="trash alternate outline icon"></i></span>';
	?>
	<div class="components-placeholder wp-block-embed is-large">
		<div class="ngl-articles-add">
			<div class="components-placeholder__label">
				<span class="block-editor-block-icon has-colors" style="color: rgb(29, 161, 242);"><svg width="24" height="24" viewBox="0 0 92.308 75" role="img" aria-hidden="true" focusable="false"><path fill="#0088A0" d="M14.423,61.067H2.885A2.885,2.885,0,0,0,0,63.952V75.49a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V63.952A2.885,2.885,0,0,0,14.423,61.067Zm0-57.692H2.885A2.885,2.885,0,0,0,0,6.26V17.8a2.885,2.885,0,0,0,2.885,2.885H14.423A2.885,2.885,0,0,0,17.308,17.8V6.26A2.885,2.885,0,0,0,14.423,3.375Zm0,28.846H2.885A2.885,2.885,0,0,0,0,35.106V46.644a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V35.106A2.885,2.885,0,0,0,14.423,32.221Zm75,31.731H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V66.837A2.885,2.885,0,0,0,89.423,63.952Zm0-57.692H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769A2.885,2.885,0,0,0,31.731,17.8H89.423a2.885,2.885,0,0,0,2.885-2.885V9.144A2.885,2.885,0,0,0,89.423,6.26Zm0,28.846H31.731a2.885,2.885,0,0,0-2.885,2.885V43.76a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V37.99A2.885,2.885,0,0,0,89.423,35.106Z" transform="translate(0 -3.375)"></path></svg></span><?php _e( 'Post embed', 'newsletter-glue' ); ?>
			</div>
			<div class="components-placeholder__fieldset">
				<div class="ngl-article-status"></div>
				<form class="ngl-article-add" action="" method="post" novalidate>
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
		$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="{permalink}"><img src="{featured_image}" data-original-src="{featured_image}" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a>' . $show_edit_controls . '</div>' : '';
		$display_labels     = ( $show_labels ) ? '<div class="ngl-article-labels" ' . $editable . '>{labels}</div>' : '';
		$display_title  	= '<div class="ngl-article-title"><a href="{permalink}" style="font-size: ' . $font_size_title . 'px;' . $link_color . '"><span ' . $editable . '>{title}</span></a></div>';
		$display_excerpt 	= '<div class="ngl-article-excerpt" ' . $editable . '>{excerpt}</div>';
		$display_date       = ( $show_date ) ? '<div class="ngl-article-date">{date}</div>' : '';
	?>

	<div class="ngl-article ngl-article-img-<?php echo $image_position; ?> ngl-article-placeholder" data-key="{key}" data-post-id="{post_id}" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>; font-size: <?php echo $font_size_text; ?>px;">

				<div class="ngl-article-list-move">
					<div class="ngl-article-list-move-up"><a href="#"><span class="material-icons">expand_less</span></a></div>
					<div class="ngl-article-list-move-down"><a href="#"><span class="material-icons">expand_more</span></a></div>
				</div>

				<div class="ngl-article-list-layer"></div>
				<div class="ngl-article-list-layer2"></div>

				<a href="#" class="ngl-article-list-link"><i class="linkify icon"></i></a>
				<a href="#" class="ngl-article-list-refresh"><i class="sync icon"></i></a>
				<a href="#" class="ngl-article-list-delete"><i class="trash alternate outline icon"></i></a>

				<div class="ngl-article-state-refreshing"><?php _e( 'Refreshing...', 'newsletter-glue' ); ?></div>
				<a href="#" class="ngl-article-state-remove"><?php _e( 'Confirm remove', 'newsletter-glue' ); ?></a>

				<div class="ngl-article-list-url-edit">
					<span contenteditable="true">{permalink}</span>
					<a href="#"><i class="check icon"></i></a>
				</div>

				<div class="ngl-article-overlay"></div>

				<?php
					if ( $table_ratio == 'full' ) :
						echo $display_image;
						echo $display_labels;
						echo $display_title;
						echo $display_excerpt;
						echo $display_date;
					else :
						if ( $image_position == 'left' ) :
							echo '<div class="ngl-article-left">' . $display_image . '</div>';
							echo '<div class="ngl-article-right">';
								echo $display_labels;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
						endif;
						if ( $image_position == 'right' ) :
							echo '<div class="ngl-article-left">';
								echo $display_labels;
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

					$display_labels = ( $show_labels ) ? '<div class="ngl-article-labels" ' . $editable . '>' . $this->get_labels( $thearticle->ID ) . '</div>' : '';
					if ( ! $editable && $show_labels && ! $this->get_labels( $thearticle->ID ) ) {
						$display_labels = '';
					}

					if ( ! empty( $thearticle->is_remote ) ) {
						$display_image  	= ( $show_image && ! empty( $thearticle->image_url ) ) ? '<div class="ngl-article-featured"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . $this->get_image_url( $thearticle ) . '" data-original-src="' . $this->get_image_default( $thearticle ). '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a>' . $show_edit_controls . '</div>' : '';
					} else {
						$display_image  	= ( $show_image ) ? '<div class="ngl-article-featured"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '"><img src="' . $this->get_image_url( $thearticle ) . '" data-original-src="' . $this->get_image_default( $thearticle ). '" style="border-radius: ' . absint( $image_radius ) . 'px;" /></a>' . $show_edit_controls . '</div>' : '';
					}

					$thecontent 		= apply_filters( 'newsletterglue_article_embed_content', strip_shortcodes( $thearticle->post_content ), $thearticle->ID );
					$display_title 		= '<div class="ngl-article-title"><a href="' . $this->get_permalink( $thearticle ) . '" target="' . $new_window . '" rel="' . $nofollow . '" style="font-size: ' . $font_size_title . 'px;' . $link_color . '">';
					$display_title     .= '<span ' . $editable . '>' . $this->display_title( $thearticle->ID, $thearticle ) . '</span></a></div>';
					$display_excerpt 	= '<div class="ngl-article-excerpt" ' . $editable . '>' . $this->display_excerpt( $thearticle->ID, $thecontent ) . '</div>';
					$display_date    	= ( $show_date && ! empty( $thearticle->post_date ) ) ? '<div class="ngl-article-date">' . date_i18n( $date_format, strtotime( $thearticle->post_date ) ) . '</div>' : '';

				else :

				endif;

				if ( ! $show_image ) {
					$table_ratio = 'full';
				}

				if ( $table_ratio === 'full' ) {
					$image_position = 'full';
				}

		?>

			<!--[if !mso]><\!-->
			<div class="ngl-article-mobile">
				<table border="0" width="100%" cellpadding="<?php echo newsletterglue_padding_factor(); ?>" cellspacing="0" style="table-layout: fixed;mso-table-lspace:0;mso-table-rspace:0;">
					<tr>
						<td valign="top" style="vertical-align: top;margin:0;">
							<div class="ngl-article-mob-wrap" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>; font-size: <?php echo $font_size_text; ?>px;">
							<?php echo $display_image . $display_labels . $display_title . $display_excerpt . $display_date; ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<!-- <![endif]-->

			<div class="ngl-article ngl-article-img-<?php echo $image_position; ?>" data-key="<?php echo $key; ?>" data-post-id="<?php echo $thearticle->ID; ?>" style="<?php echo $text_color; ?>background-color: <?php echo $background_color; ?>; padding: <?php echo $padding; ?>; border-radius: <?php echo absint( $border_radius ); ?>px; border: <?php echo absint( $border_size ); ?>px <?php echo $border_style; ?> <?php echo $border_color; ?>; font-size: <?php echo $font_size_text; ?>px;">

				<?php if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) : ?>
				<?php if ( ! defined( 'NGL_IN_EMAIL' ) ) : ?>

					<div class="ngl-article-list-move">
						<div class="ngl-article-list-move-up"><a href="#"><span class="material-icons">expand_less</span></a></div>
						<div class="ngl-article-list-move-down"><a href="#"><span class="material-icons">expand_more</span></a></div>
					</div>

					<div class="ngl-article-list-layer"></div>
					<div class="ngl-article-list-layer2"></div>

					<a href="#" class="ngl-article-list-link"><i class="linkify icon"></i></a>
					<a href="#" class="ngl-article-list-refresh"><i class="sync icon"></i></a>
					<a href="#" class="ngl-article-list-delete"><i class="trash alternate outline icon"></i></a>

					<div class="ngl-article-state-refreshing"><?php _e( 'Refreshing...', 'newsletter-glue' ); ?></div>
					<a href="#" class="ngl-article-state-remove"><?php _e( 'Confirm remove', 'newsletter-glue' ); ?></a>

					<div class="ngl-article-list-url-edit">
						<span contenteditable="true"><?php echo $this->get_permalink( $thearticle ); ?></span>
						<a href="#"><i class="check icon"></i></a>
					</div>

					<div class="ngl-article-overlay"></div>

				<?php endif; ?>
				<?php endif; ?>

				<?php
					if ( $table_ratio == 'full' ) :
						echo $display_image;
						echo $display_labels;
						echo $display_title;
						echo $display_excerpt;
						echo $display_date;
					else :
						if ( $image_position == 'left' ) :
							echo '<div class="ngl-article-left">' . $display_image . '</div>';
							echo '<div class="ngl-article-right">';
								echo $display_labels;
								echo $display_title;
								echo $display_excerpt;
								echo $display_date;
							echo '</div>';
						endif;
						if ( $image_position == 'right' ) :
							echo '<div class="ngl-article-left">';
								echo $display_labels;
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