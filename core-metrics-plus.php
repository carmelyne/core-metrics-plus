<?php
/**
 * Plugin Name: Core Metrics Plus
 * Plugin URI: https://github.com/carmelyne/core-metrics-plus
 * Description: Optimize Core Web Vitals through smart resource loading and performance enhancements
 * Version: 1.1.8
 * Author: Carmelyne
 * Author URI: https://github.com/carmelyne
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: core-metrics-plus
 * Domain Path: /languages
 *
 * @package CoreMetricsPlus
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin version
define('CMP_VERSION', '1.1.8');

// Plugin path
define('CMP_PATH', plugin_dir_path(__FILE__));

// Plugin URL
define('CMP_URL', plugin_dir_url(__FILE__));

/**
 * Add fetch priority to images and videos
 */
function cmp_add_fetch_priority() {
    $script = "
        document.addEventListener('DOMContentLoaded', function() {
            console.time('fetchPriorityOptimization');
            const elements = document.querySelectorAll('img, video');
            let optimized = 0;
            
            elements.forEach(function(el) {
                if (el.getBoundingClientRect().top < window.innerHeight) {
                    el.fetchPriority = 'high';
                    optimized++;
                }
            });
            
            console.info('Core Metrics Plus: Optimized ' + optimized + ' resources with fetch priority');
            console.timeEnd('fetchPriorityOptimization');
        });
    ";
    
    wp_add_inline_script('jquery', $script);
}

/**
 * Add defer attribute to non-essential scripts
 */
function cmp_defer_scripts($tag, $handle, $src) {
    $no_defer = array('jquery', 'jquery-core', 'jquery-migrate');
    
    if (in_array($handle, $no_defer)) {
        return $tag;
    }
    
    return str_replace(' src', ' defer src', $tag);
}

/**
 * Optimize script loading order
 */
function cmp_optimize_scripts() {
    if (!is_admin()) {
        wp_scripts()->add_data('jquery', 'group', 1);
        wp_scripts()->add_data('jquery-core', 'group', 1);
        wp_scripts()->add_data('jquery-migrate', 'group', 1);
    }
}

/**
 * Initialize plugin features
 */
function cmp_init() {
    // Script optimization
    add_action('wp_enqueue_scripts', 'cmp_add_fetch_priority');
    add_filter('script_loader_tag', 'cmp_defer_scripts', 10, 3);
    add_action('wp_enqueue_scripts', 'cmp_optimize_scripts', 99);
    
    // Load update checker
    if (file_exists(CMP_PATH . 'plugin-update-checker/plugin-update-checker.php')) {
        require_once CMP_PATH . 'plugin-update-checker/plugin-update-checker.php';
        $myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/carmelyne/core-metrics-plus',
            __FILE__,
            'core-metrics-plus'
        );
        $myUpdateChecker->getVcsApi()->enableReleaseAssets();
    }
    
    // Load critical CSS feature
    if (file_exists(CMP_PATH . 'includes/critical-css.php')) {
        require_once CMP_PATH . 'includes/critical-css.php';
    }
}

// Initialize plugin
add_action('plugins_loaded', 'cmp_init');
