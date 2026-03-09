<?php
/**
 * Plugin Name: CareOnCall - Caretaker Booking System
 * Plugin URI: https://careoncall.local
 * Description: On-Demand Caretaker Booking System - Connect clients with verified caretakers
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL2
 * Text Domain: careoncall
 * Domain Path: /languages
 * 
 * CareOnCall is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CAREONCALL_VERSION', '1.0.0');
define('CAREONCALL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CAREONCALL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CAREONCALL_INCLUDES_DIR', CAREONCALL_PLUGIN_DIR . 'includes/');
define('CAREONCALL_ADMIN_DIR', CAREONCALL_PLUGIN_DIR . 'admin/');
define('CAREONCALL_PUBLIC_DIR', CAREONCALL_PLUGIN_DIR . 'public/');
define('CAREONCALL_ASSETS_URL', CAREONCALL_PLUGIN_URL . 'assets/');

// Load plugin files
require_once CAREONCALL_INCLUDES_DIR . 'class-careoncall.php';
require_once CAREONCALL_INCLUDES_DIR . 'class-careoncall-activator.php';
require_once CAREONCALL_INCLUDES_DIR . 'class-careoncall-deactivator.php';

// Hook activation and deactivation
register_activation_hook(__FILE__, array('CareOnCall_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('CareOnCall_Deactivator', 'deactivate'));

// Initialize the plugin
add_action('plugins_loaded', array('CareOnCall', 'get_instance'));
