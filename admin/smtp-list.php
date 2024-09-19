<?php
// smtp-list.php

function mmb_list_smtp_accounts() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mmb_smtp_accounts';

    // Handle Deletion of Single SMTP Account
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['smtp_id'])) {
        $smtp_id = intval($_GET['smtp_id']);
        $wpdb->delete($table_name, array('id' => $smtp_id));
        echo '<div class="notice notice-success"><p>' . esc_html__('SMTP Account deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Handle Bulk Deletion of SMTP Accounts
    if (isset($_POST['bulk_delete']) && !empty($_POST['smtp_ids'])) {
        $smtp_ids = array_map('intval', $_POST['smtp_ids']);
        $ids_to_delete = implode(',', $smtp_ids);
        $wpdb->query("DELETE FROM $table_name WHERE id IN ($ids_to_delete)");
        echo '<div class="notice notice-success"><p>' . esc_html__('Selected SMTP accounts deleted successfully.', 'multimail-blaster') . '</p></div>';
    }

    // Fetch SMTP Accounts
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    if ($results) {
        ?>
        <form method="POST" action="">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th><?php esc_html_e('Host', 'multimail-blaster'); ?></th>
                        <th><?php esc_html_e('Port', 'multimail-blaster'); ?></th>
                        <th><?php esc_html_e('Username', 'multimail-blaster'); ?></th>
                        <th><?php esc_html_e('Group', 'multimail-blaster'); ?></th>
                        <th><?php esc_html_e('Owner', 'multimail-blaster'); ?></th>
                        <th><?php esc_html_e('Actions', 'multimail-blaster'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="smtp_ids[]" value="<?php echo esc_attr($row->id); ?>"></td>
                            <td><?php echo esc_html($row->smtp_host); ?></td>
                            <td><?php echo esc_html($row->smtp_port); ?></td>
                            <td><?php echo esc_html($row->smtp_username); ?></td>
                            <td>
                                <?php
                                // Fetch SMTP Group name
                                $group_name = $wpdb->get_var($wpdb->prepare("SELECT group_name FROM {$wpdb->prefix}mmb_smtp_groups WHERE id = %d", $row->smtp_group_id));
                                echo esc_html($group_name ? $group_name : __('No Group', 'multimail-blaster'));
                                ?>
                            </td>
                            <td>
                                <?php
                                // Fetch Owner (Admin/User who added the SMTP account)
                                $owner = get_userdata($row->owner_id);
                                echo esc_html($owner ? $owner->user_login : __('Unknown', 'multimail-blaster'));
                                ?>
                            </td>
                            <td>
                                <a href="?page=mmb-smtp-manager&action=edit&smtp_id=<?php echo esc_attr($row->id); ?>"><?php esc_html_e('Edit', 'multimail-blaster'); ?></a> | 
                                <a href="?page=mmb-smtp-manager&action=delete&smtp_id=<?php echo esc_attr($row->id); ?>" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this SMTP account?', 'multimail-blaster'); ?>');"><?php esc_html_e('Delete', 'multimail-blaster'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Bulk Actions -->
            <input type="submit" name="bulk_delete" value="<?php esc_html_e('Delete Selected', 'multimail-blaster'); ?>" class="button button-primary">
        </form>

        <!-- JavaScript to handle "Select All" checkbox -->
        <script type="text/javascript">
            document.getElementById('select-all').addEventListener('click', function() {
                let checkboxes = document.querySelectorAll('input[name="smtp_ids[]"]');
                for (let checkbox of checkboxes) {
                    checkbox.checked = this.checked;
                }
            });
        </script>
        <?php
    } else {
        echo '<p>' . esc_html__('No SMTP accounts found.', 'multimail-blaster') . '</p>';
    }
}

// Render the SMTP Account List
mmb_list_smtp_accounts();
