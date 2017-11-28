<?php
/**
 * Add any WordPress actions you wish to que
 * up here like adding scripts and styles.
 */
add_action('wp', 'bcm_remove_actions');
add_action('wp', 'bcm_deregister_scripts');
add_action('wp', 'bcm_register_scripts');
add_action('wp', 'bcm_register_styles');

add_action('after_setup_theme', 'bcm_thumbnail_setup');

function bcm_remove_actions()
{
    // Remove WordPress emojis.
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');
    // Turn off oEmbed auto discovery.
    // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');
}

function bcm_deregister_scripts()
{
    // Removes jQuery from the wordpress theme as we will be including this inside of our app.js
    wp_deregister_script('jquery');
}

function bcm_register_scripts()
{
    // Register and enque all JavaScript files here.
    wp_enqueue_script('loganlaw', get_template_directory_uri() . '/assets/js/app.js', null, '1.0.0', true);
}

function bcm_register_styles()
{
    // Register and enque all css files here.
    wp_enqueue_style('loganlaw', get_template_directory_uri() . '/assets/css/app.css');
}

function bcm_thumbnail_setup()
{
    add_image_size('featured-image', 1920, 600, ['center', 'center']);
    add_image_size('promo-image', 600, 600, ['center', 'center']);
}
