<?php
/**
 * Status table.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div style="display: none;" class="ngl-modal-contents">

<div class="ngl-modal-title"><a href="<?php echo get_edit_post_link( $post_id ); ?>"><?php echo esc_html( $post->post_title ); ?></a></div>

<table class="wp-list-table widefat fixed striped posts">

	<thead>
		<tr>
			<td scope="col" class="ngl_subject"><?php esc_html_e( 'Subject line', 'newsletter-glue' ); ?></td>
			<td scope="col" class="ngl_status"><?php esc_html_e( 'Newsletter status', 'newsletter-glue' ); ?></td>
			<td scope="col" class="ngl_datetime"><?php esc_html_e( 'Time, Date published', 'newsletter-glue' ); ?></td>
		</tr>
	</thead>

	<tbody>
		<?php foreach( $results as $time => $data ) : ?>
		<tr>
			<td class="ngl_subject"><?php echo esc_html( $data[ 'subject' ] ); ?></td>
			<td class="ngl_status">
				<?php
					$text = '';
					if ( $data['type'] == 'error' ) {
						$text .= '<span class="ngl-state ngl-error">' . esc_html( $data[ 'message' ] ) . '</span>';
					}
					if ( $data['type'] == 'success' ) {
						$text .= '<span class="ngl-state ngl-success">' . esc_html( $data[ 'message' ] ) . '</span>';
					}
					if ( $data['type'] == 'neutral' ) {
						$text .= '<span class="ngl-state ngl-neutral">' . esc_html( $data[ 'message' ] ) . '</span>';
					}
					if ( isset( $data['help'] ) && ! empty( $data[ 'help' ] ) ) {
						$text .= '<span class="ngl-error"><a href="' . esc_url( $data[ 'help' ] ) . '">' . esc_html__( 'Get help', 'newsletter-glue' ) . '</a></span>';
					}
					$text .= '</span>';
					echo $text;
				?>
			</td>
			<td class="ngl_datetime"><?php echo date_i18n( 'G:i, Y/m/d', $time ); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>

</div>