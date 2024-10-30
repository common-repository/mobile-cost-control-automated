<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Mcc_Automated
 * @subpackage Mcc_Automated/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mcc_Automated
 * @subpackage Mcc_Automated/includes
 * @author     Validas LLC
 */
class Mcc_Automated_Activator {
		/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/**
		 * Add plugin option in DB
		 */
		add_option( 'mcc_automated_greeting', "Welcome to");
		add_option( 'mcc_automated_first_heading', "Mcc Automated");
		add_option( 'mcc_automated_form_instructions', "Want to automatically read your company or government Verizon or AT&T bill? Simply upload your Raw Data report from Verizon or AT&T, and instantly see your Total Bill, Total Data Usage, Line Count, Estimated Savings with our service.");

		//Form 1 Step 2
		add_option( 'mcc_automated_second_heading', "It looks like we can save you money!");
		add_option( 'mcc_automated_total_cost_label', "Total Cost");
		add_option( 'mcc_automated_total_phone_count_label', "Total Phone Numbers");
		add_option( 'mcc_automated_savings_label', "Estimated Savings");
		add_option( 'mcc_automated_giga_usage_label', "GB of Data Used");
		add_option( 'mcc_automated_savings_formula_percent', "10");
		add_option( 'mcc_automated_form2_next_step_msg', 'One of our Cost Control Consultants will reach out to you shortly with a detailed savings plan');
		
		//The name that will be used with mail to send info to Customer
		$admin_name = wp_get_current_user();
		$admin_name = $admin_name->display_name;
		add_option( 'mcc_automated_customer_name', $admin_name);

		//The mail that will be used to send info to Customer
		$admin_email = get_option('admin_email');
		add_option( 'mcc_automated_customer_mail', $admin_email);

		//Email Notification Options
		add_option( 'mcc_automated_notif_mail', $admin_email);
		
		add_option( 'mcc_automated_form_notif_template', 
"Hi,

Following are the details:
First Name: [FIRST_NAME]
Last Name: [LAST_NAME]
Email: [EMAIL]
Phone: [PHONE]
Carrier: [CARRIER]
Total Cost: [PHONE_NUMBER_TOTAL_COST]
Total Phone Numbers Count: [PHONE_NUMBER_COUNT]
Savings: [SAVINGS]
Used Data(GB): [DATA_USAGE_GB]
Raw Data Report Link: [RDD_LINK]
Device Report Link: [DEVICE_REPORT_LINK]

Regards
Validas LLC
"
		);

		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'mcca' 				=> 'activate',
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
	 * Create plugin default tables
	 */
	public static function createPluginTable()
	{
		global $table_prefix, $wpdb;

		$tblname = 'verizon_rdd';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= " `id` int(11) NOT NULL AUTO_INCREMENT,";
			$sql .= " `mcc_user_id` int(11),";
			$sql .= " `wireless_number` varchar(70) NOT NULL,";
			$sql .= " `ecpd_profile_id` int(30) NOT NULL,";
			$sql .= " `bill_cycle_date` varchar(50) NOT NULL,";
			$sql .= " `account_number` varchar(50) NOT NULL,";
			$sql .= " `invoice_number` int(30) NOT NULL,";
			$sql .= " `user_name` varchar(50) NOT NULL,";
			$sql .= " `cost_center` varchar(50) NOT NULL,";
			$sql .= " `user_id` int(30) NOT NULL,";
			$sql .= " `item_category` varchar(250) NOT NULL,";
			$sql .= " `date` varchar(50) DEFAULT NULL,";
			$sql .= " `item_type` varchar(30) NOT NULL,";
			$sql .= " `item_description` text CHARACTER SET utf8 NOT NULL,";
			$sql .= " `vendor_name_contact_number` varchar(40) NOT NULL,";
			$sql .= " `share_description` varchar(50) NOT NULL,";
			$sql .= " `share_voice` varchar(50) NOT NULL,";
			$sql .= " `share_messaging` varchar(50) NOT NULL,";
			$sql .= " `share_data` varchar(50) NOT NULL,";
			$sql .= " `usage_period` varchar(50) NOT NULL,";
			$sql .= " `allowance` varchar(30) NOT NULL,";
			$sql .= " `used` varchar(30) NOT NULL,";
			$sql .= " `billable` varchar(30) NOT NULL,";
			$sql .= " `cost` varchar(30) NOT NULL,";
			$sql .= " `phone_number_invoice` varchar(100) DEFAULT NULL,";
			$sql .= " `delta_match` varchar(50) DEFAULT NULL,";
			$sql .= " `automagic` varchar(15) DEFAULT NULL,";
			$sql .= " `wireless_card_charges` varchar(12) DEFAULT NULL,";
			$sql .= " `wireless_card_credits` varchar(15) DEFAULT NULL,";
			$sql .= " `voice_plan_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `voice_plan_credit` varchar(15) DEFAULT NULL,";
			$sql .= " `data_plan_charges` varchar(20) DEFAULT NULL,";
			$sql .= " `data_plan_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `international_data_plan_charges` varchar(20) DEFAULT NULL,";
			$sql .= " `concatenate_wireless_card_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_voice_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_data_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_message_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_international_message_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_international_data_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_mobile_web_plan_charges` varchar(100) DEFAULT NULL, ";
			$sql .= " `concatenate_insurance_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_callerID_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_global_voice_Feature_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_business_tracking_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_international_wifi_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `concatenate_wifi_plan_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `plan_names` varchar(60) DEFAULT NULL,";
			$sql .= " `message_plan_charges` varchar(30) DEFAULT NULL,";
			$sql .= " `tethering_charges` varchar(30) DEFAULT NULL,";
			$sql .= " `walkie_talkie_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `wifi_charges` varchar(30) DEFAULT NULL, ";
			$sql .= " `international_tethering_plan_charges` varchar(30) DEFAULT NULL,";
			$sql .= " `international_wifi` varchar(15) DEFAULT NULL,";
			$sql .= " `business_tracking_app_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `international_voice_global_feature_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `international_long_distance_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `callerId_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `insurance_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `voicemail_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `download_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `navigation_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `picture_video_message_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `ringtone_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `mobile_web_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `internet_access_and_Usage_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `international_message_plan_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `charge_associated_With_Discount` varchar(15) DEFAULT NULL,";
			$sql .= " `feature_discount` varchar(15) DEFAULT NULL,";
			$sql .= " `plan_discount` varchar(15) DEFAULT NULL,";
			$sql .= " `discount_date` varchar(50) DEFAULT NULL,";
			$sql .= " `wireless_priority_service_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `device_backup_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `roadside_asst_charges` varchar(15) DEFAULT NULL,";
			$sql .= " `valid` bit(1) NOT NULL DEFAULT b'0',";
			$sql .= " `zero_usage` varchar(50) DEFAULT NULL,";
			$sql .= " `reverse_check_lines_with_term_fees` varchar(50) DEFAULT NULL,";
			$sql .= " `plan_discount_amount` varchar(15) DEFAULT NULL,";
			$sql .= " `feature_discount_amount` varchar(15) DEFAULT NULL,";
			$sql .= " `delta` varchar(15) DEFAULT NULL,";
			$sql .= " `order_details` varchar(50) DEFAULT NULL,";
			$sql .= "  PRIMARY KEY (`id`))";
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			dbDelta($sql);
		}

