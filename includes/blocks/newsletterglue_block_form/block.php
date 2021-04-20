<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Form extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_form';

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			// Ajax hooks.
			add_action( 'wp_ajax_newsletterglue_block_form_subscribe', array( $this, 'subscribe' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_block_form_subscribe', array( $this, 'subscribe' ) );
		}

	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43.403 34.722" class="ngl-block-svg-icon">
			<path d="M42.063,6H7.34a4.335,4.335,0,0,0-4.319,4.34L3,36.382a4.353,4.353,0,0,0,4.34,4.34H42.063a4.353,4.353,0,0,0,4.34-4.34V10.34A4.353,4.353,0,0,0,42.063,6Zm0,8.681L24.7,25.531,7.34,14.681V10.34L24.7,21.191,42.063,10.34Z" transform="translate(-3 -6)"/>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Subscriber form', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'New subscribers can sign up to your mailing list with this form.', 'newsletter-glue' );
	}

	/**
	 * Get defaults.
	 */
	public function get_defaults() {

		return array(
			'show_in_blog' 	=> true,
			'show_in_email' => false,
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
				'show_in_email'	=> false,
			);
		}

		$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/js/';
		$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/css/';

		$suffix  = '';

		$defaults[ 'btn_bg' ] 		= newsletterglue_get_theme_option( 'btn_bg' );
		$defaults[ 'btn_border' ] 	= newsletterglue_get_theme_option( 'btn_border' ) ? newsletterglue_get_theme_option( 'btn_border' ) : 'transparent';
		$defaults[ 'btn_colour' ] 	= newsletterglue_get_theme_option( 'btn_colour' );
		$defaults[ 'connect_url' ]  = esc_url( admin_url( 'admin.php?page=ngl-settings&tab=connect' ) );
		$defaults[ 'connect_esp' ]  = __( 'Start by connecting your email software &#x21C4;', 'newsletter-glue' );

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/form', array(
			'editor_script'   => $this->asset_id,
			'editor_style'    => $this->asset_id . '-style',
			'render_callback' => array( $this, 'render_block' ),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

		$inputs = '';

		$defaults = get_option( $this->id );
		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> false,
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
			$content = str_replace( '<button', '<a href="{post_permalink}"', $content );
			$content = str_replace( '</button>', '</a>', $content );
		}

		$content = str_replace( 'class="wp-block-newsletterglue-form', 'data-app="' . newsletterglue_default_connection() . '" class="wp-block-newsletterglue-form', $content );

		if ( ! defined( 'NGL_IN_EMAIL' ) && $content ) {
			if ( is_array( $attributes ) ) {
				$list_id 		= isset( $attributes[ 'list_id' ] ) ? $attributes[ 'list_id' ] : '';
				$extra_list_id  = isset( $attributes[ 'extra_list_id' ] ) ? $attributes[ 'extra_list_id' ] : '';
				$double_optin 	= isset( $attributes[ 'double_optin' ] ) ? 'no' : 'yes';
				if ( $list_id ) {
					if ( is_array( $list_id ) ) {
						$list_id = implode( ',', $list_id );
					}
					$inputs .= '<input type="hidden" name="ngl_list_id" id="ngl_list_id" value="' . esc_attr( $list_id ) . '">';
				}
				if ( $extra_list_id ) {
					if ( is_array( $extra_list_id ) ) {
						$extra_list_id = implode( ',', $extra_list_id );
					}
					$inputs .= '<input type="hidden" name="ngl_extra_list_id" id="ngl_extra_list_id" value="' . esc_attr( $extra_list_id ) . '">';
				}
				$inputs .= '<input type="hidden" name="ngl_double_optin" id="ngl_double_optin" value="' . esc_attr( $double_optin ) . '">';
				if ( $inputs ) {
					$content = str_replace( '</form>', $inputs . '</form>', $content );
				}
			}
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
		.ngl-form {
			max-width: 100% !important;
			margin-top: 0 !important;
			margin-bottom: 25px !important;
			position: relative;
		}

		.ngl-form h2 {
			font-size: 24px !important;
		}

		.ngl-form-input-text {
			border: 1px solid #aaa;
			padding: 5px 14px;
			border-radius: 0;
			background: #fff;
			height: 40px;
			width: 100%;
			box-sizing: border-box;
		}

		.ngl-form-field {
			margin: 0 0 25px;
			text-align: left !important;
			display: none !important;
		}

		.ngl-form-label {
			user-select: none;
		}

		.ngl-form.ngl-portrait .ngl-form-button {
			width: 100%;
			display: block;
		}

		.ngl-form.ngl-landscape {

		}

		.ngl-form.ngl-landscape .ngl-form-container {
			display: flex;
			align-items: flex-end;
			flex-wrap: wrap;
		}

		.ngl-form.ngl-landscape .ngl-form-field {
			margin-bottom: 0;
			flex: auto;
		}

		.ngl-form.ngl-landscape .ngl-form-button {
			text-align: center;
			height: 40px;
			min-width: 180px;
		}

		.ngl-form.ngl-landscape .ngl-form-text {
			flex-basis: 100%;
		}

		.ngl-form-header {
			margin-bottom: 10px;
		}

		.ngl-message-overlay {
			text-align: center;
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			opacity: 0;
			transition: opacity 0.25s ease-in-out;
			pointer-events: none;
			visibility: hidden;
			height: 0;
		}

		.ngl-message-overlay.ngl-show {
			opacity: 1;
			pointer-events: auto;
			visibility: visible;
			min-height: 200px;
			height: auto;
		}

		.ngl-message-svg-wrap {
			background: #5bca64;
			width: 40px;
			line-height: 40px;
			height: 40px;
			border-radius: 999px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
		}

		.ngl-message-svg-wrap svg {
			stroke-width: 2px !important;
			fill: transparent !important;
		}

		.ngl-message-overlay-text {
			font-size: 18px;
			margin: 14px 0 0;
		}

		.ngl-form-checkbox, .ngl-form-text {
			display: none !important;
			visibility: hidden !important;
		}

		p.ngl-form-description {
			Margin-bottom: 15px;
		}
		<?php
	}

	/**
	 * Subscribe a user via a form.
	 */
	public function subscribe() {

		$result = 0;
		$error  = '';

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		// Get app.
		$app 	= isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';

		// App Instance.
		if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
			wp_die( -1 );
		}

		include_once newsletterglue_get_path( $app ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $app );
		$api		= new $classname();

		// Prepare data to send to the ESP endpoint.
		foreach( $_POST as $key => $value ) {
			if ( strstr( $key, 'ngl_' ) ) {
				$stripped_key 			= str_replace( 'ngl_', '', $key );
				$value 					= sanitize_text_field( $_POST[ $key ] );
				$data[ $stripped_key ] 	= $value;
			}
		}

		// No email.
		if ( empty( $data[ 'email' ] ) ) {
			$error = __( 'Please enter an email.', 'newsletter-glue' );
		} else if ( ! is_email( $data[ 'email' ] ) ) {
			$error = __( 'Please enter a valid email.', 'newsletter-glue' );
		}

		// Return any errors.
		if ( $error ) {
			wp_send_json( array(
				'success'	=> false,
				'message' 	=> $error
			) );
		}

		// Load the ESP API to add a user and return a result.
		if ( method_exists( $api, 'add_user' ) ) {
			$result = $api->add_user( $data );
		}

		// Do something after that. 3rd party hooks.
		do_action( 'newsletterglue_form_block_signup', $app, $api, $data );

		// Return result.
		if ( $result > 0 ) {
			wp_send_json_success();
		} else {
			wp_send_json( array(
				'success'	=> false,
				'message' 	=> __( 'We could not subscribe you at this time. Try again later.', 'newsletter-glue' )
			) );
		}

	}

	/**
	 * Tableize.
	 */
	public function tableize( $content ) {

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		// remove unwanted elements.
		$replace = 'div.ngl-message-overlay, .ngl-form-field';
		foreach( $output->find( $replace ) as $key => $element ) {
			$output->find( $replace, $key )->outertext = '';
		}

		// remove unwanted elements.
		$replace = '.ngl-form-container > a';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->class = 'wp-block-button__link';
		}

		// remove unwanted elements.
		$replace = '.wp-block-newsletterglue-form';
		foreach( $output->find( $replace ) as $key => $element ) {
			$output->find( $replace, $key )->outertext = '<table width="100%" border="0" cellpadding="10" cellspacing="0" style="mso-table-lspace:0;mso-table-rspace:0;"><tr><td valign="top" style="vertical-align: top;margin:0;">' . $element->innertext . '</td></tr></table>';
		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Form;