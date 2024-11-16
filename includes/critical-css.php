<?php
/**
 * Critical CSS Generation and Injection
 * 
 * @package CoreMetricsPlus
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CMP_Critical_CSS {
    private static $instance = null;
    private $critical_css_cache = [];
    private $cache_key_prefix = 'cmp_critical_css_';
    private $cache_duration = 86400; // DAY_IN_SECONDS equivalent

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Priority 1: Run before other styles
        add_action('wp_head', [$this, 'inject_critical_css'], 1);
        // Priority 999: Run after theme styles
        add_action('wp_head', [$this, 'defer_non_critical_css'], 999);
        add_action('wp_enqueue_scripts', [$this, 'remove_render_blocking_css'], 999);
    }

    /**
     * Extract critical CSS for current page
     */
    private function extract_critical_css() {
        $url = get_permalink();
        if (!$url) {
            return '';
        }

        // Try to get from cache first
        $cache_key = $this->cache_key_prefix . md5($url);
        $cached_css = get_transient($cache_key);
        if ($cached_css !== false) {
            return $cached_css;
        }

        // Get all enqueued stylesheets
        global $wp_styles;
        if (!isset($wp_styles) || !is_object($wp_styles)) {
            return '';
        }

        $critical_css = '';
        
        if (!empty($wp_styles->queue)) {
            foreach ($wp_styles->queue as $handle) {
                if (!isset($wp_styles->registered[$handle])) {
                    continue;
                }
                
                $stylesheet = $wp_styles->registered[$handle];
                if (!isset($stylesheet->src)) {
                    continue;
                }
                
                $css_file = $stylesheet->src;
                
                // Convert relative URL to absolute
                if (strpos($css_file, '//') === false) {
                    if (function_exists('site_url')) {
                        $css_file = site_url($css_file);
                    } else {
                        continue;
                    }
                }

                // Get CSS content
                $response = wp_remote_get($css_file);
                if (is_wp_error($response)) {
                    continue;
                }

                $css = wp_remote_retrieve_body($response);
                if (empty($css)) {
                    continue;
                }
                
                // Extract critical rules (above the fold)
                $critical_selectors = $this->get_critical_selectors($css);
                if (!empty($critical_selectors)) {
                    $critical_css .= "/* From {$stylesheet->handle} */\n";
                    $critical_css .= implode("\n", $critical_selectors) . "\n";
                }
            }
        }

        // Cache the results
        if (!empty($critical_css)) {
            $critical_css = $this->minify_css($critical_css);
            set_transient($cache_key, $critical_css, $this->cache_duration);
        }

        return $critical_css;
    }

    /**
     * Get critical selectors from CSS content
     */
    private function get_critical_selectors($css) {
        $critical_selectors = [];
        
        // Priority selectors (commonly critical)
        $priority_patterns = [
            // Layout
            '/body\s*{[^}]+}/',
            '/\.container\s*{[^}]+}/',
            '/\.site\s*{[^}]+}/',
            '/\.content\s*{[^}]+}/',
            '/\.header\s*{[^}]+}/',
            '/\.nav\s*{[^}]+}/',
            
            // Typography
            '/h[1-6]\s*{[^}]+}/',
            '/\.entry-title\s*{[^}]+}/',
            '/\.site-title\s*{[^}]+}/',
            
            // Above fold content
            '/\.hero\s*{[^}]+}/',
            '/\.banner\s*{[^}]+}/',
            '/\.slider\s*{[^}]+}/',
            
            // Essential styles
            '/@font-face\s*{[^}]+}/',
            '/@charset[^;]+;/',
            '/@import[^;]+;/',
            
            // Responsive layout
            '/@media\s*\(max-width:[^{]+{[^}]+}/'
        ];

        foreach ($priority_patterns as $pattern) {
            if (preg_match_all($pattern, $css, $matches)) {
                $critical_selectors = array_merge($critical_selectors, $matches[0]);
            }
        }

        return array_unique($critical_selectors);
    }

    /**
     * Basic CSS minification
     */
    private function minify_css($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove space after colons
        $css = str_replace(': ', ':', $css);
        // Remove whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
        return $css;
    }

    /**
     * Inject critical CSS inline
     */
    public function inject_critical_css() {
        if (!function_exists('get_permalink')) {
            return;
        }
        
        $critical_css = $this->extract_critical_css();
        if (!empty($critical_css)) {
            echo "\n<!-- Core Metrics Plus Critical CSS -->\n";
            echo "<style id='cmp-critical-css'>\n";
            echo esc_html($critical_css);
            echo "\n</style>\n";
        }
    }

    /**
     * Defer loading of non-critical CSS
     */
    public function defer_non_critical_css() {
        global $wp_styles;
        if (!isset($wp_styles) || !is_object($wp_styles)) {
            return;
        }

        if (!empty($wp_styles->queue)) {
            foreach ($wp_styles->queue as $handle) {
                if (!isset($wp_styles->registered[$handle])) {
                    continue;
                }
                
                $stylesheet = $wp_styles->registered[$handle];
                if (!isset($stylesheet->src)) {
                    continue;
                }

                wp_dequeue_style($handle);
                wp_deregister_style($handle);
                wp_enqueue_style($handle, $stylesheet->src, array(), null, 'print');
            }
        }
    }

    /**
     * Remove render-blocking CSS
     */
    public function remove_render_blocking_css() {
        global $wp_styles;
        if (!isset($wp_styles) || !is_object($wp_styles)) {
            return;
        }

        $exclude = array('admin-bar', 'dashicons');
        
        if (!empty($wp_styles->queue)) {
            foreach ($wp_styles->queue as $handle) {
                if (in_array($handle, $exclude)) {
                    continue;
                }
                
                if (!isset($wp_styles->registered[$handle])) {
                    continue;
                }
                
                wp_style_add_data($handle, 'preload', true);
            }
        }
    }
}

// Initialize the Critical CSS feature
add_action('init', function() {
    CMP_Critical_CSS::get_instance();
});
