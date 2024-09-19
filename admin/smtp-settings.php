<?php
// smtp-settings.php

function mmb_get_smtp_settings() {
    ?>
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
                <th><label for="rotation_delay"><?php esc_html_e('Rotation Delay', 'multimail-blaster'); ?></label></th>
                <td><input type="number" id="rotation_delay" name="rotation_delay" required /></td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings', 'multimail-blaster')); ?>
    </form>
    <?php
}
mmb_get_smtp_settings();
