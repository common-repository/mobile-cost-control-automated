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
                <th scope="row"><?php _e( 'Savings per line', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_savings_formula_percent" value="<?php echo esc_attr( get_option('mcc_automated_savings_formula_percent') ); ?>" />
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
        
        <input type="hidden" name="mcc_automated_customer_name" value="<?php echo esc_attr( get_option('mcc_automated_customer_name') ); ?>" />

        <input type="hidden" name="mcc_automated_customer_mail" value="<?php echo esc_attr( get_option('mcc_automated_customer_mail') ); ?>" />

        <input type="hidden" name="mcc_automated_notif_mail" value="<?php echo esc_attr( get_option('mcc_automated_notif_mail') ); ?>" />
       
        <input type="hidden" name="mcc_automated_form_notif_template" value="<?php echo esc_html(get_option('mcc_automated_form_notif_template')); ?>" />
       
        <input type="hidden" name="mcc_automated_giga_usage_label" value="<?php echo esc_attr( get_option('mcc_automated_giga_usage_label') ); ?>" />



        <?php submit_button(); ?>

    </form>
</div>