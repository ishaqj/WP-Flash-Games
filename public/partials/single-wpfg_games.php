<?php
/*Template Name: New Template
*/
get_header(); ?>

<?php
global $post;

$title = $post->post_title;
$img = get_field('wpfg_image', $post->ID)['url'];
$description = get_field('wpfg_description', $post->ID);
$release_date = get_field('wpfg_release_date', $post->ID);
$game_width = get_field('wpfg_width', $post->ID);
$game_height = get_field('wpfg_height', $post->ID);
$file = get_field('wpfg_file', $post->ID)['url'];
$release_date = new DateTime($release_date);
$release_date = $release_date->format('Y-m-d');
$genres = wp_get_post_terms($post->ID, 'wpfg_genre', ['fields' => 'all']);

?>
    <div id="game">
        <div id="content" role="main">
            <article id="post-<?= $post->ID ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <!-- Display featured image in right-aligned floating div -->
                    <div style="float: right; margin: 10px">
                        <img src="<?= $img ?>" width="100px" height="100px">
                    </div>

                    <!-- Display Title,Genre and Release Date -->
                    <strong>Title: </strong> <?= $title ?><br/>
                    <strong>Genre: </strong> <?= genre() ?><br/>
                    <strong>Release Date: </strong> <?= $release_date ?>
                    <p><?= $description ?></p>
                </header>
            </article>
        </div> <!-- #content -->
        <div class="entry-content">
            <!-- Display game -->
            <div class="playgame">
                <object width="<?= $game_width ?>" height="<?= $game_height ?>" data="<?= $file ?>"></object>
            </div>
        </div> <!-- .entry-content -->
    </div> <!-- #game -->

<?php

function genre() {
    global $genres;

    $genreLink = '';
    $genreArr = [];

    if(count($genres) > 1) {
        foreach ($genres as $genre) {
            $genreArr[] = '<a href="'.get_term_link($genre->term_id).'"><span class="genre">'.$genre->name.'</a></span>';
        }

        $genreLink .= implode(',',$genreArr);
    }

    else {
        foreach ($genres as $genre) {
            $genreLink .=  '<a href="'.get_term_link($genre->term_id).'"><span class="genre">'.$genre->name. '</a></span>';
        }
    }

    return $genreLink;
}

?>

<?php get_footer(); ?>