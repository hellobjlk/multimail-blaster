<?php
// smtp-add.php

// Function to handle form submission and CSV upload for SMTP accounts
function mmb_add_smtp_form() {
    global $wpdb; // Use the global WordPress DB object
    $smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';

    // Handle manual form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['smtp_host'])) {
        $smtp_host = sanitize_text_field($_POST['smtp_host']);
        $smtp_port = intval($_POST['smtp_port']);
        $smtp_username = sanitize_text_field($_POST['smtp_username']);
        $smtp_password = sanitize_text_field($_POST['smtp_password']);
        $encryption_type = 'none'; // Default encryption for now, update this as needed

        // Insert the new SMTP account into the database
        $wpdb->insert(
            $smtp_table,
            array(
                'smtp_host'     => $smtp_host,
                'smtp_port'     => $smtp_port,
                'smtp_username' => $smtp_username,
                'smtp_password' => $smtp_password,
                'encryption_type' => $encryption_type // Ensure your table has this column
            ),
            array(
                '%s', // smtp_host
                '%d', // smtp_port
                '%s', // smtp_username
                '%s', // smtp_password
                '%s'  // encryption_type
            )
        );

        if ($wpdb->last_error) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Error adding SMTP account: ', 'multimail-blaster') . $wpdb->last_error . '</p></div>';
        } else {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('SMTP account added successfully!', 'multimail-blaster') . '</p></div>';
        }
    }

    // Handle CSV file upload
    if (isset($_FILES['smtp_csv']) && !empty($_FILES['smtp_csv']['tmp_name'])) {
        $csv_file = $_FILES['smtp_csv']['tmp_name'];
        $handle = fopen($csv_file, 'r');

        if ($handle !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (count($data) >= 4) { // Assuming CSV has 4 columns: smtp_host, smtp_port, smtp_username, smtp_password
                    list($smtp_host, $smtp_port, $smtp_username, $smtp_password) = $data;

                    // Insert the CSV data into the database
                    $wpdb->insert(
                        $smtp_table,
                        array(
                            'smtp_host'     => sanitize_text_field($smtp_host),
                            'smtp_port'     => intval($smtp_port),
                            'smtp_username' => sanitize_text_field($smtp_username),
                            'smtp_password' => sanitize_text_field($smtp_password),
                            'encryption_type' => 'none' // Adjust if necessary
                        ),
                        array(
                            '%s', // smtp_host
                            '%d', // smtp_port
                            '%s', // smtp_username
                            '%s', // smtp_password
                            '%s'  // encryption_type
                        )
                    );
                }
            }
            fclose($handle);
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('CSV uploaded and processed successfully!', 'multimail-blaster') . '</p></div>';
        }
    }

    // Display the Add SMTP Account form
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
            <tr>
                <th><label for="encryption_type"><?php esc_html_e('Encryption Type', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="encryption_type" name="encryption_type" required>
                        <option value="none"><?php esc_html_e('None', 'multimail-blaster'); ?></option>
                        <option value="ssl"><?php esc_html_e('SSL', 'multimail-blaster'); ?></option>
                        <option value="tls"><?php esc_html_e('TLS', 'multimail-blaster'); ?></option>
                    </select>
                </td>
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
