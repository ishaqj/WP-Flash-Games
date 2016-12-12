<?php
/**
 * The template used for displaying page content in taxonomy-content.php
 *
 */

$args_pagi = array(
    'base' => add_query_arg( 'page', '%#%' ),
    'format'   => '?page=%#%',
    'total' => $games->max_num_pages,
    'current' => $paged
);

$paginate_links = paginate_links($args_pagi);
if ( $paginate_links ) {
    $output .= '<div class="pagination">';
    $output .= $paginate_links;
    $output .= '</div>';
}

$output .= '<div id="gamesListContainer">';

foreach ($gameArr as $game) {
    $output .= '<div class="game-item">';
    $output .= '<header class="entry-header"><a href="'.$game->link.'" title="'.$game->title.'"><span class="title">'.$game->title.'</span></a></header>';
    $output .= '<div class="img"><img src="'. $game->img .'"></div>';

    if(strlen($game->description) > 100):
        $output .= '<div class="desc">'.substr($game->description,0,125) . "..." .'</div>';
    else:
        $output .= '<div class="desc">'.$game->description .'</div>';
    endif;

    $rlseDate = new DateTime($game->release_date);
    $output .= '<div class="release_date">Added: '. $rlseDate->format('Y-m-d') .'</div>';
    foreach($game->genre as $genre_id)  {

        $output .= '<div class="genre"><a href="'.get_term_link( $genre_id ).'">'.get_term($genre_id)->name.'</a></div>';

    }
    $output .= '</div>';
}
$output .= '</div>';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    
    if ( $paginate_links ) {
        $output .= '<div class="pagination">';
        $output .= $paginate_links;
        $output .= '</div>';
    }
    /* Restore original Post Data */
    wp_reset_postdata();
    ?>

   <div class="article-content clearfix">
      <div class="entry-content clearfix">
          <?php echo $output; ?>
      </div>
   </div>

   <?php do_action( 'colormag_after_post_content' ); ?>
</article>