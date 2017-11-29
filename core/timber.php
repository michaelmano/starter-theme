<?php
/**
 * Timber is a plugin which allows the use of twig inside of wordpress templates.
 * This is the main function file which we will include into the main
 * WordPress functions.php.
 */
if (!class_exists('Timber')) {
	add_action('admin_notices', function () {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
	});

	add_filter('template_include', function ($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});

	return;
}

Timber::$dirname = ['templates', 'views'];

class WordPressSite extends TimberSite
{
	public function __construct()
	{
		add_theme_support('post-formats');
		add_theme_support('post-thumbnails');
		add_theme_support('menus');
		add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption']);
		add_filter('timber_context', [$this, 'add_to_context']);
		add_filter('get_twig', [$this, 'add_to_twig']);
		add_action('init', [$this, 'register_post_types']);
		add_action('init', [$this, 'register_taxonomies']);
		parent::__construct();
	}

	public function register_post_types()
	{
		//this is where you can register custom post types
	}

	public function register_taxonomies()
	{
		//this is where you can register custom taxonomies
	}

	public function add_to_context($context)
	{
		/*
	     * This is where you add static variables to call with Timber::get_context();
	     * e.g. $context['phone_number'] = 1300444444
	     */
		// $context['foo'] = 'bar';
		// $context['stuff'] = 'I am a value set in your functions.php file';
		// $context['notes'] = 'These values are available everytime you call Timber::get_context();';
		// $context['menu'] = new TimberMenu();
		// $context['site'] = $this;

		return $context;
	}

	public function add_to_twig($twig)
	{
		/* this is where you can add your own functions to twig */
		$twig->addExtension(new Twig_Extension_StringLoader());
		$twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', [$this, 'myfoo']));

		return $twig;
	}
}

new WordPressSite();
