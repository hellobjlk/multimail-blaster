<?php
// db-manager.php

// Function to create necessary database tables for the plugin
function mmb_create_tables() {
    global $wpdb;

    // Get the database character set
    $charset_collate = $wpdb->get_charset_collate();

    // Define table names with WordPress prefix
    $smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';
    $recipients_table = $wpdb->prefix . 'mmb_recipients';
    $campaigns_table = $wpdb->prefix . 'mmb_campaigns';
    $campaign_logs_table = $wpdb->prefix . 'mmb_campaign_logs';
    $settings_table = $wpdb->prefix . 'mmb_settings';
    $smtp_groups_table = $wpdb->prefix . 'mmb_smtp_groups';
    $recipient_groups_table = $wpdb->prefix . 'mmb_recipient_groups'; // NEW
    $recipient_group_relationship_table = $wpdb->prefix . 'mmb_recipient_group_relationship'; // NEW

    // SQL for creating the SMTP Accounts table
    $sql_smtp_table = "CREATE TABLE IF NOT EXISTS $smtp_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        smtp_host varchar(255) NOT NULL,
        smtp_port int(11) NOT NULL,
        smtp_username varchar(255) NOT NULL,
        smtp_password varchar(255) NOT NULL,
        encryption_type varchar(10) NOT NULL,
        smtp_group_id mediumint(9) DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // SQL for creating the Recipients table
    $sql_recipients_table = "CREATE TABLE IF NOT EXISTS $recipients_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        recipient_email varchar(255) NOT NULL,
        recipient_name varchar(255) DEFAULT NULL,
        status varchar(20) DEFAULT 'active',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // SQL for creating the Campaigns table
    $sql_campaigns_table = "CREATE TABLE IF NOT EXISTS $campaigns_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        campaign_name varchar(255) NOT NULL,
        subject varchar(255) NOT NULL,
        message longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // SQL for creating the Campaign Logs table (for reporting)
    $sql_campaign_logs_table = "CREATE TABLE IF NOT EXISTS $campaign_logs_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        campaign_id mediumint(9) NOT NULL,
        recipient_id mediumint(9) NOT NULL,
        smtp_id mediumint(9) NOT NULL,
        status varchar(50) NOT NULL,
        sent_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        FOREIGN KEY (campaign_id) REFERENCES $campaigns_table(id),
        FOREIGN KEY (recipient_id) REFERENCES $recipients_table(id),
        FOREIGN KEY (smtp_id) REFERENCES $smtp_table(id)
    ) $charset_collate;";

    // SQL for creating the SMTP Groups table
    $sql_smtp_groups_table = "CREATE TABLE IF NOT EXISTS $smtp_groups_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        group_name varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // SQL for creating the Recipients Groups table
    $sql_recipient_groups_table = "CREATE TABLE IF NOT EXISTS $recipient_groups_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        group_name varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // SQL for creating the Recipient-Group Relationship table (many-to-many)
    $sql_recipient_group_relationship_table = "CREATE TABLE IF NOT EXISTS $recipient_group_relationship_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        recipient_id mediumint(9) NOT NULL,
        group_id mediumint(9) NOT NULL,
        PRIMARY KEY  (id),
        FOREIGN KEY (recipient_id) REFERENCES $recipients_table(id),
        FOREIGN KEY (group_id) REFERENCES $recipient_groups_table(id)
    ) $charset_collate;";

    // SQL for creating the Settings table
    $sql_settings_table = "CREATE TABLE IF NOT EXISTS $settings_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        setting_name varchar(255) NOT NULL,
        setting_value varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Execute all the SQL queries using dbDelta
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_smtp_table);
    dbDelta($sql_recipients_table);
    dbDelta($sql_campaigns_table);
    dbDelta($sql_campaign_logs_table);
    dbDelta($sql_smtp_groups_table);
    dbDelta($sql_recipient_groups_table); // NEW
    dbDelta($sql_recipient_group_relationship_table); // NEW
    dbDelta($sql_settings_table);
}

// Function to insert default settings (e.g., batch size, daily limit, etc.)
function mmb_insert_default_settings() {
    global $wpdb;
    $settings_table = $wpdb->prefix . 'mmb_settings';

    // Check if the settings already exist to avoid duplicates
    $batch_size = $wpdb->get_var("SELECT COUNT(*) FROM $settings_table WHERE setting_name = 'batch_size'");
    
    if ($batch_size == 0) {
        // Insert default settings
        $wpdb->insert($settings_table, array('setting_name' => 'batch_size', 'setting_value' => '50'));
        $wpdb->insert($settings_table, array('setting_name' => 'daily_limit', 'setting_value' => '1000'));
        $wpdb->insert($settings_table, array('setting_name' => 'rotation_delay', 'setting_value' => '10'));
    }

}

// Hook the function to insert default settings when the plugin is activated
register_activation_hook(__FILE__, 'mmb_insert_default_settings');
