<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    Mcc_Automated
 * @subpackage Mcc_Automated/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1><?php _e( 'Mcc Automated Email Notification', 'mcc_automated' ); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields( 'mcc_automated_plugin_options' ); ?>
        <?php do_settings_sections( 'mcc_automated_plugin' ); ?>
        <table class="form-table"> 
            <tr valign="top">
                <!-- This is the mail name(name sent with email) to customer -->
                <th scope="row"><?php _e( 'Customer Email Name', 'mcc_automated' ); ?><div><?php _e( '(Who is sending Email to Customer)', 'mcc_automated' ); ?></div></th>
                <td>
                    <input type="text" name="mcc_automated_customer_name" value="<?php echo esc_attr( get_option('mcc_automated_customer_name') ); ?>" />
                </td>
            </tr>  
            <tr valign="top">
                <!-- This is the mail address that will be used to send mail to customer -->
                <th scope="row"><?php _e( 'Customer Email Address', 'mcc_automated' ); ?><div>
                    <?php _e( '(What address is sending Email to Customer)', 'mcc_automated' ); ?></div></th>
                <td>
                    <input type="text" name="mcc_automated_customer_mail" value="<?php echo esc_attr( get_option('mcc_automated_customer_mail') ); ?>" />
                </td>
            </tr> 
            <tr valign="top">
                <th scope="row"><?php _e( 'Notification Email', 'mcc_automated' ); ?><div><?php _e( '(What email from your company is receiving notification leads)', 'mcc_automated' ); ?></div></th>
                <td>
                    <input type="text" name="mcc_automated_notif_mail" value="<?php echo esc_attr( get_option('mcc_automated_notif_mail') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Email Template', 'mcc_automated' ); ?></th>
                <td>
                    <p><label for="mcc_automated_form_notif_template"><b><?php _e( 'Placeholders', 'mcc_automated' ); ?>:</b>[FIRST_NAME],
                    [LAST_NAME],[EMAIL],[PHONE],[CARRIER]
                    [PHONE_NUMBER_TOTAL_COST],
                    [PHONE_NUMBER_COUNT],[SAVINGS],
                    [DATA_USAGE_GB],
                    [RDD_LINK],
                    [DEVICE_REPORT_LINK]</label></p>
                    <textarea name="mcc_automated_form_notif_template" rows="15" cols="75"  id="mcc_automated_form_notif_template" class="code" spellcheck="false"><?php echo esc_html(get_option('mcc_automated_form_notif_template')); ?></textarea>
                </td>
            </tr>
        </table>

        <input type="hidden" name="mcc_automated_greeting" value="<?php echo esc_attr( get_option('mcc_automated_greeting') ); ?>" />
        
        <input type="hidden" name="mcc_automated_first_heading" value="<?php echo esc_attr( get_option('mcc_automated_first_heading') ); ?>" />
        
        <input type="hidden" name="mcc_automated_form_instructions" value="<?php echo esc_attr( get_option('mcc_automated_form_instructions') ); ?>" />
        
        <input type="hidden" name="mcc_automated_second_heading" value="<?php echo esc_attr( get_option('mcc_automated_second_heading') ); ?>" />
        
        <input type="hidden" name="mcc_automated_total_cost_label" value="<?php echo esc_attr( get_option('mcc_automated_total_cost_label') ); ?>" />
        
        <input type="hidden" name="mcc_automated_total_phone_count_label" value="<?php echo esc_attr( get_option('mcc_automated_total_phone_count_label') ); ?>" />
        
        <input type="hidden" name="mcc_automated_savings_label" value="<?php echo esc_attr( get_option('mcc_automated_savings_label') ); ?>" />

        <input type="hidden" name="mcc_automated_form2_next_step_msg" value="<?php echo esc_attr( get_option('mcc_automated_form2_next_step_msg') ); ?>" />
        
        <input type="hidden" name="mcc_automated_giga_usage_label" value="<?php echo esc_attr( get_option('mcc_automated_giga_usage_label') ); ?>" />

        <input type="hidden" name="mcc_automated_savings_formula_percent" value="<?php echo esc_attr( get_option('mcc_automated_savings_formula_percent') ); ?>" />

        <?php submit_button(); ?>

    </form>
</div>


