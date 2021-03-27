<?php
/**
 * Upgrade.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Upgrade class.
 */
class NGL_Upgrade {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Add setting tab.
		add_filter( 'newsletterglue_settings_tabs', array( $this, 'add_tab' ), 20 );
		add_filter( 'newsletterglue_settings_tab_license_save_button', '__return_false' );
		add_action( 'newsletterglue_settings_tab_license', array( $this, 'show_settings' ), 20 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );

	}

	/**
	 * Add tab.
	 */
	public function add_tab( $tabs ) {

		foreach( $tabs as $key => $value ) {
			$new_tabs[ $key ] = $value;
			if ( $key == 'css' ) {
				$new_tabs[ 'license' ] = __( 'Pro License', 'newsletter-glue' );
			}
		}

		return $new_tabs;

	}

	/**
	 * Link to Pro license.
	 */
	public function admin_menu() {
		
		add_submenu_page( 'newsletter-glue', __( 'Upgrade to Pro', 'newsletter-glue' ), __( 'Upgrade to Pro', 'newsletter-glue' ), 'manage_newsletterglue', 'admin.php?page=ngl-settings&tab=license' );

	}

	/**
	 * Show tab.
	 */
	public function show_settings() {
		$url = NGL_PLUGIN_URL . 'assets/images/iconset/';
	?>
	<div class="ngl-upgrade">
		<div class="ngl-upgrade-left">

			<h2>
				Big changes are coming:<br />
				We’re moving some features<br />
				to our paid plugin on April 30 2021
			</h2>
			<p><a href="https://newsletterglue.com/pricing/?discount=UPGRADE70" target="_blank"><strong>Upgrade now</strong></a> to get 70% off your first year. Applies to all plans. Expires April 30 2021.</p>

			<div class="ngl-upgrade-box">
				<h3>Free plugin features<span class="ngl-upgrade-tag1">Live on April 30 2021</span></h3>
				<div class="ngl-upgrade-list">
					<div class="ngl-upgrade-item"><span style="background:#FFE01B;"><img src="<?php echo $url; ?>mailchimp.png" alt="" style="width: 21px;height: 22px;" /></span>Mailchimp</div>
					<div class="ngl-upgrade-item"><span style="background: transparent;"><img src="<?php echo $url; ?>accent.png" alt="" /></span>Newsletter Accent Color</div>
				</div>
			</div>

			<div class="ngl-upgrade-box">
				<h3>Pro plugin features<span class="ngl-upgrade-tag2">Available now</span></h3>

				<div class="ngl-upgrade-lists">
				<div class="ngl-upgrade-list">
					<div class="ngl-upgrade-item"><span style="background:#FFE01B;"><img src="<?php echo $url; ?>mailchimp.png" alt="" style="width: 21px;height: 22px;" /></span>Mailchimp</div>
					<div class="ngl-upgrade-item"><span style="background:#356AE6;"><img src="<?php echo $url; ?>activecampaign.png" alt="" style="width: 14px;height: 21px;" /></span>ActiveCampaign</div>
					<div class="ngl-upgrade-item"><span style="background:#7856FF;"><img src="<?php echo $url; ?>campaignmonitor.png" alt="" style="width: 21px;height: 14px;" /></span>Campaign Monitor</div>
					<div class="ngl-upgrade-item"><span style="background:#00A1ED;"><img src="<?php echo $url; ?>getresponse.png" alt="" style="width: 22px;height: 14px;" /></span>GetResponse</div>
					<div class="ngl-upgrade-item"><span style="background:#21C16C;"><img src="<?php echo $url; ?>mailerlite.png" alt="" style="width: 20px;height: 16px;" /></span>MailerLite</div>
					<div class="ngl-upgrade-item"><span style="background:#0092FF;"><img src="<?php echo $url; ?>sendinblue.png" alt="" style="width: 18px;height: 21px;" /></span>Sendinblue</div>
					<div class="ngl-upgrade-item"><span style="background: transparent;"><img src="<?php echo $url; ?>sendy.png" alt="" /></span>Sendy</div>
				</div>
				<div class="ngl-upgrade-list">
					<div class="ngl-upgrade-item"><span><img src="<?php echo $url; ?>theme.png" alt="" /></span>Newsletter Theme Designer</div>
					<div class="ngl-upgrade-item"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43.403 34.722" style="fill: #fff; width: 20px;"><path d="M42.063,6H7.34a4.335,4.335,0,0,0-4.319,4.34L3,36.382a4.353,4.353,0,0,0,4.34,4.34H42.063a4.353,4.353,0,0,0,4.34-4.34V10.34A4.353,4.353,0,0,0,42.063,6Zm0,8.681L24.7,25.531,7.34,14.681V10.34L24.7,21.191,42.063,10.34Z" transform="translate(-3 -6)"></path></svg></span>Subscribers forms</div>
					<div class="ngl-upgrade-item"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 40.625" style="fill: #fff; width: 20px;"><path d="M7.813,34.625H1.563A1.562,1.562,0,0,0,0,36.188v6.25A1.563,1.563,0,0,0,1.562,44h6.25a1.563,1.563,0,0,0,1.563-1.562v-6.25A1.562,1.562,0,0,0,7.813,34.625Zm0-31.25H1.563A1.563,1.563,0,0,0,0,4.938v6.25A1.562,1.562,0,0,0,1.562,12.75h6.25a1.562,1.562,0,0,0,1.563-1.562V4.938A1.563,1.563,0,0,0,7.813,3.375ZM7.813,19H1.563A1.563,1.563,0,0,0,0,20.563v6.25a1.562,1.562,0,0,0,1.562,1.563h6.25a1.562,1.562,0,0,0,1.563-1.562v-6.25A1.563,1.563,0,0,0,7.813,19ZM48.438,36.188H17.188a1.563,1.563,0,0,0-1.562,1.563v3.125a1.563,1.563,0,0,0,1.563,1.563h31.25A1.563,1.563,0,0,0,50,40.875V37.75A1.563,1.563,0,0,0,48.438,36.188Zm0-31.25H17.188A1.562,1.562,0,0,0,15.625,6.5V9.625a1.562,1.562,0,0,0,1.563,1.563h31.25A1.563,1.563,0,0,0,50,9.625V6.5A1.563,1.563,0,0,0,48.438,4.938Zm0,15.625H17.188a1.562,1.562,0,0,0-1.562,1.562V25.25a1.562,1.562,0,0,0,1.563,1.563h31.25A1.562,1.562,0,0,0,50,25.25V22.125A1.563,1.563,0,0,0,48.438,20.563Z" transform="translate(0 -3.375)"></path></svg></span>Post embeds</div>
					<div class="ngl-upgrade-item"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42.301 42.301" style="fill: #fff; width: 20px;"><path xmlns="http://www.w3.org/2000/svg" d="M21.15.563A21.15,21.15,0,1,0,42.3,21.713,21.147,21.147,0,0,0,21.15.563Zm0,8.187a7.5,7.5,0,1,1-7.5,7.5A7.505,7.505,0,0,1,21.15,8.75Zm0,29.338A16.343,16.343,0,0,1,8.656,32.271a9.509,9.509,0,0,1,8.4-5.1,2.087,2.087,0,0,1,.606.094,11.292,11.292,0,0,0,3.488.588,11.249,11.249,0,0,0,3.488-.588,2.087,2.087,0,0,1,.606-.094,9.509,9.509,0,0,1,8.4,5.1A16.343,16.343,0,0,1,21.15,38.087Z" transform="translate(0 -0.563)"></path></svg></span>Author bylines</div>
					<div class="ngl-upgrade-item"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill: #fff; width: 20px;"><path d="M21 15V18H24V20H21V23H19V20H16V18H19V15H21M14 18H3V6H19V13H21V6C21 4.89 20.11 4 19 4H3C1.9 4 1 4.89 1 6V18C1 19.11 1.9 20 3 20H14V18Z"></path></svg></span>Callout cards</div>
					<div class="ngl-upgrade-item"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31.5 31.5" style="fill: #fff; width: 20px;"><g transform="translate(-115 -126)"><path d="M30.984,12.8l.5-2.812A.844.844,0,0,0,30.656,9H25.4l1.028-5.758a.844.844,0,0,0-.831-.992H22.737a.844.844,0,0,0-.831.7L20.825,9H13.89l1.028-5.758a.844.844,0,0,0-.831-.992H11.23a.844.844,0,0,0-.831.7L9.318,9H3.757a.844.844,0,0,0-.831.7l-.5,2.813a.844.844,0,0,0,.831.992h5.26l-1.607,9H1.346a.844.844,0,0,0-.831.7l-.5,2.813A.844.844,0,0,0,.844,27H6.1L5.076,32.758a.844.844,0,0,0,.831.992H8.763a.844.844,0,0,0,.831-.7L10.675,27H17.61l-1.028,5.758a.844.844,0,0,0,.831.992H20.27a.844.844,0,0,0,.831-.7L22.182,27h5.561a.844.844,0,0,0,.831-.7l.5-2.813a.844.844,0,0,0-.831-.992h-5.26l1.607-9h5.561a.844.844,0,0,0,.831-.7Zm-12.57,9.7H11.479l1.607-9h6.935Z" transform="translate(115 123.75)"></path></g></svg></span>Metadata</div>
					<div class="ngl-upgrade-item"><span><svg xmlns="http://www.w3.org/2000/svg" width="18.665" height="15.159" viewBox="0 0 18.665 15.159" style="fill: #fff; width: 20px;"><path  d="M16.746,7.159c.012.166.012.332.012.5A10.809,10.809,0,0,1,5.874,18.54,10.81,10.81,0,0,1,0,16.823a7.913,7.913,0,0,0,.924.047,7.661,7.661,0,0,0,4.749-1.634A3.832,3.832,0,0,1,2.1,12.583a4.824,4.824,0,0,0,.722.059,4.046,4.046,0,0,0,1.007-.13A3.826,3.826,0,0,1,.758,8.757V8.71A3.852,3.852,0,0,0,2.487,9.2,3.831,3.831,0,0,1,1.3,4.079a10.873,10.873,0,0,0,7.887,4A4.318,4.318,0,0,1,9.1,7.206a3.829,3.829,0,0,1,6.62-2.617,7.531,7.531,0,0,0,2.428-.924,3.815,3.815,0,0,1-1.682,2.108,7.669,7.669,0,0,0,2.2-.592,8.223,8.223,0,0,1-1.919,1.978Z" transform="translate(0 -3.381)"/></svg></span>Social embeds</div>
				</div>
				</div>

				<h3>Get Newsletter Glue Pro now<br />for 70% off.</h3>
				<div class="ngl-upgrade-cta">
					<a href="https://newsletterglue.com/pricing/?discount=UPGRADE70" target="_blank">Upgrade to Pro <i class="arrow right icon"></i></a>
				</div>
				<p class="ngl-upgrade-small">Expires April 30 2021</p>

			</div>

		</div>
		<div class="ngl-upgrade-right">
			<h3>FAQ</h3>
			<h3>Why are you doing this?</h3>
			<p>Newsletter Glue is a small and young company. This change helps us build a sustainable business, improve the plugin, fix bugs, and support our users in the long run.</p>
			<div style="height:40px;"></div>
			<h3>What will this mean for me?</h3>
			<p><strong>For Mailchimp:</strong> Your connection will continue to work. You’ll just have fewer design customisation options in the future.</p>
			<p><strong>For other email connections:</strong> You’ll need to upgrade to Newsletter Glue Pro to maintain your connection.</p>
			<p><strong>Get 70% off:</strong> As a thank you for being an early user, we’re giving you a massive 70% off your first year on all our pro plans.</p>
			<div style="height:40px;"></div>
			<h3>What if I don’t want to upgrade?</h3>
			<p>As long as you don’t update the plugin, no changes will take place.</p>
			<p>Note: this means you won’t get any bug fixes or new features.</p>
			<div style="height:40px;"></div>
			<h3 class="ngl-upgrade-heading">Ready to upgrade? <a href="https://newsletterglue.com/pricing/?discount=UPGRADE70" target="_blank">See Pro plans<i class="arrow right icon"></i></a></h3>
			<p>If you have more questions, please reach out at <a href="mailto:support@newsletterglue.com" style="color: #003C4E !important;">support@newsletterglue.com</a></p>
		</div>
	</div>
	<?php
	}

}

return new NGL_Upgrade;