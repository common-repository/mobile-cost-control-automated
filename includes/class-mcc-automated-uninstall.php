<?php

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's uninstall.
 *
 * @since      1.0.0
 * @package    Mcc_Automated
 * @subpackage Mcc_Automated/includes
 * @author     Validas LLC 
 */
class Mcc_Automated_Uninstall {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		delete_option( 'mcc_automated_greeting');
		delete_option( 'mcc_automated_first_heading');
		delete_option( 'mcc_automated_form_instructions');
		delete_option( 'mcc_automated_second_heading');
		delete_option( 'mcc_automated_total_cost_label');
		delete_option( 'mcc_automated_total_phone_count_label');
		delete_option( 'mcc_automated_savings_label');
		delete_option( 'mcc_automated_giga_usage_label');
		delete_option( 'mcc_automated_customer_name');
		delete_option( 'mcc_automated_customer_mail');
		delete_option( 'mcc_automated_notif_mail');
		delete_option( 'mcc_automated_form_notif_template');
		delete_option( 'mcc_automated_savings_formula_percent');
		delete_option( 'mcc_automated_form2_next_step_msg');
		 
		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'mcca' 				=> 'uninstall',
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

	/**
	 * Remove plugin tables on uninstall
	 */
	public static function dropPluginTable()
	{
		global $table_prefix, $wpdb;

		$tblname = 'verizon_rdd';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );
		
		$tblname = 'device_report';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'plan_categories';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'uit_vzw';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'mcc_automated';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );

		$tblname = 'att_rdd';
		$wp_table = $table_prefix . $tblname;
		$wpdb->query( "DROP TABLE IF EXISTS ".$wp_table );
		
		delete_option("mcc_automated_db_version");
	}
}
