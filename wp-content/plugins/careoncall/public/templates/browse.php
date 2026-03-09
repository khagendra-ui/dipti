<?php
/**
 * Browse Caretakers Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$caretakers = careoncall_get_verified_caretakers();

if ($search) {
    $caretakers = array_filter($caretakers, function($c) use ($search) {
        return stripos($c->display_name, $search) !== false || stripos($c->skills, $search) !== false;
    });
}
?>

<div class="careoncall-browse">
    <h2><?php _e('Browse Caretakers', 'careoncall'); ?></h2>

    <div class="careoncall-search" style="margin: 30px 0;">
        <form method="get">
            <input type="text" name="search" placeholder="<?php _e('Search by name or skills...', 'careoncall'); ?>" value="<?php echo esc_attr($search); ?>">
            <button type="submit" class="button button-primary"><?php _e('Search', 'careoncall'); ?></button>
        </form>
    </div>

    <div class="careoncall-caretakers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php foreach ($caretakers as $caretaker): ?>
            <div class="careoncall-caretaker-card" style="border: 1px solid #ddd; border-radius: 5px; padding: 20px; background: #fff;">
                <h3><?php echo esc_html($caretaker->display_name); ?></h3>

                <div class="careoncall-caretaker-info">
                    <p><strong><?php _e('Rate:', 'careoncall'); ?></strong> $<?php echo esc_html($caretaker->hourly_rate); ?>/hr</p>
                    <p><strong><?php _e('Experience:', 'careoncall'); ?></strong> <?php echo esc_html($caretaker->experience_years); ?> years</p>
                    <p><strong><?php _e('Skills:', 'careoncall'); ?></strong> <?php echo esc_html(substr($caretaker->skills, 0, 100)); ?></p>
                </div>

                <button class="button button-primary careoncall-book-btn" data-caretaker-id="<?php echo esc_attr($caretaker->ID); ?>" onclick="careoncallShowBookingForm(<?php echo esc_attr($caretaker->ID); ?>)">
                    <?php _e('Book Now', 'careoncall'); ?>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($caretakers)): ?>
        <p><?php _e('No caretakers found.', 'careoncall'); ?></p>
    <?php endif; ?>
</div>

<style>
    .careoncall-browse {
        max-width: 1200px;
        margin: 20px auto;
    }

    .careoncall-search form {
        display: flex;
        gap: 10px;
    }

    .careoncall-search input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }

    .careoncall-caretaker-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .careoncall-caretaker-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .careoncall-caretaker-info p {
        margin: 10px 0;
        font-size: 14px;
    }
</style>
