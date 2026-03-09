<?php
/**
 * Custom Post Types Setup
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register during init
add_action('init', 'careoncall_register_custom_post_types');

function careoncall_register_custom_post_types() {
    // Caretaker Post Type
    register_post_type('careoncall_caretaker', array(
        'label' => __('Caretakers', 'careoncall'),
        'description' => __('Caretaker profiles', 'careoncall'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'hierarchical' => false,
        'exclude_from_search' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies' => array('caretaker_skills', 'service_type'),
        'rewrite' => array(
            'slug' => 'caretaker',
            'with_front' => true,
            'pages' => true,
            'feeds' => true,
        ),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-smiley',
        'labels' => array(
            'name' => __('Caretakers', 'careoncall'),
            'singular_name' => __('Caretaker', 'careoncall'),
            'menu_name' => __('Caretakers', 'careoncall'),
            'all_items' => __('All Caretakers', 'careoncall'),
            'add_new' => __('Add New Caretaker', 'careoncall'),
            'add_new_item' => __('Add New Caretaker', 'careoncall'),
            'edit_item' => __('Edit Caretaker', 'careoncall'),
            'new_item' => __('New Caretaker', 'careoncall'),
            'view_item' => __('View Caretaker', 'careoncall'),
            'view_items' => __('View Caretakers', 'careoncall'),
            'search_items' => __('Search Caretakers', 'careoncall'),
        ),
    ));

    // Booking Post Type
    register_post_type('careoncall_booking', array(
        'label' => __('Bookings', 'careoncall'),
        'description' => __('Service bookings', 'careoncall'),
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=careoncall_caretaker',
        'show_in_nav_menus' => false,
        'show_in_rest' => true,
        'has_archive' => false,
        'hierarchical' => false,
        'exclude_from_search' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'supports' => array('title'),
        'rewrite' => false,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-calendar-alt',
        'labels' => array(
            'name' => __('Bookings', 'careoncall'),
            'singular_name' => __('Booking', 'careoncall'),
            'menu_name' => __('Bookings', 'careoncall'),
            'all_items' => __('All Bookings', 'careoncall'),
            'edit_item' => __('Edit Booking', 'careoncall'),
            'view_item' => __('View Booking', 'careoncall'),
        ),
    ));

    // Review Post Type
    register_post_type('careoncall_review', array(
        'label' => __('Reviews', 'careoncall'),
        'description' => __('Caretaker reviews and ratings', 'careoncall'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=careoncall_caretaker',
        'show_in_nav_menus' => false,
        'show_in_rest' => true,
        'has_archive' => false,
        'hierarchical' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug' => 'review'),
        'menu_position' => 7,
        'menu_icon' => 'dashicons-star-filled',
        'labels' => array(
            'name' => __('Reviews', 'careoncall'),
            'singular_name' => __('Review', 'careoncall'),
            'menu_name' => __('Reviews', 'careoncall'),
            'all_items' => __('All Reviews', 'careoncall'),
            'add_new' => __('Add Review', 'careoncall'),
            'edit_item' => __('Edit Review', 'careoncall'),
            'view_item' => __('View Review', 'careoncall'),
        ),
    ));

    // Taxonomies
    register_taxonomy('caretaker_skills', array('careoncall_caretaker'), array(
        'label' => __('Skills', 'careoncall'),
        'public' => true,
        'publicly_queryable' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'rewrite' => array(
            'slug' => 'skill',
            'with_front' => true,
        ),
    ));

    register_taxonomy('service_type', array('careoncall_booking', 'careoncall_caretaker'), array(
        'label' => __('Service Type', 'careoncall'),
        'public' => true,
        'publicly_queryable' => true,
        'hierarchical' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_rest' => true,
        'rewrite' => array(
            'slug' => 'service-type',
            'with_front' => true,
        ),
    ));
}
