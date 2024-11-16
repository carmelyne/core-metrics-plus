<?php
/**
 * Critical CSS Generation and Injection
 * 
 * @package CoreMetricsPlus
 */

if (!defined('ABSPATH')) {
    exit;
}

class CMP_Critical_CSS {
    private static $instance = null;
    private $cache_key_prefix = 'cmp_critical_css_';
    private $cache_duration = 86400; // 24 hours

    // Admin-specific paths and handles to exclude
    private $excluded_patterns = array(
        // Admin paths
        '/wp-admin/',
        '/wp-includes/css/admin-',
        '/wp-includes/css/customize-',
        '/wp-includes/css/dashboard',
        
        // Admin handles
        'admin-bar',
        'wp-admin',
        'admin-menu',
        'admin-comments',
        'press-this',
        'admin-customizer',
        'customize-controls',
        'customize-widgets',
        'customize-preview',
        'ie',
        'wp-auth-check'
    );

    public static function init() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Only run on frontend
        if (!is_admin() && !is_customize_preview()) {
            add_action('wp_head', array($this, 'inject_critical_css'), 1);
            add_action('wp_enqueue_scripts', array($this, 'defer_styles'), 999);
        }
    }

    public function inject_critical_css() {
        global $wp_styles;
        if (!isset($wp_styles) || empty($wp_styles->queue)) {
            return;
        }

        $critical_css = '';
        foreach ($wp_styles->queue as $handle) {
            // Skip if style is not registered
            if (!isset($wp_styles->registered[$handle])) {
                continue;
            }

            // Skip admin-related styles
            if ($this->is_admin_style($handle)) {
                continue;
            }

            $style = $wp_styles->registered[$handle];
            if (!$style->src) {
                continue;
            }

            $cache_key = $this->cache_key_prefix . md5($style->src);
            $cached = get_transient($cache_key);

            if ($cached !== false) {
                $critical_css .= $cached;
                continue;
            }

            $css_file = $style->src;
            if (strpos($css_file, '//') === 0) {
                $css_file = 'https:' . $css_file;
            } elseif (strpos($css_file, '/') === 0) {
                $css_file = site_url($css_file);
            }

            $response = wp_remote_get($css_file);
            if (!is_wp_error($response)) {
                $css = wp_remote_retrieve_body($response);
                if (!empty($css)) {
                    $processed_css = $this->process_css($css, $handle);
                    $critical_css .= $processed_css;
                    set_transient($cache_key, $processed_css, $this->cache_duration);
                }
            }
        }

        if (!empty($critical_css)) {
            printf(
                "\n<style id='cmp-critical-css'>\n%s\n</style>\n",
                wp_strip_all_tags($critical_css)
            );
        }
    }

    private function is_admin_style($handle) {
        global $wp_styles;
        
        // Check handle against excluded patterns
        foreach ($this->excluded_patterns as $pattern) {
            if (strpos($handle, $pattern) !== false) {
                return true;
            }
        }

        // Check style source against excluded patterns
        if (isset($wp_styles->registered[$handle]) && $wp_styles->registered[$handle]->src) {
            $src = $wp_styles->registered[$handle]->src;
            foreach ($this->excluded_patterns as $pattern) {
                if (strpos($src, $pattern) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function process_css($css, $handle) {
        // Basic minification
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        
        return sprintf("/* From %s */\n%s\n", esc_html($handle), $css);
    }

    public function defer_styles() {
        global $wp_styles;
        if (!isset($wp_styles) || empty($wp_styles->queue)) {
            return;
        }

        foreach ($wp_styles->queue as $handle) {
            // Skip if style is not registered
            if (!isset($wp_styles->registered[$handle])) {
                continue;
            }

            // Skip admin-related styles
            if ($this->is_admin_style($handle)) {
                continue;
            }

            // Defer non-critical styles
            wp_style_add_data($handle, 'media', 'print');
            wp_style_add_data($handle, 'onload', 'this.media=\'all\'');
        }
    }
}

// Initialize Critical CSS
CMP_Critical_CSS::init();
