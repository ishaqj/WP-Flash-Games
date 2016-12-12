<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/ishaqj
 * @since      1.0.0
 *
 * @package    Wp_flash_games
 * @subpackage Wp_flash_games/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_flash_games
 * @subpackage Wp_flash_games/admin
 * @author     Ishaq Jound <ishaqjound@gmail.com>
 */
class Wp_flash_games_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp_flash_games-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp_flash_games-admin.js', array('jquery'), $this->version, true);

    }

    /**
     * Function for listing the data for custom column headers in "Games list" page.
     *
     * @since    1.0.0
     * @param      string $column Column name.
     * @param      int $post_id Id of the post.
     *
     * @return $output returns a html string for custom column names.
     */
    public function wpfg_custom_column_names($column, $post_id)
    {
        $output = '';
        $genres = [];
        switch ($column) {

            case 'genre':
                $wpfg_genre = get_field('wpfg_genre', $post_id);
                foreach ($wpfg_genre as $genreId) {
                    $term = get_term($genreId);
                    $genres[] = $term->name;
                }
                $output .= implode(',', $genres);
                break;

            case 'release_date':
                $date = get_field('wpfg_release_date', $post_id);
                $rlseDate = new DateTime($date);
                $output .= $rlseDate->format('Y-m-d');
                break;

            case 'description':
                $description = get_field('wpfg_description', $post_id);
                $str_lenght = strlen($description);
                $output .= $str_lenght > 125 ? substr($description, 0, 125) . "..." : $description;
                break;

            case 'cover':
                $cover = get_field('wpfg_image', $post_id);
                $output .= "<img src=" . $cover['url'] . " width='100' height='100'>";
                break;

        }

        echo $output;
    }

    /**
     * Function for listing custom column headers in "Games list" page.
     *
     * @since    1.0.0
     * @param      string $column Column name.
     *
     * @return $columns returns a html string for custom headers.
     */
    public function wpfg_column_headers($columns)
    {
        // creating custom column headers.
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Game title'),
            'genre' => __('Genre'),
            'cover' => __('Cover'),
            'description' => __('Description'),
            'release_date' => __('Release Date')
        );

        // returning new columns
        return $columns;
    }

    /**
     * Function for renaming the title of our custom post type.
     *
     * @since    1.0.0
     * @param      int $post_id Id of the post.
     *
     */
    public function wpfg_rename_post_title($post_id)
    {
        $type = get_post_type($post_id);
        if ($type == 'wpfg_games') {
            $post_title = get_field('wpfg_name', $post_id);
            $post = array(
                'ID' => $post_id,
                'post_title' => $post_title,
                'post_name' => sanitize_title($post_title)
            );
        }

        wp_update_post($post);

    }

    /**
     * Function for displaying plugin settings.
     *
     * @since    1.0.0
     *
     */
    public function wpfg_options_admin_page()
    {
        // get the default values for our options
        $options = $this->wpfg_get_current_options();


        echo('<div class="wrap">
            <h1>Game Display Settings</h1>
            <div class="postbox">
            <div class="inside">
            <form action="options.php" method="post">');

        // outputs a unique nounce for our plugin options
        settings_fields('wpfg_plugin_options');
        // generates a unique hidden field with our form handling url
        @do_settings_fields('wpfg_plugin_options');

        echo('<table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="wpfg_game_type">Game type to display</label></th>
                    <td>
                        ' . $this->wpfg_get_genre_select('wpfg_game_type', 'wpfg_game_type', $options['wpfg_game_type']) . '
                        <p class="description" id="wpfg_game_type">Select a genre and use the following shortcode in your page <strong>[wpfg_games]</strong>.  <br />
                            Alternately: You can also pass in "genre" argument for example:  <strong>[wpfg_games genre="action"][/wpfg_games]</strong>.</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="wpfg_number_of_games">Number of Games</label></th>
                    <td>
                        <input type="number" name="wpfg_number_of_games" value="' . $options['wpfg_number_of_games'] . '" class="" />
                        <p class="description" id="wpfg_number_of_games-description">Number of games to display.</p>
                    </td>
					</tr>
            
            </tbody>
          </table>
        ');

        // outputs the WP submit button html
        @submit_button();

        echo('</form></div></div></div>');

    }

    /**
     * Function for fetching current options from db.
     *
     * @since    1.0.0
     *
     * @return $current_options  Gets the current options and returns values in associative array
     */
    public function wpfg_get_current_options()
    {

        // setup our return variable
        $current_options = array();

        try {

            // build our current options associative array
            $current_options = array(
                'wpfg_game_type' => $this->wpfg_get_option('wpfg_game_type'),
                'wpfg_number_of_games' => $this->wpfg_get_option('wpfg_number_of_games')
            );

        } catch (Exception $e) {

            // php error

        }

        // return current options
        return $current_options;

    }

    /**
     * Function for fetching options value or it's default.
     *
     * @since    1.0.0
     * @param $option_name string Option name
     *
     * @return $option_value  Returns the requested item option value or it's default.
     */
    public function wpfg_get_option($option_name)
    {
        // setup return variable
        $option_value = '';

        try {

            // get default option values
            $defaults = $this->wpfg_get_default_options();

            // get the requested option
            switch ($option_name) {

                case 'wpfg_game_type':
                    $option_value = (get_option('wpfg_game_type')) ? get_option('wpfg_game_type') : $defaults['wpfg_game_type'];
                    break;
                case 'wpfg_number_of_games':
                    $option_value = (get_option('wpfg_number_of_games')) ? get_option('wpfg_number_of_games') : $defaults['wpfg_number_of_games'];
                    break;

            }

        } catch (Exception $e) {

            // php error

        }

        // return option value or it's default
        return $option_value;

    }

    /**
     * Function for fetching default options.
     *
     * @since    1.0.0
     *
     * @return $defaults  Gets the default options and returns values in associative array.
     */
    public function wpfg_get_default_options()
    {
        $defaults = array();

        try {

            // setup defaults array
            $defaults = array(
                'wpfg_number_of_games' => 6,
                'wpfg_game_type' => 'all'
            );


        } catch (Exception $e) {

            // php error

        }

        // return defaults
        return $defaults;
        
    }

    /**
     * Function for returning html string for a genre selector
     *
     * @since    1.0.0
     * @param $input_name string select name for select tag
     * @param $input_id string Input id for input tag
     * @param $selected_value string Selected value for option tag
     *
     * @return $select  returns html for a genre selector
     */
    public function wpfg_get_genre_select($input_name = "", $input_id = "", $selected_value = "")
    {

        // get genres
        $genres = get_terms(array(
            'taxonomy' => 'wpfg_genre',
            'hide_empty' => false,
        ));

        // setup our select html
        $select = '<select name="' . $input_name . '" ';

        // IF $input_id was passed in
        if (strlen($input_id)):

            // add an input id to our select html
            $select .= 'id="' . $input_id . '" ';

        endif;

        // setup our first select option
        $select .= '><option value="">- Select One -</option>';

        if ($selected_value != 'all') {
            $select .= '><option value="">All Genres</option>';
        }


        if ($selected_value == 'all') {
            $select .= '><option value="" selected>All Genres</option>';
        }


        // loop over all the pages
        foreach ($genres as &$genre):

            // get the genre id as our default option value
            $value = $genre->slug;

            // check if this option is the currently selected option
            $selected = '';
            if ($selected_value == $value):
                $selected = ' selected="selected" ';
            endif;

            // build our option html
            $option = '<option value="' . $value . '" ' . $selected . '>';
            $option .= $genre->name;
            $option .= '</option>';

            // append our option to the select html
            $select .= $option;

        endforeach;

        // close our select html tag
        $select .= '</select>';

        // return our new select
        return $select;

    }

    /**
     * Function for registering all our plugin options
     *
     * @since    1.0.0
     *
     */
    public function wpfg_register_options()
    {
        // plugin options
        register_setting('wpfg_plugin_options', 'wpfg_number_of_games');
        register_setting('wpfg_plugin_options', 'wpfg_game_type');

    }

    /**
     * Function for admin dashboard page
     *
     * @since    1.0.0
     *
     */
    public function wpfg_dashboard_admin_page()
    {
        $output = '
		<div class="wrap">
		<h2>Dashboard</h2>
		<div class="postbox">
		<div class="inside">
			
        <p>Welcome to WP Flash Games plugin. Feel free to add any kind of flash games to your website.</p>
        <h1>Stats</h1>';

        // Display total games and genres.
        $total_games = 0;
        $total_genres = wp_count_terms('wpfg_genre');

        $args = ['post_type' => 'wpfg_games'];
        $games = new WP_Query($args);
        if ($games->have_posts()) {
            while ($games->have_posts()) {
                $games->the_post();
                $total_games = $games->post_count;
            }
        }

        $output .= '<p>Total Games: <a class="admin_links" href="edit.php?post_type=wpfg_games">' . $total_games . '</a></p>';
        $output .= '<p>Total Genres: <a class="admin_links"  href="edit-tags.php?taxonomy=wpfg_genre&post_type=wpfg_games">' . $total_genres . '</a></p>';
        $output .= '</div></div></div>';
        echo $output;

    }

    /**
     * Function for registering plugin menus.
     *
     * @since    1.0.0
     *
     */
    public function wpfg_register_menus()
    {
        $top_menu_item = 'wpfg_dashboard_admin_page';

        add_menu_page('', 'WP Flash Games', 'manage_options', 'wpfg_dashboard_admin_page', array($this, 'wpfg_dashboard_admin_page'), 'dashicons-smiley');

        /* submenu items */

        // dashboard
        add_submenu_page($top_menu_item, '', 'Dashboard', 'manage_options', $top_menu_item, array($this, $top_menu_item));

        //settings page menu
        add_submenu_page($top_menu_item, '', 'Settings', 'manage_options', 'wpfg_games_settings', array($this, 'wpfg_options_admin_page'));

        // add games
        add_submenu_page($top_menu_item, '', 'Add Game', 'manage_options', 'post-new.php?post_type=wpfg_games');

        // games list
        add_submenu_page($top_menu_item, '', 'Games list', 'manage_options', 'edit.php?post_type=wpfg_games');

        // genres
        add_submenu_page($top_menu_item, '', 'Games Genre', 'manage_options', 'edit-tags.php?taxonomy=wpfg_genre&post_type=wpfg_games');

    }

}
