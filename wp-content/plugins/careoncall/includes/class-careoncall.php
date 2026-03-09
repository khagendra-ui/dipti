<?php
/**
 * Main Plugin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CareOnCall {

    private static $instance = null;

    /**
     * Get instance of the plugin
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Load admin files
        if (is_admin()) {
            require_once CAREONCALL_ADMIN_DIR . 'class-careoncall-admin.php';
            new CareOnCall_Admin();
        }

        // Load public files
        require_once CAREONCALL_PUBLIC_DIR . 'class-careoncall-public.php';
        new CareOnCall_Public();

        // Load helpers
        require_once CAREONCALL_INCLUDES_DIR . 'helpers.php';
        require_once CAREONCALL_INCLUDES_DIR . 'custom-post-types.php';
        require_once CAREONCALL_INCLUDES_DIR . 'user-roles.php';

        // Register hooks
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('init', array($this, 'register_custom_roles'));

        // Enqueue styles and scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Caretaker post type
        register_post_type('careoncall_caretaker', array(
            'label' => 'Caretakers',
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'rewrite' => array('slug' => 'caretaker'),
            'menu_icon' => 'dashicons-smiley',
        ));

        // Booking post type
        register_post_type('careoncall_booking', array(
            'label' => 'Bookings',
            'public' => false,
            'show_in_rest' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'supports' => array('title'),
            'rewrite' => array('slug' => 'booking'),
            'menu_icon' => 'dashicons-calendar-alt',
        ));

        // Review post type
        register_post_type('careoncall_review', array(
            'label' => 'Reviews',
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor'),
            'rewrite' => array('slug' => 'review'),
            'menu_icon' => 'dashicons-star-filled',
        ));
    }

    /**
     * Register post type taxonomies
     */
    public function register_taxonomies() {
        // Service type taxonomy
        register_taxonomy('service_type', array('careoncall_booking', 'careoncall_caretaker'), array(
            'label' => 'Service Type',
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'service-type'),
        ));

        // Caretaker skills taxonomy
        register_taxonomy('caretaker_skills', 'careoncall_caretaker', array(
            'label' => 'Skills',
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'skill'),
        ));
    }

    /**
     * Register custom user roles
     */
    public function register_custom_roles() {
        $subscriber = get_role('subscriber');

        // Client role
        if (!get_role('careoncall_client')) {
            add_role('careoncall_client', 'Client', $subscriber->capabilities);
        }

        // Caretaker role
        if (!get_role('careoncall_caretaker')) {
            $caretaker_cap = $subscriber->capabilities;
            $caretaker_cap['edit_posts'] = true;
            $caretaker_cap['edit_careoncall_caretakers'] = true;
            add_role('careoncall_caretaker', 'Caretaker', $caretaker_cap);
        }
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'careoncall-style',
            CAREONCALL_ASSETS_URL . 'css/careoncall.css',
            array(),
            CAREONCALL_VERSION
        );

        wp_enqueue_script(
            'careoncall-script',
            CAREONCALL_ASSETS_URL . 'js/careoncall.js',
            array('jquery'),
            CAREONCALL_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('careoncall-script', 'careoncall_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('careoncall_nonce'),
        ));
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style(
            'careoncall-admin-style',
            CAREONCALL_ASSETS_URL . 'css/admin.css',
            array(),
            CAREONCALL_VERSION
        );

        wp_enqueue_script(
            'careoncall-admin-script',
            CAREONCALL_ASSETS_URL . 'js/admin.js',
            array('jquery'),
            CAREONCALL_VERSION,
            true
        );
    }
}
