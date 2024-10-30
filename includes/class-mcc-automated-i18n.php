<?php



/**

 * Define the internationalization functionality

 *

 * Loads and defines the internationalization files for this plugin

 * so that it is ready for translation.

 *

 * @link       https://www.google.com/

 * @since      1.0.0

 *

 * @package    Mcc_Automated

 * @subpackage Mcc_Automated/includes

 */



/**

 * Define the internationalization functionality.

 *

 * Loads and defines the internationalization files for this plugin

 * so that it is ready for translation.

 *

 * @since      1.0.0

 * @package    Mcc_Automated

 * @subpackage Mcc_Automated/includes

 * @author     Validas LLC 

 */

class Mcc_Automated_i18n {





	/**

	 * Load the plugin text domain for translation.

	 *

	 * @since    1.0.0

	 */

	public function load_plugin_textdomain() {



		load_plugin_textdomain(

			'mcc-automated',

			false,

			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'

		);



	}







}

