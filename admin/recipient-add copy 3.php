<?php
// recipient-add.php

// Function to display Add Recipient form
function mmb_add_recipient_form() {
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
                <th><label for="personal_greeting"><?php esc_html_e('Personalized Greeting', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="personal_greeting" name="personal_greeting" /></td>
            </tr>
            <tr>
                <th><label for="personal_subject"><?php esc_html_e('Personalized Subject', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="personal_subject" name="personal_subject" /></td>
            </tr>
            <tr>
                <th><label for="personal_message"><?php esc_html_e('Personalized Message', 'multimail-blaster'); ?></label></th>
                <td><textarea id="personal_message" name="personal_message"></textarea></td>
            </tr>
        </table>
        <?php submit_button(__('Add Recipient', 'multimail-blaster')); ?>
    </form>

    <!-- CSV Upload Section -->
    <h3><?php esc_html_e('Upload CSV for Multiple Recipients', 'multimail-blaster'); ?></h3>
    <form method="POST" enctype="multipart/form-data" action="">
        <input type="file" name="recipients_csv" accept=".csv" required />
        <?php submit_button(__('Upload CSV', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Call the function to display the form
mmb_add_recipient_form();
