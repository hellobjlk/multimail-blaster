<?php
// campaign-add.php

function mmb_add_campaign_form() {
    global $wpdb;
    $campaigns_table = $wpdb->prefix . 'mmb_campaigns';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign_name'])) {
        $campaign_name = sanitize_text_field($_POST['campaign_name']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']); // Rich text editor support

        // Validate the form inputs
        if (!empty($campaign_name) && !empty($subject) && !empty($message)) {
            // Insert the new campaign into the database
            $wpdb->insert(
                $campaigns_table,
                array(
                    'campaign_name' => $campaign_name,
                    'subject' => $subject,
                    'message' => $message,
                    'created_at' => current_time('mysql')
                )
            );

            // Success message
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Campaign added successfully!', 'multimail-blaster') . '</p></div>';
        } else {
            // Error message
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Please fill in all required fields.', 'multimail-blaster') . '</p></div>';
        }
    }

    // Display the campaign creation form
    ?>
    <h2><?php esc_html_e('Add New Campaign', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="campaign_name"><?php esc_html_e('Campaign Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="campaign_name" name="campaign_name" required /></td>
            </tr>
            <tr>
                <th><label for="subject"><?php esc_html_e('Subject', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="subject" name="subject" required /></td>
            </tr>
            <tr>
                <th><label for="message"><?php esc_html_e('Message', 'multimail-blaster'); ?></label></th>
                <td>
                    <?php
                    wp_editor('', 'message', array(
                        'textarea_name' => 'message',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                    ));
                    ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Create Campaign', 'multimail-blaster')); ?>
    </form>
    <?php
}

mmb_add_campaign_form();
?>
