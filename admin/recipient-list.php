<?php
// recipient-list.php

function mmb_list_recipients() {
    global $wpdb;

    // Define your table names
    $recipients_table = $wpdb->prefix . 'mmb_recipients';
    $recipient_group_relationship_table = $wpdb->prefix . 'mmb_recipient_group_relationship';
    $recipient_groups_table = $wpdb->prefix . 'mmb_recipient_groups';

    // Handle Bulk Delete
    if (isset($_POST['bulk_delete']) && !empty($_POST['recipients'])) {
        $recipient_ids = array_map('intval', $_POST['recipients']);
        foreach ($recipient_ids as $id) {
            $wpdb->delete($recipients_table, array('id' => $id));
            $wpdb->delete($recipient_group_relationship_table, array('recipient_id' => $id));
        }
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Selected recipients deleted successfully!', 'multimail-blaster') . '</p></div>';
    }

    // Handle Single Delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['recipient_id'])) {
        $recipient_id = intval($_GET['recipient_id']);
        $wpdb->delete($recipients_table, array('id' => $recipient_id));
        $wpdb->delete($recipient_group_relationship_table, array('recipient_id' => $recipient_id));
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Recipient deleted successfully!', 'multimail-blaster') . '</p></div>';
    }

    // Fetch all recipients and their group relationships
    $results = $wpdb->get_results("
        SELECT r.id, r.recipient_name, r.recipient_email, g.group_name
        FROM $recipients_table AS r
        LEFT JOIN $recipient_group_relationship_table AS rg ON r.id = rg.recipient_id
        LEFT JOIN $recipient_groups_table AS g ON rg.group_id = g.id
    ");

    if ($results) {
        echo '<form method="POST" action="">';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th><input type="checkbox" id="bulk-select-all"></th>'; // Bulk select checkbox
        echo '<th>' . esc_html__('Name', 'multimail-blaster') . '</th>';
        echo '<th>' . esc_html__('Email', 'multimail-blaster') . '</th>';
        echo '<th>' . esc_html__('Group', 'multimail-blaster') . '</th>';
        echo '<th>' . esc_html__('Actions', 'multimail-blaster') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            $group_name = !empty($row->group_name) ? esc_html($row->group_name) : esc_html__('No Group', 'multimail-blaster');
            echo '<tr>';
            echo '<td><input type="checkbox" name="recipients[]" value="' . esc_attr($row->id) . '"></td>'; // Checkbox for bulk action
            echo '<td>' . esc_html($row->recipient_name) . '</td>';
            echo '<td>' . esc_html($row->recipient_email) . '</td>';
            echo '<td>' . $group_name . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(add_query_arg(array('action' => 'edit', 'recipient_id' => $row->id))) . '">' . esc_html__('Edit', 'multimail-blaster') . '</a> | ';
            echo '<a href="' . esc_url(add_query_arg(array('action' => 'delete', 'recipient_id' => $row->id))) . '" onclick="return confirm(\'' . esc_html__('Are you sure you want to delete this recipient?', 'multimail-blaster') . '\');">' . esc_html__('Delete', 'multimail-blaster') . '</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // Bulk Action Dropdown
        echo '<select name="bulk_action">';
        echo '<option value="">' . esc_html__('Bulk Actions', 'multimail-blaster') . '</option>';
        echo '<option value="delete">' . esc_html__('Delete', 'multimail-blaster') . '</option>';
        echo '</select>';
        submit_button(__('Apply', 'multimail-blaster'), 'action', 'bulk_delete', false);

        echo '</form>';

        // Add JavaScript for "Select All" functionality
        echo '<script type="text/javascript">
        document.getElementById("bulk-select-all").addEventListener("click", function() {
            var checkboxes = document.querySelectorAll("input[name=\'recipients[]\']");
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
        </script>';

    } else {
        echo '<p>' . esc_html__('No recipients found.', 'multimail-blaster') . '</p>';
    }
}
mmb_list_recipients();
?>
