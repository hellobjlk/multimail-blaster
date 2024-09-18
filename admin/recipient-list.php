<?php
// Function to list all recipients
function mmb_list_recipients() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mmb_recipients';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>' . esc_html__('Email', 'multimail-blaster') . '</th><th>' . esc_html__('Name', 'multimail-blaster') . '</th><th>' . esc_html__('Actions', 'multimail-blaster') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->recipient_email) . '</td>';
            echo '<td>' . esc_html($row->recipient_name) . '</td>';
            echo '<td><a href="#">' . esc_html__('Edit', 'multimail-blaster') . '</a> | <a href="#">' . esc_html__('Delete', 'multimail-blaster') . '</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>' . esc_html__('No recipients found.', 'multimail-blaster') . '</p>';
    }
}
mmb_list_recipients();
?>
