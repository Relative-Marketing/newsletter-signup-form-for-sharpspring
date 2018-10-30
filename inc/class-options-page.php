<?php
/**
 * Class OptionsPage
 *
 * Creates an options page that allows the user to enter their sharpsprings secret and other info
 */

namespace RelativeMarketing\Newsletter;

class Options_Page {
	/**
	 * Static property to hold our singleton instance
	 *
	 * @var bool
	 */
	static protected $instance = false;

	/**
	 * constructor.
	 */
	private function __construct() {
		$this->start();
	}

	protected function start() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu_item' ] );
		add_action( 'admin_init', [ $this, 'display_theme_panel_fields' ] );
	}

	/**
	 * Adds a sub menu page 
	 */
	public function add_admin_menu_item() {
		add_submenu_page( 'options-general.php', 'Relative newsletter', 'Relative newsletter', 'manage_options', 'relative-newsletter', [ $this, 'option_page_callback' ] );
	}

	/**
	 * The main option page content
	 */
	public function option_page_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page' ) );
		}
		?>
		<div class="wrap">
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'relative-newsletter-section' );
				?>
				<h1>Relative Newsletter</h1>
				<p>Please add the relevant information for the newsletter popup</p>
				<?php
				do_settings_sections( 'relative-newsletter-options' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * The field that accepts the GTM ID
	 */
	public function display_theme_panel_fields() {
		add_settings_section( 'relative-newsletter-section', 'Newsletter settings', null, 'relative-newsletter-options' );

		$fields = [
			['key' => 'relative_newsletter_sharpspring_campaign_id', 'heading' => 'Sharpspring Campaign Id' , 'callback' => 'display_campaign_id_input'],
			['key' => 'relative_newsletter_heading', 'heading' => 'Newsletter Heading' , 'callback' => 'display_heading_input'],
			['key' => 'relative_newsletter_heading', 'heading' => 'Newsletter Heading' , 'callback' => 'display_heading_input'],
			['key' => 'relative_newsletter_paragraph', 'heading' => 'Newsletter paragraph' , 'callback' => 'display_paragraph_input'],
			['key' => 'relative_newsletter_error_heading', 'heading' => 'Error Heading' , 'callback' => 'display_error_heading_input'],
			['key' => 'relative_newsletter_error_message', 'heading' => 'Error message' , 'callback' => 'display_error_message_input'],
			['key' => 'relative_newsletter_success_heading', 'heading' => 'Success Heading' , 'callback' => 'display_success_heading_input'],
			['key' => 'relative_newsletter_success_message', 'heading' => 'Success message' , 'callback' => 'display_success_message_input'],
			['key' => 'relative_newsletter_img_x1', 'heading' => 'Image @1x resolution' , 'callback' => 'display_img_1x_input'],
			['key' => 'relative_newsletter_img_x2', 'heading' => 'Image @2x resolution' , 'callback' => 'display_img_2x_input'],
			['key' => 'relative_newsletter_img_x3', 'heading' => 'Image @3x resolution' , 'callback' => 'display_img_3x_input'],
			['key' => 'relative_newsletter_img_alt', 'heading' => 'Image alt' , 'callback' => 'display_img_alt_input'],
			['key' => 'relative_newsletter_popup_delay', 'heading' => 'Popup Delay' , 'callback' => 'display_popup_delay_input'],

		];
		
		foreach ($fields as $field) {
			add_settings_field( $field['key'], $field['heading'], [ $this, $field['callback'] ], 'relative-newsletter-options', 'relative-newsletter-section' );
			register_setting( 'relative-newsletter-section', $field['key'] );
		}

	}

	public function add_input( $option ) {
		echo sprintf( '<input name="%s" id="%1$s" value="%2$s" />', $option, sanitize_text_field( get_option( $option ) ) );
	}

	public function display_campaign_id_input() {
		$this->add_input( 'relative_newsletter_sharpspring_campaign_id' );
		echo '<br/><span>Note: This is the campaign the user will be added to when they are signed up</span>';
	}

	public function display_popup_delay_input() {
		$this->add_input( 'relative_newsletter_popup_delay' );
	}

	public function display_img_alt_input() {
		$this->add_input( 'relative_newsletter_img_alt' );
	}

	public function display_img_1x_input() {
		$this->add_input( 'relative_newsletter_img_x1' );
	}
	public function display_img_2x_input() {
		$this->add_input( 'relative_newsletter_img_x2' );
	}
	public function display_img_3x_input() {
		$this->add_input( 'relative_newsletter_img_x3' );
	}

	public function display_success_message_input() {
		$this->add_input( 'relative_newsletter_success_message' );
	}
	
	public function display_success_heading_input() {
		$this->add_input( 'relative_newsletter_success_heading' );
	}
	
	public function display_error_message_input() {
		$this->add_input( 'relative_newsletter_error_message' );
	}
	
	public function display_error_heading_input() {
		$this->add_input( 'relative_newsletter_error_heading' );
	}

	public function display_paragraph_input() {
		$this->add_input( 'relative_newsletter_paragraph' );
	}

	public function display_heading_input() {
		$this->add_input( 'relative_newsletter_heading' );
	}

	/**
	 * The form input for the secret key
	 */
	public function display_secret_input() {
		$this->add_input( 'ss_secret_key' );
	}

	/**
	 * The form input
	 */
	public function display_api_input() {
		$this->add_input( 'ss_api_key' );
	}

	/**
	 * If an instance exists , this returns it. If not it creates one and then returns it.
	 *
	 * @return Options_Page
	 */

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}