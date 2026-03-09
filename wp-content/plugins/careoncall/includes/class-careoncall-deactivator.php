<?php
/**
 * Plugin Deactivation Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class CareOnCall_Deactivator {

    /**
     * Deactivate plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
