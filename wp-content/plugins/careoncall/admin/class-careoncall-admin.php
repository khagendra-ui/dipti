<?php
/**
 * Admin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CareOnCall_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_careoncall_booking', array($this, 'save_booking_meta'));
        add_action('save_post_careoncall_caretaker', array($this, 'save_caretaker_meta'));
        add_action('manage_users_columns', array($this, 'add_user_columns'));
        add_action('manage_users_custom_column', array($this, 'populate_user_columns'), 10, 3);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('CareOnCall', 'careoncall'),
            __('CareOnCall', 'careoncall'),
            'manage_careoncall',
            'careoncall',
            array($this, 'dashboard_page'),
            'dashicons-heart',
            25
        );

        add_submenu_page(
            'careoncall',
            __('Dashboard', 'careoncall'),
            __('Dashboard', 'careoncall'),
            'manage_careoncall',
            'careoncall',
            array($this, 'dashboard_page')
        );

        add_submenu_page(
            'careoncall',
            __('Manage Caretakers', 'careoncall'),
            __('Manage Caretakers', 'careoncall'),
            'manage_careoncall',
            'careoncall-caretakers',
            array($this, 'manage_caretakers_page')
        );

        add_submenu_page(
            'careoncall',
            __('Verify Caretakers', 'careoncall'),
            __('Verify Caretakers', 'careoncall'),
            'verify_caretakers',
            'careoncall-verify',
            array($this, 'verify_caretakers_page')
        );

        add_submenu_page(
            'careoncall',
            __('Activity Logs', 'careoncall'),
            __('Activity Logs', 'careoncall'),
            'manage_careoncall',
            'careoncall-logs',
            array($this, 'activity_logs_page')
        );
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        global $wpdb;

        // Statistics
        $total_users = count_users();
        $total_caretakers = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}careoncall_caretaker_details"
        );
        $pending_caretakers = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}careoncall_caretaker_details WHERE verification_status = 'pending'"
        );
        $total_bookings = wp_count_posts('careoncall_booking')->publish;

        include CAREONCALL_ADMIN_DIR . 'templates/dashboard.php';
    }

    /**
     * Manage caretakers page
     */
    public function manage_caretakers_page() {
        include CAREONCALL_ADMIN_DIR . 'templates/manage-caretakers.php';
    }

    /**
     * Verify caretakers page
     */
    public function verify_caretakers_page() {
        include CAREONCALL_ADMIN_DIR . 'templates/verify-caretakers.php';
    }

    /**
     * Activity logs page
     */
    public function activity_logs_page() {
        include CAREONCALL_ADMIN_DIR . 'templates/activity-logs.php';
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('careoncall_settings', 'careoncall_options');
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        // Booking meta box
        add_meta_box(
            'careoncall_booking_details',
            __('Booking Details', 'careoncall'),
            array($this, 'booking_meta_box'),
            'careoncall_booking',
            'normal',
            'high'
        );

        // Caretaker meta box
        add_meta_box(
            'careoncall_caretaker_details',
            __('Caretaker Information', 'careoncall'),
            array($this, 'caretaker_meta_box'),
            'careoncall_caretaker',
            'normal',
            'high'
        );
    }

    /**
     * Booking meta box
     */
    public function booking_meta_box($post) {
        wp_nonce_field('careoncall_booking_nonce', 'careoncall_nonce');

        $client_id = get_post_meta($post->ID, '_client_id', true);
        $caretaker_id = get_post_meta($post->ID, '_caretaker_id', true);
        $booking_date = get_post_meta($post->ID, '_booking_date', true);
        $start_time = get_post_meta($post->ID, '_start_time', true);
        $end_time = get_post_meta($post->ID, '_end_time', true);
        $location = get_post_meta($post->ID, '_location', true);
        $total_cost = get_post_meta($post->ID, '_total_cost', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="client_id"><?php _e('Client', 'careoncall'); ?></label></th>
                <td>
                    <select name="client_id" id="client_id">
                        <?php
                        $users = get_users(['role' => 'careoncall_client']);
                        foreach ($users as $user) {
                            echo '<option value="' . $user->ID . '" ' . selected($client_id, $user->ID) . '>' . $user->display_name . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="caretaker_id"><?php _e('Caretaker', 'careoncall'); ?></label></th>
                <td>
                    <select name="caretaker_id" id="caretaker_id">
                        <?php
                        $caretakers = get_users(['role' => 'careoncall_caretaker']);
                        foreach ($caretakers as $caretaker) {
                            echo '<option value="' . $caretaker->ID . '" ' . selected($caretaker_id, $caretaker->ID) . '>' . $caretaker->display_name . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="booking_date"><?php _e('Booking Date', 'careoncall'); ?></label></th>
                <td><input type="date" name="booking_date" id="booking_date" value="<?php echo esc_attr($booking_date); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="start_time"><?php _e('Start Time', 'careoncall'); ?></label></th>
                <td><input type="time" name="start_time" id="start_time" value="<?php echo esc_attr($start_time); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="end_time"><?php _e('End Time', 'careoncall'); ?></label></th>
                <td><input type="time" name="end_time" id="end_time" value="<?php echo esc_attr($end_time); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="location"><?php _e('Location', 'careoncall'); ?></label></th>
                <td><input type="text" name="location" id="location" value="<?php echo esc_attr($location); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="total_cost"><?php _e('Total Cost', 'careoncall'); ?></label></th>
                <td><input type="number" name="total_cost" id="total_cost" value="<?php echo esc_attr($total_cost); ?>" step="0.01" class="regular-text"></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Caretaker meta box
     */
    public function caretaker_meta_box($post) {
        wp_nonce_field('careoncall_caretaker_nonce', 'careoncall_nonce');

        // Get caretaker details if exists
        $author_id = $post->post_author;
        $details = careoncall_get_caretaker_details($author_id);

        $experience_years = $details ? $details->experience_years : '';
        $hourly_rate = $details ? $details->hourly_rate : '';
        $skills = $details ? $details->skills : '';
        $verification_status = $details ? $details->verification_status : 'pending';

        ?>
        <table class="form-table">
            <tr>
                <th><label for="experience_years"><?php _e('Experience (Years)', 'careoncall'); ?></label></th>
                <td><input type="number" name="experience_years" id="experience_years" value="<?php echo esc_attr($experience_years); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="hourly_rate"><?php _e('Hourly Rate ($)', 'careoncall'); ?></label></th>
                <td><input type="number" name="hourly_rate" id="hourly_rate" value="<?php echo esc_attr($hourly_rate); ?>" step="0.01" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="skills"><?php _e('Skills', 'careoncall'); ?></label></th>
                <td><textarea name="skills" id="skills" rows="5" class="large-text"><?php echo esc_textarea($skills); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="verification_status"><?php _e('Verification Status', 'careoncall'); ?></label></th>
                <td>
                    <select name="verification_status" id="verification_status">
                        <option value="pending" <?php selected($verification_status, 'pending'); ?>><?php _e('Pending', 'careoncall'); ?></option>
                        <option value="approved" <?php selected($verification_status, 'approved'); ?>><?php _e('Approved', 'careoncall'); ?></option>
                        <option value="rejected" <?php selected($verification_status, 'rejected'); ?>><?php _e('Rejected', 'careoncall'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save booking meta
     */
    public function save_booking_meta($post_id) {
        if (!isset($_POST['careoncall_nonce']) || !wp_verify_nonce($_POST['careoncall_nonce'], 'careoncall_booking_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($_POST['post_type'] !== 'careoncall_booking') {
            return;
        }

        $fields = ['client_id', 'caretaker_id', 'booking_date', 'start_time', 'end_time', 'location', 'total_cost'];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    /**
     * Save caretaker meta
     */
    public function save_caretaker_meta($post_id) {
        if (!isset($_POST['careoncall_nonce']) || !wp_verify_nonce($_POST['careoncall_nonce'], 'careoncall_caretaker_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($_POST['post_type'] !== 'careoncall_caretaker') {
            return;
        }

        $author_id = get_post($post_id)->post_author;
        global $wpdb;
        $table = $wpdb->prefix . 'careoncall_caretaker_details';

        $data = array(
            'user_id' => $author_id,
            'experience_years' => intval($_POST['experience_years'] ?? 0),
            'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
            'skills' => sanitize_textarea_field($_POST['skills'] ?? ''),
            'verification_status' => sanitize_text_field($_POST['verification_status'] ?? 'pending'),
        );

        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d",
            $author_id
        ));

        if ($existing) {
            $wpdb->update($table, $data, ['user_id' => $author_id]);
        } else {
            $wpdb->insert($table, $data);
        }
    }

    /**
     * Add user columns
     */
    public function add_user_columns($columns) {
        $columns['user_type'] = __('User Type', 'careoncall');
        $columns['verification'] = __('Verification Status', 'careoncall');
        return $columns;
    }

    /**
     * Populate user columns
     */
    public function populate_user_columns($output, $column_name, $user_id) {
        if ($column_name === 'user_type') {
            $user = get_user_by('ID', $user_id);
            $roles = $user->roles;
            if (in_array('careoncall_caretaker', $roles)) {
                return 'Caretaker';
            } elseif (in_array('careoncall_client', $roles)) {
                return 'Client';
            } else {
                return 'Other';
            }
        }

        if ($column_name === 'verification') {
            $details = careoncall_get_caretaker_details($user_id);
            if ($details) {
                return ucfirst($details->verification_status);
            }
            return '—';
        }

        return $output;
    }
}
