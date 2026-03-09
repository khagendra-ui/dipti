<?php
/**
 * Bookings Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$bookings = careoncall_get_user_bookings($user->ID);
?>

<div class="careoncall-bookings">
    <h2><?php _e('My Bookings', 'careoncall'); ?></h2>

    <?php if (empty($bookings)): ?>
        <p><?php _e('No bookings yet.', 'careoncall'); ?></p>
    <?php else: ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'careoncall'); ?></th>
                    <th><?php _e('Date', 'careoncall'); ?></th>
                    <th><?php _e('Time', 'careoncall'); ?></th>
                    <th><?php _e('Location', 'careoncall'); ?></th>
                    <th><?php _e('Cost', 'careoncall'); ?></th>
                    <th><?php _e('Status', 'careoncall'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo '#' . esc_html($booking->ID); ?></td>
                        <td><?php echo esc_html(get_post_meta($booking->ID, '_booking_date', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($booking->ID, '_start_time', true)); ?> - <?php echo esc_html(get_post_meta($booking->ID, '_end_time', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($booking->ID, '_location', true)); ?></td>
                        <td>$<?php echo esc_html(get_post_meta($booking->ID, '_total_cost', true)); ?></td>
                        <td>
                            <span style="background: #d4edda; padding: 5px 10px; border-radius: 3px;">
                                <?php echo esc_html($booking->post_status); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
    .careoncall-bookings {
        max-width: 1000px;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 5px;
    }

    .widefat {
        width: 100%;
        border-collapse: collapse;
    }

    .widefat th,
    .widefat td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .widefat th {
        background: #f5f5f5;
        font-weight: bold;
    }

    .striped tbody tr:nth-child(odd) {
        background: #f9f9f9;
    }
</style>
