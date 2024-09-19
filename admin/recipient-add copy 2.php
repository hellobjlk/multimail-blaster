<?php
// recipient-add.php

function mmb_add_recipient_form() {
    ?>
    <h2><?php esc_html_e('Add Recipient', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="recipient_name"><?php esc_html_e('Recipient Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="recipient_name" name="recipient_name" /></td>
            </tr>
            <tr>
                <th><label for="recipient_email"><?php esc_html_e('Recipient Email', 'multimail-blaster'); ?></label></th>
                <td><input type="email" id="recipient_email" name="recipient_email" required /></td>
            </tr>
            <tr>
                <th><label for="personalized_greeting"><?php esc_html_e('Personalized Greeting', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="personalized_greeting" name="personalized_greeting" /></td>
            </tr>
            <tr>
                <th><label for="personalized_subject"><?php esc_html_e('Personalized Subject', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="personalized_subject" name="personalized_subject" /></td>
            </tr>
            <tr>
                <th><label for="personalized_message"><?php esc_html_e('Personalized Message', 'multimail-blaster'); ?></label></th>
                <td><textarea id="personalized_message" name="personalized_message" rows="5"></textarea></td>
            </tr>
            <tr>
                <th><label for="recipient_group"><?php esc_html_e('Assign to Group', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="recipient_group" name="recipient_group">
                        <option value=""><?php esc_html_e('Select Group', 'multimail-blaster'); ?></option>
                        <?php
                        // Populate groups dynamically from the database
                        global $wpdb;
                        $groups = $wpdb->get_results("SELECT id, group_name FROM {$wpdb->prefix}mmb_recipient_groups");
                        foreach ($groups as $group) {
                            echo '<option value="' . esc_attr($group->id) . '">' . esc_html($group->group_name) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Recipient', 'multimail-blaster')); ?>
    </form>

    <!-- CSV Upload Section -->
    <h3><?php esc_html_e('Upload CSV for Multiple Recipients', 'multimail-blaster'); ?></h3>
    <form method="POST" enctype="multipart/form-data" action="">
        <input type="file" name="recipients_csv" accept=".csv" required />
        <?php submit_button(__('Upload CSV', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['recipient_email'])) {
    global $wpdb;

    $data = array(
        'recipient_name' => sanitize_text_field($_POST['recipient_name']),
        'recipient_email' => sanitize_email($_POST['recipient_email']),
        'personalized_greeting' => sanitize_text_field($_POST['personalized_greeting']),
        'personalized_subject' => sanitize_text_field($_POST['personalized_subject']),
        'personalized_message' => wp_kses_post($_POST['personalized_message']),
        'recipient_group_id' => intval($_POST['recipient_group']),
        'owner_id' => get_current_user_id(),
    );

    // Insert into recipients table
    $wpdb->insert($wpdb->prefix . 'mmb_recipients', $data);
}
