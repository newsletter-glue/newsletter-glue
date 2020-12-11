<?php
/**
 * Articles.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


?>

<div class="ngl-articles ngl-articles-<?php echo $table_ratio; ?> <?php echo ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ? 'ngl-articles-admin' : 'ngl-articles-frontend'; ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">

	<?php if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) : ?>
	<?php if ( ! defined( 'NGL_IN_EMAIL' ) ) : ?>
	<div class="components-placeholder wp-block-embed is-large">
		<div class="ngl-articles-add">
			<div class="components-placeholder__label">
				<span class="block-editor-block-icon has-colors" style="color: rgb(29, 161, 242);"><svg width="24" height="24" viewBox="0 0 92.308 75" role="img" aria-hidden="true" focusable="false"><path fill="#DD3714" d="M14.423,61.067H2.885A2.885,2.885,0,0,0,0,63.952V75.49a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V63.952A2.885,2.885,0,0,0,14.423,61.067Zm0-57.692H2.885A2.885,2.885,0,0,0,0,6.26V17.8a2.885,2.885,0,0,0,2.885,2.885H14.423A2.885,2.885,0,0,0,17.308,17.8V6.26A2.885,2.885,0,0,0,14.423,3.375Zm0,28.846H2.885A2.885,2.885,0,0,0,0,35.106V46.644a2.885,2.885,0,0,0,2.885,2.885H14.423a2.885,2.885,0,0,0,2.885-2.885V35.106A2.885,2.885,0,0,0,14.423,32.221Zm75,31.731H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V66.837A2.885,2.885,0,0,0,89.423,63.952Zm0-57.692H31.731a2.885,2.885,0,0,0-2.885,2.885v5.769A2.885,2.885,0,0,0,31.731,17.8H89.423a2.885,2.885,0,0,0,2.885-2.885V9.144A2.885,2.885,0,0,0,89.423,6.26Zm0,28.846H31.731a2.885,2.885,0,0,0-2.885,2.885V43.76a2.885,2.885,0,0,0,2.885,2.885H89.423a2.885,2.885,0,0,0,2.885-2.885V37.99A2.885,2.885,0,0,0,89.423,35.106Z" transform="translate(0 -3.375)"></path></svg></span><?php _e( 'Article embed', 'newsletter-glue' ); ?>
			</div>
			<div class="components-placeholder__fieldset">
				<form>
					<input type="text" class="components-placeholder__input ngl_article_s" aria-label="<?php _e( 'Search a post or enter a post URL…', 'newsletter-glue' ); ?>" placeholder="<?php _e( 'Search a post or enter a post URL…', 'newsletter-glue' ); ?>" value="">
					<button type="submit" class="components-button is-primary"><?php _e( 'Add', 'newsletter-glue' ); ?></button>
				</form>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>

	<?php if ( $articles ) : ?>
		<table class="ngl-articles-table" cellpadding="0" cellspacing="0" border="0">
		<?php 
			foreach( $articles as $article ) :
				$thearticle = get_post( $article );
				$tags 		= wp_get_post_tags( $thearticle->ID );
		?>

			<tr>
				<td>
					<div class="ngl-article">
						<?php if ( has_post_thumbnail( $article ) && $show_image ) : ?>
						<div class="ngl-article-featured">
							<img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id( $thearticle->ID ), 'full' ); ?>" />
						</div>
						<?php endif; ?>
						<?php if ( $tags && $show_tags ) : ?>
						<div class="ngl-article-tags">
							<?php foreach( $tags as $tag ) : ?>
							<div class="ngl-article-tag"><?php echo $tag->name; ?></div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
						<div class="ngl-article-title"><a href="<?php echo get_permalink( $thearticle->ID ); ?>"><?php echo get_the_title( $thearticle ); ?></a></div>
						<div class="ngl-article-excerpt">
							<?php echo wp_trim_words( $thearticle->post_content, 55 ); ?>
						</div>
						<?php if ( $show_date ) : ?>
						<div class="ngl-article-date">
							<?php echo date_i18n( $date_format, strtotime( $thearticle->post_date ) ); ?>
						</div>
						<?php endif; ?>
					</div>
				</td>
			</tr>

		<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>