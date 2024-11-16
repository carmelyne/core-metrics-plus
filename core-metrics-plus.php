<?php
/*
Plugin Name: Core Metrics Plus
Description: Optimize your WordPress site's Core Web Vitals through smart resource loading and performance enhancements.
Version: 1.1.3
Author: Carmelyne
Plugin URI: https://github.com/carmelyne/core-metrics-plus
*/

if (file_exists(dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php')) {
    require_once dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
    $myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/carmelyne/core-metrics-plus',
        __FILE__,
        'core-metrics-plus'
    );
    
    // Configure to use GitHub releases
    $myUpdateChecker->getVcsApi()->enableReleaseAssets();
    
    // Enable WordPress to download from GitHub releases
    // Add your GitHub token here if your repository is private
    // $myUpdateChecker->setAuthentication('your-github-token');
}

function add_fetch_priority() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            try {
                console.time('CMP: Fetch Priority Setup');
                // Get all images and videos
                let images = document.querySelectorAll('img');
                let videos = document.querySelectorAll('video');

                // Set high priority for first 3 images
                for (let i = 0; i < images.length && i < 3; i++) {
                    if (images[i]) {
                        images[i].setAttribute('fetchpriority', 'high');
                    }
                }

                // Set high priority for first video
                if (videos.length > 0 && videos[0]) {
                    videos[0].setAttribute('fetchpriority', 'high');
                }
                
                console.timeEnd('CMP: Fetch Priority Setup');
                console.info('CMP: Optimized', Math.min(3, images.length), 'images and', Math.min(1, videos.length), 'videos');
            } catch (error) {
                console.warn('Core Metrics Plus: Error setting fetch priorities:', error);
            }
        });
    </script>
    <?php
}

// Hook into WordPress footer
add_action('wp_footer', 'add_fetch_priority');

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
