<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/ishaqj
 * @since      1.0.0
 *
 * @package    Wp_flash_games
 * @subpackage Wp_flash_games/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_flash_games
 * @subpackage Wp_flash_games/public
 * @author     Ishaq Jound <ishaqjound@gmail.com>
 */
class Wp_flash_games_Public
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
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_flash_games_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_flash_games_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp_flash_games-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_flash_games_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_flash_games_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp_flash_games-public.js', array('jquery'), $this->version, false);

    }

    /**
     * function for handling shortcode.
     *
     * @since    1.0.0
     * @param      array $args Arguments for the shortcode.
     *
     * @return $output returns a html string for displaying games
     */
    public function wpfg_shortcode($args)
    {
        // setup our variables
        $output = '';
        $genre = '';

        if ( get_query_var( 'paged' ) ): $paged = get_query_var( 'paged' );
        elseif ( get_query_var( 'page' ) ): $paged = get_query_var( 'page' );
        else: $paged = 1;
        endif;

        //get options from db
        $numberOfGames = get_option('wpfg_number_of_games') ? get_option('wpfg_number_of_games') : 6;
        $settingsGenre = get_option('wpfg_game_type');

        // get genre argument
        if (isset($args['genre'])) $genre = (string)$args['genre'];

        // If "all genres" is selected, get all games from db
        if (!strlen($settingsGenre)) {
            $args = array(
                'post_type' => array('wpfg_games'),
                'posts_per_page' => $numberOfGames,
                'paged' => $paged,
            );

            $games = new WP_Query($args);

            // Loop - list games
            $output = $this->listGames($games, $paged);
        }
        // else if genre is set in the settings, get games by genre
        elseif (strlen($settingsGenre)) {
            $args = array(
                'post_type' => array('wpfg_games'),
                'posts_per_page' => $numberOfGames,
                'paged' => $paged,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'wpfg_genre',
                        'field' => 'slug',
                        'terms' => $settingsGenre,
                    ),
                ),
            );

            $games = new WP_Query($args);

            //Loop - list games
            $output = $this->listGames($games, $paged);
        }

        // If shortcode has genre argument, get games by genre
        if (strlen($genre)):
            $args = array(
                'post_type' => array('wpfg_games'),
                'posts_per_page' => $numberOfGames,
                'paged' => $paged,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'wpfg_genre',
                        'field' => 'slug',
                        'terms' => $genre,
                    ),
                ),
            );

            $games = new WP_Query($args);

            //Loop - list games
            $output = $this->listGames($games, $paged);

        endif;

        // return our results/html
        return $output;

    }

    /**
     * Function for listing the games.
     *
     * @since    1.0.0
     * @param      array $games Games that are passed into the function.
     * @param      int $paged Current page number
     *
     * @return $output returns a html string for displaying games
     */
    public function listGames($games, $paged)
    {
        $output = "";

        if ($games->have_posts()) {
            $gameArr = [];

            while ($games->have_posts()) {
                $games->the_post();
                $gameArr[] = (object)array(
                    'title' => get_the_title(),
                    'img' => get_field('wpfg_image', get_the_ID())['url'],
                    'description' => get_field('wpfg_description', get_the_ID()),
                    'release_date' => get_field('wpfg_release_date', get_the_ID()),
                    'link' => get_permalink(),
                    'genre' => get_field('wpfg_genre', get_the_ID())
                );
            }

            // Arguments for pagination
            $args_pagi = array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '?paged=%#%',
                'total' => $games->max_num_pages,
                'current' => $paged
            );

            // Pagination
            $paginate_links = paginate_links($args_pagi);

            // Display Pagination
            if ($paginate_links) {
                $output .= '<div class="pagination">';
                $output .= $paginate_links;
                $output .= '</div>';
            }

            // List games
            $output .= '<div id="gamesListContainer">';
            foreach ($gameArr as $game) {
                $output .= '<div class="game-item">';
                $output .= '<header class="entry-header"><a href="' . $game->link . '" title="' . $game->title . '"><span class="title">' . $game->title . '</span></a></header>';
                $output .= '<div class="img"><img src="' . $game->img . '"></div>';
                $output .= $this->list_game_description($game->description);
                $rlseDate = new DateTime($game->release_date);
                $output .= '<div class="release_date">Added: ' . $rlseDate->format('Y-m-d') . '</div>';
                $output .= $this->list_game_genres($game->genre);
                $output .= '</div>';

            }
            $output .= '</div>';

            // Pagination
            $paginate_links = paginate_links($args_pagi);

            // Display Pagination
            if ($paginate_links) {
                $output .= '<div class="pagination">';
                $output .= $paginate_links;
                $output .= '</div>';
            }

            /* Restore original Post Data */
            wp_reset_postdata();

        } else {
            $output .= "no games found";
        }

        return $output;
    }

    /**
     * returns a html string for game description
     *
     * @since    1.0.0
     * @param      string $description Description of the game.
     *
     * @return $output returns a html string for game description
     */
    public function list_game_description($description)
    {

        $output = '';
        if (strlen($description) > 100):
            $output .= '<div class="desc">' . substr($description, 0, 125) . "..." . '</div>';
        else:
            $output .= '<div class="desc">' . $description . '</div>';
        endif;

        return $output;
    }

    public function list_game_genres($genres)
    {
        $output = '';
        if (!empty($genres)) {
            foreach ($genres as $genre_id) {
                $output .= '<div class="genre"><a href="' . get_term_link($genre_id) . '">' . get_term($genre_id)->name . '</a></div>';
            }
        }

        return $output;
    }

    /**
     * Function for loading custom template for our custom post type.
     *
     * @since    1.0.0
     * @param      string $template_path Path for template.
     *
     * @return $template_path return the path for custom template.
     */
    public function include_template_function($template_path)
    {
        if (is_singular('wpfg_games')) {

            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin

            // check for single page .
            if (file_exists(get_stylesheet_directory() . '/single-wpfg_games.php')) {
                $template_path = get_stylesheet_directory() . '/single-wpfg_games.php';
            } else {
                $template_path = plugin_dir_path(__FILE__) . 'partials/single-wpfg_games.php';
            }
        }
        // check for archive page.
        elseif (is_post_type_archive('wpfg_games')) {
            if (file_exists(get_stylesheet_directory() . '/archive-wpfg_games.php')) {
                $template_path = get_stylesheet_directory() . '/archive-wpfg_games.php';

            } else {
                $template_path = plugin_dir_path(__FILE__) . 'partials/archive-wpfg_games.php';
            }
        }
        // check for wpfg_genre taxnomy.
        elseif (is_tax('wpfg_genre')) {
            if (file_exists(get_stylesheet_directory() . '/taxonomy-wpfg_genre.php')) {
                $template_path = get_stylesheet_directory() . '/taxonomy-wpfg_genre.php';

            } else {
                $template_path = plugin_dir_path(__FILE__) . 'partials/taxonomy-wpfg_genre.php';
            }
        }

        return $template_path;
    }
}
