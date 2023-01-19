<?php
/**
 * The template for displaying search results pages.
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<div class="section latest-post-sec" id="archive_section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('Search Results', 'industrydive'); ?></h2>
        <?php 

            if ( have_posts() ) {
                echo '<div class="row" id="latest_posts">';
                while ( have_posts() ) {
                    the_post();                
                    get_template_part( 'template-parts/content', get_post_format() );
                }
                echo '</div>';
            } else {
                get_template_part( 'template-parts/content-none' );                
            }
    
            global $wp_query;
            if (  $wp_query->max_num_pages > 1 ){
                printf('<div class="text-center"><a class="btn btn-primary btn-lg loadmore-post">%s</a></div>', esc_html__('Load More', 'industrydive'));
            }
        ?>
    </div>
</div>

<?php get_footer();
