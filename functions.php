<?php
    /**
     * Bloxby theme functions and definitions.
     *
     * @link https://developer.wordpress.org/themes/basics/theme-functions/
     *
     * @package Bloxby_WP
     */
    function register_bloxbywp_menus() {
    register_nav_menus(
      array(
        'header-menu' => __( 'Header Menu' ),
        'extra-menu' => __( 'Extra Menu' )
       )
     );
   }
   add_action( 'init', 'register_bloxbywp_menus' );

   function add_bloxbywp_scripts() {
      wp_enqueue_script( 'bootstrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array(), get_theme_mod('version'), true );
   }
   add_action( 'wp_enqueue_scripts', 'add_bloxbywp_scripts' );

   function add_bloxbywp_styles() {
     wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), get_theme_mod('version') );
     wp_enqueue_style( 'style', get_stylesheet_uri(), array('bootstrap'), get_theme_mod('version') );
   }
   add_action( 'enqueue_block_assets', 'add_bloxbywp_styles' );

   add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'block-inserter',
        get_theme_file_uri('/assets/js/block-inserter.js'),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-data'],
        false,
        true
    );
});