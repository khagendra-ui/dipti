<?php
/**
 * Helper Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get caretaker details
 */
function careoncall_get_caretaker_details($user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'careoncall_caretaker_details';
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE user_id = %d",
        $user_id
    ));
}

/**
 * Get caretaker availability
 */
function careoncall_get_availability($user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'careoncall_availability';
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE caretaker_id = %d",
        $user_id
    ));
}

/**
 * Check if user is verified caretaker
 */
function careoncall_is_verified_caretaker($user_id) {
    $details = careoncall_get_caretaker_details($user_id);
    return $details && $details->verification_status === 'approved';
}

/**
 * Get all verified caretakers
 */
function careoncall_get_verified_caretakers() {
    global $wpdb;
    $table = $wpdb->prefix . 'careoncall_caretaker_details';
    return $wpdb->get_results(
        "SELECT u.ID, u.user_login, u.user_email, u.display_name, cd.*
         FROM {$wpdb->users} u
         JOIN $table cd ON u.ID = cd.user_id
         WHERE cd.verification_status = 'approved'
         ORDER BY u.display_name"
    );
}

/**
 * Get user bookings
 */
function careoncall_get_user_bookings($user_id) {
    return get_posts(array(
        'post_type' => 'careoncall_booking',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_client_id',
                'value' => $user_id,
                'compare' => '='
            ),
            array(
                'key' => '_caretaker_id',
                'value' => $user_id,
                'compare' => '='
            )
        )
    ));
}

/**
 * Create a booking
 */
function careoncall_create_booking($args = array()) {
    $defaults = array(
        'client_id' => 0,
        'caretaker_id' => 0,
        'booking_date' => '',
        'start_time' => '',
        'end_time' => '',
        'location' => '',
        'service_type' => '',
        'special_requirements' => '',
        'total_hours' => 0,
        'total_cost' => 0,
    );

    $args = wp_parse_args($args, $defaults);

    $booking_id = wp_insert_post(array(
        'post_type' => 'careoncall_booking',
        'post_status' => 'publish',
        'post_title' => 'Booking #' . uniqid(),
    ));

    if ($booking_id) {
        foreach ($args as $key => $value) {
            update_post_meta($booking_id, '_' . $key, $value);
        }

        // Create booking request
        global $wpdb;
        $requests_table = $wpdb->prefix . 'careoncall_booking_requests';
        $wpdb->insert($requests_table, array(
            'booking_id' => $booking_id,
            'caretaker_id' => $args['caretaker_id'],
            'status' => 'pending',
        ));
    }

    return $booking_id;
}

/**
 * Update booking status
 */
function careoncall_update_booking_status($booking_id, $status) {
    wp_update_post(array(
        'ID' => $booking_id,
        'post_status' => $status,
    ));
}

/**
 * Log admin action
 */
function careoncall_log_action($action, $target_user_id = 0, $description = '') {
    global $wpdb;
    $logs_table = $wpdb->prefix . 'careoncall_admin_logs';
    
    $wpdb->insert($logs_table, array(
        'admin_id' => get_current_user_id(),
        'action' => $action,
        'target_user_id' => $target_user_id,
        'description' => $description,
    ));
}

/**
 * Hash password (WordPress way)
 */
function careoncall_hash_password($password) {
    return wp_hash_password($password);
}

/**
 * Verify password (WordPress way)
 */
function careoncall_verify_password($password, $hash) {
    return wp_check_password($password, $hash);
}
