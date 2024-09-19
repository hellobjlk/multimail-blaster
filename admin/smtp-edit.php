<?php
// smtp-edit.php

// Function to handle SMTP account editing
function mmb_edit_smtp_account($smtp_id) {
    global $wpdb;
    $smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';

    // Get the SMTP account data
    $smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM $smtp_table WHERE id = %d", $smtp_id));

    if (!$smtp_account) {
        echo '<div class="notice notice-error"><p>' . esc_html__('SMTP Account not found.', 'multimail-blaster') . '</p></div>';
        return;
    }

    // Handle form submission for updating SMTP account
    if (isset($_POST['mmb_update_smtp_account'])) {
        $smtp_host = sanitize_text_field($_POST['smtp_host']);
        $smtp_port = intval($_POST['smtp_port']);
        $smtp_username = sanitize_text_field($_POST['smtp_username']);
        $smtp_password = sanitize_text_field($_POST['smtp_password']);
        $encryption_type = sanitize_text_field($_POST['encryption_type']);
        $smtp_group_id = intval($_POST['smtp_group_id']);

        // Update the SMTP account in the database
        $wpdb->update(
            $smtp_table,
            array(
                'smtp_host' => $smtp_host,
                'smtp_port' => $smtp_port,
                'smtp_username' => $smtp_username,
                'smtp_password' => $smtp_password,
                'encryption_type' => $encryption_type,
                'smtp_group_id' => $smtp_group_id,
            ),
            array('id' => $smtp_id)
        );

        echo '<div class="notice notice-success"><p>' . esc_html__('SMTP Account updated successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Display the form with the current SMTP account details
    ?>
    <h2><?php esc_html_e('Edit SMTP Account', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="smtp_host"><?php esc_html_e('SMTP Host', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="smtp_host" name="smtp_host" value="<?php echo esc_attr($smtp_account->smtp_host); ?>" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_port"><?php esc_html_e('SMTP Port', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="smtp_port" name="smtp_port" value="<?php echo esc_attr($smtp_account->smtp_port); ?>" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_username"><?php esc_html_e('SMTP Username', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="smtp_username" name="smtp_username" value="<?php echo esc_attr($smtp_account->smtp_username); ?>" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_password"><?php esc_html_e('SMTP Password', 'multimail-blaster'); ?></label></th>
                <td><input type="password" id="smtp_password" name="smtp_password" value="<?php echo esc_attr($smtp_account->smtp_password); ?>" required /></td>
            </tr>
            <tr>
                <th><label for="encryption_type"><?php esc_html_e('Encryption Type', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="encryption_type" name="encryption_type">
                        <option value="none" <?php selected($smtp_account->encryption_type, 'none'); ?>><?php esc_html_e('None', 'multimail-blaster'); ?></option>
                        <option value="ssl" <?php selected($smtp_account->encryption_type, 'ssl'); ?>><?php esc_html_e('SSL', 'multimail-blaster'); ?></option>
                        <option value="tls" <?php selected($smtp_account->encryption_type, 'tls'); ?>><?php esc_html_e('TLS', 'multimail-blaster'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="smtp_group_id"><?php esc_html_e('SMTP Group', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="smtp_group_id" name="smtp_group_id">
                        <option value="0"><?php esc_html_e('No Group', 'multimail-blaster'); ?></option>
                        <?php
                        $smtp_groups = $wpdb->get_results("SELECT id, group_name FROM {$wpdb->prefix}mmb_smtp_groups");
                        foreach ($smtp_groups as $group) {
                            echo '<option value="' . esc_attr($group->id) . '" ' . selected($smtp_account->smtp_group_id, $group->id, false) . '>' . esc_html($group->group_name) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <input type="hidden" name="mmb_update_smtp_account" value="1" />
        <?php submit_button(__('Update SMTP Account', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Check for "edit" action and display edit form if needed
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['smtp_id'])) {
    mmb_edit_smtp_account(intval($_GET['smtp_id']));
} else {
    // Display normal account management here
}
?>
