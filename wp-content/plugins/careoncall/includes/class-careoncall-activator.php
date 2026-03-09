<?php
/**
 * Plugin Activation Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CareOnCall_Activator {

    /**
     * Activate plugin
     */
    public static function activate() {
        // Create custom database tables
        self::create_tables();

        // Register custom post types
        self::register_post_types();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Create default admin user
        self::create_default_admin();
    }

    /**
     * Create plugin database tables
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Caretaker details table
        $caretaker_table = $wpdb->prefix . 'careoncall_caretaker_details';
        $caretaker_sql = "CREATE TABLE IF NOT EXISTS $caretaker_table (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT NOT NULL,
            experience_years INT,
            skills LONGTEXT,
            bio LONGTEXT,
            hourly_rate DECIMAL(10, 2),
            certification_document VARCHAR(255),
            verification_status VARCHAR(20) DEFAULT 'pending',
            verification_date DATETIME,
            admin_notes LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user (user_id),
            KEY verification_status (verification_status),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Availability table
        $availability_table = $wpdb->prefix . 'careoncall_availability';
        $availability_sql = "CREATE TABLE IF NOT EXISTS $availability_table (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            caretaker_id BIGINT NOT NULL,
            day_of_week VARCHAR(20) NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            is_available BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_availability (caretaker_id, day_of_week),
            KEY caretaker_id (caretaker_id)
        ) $charset_collate;";

        // Booking requests table
        $requests_table = $wpdb->prefix . 'careoncall_booking_requests';
        $requests_sql = "CREATE TABLE IF NOT EXISTS $requests_table (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            booking_id BIGINT NOT NULL,
            caretaker_id BIGINT NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            caretaker_response LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            responded_at DATETIME,
            KEY booking_id (booking_id),
            KEY caretaker_id (caretaker_id),
            KEY status (status)
        ) $charset_collate;";

        // Admin logs table
        $logs_table = $wpdb->prefix . 'careoncall_admin_logs';
        $logs_sql = "CREATE TABLE IF NOT EXISTS $logs_table (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            admin_id BIGINT,
            action VARCHAR(255) NOT NULL,
            target_user_id BIGINT,
            description LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY admin_id (admin_id),
            KEY target_user_id (target_user_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($caretaker_sql);
        dbDelta($availability_sql);
        dbDelta($requests_sql);
        dbDelta($logs_sql);
    }

    /**
     * Register post types during activation
     */
    private static function register_post_types() {
        do_action('careoncall_register_post_types');
        flush_rewrite_rules();
    }

    /**
     * Create default admin user
     */
    private static function create_default_admin() {
        $admin_user = get_user_by('login', 'admin');

        if (!$admin_user) {
            wp_create_user(
                'admin',
                'admin@123',
                'admin@careoncall.com'
            );

            $admin = get_user_by('login', 'admin');
            $admin->set_role('administrator');
        }
    }
}
