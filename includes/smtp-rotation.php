<?php

// Function to get the next SMTP account
function get_next_smtp_account() {
    global $wpdb;
    $smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';

    // Select an SMTP account that hasn't reached the daily limit
    $smtp_accounts = $wpdb->get_results("SELECT * FROM $smtp_table WHERE sent_today < daily_limit");

    // If no available accounts, return null
    if (empty($smtp_accounts)) {
        return null;
    }

    // Use round-robin or random selection (choose random for now)
    return $smtp_accounts[array_rand($smtp_accounts)];
}

// Function to send email using wp_mail() and SMTP rotation
function send_email_with_smtp_rotation($smtp_account, $to_email, $subject, $message) {
    // Hook wp_mail() to use the selected SMTP account
    add_action('phpmailer_init', function($phpmailer) use ($smtp_account) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_account->smtp_host;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $smtp_account->smtp_username;
        $phpmailer->Password = $smtp_account->smtp_password;
        $phpmailer->SMTPSecure = $smtp_account->encryption_type;
        $phpmailer->Port = $smtp_account->smtp_port;
    });

    // Send the email using wp_mail()
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $success = wp_mail($to_email, $subject, $message, $headers);

    // Log the result and update sent_today
    if ($success) {
        global $wpdb;
        $wpdb->query("UPDATE {$wpdb->prefix}mmb_smtp_accounts SET sent_today = sent_today + 1, last_sent = NOW() WHERE id = {$smtp_account->id}");
    }

    return $success;
}
