<?php
// campaign-add.php

function mmb_add_campaign_form() {
    global $wpdb;

    // Define table names
    $campaigns_table = $wpdb->prefix . 'mmb_campaigns';
    $recipient_groups_table = $wpdb->prefix . 'mmb_recipient_groups';

    // Handle form submission for adding a campaign
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campaign_name'])) {
        $campaign_name = sanitize_text_field($_POST['campaign_name']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']); // Sanitizing for rich text
        $recipient_group = sanitize_text_field($_POST['recipient_group']);

        if (!empty($campaign_name) && !empty($subject) && !empty($message) && !empty($recipient_group)) {
            // Insert the campaign into the database
            $wpdb->insert(
                $campaigns_table,
                array(
                    'campaign_name' => $campaign_name,
                    'subject' => $subject,
                    'message' => $message,
                    'created_at' => current_time('mysql')
                )
            );
            $campaign_id = $wpdb->insert_id;

            // Log success message
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Campaign added successfully!', 'multimail-blaster') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('All fields are required.', 'multimail-blaster') . '</p></div>';
        }
    }

    // Fetch available recipient groups
    $recipient_groups = $wpdb->get_results("SELECT * FROM $recipient_groups_table");

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
                    // WordPress' built-in rich text editor
                    wp_editor('', 'message', array(
                        'textarea_name' => 'message',
                        'media_buttons' => true,
                        'textarea_rows' => 10,
                        'tinymce' => array(
                            'toolbar1' => 'bold italic underline | bullist numlist | blockquote | alignleft aligncenter alignright | link',
                        )
                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="recipient_group"><?php esc_html_e('Recipient Group', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="recipient_group" name="recipient_group" required>
                        <option value=""><?php esc_html_e('Select a Group', 'multimail-blaster'); ?></option>
                        <?php
                        if ($recipient_groups) {
                            foreach ($recipient_groups as $group) {
                                echo '<option value="' . esc_attr($group->id) . '">' . esc_html($group->group_name) . '</option>';
                            }
                        } else {
                            echo '<option value="">' . esc_html__('No groups available', 'multimail-blaster') . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Create Campaign', 'multimail-blaster')); ?>
    </form>
    <?php
}

mmb_add_campaign_form();
