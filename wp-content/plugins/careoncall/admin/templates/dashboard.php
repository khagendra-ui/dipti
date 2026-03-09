<?php
/**
 * Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div id="dashboard-widgets-wrap">
        <div class="postbox-container" style="width: 100%;">
            <div class="postbox">
                <h2 class="hndle"><span><?php _e('System Overview', 'careoncall'); ?></span></h2>
                <div class="inside">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div style="padding: 20px; background: #f5f5f5; border-radius: 5px;">
                            <h3><?php _e('Total Users', 'careoncall'); ?></h3>
                            <p style="font-size: 32px; font-weight: bold; margin: 10px 0 0 0;">
                                <?php echo $total_users['total_users']; ?>
                            </p>
                        </div>

                        <div style="padding: 20px; background: #f5f5f5; border-radius: 5px;">
                            <h3><?php _e('Total Caretakers', 'careoncall'); ?></h3>
                            <p style="font-size: 32px; font-weight: bold; margin: 10px 0 0 0;">
                                <?php echo $total_caretakers; ?>
                            </p>
                        </div>

                        <div style="padding: 20px; background: #fff3cd; border-radius: 5px;">
                            <h3><?php _e('Pending Verification', 'careoncall'); ?></h3>
                            <p style="font-size: 32px; font-weight: bold; margin: 10px 0 0 0;">
                                <?php echo $pending_caretakers; ?>
                            </p>
                        </div>

                        <div style="padding: 20px; background: #f5f5f5; border-radius: 5px;">
                            <h3><?php _e('Total Bookings', 'careoncall'); ?></h3>
                            <p style="font-size: 32px; font-weight: bold; margin: 10px 0 0 0;">
                                <?php echo $total_bookings; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle"><span><?php _e('Quick Actions', 'careoncall'); ?></span></h2>
                <div class="inside">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">
                            <a href="<?php echo admin_url('admin.php?page=careoncall-verify'); ?>" class="button button-primary">
                                <?php _e('Verify Caretakers', 'careoncall'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="<?php echo admin_url('edit.php?post_type=careoncall_booking'); ?>" class="button button-primary">
                                <?php _e('View All Bookings', 'careoncall'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="<?php echo admin_url('users.php'); ?>" class="button button-primary">
                                <?php _e('Manage Users', 'careoncall'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo admin_url('admin.php?page=careoncall-logs'); ?>" class="button button-primary">
                                <?php _e('View Activity Logs', 'careoncall'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