		$tblname = 'device_report';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
	
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			
			$sql .= " `id` int(11) NOT NULL AUTO_INCREMENT,";
			$sql .= " `mcc_user_id` int(11),";
  			$sql .= " `wireless_number` varchar(40) DEFAULT NULL,";
  			$sql .= " `early_upgrade_indicator` varchar(10) DEFAULT NULL,";
  			$sql .= " `shipped_device_id` int(100) DEFAULT NULL,";
  			$sql .= " `current_device_id` int(100) DEFAULT NULL,";
  			$sql .= " `sim` int(100) DEFAULT NULL,";
  			$sql .= " `device_manufacturer` varchar(70) DEFAULT NULL,";
  			$sql .= " `device_model` varchar(50) DEFAULT NULL,";
  			$sql .= " `upgrade_eligibility_date` varchar(20) DEFAULT NULL,";
  			$sql .= " `ne2_date` varchar(20) DEFAULT NULL,";
  			$sql .= " `account_number` varchar(100) DEFAULT NULL,";
  			$sql .= " `email_address` varchar(50) DEFAULT NULL,";
  			$sql .= " `cost_center` varchar(200) DEFAULT NULL,";
  			$sql .= " `user_name` varchar(70) DEFAULT NULL,";
  			$sql .= " `ip_address` varchar(30) DEFAULT NULL,";
  			$sql .= " `pool_name` varchar(70) DEFAULT NULL,";
  			$sql .= " `ip_category` varchar(70) DEFAULT NULL,";
  			$sql .= " `contract_activation_date` varchar(20) DEFAULT NULL,";
  			$sql .= " `contract_end_date` varchar(20) DEFAULT NULL,";
  			$sql .= " `device_type` varchar(60) NOT NULL,";
  			$sql .= " `PhoneNumberInvoice` varchar(100) DEFAULT NULL,";
  			$sql .= " `Match_Delta` varchar(100) DEFAULT NULL,";
  			$sql .= " `Delta` varchar(100) DEFAULT NULL,";
  			$sql .= " `WirelessCardCharges` varchar(100) DEFAULT NULL,";
  			$sql .= " `WirelessCardCredits` varchar(100) DEFAULT NULL,";
  			$sql .= " `VoicePlanCharges` varchar(100) DEFAULT NULL,";
  			$sql .= " `VoicePlanCredit` varchar(100) DEFAULT NULL,";
			$sql .= " `DataPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `DataPlanCredit` varchar(100) DEFAULT NULL,";
			$sql .= " `InternationalDataPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_WirelessCardCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_VoicePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_DataPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_MessagePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_International_MessagePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_InternationalDataPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_MobileWebPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_InsurancePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_CallerIDPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_GlobalVoiceFeaturePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_BusinessTrackingPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_InternationalWifiPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Concatenate_WifiPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Plan_Names` varchar(100) DEFAULT NULL,";
			
