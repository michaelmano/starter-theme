<?php

Timber::$dirname = [
    'views',
    'views/layouts',
    'views/partials',
    'views/macros'
];

class StarterTheme extends TimberSite
{
    public function __construct()
    {
		// Remove Actions.
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('rest_api_init', 'wp_oembed_register_route');
		remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		remove_action('wp_head', 'wp_oembed_add_host_js');

		// Theme supports.
        add_theme_support('post-formats');
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('html5', [
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption'
		]);

		// Filters.
        add_filter('timber_context', [$this, 'add_to_context']);
		add_filter('get_twig', [$this, 'add_to_twig']);
		add_filter('upload_mimes', [$this, 'add_to_mime_types']);

		// Add Actions.
        add_action('init', [$this, 'register_post_types']);
		add_action('init', [$this, 'register_taxonomies']);
		add_action('init', [$this, 'register_menus']);
		add_action('wp_enqueue_scripts', [$this, 'register_assets']);
		add_action('after_setup_theme', [$this, 'register_image_sizes']);
		add_action('admin_menu', [$this, 'remove_from_admin_sidebar']);

        parent::__construct();
	}
	
	/**
	 * Action to remove sidebar links from the admin menu.
	 * 
	 * for most common available options refer to the link below.
	 * https://codex.wordpress.org/Function_Reference/remove_menu_page#Return_Values
	 * 
	 */
	public function remove_from_admin_sidebar()
	{
	}

	public function register_assets()
	{
		wp_enqueue_style('theme-style', get_template_directory_uri() . '/assets/css/app.css');
		wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/app.js', null, '1.0.0', true);
	}

	public function register_image_sizes()
	{
		add_image_size('feature', 1920, 600, ['center', 'center']);
		add_image_size('card', 350, 200, ['center', 'center']);
	}


	/**
     * Create custom post types for your theme, example below.
	 * 
	 * $post_types = [
	 * 		'team_member' => (object) [
	 * 			'title_singular' => 'Team Member',
	 * 			'title_plural' => 'Team Members',
	 * 			'icon' => 'dashicons-groups',
	 * 			'supports' => ['title', 'editor', 'thumbnail'],
	 * 		],
	 * ]
     */
    public function register_post_types()
    {
		$post_types = [];

		if (!empty($post_types)) {
			foreach ($post_types as $key => $value) {
				register_post_type($key, $this->generate_custom_post_type($value));
			}
		}
    }

	/**
	 * Sets WordPress menu locations to be used within the theme.
	 */
	public function register_menus()
	{
		register_nav_menu('header-navigation', __('Header Navigation'));
		register_nav_menu('footer-navigation', __('Footer Navigation'));
	}

	function add_to_mime_types($mimes)
	{
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * This theme uses the $context variable which you can refference globally,
	 * Below there is an example of navigation items being set which can be
	 * called inside of template files like so: {{ navigation.header }}
	 */
	public function add_to_context($context)
    {
		$context['navigation'] = [
            'header' => new TimberMenu('header-navigation'),
            'footer' => new TimberMenu('footer-navigation'),
        ];
        return $context;
    }

    /**
     * Add functions and filters to twig.
     *
     * If you require any functions that you need to call from a .twig file, You can use the
     * {{ function('class_builder', item) }}, However if you feel that this is not practical
     * you can add your function to the twig context so it can be called directly.
	 * 
     * {{ class_builder('Navigation__list-item-link', item) }}
     */
    public function add_to_twig($twig)
    {
        $twig->addExtension(new Twig_Extension_StringLoader());
        $twig->addFilter('class_builder', new Twig_SimpleFilter('class_builder', [$this, 'class_builder']));
        return $twig;
    }
    
    /**
     * @param string $class the class you want to build a bem class from.
     * @param array $item is the menu item inside of the navigational loop.
     * @param boolean $wpclasses show WordPress menu classes with it or not.
     *
     * @return string
     */
    private function class_builder($class, $item, $wpclasses = false)
    {
        $classes = [$class];
        // Add class to menu items which contain sub menus.
        if ($item->children) {
            array_push($classes, $class.'--has-submenu');
        }
        // Add class to current page or ancestors.
        switch (true) {
            case $item->current:
                array_push($classes, $class.'--current');
                break;
            case $item->current_item_ancestor:
                array_push($classes, $class.'--current-item-ancestor');
                break;
            case $item->current_item_parent:
                array_push($classes, $class.'--current-item-parent');
                break;
            break;
        }
        if ($wpclasses) {
            array_merge($classes, $item->classes);
        }
        return implode($classes, ' ');
    }

    /**
     * Generates custom post types on the fly without having to type them out multiple times,
     * register_post_type('name', $args);
     *
     * ['title_singular']   used to set the singular name for the post type.
     * ['title_plural']     as above.
     * ['supports']         refer to https://codex.wordpress.org/Function_Reference/register_post_type#supports
     * ['icon']             icon in sidebar, refer to https://developer.wordpress.org/resource/dashicons
     *
     * @param array $args see above.
     *
     * @return array to be used to register the post type.
     */
    private function generate_custom_post_type($args)
    {
        $labels = [
            'name' => _x($args->title_plural, 'post type general name'),
            'singular_name' => _x($args->title_singular, 'post type singular name'),
            'add_new' => _x('Add New', $args->title_singular),
            'add_new_item' => __('Add New ' . $args->title_singular),
            'edit_item' => __('Edit ' . $args->title_singular),
            'new_item' => __('New ' . $args->title_singular),
            'all_items' => __('All ' . $args->title_plural),
            'view_item' => __('View ' . $args->title_singular),
            'search_items' => __('Search ' . $args->title_plural),
            'not_found' => __('No ' . $args->title_singular . ' found'),
            'not_found_in_trash' => __('No ' . $args->title_singular . ' found in the Trash'),
            'menu_name' => $args->title_plural,
        ];
        $args = [
            'labels' => $labels,
            'description' => 'Holds ' . $args->title_singular . ' specific data',
            'menu_position' => 30,
            'show_in_rest' => true,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => $args->supports,
            'menu_icon' => $args->icon,
            'public' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'query_var' => false,
        ];
        return $args;
    }
}

new StarterTheme();
