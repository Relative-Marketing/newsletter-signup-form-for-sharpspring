<?php
/**
 * Class OptionsPage
 *
 * Creates an options page that allows the user to enter their sharpsprings secret and other info
 */

namespace RelativeMarketing\Options;

class Page {
	/**
	 * Static property to hold our singleton instance
	 *
	 * @var bool
	 */
	static protected $instance = false;

	protected $sections = [
		'relative-newsletter-section' => [
			'title'  => 'Newsletter settings',
			'fields' => [
				'relative_newsletter_sharpspring_campaign_id' => ['heading' => 'Sharpspring Campaign Id', 'desc' => 'Note: This is the campaign the user will be added to when they are signed up', 'type' => 'input'],
				'relative_newsletter_heading'                 => ['heading' => 'Newsletter Heading', 'type' => 'input'],
				'relative_newsletter_heading'                 => ['heading' => 'Newsletter Heading', 'type' => 'input'],
				'relative_newsletter_paragraph'               => ['heading' => 'Newsletter paragraph', 'type' => 'textarea'],
				'relative_newsletter_error_heading'           => ['heading' => 'Error Heading', 'type' => 'input'],
				'relative_newsletter_error_message'           => ['heading' => 'Error message', 'type' => 'input'],
				'relative_newsletter_success_heading'         => ['heading' => 'Success Heading', 'type' => 'input'],
				'relative_newsletter_success_message'         => ['heading' => 'Success message', 'type' => 'input'],
				'relative_newsletter_img_x1'                  => ['heading' => 'Image @1x resolution', 'type' => 'input'],
				'relative_newsletter_img_x2'                  => ['heading' => 'Image @2x resolution', 'type' => 'input'],
				'relative_newsletter_img_x3'                  => ['heading' => 'Image @3x resolution', 'type' => 'input'],
				'relative_newsletter_img_alt'                 => ['heading' => 'Image alt', 'type' => 'input'],
				'relative_newsletter_popup_delay'             => ['heading' => 'Popup Delay', 'type' => 'input'],
			],
		]
	];

	protected $page_arguments = [
		'parent' => 'options-general.php',
		'page_title' => 'Relative newsletter',
		'page_description' => 'Please add the relevant information for the newsletter popup',
		'menu_title' => 'Relative newsletter',
		'menu_slug' => 'relative-newsletter',
		'capability' => 'manage_options',
	];
	/**
	 * constructor.
	 */
	public function __construct() {

	}

	public function render() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu_item' ] );
		add_action( 'admin_init', [ $this, 'display_theme_panel_fields' ] );
	}

	/**
	 * Adds a sub menu page 
	 */
	public function add_admin_menu_item() {
		add_submenu_page( $this->get_parent_page(), $this->get_page_title(), $this->get_menu_title(), $this->get_required_capability(), $this->get_menu_slug(), [ $this, 'option_page_callback' ] );
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
			<div class="page-information">
				<h1><?php echo $this->get_page_title(); ?></h1>
				<p><?php echo $this->get_page_description(); ?></p>
			</div>
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'relative-newsletter-section' );
				/**
				 * Do all the sections for the current page
				 */
				do_settings_sections( $this->get_menu_slug() );
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
		$page_id = $this->get_menu_slug();

		foreach ($this->sections as $section_id => $section) {
			add_settings_section( $section_id, $section['title'], null, $page_id );

			foreach ( $section['fields'] as $option_id => $field ) {
				$callback = 'add_option__' . $field['type'] . '__' . $option_id;

				/**
				 * Add a new field specifying the options id, it's heading, how it should be output on the page, the
				 * section it belongs to.
				 */
				add_settings_field( $option_id, $field['heading'], [ $this, 'add_option' ], $page_id, $section_id, [$option_id, $field['type'], $section_id] );

				/**
				 * Then actually add that setting to the current section
				 */
				register_setting( $section_id, $option_id );
			}
		}
	}

	public function add_option( $option, $type = '' ) {
		$option_id = $option[0];
		$field_type = $option[1];
		$section = $option[2];

		$option_data = $this->sections[$section]['fields'][ $option_id ];

		echo Form\Generate::field( $option_id, $field_type, get_option( $option_id ) );

		// Check to see if a description has been added and if it has output that description
		if ( array_key_exists( 'desc', $option_data ) ) {
			echo '<br/><span>' . sanitize_text_field( $option_data['desc'] ) . '</span>';
		}
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

	/**
	 * Getters and Setters
	 */

	/**
	 * Will return the value of given argument for the page.
	 * 
	 * Although this may seem redundant as the methods that use this method could
	 * access the page_arguments property directly I may want to change how the page
	 * arguments are stored in the future. If the way the page arguments are accessed
	 * changes I will just have to update this one method 👍
	 */
	public function get_page_argument( string $arg ) {
		return $this->page_arguments[ $arg ];
	}

	public function get_parent_page() {
		return $this->get_page_argument( 'parent' );
	}
	
	public function get_page_title() {
		return $this->get_page_argument( 'page_title' );
	}

	public function get_page_description() {
		return $this->get_page_argument( 'page_description' );
	}

	public function get_menu_title() {
		return $this->get_page_argument( 'menu_title' );
	}
	
	public function get_menu_slug() {
		return $this->get_page_argument( 'menu_slug' );
	}
	
	public function get_required_capability() {
		return $this->get_page_argument( 'capability' );
	}
}