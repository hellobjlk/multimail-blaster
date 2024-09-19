<?php
// admin-menu.php

// Hook to add the admin menu
add_action('admin_menu', 'mmb_create_admin_menu');

// Function to create the main menu and submenus
function mmb_create_admin_menu() {
    // Main Menu - MultiMail Blaster (Dashboard)
    add_menu_page(
        __('MultiMail Blaster', 'multimail-blaster'), // Page title
        __('MultiMail Blaster', 'multimail-blaster'), // Menu title
        'manage_options',                             // Capability
        'mmb-dashboard',                              // Menu slug
        'mmb_dashboard_page',                         // Function to display the dashboard page
        'dashicons-email',                            // Menu icon (WordPress Dashicon)
        6                                             // Position
    );

    // Submenu - SMTP Manager
    add_submenu_page(
        'mmb-dashboard',                              // Parent slug (matches main menu)
        __('SMTP Manager', 'multimail-blaster'),      // Page title
        __('SMTP Manager', 'multimail-blaster'),      // Menu title
        'manage_options',                             // Capability
        'mmb-smtp-manager',                           // Submenu slug
        'mmb_smtp_manager_page'                       // Function to display the SMTP Manager page
    );

    // Submenu - Recipient Manager
    add_submenu_page(
        'mmb-dashboard',                              // Parent slug
        __('Recipient Manager', 'multimail-blaster'), // Page title
        __('Recipient Manager', 'multimail-blaster'), // Menu title
        'manage_options',                             // Capability
        'mmb-recipient-manager',                      // Submenu slug
        'mmb_recipient_manager_page'                  // Function to display Recipient Manager
    );

    // Submenu - Campaign Manager
    add_submenu_page(
        'mmb-dashboard',                              // Parent slug
        __('Campaign Manager', 'multimail-blaster'),  // Page title
        __('Campaign Manager', 'multimail-blaster'),  // Menu title
        'manage_options',                             // Capability
        'mmb-campaign-manager',                       // Submenu slug
        'mmb_campaign_manager_page'                   // Function to display Campaign Manager
    );

    // Submenu - Run Campaign
    add_submenu_page(
        'mmb-dashboard',                              // Parent slug
        __('Run Campaign', 'multimail-blaster'),      // Page title
        __('Run Campaign', 'multimail-blaster'),      // Menu title
        'manage_options',                             // Capability
        'mmb-run-campaign',                           // Submenu slug
        'mmb_run_campaign_page'                       // Function to display the Run Campaign page
    );

    // Submenu - Reports
    add_submenu_page(
        'mmb-dashboard',                              // Parent slug
        __('Reports', 'multimail-blaster'),           // Page title
        __('Reports', 'multimail-blaster'),           // Menu title
        'manage_options',                             // Capability
        'mmb-reports',                                // Submenu slug
        'mmb_reports_page'                            // Function to display the Reports page
    );
}

// Main Dashboard page
function mmb_dashboard_page() {
    echo '<div class="wrap"><h1>' . esc_html__('MultiMail Blaster Dashboard', 'multimail-blaster') . '</h1>';
    echo '<p>' . esc_html__('Welcome to the MultiMail Blaster plugin! Use the menu to manage your SMTP accounts, campaigns, and more.', 'multimail-blaster') . '</p></div>';
}

// Include the full content of SMTP Manager from smtp-manager.php
require_once(plugin_dir_path(__FILE__) . '/smtp-manager.php');

// Recipient Manager page
function mmb_recipient_manager_page() {
    echo '<div class="wrap"><h1>' . esc_html__('Recipient Manager', 'multimail-blaster') . '</h1>';
}

// Campaign Manager page
function mmb_campaign_manager_page() {
    echo '<div class="wrap"><h1>' . esc_html__('Campaign Manager', 'multimail-blaster') . '</h1>';
}

// Run Campaign page
function mmb_run_campaign_page() {
    echo '<div class="wrap"><h1>' . esc_html__('Run Campaign', 'multimail-blaster') . '</h1>';
}

// Reports page
function mmb_reports_page() {
    echo '<div class="wrap"><h1>' . esc_html__('Reports', 'multimail-blaster') . '</h1>';
}
