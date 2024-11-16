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
    
    // Styles that should always be included in critical CSS
    private $critical_handles = array(
        'theme-style',     // Theme's main stylesheet
        'theme-styles',    // Alternative theme style handle
        'style',          // Common theme style handle
        'main-style',     // Another common theme style handle
        'site-style',     // Site-wide styles
        'header-style',   // Header styles
        'twenty',         // Twenty* theme styles
        'elementor',      // Elementor styles
        'astra',          // Astra theme styles
        'generate',       // GeneratePress styles
        'oceanwp',        // OceanWP styles
        'kadence'         // Kadence styles
    );

    // Only exclude obvious admin styles
    private $excluded_handles = array(
        'admin-bar',
        'wp-admin',
        'customize-preview',
        'wp-custom-admin'
    );

    // CSS rules that should always be included
    private $critical_selectors = array(
        'body',
        'header',
        '#header',
        '.header',
        '#logo',
        '.logo',
        '.site-logo',
        '.custom-logo',
        '.custom-logo-link',
        '.wp-custom-logo',
        'img',
        '.wp-post-image',
        'figure',
        '.wp-block-image',
        '#content',
        '.content',
        '.entry-content',
        '.site-content',
        'article',
        '.post',
        '.page',
        '.post-thumbnail',
        '.attachment-',    // Catches all attachment image sizes
        '.size-',         // Catches all size-specific styles
        '.featured-image',
        '.entry-header',
        '.site-header',
        '.site-branding',
        'nav',
        '.nav',
        '.menu',
        '#site-navigation',
        '.main-navigation',
        '.navigation'
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

        $critical_css = $this->get_base_critical_css();
        
        // Get the current theme's stylesheet handle
        $theme_style = get_stylesheet();
        
        foreach ($wp_styles->queue as $handle) {
            // Skip if style is not registered
            if (!isset($wp_styles->registered[$handle])) {
                continue;
            }

            // Skip obvious admin-related styles
            if ($this->is_admin_style($handle)) {
                continue;
            }

            $style = $wp_styles->registered[$handle];
            if (!$style->src) {
                continue;
            }

            // Consider a style critical if:
            // 1. It's in our critical handles list
            // 2. It's the current theme's stylesheet
            // 3. It's a dependency of the theme's stylesheet
            $is_critical = $this->is_critical_handle($handle) || 
                          $handle === $theme_style || 
                          $this->is_theme_dependency($handle);
            
            $cache_key = $this->cache_key_prefix . md5($style->src);
            $cached = get_transient($cache_key);

            if ($cached !== false) {
                if ($is_critical) {
                    $critical_css .= $cached;
                }
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
                    $processed_css = $this->process_css($css, $handle, $is_critical);
                    if ($is_critical || $this->has_critical_styles($css)) {
                        $critical_css .= $processed_css;
                    }
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

    private function get_base_critical_css() {
        // Essential CSS rules that should always be included
        return "
            /* Base critical CSS */
            img, figure { max-width: 100%; height: auto; display: block; }
            .custom-logo-link, .custom-logo, .site-logo, .wp-post-image { display: block; max-width: 100%; height: auto; }
            .wp-block-image img { height: auto; max-width: 100%; }
            .post-thumbnail img { max-width: 100%; height: auto; display: block; }
            .site-header, .site-branding { display: block; }
            .featured-image img { max-width: 100%; height: auto; display: block; }
            body { opacity: 1 !important; }
            
            /* Fix for various theme layouts */
            .site-logo-img img { display: block; max-width: 100%; }
            .header-image img { display: block; max-width: 100%; }
            .site-branding img { display: block; max-width: 100%; }
        ";
    }

    private function is_critical_handle($handle) {
        // Check if it's a critical handle
        foreach ($this->critical_handles as $critical) {
            if (strpos($handle, $critical) !== false) {
                return true;
            }
        }
        return false;
    }

    private function is_theme_dependency($handle) {
        global $wp_styles;
        $theme_style = get_stylesheet();
        
        if (isset($wp_styles->registered[$theme_style])) {
            $deps = $wp_styles->registered[$theme_style]->deps;
            return in_array($handle, $deps);
        }
        
        return false;
    }

    private function has_critical_styles($css) {
        foreach ($this->critical_selectors as $selector) {
            if (strpos($css, $selector) !== false) {
                return true;
            }
        }
        return false;
    }

    private function is_admin_style($handle) {
        // Only exclude obvious admin styles
        foreach ($this->excluded_handles as $excluded) {
            if (strpos($handle, $excluded) !== false) {
                return true;
            }
        }
        return false;
    }

    private function process_css($css, $handle, $is_critical) {
        // Basic minification
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);

        // If it's a critical handle or contains critical selectors, include all CSS
        if ($is_critical || $this->has_critical_styles($css)) {
            return sprintf("/* From %s */\n%s\n", esc_html($handle), $css);
        }

        // Otherwise, only include critical selectors
        $critical_css = '';
        foreach ($this->critical_selectors as $selector) {
            if (preg_match_all('/' . preg_quote($selector, '/') . '[^}]+\{[^}]+\}/i', $css, $matches)) {
                $critical_css .= implode("\n", $matches[0]);
            }
        }

        return !empty($critical_css) ? sprintf("/* From %s */\n%s\n", esc_html($handle), $critical_css) : '';
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

            // Don't defer critical styles
            if ($this->is_critical_handle($handle) || $this->is_theme_dependency($handle)) {
                continue;
            }

            // Skip obvious admin-related styles
            if ($this->is_admin_style($handle)) {
                continue;
            }

            // Check if the stylesheet contains critical styles
            $style = $wp_styles->registered[$handle];
            if ($style->src) {
                $css_file = $style->src;
                if (strpos($css_file, '//') === 0) {
                    $css_file = 'https:' . $css_file;
                } elseif (strpos($css_file, '/') === 0) {
                    $css_file = site_url($css_file);
                }

                $response = wp_remote_get($css_file);
                if (!is_wp_error($response)) {
                    $css = wp_remote_retrieve_body($response);
                    if ($this->has_critical_styles($css)) {
                        continue;
                    }
                }
            }

            // Defer non-critical styles
            wp_style_add_data($handle, 'media', 'print');
            wp_style_add_data($handle, 'onload', 'this.media=\'all\'');
        }
    }
}

// Initialize Critical CSS
CMP_Critical_CSS::init();
