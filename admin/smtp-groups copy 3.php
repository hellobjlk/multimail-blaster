<?php
// smtp-groups.php

function mmb_list_smtp_groups() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mmb_smtp_groups';

    // Handle delete request
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['group_id'])) {
        $group_id = intval($_GET['group_id']);
        $wpdb->delete($table_name, ['id' => $group_id]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle edit request
    if (isset($_POST['update_smtp_group']) && isset($_POST['group_id']) && isset($_POST['group_name'])) {
        $group_id = intval($_POST['group_id']);
        $group_name = sanitize_text_field($_POST['group_name']);
        $wpdb->update($table_name, ['group_name' => $group_name], ['id' => $group_id]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group updated successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Retrieve all SMTP groups
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>' . esc_html__('Group Name', 'multimail-blaster') . '</th><th>' . esc_html__('Actions', 'multimail-blaster') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->group_name) . '</td>';
            echo '<td>';
            echo '<a href="?page=mmb-smtp-groups&action=edit&group_id=' . esc_attr($row->id) . '">' . esc_html__('Edit', 'multimail-blaster') . '</a> | ';
            echo '<a href="?page=mmb-smtp-groups&action=delete&group_id=' . esc_attr($row->id) . '" onclick="return confirm(\'' . esc_html__('Are you sure you want to delete this group?', 'multimail-blaster') . '\');">' . esc_html__('Delete', 'multimail-blaster') . '</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>' . esc_html__('No SMTP groups found.', 'multimail-blaster') . '</p>';
    }

    // Edit SMTP Group form
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['group_id'])) {
        $group_id = intval($_GET['group_id']);
        $group = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $group_id");
        
        if ($group) {
            ?>
            <h3><?php esc_html_e('Edit SMTP Group', 'multimail-blaster'); ?></h3>
            <form method="POST" action="">
                <input type="hidden" name="group_id" value="<?php echo esc_attr($group->id); ?>" />
                <table class="form-table">
                    <tr>
                        <th><label for="group_name"><?php esc_html_e('Group Name', 'multimail-blaster'); ?></label></th>
                        <td><input type="text" id="group_name" name="group_name" value="<?php echo esc_attr($group->group_name); ?>" required /></td>
                    </tr>
                </table>
                <?php submit_button(__('Update SMTP Group', 'multimail-blaster'), 'primary', 'update_smtp_group'); ?>
            </form>
            <?php
        } else {
            echo '<p>' . esc_html__('SMTP Group not found.', 'multimail-blaster') . '</p>';
        }
    }
}

// Call the function to display SMTP Groups
mmb_list_smtp_groups();
?>
