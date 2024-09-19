<?php
// recipient-groups.php

function mmb_manage_recipient_groups() {
    global $wpdb;

    // Define the table name
    $recipient_groups_table = $wpdb->prefix . 'mmb_recipient_groups';

    // Handle form submission for adding new group
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_name'])) {
        $group_name = sanitize_text_field($_POST['group_name']);
        if (!empty($group_name)) {
            // Insert new group into the database
            $wpdb->insert(
                $recipient_groups_table,
                array('group_name' => $group_name)
            );
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Recipient group added successfully!', 'multimail-blaster') . '</p></div>';
        }
    }

    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['group_id'])) {
        $group_id = intval($_GET['group_id']);
        // Delete the group from the database
        $wpdb->delete($recipient_groups_table, array('id' => $group_id));
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Recipient group deleted successfully!', 'multimail-blaster') . '</p></div>';
    }

    // Handle edit action
    if (isset($_POST['edit_group_id']) && isset($_POST['edit_group_name'])) {
        $edit_group_id = intval($_POST['edit_group_id']);
        $edit_group_name = sanitize_text_field($_POST['edit_group_name']);
        // Update the group name in the database
        $wpdb->update(
            $recipient_groups_table,
            array('group_name' => $edit_group_name),
            array('id' => $edit_group_id)
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Recipient group updated successfully!', 'multimail-blaster') . '</p></div>';
    }

    // Fetch all recipient groups from the database
    $groups = $wpdb->get_results("SELECT * FROM $recipient_groups_table");

    // Display the Add New Recipient Group form
    ?>
    <h2><?php esc_html_e('Add New Recipient Group', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <table class="form-table">
            <tr>
                <th><label for="group_name"><?php esc_html_e('Group Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="group_name" name="group_name" required /></td>
            </tr>
        </table>
        <?php submit_button(__('Add Group', 'multimail-blaster')); ?>
    </form>

    <!-- Display existing groups -->
    <h2><?php esc_html_e('Manage Recipient Groups', 'multimail-blaster'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Group Name', 'multimail-blaster'); ?></th>
                <th><?php esc_html_e('Actions', 'multimail-blaster'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($groups) {
            foreach ($groups as $group) {
                echo '<tr>';
                echo '<td>' . esc_html($group->group_name) . '</td>';
                echo '<td>';
                echo '<a href="' . esc_url(add_query_arg(array('action' => 'edit', 'group_id' => $group->id))) . '">' . esc_html__('Edit', 'multimail-blaster') . '</a> | ';
                echo '<a href="' . esc_url(add_query_arg(array('action' => 'delete', 'group_id' => $group->id))) . '" onclick="return confirm(\'' . esc_html__('Are you sure you want to delete this group?', 'multimail-blaster') . '\');">' . esc_html__('Delete', 'multimail-blaster') . '</a>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="2">' . esc_html__('No groups found.', 'multimail-blaster') . '</td></tr>';
        }
        ?>
        </tbody>
    </table>

    <?php
    // Handle edit form if action=edit
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['group_id'])) {
        $group_id = intval($_GET['group_id']);
        $group = $wpdb->get_row("SELECT * FROM $recipient_groups_table WHERE id = $group_id");
        if ($group) {
            ?>
            <h2><?php esc_html_e('Edit Recipient Group', 'multimail-blaster'); ?></h2>
            <form method="POST" action="">
                <input type="hidden" name="edit_group_id" value="<?php echo esc_attr($group->id); ?>" />
                <table class="form-table">
                    <tr>
                        <th><label for="edit_group_name"><?php esc_html_e('Group Name', 'multimail-blaster'); ?></label></th>
                        <td><input type="text" id="edit_group_name" name="edit_group_name" value="<?php echo esc_attr($group->group_name); ?>" required /></td>
                    </tr>
                </table>
                <?php submit_button(__('Update Group', 'multimail-blaster')); ?>
            </form>
            <?php
        }
    }
}

// Call the function to display the form and group list
mmb_manage_recipient_groups();
?>
