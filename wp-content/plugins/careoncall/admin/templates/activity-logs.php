<?php
/**
 * Activity Logs Template
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'careoncall_admin_logs';
$logs = $wpdb->get_results(
    "SELECT al.*, u.display_name as admin_name
     FROM $table al
     LEFT JOIN {$wpdb->users} u ON al.admin_id = u.ID
     ORDER BY al.created_at DESC
     LIMIT 100"
);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th><?php _e('Admin', 'careoncall'); ?></th>
                <th><?php _e('Action', 'careoncall'); ?></th>
                <th><?php _e('Target User', 'careoncall'); ?></th>
                <th><?php _e('Description', 'careoncall'); ?></th>
                <th><?php _e('Date', 'careoncall'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo esc_html($log->admin_name ?? 'System'); ?></td>
                    <td><?php echo esc_html($log->action); ?></td>
                    <td><?php echo esc_html($log->target_user_id ? get_user_by('ID', $log->target_user_id)->display_name : '—'); ?></td>
                    <td><?php echo esc_html($log->description); ?></td>
                    <td><?php echo date_i18n('M d, Y H:i', strtotime($log->created_at)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
