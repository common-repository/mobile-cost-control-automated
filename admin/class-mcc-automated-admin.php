<?php



/**

 * The admin-specific functionality of the plugin.

 *

 * @link       https://www.google.com/

 * @since      1.0.0

 *

 * @package    Mcc_Automated

 * @subpackage Mcc_Automated/admin

 */



/**

 * The admin-specific functionality of the plugin.

 *

 * Defines the plugin name, version, and two examples hooks for how to

 * enqueue the admin-specific stylesheet and JavaScript.

 *

 * @package    Mcc_Automated

 * @subpackage Mcc_Automated/admin

 * @author     Validas LLC

 */

class Mcc_Automated_Admin{



	/**

	 * The ID of this plugin.

	 *

	 * @since    1.0.0

	 * @access   private

	 * @var      string    $plugin_name    The ID of this plugin.

	 */

	private $plugin_name;



	/**

	 * The version of this plugin.

	 *

	 * @since    1.0.0

	 * @access   private

	 * @var      string    $version    The current version of this plugin.

	 */

	private $version;



	/**

	 * Initialize the class and set its properties.

	 *

	 * @since    1.0.0

	 * @param      string    $plugin_name       The name of this plugin.

	 * @param      string    $version    The version of this plugin.

	 */

	public function __construct( $plugin_name, $version )

	{

		$this->plugin_name = $plugin_name;

		$this->version = $version;

	}



	/**

	 * Register the stylesheets for the admin area.

	 *

	 * @since    1.0.0

	 */

	public function enqueue_styles() {



		/**

		 * This function is provided for demonstration purposes only.

		 *

		 * An instance of this class should be passed to the run() function

		 * defined in Mcc_Automated_Loader as all of the hooks are defined

		 * in that particular class.

		 *

		 * The Mcc_Automated_Loader will then create the relationship

		 * between the defined hooks and the functions defined in this

		 * class.

		 */



	}



	/**

	 * Register the JavaScript for the admin area.

	 *

	 * @since    1.0.0

	 */

	public function enqueue_scripts() {



		/**

		 * This function is provided for demonstration purposes only.

		 *

		 * An instance of this class should be passed to the run() function

		 * defined in Mcc_Automated_Loader as all of the hooks are defined

		 * in that particular class.

		 *

		 * The Mcc_Automated_Loader will then create the relationship

		 * between the defined hooks and the functions defined in this

		 * class.

		 */



	}

	

	/**

	 * Plugin menu for admin

	 */

	function add_admin_menu(){

		add_menu_page('Labels', 'Mcc Automated', 'manage_options', 'mcc_automated_menu', array( $this, 'form_1_option' ),plugin_dir_url( __FILE__ ) . 'images/mcc_icon.jpg');

		//Labels page
		add_submenu_page( 'mcc_automated_menu', 'Mcc Automated', 'Labels', 'manage_options', 'mcc_automated_menu', array( $this, 'form_1_option' ));

		//Email notification page
		add_submenu_page( 'mcc_automated_menu', 'Email Notification', 'Email Notification', 'manage_options', 'mcc_automated_notification', array( $this, 'email_notification' ));

		//Configuration
		add_submenu_page( 'mcc_automated_menu', 'Offer Configuration', 'Offer Configuration', 'manage_options', 'mcc_automated_configuration', array( $this, 'configurations' ));	

		add_action( 'admin_init', array($this, 'register_mcc_automated_plugin_settings') );

	}

	
	/**

	 * Form Option page

	 */

	function form_1_option(){

		include_once plugin_dir_path( __FILE__ ) . 'partials/mcc-automated-form-1-option.php';

	}

	/**
	 * Email Notification page
	 */
	function email_notification(){
		include_once plugin_dir_path( __FILE__ ) . 'partials/mcc-automated-email-notification.php';
	}

	/**
	 * Configurations
	 */
	function configurations(){
		include_once plugin_dir_path( __FILE__ ) . 'partials/mcc-automated-configurations.php';
	}



	/**

	 * Plugin Setting

	 */

	function register_mcc_automated_plugin_settings() {

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_greeting', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_first_heading', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_form_instructions', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_second_heading', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_total_cost_label', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_total_phone_count_label', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_savings_label', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_giga_usage_label', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_customer_name', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_customer_mail', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_notif_mail', 'sanitize_text_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_form_notif_template', 'sanitize_textarea_field');

		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_savings_formula_percent', 'sanitize_text_field');
		
		register_setting( 'mcc_automated_plugin_options', 'mcc_automated_form2_next_step_msg', 'sanitize_text_field');
	}

	

}

