<?php
/**
 * Created by PhpStorm.
 * User: Ishaq17
 * Date: 2016-11-29
 * Time: 17:58
 */

// Creates Custom fields for wp_games post type
if(function_exists("register_field_group"))
{
    register_field_group(array (
        'id' => 'acf_games-detail',
        'title' => 'Games Detail',
        'fields' => array (
            array (
                'key' => 'field_5834714617a17',
                'label' => 'Name',
                'name' => 'wpfg_name',
                'type' => 'text',
                'required' => 1,
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'formatting' => 'html',
                'maxlength' => '',
            ),
            array (
                'key' => 'field_5834719917a18',
                'label' => 'Description',
                'name' => 'wpfg_description',
                'type' => 'wysiwyg',
                'required' => 1,
                'default_value' => '',
                'toolbar' => 'full',
                'media_upload' => 'no',
            ),
            array (
                'key' => 'field_5834cc0541f6e',
                'label' => 'File',
                'name' => 'wpfg_file',
                'type' => 'file',
                'required' => 1,
                'save_format' => 'object',
                'library' => 'all',
            ),
            array (
                'key' => 'field_5835ec55ee301',
                'label' => 'Width',
                'name' => 'wpfg_width',
                'type' => 'number',
                'default_value' =>750,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
            ),
            array (
                'key' => 'field_5835ec64ee302',
                'label' => 'Height',
                'name' => 'wpfg_height',
                'type' => 'number',
                'default_value' => 500,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
            ),
            array (
                'key' => 'field_583471aa17a19',
                'label' => 'Image',
                'name' => 'wpfg_image',
                'type' => 'image',
                'required' => 1,
                'save_format' => 'object',
                'preview_size' => 'thumbnail',
                'library' => 'all',
            ),
            array (
                'key' => 'field_583471c917a1a',
                'label' => 'Release date',
                'name' => 'wpfg_release_date',
                'type' => 'date_picker',
                'required' => 1,
                'date_format' => 'yymmdd',
                'display_format' => 'dd/mm/yy',
                'first_day' => 1,
            ),
            array (
                'key' => 'field_583471f117a1b',
                'label' => 'Genre',
                'name' => 'wpfg_genre',
                'type' => 'taxonomy',
                'required' => 1,
                'taxonomy' => 'wpfg_genre',
                'field_type' => 'checkbox',
                'allow_null' => 0,
                'load_save_terms' => 1,
                'return_format' => 'id',
                'multiple' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'wpfg_games',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array (
            ),
        ),
        'menu_order' => 0,
    ));
}

// Creates custom post type wpfg_games
add_action( 'init', 'cptui_register_my_cpts_wpfg_games' );
function cptui_register_my_cpts_wpfg_games() {
    $labels = array(
        "name" => __( 'Games'),
        "singular_name" => __( 'Game'),
        "add_new_item" => __( 'Add New Game'),
        "edit_item" => __( 'Edit Game'),
        "new_item" => __( 'New Game'),
        "view_item" => __( 'View Game'),
        "search_items" => __( 'Search Game'),
        "not_found" => __( 'Game not found'),
        "not_found_in_trash" => __( 'No Game Found In Trash'),
        "featured_image" => __( 'Featured Image in this game'),
        "archives" => __( 'Latest Games'),
        "items_list" => __( 'Games List'),
    );

    $args = array(
        "label" => __( 'Games'),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => false,
        "rest_base" => "",
        "has_archive" => true,
        "show_in_menu" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => array( "slug" => "wpfg-games", "with_front" => true ),
        "query_var" => true,
        "supports" => false,
        "taxonomies" => array( "wpfg_genre" ),
    );
    register_post_type( "wpfg_games", $args );

// End of cptui_register_my_cpts_wpfg_games()
}

// Refresh the permalink structure for our custom post type.
function wpfg_flush_rewrite_rules() {
    cptui_register_my_cpts_wpfg_games();

    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpfg_flush_rewrite_rules' );

// Creates custom taxonomy wpfg_genre
add_action( 'init', 'cptui_register_my_taxes_wpfg_genre' );
function cptui_register_my_taxes_wpfg_genre() {
    $labels = array(
        "name" => __( 'Games Genre'),
        "singular_name" => __( 'Game Genre'),
        "not_found" => __( 'No genres found'),
        "add_new_item" => __( 'Add New Genre'),
        "edit_item" => __( 'Edit Genre'),
        "new_item" => __( 'New Genre'),
        "view_item" => __( 'View Genre'),
        "search_items" => __( 'Search Genre'),
        "not_found_in_trash" => __( 'No Genre Found In Trash'),
        "featured_image" => __( 'Featured Image in this genre'),
        "archives" => __( 'Genres', 'colormag' ),
        "items_list" => __( 'Genres List'),
        "popular_items" => __('Popular Genres')
    );

    $args = array(
        "label" => __( 'Games Genre'),
        "labels" => $labels,
        "public" => true,
        "hierarchical" => false,
        "show_ui" => true,
        "show_in_menu" => false,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array( 'slug' => 'wpfg-genre', 'with_front' => true, ),
        "show_admin_column" => false,
        "show_in_rest" => false,
        "rest_base" => "",
        "show_in_quick_edit" => false,
    );
    register_taxonomy( "wpfg_genre", array( "wpfg_games" ), $args );

// End cptui_register_my_taxes_wpfg_genre()
}

