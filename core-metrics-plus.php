<?php
/**
 * Plugin Name: Core Metrics Plus
 * Plugin URI: https://github.com/carmelyne/core-metrics-plus
 * Description: Optimize Core Web Vitals through smart resource loading and performance enhancements
 * Version: 1.1.5
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
    exit; // Exit if accessed directly
}

// Plugin version
define('CMP_VERSION', '1.1.5');

// Plugin path
define('CMP_PATH', plugin_dir_path(__FILE__));

// Plugin URL
define('CMP_URL', plugin_dir_url(__FILE__));

// Require the update checker
require_once plugin_dir_path(__DIR__) . 'plugin-update-checker/plugin-update-checker.php';

// Initialize update checker
$myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/carmelyne/core-metrics-plus',
    __FILE__,
    'core-metrics-plus'
);

// Configure to use GitHub releases
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

// Include core features
require_once CMP_PATH . 'includes/critical-css.php';

/**
 * Add fetch priority to images and videos
 */
function add_fetch_priority() {
    try {
        console.time('fetchPriorityOptimization');
        
        $script = "
            document.addEventListener('DOMContentLoaded', function() {
                const elements = document.querySelectorAll('img, video');
                let optimized = 0;
                
                elements.forEach(function(el) {
                    if (el.getBoundingClientRect().top < window.innerHeight) {
                        el.fetchPriority = 'high';
                        optimized++;
                    }
                });
                
                console.info('Core Metrics Plus: Optimized ' + optimized + ' resources with fetch priority');
            });
        ";
        
        wp_add_inline_script('jquery', $script);
        console.timeEnd('fetchPriorityOptimization');
        
    } catch (Exception $e) {
        error_log('Core Metrics Plus - Fetch Priority Error: ' . $e->getMessage());
    }
}
add_action('wp_enqueue_scripts', 'add_fetch_priority');

/**
 * Add defer attribute to non-essential scripts
 */
function cmp_defer_scripts($tag, $handle, $src) {
    // List of scripts that should not be deferred
    $no_defer = array(
        'jquery',              // Core jQuery
        'jquery-core',         // jQuery core
        'jquery-migrate'       // jQuery migrate
    );

    // Don't defer these critical scripts
    if (in_array($handle, $no_defer)) {
        return $tag;
    }

    // Add defer attribute to script tag
    return str_replace(' src', ' defer src', $tag);
}
add_filter('script_loader_tag', 'cmp_defer_scripts', 10, 3);

/**
 * Optimize script loading order
 */
function cmp_optimize_scripts() {
    if (!is_admin()) {
        // Move jQuery to footer if it's not needed in header
        wp_scripts()->add_data('jquery', 'group', 1);
        wp_scripts()->add_data('jquery-core', 'group', 1);
        wp_scripts()->add_data('jquery-migrate', 'group', 1);
    }
}
add_action('wp_enqueue_scripts', 'cmp_optimize_scripts', 99);
