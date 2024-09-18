<?php
// Form to add single recipient or upload a CSV of multiple recipients
function mmb_add_recipient_form() {
    ?>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="recipient_email"><?php esc_html_e('Recipient Email', 'multimail-blaster'); ?></label></th>
                <td><input type="email" id="recipient_email" name="recipient_email" required /></td>
            </tr>
            <tr>
                <th><label for="recipient_name"><?php esc_html_e('Recipient Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="recipient_name" name="recipient_name" /></td>
            </tr>
        </table>
        <?php submit_button(__('Add Recipient', 'multimail-blaster')); ?>
    </form>

    <!-- CSV Upload Section -->
    <h3><?php esc_html_e('Upload CSV for Multiple Recipients', 'multimail-blaster'); ?></h3>
    <form method="POST" enctype="multipart/form-data" action="">
        <input type="file" name="recipient_csv" accept=".csv" required />
        <?php submit_button(__('Upload CSV', 'multimail-blaster')); ?>
    </form>
    <?php
}
mmb_add_recipient_form();
?>
