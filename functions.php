<?php

/**
 * This theme relies apon the Timber WordPress plugin.
 *
 * https://timber.github.io/docs/
 */

if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    });

    add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/views/layouts/activate-plugins.html';
	});

    return;
}

Timber::$dirname = [
    'views',
    'views/layouts',
    'views/partials',
    'views/macros'
];

include_once 'core/theme.php';
