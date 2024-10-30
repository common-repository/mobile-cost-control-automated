<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.google.com/
 * @since      1.0.0
 *
 * @package    mcc_automated
 * @subpackage mcc_automated/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1><?php _e( 'Mcc Automated', 'mcc_automated' ); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'mcc_automated_plugin_options' ); ?>
        <?php do_settings_sections( 'mcc_automated_plugin' ); ?>
        <h3><?php _e( 'Form Shortcode', 'mcc_automated' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Shortcode', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text"value="[mcc_automated_form_1]" readonly />
                </td>
            </tr>
        </table>
        <h3><?php _e( 'Step 1 Form Options', 'mcc_automated' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Greeting', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_greeting" value="<?php echo esc_attr( get_option('mcc_automated_greeting') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Heading', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_first_heading" value="<?php echo esc_attr( get_option('mcc_automated_first_heading') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Instructions Label', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_form_instructions" value="<?php echo esc_attr( get_option('mcc_automated_form_instructions') ); ?>" />
                </td>
            </tr>
        </table>
        <h3><?php _e( 'Step 2 Form Options', 'mcc_automated' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Form Heading', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_second_heading" value="<?php echo esc_attr( get_option('mcc_automated_second_heading') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Total Cost Label', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_total_cost_label" value="<?php echo esc_attr( get_option('mcc_automated_total_cost_label') ); ?>" />
                    </td>
                </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Total Phone Count Label', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_total_phone_count_label" value="<?php echo esc_attr( get_option('mcc_automated_total_phone_count_label') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Savings Label', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_savings_label" value="<?php echo esc_attr( get_option('mcc_automated_savings_label') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'GB of Data Used Label', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_giga_usage_label" value="<?php echo esc_attr( get_option('mcc_automated_giga_usage_label') ); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Form2 Next Step Message', 'mcc_automated' ); ?></th>
                <td>
                    <input type="text" name="mcc_automated_form2_next_step_msg" value="<?php echo esc_attr( get_option('mcc_automated_form2_next_step_msg') ); ?>" />
                </td>
            </tr>
           
        </table>
        
        <input type="hidden" name="mcc_automated_notif_mail" value="<?php echo esc_attr( get_option('mcc_automated_customer_name') ); ?>" />
        <input type="hidden" name="mcc_automated_notif_mail" value="<?php echo esc_attr( get_option('mcc_automated_customer_mail') ); ?>" />
        <input type="hidden" name="mcc_automated_notif_mail" value="<?php echo esc_attr( get_option('mcc_automated_notif_mail') ); ?>" />
        <input type="hidden" name="mcc_automated_form_notif_template" value="<?php echo esc_html(get_option('mcc_automated_form_notif_template')); ?>" />
        <input type="hidden" name="mcc_automated_savings_formula_percent" value="<?php echo esc_attr( get_option('mcc_automated_savings_formula_percent') ); ?>" />
        


        <?php submit_button(); ?>

    </form>
</div>