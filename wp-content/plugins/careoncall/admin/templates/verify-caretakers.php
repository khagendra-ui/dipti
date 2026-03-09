<?php
/**
 * Verify Caretakers Template
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'careoncall_caretaker_details';
$pending = $wpdb->get_results(
    "SELECT u.ID, u.display_name, u.user_email, u.user_nicename, cd.*
     FROM {$wpdb->users} u
     JOIN $table cd ON u.ID = cd.user_id
     WHERE cd.verification_status = 'pending'"
);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (empty($pending)): ?>
        <div class="notice notice-success inline">
            <p><?php _e('No pending caretaker applications.', 'careoncall'); ?></p>
        </div>
    <?php else: ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Name', 'careoncall'); ?></th>
                    <th><?php _e('Email', 'careoncall'); ?></th>
                    <th><?php _e('Experience', 'careoncall'); ?></th>
                    <th><?php _e('Skills', 'careoncall'); ?></th>
                    <th><?php _e('Applied', 'careoncall'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $caretaker): ?>
                    <tr>
                        <td>
                            <a href="<?php echo get_edit_user_link($caretaker->ID); ?>">
                                <?php echo esc_html($caretaker->display_name); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($caretaker->user_email); ?></td>
                        <td><?php echo esc_html($caretaker->experience_years); ?> years</td>
                        <td><?php echo esc_html(substr($caretaker->skills, 0, 50)); ?>...</td>
                        <td><?php echo esc_html(date_i18n('M d, Y', strtotime($caretaker->created_at))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
