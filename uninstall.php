<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/ishaqj
 * @since      1.0.0
 *
 * @package    Wp_flash_games
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


// removes plugin related custom post type post data
function wpfg_remove_post_data()
{

	// get WP's wpdb class
	global $wpdb;


	try {

		// get our custom table name
		$table_name = $wpdb->prefix . "posts";

		// set up custom post types array
		$custom_post_types = 'wpfg_games';

		// remove data from the posts db table where post types are equal to our custom post types
		$data_removed = $wpdb->query(
			$wpdb->prepare(
				"
					DELETE FROM $table_name 
					WHERE post_type = %s
				",
				$custom_post_types
			)
		);

		// get the table names for postmet and posts with the correct prefix
		$table_name_1 = $wpdb->prefix . "postmeta";
		$table_name_2 = $wpdb->prefix . "posts";

		// delete orphaned meta data
		$wpdb->query(
			$wpdb->prepare(
				"
				DELETE pm
				FROM $table_name_1 pm
				LEFT JOIN $table_name_2 wp ON pm.post_id = wp.ID
				WHERE wp.ID IS NULL
				", NULL
			)
		);


	} catch (Exception $e) {

		// php error

	}


}

// removes any custom options from the database
function wpfg_remove_options()
{

	try {

		// plugin to remove options
		unregister_setting('wpfg_plugin_options', 'wpfg_number_of_games');
		unregister_setting('wpfg_plugin_options', 'wpfg_game_type');

		// return true if everything worked
		$options_removed = true;

	} catch (Exception $e) {

		// php error

	}

}

// Remove terms, term_taxonomy and term_relationships
function wpfg_remove_terms_termTaxonomy_termRelationships() {
    global $wpdb;

    try {
        // get the table names for posts,terms,term_taxonomy and term_relationship
        $table_posts = $wpdb->prefix . "posts";
        $table_terms = $wpdb->prefix . "terms";
        $table_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $table_term_relationships = $wpdb->prefix . "term_relationships";
        

        // REMOVE wp_terms and wp_term_taxonomy content
        $wpdb->query(
            $wpdb->prepare(
                "
                DELETE t, tt
                FROM $table_terms t
                INNER JOIN $table_term_taxonomy tt ON t.term_id = tt.term_id
                WHERE tt.taxonomy = %s
                ", 'wpfg_genre'
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "
                DELETE tr
                FROM $table_term_relationships tr
                LEFT JOIN $table_posts tp ON tr.object_id = tp.ID
                WHERE tp.ID IS NULL
                ", NULL
            )
        );

    } catch (Exception $e) {

    }
}

// remove attachments such covers and swf files.
function wpfg_remove_attachments() {
     $args = array(
        'post_type' => 'wpfg_games'
    );

    $posts = new WP_Query($args);

    if($posts->have_posts()) {

    	while($posts->have_posts()) {
    		$posts->the_post();
    		$post_id = get_the_ID();
    		$post_parent_args = ['post_parent' => $post_id];
    		$attachments = get_children($post_parent_args);

    		if($attachments) {
    			foreach ($attachments as $attachment) {
    				 wp_delete_attachment($attachment->ID, true);
    			}
    		}
    	}
    	
    }

 	/* Restore original Post Data */
    wp_reset_postdata();

}

// uninstall the plugin
function wpfg_uninstall_plugin()
{
	wpfg_remove_attachments();
	wpfg_remove_post_data();
	wpfg_remove_options();
    wpfg_remove_terms_termTaxonomy_termRelationships();
}

echo wpfg_uninstall_plugin();