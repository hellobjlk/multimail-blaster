<?php
global $wpdb;
$smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';

// Fetch all SMTP accounts
$smtp_accounts = $wpdb->get_results("SELECT * FROM $smtp_table");

// Initialize variables to store fetched settings for the selected SMTP account
$selected_smtp_account = null;
$daily_limit = '';
$batch_size = '';
$rotation_delay = '';

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['smtp_id'])) {
    $smtp_id = intval($_POST['smtp_id']);
    $daily_limit = isset($_POST['daily_limit']) ? intval($_POST['daily_limit']) : 500;
    $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : 50;
    $rotation_delay = isset($_POST['rotation_delay']) ? intval($_POST['rotation_delay']) : 10;

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

    // Fetch the updated settings for the selected SMTP account
    $selected_smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM $smtp_table WHERE id = %d", $smtp_id));
    if ($selected_smtp_account) {
        $daily_limit = $selected_smtp_account->daily_limit;
        $batch_size = $selected_smtp_account->batch_size;
        $rotation_delay = $selected_smtp_account->rotation_delay;
    }
}

// Fetch the selected SMTP account settings if account selected via GET request
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['smtp_id'])) {
    $smtp_id = intval($_GET['smtp_id']);
    $selected_smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM $smtp_table WHERE id = %d", $smtp_id));

    if ($selected_smtp_account) {
        $daily_limit = $selected_smtp_account->daily_limit;
        $batch_size = $selected_smtp_account->batch_size;
        $rotation_delay = $selected_smtp_account->rotation_delay;
    }
}

?>

<h2><?php esc_html_e('SMTP Settings', 'multimail-blaster'); ?></h2>

<form method="POST">
    <table class="form-table">
        <!-- Select SMTP Account -->
        <tr>
            <th><label for="smtp_id"><?php esc_html_e('Select SMTP Account', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="smtp_id" name="smtp_id" required onchange="this.form.submit()">
                    <option value=""><?php esc_html_e('Select an SMTP Account', 'multimail-blaster'); ?></option>
                    <?php foreach ($smtp_accounts as $smtp) : ?>
                        <option value="<?php echo esc_attr($smtp->id); ?>" <?php selected($smtp->id, isset($smtp_id) ? $smtp_id : ''); ?>>
                            <?php echo esc_html($smtp->smtp_host); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <?php if ($selected_smtp_account): ?>
            <!-- Daily Limit -->
            <tr>
                <th><label for="daily_limit"><?php esc_html_e('Daily Limit', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="daily_limit" name="daily_limit" value="<?php echo esc_attr($daily_limit); ?>" required /></td>
            </tr>

            <!-- Batch Size -->
            <tr>
                <th><label for="batch_size"><?php esc_html_e('Batch Size', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="batch_size" name="batch_size" value="<?php echo esc_attr($batch_size); ?>" required /></td>
            </tr>

            <!-- Rotation Delay -->
            <tr>
                <th><label for="rotation_delay"><?php esc_html_e('Rotation Delay (in seconds)', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="rotation_delay" name="rotation_delay" value="<?php echo esc_attr($rotation_delay); ?>" required /></td>
            </tr>

            <?php submit_button(__('Save Settings', 'multimail-blaster')); ?>
        <?php endif; ?>
    </table>
</form>

<!-- JavaScript to reload form when SMTP account is selected -->
<script>
    document.getElementById('smtp_id').addEventListener('change', function () {
        this.form.submit();
    });
</script>
