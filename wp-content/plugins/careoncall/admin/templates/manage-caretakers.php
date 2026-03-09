<?php
/**
 * Manage Caretakers Template
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$caretakers = careoncall_get_verified_caretakers();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th><?php _e('Name', 'careoncall'); ?></th>
                <th><?php _e('Email', 'careoncall'); ?></th>
                <th><?php _e('Experience', 'careoncall'); ?></th>
                <th><?php _e('Rate', 'careoncall'); ?></th>
                <th><?php _e('Status', 'careoncall'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($caretakers as $caretaker): ?>
                <tr>
                    <td><?php echo esc_html($caretaker->display_name); ?></td>
                    <td><?php echo esc_html($caretaker->user_email); ?></td>
                    <td><?php echo esc_html($caretaker->experience_years); ?> years</td>
                    <td>$<?php echo esc_html($caretaker->hourly_rate); ?>/hr</td>
                    <td>
                        <span style="background: #d4edda; padding: 5px 10px; border-radius: 3px;">
                            <?php _e('Verified', 'careoncall'); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
