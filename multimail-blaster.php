<?php
/*
Plugin Name: MultiMail Blaster
Description: A plugin to manage SMTP accounts, recipients, and email campaigns.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files for setup and management
require_once(plugin_dir_path(__FILE__) . 'includes/install.php');      // For initial database setup on activation
require_once(plugin_dir_path(__FILE__) . 'includes/db-manager.php');   // For ongoing database operations
require_once(plugin_dir_path(__FILE__) . 'admin/admin-menu.php');      // For admin menus

// Register activation hook to create database tables
register_activation_hook(__FILE__, 'mmb_create_tables');

// Hook into WordPress admin menu
add_action('admin_menu', 'mmb_create_admin_menu');