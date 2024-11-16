<?php
/**
 * Plugin Name: Core Metrics Plus
 * Plugin URI: https://github.com/carmelyne/core-metrics-plus
 * Description: Optimize Core Web Vitals through smart resource loading and performance enhancements
 * Version: 1.1.7
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
define('CMP_VERSION', '1.1.7');

// Plugin path
define('CMP_PATH', plugin_dir_path(__FILE__));

// Plugin URL
define('CMP_URL', plugin_dir_url(__FILE__));

// Required files
require_once CMP_PATH . 'includes/critical-css.php';
require_once CMP_PATH . 'includes/script-optimizer.php';

/**
 * Add fetch priority to images and videos
 */
function cmp_add_fetch_priority($tag, $handle, $src) {
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
 * Add fetch priority to content
 */
function cmp_add_fetch_priority_to_content($content) {
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
    
    return $content;
}

/**
 * Initialize plugin features
 */
function cmp_init() {
    // Load text domain
    load_plugin_textdomain('core-metrics-plus', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Initialize Critical CSS
    CMP_Critical_CSS::init();

    // Initialize Script Optimizer
    CMP_Script_Optimizer::init();

    // Add fetch priority to media
    add_filter('wp_get_attachment_image', 'cmp_add_fetch_priority', 10, 3);
    add_filter('the_content', 'cmp_add_fetch_priority_to_content');
}

// Initialize plugin
add_action('plugins_loaded', 'cmp_init');
