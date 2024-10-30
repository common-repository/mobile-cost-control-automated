<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.google.com/
 * @since             1.0.0
 * @package           Mcc_Automated
 *
 * @wordpress-plugin
 * Plugin Name:       Mobile Cost Control Automated
 * Description:       Implement savings on wireless bills for business 						 and government agencies by extracting data from 						 electronic bills on a scalable basis.
 * Version:           1.2.8
 * Author:            Validas LLC
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mcc-automated
 * Domain Path:       /languages
 */

// If the file gets hit directly then abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



/*
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

define( 'MOBILE_COST_CONTROL_AUTOMATED_VERSION', '1.2.6' );

/*
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mcc-automated-activator.php
*/

function activate_mcc_automated() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mcc-automated-activator.php';
	Mcc_Automated_Activator::activate();
	Mcc_Automated_Activator::createPluginTable();
}

/*
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mcc-automated-deactivator.php
 */

function deactivate_mcc_automated() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mcc-automated-deactivator.php';
	Mcc_Automated_Deactivator::deactivate();
}

/*
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-mcc-automated-uninstall.php
*/

function uninstall_mcc_automated() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mcc-automated-uninstall.php';
	Mcc_Automated_Uninstall::dropPluginTable();
	Mcc_Automated_Uninstall::uninstall();
}

register_activation_hook( __FILE__,'activate_mcc_automated');
register_deactivation_hook( __FILE__,'deactivate_mcc_automated');
register_uninstall_hook( __FILE__,'uninstall_mcc_automated');

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mcc-automated.php';


/*
 * Class for handling zip files upload and extraction
*/
//require_once plugin_dir_path( __FILE__ ) . 'admin/class-mcc-automated-admin-zip-uploader.php';

require_once plugin_dir_path( __FILE__ ) . 'public/class-mcc-automated-public-zip-uploader.php';

/*
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mcc_automated() {

	$plugin = new Mcc_Automated();
	$plugin->run();

}
run_mcc_automated();


	
