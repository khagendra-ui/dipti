<?php
/**
 * User Roles and Capabilities
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'careoncall_register_user_roles');

function careoncall_register_user_roles() {
    // Get subscriber role as base
    $subscriber = get_role('subscriber');

    // Create Client Role
    if (!get_role('careoncall_client')) {
        add_role(
            'careoncall_client',
            __('Client', 'careoncall'),
            array(
                'read' => true,
                'edit_posts' => true,
                'delete_posts' => true,
                'view_careoncall_bookings' => true,
                'create_careoncall_bookings' => true,
            )
        );
    }

    // Create Caretaker Role
    if (!get_role('careoncall_caretaker')) {
        add_role(
            'careoncall_caretaker',
            __('Caretaker', 'careoncall'),
            array(
                'read' => true,
                'edit_posts' => true,
                'edit_published_posts' => true,
                'delete_posts' => true,
                'upload_files' => true,
                'edit_careoncall_caretakers' => true,
                'edit_published_careoncall_caretakers' => true,
                'delete_careoncall_caretakers' => true,
                'view_careoncall_bookings' => true,
                'respond_careoncall_bookings' => true,
            )
        );
    }

    // Add admin capabilities
    $admin = get_role('administrator');
    if ($admin) {
        $admin->add_cap('manage_careoncall');
        $admin->add_cap('verify_caretakers');
        $admin->add_cap('manage_careoncall_bookings');
    }
}

/**
 * Check if user is caretaker
 */
function careoncall_user_is_caretaker($user_id = 0) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $user = get_user_by('ID', $user_id);
    return $user && in_array('careoncall_caretaker', $user->roles);
}

/**
 * Check if user is client
 */
function careoncall_user_is_client($user_id = 0) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $user = get_user_by('ID', $user_id);
    return $user && in_array('careoncall_client', $user->roles);
}

/**
 * Check if user is verified caretaker
 */
function careoncall_user_is_verified_caretaker($user_id = 0) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!careoncall_user_is_caretaker($user_id)) {
        return false;
    }

    $details = careoncall_get_caretaker_details($user_id);
    return $details && $details->verification_status === 'approved';
}
