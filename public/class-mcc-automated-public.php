<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Mcc-Automated
 * @subpackage Mcc-Automated/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mcc-Automated
 * @subpackage Mcc-Automated/public
 * @author     Validas LLC 
 */
class Mcc_Automated_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mcc-Automated_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mcc-Automated_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mcc-automated-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-dropzone', plugin_dir_url( __FILE__ ) . 'css/dropzone.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mcc-Automated_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mcc-Automated_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mcc-automated-public.js', array( 'jquery' ), $this->version, false );
		
		wp_enqueue_script( $this->plugin_name.'-dropzone', plugin_dir_url( __FILE__ ) . 'js/dropzone.min.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, "mccAutomatedObj", array( 'ajaxurl' => admin_url( 'admin-post.php' ) ));

	}
	
	/**
	 * Plugin initlization function
	 */
	public function plugin_init( ) {
		add_shortcode('mcc_automated_form_1', array($this, 'mcc_automated_form_1_func') );
		
		add_action('admin_post_nopriv_mcc_automated_form_1_step_1', array($this, 'handle_form_1_step_1_submit') );
		
		add_action('admin_post_mcc_automated_form_1_step_1', array($this, 'handle_form_1_step_1_submit') );

		add_action('admin_post_nopriv_mcc_automated_form_1_step_2', array($this, 'handle_form_1_step_2_submit') );
		
		add_action('admin_post_mcc_automated_form_1_step_2', array($this, 'handle_form_1_step_2_submit') );

		add_action( 'upgrader_process_complete', array($this, 'plugin_upgrader_process_complete'), 10, 2 );
	}

	function plugin_upgrader_process_complete( $upgrader_object, $options ) {
		$pluginUpdated = false;

		if ( isset( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $index => $plugin ) {
				if ( 'mobile-cost-control-automated/mcc-automated.php' === $plugin ) {
					$pluginUpdated = true;
					break;
				}
			}
		}

		if ( ! $pluginUpdated ) {
			return;
		}

		// Do something when plugin has been updated.
		$data = array(
			'action' 			=> 'plugin_activation_sync',
			'sourceDomain' 		=> get_site_url(),
			'mcca' 				=> 'updated',
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

	/*
	 * Frontend form shortcode function
	*/
	function mcc_automated_form_1_func( $atts ) {
		$this->enqueue_styles();
		$this->enqueue_scripts();
		include_once plugin_dir_path( __FILE__ ) . 'partials/mcc-automated-form-1.php';
	}

	/**
	 * Handle form submit
	 */
	function handle_form_1_step_1_submit($data) {

		global $table_prefix, $wpdb;

		global $wp_filesystem;

		$data = array(
			'carrier' 	=> sanitize_text_field($_POST['carrier']),
			'fname' 	=> sanitize_text_field($_POST['fname']),
			'lname' 	=> sanitize_text_field($_POST['lname']),
			'email' 	=> sanitize_text_field($_POST['email']),
			'phone' 	=> sanitize_text_field($_POST['phone']),
		);

		$file_urls = array();

		$billFileURL = '';
		$upload_dir = wp_upload_dir();

		$message = '';
		$tab = "\t";
 		$dir = 'mcc-automated';
		$carrier = $data['carrier']; 

        if ( ! empty( $upload_dir['basedir'] ) ) {
            $dirname = $upload_dir['basedir'].'/'.$dir;
            if ( ! file_exists( $dirname ) ) {
                wp_mkdir_p( $dirname );
            }
 
			$countfiles = count($_FILES);

			$allow_upload = 0;

			//For Verizon and AT&T we need to upload atleast 1 file
			
			if($countfiles>=1){
				
				$mcc_userid = self::insert_mcc_user($data);

				foreach($_FILES as $file){

					$file_name = wp_unique_filename( $dirname, $file['name'] );

					
					$fileExt = explode(".", $file_name)[1];

					if($fileExt == "zip")
	            	{
	            		
	            		$tblname = 'verizon_rdd';
	            		$wp_table = $table_prefix . $tblname;
	            		
						$uploader = new Mcc_Automated_Public_Zip_Uploader($dir);

						$file_data['file'] = $file;

						$result = $uploader->upload($file_data);
						
						if ( is_wp_error( $result ) ) {
							$this->errors->add( $result->get_error_code(), $result->get_error_message() );
						}

						$this->notices[] = 'Uploaded! Path: ' . $result;

						$temp_dir = basename($result['zip_file'], '.zip');

						$dir_file = list_files($temp_dir);

						//get only required file from unziped files
						$matches = preg_grep("/\bWireless Charges Detail Summary\b/i", $dir_file);

						$matches = implode("", $matches);

						$zip_pckg = explode("/", $result['zip_file']);
						
						$zip_pckg = end($zip_pckg);
						
						$file_urls['verizon_rdd'] = $result['zip_file_url'];

						$handle = fopen($matches, "r");
						


						$row=1;
						while(!feof($handle) )
			            {
							$line = fgets($handle, 2048);
			            	//print_r($line);   
			            	$data = str_getcsv($line, $tab); 
			            	//print_r($data);  
							if(!empty(trim($line))) {
								if($row>1 && isset($data[0]) ){
									
									$ecpd_profile_id = $wpdb->_real_escape($data[0]);
									$bill_cycle_date = $wpdb->_real_escape($data[1]);  
									$account_number = $wpdb->_real_escape($data[2]);
									$invoice_number = $wpdb->_real_escape($data[3]);
									$wireless_number = $wpdb->_real_escape($data[4]);
									$user_name = $wpdb->_real_escape($data[5]);
									$cost_center = $wpdb->_real_escape($data[6]);
									$user_id = $wpdb->_real_escape($data[7]);
									$item_category = $wpdb->_real_escape($data[8]);
									$date = $wpdb->_real_escape($data[9]);
									$item_type = $wpdb->_real_escape($data[10]);
									$item_description = $wpdb->_real_escape(trim($data[11]));
									$vendor_name_contact_number = $wpdb->_real_escape($data[12]);
									$share_description = $wpdb->_real_escape($data[13]);
									$share_voice = $wpdb->_real_escape($data[14]);
									$share_messaging = $wpdb->_real_escape($data[15]);
									$share_data = $wpdb->_real_escape($data[16]);
									$usage_period = $wpdb->_real_escape($data[17]);
									$allowance = $wpdb->_real_escape($data[18]);
									$used = $wpdb->_real_escape($data[19]);
									$billable = $wpdb->_real_escape($data[20]);
									$cost = $wpdb->_real_escape($data[21]);
									$order_details = $wpdb->_real_escape($data[22]);

									$data = array(
									'mcc_user_id' 		=> $mcc_userid,
									'ecpd_profile_id'   => $ecpd_profile_id,
									'bill_cycle_date'   => $bill_cycle_date,
									'account_number'    => $account_number,
									'invoice_number'  	=> $invoice_number,
									'wireless_number'  	=> $wireless_number,
									
									'user_name'   		=> $user_name,
									'cost_center'       => $cost_center,
									'user_id'        	=> $user_id,
									'item_category'  	=> $item_category,
									'date'  			=> $date,
									
									'item_type'   		=> $item_type,
									'item_description'  => $item_description,
									'vendor_name_contact_number'        					=> $vendor_name_contact_number,
									'share_description' => $share_description,
									'share_voice'  		=> $share_voice,

									'share_messaging'   => $share_messaging,
									'share_data'       	=> $share_data,
									'usage_period'      => $usage_period,
									'allowance'  		=> $allowance,
									'used'  			=> $used,

									'billable'   		=> $billable,
									'bill_cycle_date'   => $bill_cycle_date,
									'cost'        		=> $cost,
									'order_details'  	=> $order_details,
									
									);

									$wpdb->insert( $wp_table, $data );
								}
							}

			              	$row++;
			            }
			            fclose($handle);

			            //Removing the temporary extraction folder

						if ( $wp_filesystem->is_dir( $temp_dir) ) {

							$wp_filesystem->delete($temp_dir , true );
						}

					}//end zip upload/extraction and insert
					elseif($fileExt == "csv")
		            {
						if($carrier=='verizon'){
							$tblname  = 'device_report';
							$wp_table = $table_prefix . $tblname;

							$upload_overrides = array( 'test_form' => false, 'unique_filename_callback' => 'wp_unique_filename' );
							$movefile = wp_handle_upload( $file, $upload_overrides );
							if ( $movefile && ! isset( $movefile['error'] ) ) {
								$handle = fopen($movefile['file'], "r");

								$file_urls['device_report'] = $movefile['url'];

								$row=1;
								while($data = fgetcsv($handle))
								{
									if($row>14){
										$wireless_number = $wpdb->_real_escape($data[0]);
										$early_upgrade_indicator = $wpdb->_real_escape($data[1]);  
										$shipped_device_id = $wpdb->_real_escape($data[2]);
										$current_device_id = $wpdb->_real_escape($data[3]);
										$sim = $wpdb->_real_escape($data[4]);
										$device_manufacturer = $wpdb->_real_escape($data[5]);
										$device_model = $wpdb->_real_escape($data[6]);
										$device_type = $wpdb->_real_escape($data[7]);
										$upgrade_eligibility_date = $wpdb->_real_escape($data[8]);
										$ne2_date = $wpdb->_real_escape($data[9]);
										$account_number = $wpdb->_real_escape($data[10]);
										$email_address = $wpdb->_real_escape($data[11]);
										$cost_center = $wpdb->_real_escape($data[12]);
										$user_name = $wpdb->_real_escape($data[13]);
										$ip_address = $wpdb->_real_escape($data[14]);
										$pool_name = $wpdb->_real_escape($data[15]);
										$ip_category = $wpdb->_real_escape($data[16]);
										$contract_activation_date = $wpdb->_real_escape($data[17]);
										$contract_end_date = $wpdb->_real_escape($data[18]);
	
					
										$data = array(
											'mcc_user_id' 	  => $mcc_userid,
											'wireless_number' => $wireless_number,
											'early_upgrade_indicator' => $early_upgrade_indicator,
											'shipped_device_id' => $shipped_device_id,
											'current_device_id' => $current_device_id,
											'sim' => $sim,
											
											'device_manufacturer' => $device_manufacturer,
											
											'device_model' => $device_model,
											
											'device_type' => $device_type,
											
											'upgrade_eligibility_date' => $upgrade_eligibility_date,
											
											'ne2_date' => $ne2_date,
											
											'account_number' => $account_number,
											
	
											'email_address' => $email_address,
											
											'cost_center' => $cost_center,
											
											'user_name' => $user_name,
											
											'ip_address' => $ip_address,
	
											'pool_name' => $pool_name,
	
											'ip_category' => $ip_category,
	
											'contract_activation_date' => $contract_activation_date,
											
											'contract_end_date' => $contract_end_date,
											
											);
										$wpdb->insert( $wp_table, $data );
									}
									$row++;
								}
								fclose($handle);
							}
						}
						elseif($carrier=='att'){
							$tblname  = 'att_rdd';
							$wp_table = $table_prefix . $tblname;

							$upload_overrides = array( 'test_form' => false, 'unique_filename_callback' => 'wp_unique_filename' );
							$movefile = wp_handle_upload( $file, $upload_overrides );
							if ( $movefile && ! isset( $movefile['error'] ) ) {
								$handle = fopen($movefile['file'], "r");

								$file_urls['att_rdd'] = $movefile['url'];
								
								$record = array();
								$row=1;
								$tab = "|";
								
								while($record = fgetcsv($handle,3000,$tab))
								{
									
									if($row>1){
										
										$data = array(
										'mcc_user_id' 										=> $mcc_userid,
										'section_id' 										=> $wpdb->_real_escape($record[0]),
										'billing_entity_level' 								=> $wpdb->_real_escape($record[1]),  
										'foundation_account_type'							=> $wpdb->_real_escape($record[2]),
										'remit_to_address' 									=> $wpdb->_real_escape($record[3]),
										'due_date' 											=> $wpdb->_real_escape($record[4]),
										'foundation_account_number' 						=> $wpdb->_real_escape($record[5]),
										'foundation_account_name' 							=> $wpdb->_real_escape($record[6]),
										'billing_account_number' 							=> $wpdb->_real_escape($record[7]),
										'billing_account_name' 								=> $wpdb->_real_escape($record[8]),
										'ban_invoice_number' 								=> $wpdb->_real_escape($record[9]),
										'period_end_date' 									=> $wpdb->_real_escape($record[10]),
										'wireless_number' 									=> $wpdb->_real_escape($record[11]),
										// 'group_id'											=> $wpdb->_real_escape($record[12]),
										'user_name' 										=> $wpdb->_real_escape($record[12]),
										'fan_invoice_number' 								=> $wpdb->_real_escape($record[13]),
										'fan_invoice_date' 									=> $wpdb->_real_escape($record[14]),
										'fan_invoice_previous_balance' 						=> $wpdb->_real_escape($record[15]),
										'fan_invoice_total_payments' 						=> $wpdb->_real_escape($record[16]),
										'fan_invoice_payment_date' 							=> $wpdb->_real_escape($record[17]),
										'fan_invoice_past_due' 								=> $wpdb->_real_escape($record[18]),
		
										'adjustment_to_previous_balance_description'     	=> $wpdb->_real_escape($record[19]),
										'adjustment_to_previous_balance_amount'  		 	=> $wpdb->_real_escape($record[20]),
										'adjustment_type'  								 	=> $wpdb->_real_escape($record[21]),
										'fan_level_nbs_charges_and_credits_description'  	=> $wpdb->_real_escape($record[22]),
										'fan_level_nbs_charges_and_credits_amount'       	=> $wpdb->_real_escape($record[23]),
										'fan_invoice_total_current_charges'              	=> $wpdb->_real_escape($record[24]),
										'fan_invoice_amount_due'  			 			 	=> $wpdb->_real_escape($record[25]),
										'fan_user_defined_label_1'  		 			 	=> $wpdb->_real_escape($record[26]),
										'service_id_1_udl_1_ctn_level'  	 			 	=> $wpdb->_real_escape($record[27]),
										
										'fan_user_defined_label_2'  					 	=> $wpdb->_real_escape($record[28]),
										'service_id_2_udl2_ctn_level'  					 	=> $wpdb->_real_escape($record[29]),
										'fan_user_defined_label_3'  						 => $wpdb->_real_escape($record[30]),
										'service_id_3_udl_3_ctn_level'  				 	=> $wpdb->_real_escape($record[31]),
										'fan_user_defined_label_4'  					 	=> $wpdb->_real_escape($record[32]),
										
										'service_id_4_udl4_ctn_level'   					=> $wpdb->_real_escape($record[33]),
										'voice_pooling_rate_plan_code'  					=> $wpdb->_real_escape($record[34]),
										'voice_pool_name'  									=> $wpdb->_real_escape($record[35]),
										'pooling_mou_contribution'  						=> $wpdb->_real_escape($record[36]),
										'pooling_bucket_mou_used'  							=> $wpdb->_real_escape($record[37]),
										'airtime_over'  									=> $wpdb->_real_escape($record[38]),
										'airtime_under'  									=> $wpdb->_real_escape($record[39]),
										
										'voice_allocation_factor'  							=> $wpdb->_real_escape($record[40]),
										'allocated_back_minutes'  							=> $wpdb->_real_escape($record[41]),
										'additional_minutes_rate'  							=> $wpdb->_real_escape($record[42]),
										'voice_allocated_back_credit'  						=> $wpdb->_real_escape($record[43]),
										'total_voice_tax_credit'  							=> $wpdb->_real_escape($record[44]),
										'total_voice_pooling_credit'  						=> $wpdb->_real_escape($record[45]),
										'voice_mac_adjustment'  							=> $wpdb->_real_escape($record[46]),
										'data_pooling_rate_plan_code'  						=> $wpdb->_real_escape($record[47]),
										'data_pool _name'  									=> $wpdb->_real_escape($record[48]),
										'pooling_kb_contribution'  							=> $wpdb->_real_escape($record[49]),
										'pooling_bucket_kb_used'  							=> $wpdb->_real_escape($record[50]),
										
										'kb_over'  											=> $wpdb->_real_escape($record[51]),
										'kb_under'  										=> $wpdb->_real_escape($record[52]),
										'data_allocation_factor'  							=> $wpdb->_real_escape($record[53]),
										'allocated_back_kb'  								=> $wpdb->_real_escape($record[54]),
										'additional_kb_rate'  								=> $wpdb->_real_escape($record[55]),
										'data_allocated_back_credit' 						=> $wpdb->_real_escape($record[56]),
										'total_data_tax_credit' 							=> $wpdb->_real_escape($record[57]),
										'total_data_pooling_credit' 						=> $wpdb->_real_escape($record[58]),
										'aata_mac_adjustment' 								=> $wpdb->_real_escape($record[59]),
		
										'section_1' 										=> $wpdb->_real_escape($record[60]),
										'section_2' 										=> $wpdb->_real_escape($record[61]),
										'section_3' 										=> $wpdb->_real_escape($record[62]),
										'section_4' 										=> $wpdb->_real_escape($record[63]),
										'section_5' 										=> $wpdb->_real_escape($record[64]),
										'section_6' 										=> $wpdb->_real_escape($record[65]),
										'section_7' 										=> $wpdb->_real_escape($record[66]),
		
										'period'  											=> $wpdb->_real_escape($record[67]),
										'prorated_charge'  									=> $wpdb->_real_escape($record[68]),
										'monthly_charge'  									=> $wpdb->_real_escape($record[69]),
										'amount'  											=> $wpdb->_real_escape($record[70]),
										'total'  											=> $wpdb->_real_escape($record[71]),
										'total_charge'  									=> $wpdb->_real_escape($record[72]),
										'monthly_service'  									=> $wpdb->_real_escape($record[73]),
										
										'usage_charges'  									=> $wpdb->_real_escape($record[74]),
										'credits_adj_other_charges'  						=> $wpdb->_real_escape($record[75]),
										'government_fees_taxes'  							=> $wpdb->_real_escape($record[76]),
										'non_comm_related_charges'  						=> $wpdb->_real_escape($record[77]),
										'minutes_included_in_plan'  						=> $wpdb->_real_escape($record[78]),
										'minutes_used'  									=> $wpdb->_real_escape($record[79]),
										'billed_minutes'  									=> $wpdb->_real_escape($record[80]),
										'billed_rate'  										=> $wpdb->_real_escape($record[81]),
										
										'msg_kb_mb_included_in_plan'  						=> $wpdb->_real_escape($record[82]),
										'msg_kb_mb_used'  									=> $wpdb->_real_escape($record[83]),
										'billed_msg_kb_mb'  								=> $wpdb->_real_escape($record[84]),
										'shared_text_msgs'  								=> $wpdb->_real_escape($record[85]),
										'shared_mms_msgs'  									=> $wpdb->_real_escape($record[86]),
										'shared_kbs'  										=> $wpdb->_real_escape($record[87]),
										'other_shared_minutes'  							=> $wpdb->_real_escape($record[88]),
										'billed_text_msgs'  								=> $wpdb->_real_escape($record[89]),
										'billed_mms_msgs'  									=> $wpdb->_real_escape($record[90]),
										
										'billed_kbs'  										=> $wpdb->_real_escape($record[91]),
										'billed_charges'  									=> $wpdb->_real_escape($record[92]),
										'ban_pooling_type'  								=> $wpdb->_real_escape($record[93]),
										'ban_pooling_rate_plan_code'  						=> $wpdb->_real_escape($record[94]),
										'ban_pooling_allowance_min_kb'  					=> $wpdb->_real_escape($record[95]),
										'ban_pooling_used_min_kb'  							=> $wpdb->_real_escape($record[96]),
										'ban_pooling_allocated_back_min_kb'					=> $wpdb->_real_escape($record[97]),
										'ban_pooling_adjustment_amount'  					=> $wpdb->_real_escape($record[98]),
										'left_min'  										=> $wpdb->_real_escape($record[99]),
										
										'exp_date'  										=> $wpdb->_real_escape($record[100]),
										'rollover_minutes'  								=> $wpdb->_real_escape($record[101]),
										'fan_contract_reference_number'  					=> $wpdb->_real_escape($record[102]),
										'clin'  											=> $wpdb->_real_escape($record[103]),
										'clin_total_due'  									=> $wpdb->_real_escape($record[104]),
										'quantity'  										=> $wpdb->_real_escape($record[105]),
										'equipment_transaction_date'  						=> $wpdb->_real_escape($record[106]),
										'equipment_transaction_number'  					=> $wpdb->_real_escape($record[107]),
										'equipment_item_description'  						=> $wpdb->_real_escape($record[108]),
										
										'equipment_item_id'  								=> $wpdb->_real_escape($record[109]),
										'equipment_unit_price'  							=> $wpdb->_real_escape($record[110]),
										'item_number'  										=> $wpdb->_real_escape($record[111]),
										'copay_allowance_amount'  							=> $wpdb->_real_escape($record[112]),
										'copay_employee_name'  								=> $wpdb->_real_escape($record[113]),
										'wireless_line_credit_to_number'  					=> $wpdb->_real_escape($record[114]),
										'wireless_line_credit_date_of_call' 				=> $wpdb->_real_escape($record[115]),
										'wireless_line_credit_time_of_call' 				=> $wpdb->_real_escape($record[116]),
										
										'wireless_line_credit_call_to'  					=> $wpdb->_real_escape($record[117]),
										'wireless_line_credit_for_number_called'  			=> $wpdb->_real_escape($record[118]),
										'date'  											=> $wpdb->_real_escape($record[119]),
										'item_description'  								=> $wpdb->_real_escape($record[120]),
										'short_code'  										=> $wpdb->_real_escape($record[121]),
										'type'  											=> $wpdb->_real_escape($record[122]),
										'content_provider'  								=> $wpdb->_real_escape($record[123]),
										'merchant_name'  									=> $wpdb->_real_escape($record[124]),
										'merchant_contact' 								 	=> $wpdb->_real_escape($record[125]),
										'item_id'  											=> $wpdb->_real_escape($record[126]),
										'renew_date'  										=> $wpdb->_real_escape($record[127]),
										'cost'  											=> $wpdb->_real_escape($record[128]),
										'tax' 												=> $wpdb->_real_escape($record[129])
										);
									
										$wpdb->insert( $wp_table, $data );
									
									}	
	
									$row++;
								}
								fclose($handle);
							}
						}
		            }
					elseif($fileExt == "txt")
		            {
						$tblname  = 'att_rdd';
						$wp_table = $table_prefix . $tblname;

						$upload_overrides = array( 'test_form' => false, 'unique_filename_callback' => 'wp_unique_filename' );
						$movefile = wp_handle_upload( $file, $upload_overrides );
						if ( $movefile && ! isset( $movefile['error'] ) ) {
							$handle = fopen($movefile['file'], "r");

							$file_urls['att_rdd'] = $movefile['url'];
							
							$row=1;
							$record = array();
							$tab = "|";
							while(!feof($handle) )
							{
								
								$line = fgets($handle, 2048);
								$record = str_getcsv($line, $tab); 
								
								if(!empty(trim($line))) {
									
									if($row>2 && isset($record[0])){
	
										$data = array(
										'mcc_user_id' 										=> $mcc_userid,
										'section_id' 										=> $wpdb->_real_escape($record[0]),
										'billing_entity_level' 								=> $wpdb->_real_escape($record[1]),  
										'foundation_account_type'							=> $wpdb->_real_escape($record[2]),
										'remit_to_address' 									=> $wpdb->_real_escape($record[3]),
										'due_date' 											=> $wpdb->_real_escape($record[4]),
										'foundation_account_number' 						=> $wpdb->_real_escape($record[5]),
										'foundation_account_name' 							=> $wpdb->_real_escape($record[6]),
										'billing_account_number' 							=> $wpdb->_real_escape($record[7]),
										'billing_account_name' 								=> $wpdb->_real_escape($record[8]),
										'ban_invoice_number' 								=> $wpdb->_real_escape($record[9]),
										'period_end_date' 									=> $wpdb->_real_escape($record[10]),
										'wireless_number' 									=> $wpdb->_real_escape($record[11]),
										'group_id'											=> $wpdb->_real_escape($record[12]),
										'user_name' 										=> $wpdb->_real_escape($record[13]),
										'fan_invoice_number' 								=> $wpdb->_real_escape($record[14]),
										'fan_invoice_date' 									=> $wpdb->_real_escape($record[15]),
										'fan_invoice_previous_balance' 						=> $wpdb->_real_escape($record[16]),
										'fan_invoice_total_payments' 						=> $wpdb->_real_escape($record[17]),
										'fan_invoice_payment_date' 							=> $wpdb->_real_escape($record[18]),
										'fan_invoice_past_due' 								=> $wpdb->_real_escape($record[19]),
	
										'adjustment_to_previous_balance_description'     	=> $wpdb->_real_escape($record[20]),
										'adjustment_to_previous_balance_amount'  		 	=> $wpdb->_real_escape($record[21]),
										'adjustment_type'  								 	=> $wpdb->_real_escape($record[22]),
										'fan_level_nbs_charges_and_credits_description'  	=> $wpdb->_real_escape($record[23]),
										'fan_level_nbs_charges_and_credits_amount'       	=> $wpdb->_real_escape($record[24]),
										'fan_invoice_total_current_charges'              	=> $wpdb->_real_escape($record[25]),
										'fan_invoice_amount_due'  			 			 	=> $wpdb->_real_escape($record[26]),
										'fan_user_defined_label_1'  		 			 	=> $wpdb->_real_escape($record[27]),
										'service_id_1_udl_1_ctn_level'  	 			 	=> $wpdb->_real_escape($record[28]),
										
										'fan_user_defined_label_2'  					 	=> $wpdb->_real_escape($record[29]),
										'service_id_2_udl2_ctn_level'  					 	=> $wpdb->_real_escape($record[30]),
										'fan_user_defined_label_3'  						 => $wpdb->_real_escape($record[31]),
										'service_id_3_udl_3_ctn_level'  				 	=> $wpdb->_real_escape($record[32]),
										'fan_user_defined_label_4'  					 	=> $wpdb->_real_escape($record[33]),
										
										'service_id_4_udl4_ctn_level'   					=> $wpdb->_real_escape($record[34]),
										'voice_pooling_rate_plan_code'  					=> $wpdb->_real_escape($record[35]),
										'voice_pool_name'  									=> $wpdb->_real_escape($record[36]),
										'pooling_mou_contribution'  						=> $wpdb->_real_escape($record[37]),
										'pooling_bucket_mou_used'  							=> $wpdb->_real_escape($record[38]),
										'airtime_over'  									=> $wpdb->_real_escape($record[39]),
										'airtime_under'  									=> $wpdb->_real_escape($record[40]),
										
										'voice_allocation_factor'  							=> $wpdb->_real_escape($record[41]),
										'allocated_back_minutes'  							=> $wpdb->_real_escape($record[42]),
										'additional_minutes_rate'  							=> $wpdb->_real_escape($record[43]),
										'voice_allocated_back_credit'  						=> $wpdb->_real_escape($record[44]),
										'total_voice_tax_credit'  							=> $wpdb->_real_escape($record[45]),
										'total_voice_pooling_credit'  						=> $wpdb->_real_escape($record[46]),
										'voice_mac_adjustment'  							=> $wpdb->_real_escape($record[47]),
										'data_pooling_rate_plan_code'  						=> $wpdb->_real_escape($record[48]),
										'data_pool _name'  									=> $wpdb->_real_escape($record[49]),
										'pooling_kb_contribution'  							=> $wpdb->_real_escape($record[50]),
										'pooling_bucket_kb_used'  							=> $wpdb->_real_escape($record[51]),
										
										'kb_over'  											=> $wpdb->_real_escape($record[52]),
										'kb_under'  										=> $wpdb->_real_escape($record[53]),
										'data_allocation_factor'  							=> $wpdb->_real_escape($record[54]),
										'allocated_back_kb'  								=> $wpdb->_real_escape($record[55]),
										'additional_kb_rate'  								=> $wpdb->_real_escape($record[56]),
										'data_allocated_back_credit' 						=> $wpdb->_real_escape($record[57]),
										'total_data_tax_credit' 							=> $wpdb->_real_escape($record[58]),
										'total_data_pooling_credit' 						=> $wpdb->_real_escape($record[59]),
										'aata_mac_adjustment' 								=> $wpdb->_real_escape($record[60]),
	
										'section_1' 										=> $wpdb->_real_escape($record[61]),
										'section_2' 										=> $wpdb->_real_escape($record[62]),
										'section_3' 										=> $wpdb->_real_escape($record[63]),
										'section_4' 										=> $wpdb->_real_escape($record[64]),
										'section_5' 										=> $wpdb->_real_escape($record[65]),
										'section_6' 										=> $wpdb->_real_escape($record[66]),
										'section_7' 										=> $wpdb->_real_escape($record[67]),
	
										'period'  											=> $wpdb->_real_escape($record[68]),
										'prorated_charge'  									=> $wpdb->_real_escape($record[69]),
										'monthly_charge'  									=> $wpdb->_real_escape($record[70]),
										'amount'  											=> $wpdb->_real_escape($record[71]),
										'total'  											=> $wpdb->_real_escape($record[72]),
										'total_charge'  									=> $wpdb->_real_escape($record[73]),
										'monthly_service'  									=> $wpdb->_real_escape($record[74]),
										
										'usage_charges'  									=> $wpdb->_real_escape($record[75]),
										'credits_adj_other_charges'  						=> $wpdb->_real_escape($record[76]),
										'government_fees_taxes'  							=> $wpdb->_real_escape($record[77]),
										'non_comm_related_charges'  						=> $wpdb->_real_escape($record[78]),
										'minutes_included_in_plan'  						=> $wpdb->_real_escape($record[79]),
										'minutes_used'  									=> $wpdb->_real_escape($record[80]),
										'billed_minutes'  									=> $wpdb->_real_escape($record[81]),
										'billed_rate'  										=> $wpdb->_real_escape($record[82]),
										
										'msg_kb_mb_included_in_plan'  						=> $wpdb->_real_escape($record[83]),
										'msg_kb_mb_used'  									=> $wpdb->_real_escape($record[84]),
										'billed_msg_kb_mb'  								=> $wpdb->_real_escape($record[85]),
										'shared_text_msgs'  								=> $wpdb->_real_escape($record[86]),
										'shared_mms_msgs'  									=> $wpdb->_real_escape($record[87]),
										'shared_kbs'  										=> $wpdb->_real_escape($record[88]),
										'other_shared_minutes'  							=> $wpdb->_real_escape($record[89]),
										'billed_text_msgs'  								=> $wpdb->_real_escape($record[90]),
										'billed_mms_msgs'  									=> $wpdb->_real_escape($record[91]),
										
										'billed_kbs'  										=> $wpdb->_real_escape($record[92]),
										'billed_charges'  									=> $wpdb->_real_escape($record[93]),
										'ban_pooling_type'  								=> $wpdb->_real_escape($record[94]),
										'ban_pooling_rate_plan_code'  						=> $wpdb->_real_escape($record[95]),
										'ban_pooling_allowance_min_kb'  					=> $wpdb->_real_escape($record[96]),
										'ban_pooling_used_min_kb'  							=> $wpdb->_real_escape($record[97]),
										'ban_pooling_allocated_back_min_kb'					=> $wpdb->_real_escape($record[98]),
										'ban_pooling_adjustment_amount'  					=> $wpdb->_real_escape($record[99]),
										'left_min'  										=> $wpdb->_real_escape($record[100]),
										
										'exp_date'  										=> $wpdb->_real_escape($record[101]),
										'rollover_minutes'  								=> $wpdb->_real_escape($record[102]),
										'fan_contract_reference_number'  					=> $wpdb->_real_escape($record[103]),
										'clin'  											=> $wpdb->_real_escape($record[104]),
										'clin_total_due'  									=> $wpdb->_real_escape($record[105]),
										'quantity'  										=> $wpdb->_real_escape($record[106]),
										'equipment_transaction_date'  						=> $wpdb->_real_escape($record[107]),
										'equipment_transaction_number'  					=> $wpdb->_real_escape($record[108]),
										'equipment_item_description'  						=> $wpdb->_real_escape($record[109]),
										
										'equipment_item_id'  								=> $wpdb->_real_escape($record[110]),
										'equipment_unit_price'  							=> $wpdb->_real_escape($record[111]),
										'item_number'  										=> $wpdb->_real_escape($record[112]),
										'copay_allowance_amount'  							=> $wpdb->_real_escape($record[113]),
										'copay_employee_name'  								=> $wpdb->_real_escape($record[114]),
										'wireless_line_credit_to_number'  					=> $wpdb->_real_escape($record[115]),
										'wireless_line_credit_date_of_call' 				=> $wpdb->_real_escape($record[116]),
										'wireless_line_credit_time_of_call' 				=> $wpdb->_real_escape($record[117]),
										
										'wireless_line_credit_call_to'  					=> $wpdb->_real_escape($record[118]),
										'wireless_line_credit_for_number_called'  			=> $wpdb->_real_escape($record[119]),
										'date'  											=> $wpdb->_real_escape($record[120]),
										'item_description'  								=> $wpdb->_real_escape($record[121]),
										'short_code'  										=> $wpdb->_real_escape($record[122]),
										'type'  											=> $wpdb->_real_escape($record[123]),
										'content_provider'  								=> $wpdb->_real_escape($record[124]),
										'merchant_name'  									=> $wpdb->_real_escape($record[125]),
										'merchant_contact' 								 	=> $wpdb->_real_escape($record[126]),
										'item_id'  											=> $wpdb->_real_escape($record[127]),
										'renew_date'  										=> $wpdb->_real_escape($record[128]),
										'cost'  											=> $wpdb->_real_escape($record[129]),
										'tax' 												=> $wpdb->_real_escape($record[130])
										);
										
										$wpdb->insert( $wp_table, $data );
									
									}
								}
								$row++;
							}
							fclose($handle);
						}
					}
					elseif($fileExt == "xlsx")
		            {
						//incoming feature
					}
		            else
		            {
		                $message = '<label class="text-danger">Please Select Zip or csv Files only</label>';
		            }

				}

				if($carrier=='verizon'){
					self::process_verizon_rdd($mcc_userid);
            		self::process_plans();
				}
				self::get_uit_data($mcc_userid,$file_urls,$carrier);

			}
			else
			{
				$message = '<label class="text-danger">Please Select 2 bill files</label>';
			}
        }

	}


	public static function insert_mcc_user($data){
		
		global $table_prefix, $wpdb;
		$tblname = 'mcc_automated';
		$wp_table = $table_prefix . $tblname;
		
		$wpdb->insert($wp_table, array(
			'first_name' 		=> $data['fname'],
			'last_name' 		=> $data['lname'],
			'email' 			=> $data['email'],
			'phone' 			=> $data['phone']?? '',
			'carrier' 			=> $data['carrier'],
		));

		$id = $wpdb->insert_id;

		$customerRow = $wpdb->get_row("SELECT * FROM ".$wp_table." WHERE id = ".$id, 'ARRAY_A');
		$customerRow['action'] = 'mcc_automated_sync_customer';
		$customerRow['sourceDomain'] = get_site_url();
		$result = wp_remote_post('https://wirelessbutlerserver.com/wp/wp-admin/admin-post.php', 
			array(
				'method' 		=> 'POST',
				'timeout'     	=> 45,
				'httpversion' 	=> '1.0',
				'sslverify' 	=> false,
				'body' 			=> $customerRow
			)
		);

		return $id;	
	} 

	public static function process_verizon_rdd($mcc_userid){
		global $table_prefix, $wpdb;
		$results = array();
		
		$rd_tblname = 'verizon_rdd';
		$rd_table = $table_prefix . $rd_tblname;

		$dr_tblname = 'device_report';
		$dr_table = $table_prefix . $dr_tblname;

		$pc_tblname = 'plan_categories';
		$pc_table = $table_prefix . $pc_tblname;

		$uit_tblname = 'uit_vzw';
		$uit_table = $table_prefix . $uit_tblname;

	
		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table 
		SET wireless_number = 
		LEFT(wireless_number,50) 
		WHERE LENGTH(wireless_number) > 50 and mcc_user_id = %s",$mcc_userid) );


		$results[] =  $wpdb->query( $wpdb->prepare(
		"UPDATE	$rd_table
	    SET	wireless_number = 
	    'NA-' + LTRIM(RTRIM(account_number))
	    WHERE	wireless_number = 'NA' and mcc_user_id = %s",$mcc_userid) );
	            

        //removing spaces from left and right sides of item description in $rd_table table
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table
        SET item_description = TRIM(item_description) where mcc_user_id = %s",$mcc_userid));

        //removing spaces from left and right sides of item description in $pc_table table
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $pc_table
        SET item_description = TRIM(item_description) "));
        
        
        //set  item_description to 'month in advance' where iteam category = Monthly charges and item description similar to month in adv 
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table
        SET item_description = REPLACE(item_description, ' (month in advance)', '')
        WHERE item_category = 'Monthly Charges' 
        AND item_description LIKE '% (month in adv)%' and mcc_user_id = %s",$mcc_userid));
        

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET usage_period = 'Month in adv' 
        WHERE item_category = 'Monthly Charges'
        AND  LENGTH(date) > 20 
        AND 
        (ABS(DATEDIFF(str_to_date(RIGHT(date,10), 
        '%m/%d/%Y'),str_to_date(LEFT(date,10), '%m/%d/%Y')))) + 1 =
        ABS(DATEDIFF(str_to_date(RIGHT(date,10), '%m/%d/%Y'),STR_TO_DATE(bill_cycle_date, '%b %d, %Y'))) and mcc_user_id = %s",$mcc_userid) );
        

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table
        SET   usage_period = 'Month in adv'
        WHERE wireless_number LIKE 'NA-%'
        AND   LENGTH(date) > 16
        AND   (ABS(DATEDIFF(str_to_date(RIGHT(date,8), '%m/%d/%Y'),str_to_date(LEFT(date,8), '%m/%d/%Y')))) + 1 = ABS(DATEDIFF(str_to_date(RIGHT(date,8), '%m/%d/%Y'),str_to_date(bill_cycle_date, '%b %d, %Y'))) and mcc_user_id = %s",$mcc_userid) );

        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table SET valid = 0 where mcc_user_id = %s",$mcc_userid) );


        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table SET valid = 1
        WHERE Usage_period LIKE '%Month in adv%' 
        AND item_description != 'Discount' 
        AND item_description NOT LIKE '%\%%' and mcc_user_id = %s",$mcc_userid) );


        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table 
        SET phone_number_invoice 
        = CONCAT(wireless_number,invoice_number) where mcc_user_id = %s",$mcc_userid) );
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table 
        SET delta_match = NULL where mcc_user_id = %s",$mcc_userid) );
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table 
        SET delta = NULL where mcc_user_id = %s",$mcc_userid) );
                

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table 
        SET wireless_card_charges = NULL where mcc_user_id = %s",$mcc_userid) );

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table 
        SET wireless_card_charges = NULL where mcc_user_id = %s",$mcc_userid) );
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table 
        SET wireless_card_charges = COALESCE(CAST(cost as float),0) 
        WHERE valid = 1
        AND EXISTS
        (
          SELECT  1
          FROM  $pc_table WHERE 
          $pc_table.item_description 
          = $rd_table.item_description 
          AND 
          $pc_table.wireless_card_charges = 1
        ) and mcc_user_id = %s",$mcc_userid ) );
        

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table 
        SET wireless_card_credits = NULL where mcc_user_id = %s",$mcc_userid) );
            

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET wireless_card_credits = cost
        WHERE valid = 1
        AND EXISTS
        (
          SELECT  1
          FROM  $pc_table
          WHERE $pc_table.item_description
          = $rd_table.item_description
          AND $pc_table.wireless_card_credit= 1
        ) and mcc_user_id = %s",$mcc_userid) );
        
            
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE  $rd_table
        SET voice_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );
        
        $results[] = $wpdb->query( $wpdb->prepare( 
    	"UPDATE $rd_table
        SET     voice_plan_charges = cost
        WHERE   valid = 1
        AND   EXISTS
        (
          SELECT  1
          FROM  $pc_table
          WHERE $pc_table.item_description 
          = $rd_table.item_description
          AND $pc_table.voice_plan_charges = 1
        ) and mcc_user_id = %s",$mcc_userid) );
            
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET voice_plan_credit = NULL where mcc_user_id = %s",$mcc_userid) );
        

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET     voice_plan_credit = cost
        WHERE   valid = 1
        AND   	EXISTS
        (
          SELECT  1
          FROM  $pc_table
          WHERE $pc_table.item_description
          = $rd_table.item_description
          AND $pc_table.voice_plan_credit = 1
        ) and mcc_user_id = %s",$mcc_userid) );
            

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET data_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );
        
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET     data_plan_charges = cost
        WHERE   valid = 1
        AND   	EXISTS
        (
          SELECT  1
          FROM  $pc_table
          WHERE $pc_table.item_description 
          = $rd_table.item_description
          AND $pc_table.data_plan_charges
           = 1
        ) and mcc_user_id = %s",$mcc_userid) );
        

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET data_plan_credit = NULL where mcc_user_id = %s",$mcc_userid) );
            
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET data_plan_credit = cost
        WHERE usage_period = 'Month in adv'
        AND EXISTS
        (
          SELECT  1
          FROM  $pc_table
          WHERE $pc_table.item_description = $rd_table.item_description
            AND $pc_table.data_plan_credit = 1
        ) and mcc_user_id = %s",$mcc_userid) );
                

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET data_plan_credit = cost
        WHERE  item_description = '22% - Feature Discount'
        AND   cost IS NOT NULL
        AND   CAST(cost AS decimal(18, 2)) = -5.00 and mcc_user_id = %s",$mcc_userid) );
        
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET international_data_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );
        

        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET international_data_plan_charges = cost
        WHERE valid = 1
        AND EXISTS
        (
          SELECT  1
          FROM  $pc_table
          WHERE $pc_table.item_description 
          = $rd_table.item_description
          AND $pc_table.international_data_plan_charges = 1
        ) and mcc_user_id = %s",$mcc_userid) );
                
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET  wireless_card_charges = NULL
        WHERE  LENGTH(wireless_card_charges) = 0 and mcc_user_id = %s",$mcc_userid) );
        
        $results[] = $wpdb->query( $wpdb->prepare( 
        "UPDATE $rd_table
        SET  concatenate_wireless_card_charges = 
        CONCAT(phone_number_invoice,wireless_card_charges)
        WHERE valid = 1
        AND wireless_card_charges 
        REGEXP '[[:digit:]]+' = 1
        AND CAST(wireless_card_charges 
        as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );
        
		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET voice_plan_charges = NULL
		WHERE LENGTH(voice_plan_charges) = 0 and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_voice_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_voice_plan_charges = 
		CONCAT(phone_number_invoice,voice_plan_charges)
		WHERE valid = 1
		AND   voice_plan_charges REGEXP '[[:digit:]]+' = 1
		AND   CAST(voice_plan_charges as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET data_plan_charges = NULL
		WHERE LENGTH(data_plan_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_data_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_data_plan_charges 
		= CONCAT(phone_number_invoice,data_plan_charges)
		WHERE valid = 1
		AND data_plan_charges REGEXP '[[:digit:]]+' = 1
		AND CAST(data_plan_charges as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET  message_plan_charges = NULL
		WHERE  LENGTH(message_plan_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_message_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_message_plan_charges = 
		CONCAT(phone_number_invoice,message_plan_charges)
		WHERE valid = 1
		AND message_plan_charges REGEXP '[[:digit:]]+' = 1
		AND CAST(message_plan_charges as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_message_plan_charges = NULL
		WHERE  LENGTH(international_message_plan_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_international_message_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_international_message_plan_charges = CONCAT(phone_number_invoice,international_message_plan_charges)
		WHERE valid = 1
		AND international_message_plan_charges 
		REGEXP '[[:digit:]]+' = 1
		AND CAST(international_message_plan_charges 
		as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_data_plan_charges = NULL
		WHERE   LENGTH(international_data_plan_charges) 
		= 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_international_data_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET  concatenate_international_data_plan_charges 
		= CONCAT(phone_number_invoice,international_data_plan_charges)
		WHERE  valid = 1
		AND  international_data_plan_charges 
		REGEXP '[[:digit:]]+' = 1
		AND  CAST(international_data_plan_charges 
		as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET     mobile_web_charges = NULL
		WHERE   LENGTH(mobile_web_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_mobile_web_plan_charges 
		= NULL where mcc_user_id = %s",$mcc_userid));

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_mobile_web_plan_charges 
		= CONCAT(phone_number_invoice,mobile_web_charges)
		WHERE   valid = 1
		AND   mobile_web_charges REGEXP '[[:digit:]]+' = 1
		AND   CAST(mobile_web_charges 
		as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET insurance_charges = NULL
		WHERE  LENGTH(insurance_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_insurance_Plan_charges 
		= NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_insurance_Plan_charges = 
		CONCAT(phone_number_invoice,insurance_charges)
		WHERE valid = 1
		AND   insurance_charges REGEXP '[[:digit:]]+' = 1
		AND   CAST(insurance_charges as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET callerId_charges = NULL
		WHERE LENGTH(callerId_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table
		SET  concatenate_callerID_plan_charges 
		= NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_callerID_plan_charges = 
		CONCAT(phone_number_invoice,callerId_charges)
		WHERE valid = 1
		AND callerId_charges REGEXP '[[:digit:]]+' = 1
		AND CAST(callerId_charges as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_voice_global_feature_charges 
		= NULL
		WHERE LENGTH(international_voice_global_feature_charges) 
		= 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_global_voice_feature_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_global_voice_feature_plan_charges = CONCAT(phone_number_invoice,international_voice_global_feature_charges)
		WHERE valid = 1
		AND international_voice_global_feature_charges 
		REGEXP '[[:digit:]]+' = 1 AND 
		CAST(international_voice_global_feature_charges 
		as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET business_tracking_app_charges = NULL
		WHERE LENGTH(business_tracking_app_charges)
		 = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_business_tracking_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_business_tracking_plan_charges 
		= CONCAT(phone_number_invoice,business_tracking_app_charges)
		WHERE valid = 1
		AND business_tracking_app_charges 
		REGEXP '[[:digit:]]+' = 1
		AND CAST(business_tracking_app_charges 
		as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_wifi = NULL
		WHERE LENGTH(international_wifi) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_international_wifi_plan_charges 
		= NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_international_wifi_plan_charges 
		= CONCAT(phone_number_invoice,international_wifi)
		WHERE valid = 1
		AND   international_wifi REGEXP '[[:digit:]]+' = 1
		AND   CAST(international_wifi as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET wifi_charges = NULL
		WHERE LENGTH(wifi_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET wifi_charges = NULL
		WHERE LENGTH(wifi_charges) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $rd_table
		SET concatenate_wifi_plan_charges = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET concatenate_wifi_plan_charges 
		= CONCAT(phone_number_invoice,wifi_charges)
		WHERE valid = 1
		AND  wifi_charges REGEXP '[[:digit:]]+' = 1
		AND  CAST(wifi_charges as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table 
		SET plan_names = item_description where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET message_plan_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.message_plan_charges 
		  = 1
		) and mcc_user_id = %s",$mcc_userid) );

		  
		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET tethering_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.tethering_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );



		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET walkie_talkie_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.walkie_talkie_charges 
		  = 1
		) and mcc_user_id = %s",$mcc_userid) );

		 
		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET wifi_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description = $rd_table.item_description
		  AND $pc_table.wifi_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );
		  

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_tethering_plan_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description
		  = $rd_table.item_description
		  AND $pc_table.international_tethering_plan_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_wifi = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description
		  = $rd_table.item_description
		  AND $pc_table.international_wifi = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET business_tracking_app_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.business_tracking_app_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_voice_global_feature_charges 
		= 0 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_voice_global_feature_charges 
		= cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT 1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.international_voice_global_feature_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET international_long_distance_charges = cost
		WHERE valid = 1
		AND   EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.international_long_distance_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET callerID_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.callerId_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET insurance_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT 1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.insurance_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );



		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET voicemail_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT 1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.voice_mail_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET download_charges = cost
		WHERE valid = 1
		AND item_description LIKE '%media%'
		AND item_description NOT LIKE '%premium%' and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET navigation_charges = cost
		WHERE EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.navigation_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );



		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET 	picture_video_message_charges = cost
		WHERE 	valid = 1
		AND 	EXISTS
		(
		  SELECT 1
		  FROM $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.picture_video_message_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET 	ringtone_charges = cost
		WHERE 	valid = 1
		AND   	EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.ringtone_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET  	mobile_web_charges = cost
		WHERE 	valid = 1
		AND   	EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.mobile_web_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );



		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET     internet_access_and_usage_charges = cost
		WHERE   valid = 1
		AND   	EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.internet_access_and_usage_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET     international_message_plan_charges = cost
		WHERE   valid = 1
		AND   EXISTS
		(
		  SELECT 1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.international_message_plan_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET feature_discount = 0 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET feature_discount = 1
		WHERE item_description LIKE '%\%%'
		AND item_description LIKE '%Feature%' and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET plan_discount = 0 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table SET plan_discount = 1
		WHERE item_description LIKE '%\%%'
		AND item_description LIKE '%access%' and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table SET 
		discount_date = bill_cycle_date where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET wireless_priority_service_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.wireless_priority_service_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET     device_backup_charges = cost
		WHERE   valid = 1
		AND   EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description 
		  = $rd_table.item_description
		  AND $pc_table.device_backup_charges 
		  = 1
		) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET roadside_asst_charges = cost
		WHERE valid = 1
		AND EXISTS
		(
		  SELECT  1
		  FROM  $pc_table
		  WHERE $pc_table.item_description = $rd_table.item_description
		  AND $pc_table.roadside_asst_charges = 1
		) and mcc_user_id = %s",$mcc_userid) );


		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table SET 
		zero_usage = 1 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table SET zero_usage = 0
		WHERE used REGEXP '[[:digit:]]+' = 1
		AND CAST(used as decimal(9,2)) > 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table 
		SET reverse_check_lines_with_term_fees = 0 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table 
		SET reverse_check_lines_with_term_fees = 1
		WHERE item_description LIKE '%term%'
		AND cost REGEXP '[[:digit:]]+' = 1
		AND CAST(cost as decimal(9,2)) < 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET plan_discount_amount = 0 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET plan_discount_amount = RTRIM(LTRIM(REPLACE(item_description, '% Access Discount', '')))
		WHERE item_description LIKE '%Access discount%'
		AND   item_description NOT LIKE '%reversal%' and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET feature_discount_amount = 0 where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table 
		SET feature_discount_amount = RTRIM(LTRIM(REPLACE(item_description, '% - Feature Discount', '')))
		WHERE item_description 
		LIKE '% - Feature Discount%'
		AND item_description NOT 
		LIKE '%reversal%' and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table 
		SET charge_associated_with_discount = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table
		SET   charge_associated_with_discount = 
		CONVERT((CAST(cost AS decimal(9,2)) * -100 /CAST(feature_discount_amount 
		AS decimal(9,2))),DECIMAL(10,2))
		WHERE feature_discount = 1
		AND feature_discount_amount <> 0 and mcc_user_id = %s",$mcc_userid) );
		 
		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table
		SET  charge_associated_with_discount = 
		CONVERT((CAST(cost AS decimal(9,2)) * -100 /CAST(plan_discount_amount AS decimal(9,2))),DECIMAL(10,2))
		WHERE plan_discount = 1
		AND plan_discount_amount <> 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE  $rd_table
		SET   wireless_number = 
		LTRIM(RTRIM(wireless_number)) + '-Migrated'
		WHERE wireless_number NOT LIKE '%-Migrated'
		AND phone_number_invoice IN
		(
		  SELECT    DISTINCT r.phone_number_invoice
		  FROM    $rd_table r
		  WHERE   valid = 0
		  and 	  mcc_user_id = $mcc_userid
		  AND   EXISTS
		  (
		    SELECT    1
		    FROM    $rd_table r2
		    WHERE   r.wireless_number = r2.wireless_number
		    AND   	r.phone_number_invoice <> r2.phone_number_invoice
		    AND   r.valid = 0
		    AND   r2.valid = 0
		    AND   r.mcc_user_id = $mcc_userid 
		    AND   r2.mcc_user_id = $mcc_userid
		  )
		  AND phone_number_invoice NOT IN
		  (
		    SELECT  phone_number_invoice
		    FROM    $rd_table r3
		    WHERE   r.wireless_number = r3.wireless_number
		    AND   	r3.valid = 1 
		    AND r3.mcc_user_id = $mcc_userid
		  )
		) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET automagic = NULL where mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET automagic = 'Automagic'
		WHERE   Valid = 1
		AND   CAST(Cost AS DECIMAL(9,2)) <> 0
		AND   COALESCE(CAST(wireless_card_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(Wireless_card_credits AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(voice_plan_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(voice_plan_credit AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(data_plan_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(data_plan_credit AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(international_data_plan_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(message_plan_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(tethering_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(walkie_talkie_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(wifi_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(international_tethering_plan_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(international_wifi AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(business_tracking_app_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(international_voice_global_feature_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(international_long_distance_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(callerId_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(insurance_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(voicemail_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(download_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(navigation_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(picture_video_message_Charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(ringtone_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(mobile_web_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(internet_access_and_usage_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(international_message_plan_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(wireless_priority_service_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(device_Backup_charges AS decimal(9,2)),0) = 0
		AND   COALESCE(CAST(roadside_asst_charges AS decimal(9,2)),0) = 0 and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare( 
		"UPDATE $rd_table
		SET automagic = 'Valid'
		WHERE   Valid = 1
		AND   CAST(Cost AS DECIMAL(9,2)) <> 0
		AND (
		    COALESCE(CAST(wireless_card_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(wireless_card_credits AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(voice_plan_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(voice_plan_credit AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(data_plan_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(data_plan_credit AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(international_data_plan_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(message_plan_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(tethering_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(walkie_talkie_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(wifi_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(international_tethering_plan_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(international_wifi AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(business_tracking_app_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(international_voice_global_feature_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(international_long_distance_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(callerId_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(insurance_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(voicemail_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(download_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(navigation_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(picture_video_message_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(ringtone_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(mobile_web_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(internet_access_and_usage_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(international_message_plan_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(wireless_priority_service_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(device_backup_charges AS decimal(9,2)),0) <> 0
		    OR    COALESCE(CAST(roadside_asst_charges AS decimal(9,2)),0) <> 0
		  ) and mcc_user_id = %s",$mcc_userid) );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $rd_table
        SET  automagic = 'Not Valid'
        WHERE Valid = 0 and mcc_user_id = %s",$mcc_userid) );
	}

	public static function process_plans(){
		
		global $table_prefix, $wpdb;
		$results = array();
		
		$rd_tblname = 'verizon_rdd';
		$rd_table = $table_prefix . $rd_tblname;

		$dr_tblname = 'device_report';
		$dr_table = $table_prefix . $dr_tblname;

		$pc_tblname = 'plan_categories';
		$pc_table = $table_prefix . $pc_tblname;

		$uit_tblname = 'uit_vzw';
		$uit_table = $table_prefix . $uit_tblname;

		$results[] = $wpdb->query( $wpdb->prepare(
		"INSERT INTO $pc_table
		(item_description, automagic)
		SELECT    DISTINCT trim(item_description), 1 AS automagic
		FROM    $rd_table
		WHERE   automagic = 'Automagic'
		AND   NOT EXISTS
		(
		  SELECT    trim(item_description)
		  FROM    $pc_table
		  WHERE   trim($rd_table.item_description) = trim($pc_table.item_description)
		) ") );
                
		$results[] = $wpdb->query( $wpdb->prepare("UPDATE $pc_table
		SET voice_plan_charges = 1
		WHERE automagic = 1
		AND(
		item_description LIKE '%New Verizon Plan%'
		OR
		item_description LIKE '%Basic%'
		OR
		item_description LIKE '%bsc%'
		OR
		item_description LIKE '%feature phone%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE $pc_table
		SET data_plan_charges = 1
		WHERE automagic = 1
		AND(
		item_description LIKE '%Smart%'
		OR
		  item_description LIKE '%Smtphn%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET  international_data_plan_charges = 1
		WHERE  automagic = 1
		AND   (
		item_description LIKE '%International%'
		OR
		item_description LIKE '%World%'
		OR
		item_description LIKE '%Glob%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE $pc_table
		SET wireless_card_charges = 1
		WHERE automagic = 1
		AND(
		item_description LIKE '%Tablet%'
		OR
		item_description LIKE '%Tblt%'
		OR
		item_description LIKE '%jetpack%'
		OR
		item_description LIKE '%Data Device%'
		OR
		item_description LIKE '%Data only%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE $pc_table
		SET device_backup_charges = 1
		WHERE automagic = 1
		AND item_description LIKE '%cloud%' ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE $pc_table
		SET peak_minutes_included_full_month = 0
		WHERE automagic = 1
		AND wireless_card_charges = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE    $pc_table
		SET     peak_minutes_included_full_month = 99999
		WHERE   automagic = 1
		AND   
		(
		  voice_plan_charges = 1
		  OR
		  data_plan_charges = 1
		)
		AND
		(
		  item_description LIKE '%Unlimited%'
		  OR
		  item_description LIKE '%line access%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE    $pc_table
		SET     messages_included = 99999
		WHERE   automagic = 1
		AND   
		(
		  voice_plan_charges = 1
		  OR
		  data_plan_charges = 1
		)
		AND
		(
		  item_description LIKE '%Unlimited%'
		  OR
		  item_description LIKE '%line access%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE $pc_table
		SET domestic_data_included_mb = 0
		WHERE automagic = 1
		AND domestic_data_included_mb IS NULL
		AND item_description LIKE '%line access%'
		AND(
		wireless_card_charges = 1
		OR
		voice_plan_charges = 1
		OR
		data_plan_charges = 1
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare("UPDATE  $pc_table
		SET domestic_data_included_mb = 99999
		WHERE automagic = 1
		AND domestic_data_included_mb IS NULL
		AND item_description LIKE '%Unlimited%' ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = NULL ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = LTRIM(RTRIM(item_description))
		WHERE item_description LIKE '%GB%'
		AND item_description NOT LIKE '%rinGBack%'
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET scratch = 
		LTRIM(RTRIM(REPLACE(scratch, '/',' ')))
		WHERE scratch IS NOT NULL
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = 
		LTRIM(RTRIM(REPLACE(scratch, '&',' ')))
		WHERE scratch IS NOT NULL
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET scratch = 
		LTRIM(RTRIM(REPLACE(scratch, 'SHR','SHR ')))
		WHERE scratch IS NOT NULL
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET  scratch = 
		LTRIM(RTRIM(REPLACE(scratch, ' GB','GB')))
		WHERE scratch IS NOT NULL
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = LTRIM(RTRIM(
		RIGHT(scratch, LENGTH(scratch) - POSITION('GB' 
		IN scratch) + LENGTH('GB') + 2)))
		WHERE scratch IS NOT NULL
		AND  automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET scratch = LTRIM(RTRIM(LEFT
		(scratch, POSITION('GB' IN scratch) - 1)))
		WHERE scratch IS NOT NULL
		AND scratch LIKE '%GB%'
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = LTRIM(RTRIM(RIGHT(scratch, 
		LENGTH(scratch) - POSITION(' ' IN scratch))))
		WHERE   scratch IS NOT NULL
		AND   scratch LIKE '% %'
		AND   automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = 
		LTRIM(RTRIM(REPLACE(scratch, '/','')))
		WHERE scratch IS NOT NULL
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET scratch = 
		LTRIM(RTRIM(REPLACE(scratch, 'gb','')))
		WHERE scratch IS NOT NULL
		AND automagic = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET  domestic_data_included_mb = 
		CONVERT(scratch,INT) * 1024
		WHERE  automagic = 1
		AND scratch REGEXP '[[:digit:]]+' = 1
		AND  scratch < 1024 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE  $pc_table
		SET device_name_given_by_validas = 'Account Host'
		WHERE device_name_given_by_validas IS NULL
		AND automagic = 1
		AND item_description LIKE '%New Verizon Plan%' ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET  device_name_given_by_validas = 'Tablet'
		WHERE  device_name_given_by_validas IS NULL
		AND   automagic = 1
		AND  wireless_card_charges = 1
		AND 
		(
		  item_description LIKE '%Tab%'
		    OR
		  item_description LIKE '%Tblt%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET device_Name_given_by_validas = 'Aircard'
		WHERE device_name_given_by_validas IS NULL
		AND automagic = 1
		AND   
		(
		  item_description LIKE '%Jet%'
		  OR
		  item_description LIKE '%Jtp%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET device_name_given_by_validas = 
		'Machine to Machine'
		WHERE device_name_given_by_validas IS NULL
		AND automagic = 1
		AND 
		(
		  item_description LIKE '%Machine%'
		  OR
		  item_description LIKE '%M2M%'
		) ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET  device_name_given_by_validas = 'Smartphone'
		WHERE device_name_given_by_validas IS NULL
		AND automagic = 1
		AND data_plan_charges = 1 ") );

		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET device_name_given_by_validas = 'Basic Phone'
		WHERE  device_name_given_by_validas IS NULL
		AND automagic = 1
		AND voice_plan_charges = 1 ") );


		$results[] = $wpdb->query( $wpdb->prepare(
		"UPDATE $pc_table
		SET device_name_given_by_validas
		 = 'International Smartphone'
		WHERE device_name_given_by_validas IS NULL
		AND automagic = 1
		AND international_data_plan_charges = 1 ") );           
	}


	public static function get_uit_data($mcc_userid,$file_urls,$carrier){

		global $table_prefix, $wpdb;

		$savings_formula_percent = get_option('mcc_automated_savings_formula_percent');

		if($carrier=='verizon'){

			$returnData = array();

			$rd_tblname = 'verizon_rdd';
			$rd_table = $table_prefix . $rd_tblname;

			$dr_tblname = 'device_report';
			$dr_table = $table_prefix . $dr_tblname;

			$pc_tblname = 'plan_categories';
			$pc_table = $table_prefix . $pc_tblname;

			$uit_tblname = 'uit_vzw';
			$uit_table = $table_prefix . $uit_tblname;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(used+0)/1024/1024)) as kilo
			FROM $rd_table 
			WHERE item_category = 'data'
			AND item_type LIKE '%kilo%'
			AND usage_period NOT LIKE '%prev%' AND mcc_user_id = %s",$mcc_userid ) );

			$returnData['kilo'] = $val[0]->kilo;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(used+0)/1024)) as mega
			FROM $rd_table 
			WHERE item_category = 'data'
			AND item_type LIKE '%mega%'
			AND usage_period NOT LIKE '%prev%' AND mcc_user_id = %s",$mcc_userid ) );

			$returnData['mega'] = $val[0]->mega;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(used+0))) as giga
			FROM $rd_table 
			WHERE item_category = 'data'
			AND item_type LIKE '%giga%'
			AND usage_period NOT LIKE '%prev%' AND mcc_user_id = %s",$mcc_userid ) );

			$returnData['giga'] = $val[0]->giga;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT round(SUM(cost),2) as PhoneNumberTotalCost FROM 
			$rd_table WHERE 
			item_description NOT 
			LIKE '%payment received%' 
			AND mcc_user_id = %s",$mcc_userid ) );
				
			$returnData['PhoneNumberTotalCost'] = $val[0]->PhoneNumberTotalCost;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COUNT(DISTINCT phone_number_invoice) as phone_number_count FROM $rd_table where mcc_user_id = %s",$mcc_userid ) );

			$returnData['phone_number_count'] = $val[0]->phone_number_count;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COUNT(DISTINCT phone_number_invoice) * $savings_formula_percent as savings FROM $rd_table where mcc_user_id = %s",$mcc_userid ) );

			$returnData['savings'] = $val[0]->savings;
		
			$returnData['rec_id'] = $mcc_userid;

			$returnData['rdd_report'] = isset($file_urls['verizon_rdd'])? $file_urls['verizon_rdd']:'';
			
			$returnData['device_report'] = isset($file_urls['device_report'])? $file_urls['device_report']:'';
		
		}
		elseif($carrier=='att'){
			
			$returnData = array();
			$rd_tblname = 'att_rdd';
			$rd_table = $table_prefix . $rd_tblname;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT (SUM(ROUND(total_charge,2))) as total_charge FROM $rd_table WHERE total_charge <> 'NULL' AND
			section_2 = 'Total Charges' AND mcc_user_id = %s",$mcc_userid ) );

			$total_charge = round($val[0]->total_charge);

			$returnData['PhoneNumberTotalCost'] = $total_charge;
			

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0)/1024/1024)) as kilo1
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate LIKE '%kb' AND mcc_user_id = %s",$mcc_userid ) );

			$kilo1 = $val[0]->kilo1;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0)/1024)) as mega1
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate LIKE '%mb' AND mcc_user_id = %s",$mcc_userid ) );

			$mega1 = $val[0]->mega1;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0))) as giga1
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate LIKE '%gb' AND mcc_user_id = %s",$mcc_userid ) );

			$giga1 = $val[0]->giga1;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0)/1024)) as mega2
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate LIKE '%mb'
			AND msg_kb_mb_used = '0' AND mcc_user_id = %s",$mcc_userid ) );

			$mega2 = $val[0]->mega2;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0)/1024/1024)) as kilo2
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate = '$/msg'
			AND section_5 = 'gprs kb' AND mcc_user_id = %s",$mcc_userid ) );

			$kilo2 = $val[0]->kilo2;


			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(billed_msg_kb_mb)/1024/1024)) AS kilo3
			FROM $rd_table
			WHERE billed_msg_kb_mb <> 'NULL'
			AND	billed_rate = '$/kb'
			AND section_5 = 'gprs' AND mcc_user_id = %s",$mcc_userid ));

			$kilo3 = $val[0]->kilo3;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0)/1024)) as mega3
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate = '$/msg'
			AND section_5 = 'gprs mb' AND mcc_user_id = %s",$mcc_userid ) );

			$mega3 = $val[0]->mega3;	

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COALESCE(SUM(ROUND(msg_kb_mb_used+0))) as giga2
			FROM $rd_table
			WHERE msg_kb_mb_used <> 'NULL'
			AND billed_rate = '$/msg'
			AND section_5 = 'gprs gb' AND mcc_user_id = %s",$mcc_userid ) );

			$giga2 = $val[0]->giga2;		

			$giga = floatval($kilo1)+floatval($mega1)+floatval($giga1)+floatval($kilo2)+floatval($mega2)+floatval($giga2)+floatval($mega3)+floatval($kilo3);

			$giga = round($giga);

			$returnData['giga'] = $giga;

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COUNT(DISTINCT wireless_number) as wireless_number_count FROM $rd_table 
			WHERE wireless_number <> '' AND mcc_user_id = %s",$mcc_userid ) );
			
			$returnData['phone_number_count'] = $val[0]->wireless_number_count;	

			$val = $wpdb->get_results( 
			$wpdb->prepare(
			"SELECT COUNT(DISTINCT wireless_number) * $savings_formula_percent as savings FROM $rd_table WHERE wireless_number <> '' AND mcc_user_id = %s",$mcc_userid ) );
		
			$returnData['savings'] = $val[0]->savings;

			$returnData['rec_id'] = $mcc_userid;

			$returnData['rdd_report'] = $file_urls['att_rdd'];


		}

		wp_send_json($returnData);
	}

	/**
	 * Handle form submit part 2
	 */
	function handle_form_1_step_2_submit($data) {

		global $table_prefix, $wpdb;

		$data = array( 
			'recID' 					=> sanitize_text_field($_POST['recID']),
			'PhoneNumberTotalCost' 		=> sanitize_text_field($_POST['PhoneNumberTotalCost']),
			'phone_number_count' 		=> sanitize_text_field($_POST['phone_number_count']),
			'savings' 					=> sanitize_text_field($_POST['savings']),
			'giga' 						=> sanitize_text_field($_POST['giga']),
			'rdd_report' 				=> sanitize_text_field($_POST['rdd_report']),
			'device_report' 			=> sanitize_text_field($_POST['device_report']),
		);
		$returnData = array();
		
		$tblname = 'mcc_automated';
		$wp_table = $table_prefix . $tblname;

		$user_id = $data['recID'];

		$wpdb->update($wp_table, array(
			'total_cost' 		=> $data['PhoneNumberTotalCost'],
			'total_phone_count' => $data['phone_number_count'],
			'savings' 			=> $data['savings'],
			'data_usage_gb' 	=> $data['giga'],
			'rdd_report' 		=> $data['rdd_report'],
			'device_report' 	=> $data['device_report'],

		), array('id' => $user_id));

		$row = $wpdb->get_row("SELECT * FROM ".$wp_table." WHERE id = ".$user_id);

		$customerRow = $wpdb->get_row("SELECT * FROM ".$wp_table." WHERE id = ".$user_id, 'ARRAY_A');
		$customerRow['action'] = 'mcc_automated_sync_customer';
		$customerRow['sourceDomain'] = get_site_url();
		$result = wp_remote_post('https://wirelessbutlerserver.com/wp/wp-admin/admin-post.php', 
			array(
				'method' 		=> 'POST',
				'timeout'     	=> 45,
				'httpversion' 	=> '1.0',
				'sslverify' 	=> false,
				'body' 			=> $customerRow
			)
		);


		//Name that will be used with email to send info to customer
		$adminName = get_option('mcc_automated_customer_name');

		//Email that will be used to send info to customer
		$adminEmail = get_option('mcc_automated_customer_mail');

		//send email notification
		$notificationEmail = get_option('mcc_automated_notif_mail');

		if($notificationEmail != NULL && $notificationEmail != '') {
			$emailContent = get_option("mcc_automated_form_notif_template");
			
			
			$carrier = str_replace("att","AT&T",$row->carrier);
			
			$emailContent = str_replace('[FIRST_NAME]', $row->first_name, $emailContent);
			$emailContent = str_replace('[LAST_NAME]', $row->last_name, $emailContent);
			$emailContent = str_replace('[EMAIL]', $row->email, $emailContent);
			$emailContent = str_replace('[PHONE]', $row->phone, $emailContent);
			$emailContent = str_replace('[CARRIER]', $carrier, $emailContent);
			$emailContent = str_replace('[PHONE_NUMBER_TOTAL_COST]', $row->total_cost,$emailContent);
			$emailContent = str_replace('[PHONE_NUMBER_COUNT]', $row->total_phone_count, $emailContent);
			$emailContent = str_replace('[SAVINGS]', $row->savings, $emailContent);
			$emailContent = str_replace('[DATA_USAGE_GB]', $row->data_usage_gb, $emailContent);
			$emailContent = str_replace('[RDD_LINK]', $row->rdd_report, $emailContent);
			$emailContent = str_replace('[DEVICE_REPORT_LINK]', $row->device_report, $emailContent);
			wp_mail($notificationEmail, 'Mcc Automated Email Notification', $emailContent);
		}

		//send email to user
		$content = 'Hi '.$row->first_name.',

		Thanks for using Mcc Automated to analyze your mobile bill! 
		We are reviewing your bill and will reach out to you within the next 48 hours.
		If you have any questions, please reach out to our CTO Greg Urban, at gurban@validas.com.

		Regards
		Validas LLC
		';
		
		$headers = array('From: '.$adminName.' <'.$adminEmail.'>');
		wp_mail($row->email, 'Thanks from Mcc Automated', $content, $headers);

		wp_send_json($returnData);
	}
	
}
