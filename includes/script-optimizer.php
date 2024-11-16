<?php
/**
 * Minimal Script Optimization
 * Only handles analytics and non-critical scripts without affecting core functionality
 *
 * @package CoreMetricsPlus
 */

if (!defined('ABSPATH')) {
    exit;
}

class CMP_Script_Optimizer {
    private static $instance = null;
    
    // Scripts that should be loaded asynchronously (typically analytics and tracking)
    private $async_scripts = array(
        'google-analytics',
        'ga',
        'gtag',
        'facebook-pixel',
        'fbevents',
        'hotjar',
        'clarity'
    );

    public static function init() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Only run on frontend
        if (!is_admin()) {
            add_filter('script_loader_tag', array($this, 'optimize_script_loading'), 10, 3);
        }
    }

    /**
     * Only add async to analytics/tracking scripts
     * Don't touch core WordPress scripts or jQuery
     */
    public function optimize_script_loading($tag, $handle, $src) {
        // Don't modify admin scripts
        if (is_admin()) {
            return $tag;
        }

        // Skip if already modified
        if (strpos($tag, 'async') !== false || strpos($tag, 'defer') !== false) {
            return $tag;
        }

        // Skip core WordPress scripts
        if (strpos($handle, 'wp-') === 0 || $handle === 'jquery' || $handle === 'jquery-core') {
            return $tag;
        }

        // Add async to analytics and tracking scripts
        if ($this->is_tracking_script($handle, $src)) {
            return str_replace(' src', ' async src', $tag);
        }

        return $tag;
    }

    /**
     * Check if a script is a tracking or analytics script
     */
    private function is_tracking_script($handle, $src) {
        // Check against our known list
        if (in_array($handle, $this->async_scripts)) {
            return true;
        }

        // Check URL patterns
        $tracking_patterns = array(
            'analytics',
            'gtag',
            'fbevents',
            'pixel',
            'hotjar',
            'clarity'
        );

        foreach ($tracking_patterns as $pattern) {
            if (strpos($handle, $pattern) !== false || strpos($src, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}

// Initialize Script Optimizer
CMP_Script_Optimizer::init();
