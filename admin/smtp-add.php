<?php
// SMTP Add page for adding new SMTP accounts

// Handle form submission for adding a new SMTP account
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;

    // Sanitize input data
    $smtp_host = sanitize_text_field($_POST['smtp_host']);
    $smtp_port = intval($_POST['smtp_port']);
    $smtp_username = sanitize_text_field($_POST['smtp_username']);
    $smtp_password = sanitize_text_field($_POST['smtp_password']);
    $smtp_encryption = sanitize_text_field($_POST['smtp_encryption']);

    // Table name
    $table_name = $wpdb->prefix . 'mmb_smtp_accounts';

    // Insert data into the database
    $wpdb->insert(
        $table_name,
        [
            'smtp_host' => $smtp_host,
            'smtp_port' => $smtp_port,
            'smtp_username' => $smtp_username,
            'smtp_password' => $smtp_password,
            'smtp_encryption' => $smtp_encryption,
        ]
    );

    // Check if there's an error during insertion
    if ($wpdb->last_error) {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Error saving SMTP account: ', 'multimail-blaster') . esc_html($wpdb->last_error) . '</p></div>';
    } else {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('SMTP account saved successfully!', 'multimail-blaster') . '</p></div>';
    }
}
?>

<!-- HTML form for adding a new SMTP account -->
<div class="wrap">
    <h2><?php esc_html_e('Add SMTP Account', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="smtp_host"><?php esc_html_e('SMTP Host', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="smtp_host" name="smtp_host" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_port"><?php esc_html_e('SMTP Port', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="smtp_port" name="smtp_port" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_username"><?php esc_html_e('SMTP Username', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="smtp_username" name="smtp_username" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_password"><?php esc_html_e('SMTP Password', 'multimail-blaster'); ?></label></th>
                <td><input type="password" id="smtp_password" name="smtp_password" required /></td>
            </tr>
            <tr>
                <th><label for="smtp_encryption"><?php esc_html_e('SMTP Encryption', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="smtp_encryption" name="smtp_encryption">
                        <option value="ssl">SSL</option>
                        <option value="tls">TLS</option>
                        <option value="none">None</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save SMTP Account', 'multimail-blaster')); ?>
    </form>
</div>
