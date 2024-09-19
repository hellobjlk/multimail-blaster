<?php
// smtp-groups.php

// Function to display and manage SMTP groups
function mmb_manage_smtp_groups() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mmb_smtp_groups';

    // Ensure user has the right capability to access the page
    if (!current_user_can('manage_options')) {
        wp_die(__('Sorry, you are not allowed to access this page.', 'multimail-blaster'));
    }

    // Handle form submission to create a new group
    if (isset($_POST['create_smtp_group']) && isset($_POST['group_name'])) {
        check_admin_referer('create_smtp_group_action'); // Check nonce for security

        $group_name = sanitize_text_field($_POST['group_name']);
        $wpdb->insert($table_name, ['group_name' => $group_name]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group created successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle bulk delete
    if (isset($_POST['bulk_delete_smtp_groups']) && !empty($_POST['group_ids'])) {
        check_admin_referer('bulk_delete_smtp_groups_action'); // Check nonce for security

        $group_ids = array_map('intval', $_POST['group_ids']);
        foreach ($group_ids as $group_id) {
            $wpdb->delete($table_name, ['id' => $group_id]);
        }
        echo '<div class="updated notice"><p>' . esc_html__('Selected SMTP Groups deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle individual delete request
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['group_id'])) {
        check_admin_referer('delete_smtp_group_action'); // Check nonce for security

        $group_id = intval($_GET['group_id']);
        $wpdb->delete($table_name, ['id' => $group_id]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle edit request and submission
    if (isset($_POST['update_smtp_group']) && isset($_POST['group_id']) && isset($_POST['group_name'])) {
        check_admin_referer('edit_smtp_group_action'); // Check nonce for security

        $group_id = intval($_POST['group_id']);
        $group_name = sanitize_text_field($_POST['group_name']);
        $wpdb->update($table_name, ['group_name' => $group_name], ['id' => $group_id]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group updated successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Fetch all SMTP groups
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Display Create New SMTP Group Form
    ?>
    <h2><?php esc_html_e('Create New SMTP Group', 'multimail-blaster'); ?></h2>
    <form method="POST" action="">
        <?php wp_nonce_field('create_smtp_group_action'); // Add nonce for security ?>
        <table class="form-table">
            <tr>
                <th><label for="group_name"><?php esc_html_e('Group Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" id="group_name" name="group_name" required /></td>
            </tr>
        </table>
        <?php submit_button(__('Create SMTP Group', 'multimail-blaster'), 'primary', 'create_smtp_group'); ?>
    </form>
    <?php

    // Display SMTP Groups List with Bulk Delete
    if ($results) {
        ?>
        <h2><?php esc_html_e('Manage SMTP Groups', 'multimail-blaster'); ?></h2>
        <form method="POST" action="">
            <?php wp_nonce_field('bulk_delete_smtp_groups_action'); // Add nonce for security ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td><input type="checkbox" id="select_all_groups"></td>
                        <th><?php esc_html_e('Group Name', 'multimail-blaster'); ?></th>
                        <th><?php esc_html_e('Actions', 'multimail-blaster'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($results as $row) {
                        ?>
                        <tr>
                            <td><input type="checkbox" name="group_ids[]" value="<?php echo esc_attr($row->id); ?>"></td>
                            <td><?php echo esc_html($row->group_name); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mmb-smtp-groups&action=edit&group_id=' . esc_attr($row->id)), 'edit_smtp_group_action'); ?>"><?php esc_html_e('Edit', 'multimail-blaster'); ?></a> | 
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mmb-smtp-groups&action=delete&group_id=' . esc_attr($row->id)), 'delete_smtp_group_action'); ?>" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this group?', 'multimail-blaster'); ?>');"><?php esc_html_e('Delete', 'multimail-blaster'); ?></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <?php submit_button(__('Delete Selected Groups', 'multimail-blaster'), 'delete', 'bulk_delete_smtp_groups'); ?>
        </form>
        <?php
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
                <?php wp_nonce_field('edit_smtp_group_action'); // Add nonce for security ?>
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

// Call the function to display and manage SMTP Groups
mmb_manage_smtp_groups();

?>
<script>
// Select/Deselect all checkboxes
document.getElementById('select_all_groups').addEventListener('click', function(event) {
    let checkboxes = document.querySelectorAll('input[name="group_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = event.target.checked;
    });
});
</script>
