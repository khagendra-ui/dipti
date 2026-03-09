<?php
/**
 * Frontend Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$user_role = careoncall_user_is_caretaker($user->ID) ? 'caretaker' : 'client';
?>

<div class="careoncall-dashboard">
    <h2><?php printf(__('Welcome, %s!', 'careoncall'), esc_html($user->display_name)); ?></h2>

    <?php if ($user_role === 'caretaker'): ?>
        <div class="careoncall-caretaker-dashboard">
            <div class="careoncall-stats">
                <?php
                $details = careoncall_get_caretaker_details($user->ID);
                $verified = careoncall_user_is_verified_caretaker($user->ID);
                ?>

                <div class="stat-box">
                    <h3><?php _e('Verification Status', 'careoncall'); ?></h3>
                    <p>
                        <?php if ($verified): ?>
                            <span style="color: green;">✓ <?php _e('Verified', 'careoncall'); ?></span>
                        <?php else: ?>
                            <span style="color: orange;">⧗ <?php _e('Pending', 'careoncall'); ?></span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="stat-box">
                    <h3><?php _e('Hourly Rate', 'careoncall'); ?></h3>
                    <p>$<?php echo esc_html($details ? $details->hourly_rate : '0'); ?>/hr</p>
                </div>

                <div class="stat-box">
                    <h3><?php _e('Experience', 'careoncall'); ?></h3>
                    <p><?php echo esc_html($details ? $details->experience_years : '0'); ?> <?php _e('years', 'careoncall'); ?></p>
                </div>
            </div>

            <div class="careoncall-actions">
                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=careoncall_availability'), 'careoncall'); ?>" class="button button-primary">
                    <?php _e('Set Availability', 'careoncall'); ?>
                </a>
                <a href="[careoncall_bookings]" class="button button-secondary">
                    <?php _e('View Bookings', 'careoncall'); ?>
                </a>
            </div>
        </div>

    <?php else: ?>
        <div class="careoncall-client-dashboard">
            <div class="careoncall-actions">
                <a href="[careoncall_browse]" class="button button-primary button-large">
                    <?php _e('Browse Caretakers', 'careoncall'); ?>
                </a>
                <a href="[careoncall_bookings]" class="button button-secondary button-large">
                    <?php _e('My Bookings', 'careoncall'); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .careoncall-dashboard {
        max-width: 1000px;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .careoncall-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .stat-box {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    }

    .stat-box h3 {
        margin: 0 0 10px 0;
        color: #333;
    }

    .stat-box p {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        color: #2196F3;
    }

    .careoncall-actions {
        margin: 30px 0;
    }

    .careoncall-actions .button {
        margin-right: 10px;
        margin-bottom: 10px;
    }
</style>
