<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    mcc_automated
 * @subpackage mcc_automated/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="mcc_automated_form_1">
   <div class="mcc_automated_form_1_step_1">
      <div class="mcc-auto-title_bill">
         <h3>
            <?php echo esc_attr(get_option('mcc_automated_greeting')); ?> <br> 
            <span><?php echo esc_attr(get_option('mcc_automated_first_heading')); ?></span>
         </h3>
         <div class="mcc-auto-title-heading"><?php echo esc_attr(get_option('mcc_automated_form_instructions')); ?></div>
      </div>
      <form method="post" enctype="multipart/form-data" id="mccAutomatedForm1Step1">
         <div class="mcc-auto-bill-form">
            <div class="mcc-auto-formrow">
               <div class="col-6 mcc-auto-cl6">
                  <label for="fname"><?php _e( 'First Name', 'mcc_automated' ); ?>:</label>
                  <input type="text" class="mcc-auto-formcntrl" name="fname" id="fname" placeholder="<?php _e( 'Enter your first name', 'mcc_automated' ); ?>" value="" required>
               </div>
               <div class="col-6 mcc-auto-cl6">
                  <label for="fname"><?php _e( 'Last Name', 'mcc_automated' ); ?>:</label>
                  <input type="text" class="mcc-auto-formcntrl" name="lname" placeholder="<?php _e( 'Enter your last name', 'mcc_automated' ); ?>" id="lname" value="" required>
               </div>
               <div class="col-6 mcc-auto-cl6">
                  <label for="fname"><?php _e( 'Mail Address', 'mcc_automated' ); ?>:</label>
                  <input type="email" class="mcc-auto-formcntrl" name="email" placeholder="<?php _e( 'Enter your email', 'mcc_automated' ); ?>" value="" required>
               </div>
               <div class="col-6 mcc-auto-cl6">
                  <label for="fname"><?php _e( 'Phone Number', 'mcc_automated' ); ?>:</label>
                  <input type="text" class="mcc-auto-formcntrl" name="phone" placeholder="<?php _e( 'Enter your phone', 'mcc_automated' ); ?>" value="">
               </div>
               <div class="col-12 mcc-auto-cl12">
                  <div class="mcc-auto-input-select mcc-auto-inputmrgin">
                     <label for="service"><?php _e( 'Choose your carrier', 'mcc_automated' ); ?>:</label>
                     <div class="mcc-auto-formcntrl">
                        <select name="carrier"  required>
                           <option><?php _e( 'Choose your carrier', 'mcc_automated' ); ?></option>
                           <option value="verizon">Verizon</option>
                           <option value="att">AT&T</option>
                        </select>
                        <div class="dropDownSelect2"></div>
                     </div>
                  </div>
               </div>

               <div class="mcc-auto-dropzone-section" > 
                  <div class="fallback">
                     <i class="fas fa-file-pdf"></i>
                     <input type="file" name="file" multiple="multiple">
                  </div>
               </div>
               <div class="mcc-auto-submit-btn-sec">
                  <button type="submit" class="mcc-auto-btn mcc-auto-submit-btn">
                     <span class="loader mcc-auto-hide">
                        <img src="<?php echo esc_url(plugin_dir_url(__DIR__).'images/spinner.gif'); ?>" class="mcc-auto-loader-img"/>
                        <?php _e( 'Please Wait', 'mcc_automated' ); ?>
                     </span>
                     <span class="btn-element"><?php _e( 'Submit for Real-Time Analysis', 'mcc_automated' ); ?></span>
                  </button>
               </div>
            </div>
         </div>
      </form>
   </div>
   <div class="mcc_automated_form_1_step_2 mcc-auto-hide">
        <h4><?php echo esc_attr(get_option('mcc_automated_second_heading')); ?></h4>
        <form id="mccAutomatedForm1Step2">
            <div class="form-group row">
                <label class="col-sm-6 col-form-label"><?php echo esc_attr(get_option('mcc_automated_total_cost_label')); ?></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" list="PhoneNumberTotalCostList" id="PhoneNumberTotalCost">
                    <datalist id="PhoneNumberTotalCostList"></datalist>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-6 col-form-label"><?php echo esc_attr(get_option('mcc_automated_total_phone_count_label')); ?></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="phone_number_count" list="phone_number_countList">
                    <datalist id="phone_number_countList"></datalist>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-6 col-form-label"><?php echo esc_attr(get_option('mcc_automated_savings_label')); ?></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="savings" list="savingsList">
                    <datalist id="savingsList"></datalist>
                </div>
            </div>
          
            <div class="form-group row">
                <label class="col-sm-6 col-form-label"><?php echo esc_attr(get_option('mcc_automated_giga_usage_label')); ?></label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="usedData" list="usedDataList">
                    <datalist id="usedDataList"></datalist>
                </div>
            </div>
            
            <div class="mcc-auto-submit-btn-sec">
                  <input type="hidden" id="recID">
                  <input type="hidden" id="rdd_report">
                  <input type="hidden" id="device_report">
                  
                  <button type="submit" class="mcc-auto-btn mcc-auto-submit-btn">
                     <span class="loader mcc-auto-hide">
                        <img src="<?php echo esc_url(plugin_dir_url(__DIR__).'images/spinner.gif'); ?>" class="mcc-auto-loader-img"/>
                        <?php _e( 'Please Wait', 'mcc_automated' ); ?>
                     </span>
                     <span class="btn-element"><?php _e( 'Submit', 'mcc_automated' ); ?></span>
                  </button>
               </div>
        </form>
        <div style="color:red"><?php _e( 'Powered by Validas', 'mcc_automated' ); ?></div>
        <div><?php echo esc_attr(get_option('mcc_automated_form2_next_step_msg')); ?></div>
    </div>
    <div class="mcc_automated_form_1_step_2_message mcc-auto-hide"><?php _e( 'Thanks for submitting. Email has been sent.', 'mcc_automated' ); ?></div>
</div>