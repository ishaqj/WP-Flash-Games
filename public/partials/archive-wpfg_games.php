<?php
/**
 * The template for displaying Archive page.
 *
 */
?>

<?php

$paged = ( get_query_var( 'page' ) ) ? absint( get_query_var( 'page' ) ) : 1;
$numberOfGames = get_option('wpfg_number_of_games') ? get_option('wpfg_number_of_games') : 6;
$args = array(
    'post_type' => array('wpfg_games'),
    'posts_per_page' => $numberOfGames,
    'paged' => $paged,
);

$games = new WP_Query($args);
$output = "";
?>
<?php get_header(); ?>
    <div id="primary">
    <div id="content" class="clearfix">
        
    <?php if($games->have_posts()): ?>
        
    <header class="page-header">
        <h1 class="page-title">
            <span>Games</span>
        </h1>
    </header><!-- .page-header -->
    <div class="article-container">
        <?php $gameArr = []; ?>

        <?php while ( $games->have_posts() ) : $games->the_post(); ?>
            <?php
            $gameArr[] =  (object) array(
                'title' => get_the_title(),
                'img' => get_field('wpfg_image',get_the_ID())['url'],
                'description' => get_field('wpfg_description', get_the_ID()),
                'release_date' =>get_field('wpfg_release_date', get_the_ID()),
                'link' => get_permalink(),
                'genre' => get_field('wpfg_genre', get_the_ID()));
            ?>
        <?php endwhile; ?>

        <?php include_once 'taxonomy-content.php'; ?>
    </div>
        <?php else: 
            echo "no games found"; 
        ?>
        <?php endif; ?>
    </div><!-- #content -->
    </div><!-- #primary -->

<?php get_footer(); ?>