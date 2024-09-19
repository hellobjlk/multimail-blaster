<?php
global $wpdb;
$recipient_groups_table = $wpdb->prefix . 'mmb_recipient_groups';
$recipient_group_relationship_table = $wpdb->prefix . 'mmb_recipient_group_relationship';

// Handle adding a new group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_group_name'])) {
    $new_group_name = sanitize_text_field($_POST['new_group_name']);
    
    // Check if the group name is not empty
    if (!empty($new_group_name)) {
        $wpdb->insert(
            $recipient_groups_table,
            array('group_name' => $new_group_name),
            array('%s')
        );

        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('New recipient group added successfully.', 'multimail-blaster') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Group name cannot be empty.', 'multimail-blaster') . '</p></div>';
    }
}

// Handle group deletion with safe handling for relationships
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['group_id'])) {
    $group_id = intval($_GET['group_id']);

    // Remove all relationships for this group before deleting the group
    $wpdb->delete($recipient_group_relationship_table, array('group_id' => $group_id));

    // Now delete the group itself
    $wpdb->delete($recipient_groups_table, array('id' => $group_id));

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Recipient group deleted successfully.', 'multimail-blaster') . '</p></div>';
}

// Fetch recipient groups for listing
$recipient_groups = $wpdb->get_results("SELECT * FROM $recipient_groups_table");

?>

<h2><?php esc_html_e('Manage Recipient Groups', 'multimail-blaster'); ?></h2>

<!-- Add New Recipient Group Form -->
<h3><?php esc_html_e('Add New Recipient Group', 'multimail-blaster'); ?></h3>
<form method="POST" action="">
    <table class="form-table">
        <tr>
            <th><label for="new_group_name"><?php esc_html_e('Group Name', 'multimail-blaster'); ?></label></th>
            <td><input type="text" id="new_group_name" name="new_group_name" required /></td>
        </tr>
    </table>
    <?php submit_button(__('Add Group', 'multimail-blaster')); ?>
</form>

<!-- List Existing Groups -->
<h3><?php esc_html_e('Existing Groups', 'multimail-blaster'); ?></h3>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php esc_html_e('Group Name', 'multimail-blaster'); ?></th>
            <th><?php esc_html_e('Actions', 'multimail-blaster'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($recipient_groups): ?>
            <?php foreach ($recipient_groups as $group): ?>
                <tr>
                    <td><?php echo esc_html($group->group_name); ?></td>
                    <td>
                        <a href="?page=mmb-recipient-manager&action=edit&group_id=<?php echo esc_attr($group->id); ?>"><?php esc_html_e('Edit', 'multimail-blaster'); ?></a> | 
                        <a href="?page=mmb-recipient-manager&action=delete&group_id=<?php echo esc_attr($group->id); ?>" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this group? This will also remove all relationships.', 'multimail-blaster'); ?>');">
                            <?php esc_html_e('Delete', 'multimail-blaster'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2"><?php esc_html_e('No recipient groups found.', 'multimail-blaster'); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
