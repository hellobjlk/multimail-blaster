<?php
// smtp-add.php

// Function to display Add SMTP Account form
function mmb_add_smtp_form() {
    ?>
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
        </table>
        <?php submit_button(__('Save SMTP Account', 'multimail-blaster')); ?>
    </form>

    <!-- CSV Upload Section -->
    <h3><?php esc_html_e('Upload CSV for Multiple SMTP Accounts', 'multimail-blaster'); ?></h3>
    <form method="POST" enctype="multipart/form-data" action="">
        <input type="file" name="smtp_csv" accept=".csv" required />
        <?php submit_button(__('Upload CSV', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Call the function to display the form
mmb_add_smtp_form();
?>
