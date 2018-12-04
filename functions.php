<?php
// Newsportal Magazine Functions goes here

define('NEWSPORTAL_MAGAZINE_VERSION', '1.0.0');


//Tab Widget


require get_stylesheet_directory() . '/inc/widget-newsportal-magazine-tab.php'; //Tab Widgets


function newsportal_magazine_scripts()
{

    wp_register_script('newsportal-magazine-easytabs', get_stylesheet_directory_uri() . '/assets/js/jquery.easytabs.js', array(), esc_attr(NEWSPORTAL_MAGAZINE_VERSION), true);
    wp_enqueue_script('newsportal-magazine', get_stylesheet_directory_uri() . '/assets/js/main.js', array('newsportal-magazine-easytabs'), esc_attr(NEWSPORTAL_MAGAZINE_VERSION), true);

}

add_action('wp_enqueue_scripts', 'newsportal_magazine_scripts');

if (!function_exists('newsportal_magazine_setup')) :

    function newsportal_magazine_setup()
    {


        add_image_size('newsportal-magazine-tab-thumbnail', 136, 102, true);
        /*
           * Enable support for Post Thumbnails on posts and pages.
           *
           * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
           */
        add_theme_support('post-thumbnails');

    }

endif;
add_action('after_setup_theme', 'newsportal_magazine_setup');

if (!function_exists('newsportal_magazine_style')):
    function newsportal_magazine_style()
    {

        $nm_theme_color = esc_attr(get_theme_mod('eggnews_theme_color', ''));

        $nm_dynamic_css = '';

        if (!empty($nm_theme_color)) {


            $nm_dynamic_css .= ".tab-widget ul.widget-tabs li a{background-color: " . eggnews_sass_lighten($nm_theme_color, '20%') . ";}\n";
            $nm_dynamic_css .= ".tab-widget ul.widget-tabs li.active a {background-color: " . $nm_theme_color . ";}\n";
            $nm_dynamic_css .= ".tab-widget .below-entry-meta a:hover, .tab-widget .below-entry-meta span:hover {color: " . $nm_theme_color . ";}\n";

        }

        ?>
        <style type="text/css">
            <?php

                if( !empty( $nm_dynamic_css ) ) {
                    echo $nm_dynamic_css;
                }
            ?>
        </style>
        <?php
    }
endif;
add_action('wp_head', 'newsportal_magazine_style');