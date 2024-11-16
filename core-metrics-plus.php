<?php
/*
Plugin Name: Core Metrics Plus
Description: Optimize your WordPress site's Core Web Vitals through smart resource loading and performance enhancements.
Version: 1.0.0
Author: Carmelyne
Plugin URI: https://github.com/carmelyne/core-metrics-plus
*/

if (file_exists(dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php')) {
    require_once dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
    $myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://raw.githubusercontent.com/carmelyne/core-metrics-plus/main/plugin.json',
        __FILE__,
        'core-metrics-plus'
    );
}

function add_fetch_priority() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Get all images and videos
            let images = document.querySelectorAll('img');
            let videos = document.querySelectorAll('video');

            // Set high priority for first 3 images
            for (let i = 0; i < images.length && i < 3; i++) {
                images[i].setAttribute('fetchpriority', 'high');
            }

            // Set high priority for first video
            if (videos.length > 0) {
                videos[0].setAttribute('fetchpriority', 'high');
            }
        });
    </script>
    <?php
}

// Hook into WordPress footer
add_action('wp_footer', 'add_fetch_priority');
