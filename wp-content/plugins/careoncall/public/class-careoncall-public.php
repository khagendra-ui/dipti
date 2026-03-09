<?php
/**
 * Public Frontend Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CareOnCall_Public {

    public function __construct() {
        add_action('init', array($this, 'register_shortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_nopriv_careoncall_search_caretakers', array($this, 'search_caretakers_ajax'));
        add_action('wp_ajax_careoncall_create_booking', array($this, 'create_booking_ajax'));
        add_action('wp_ajax_careoncall_respond_booking', array($this, 'respond_booking_ajax'));
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('careoncall_dashboard', array($this, 'dashboard_shortcode'));
        add_shortcode('careoncall_browse', array($this, 'browse_shortcode'));
        add_shortcode('careoncall_bookings', array($this, 'bookings_shortcode'));
        add_shortcode('careoncall_profile', array($this, 'profile_shortcode'));
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        if (!is_user_logged_in()) {
            return;
        }

        wp_enqueue_style(
            'careoncall-frontend',
            CAREONCALL_ASSETS_URL . 'css/frontend.css',
            array(),
            CAREONCALL_VERSION
        );

        wp_enqueue_script(
            'careoncall-frontend',
            CAREONCALL_ASSETS_URL . 'js/frontend.js',
            array('jquery'),
            CAREONCALL_VERSION,
            true
        );

        wp_localize_script('careoncall-frontend', 'careoncall_data', array(
            'user_id' => get_current_user_id(),
            'user_role' => $this->get_user_role(),
            'nonce' => wp_create_nonce('careoncall_frontend_nonce'),
        ));
    }

    /**
     * Dashboard shortcode
     */
    public function dashboard_shortcode() {
        if (!is_user_logged_in()) {
            return '<p style="color: red;">' . __('You must be logged in.', 'careoncall') . '</p>';
        }

        ob_start();
        include CAREONCALL_PUBLIC_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }

    /**
     * Browse caretakers shortcode
     */
    public function browse_shortcode() {
        if (!is_user_logged_in()) {
            return '<p style="color: red;">' . __('You must be logged in.', 'careoncall') . '</p>';
        }

        ob_start();
        include CAREONCALL_PUBLIC_DIR . 'templates/browse.php';
        return ob_get_clean();
    }

    /**
     * Bookings shortcode
     */
    public function bookings_shortcode() {
        if (!is_user_logged_in()) {
            return '<p style="color: red;">' . __('You must be logged in.', 'careoncall') . '</p>';
        }

        ob_start();
        include CAREONCALL_PUBLIC_DIR . 'templates/bookings.php';
        return ob_get_clean();
    }

    /**
     * Profile shortcode
     */
    public function profile_shortcode() {
        if (!is_user_logged_in()) {
            return '<p style="color: red;">' . __('You must be logged in.', 'careoncall') . '</p>';
        }

        ob_start();
        include CAREONCALL_PUBLIC_DIR . 'templates/profile.php';
        return ob_get_clean();
    }

    /**
     * Search caretakers AJAX
     */
    public function search_caretakers_ajax() {
        $search = sanitize_text_field($_POST['search'] ?? '');

        $caretakers = careoncall_get_verified_caretakers();

        if ($search) {
            $caretakers = array_filter($caretakers, function($c) use ($search) {
                return stripos($c->display_name, $search) !== false || stripos($c->skills, $search) !== false;
            });
        }

        wp_send_json_success($caretakers);
    }

    /**
     * Create booking AJAX
     */
    public function create_booking_ajax() {
        if (!wp_verify_nonce($_POST['nonce'], 'careoncall_frontend_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        $booking_id = careoncall_create_booking(array(
            'client_id' => get_current_user_id(),
            'caretaker_id' => intval($_POST['caretaker_id']),
            'booking_date' => sanitize_text_field($_POST['booking_date']),
            'start_time' => sanitize_text_field($_POST['start_time']),
            'end_time' => sanitize_text_field($_POST['end_time']),
            'location' => sanitize_text_field($_POST['location']),
            'service_type' => sanitize_text_field($_POST['service_type']),
            'special_requirements' => sanitize_textarea_field($_POST['special_requirements']),
            'total_hours' => floatval($_POST['total_hours']),
            'total_cost' => floatval($_POST['total_cost']),
        ));

        careoncall_log_action('Booking created', intval($_POST['caretaker_id']), 'New booking #' . $booking_id);

        wp_send_json_success(array('booking_id' => $booking_id));
    }

    /**
     * Respond to booking AJAX
     */
    public function respond_booking_ajax() {
        if (!wp_verify_nonce($_POST['nonce'], 'careoncall_frontend_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'careoncall_booking_requests';
        $booking_id = intval($_POST['booking_id']);
        $status = sanitize_text_field($_POST['status']); // 'accepted' or 'rejected'
        $response = sanitize_textarea_field($_POST['response'] ?? '');

        $wpdb->update($table, array(
            'status' => $status,
            'caretaker_response' => $response,
            'responded_at' => current_time('mysql'),
        ), array('booking_id' => $booking_id));

        careoncall_log_action('Booking ' . $status, get_current_user_id(), 'Caretaker response to booking #' . $booking_id);

        wp_send_json_success();
    }

    /**
     * Get user role
     */
    private function get_user_role() {
        $user = wp_get_current_user();
        if (in_array('careoncall_caretaker', $user->roles)) {
            return 'caretaker';
        } elseif (in_array('careoncall_client', $user->roles)) {
            return 'client';
        } elseif (in_array('administrator', $user->roles)) {
            return 'admin';
        }
        return 'user';
    }
}
