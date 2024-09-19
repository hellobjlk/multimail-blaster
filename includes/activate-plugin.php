<?php
function mmb_activate() {
    if (!wp_next_scheduled('reset_smtp_daily_limit')) {
        wp_schedule_event(time(), 'daily', 'reset_smtp_daily_limit');
    }
}
register_activation_hook(__FILE__, 'mmb_activate');
