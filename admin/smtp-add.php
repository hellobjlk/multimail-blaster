<?php
// smtp-add.php

// Function to get existing SMTP groups from the database
function mmb_get_smtp_groups() {
    global $wpdb;
    $smtp_groups_table = $wpdb->prefix . 'mmb_smtp_groups';
    $groups = $wpdb->get_results("SELECT id, group_name FROM $smtp_groups_table");
    return $groups;
}

// Function to display Add SMTP Account form
function mmb_add_smtp_form() {
    $smtp_groups = mmb_get_smtp_groups(); // Fetch existing SMTP groups
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
                    <select id="encryption_type" name="encryption_type">
                        <option value="none"><?php esc_html_e('None', 'multimail-blaster'); ?></option>
                        <option value="ssl"><?php esc_html_e('SSL', 'multimail-blaster'); ?></option>
                        <option value="tls"><?php esc_html_e('TLS', 'multimail-blaster'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="smtp_group"><?php esc_html_e('SMTP Group', 'multimail-blaster'); ?></label></th>
                <td>
                    <select id="smtp_group" name="smtp_group">
                        <option value=""><?php esc_html_e('Select Group', 'multimail-blaster'); ?></option>
                        <?php foreach ($smtp_groups as $group): ?>
                            <option value="<?php echo esc_attr($group->id); ?>"><?php echo esc_html($group->group_name); ?></option>
                        <?php endforeach; ?>
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
        <select name="smtp_group_for_csv">
            <option value=""><?php esc_html_e('Select Group', 'multimail-blaster'); ?></option>
            <?php foreach ($smtp_groups as $group): ?>
                <option value="<?php echo esc_attr($group->id); ?>"><?php echo esc_html($group->group_name); ?></option>
            <?php endforeach; ?>
        </select>
        <?php submit_button(__('Upload CSV', 'multimail-blaster')); ?>
    </form>
    <?php
}

// Call the function to display the form
mmb_add_smtp_form();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $wpdb;
    
    if (isset($_POST['smtp_host'])) {
        // For manual entry form
        $smtp_host = sanitize_text_field($_POST['smtp_host']);
        $smtp_port = intval($_POST['smtp_port']);
        $smtp_username = sanitize_text_field($_POST['smtp_username']);
        $smtp_password = sanitize_text_field($_POST['smtp_password']);
        $encryption_type = sanitize_text_field($_POST['encryption_type']);
        $smtp_group_id = isset($_POST['smtp_group']) ? intval($_POST['smtp_group']) : null;

        // Insert the SMTP account into the database
        $wpdb->insert(
            $wpdb->prefix . 'mmb_smtp_accounts',
            array(
                'smtp_host' => $smtp_host,
                'smtp_port' => $smtp_port,
                'smtp_username' => $smtp_username,
                'smtp_password' => $smtp_password,
                'encryption_type' => $encryption_type, // Save encryption type
                'smtp_group_id' => $smtp_group_id // Save the selected group
            ),
            array('%s', '%d', '%s', '%s', '%s', '%d') // Data types
        );

        echo '<div class="notice notice-success is-dismissible"><p>' . __('SMTP account added successfully!', 'multimail-blaster') . '</p></div>';
    }

    if (isset($_FILES['smtp_csv']) && isset($_POST['smtp_group_for_csv'])) {
        // For CSV upload
        $file = $_FILES['smtp_csv']['tmp_name'];
        $smtp_group_id = intval($_POST['smtp_group_for_csv']);

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                // Assume CSV structure: SMTP Host, SMTP Port, SMTP Username, SMTP Password, Encryption Type
                $smtp_host = sanitize_text_field($row[0]);
                $smtp_port = intval($row[1]);
                $smtp_username = sanitize_text_field($row[2]);
                $smtp_password = sanitize_text_field($row[3]);
                $encryption_type = sanitize_text_field($row[4]);

                // Insert each row into the database
                $wpdb->insert(
                    $wpdb->prefix . 'mmb_smtp_accounts',
                    array(
                        'smtp_host' => $smtp_host,
                        'smtp_port' => $smtp_port,
                        'smtp_username' => $smtp_username,
                        'smtp_password' => $smtp_password,
                        'encryption_type' => $encryption_type, // Save encryption type
                        'smtp_group_id' => $smtp_group_id // Associate with selected group
                    ),
                    array('%s', '%d', '%s', '%s', '%s', '%d')
                );
            }
            fclose($handle);

            echo '<div class="notice notice-success is-dismissible"><p>' . __('SMTP accounts added from CSV!', 'multimail-blaster') . '</p></div>';
        }
    }
}
?>
