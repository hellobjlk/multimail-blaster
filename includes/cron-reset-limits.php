<?php
// Function to reset daily limits at midnight
function reset_smtp_daily_limit() {
    global $wpdb;
    $smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';

    $wpdb->query("UPDATE $smtp_table SET sent_today = 0");
}

// Schedule the event
if (!wp_next_scheduled('reset_smtp_daily_limit')) {
    wp_schedule_event(time(), 'daily', 'reset_smtp_daily_limit');
}

// Hook the reset function to the scheduled event
add_action('reset_smtp_daily_limit', 'reset_smtp_daily_limit');
?>
