<?php
// campaign-list.php

function mmb_list_campaigns() {
    global $wpdb;
    $campaigns_table = $wpdb->prefix . 'mmb_campaigns';

    // Fetch all campaigns from the database
    $campaigns = $wpdb->get_results("SELECT * FROM $campaigns_table");

    // Check if any campaigns exist
    if ($campaigns) {
        echo '<h2>' . esc_html__('Manage Campaigns', 'multimail-blaster') . '</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Campaign Name', 'multimail-blaster') . '</th>';
        echo '<th>' . esc_html__('Subject', 'multimail-blaster') . '</th>';
        echo '<th>' . esc_html__('Created At', 'multimail-blaster') . '</th>';
        echo '<th>' . esc_html__('Actions', 'multimail-blaster') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        // Loop through each campaign and display it
        foreach ($campaigns as $campaign) {
            echo '<tr>';
            echo '<td>' . esc_html($campaign->campaign_name) . '</td>';
            echo '<td>' . esc_html($campaign->subject) . '</td>';
            echo '<td>' . esc_html($campaign->created_at) . '</td>';
            echo '<td>';
            echo '<a href="#">' . esc_html__('Edit', 'multimail-blaster') . '</a> | ';
            echo '<a href="#">' . esc_html__('Delete', 'multimail-blaster') . '</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>' . esc_html__('No campaigns found.', 'multimail-blaster') . '</p>';
    }
}

mmb_list_campaigns();
?>
