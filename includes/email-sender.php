<?php
// includes/email-sender.php

// Hook into 'phpmailer_init' to set custom SMTP settings
function mmb_smtp_custom_mailer( $phpmailer ) {
    global $wpdb;

    // Fetch all SMTP accounts from the database
    $smtp_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts");

    // Randomly choose one SMTP account for load balancing
    $smtp_account = $smtp_accounts[array_rand($smtp_accounts)];

    // Configure PHPMailer with selected SMTP account
    $phpmailer->isSMTP();
    $phpmailer->Host       = $smtp_account->smtp_host;
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = $smtp_account->smtp_username;
    $phpmailer->Password   = $smtp_account->smtp_password;
    $phpmailer->SMTPSecure = $smtp_account->encryption_type;
    $phpmailer->Port       = $smtp_account->smtp_port;
}

// Add our custom SMTP handler for wp_mail()
add_action( 'phpmailer_init', 'mmb_smtp_custom_mailer' );
