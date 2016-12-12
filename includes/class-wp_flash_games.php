<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/ishaqj
 * @since      1.0.0
 *
 * @package    Wp_flash_games
 * @subpackage Wp_flash_games/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_flash_games
 * @subpackage Wp_flash_games/includes
 * @author     Ishaq Jound <ishaqjound@gmail.com>
 */
class Wp_flash_games {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_flash_games_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wp_flash_games';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_flash_games_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_flash_games_i18n. Defines internationalization functionality.
	 * - Wp_flash_games_Admin. Defines all hooks for the admin area.
	 * - Wp_flash_games_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp_flash_games-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp_flash_games-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp_flash_games-admin.php';


		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp_flash_games-public.php';

        // Include Custom fields and Custom post
        require_once(plugin_dir_path(dirname(__FILE__)). 'lib/advanced-custom-fields/acf.php');

        require_once(plugin_dir_path(dirname(__FILE__)). 'cpt/wpfg_games.php');

		$this->loader = new Wp_flash_games_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_flash_games_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_flash_games_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_flash_games_Admin( $this->get_plugin_name(), $this->get_version() );

        // add actions
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action('acf/save_post',$plugin_admin,'wpfg_rename_post_title');
        $this->loader->add_action('admin_init', $plugin_admin, 'wpfg_register_options');
        $this->loader->add_action('admin_menu', $plugin_admin, 'wpfg_register_menus');
        if(!defined('ACF_LITE')) define('ACF_LITE',true); //turn off acf plugin menu

        // add filters
        $this->loader->add_filter('upload_mimes',$this,'pixert_upload_swf');
        $this->loader->add_filter('manage_wpfg_games_posts_custom_column',$plugin_admin,'wpfg_custom_column_names',1,2);
        $this->loader->add_filter('manage_edit-wpfg_games_columns',$plugin_admin,'wpfg_column_headers');
        $this->loader->add_filter('acf/settings/path',$plugin_admin,'wpfg_acf_settings_path');
        $this->loader->add_filter('acf/settings/dir',$plugin_admin, 'wpfg_acf_settings_dir');
        $this->loader->add_filter('acf/settings/show_admin',$plugin_admin,'wpfg_acf_show_admin');
        
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_flash_games_Public( $this->get_plugin_name(), $this->get_version() );

        // add shortcode
        add_shortcode( 'wpfg_games', array( $plugin_public, 'wpfg_shortcode') );

        // add actions
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $plugin_public, 'wpfg_shortcode' );
        $this->loader->add_action( 'wp', $plugin_public, 'include_template_function');

        // add filters
        $this->loader->add_filter('single_template',$plugin_public,'include_template_function');
        $this->loader->add_filter('archive_template',$plugin_public,'include_template_function');
        $this->loader->add_filter('taxonomy_template',$plugin_public,'include_template_function');
        
    }

    /**
     * Allows swf files to be uploaded.
     *
     * @since    1.0.0
     * @param $existing_mimes array list of existing mimes
     * @access   public
     *
     * @return $existing_mimes Returns new list of mimes
     */
    public function pixert_upload_swf($existing_mimes){
        $existing_mimes['swf'] = 'text/swf'; //allow swf files
        return $existing_mimes;
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_flash_games_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
