<?php
/**
 * Profile Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$is_caretaker = careoncall_user_is_caretaker($user->ID);
$details = $is_caretaker ? careoncall_get_caretaker_details($user->ID) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    // Update user
    wp_update_user(array(
        'ID' => $user->ID,
        'display_name' => sanitize_text_field($_POST['display_name']),
        'user_email' => sanitize_email($_POST['user_email']),
    ));

    // Update caretaker details if applicable
    if ($is_caretaker) {
        global $wpdb;
        $table = $wpdb->prefix . 'careoncall_caretaker_details';

        $data = array(
            'experience_years' => intval($_POST['experience_years'] ?? 0),
            'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
            'skills' => sanitize_textarea_field($_POST['skills'] ?? ''),
        );

        $where = array('user_id' => $user->ID);

        if ($details) {
            $wpdb->update($table, $data, $where);
        } else {
            $data['user_id'] = $user->ID;
            $wpdb->insert($table, $data);
        }
    }

    wp_safe_remote_get(admin_url('admin-ajax.php'), array('blocking' => false));
    echo '<div class="notice notice-success"><p>' . __('Profile updated successfully!', 'careoncall') . '</p></div>';
}
?>

<div class="careoncall-profile">
    <h2><?php _e('My Profile', 'careoncall'); ?></h2>

    <form method="post" class="careoncall-profile-form">
        <input type="hidden" name="action" value="update_profile">

        <table class="form-table">
            <tr>
                <th><label for="display_name"><?php _e('Full Name', 'careoncall'); ?></label></th>
                <td><input type="text" name="display_name" id="display_name" value="<?php echo esc_attr($user->display_name); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="user_email"><?php _e('Email', 'careoncall'); ?></label></th>
                <td><input type="email" name="user_email" id="user_email" value="<?php echo esc_attr($user->user_email); ?>" class="regular-text"></td>
            </tr>

            <?php if ($is_caretaker && $details): ?>
                <tr>
                    <th><label for="experience_years"><?php _e('Experience (Years)', 'careoncall'); ?></label></th>
                    <td><input type="number" name="experience_years" id="experience_years" value="<?php echo esc_attr($details->experience_years); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="hourly_rate"><?php _e('Hourly Rate ($)', 'careoncall'); ?></label></th>
                    <td><input type="number" name="hourly_rate" id="hourly_rate" value="<?php echo esc_attr($details->hourly_rate); ?>" step="0.01" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="skills"><?php _e('Skills', 'careoncall'); ?></label></th>
                    <td><textarea name="skills" id="skills" rows="5" class="large-text"><?php echo esc_textarea($details->skills); ?></textarea></td>
                </tr>
            <?php endif; ?>
        </table>

        <p>
            <button type="submit" class="button button-primary">
                <?php _e('Save Changes', 'careoncall'); ?>
            </button>
        </p>
    </form>
</div>

<style>
    .careoncall-profile {
        max-width: 600px;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-table {
        width: 100%;
        border-collapse: collapse;
    }

    .form-table th {
        text-align: left;
        padding: 15px;
        background: #f5f5f5;
        font-weight: bold;
    }

    .form-table td {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .regular-text,
    .large-text {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-family: inherit;
    }

    .button {
        padding: 10px 20px;
        background: #2196F3;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 14px;
    }

    .button:hover {
        background: #0b7dda;
    }
</style>
