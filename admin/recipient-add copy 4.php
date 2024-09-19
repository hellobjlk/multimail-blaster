<?php
// recipient-add.php

// Function to display Add Recipient form
function mmb_add_recipient_form() {
    global $wpdb;

    // Define your table names
    $recipients_table = $wpdb->prefix . 'mmb_recipients';
    $recipient_group_relationship_table = $wpdb->prefix . 'mmb_recipient_group_relationship';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if adding a recipient manually
        if (isset($_POST['recipient_name'])) {
            $recipient_name = sanitize_text_field($_POST['recipient_name']);
            $recipient_email = sanitize_email($_POST['recipient_email']);
            $personal_greeting = sanitize_text_field($_POST['personal_greeting']);
            $personal_subject = sanitize_text_field($_POST['personal_subject']);
            $personal_message = sanitize_textarea_field($_POST['personal_message']);
            $group_id = sanitize_text_field($_POST['recipient_group']); // Group selected for the recipient

            // Insert recipient into the database
            $wpdb->insert(
                $recipients_table,
                array(
                    'recipient_name' => $recipient_name,
                    'recipient_email' => $recipient_email,
                    'personal_greeting' => $personal_greeting,
                    'personal_subject' => $personal_subject,
                    'personal_message' => $personal_message,
                    'owner_id' => get_current_user_id()
                )
            );

            // Get recipient ID and assign to group
            $recipient_id = $wpdb->insert_id;
            if (!empty($group_id)) {
                $wpdb->insert(
                    $recipient_group_relationship_table,
                    array(
                        'recipient_id' => $recipient_id,
                        'group_id' => $group_id
                    )
                );
            }

            echo '<div class="notice notice-success is-dismissible"><p>' . __('Recipient added successfully!', 'multimail-blaster') . '</p></div>';
        }

        // Check if a CSV is uploaded
        if (isset($_FILES['recipients_csv'])) {
            $csv_file = $_FILES['recipients_csv']['tmp_name'];
            $group_id = sanitize_text_field($_POST['recipient_group_csv']); // Group selected for all CSV recipients

            if (!empty($group_id)) {
                if (($handle = fopen($csv_file, 'r')) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        // Skip the header row
                        if ($data[0] === 'name') continue;

                        $recipient_name = sanitize_text_field($data[0]);
                        $recipient_email = sanitize_email($data[1]);
                        $personal_greeting = sanitize_text_field($data[2]);
                        $personal_subject = sanitize_text_field($data[3]);
                        $personal_message = sanitize_textarea_field($data[4]);

                        // Insert recipient into the database
                        $wpdb->insert(
                            $recipients_table,
                            array(
                                'recipient_name' => $recipient_name,
                                'recipient_email' => $recipient_email,
                                'personal_greeting' => $personal_greeting,
                                'personal_subject' => $personal_subject,
                                'personal_message' => $personal_message,
                                'owner_id' => get_current_user_id()
                            )
                        );

                        // Get recipient ID and assign to group
                        $recipient_id = $wpdb->insert_id;
                        $wpdb->insert(
                            $recipient_group_relationship_table,
                            array(
                                'recipient_id' => $recipient_id,
                                'group_id' => $group_id
                            )
                        );
                    }
                    fclose($handle);
                }

                echo '<div class="notice notice-success is-dismissible"><p>' . __('Recipients added successfully from CSV!', 'multimail-blaster') . '</p></div>';
            }
        }
    }

    // Display the Add Recipient form
    ?>
    <h2><?php esc_html_e('Add Recipient', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="recipient_name"><?php esc_html_e('Recipient Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="recipient_name" name="recipient_name" required /></td>
            </tr>
            <tr>
                <th><label for="recipient_email"><?php esc_html_e('Recipient Email', 'multimail-blaster'); ?></label></th>
                <td><input type="email" id="recipient_email" name="recipient_email" required /></td>
            </tr>
            <tr>
                <th><label for="personal_greeting"><?php esc_html_e('Personal Greeting', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="personal_greeting" name="personal_greeting" /></td>
            </tr>
            <tr>
                <th><label for="personal_subject"><?php esc_html_e('Personal Subject', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="personal_subject" name="personal_subject" /></td>
            </tr>
            <tr>
                <th><label for="personal_message"><?php esc_html_e('Personal Message', 'multimail-blaster'); ?></label></th>
                <td><textarea id="personal_message" name="personal_message"></textarea></td>
            </tr>
            <tr>
                <th><label for="recipient_group"><?php esc_html_e('Assign to Group', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="recipient_group" name="recipient_group">
                        <option value=""><?php esc_html_e('Select a Group', 'multimail-blaster'); ?></option>
                        <?php
                        // Fetch and display recipient groups
                        $groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_recipient_groups");
                        foreach ($groups as $group) {
                            echo '<option value="' . esc_attr($group->id) . '">' . esc_html($group->group_name) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Add Recipient', 'multimail-blaster')); ?>
    </form>

    <!-- CSV Upload Section -->
    <h3><?php esc_html_e('Upload CSV for Multiple Recipients', 'multimail-blaster'); ?></h3>
    <form method="POST" enctype="multipart/form-data" action="">
        <input type="file" name="recipients_csv" accept=".csv" required />
        <label for="recipient_group_csv"><?php esc_html_e('Assign all recipients to Group', 'multimail-blaster'); ?></label>
        <select id="recipient_group_csv" name="recipient_group_csv">
            <option value=""><?php esc_html_e('Select a Group', 'multimail-blaster'); ?></option>
            <?php
            // Fetch and display recipient groups
            foreach ($groups as $group) {
                echo '<option value="' . esc_attr($group->id) . '">' . esc_html($group->group_name) . '</option>';
            }
            ?>
        </select>
        <?php submit_button(__('Upload CSV', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Call the function to display the form
mmb_add_recipient_form();
?>
