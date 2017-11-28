<?php
/**
 * This file we will set all of the Advanced custom fields options for the theme.
 */
add_filter('acf/settings/save_json', 'bcm_save_acf');
add_filter('acf/settings/load_json', 'bcm_load_acf');

function bcm_save_acf($path)
{
    $path = get_stylesheet_directory() . '/acf-json';

    return $path;
}

function bcm_load_acf($paths)
{
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';

    return $paths;
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'Options',
        'menu_title' => 'Options',
        'menu_slug' => 'loganlaw-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false,
    ]);

    acf_add_options_sub_page([
        'page_title' => 'Header Settings',
        'menu_title' => 'Header',
        'parent_slug' => 'loganlaw-general-settings',
    ]);

    acf_add_options_sub_page([
        'page_title' => 'Footer Settings',
        'menu_title' => 'Footer',
        'parent_slug' => 'loganlaw-general-settings',
    ]);
}
