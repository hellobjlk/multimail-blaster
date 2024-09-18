<?php
// smtp-settings.php

// Function to display SMTP settings form
function mmb_smtp_settings_form() {
    ?>
    <h2><?php esc_html_e('SMTP Settings', 'multimail-blaster'); ?></h2>

    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="batch_size"><?php esc_html_e('Batch Size', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="batch_size" name="batch_size" required /></td>
            </tr>
            <tr>
                <th><label for="daily_limit"><?php esc_html_e('Daily Limit', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="daily_limit" name="daily_limit" required /></td>
            </tr>
            <tr>
                <th><label for="rotation_delay"><?php esc_html_e('Rotation Delay (Seconds)', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="rotation_delay" name="rotation_delay" required /></td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Call the function to display the settings form
mmb_smtp_settings_form();
?>
