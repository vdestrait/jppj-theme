<?php

add_action('after_setup_theme', function() {
    // Customize Appearance Options
    add_theme_support('custom-logo', [
        'height' => 400,
        'width' => 400,
        'flex-height' => true,
        'flex-width'  => true,
        'title' => 'site-description'
    ]);

    add_theme_support('custom-header');


    add_theme_support( 'hybrid-core-template-hierarchy');
    add_theme_support( 'loop-pagination' );
    add_theme_support( 'get-the-image' );   
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'cleaner-caption' );
    add_theme_support( 'cleaner-gallery' );

    // Menus
    register_nav_menus([
        "primary_menu" => "Primary"
    ]);
    
});

function get_my_menu() {
    // Replace your menu name, slug or ID carefully
    return wp_get_nav_menu_items('Main Menu');
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', 'menu', array(
        'methods' => 'GET',
        'callback' => 'get_my_menu',
        'show_in_rest' => 'true'
    ) );
} );

function jppj_customize_register ($wp_customize) {
    $wp_customize->remove_section("colors");
    $wp_customize->add_setting('jppj_primary_color', array(
        'default' => '#F25D50',
        'transport' => 'refresh'
    ));
    $wp_customize->add_section('jppj_theme_colors', array(
        'title' => __('Couleurs', 'jppj-theme'),
        'priority' => '30'
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control( $wp_customize, 'jppj_primary_color_control', array(
        'label' => __('Couleur Primaire', 'jppj-chorale'),
        'section' => 'jppj_theme_colors',
        'settings' => 'jppj_primary_color'
    )));
}

add_action('customize_register', 'jppj_customize_register');
// Ouput Customize CSS
function jppj_customize_css(){ 
    ?>
    <style type="text/css">
        .home #agenda {
            background-color: <?php echo get_theme_mod('jppj_primary_color'); ?>
        }
    </style>
<?php 
}
add_action('wp_head', 'jppj_customize_css');


add_action('wp_enqueue_scripts', function(){
    // fct wp pui permet de register un style
    wp_register_style('jppjStyle', get_stylesheet_directory_uri().'/style.css');
    wp_enqueue_style('jppjStyle');

    wp_enqueue_script( 'jppjScript', get_template_directory_uri() . '/assets/js/main.js');
    wp_enqueue_script('jppjScript');

    
});

add_filter('wp_nav_menu_items', function($items, $args){
    // if($args->theme_location === 'socials_links_menu'){
    //     preg_match('(?:[a-z][a-z]+)(<\/a>)', $items, $matches, PREG_OFFSET_CAPTURE);
    // }
    return $items;
}, 10, 2);

//Widgets
function jppj_widgets_init() {

    register_sidebar( array(
        'name'          => 'Footer sidebar',
        'id'            => 'footer_sidebar',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ) );

}
add_action( 'widgets_init', 'jppj_widgets_init' );


// Post types

/* Concert */
add_action( 'init', 'register_cpt_concert' );
function register_cpt_concert() {

    $labels = array(
    'name' => __( 'Concert', 'concert' ),
    'singular_name' => __( 'Concert', 'concert' ),
    'add_new' => __( 'Ajouter', 'concert' ),
    'add_new_item' => __( 'Ajouter un Concert', 'concert' ),
    'edit_item' => __( 'Modifier', 'concert' ),
    'new_item' => __( 'Nouveau Concert', 'concert' ),
    'view_item' => __( 'Voir Concert', 'concert' ),
    'search_items' => __( 'Chercher', 'concert' ),
    'not_found' => __( 'Pas de Concert trouv&eacute', 'concert' ),
    'not_found_in_trash' => __( 'Pas de Concert dans la corbeille', 'concert' ),
    'parent_item_colon' => __( 'Concert Parent:', 'concert' ),
    'menu_name' => __( 'Concerts', 'concert' ),
    );

    $args = array(
    'labels' => $labels,
    'hierarchical' => false,
    'supports' => array('title', 'excerpt', 'author', 'thumbnail', 'revisions' ),
    'public' => true,
    'show_ui' => true,
    'menu_position' => 4,
    'show_in_nav_menus' => true,
    'publicly_queryable' => true,
    'exclude_from_search' => true,
    'has_archive' => true,
    'query_var' => true,
    'can_export' => true,
    'rewrite' => true,
    'capability_type' => 'post'
    );

    register_post_type( 'concert', $args );

}

/**
 * Add REST API support to an already registered post type.
 */
add_filter( 'register_post_type_args', 'my_post_type_args', 10, 2 );

function my_post_type_args( $args, $post_type ) {

    if ( 'concert' === $post_type ) {
        $args['show_in_rest'] = true;

        // Optionally customize the rest_base or rest_controller_class
        $args['rest_base']             = 'concerts';
        $args['rest_controller_class'] = 'WP_REST_Posts_Controller';
    }

    return $args;
}





