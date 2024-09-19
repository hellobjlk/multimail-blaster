<?php
// smtp-groups.php

// Function to display the Add SMTP Group form and existing groups
function mmb_add_smtp_group_form() {
    global $wpdb;

    // Handle form submission
    if (isset($_POST['mmb_add_smtp_group'])) {
        $group_name = sanitize_text_field($_POST['smtp_group_name']);

        // Insert new group into the database
        if (!empty($group_name)) {
            $table_name = $wpdb->prefix . 'mmb_smtp_groups';
            $wpdb->insert(
                $table_name,
                array('group_name' => $group_name)
            );
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('SMTP Group added successfully!', 'multimail-blaster') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Please enter a group name.', 'multimail-blaster') . '</p></div>';
        }
    }

    // Retrieve existing SMTP groups
    $smtp_groups = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "mmb_smtp_groups");

    // Display the form and existing groups
    ?>
    <h2><?php esc_html_e('Add New SMTP Group', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="smtp_group_name"><?php esc_html_e('SMTP Group Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="smtp_group_name" name="smtp_group_name" required /></td>
            </tr>
        </table>
        <input type="hidden" name="mmb_add_smtp_group" value="1" />
        <?php submit_button(__('Add SMTP Group', 'multimail-blaster')); ?>
    </form>

    <!-- Display Existing SMTP Groups -->
    <h3><?php esc_html_e('Existing SMTP Groups', 'multimail-blaster'); ?></h3>
    <?php if (!empty($smtp_groups)): ?>
        <ul>
            <?php foreach ($smtp_groups as $group): ?>
                <li><?php echo esc_html($group->group_name); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?php esc_html_e('No SMTP groups available.', 'multimail-blaster'); ?></p>
    <?php endif; ?>
    <?php
}

// Call the function to display the form and groups
mmb_add_smtp_group_form();
