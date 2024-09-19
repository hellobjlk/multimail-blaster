<?php
// smtp-groups.php

// Display and manage SMTP groups
function mmb_manage_smtp_groups() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mmb_smtp_groups';

    // Ensure user has the correct capability to access the page
    if (!current_user_can('manage_options')) {
        wp_die(__('Sorry, you are not allowed to access this page.', 'multimail-blaster'));
    }

    // Handle group creation
    if (isset($_POST['create_smtp_group']) && isset($_POST['group_name'])) {
        check_admin_referer('create_smtp_group_action');
        $group_name = sanitize_text_field($_POST['group_name']);
        $wpdb->insert($table_name, ['group_name' => $group_name]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group created successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle individual delete request
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['group_id'])) {
        check_admin_referer('delete_smtp_group_action');
        $group_id = intval($_GET['group_id']);
        $wpdb->delete($table_name, ['id' => $group_id]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle bulk delete request
    if (isset($_POST['bulk_delete_smtp_groups']) && isset($_POST['group_ids'])) {
        check_admin_referer('bulk_delete_smtp_groups_action');
        $group_ids = array_map('intval', $_POST['group_ids']);
        foreach ($group_ids as $group_id) {
            $wpdb->delete($table_name, ['id' => $group_id]);
        }
        echo '<div class="updated notice"><p>' . esc_html__('Selected SMTP Groups deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle edit request
    if (isset($_POST['update_smtp_group']) && isset($_POST['group_id']) && isset($_POST['group_name'])) {
        check_admin_referer('edit_smtp_group_action');
        $group_id = intval($_POST['group_id']);
        $group_name = sanitize_text_field($_POST['group_name']);
        $wpdb->update($table_name, ['group_name' => $group_name], ['id' => $group_id]);
        echo '<div class="updated notice"><p>' . esc_html__('SMTP Group updated successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Fetch all SMTP groups
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Display form to create a new SMTP group
    ?>
    <h2><?php esc_html_e('Manage SMTP Groups', 'multimail-blaster'); ?></h2>

    <h3><?php esc_html_e('Create New SMTP Group', 'multimail-blaster'); ?></h3>
    <form method="POST" action="">
        <?php wp_nonce_field('create_smtp_group_action'); ?>
        <table class="form-table">
            <tr>
                <th><label for="group_name"><?php esc_html_e('SMTP Group Name', 'multimail-blaster'); ?></label></th>
                <td><input type="text" name="group_name" id="group_name" required /></td>
            </tr>
        </table>
        <?php submit_button(__('Create SMTP Group', 'multimail-blaster'), 'primary', 'create_smtp_group'); ?>
    </form>

    <h3><?php esc_html_e('Existing SMTP Groups', 'multimail-blaster'); ?></h3>
    <form method="POST" action="">
        <?php wp_nonce_field('bulk_delete_smtp_groups_action'); ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all" /></th>
                    <th><?php esc_html_e('Group Name', 'multimail-blaster'); ?></th>
                    <th><?php esc_html_e('Actions', 'multimail-blaster'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($results) {
                    foreach ($results as $row) {
                        ?>
                        <tr>
                            <td><input type="checkbox" name="group_ids[]" value="<?php echo esc_attr($row->id); ?>" /></td>
                            <td><?php echo esc_html($row->group_name); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mmb-smtp-groups&action=edit&group_id=' . esc_attr($row->id)), 'edit_smtp_group_action'); ?>">
                                    <?php esc_html_e('Edit', 'multimail-blaster'); ?>
                                </a> | 
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mmb-smtp-groups&action=delete&group_id=' . esc_attr($row->id)), 'delete_smtp_group_action'); ?>" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this group?', 'multimail-blaster'); ?>');">
                                    <?php esc_html_e('Delete', 'multimail-blaster'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="3">' . esc_html__('No SMTP groups found.', 'multimail-blaster') . '</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <?php submit_button(__('Delete Selected', 'multimail-blaster'), 'delete', 'bulk_delete_smtp_groups'); ?>
    </form>

    <script type="text/javascript">
    // Select/deselect all checkboxes
    document.getElementById('select-all').onclick = function() {
        var checkboxes = document.querySelectorAll('input[name="group_ids[]"]');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
    </script>
    <?php
}
mmb_manage_smtp_groups();
