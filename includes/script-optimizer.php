<?php
/**
 * Script Loading Optimization
 *
 * @package CoreMetricsPlus
 */

if (!defined('ABSPATH')) {
    exit;
}

class CMP_Script_Optimizer {
    private static $instance = null;
    
    // Scripts that should load with high priority
    private $critical_scripts = array(
        'jquery',
        'jquery-core',
        'wp-embed'
    );

    // Scripts that can be safely deferred
    private $deferrable_scripts = array(
        'comment-reply',
        'wp-embed',
        'wp-emoji',
        'wp-playlist'
    );

    // Scripts that should be loaded asynchronously
    private $async_scripts = array(
        'google-analytics',
        'ga',
        'gtag',
        'facebook-pixel'
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
            add_action('wp_enqueue_scripts', array($this, 'optimize_script_order'), 9999);
            add_action('wp_head', array($this, 'preload_critical_scripts'), 1);
        }
    }

    /**
     * Optimize script loading by adding async/defer attributes
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

        // Add async to analytics and tracking scripts
        if (in_array($handle, $this->async_scripts)) {
            return str_replace(' src', ' async src', $tag);
        }

        // Add defer to non-critical scripts
        if (in_array($handle, $this->deferrable_scripts)) {
            return str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }

    /**
     * Optimize script loading order
     */
    public function optimize_script_order() {
        global $wp_scripts;

        if (!is_object($wp_scripts)) {
            return;
        }

        foreach ($wp_scripts->registered as $handle => $script) {
            // Remove emoji script if not needed
            if ($handle === 'wp-emoji') {
                remove_action('wp_head', 'print_emoji_detection_script', 7);
                continue;
            }

            // Defer jQuery Migrate if present
            if ($handle === 'jquery-migrate') {
                wp_script_add_data($handle, 'defer', true);
                continue;
            }

            // Set priority for critical scripts
            if (in_array($handle, $this->critical_scripts)) {
                // Move to head with high priority
                wp_scripts()->add_data($handle, 'group', 0);
            } else {
                // Move to footer
                wp_scripts()->add_data($handle, 'group', 1);
            }
        }
    }

    /**
     * Preload critical scripts
     */
    public function preload_critical_scripts() {
        global $wp_scripts;

        if (!is_object($wp_scripts)) {
            return;
        }

        $preload_tags = array();
        foreach ($this->critical_scripts as $handle) {
            if (isset($wp_scripts->registered[$handle]) && $wp_scripts->registered[$handle]->src) {
                $src = $wp_scripts->registered[$handle]->src;
                if (strpos($src, '//') === 0) {
                    $src = 'https:' . $src;
                }
                $preload_tags[] = sprintf(
                    '<link rel="preload" href="%s" as="script" />',
                    esc_url($src)
                );
            }
        }

        if (!empty($preload_tags)) {
            echo "\n" . implode("\n", $preload_tags) . "\n";
        }
    }

    /**
     * Check if a script is a tracking or analytics script
     */
    private function is_tracking_script($handle, $src) {
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
