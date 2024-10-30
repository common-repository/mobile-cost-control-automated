<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Mcc_Automated
 * @subpackage Mcc_Automated/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Mcc_Automated
 * @subpackage Mcc_Automated/includes
 * @author     Validas LLC 
 */
class Mcc_Automated_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'mcca' 				=> 'deactivate',
		);
		$result = wp_remote_post('https://wirelessbutlerserver.com/wp/wp-admin/admin-post.php', 
			array(
				'method' 		=> 'POST',
				'timeout'     	=> 45,
				'httpversion' 	=> '1.0',
				'sslverify' 	=> false,
				'body' 			=> $data
			)
		);
	}

}
