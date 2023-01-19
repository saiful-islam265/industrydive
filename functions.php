<?php 

 // Load Customizer File
require_once get_template_directory() . '/inc/customizer.php';

/**
 * initial theme setup
 */
 if(!function_exists('industrydive_setup')){
    function industrydive_setup(){
        load_theme_textdomain( 'industrydive', get_template_directory() . '/languages' );
        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        /*post formats support*/
        add_theme_support(
            'post-formats',
            array(
                'link',
                'aside',
                'gallery',
                'image',
                'quote',
            )
        );

        /* Enable support for Post Thumbnails on posts and pages. */
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 1530, 9999 );
        add_image_size( 'post-secondary', 745, 360, true );
        add_image_size( 'post-tertiary', 745, 760, true );
        add_image_size( 'post-general', 485, 390, true );

        register_nav_menus(
            array(
                'primary' => esc_html__( 'Header Menu', 'industrydive' ),
                'footer'  => esc_html__( 'Footer Menu', 'industrydive' ),
            )
        );

        /* Switch default core markup for search form, comment form, and comments to output valid HTML5. */
        add_theme_support(
            'html5',
            array(
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-widgets',
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        // Add support for responsive embedded content.
        add_theme_support( 'responsive-embeds' );
    }

    add_action( 'after_setup_theme', 'industrydive_setup' );
 }

/**
 * Enqueue scripts and styles.
 * @return void
 */
function industry_dive_scripts() {
	wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/assets/css/slick.css' );
	wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/assets/css/slick-theme.css' );
	wp_enqueue_style( 'id-style', get_template_directory_uri() . '/style.css', array(), filemtime(get_theme_file_path('/style.css')) );
    
    wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/js/slick.min.js', array('jquery'), false, true );
    wp_enqueue_script( 'id', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), filemtime(get_theme_file_path('/assets/js/theme.js')), true );
    wp_localize_script( 'id', 'id', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
	) );

	// Threaded comment reply styles.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'industry_dive_scripts' );

/**
 * Enqueue Colorpicker
 * @param mixed $taxonomy
 * @return void
 */
function enqueue_colorpicker( $taxonomy ) {
    if( null !== ( $screen = get_current_screen() ) && 'edit-category' !== $screen->id ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'id-admin', get_template_directory_uri() . '/assets/js/admin.js', array('wp-color-picker'), filemtime(get_theme_file_path('/assets/js/admin.js')), true );
}
add_action( 'admin_enqueue_scripts', 'enqueue_colorpicker' );

/**
 * Subribe email to database from popup
 * @return void
 */
function subscribe_mail_newsletter(){
    if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce($_POST['nonce'], 'subscribe_email')) {
        wp_send_json_error(__('Sorry, something went wrong', 'industrydive'));
    } else {
        if(is_email($_POST['email'])){
            wp_send_json_success(__('Thank you for subscribing!', 'industrydive'));
        } else {
            wp_send_json_error(__('Sorry, please enter a valid email address.', 'industrydive'));
        }
    }
}
add_action('wp_ajax_nopriv_subscribe_email', 'subscribe_mail_newsletter');
add_action('wp_ajax_subscribe_email', 'subscribe_mail_newsletter');

/**
 * Save color category data
 * @param mixed $term_id
 * @return void
 */
function save_color_termmeta( $term_id ) {
    if( empty( $_POST['_category_color'] ) ) {
        update_term_meta( $term_id, '_category_color', sanitize_hex_color_no_hash( $_POST['_category_color'] ) );
    } else {
        delete_term_meta( $term_id, '_category_color' );
    }

}
add_action( 'created_category', 'save_color_termmeta' ); 
add_action( 'edited_category',  'save_color_termmeta' );

/**
 * add color to category
 * @param mixed $term
 * @return void
 */
function edit_colorpicker_category_field_colorpicker( $term ) {
    $color = isset($term->term_id) ? get_term_meta( $term->term_id, '_category_color', true ) : 'f7c546';
    $color = ( ! empty( $color ) ) ? "#{$color}" : '#f7c546';
  ?>
    <tr class="form-field term-colorpicker-wrap">
        <th scope="row"><label for="term-colorpicker">Color</label></th>
        <td>
            <input name="_category_color" value="<?php echo $color; ?>" class="colorpicker" id="term-colorpicker" data-default-color="<?php echo $color; ?>"/>
            <p class="description"><?php _e('Select a color for the category to style posts', 'industry-drive'); ?></p>
        </td>
    </tr>
  <?php
}
add_action( 'category_edit_form_fields', 'edit_colorpicker_category_field_colorpicker' );
add_action( 'category_add_form_fields', 'edit_colorpicker_category_field_colorpicker' );

/**
 * Load 3 more posts onclick
 * @return never
 */
function loadmore_posts(){
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'paged' => $_POST['page']
    );
 
	$query = new WP_Query( $args );
 
    ob_start();
	if( $query->have_posts() ) :
		while( $query->have_posts() ): $query->the_post();
			get_template_part( 'template-parts/content', get_post_format() ); 
		endwhile; 
	endif;
    $post_data = ob_get_clean();
    wp_send_json(array('posts' => $post_data, 'max_page' => $query->max_num_pages));
    wp_reset_postdata();
	die;
}
add_action('wp_ajax_loadmore_posts', 'loadmore_posts'); 
add_action('wp_ajax_nopriv_loadmore_posts', 'loadmore_posts');
