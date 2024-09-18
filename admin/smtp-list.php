<?php
// smtp-list.php

// Function to display the list of SMTP accounts
function mmb_list_smtp_accounts() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mmb_smtp_accounts';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    ?>
    <h2><?php esc_html_e('Manage SMTP Accounts', 'multimail-blaster'); ?></h2>
    <?php
    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>' . esc_html__('Host', 'multimail-blaster') . '</th><th>' . esc_html__('Port', 'multimail-blaster') . '</th><th>' . esc_html__('Username', 'multimail-blaster') . '</th><th>' . esc_html__('Actions', 'multimail-blaster') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->smtp_host) . '</td>';
            echo '<td>' . esc_html($row->smtp_port) . '</td>';
            echo '<td>' . esc_html($row->smtp_username) . '</td>';
            echo '<td><a href="#">' . esc_html__('Edit', 'multimail-blaster') . '</a> | <a href="#">' . esc_html__('Delete', 'multimail-blaster') . '</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>' . esc_html__('No SMTP accounts found.', 'multimail-blaster') . '</p>';
    }
}

// Call the function to list the SMTP accounts
mmb_list_smtp_accounts();
?>
