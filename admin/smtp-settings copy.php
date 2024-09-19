<?php
global $wpdb;
$smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';

// Fetch all SMTP accounts
$smtp_accounts = $wpdb->get_results("SELECT * FROM $smtp_table");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $smtp_id = intval($_POST['smtp_id']);
    $daily_limit = intval($_POST['daily_limit']);
    $batch_size = intval($_POST['batch_size']);
    $rotation_delay = intval($_POST['rotation_delay']);

    // Update SMTP settings in the database
    $wpdb->update(
        $smtp_table,
        [
            'daily_limit' => $daily_limit,
            'batch_size' => $batch_size,
            'rotation_delay' => $rotation_delay
        ],
        ['id' => $smtp_id]
    );

    echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings updated successfully!', 'multimail-blaster') . '</p></div>';
}

?>
<h2><?php esc_html_e('SMTP Settings', 'multimail-blaster'); ?></h2>
<form method="POST">
    <table class="form-table">
        <tr>
            <th><label for="smtp_id"><?php esc_html_e('Select SMTP Account', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="smtp_id" name="smtp_id" required>
                    <option value=""><?php esc_html_e('Select an SMTP Account', 'multimail-blaster'); ?></option>
                    <?php foreach ($smtp_accounts as $smtp) : ?>
                        <option value="<?php echo esc_attr($smtp->id); ?>"><?php echo esc_html($smtp->smtp_host); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="daily_limit"><?php esc_html_e('Daily Limit', 'multimail-blaster'); ?></label></th>
            <td><input type="number" id="daily_limit" name="daily_limit" required /></td>
        </tr>
        <tr>
            <th><label for="batch_size"><?php esc_html_e('Batch Size', 'multimail-blaster'); ?></label></th>
            <td><input type="number" id="batch_size" name="batch_size" required /></td>
        </tr>
        <tr>
            <th><label for="rotation_delay"><?php esc_html_e('Rotation Delay (in seconds)', 'multimail-blaster'); ?></label></th>
            <td><input type="number" id="rotation_delay" name="rotation_delay" required /></td>
        </tr>
    </table>
    <?php submit_button(__('Save Settings', 'multimail-blaster')); ?>
</form>