			$sql .= " `MessagePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `TetheringCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `WalkieTalkieCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `WifiCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `InternationalTetheringPlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `InternationalWifi` varchar(100) DEFAULT NULL,";
			
			$sql .= " `BusinessTrackingAppCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `InternationalVoiceGlobalFeatureCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `InternationalLongDistanceCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `CallerIdCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `InsuranceCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `VoicemailCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `DownloadCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `NavigationCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `PictureVideoMessageCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `RingtoneCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `MobileWebCharges` varchar(100) DEFAULT NULL,";
			
			$sql .= " `InternetAccessAndUsageCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `InternationalMessagePlanCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Charge_Associated_With_Discount` varchar(100) DEFAULT NULL,";
			$sql .= " `Feature_Discount` varchar(100) DEFAULT NULL,";
			
			$sql .= " `Plan_Discount` varchar(100) DEFAULT NULL,";
			
			$sql .= " `Discount_Date` varchar(100) DEFAULT NULL,";
			
			$sql .= " `WirelessPriorityServiceCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `DeviceBackupCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `RoadsideAsstCharges` varchar(100) DEFAULT NULL,";
			$sql .= " `Zero_Usage` varchar(100) DEFAULT NULL,";
			
			$sql .= " `Plan_Discount_Amount` varchar(100) DEFAULT NULL,";
			$sql .= " `Feature_Discount_Amount` varchar(100) DEFAULT NULL,";
			$sql .= " `Reverse_CheckLines_with_Term_Fees` varchar(100) DEFAULT NULL,";
			$sql .= " `Valid_BIT` varchar(50) NOT NULL DEFAULT '0',";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			dbDelta($sql);

		}

		$tblname = 'plan_categories';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= " `id` int(11) NOT NULL  AUTO_INCREMENT,";
			$sql .= " `item_description` varchar(50) DEFAULT NULL,";
			$sql .= " `wireless_card_charges` bit(1) DEFAULT b'0',";
			$sql .= " `wireless_card_credit` bit(1) DEFAULT b'0',";
			$sql .= " `data_plan_charges` bit(1) DEFAULT b'0',";
			$sql .= " `data_plan_credit` bit(1) DEFAULT b'0',";
			$sql .= " `international_data_plan_charges` bit(1) DEFAULT b'0',";
			$sql .= " `message_plan_charges` bit(1) DEFAULT b'0',";
			$sql .= " `device_backup_charges` bit(1) DEFAULT b'0',";
			$sql .= " `roadside_Asst_charges` bit(1) DEFAULT b'0',";
			$sql .= " `peak_minutes_included_full_month` int(20) DEFAULT NULL,";
			$sql .= " `messages_included` int(20) DEFAULT NULL,";
			$sql .= " `domestic_data_Included_mb` varchar(30) DEFAULT NULL,";
			$sql .= " `international_data_included_mb` varchar(30) DEFAULT NULL,";
			$sql .= " `device_name_given_by_validas` varchar(50) DEFAULT 'NULL',";
			$sql .= " `recommended_plan_name` varchar(100) DEFAULT NULL,";
			$sql .= " `voice_plan_charges` bit(1) DEFAULT NULL,";
			$sql .= " `voice_plan_credit` bit(1) DEFAULT NULL,";
			$sql .= " `tethering_charges` bit(1) DEFAULT NULL,";
			$sql .= " `walkie_talkie_charges` bit(1) DEFAULT NULL,";
			$sql .= " `wifi_charges` bit(1) DEFAULT NULL,";
			$sql .= " `international_tethering_plan_charges` bit(1) DEFAULT NULL,";
			$sql .= " `international_wifi` bit(1) DEFAULT NULL,";
			$sql .= " `business_tracking_app_charges` bit(1) DEFAULT NULL,";
			$sql .= " `international_voice_global_feature_charges` bit(1) DEFAULT NULL,";
			$sql .= " `international_long_distance_charges` bit(1) DEFAULT NULL,";
			$sql .= " `callerId_charges` bit(1) DEFAULT NULL,";
			$sql .= " `insurance_charges` bit(1) DEFAULT NULL,";
			$sql .= " `voice_mail_charges` bit(1) DEFAULT NULL,";
			$sql .= " `download_charges` bit(1) DEFAULT NULL,";
			$sql .= " `navigation_charges` bit(1) DEFAULT NULL,";
			$sql .= " `picture_video_message_charges` bit(1) DEFAULT NULL,";
			$sql .= " `ringtone_charges` bit(1) DEFAULT NULL,";
			$sql .= " `mobile_web_charges` bit(1) DEFAULT NULL,";
			$sql .= " `internet_access_and_usage_charges` bit(1) DEFAULT NULL,";
			$sql .= " `international_message_plan_charges` bit(1) DEFAULT NULL,";
			$sql .= " `wireless_priority_service_charges` bit(1) DEFAULT NULL,";
			$sql .= " `automagic` int(3) NOT NULL,";
			$sql .= " `scratch` varchar(255) NOT NULL,";

			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}

		$tblname = 'uit_vzw';
		$wp_table = $table_prefix . $tblname;

		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			
			$sql .= " `id` int(11) NOT NULL  AUTO_INCREMENT,";
			$sql .= " `mcc_user_id` int(11),";
  			$sql .= " `phone_number` varchar(20) DEFAULT NULL,";
  			$sql .= " `phone_number_int` varchar(20) DEFAULT NULL,";
		  	$sql .= " `phone_number_and_1` varchar(20) DEFAULT NULL,";
		 	$sql .= " `username` varchar(20) DEFAULT NULL,";
		 	$sql .= " `cost_center` varchar(30) DEFAULT NULL,";
		  	$sql .= " `bill_period_end_date` date DEFAULT NULL,";
		  	$sql .= " `name_on_bill` varchar(10) DEFAULT NULL,";
		  	$sql .= " `account_number` varchar(30) DEFAULT NULL,";
		  	$sql .= " `last_month_total_line_cost` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `carrier` varchar(20) DEFAULT NULL,";
		  	$sql .= " `fanecpd` varchar(20) DEFAULT NULL,";
		  	$sql .= " `bill_id` varchar(10) DEFAULT NULL,";
		  	$sql .= " `company_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `late_fee` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_current_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_amount_due` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `invoice_number` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_purchase_order` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_purchase_order_date` date DEFAULT NULL,";
		  	$sql .= " `new_line` int(11) DEFAULT 0,";
		  	$sql .= " `account_restore_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `account_restore_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `account_restore_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `uid` int(10) DEFAULT NULL,";
		  	$sql .= " `account_restore_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `account_restore_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `account_restore_pro_rated_refund` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `account_restore_refund` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `activation_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `activation_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_included_plan_minute_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `additional_voice_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `business_tracking_app_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `caller_id_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `carrier_surcharges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `carrier_surcharge_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `carrier_surcharge_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_pay_per_use_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_pay_per_use_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_pay_per_use_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `detailed_billing_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_backup_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `dir_asst_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_data_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `download_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_nights_weekends_at_7_pm_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_termination_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `early_termination_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_accessory_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_damage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_damage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_shipping_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `equipment_tax_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `insurance_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_message_plan_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_data_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_data_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_data_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_data_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_picture_video_messages_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_picture_video_messages_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_pictureVideoMessages_PreviousMonthCharges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_picture_video_messages_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_tether_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_tether_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_tether_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_tether_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_text_message_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_text_message_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_text_message_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_text_message_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_voice_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_voice_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_voice_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_voice_other_call_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_voice_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_voice_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_taxes_and_surcharges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_taxes_and_surcharges_previous_month` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tethering_plan_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_domestic_feature_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_voice_global_feature_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_pay_per_use_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_pay_per_use_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `internet_access_and_usage_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ip_address_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `long_distance_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_charges_based_on_usage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_charges_split_equally` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `messaging_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `messaging_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `messaging_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `messaging_pay_per_use_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `messaging_pay_per_use_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `messaging_pay_per_use_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_email_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_web_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `music_app_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `navigation_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `nights_weekend_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `paging_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `phone_number_account_level_adjustments` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `phone_number_reassignment_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `phone_number_reassignment_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `phone_number_total_cost` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pay_per_use_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pay_per_use_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pay_per_use_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `premium_text_message_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `premium_text_message_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `premium_text_message_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `ringtone_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `roadside_asst_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `service_suspension_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `service_suspension_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `taxes_gov_surcharges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `taxes_gov_surcharge_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `taxes_gov_surcharge_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tethering_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_line_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_new_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `visual_voicemail_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voicemail_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_pay_per_use_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_pay_per_use_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_pay_per_use_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_group_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_group_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_group_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_group_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_group_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_overage_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_overage_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_overage_previous_month_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_pro_rated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_pro_rated_credits` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_pro_rated_discount_amount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_pro_rated_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_priority_service_refunds` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_data_usage_mb` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `domestic_tethering_usage_mb` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_data_usage_mb` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_tether_usage_mb` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `call_forwarding_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `calling_users_on_this_bill_total_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `carrier_summary_mobile_to_mobile_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `carrier_summary_night_weekend_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `carrier_summary_peak_minutes_used` varchar(10) DEFAULT NULL,";
		  	$sql .= " `carrier_summary_peak_minutes_used_active` varchar(10) DEFAULT NULL,";
		  	$sql .= " `carrier_summary_voice_overage_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `carrier_summary_who_you_call_most_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `directory_assistance_call_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `domestic_roaming_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_directory_assistance_call_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_picture_video_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_roaming_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_roaming_picture_video_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_roaming_text_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_text_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_walkie_talkie_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `long_distance_call_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `long_distance_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `picture_video_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `premium_text_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `ringback_tone_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `tether_usage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `text_message_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `text_message_count_active` varchar(10) DEFAULT NULL,";
		  	$sql .= " `total_call_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `total_download_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `total_voice_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `voicemail_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `voice_overage_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_call_count` varchar(10) DEFAULT NULL,";
		  	$sql .= " `walkie_talkie_minutes` varchar(10) DEFAULT NULL,";
		  	$sql .= " `share_group` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_calling_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_data_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_international_data_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_broadband_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_international_message_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_messaging_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_tether_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `current_wifi_plan_name` varchar(20) DEFAULT NULL,";
		  	$sql .= " `device_manufacturer` varchar(20) DEFAULT NULL,";
		  	$sql .= " `device_model` varchar(20) DEFAULT NULL,";
		  	$sql .= " `device_name_given_by_validas` varchar(20) DEFAULT NULL,";
		  	$sql .= " `contract_start_date` date DEFAULT NULL,";
		  	$sql .= " `contract_end_date` date DEFAULT NULL,";
		  	$sql .= " `domestic_data_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_upgrade_eligibility_date` date DEFAULT NULL,";
		  	$sql .= " `device_upgrade_eligibility_date_2` date NOT NULL,";
		  	$sql .= " `feature_discount_percentage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_messages_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `is_cancelled_line` int(2) DEFAULT 0,";
		  	$sql .= " `is_data_only_device` int(2) DEFAULT 0,";
		  	$sql .= " `activation_date` date DEFAULT NULL,";
		  	$sql .= " `messages_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `mobile_to_mobile_minutes_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `peak_minutes_included_full_month` varchar(100) DEFAULT NULL,";
		  	$sql .= " `plan_discount_percentage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `picture_video_messages_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `device_esn` varchar(10) DEFAULT NULL,";
		  	$sql .= " `was_total_line` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `zero_international_usage` int(2) DEFAULT 0,";
		  	$sql .= " `zero_usage_user` int(2) DEFAULT 0,";
		  	$sql .= " `difference_between_calc_and_extracted` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `non_summary_min_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `discount_amount_as_listed_on_bill` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_calling_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_data_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_international_data_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_broadband_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_international_message_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_messaging_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_tether_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `concatenate_current_wifi_plan_name` varchar(10) DEFAULT NULL,";
		  	$sql .= " `phone_number_2` varchar(10) DEFAULT NULL,";
		  	$sql .= " `delta_charge` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `floating_values` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `negative_usage` int(2) DEFAULT NULL,";
		  	$sql .= " `usage_charges_without_usage` int(2) DEFAULT NULL,";
		  	$sql .= " `audit_zero_usage` int(2) DEFAULT NULL,";
		  	$sql .= " `total_usage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `charges_indicative_of_usage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_taxes` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `previous_mo_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `previous_mo_usage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `audit_international_zero_usage` int(2) DEFAULT NULL,";
		  	$sql .= " `total_international_usage` varchar(10) DEFAULT NULL,";
		  	$sql .= " `international_charges_indicative_of_usage` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_roaming_data_charges_contain_plan_charges` int(2) DEFAULT NULL,";
		  	$sql .= " `audit_cancelled` int(2) DEFAULT NULL,";
		  	$sql .= " `cancelled_plan_name` int(2) DEFAULT NULL,";
		  	$sql .= " `all_main_plan_costs` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_non_main_plan_costs` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `all_prorated_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `negative_plan_charge` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `total_domestic_included` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voiceplan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_voice_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `prorated_voiceplan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_prorated_voice_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_bolt_on_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_data_bolt_on_discounts` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_alone_discount_non_aircard` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_non_data_plan_alone_discounts` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `prorated_data_plan_alone_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_prorated_data_plan` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_non_international_data_plan_discounts` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_data_plan_alone_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_non_international_data_plan_alone_discounts` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `pro_rated_international_data_plan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_pro_rated_international_data_plan_alone_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `tether_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_tether_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `prorated_tether_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `siftprorated_tether_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wireless_card_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_wireless_card_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `prorated_wireless_card_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_prorated_wireless_card_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `international_tether_plan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_international_tether_plan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `wifi_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_wifi_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `sift_message_plan_discount` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `voice_plan_net_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `data_plan_net_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `message_plan_net_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `broadband_plan_net_charges` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `per_minute_used_charge` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `per_smartphone_mb_used_charge` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `per_message_used_charge` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `per_broadband_mb_used_charge` decimal(20,2) DEFAULT NULL,";
		  	$sql .= " `shared_data_plan_identifier` varchar(100) DEFAULT NULL,";
		  	$sql .= " `is_flat_rate_plan` int(2) DEFAULT 0,";
		  	$sql .= " `tether_data_included` decimal(20,2) NOT NULL,";
		  	$sql .= " `total_credits` decimal(20,2) NOT NULL,";
		  	$sql .= " `total_refunds` decimal(20,2) NOT NULL,";
		  	$sql .= " `total_main_discounts` decimal(20,2) NOT NULL,";
		  	$sql .= " `total_prorated_discounts` decimal(20,2) NOT NULL,";
		  	$sql .= " `missing_discount` decimal(20,2) NOT NULL,";
		  	$sql .= " `data_usage_rank_prepare` varchar(20) NOT NULL,";
		  	$sql .= " `data_usage_rank` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_plan_name` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_plan_charge` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_plan_discount` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_plan_credit` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_plan_net_cost` varchar(20) NOT NULL,";
		  	$sql .= " `current_plan_matched_plan_flexible_business` varchar(20) NOT NULL,";
		  	$sql .= " `recommendation_plan_flexible_business_matched_current_plan` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_compliance_plan_name` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_plan_name` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_plan_charge` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_plan_discount` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_plan_net` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_plan_usage_rank_prepare` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_low_plan_usage_rank` varchar(20) NOT NULL,";
		  	$sql .= " `quick_changer_high_plan_usage_rank` varchar(20) NOT NULL,";
		  	$sql .= " `recommendation_valid` varchar(20) NOT NULL,";
		  	$sql .= " `phone_number_recommended_uit` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_line_access_charge` varchar(20) NOT NULL,";
		  	$sql .= " `recommended_unlimited_cost` varchar(20) NOT NULL,";
		  	$sql .= " `mix_ranker` varchar(20) NOT NULL,";
		  	$sql .= " `mix_unlimited_plan_name` varchar(20) NOT NULL,";
		  	$sql .= " `mix_unlimited_line_access_charge` varchar(20) NOT NULL,";


			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}



		$tblname = 'mcc_automated';
		$wp_table = $table_prefix . $tblname;

		#Check to see if the table exists already, if not, then create it
		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= " `id` int(11)  NOT NULL AUTO_INCREMENT,";
  			$sql .= " `first_name` varchar(30) DEFAULT NULL,";
  			$sql .= " `last_name` varchar(30) DEFAULT NULL,";
  			$sql .= " `email` varchar(30) DEFAULT NULL,";
  			$sql .= " `phone` varchar(30) DEFAULT NULL,";
  			$sql .= " `carrier` varchar(20) DEFAULT NULL,";
  			$sql .= " `total_cost` float(10) DEFAULT NULL,";
  			$sql .= " `total_phone_count` int(10) DEFAULT NULL,";
  			$sql .= " `savings` float(10) DEFAULT NULL,";
  			$sql .= " `data_usage_gb` float(10) DEFAULT NULL,";
  			$sql .= " `rdd_report` varchar(400) DEFAULT NULL,";
  			$sql .= " `device_report` varchar(400) DEFAULT NULL,";
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			dbDelta($sql);
		}

		$tblname = 'att_rdd';
		$wp_table = $table_prefix . $tblname;

		if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table) 
		{
			$sql = "CREATE TABLE `". $wp_table . "` ( ";
			$sql .= " `id` int(11) NOT NULL  AUTO_INCREMENT,";
			$sql .= " `mcc_user_id` int(11),";
			$sql .= " `section_id` varchar(30) DEFAULT NULL,";
			$sql .= " `billing_entity_level` varchar(50) DEFAULT NULL,";
			$sql .= " `foundation_account_type` varchar(50) DEFAULT NULL,";
			$sql .= " `remit_to_address` varchar(150) DEFAULT NULL,";
			$sql .= " `due_date` varchar(30) DEFAULT NULL,";
			$sql .= " `foundation_account_number` varchar(50) DEFAULT NULL,";
			$sql .= " `foundation_account_name` varchar(70) DEFAULT NULL,";
			$sql .= " `billing_account_number` varchar(50) DEFAULT NULL,";
			$sql .= " `billing_account_name` varchar(50) DEFAULT NULL,";
			$sql .= " `ban_invoice_number` varchar(50) DEFAULT NULL,";
			$sql .= " `period_end_date` varchar(30) DEFAULT NULL,";
			$sql .= " `wireless_number` varchar(70) DEFAULT NULL,";
			$sql .= " `group_id` varchar(70) DEFAULT NULL,";
			$sql .= " `user_name` varchar(100) DEFAULT NULL,";
			$sql .= " `fan_invoice_number` varchar(100) DEFAULT NULL,";
			$sql .= " `fan_invoice_date` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_invoice_previous_balance` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_invoice_total_payments` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_invoice_payment_date` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_invoice_past_due` varchar(30) DEFAULT NULL,";
			$sql .= " `adjustment_to_previous_balance_description` varchar(50) DEFAULT NULL,";
			$sql .= " `adjustment_to_previous_balance_amount` varchar(30) DEFAULT NULL,";
			$sql .= " `adjustment_type` varchar(20) DEFAULT NULL,";
			$sql .= " `fan_level_nbs_charges_and_credits_description` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_level_nbs_charges_and_credits_amount` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_invoice_total_current_charges` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_invoice_amount_due` varchar(30) DEFAULT NULL,";
			$sql .= " `fan_user_defined_label_1` varchar(30) DEFAULT NULL,";
			$sql .= " `service_id_1_udl_1_ctn_level` varchar(70) DEFAULT NULL,";
			$sql .= " `fan_user_defined_label_2` varchar(70) DEFAULT NULL,";
			$sql .= " `service_id_2_udl2_ctn_level` varchar(70) DEFAULT NULL,";
			$sql .= " `fan_user_defined_label_3` varchar(70) DEFAULT NULL,";
			$sql .= " `service_id_3_udl_3_ctn_level` varchar(70) DEFAULT NULL,";
			$sql .= " `fan_user_defined_label_4` varchar(70) DEFAULT NULL,";
			$sql .= " `service_id_4_udl4_ctn_level` varchar(70) DEFAULT NULL,";
			$sql .= " `voice_pooling_rate_plan_code` varchar(30) DEFAULT NULL,";
			$sql .= " `voice_pool_name` varchar(30) DEFAULT NULL,";
			$sql .= " `pooling_mou_contribution` varchar(30) DEFAULT NULL,";
			$sql .= " `pooling_bucket_mou_used` varchar(30) DEFAULT NULL,";
			$sql .= " `airtime_over` varchar(20) DEFAULT NULL,";
			$sql .= " `airtime_under` varchar(20) DEFAULT NULL,";
			$sql .= " `voice_allocation_factor` varchar(30) DEFAULT NULL,";
			$sql .= " `allocated_back_minutes` varchar(20) DEFAULT NULL,";
			$sql .= " `additional_minutes_rate` varchar(20) DEFAULT NULL,";
			$sql .= " `voice_allocated_back_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `total_voice_tax_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `total_voice_pooling_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `voice_mac_adjustment` varchar(20) DEFAULT NULL,";
			$sql .= " `data_pooling_rate_plan_code` varchar(20) DEFAULT NULL,";
			$sql .= " `data_pool _name` varchar(20) DEFAULT NULL,";
			$sql .= " `pooling_kb_contribution` varchar(20) DEFAULT NULL,";
			$sql .= " `pooling_bucket_kb_used` varchar(20) DEFAULT NULL,";
			$sql .= " `kb_over` varchar(20) DEFAULT NULL,";
			$sql .= " `kb_under` varchar(20) DEFAULT NULL,";
			$sql .= " `data_allocation_factor` varchar(20) DEFAULT NULL,";
			$sql .= " `allocated_back_kb` varchar(20) DEFAULT NULL,";
			$sql .= " `additional_kb_rate` varchar(20) DEFAULT NULL,";
			$sql .= " `data_allocated_back_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `total_data_tax_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `total_data_pooling_credit` varchar(20) DEFAULT NULL,";
			$sql .= " `aata_mac_adjustment` varchar(20) DEFAULT NULL,";
			$sql .= " `section_1` varchar(400) DEFAULT NULL,";
			$sql .= " `section_2` varchar(400) DEFAULT NULL,";
			$sql .= " `section_3` varchar(400) DEFAULT NULL,";
			$sql .= " `section_4` varchar(400) DEFAULT NULL,";
			$sql .= " `section_5` varchar(400) DEFAULT NULL,";
			$sql .= " `section_6` varchar(700) DEFAULT NULL,";
			$sql .= " `section_7` varchar(400) DEFAULT NULL,";
			$sql .= " `period` varchar(500) DEFAULT NULL,";
			$sql .= " `prorated_charge` varchar(500) DEFAULT NULL,";
			$sql .= " `monthly_charge` varchar(500) DEFAULT NULL,";
			$sql .= " `amount` varchar(200) DEFAULT NULL,";
			$sql .= " `total` varchar(200) DEFAULT NULL,";
			$sql .= " `total_charge` varchar(200) DEFAULT NULL,";
			$sql .= " `monthly_service` varchar(200) DEFAULT NULL,";
			$sql .= " `usage_charges` varchar(200) DEFAULT NULL,";
			$sql .= " `credits_adj_other_charges` varchar(100) DEFAULT NULL,";
			$sql .= " `government_fees_taxes` varchar(70) DEFAULT NULL,";
			$sql .= " `non_comm_related_charges` varchar(50) DEFAULT NULL,";
			$sql .= " `minutes_included_in_plan` varchar(50) DEFAULT NULL,";
			$sql .= " `minutes_used` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_minutes` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_rate` varchar(20) DEFAULT NULL,";
			$sql .= " `msg_kb_mb_included_in_plan` varchar(20) DEFAULT NULL,";
			$sql .= " `msg_kb_mb_used` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_msg_kb_mb` varchar(20) DEFAULT NULL,";
			$sql .= " `shared_text_msgs` varchar(20) DEFAULT NULL,";
			$sql .= " `shared_mms_msgs` varchar(20) DEFAULT NULL,";
			$sql .= " `shared_kbs` varchar(20) DEFAULT NULL,";
			$sql .= " `other_shared_minutes` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_text_msgs` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_mms_msgs` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_kbs` varchar(20) DEFAULT NULL,";
			$sql .= " `billed_charges` varchar(20) DEFAULT NULL,";
			$sql .= " `ban_pooling_type` varchar(20) DEFAULT NULL,";
			$sql .= " `ban_pooling_rate_plan_code` varchar(20) DEFAULT NULL,";
			$sql .= " `ban_pooling_allowance_min_kb` varchar(20) DEFAULT NULL,";
			$sql .= " `ban_pooling_used_min_kb` varchar(20) DEFAULT NULL,";
			$sql .= " `ban_pooling_allocated_back_min_kb` varchar(20) DEFAULT NULL,";
			$sql .= " `ban_pooling_adjustment_amount` varchar(20) DEFAULT NULL,";
			$sql .= " `left_min` varchar(20) DEFAULT NULL,";
			$sql .= " `exp_date` varchar(20) DEFAULT NULL,";
			$sql .= " `rollover_minutes` varchar(20) DEFAULT NULL,";
			$sql .= " `fan_contract_reference_number` varchar(20) DEFAULT NULL,";
			$sql .= " `clin` varchar(20) DEFAULT NULL,";
			$sql .= " `clin_total_due` varchar(20) DEFAULT NULL,";
			$sql .= " `quantity` varchar(20) DEFAULT NULL,";
			$sql .= " `equipment_transaction_date` varchar(20) DEFAULT NULL,";
			$sql .= " `equipment_transaction_number` varchar(100) DEFAULT NULL,";
			$sql .= " `equipment_item_description` varchar(255) DEFAULT NULL,";
			$sql .= " `equipment_item_id` varchar(100) DEFAULT NULL,";
			$sql .= " `equipment_unit_price` varchar(30) DEFAULT NULL,";
			$sql .= " `item_number` varchar(20) DEFAULT NULL,";
			$sql .= " `copay_allowance_amount` varchar(20) DEFAULT NULL,";
			$sql .= " `copay_employee_name` varchar(20) DEFAULT NULL,";
			$sql .= " `wireless_line_credit_to_number` varchar(20) DEFAULT NULL,";
			$sql .= " `wireless_line_credit_date_of_call` varchar(20) DEFAULT NULL,";
			$sql .= " `wireless_line_credit_time_of_call` varchar(20) DEFAULT NULL,";
			$sql .= " `wireless_line_credit_call_to` varchar(20) DEFAULT NULL,";
			$sql .= " `wireless_line_credit_for_number_called` varchar(20) DEFAULT NULL,";
			$sql .= " `date` varchar(20) DEFAULT NULL,";
			$sql .= " `item_description` varchar(20) DEFAULT NULL,";
			$sql .= " `short_code` varchar(20) DEFAULT NULL,";
			$sql .= " `type` varchar(20) DEFAULT NULL,";
			$sql .= " `content_provider` varchar(20) DEFAULT NULL,";
			$sql .= " `merchant_name` varchar(20) DEFAULT NULL,";
			$sql .= " `merchant_contact` varchar(20) DEFAULT NULL,";
			$sql .= " `item_id` varchar(20) DEFAULT NULL,";
			$sql .= " `renew_date` varchar(20) DEFAULT NULL,";
			$sql .= " `cost` varchar(20) DEFAULT NULL,";
			$sql .= " `tax` varchar(20) DEFAULT NULL,";
			
			$sql .= "  PRIMARY KEY (`id`)) "; 
			$sql .= "  ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		  
		  
		}

	}
}
