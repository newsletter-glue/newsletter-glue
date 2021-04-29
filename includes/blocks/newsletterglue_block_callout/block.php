<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Callout extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_callout';

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );
		}

	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="ngl-block-svg-icon">
			<path d="M21 15V18H24V20H21V23H19V20H16V18H19V15H21M14 18H3V6H19V13H21V6C21 4.89 20.11 4 19 4H3C1.9 4 1 4.89 1 6V18C1 19.11 1.9 20 3 20H14V18Z"/>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Callout card', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Customise the background and border of this card to help its content stand out.', 'newsletter-glue' );
	}

	/**
	 * Get defaults.
	 */
	public function get_defaults() {

		return array(
			'show_in_blog' 	=> true,
			'show_in_email' => true,
		);

	}

	/**
	 * Register the block.
	 */
	public function register_block() {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> true,
			);
		}

		$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/js/';
		$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/css/';

		$suffix  = '';

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/callout', array(
			'editor_script'   => $this->asset_id,
			'editor_style'    => $this->asset_id . '-style',
			'render_callback' => array( $this, 'render_block' ),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> true,
			);
		}

		$show_in_blog  = isset( $attributes[ 'show_in_blog' ] ) ? $attributes[ 'show_in_blog' ] : $defaults[ 'show_in_blog' ];
		$show_in_email = isset( $attributes[ 'show_in_email' ] ) ? $attributes[ 'show_in_email' ] : $defaults[ 'show_in_email' ];

		// Hidden from blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
			$content = '';
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			$content = '';
		}

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			$content = str_replace( '<section', '<div', $content );
			$content = str_replace( '/section>', '/div>', $content );
		}
		
		if ( defined( 'NGL_IN_EMAIL' ) && $content ) {
			$content = $this->tableize( $content );
		}

		return $content;

	}

	/**
	 * Save settings.
	 */
	public function save_settings() {

		delete_option( $this->id );

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array();
		}

		if ( isset( $_POST[ $this->id . '_show_in_email' ] ) ) {
			$defaults[ 'show_in_email' ] = true;
		} else {
			$defaults[ 'show_in_email' ] = false;
		}

		if ( isset( $_POST[ $this->id . '_show_in_blog' ] ) ) {
			$defaults[ 'show_in_blog' ] = true;
		} else {
			$defaults[ 'show_in_blog' ] = false;
		}

		update_option( $this->id, $defaults );

		return $defaults;

	}

	/**
	 * CSS.
	 */
	public function email_css() {
		?>
		.wp-block-newsletterglue-callout {
			padding: 0 !important;
		}

		.wp-block-newsletterglue-callout * {
			text-align: inherit;
		}

		.wp-block-newsletterglue-callout img {
			width: auto;
		}
		<?php
	}

	/**
	 * Tableize.
	 */
	public function tableize( $content ) {

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		$replace = '.wp-block-newsletterglue-callout img';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $element->class ) {
				$element->class = $element->class . ' callout-img';
			} else {
				$element->class = 'callout-img';
			}
		}

		$replace = 'div.wp-block-newsletterglue-callout';
		$gap = 20;
		foreach( $output->find( $replace ) as $key => $element ) {
			$s = $element->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			foreach( $results as $key => $value ) {
				if ( strstr( $key, 'margin' ) ) {
					unset( $results[ $key ] );
				}
				if ( strstr( $key, 'padding' ) ) {
					$gap = absint( $value );
					unset( $results[ $key ] );
				}
			}
			$styles = '';
			foreach( $results as $key => $value ) {
				$styles .= "$key: $value;";
			}

			$original_gap = absint( $gap );

			$gap = $gap - 10;

			if ( $gap <= 0 ) {
				$gap = 5;
			}

			$color = ! empty( $results[ 'color' ] ) ? $results[ 'color' ] : '';

			$top = $original_gap - 10;

			if ( ! empty( $color ) ) {
				foreach( $element->find( '*' ) as $child => $el ) {
					if ( ! strstr( $el->style, 'color:' ) ) {
						$el->style = $el->style . 'color: ' . $color;
					}
				}
			}

			$bg = ! empty( $results[ 'background-color' ] ) ? $results[ 'background-color' ] : 'transaprent';

			$is_bordered = ! empty( $results[ 'border-width' ] ) ? absint( $results[ 'border-width' ] ) * 2 : 0;
			$is_bordered = ! empty( $results[ 'border-style' ] ) && $results[ 'border-style' ] == 'none' ? 0 : $is_bordered;

			$boxed = false;

			$container_padding = '10px 20px';

			if ( $boxed ) {
				$container_padding = 0;
			}

			$boxed_gap = $boxed ? 0 : 40;

			$image_width_for_callout = floor( 600 - ( $original_gap * 2 ) ) - $boxed_gap - $is_bordered;

			foreach( $element->find( 'img' ) as $image_id => $image_el ) {
				$image_el->width = $image_width_for_callout;
				$image_el->height = '';
			}

			$element->outertext = '
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-callout">
				<tr>
					<td valign="top" style="vertical-align: top;padding: ' . $container_padding . ';">
						<div style="' . $styles . '">
						<table bgcolor="' . $bg . '" class="' . $element->class . '" border="0" width="100%" cellpadding="0" cellspacing="0" style="font-size: inherit !important;mso-table-lspace: 0pt; mso-table-rspace: 0pt;border: 0 !important;width: 100% !important;' . $styles . '">
							<tr>
								<td class="ngl-callout-content" style="border:none; vertical-align: top; font-size: inherit !important;padding: ' . $top . 'px ' . $original_gap . 'px;" valign="top">' . $element->innertext . '</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
			</table>';

		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Callout;