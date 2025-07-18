<?php

/**
 * Bloxby theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Bloxby_WP
 */
function register_bloxbywp_menus()
{
   register_nav_menus(
      array(
         'header-menu' => __('Header Menu'),
         'extra-menu' => __('Extra Menu')
      )
   );
}
add_action('init', 'register_bloxbywp_menus');

function add_bloxbywp_scripts()
{
   wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array(), get_theme_mod('version'), true);
}
add_action('wp_enqueue_scripts', 'add_bloxbywp_scripts');

function add_bloxbywp_styles()
{
   wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), get_theme_mod('version'));
   wp_enqueue_style('style', get_stylesheet_uri(), array('bootstrap'), get_theme_mod('version'));
}
add_action('enqueue_block_assets', 'add_bloxbywp_styles');

/**
 * Bootstrap Overrides from 
 * Options 
 */
add_action( 'wp_head', function () {
   $site_data = get_option('sitedata', '');
   if( !empty($site_data) ){
      $data_array = unserialize(base64_decode($site_data));
      // echo "<pre>";
      // print_r($data_array);
      // echo "</pre>";

      $site_theme = $data_array['theme'];
      $site_font = $data_array['fonts'];
      switch ($site_theme) {
         case 'soft':
            $font_primary = 'https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap';
            $font_primary_family = '"Noto Sans", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap';
            $font_secondary_family = ' "Noto Serif", serif';
            $border_radius = '25px';
            break;
         
         default:
            # code...
            $font_primary = 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap';
            $font_primary_family = ' "Poppins", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap';
            $font_secondary_family = ' "Noto Serif", serif';
            $border_radius = '5px';
            break;
      }
      $site_colors = $data_array['colors'];

?>
   <style>
      @import url('<?php echo $font_primary; ?>');
      @import url('<?php echo $font_secondary; ?>');
      :root{
         --color-primary: rgb(<?php echo implode(",", $site_colors[0][0]) ?>);
         --color-primary-rgb: <?php echo implode(",", $site_colors[0][0]) ?>;
         --color-secondary: rgb(<?php echo implode(",", $site_colors[0][1]) ?>);
         --color-secondary-rgb: <?php echo implode(",", $site_colors[0][1]) ?>;

         --bs-primary: var( --color-primary );
         --bs-primary-rgb: var( --color-primary-rgb );
         --bs-primary-bg-rgb: var( --color-primary-rgb );
         --bs-secondary: var( --color-secondary );
         --bs-secondary-bg-rgb: var(--color-secondary-rgb);

         
         --bs-light: #F8F9FA;
         --bs-dark: #343A40;

         /* Btn Link Colors */
         --bs-link-color-rgb: <?php echo implode(",", $site_colors[0][0]) ?>;
         --bs-link-hover-color-rgb: var(--bs-dark);

         /* Border Radius */
         --bs-border-radius: <?php echo $border_radius ?>;
         --bs-border-radius-sm: <?php echo $border_radius ?>;
         --bs-border-radius-lg: <?php echo $border_radius ?>;
         --bs-border-radius-xl: <?php echo $border_radius ?>;

      }
      body {
         font-family: <?php echo $font_primary_family; ?>;
      }
      h1,h2,h3,h4,h5,h6,.btn {
         font-family: <?php echo $font_secondary_family; ?>;
      }
      h1 a,h2 a,h3 a,h4 a,h5 a,h6 a{
         color: inherit;
      }
      .bg-body-secondary {
         --bs-bg-opacity: 0.4;
      }

      .btn-primary {
         --bs-btn-color: var(  --bs-light );
         --bs-btn-bg: var( --color-primary );
         --bs-btn-border-color: var( --color-primary );
         --bs-btn-hover-color: var( --color-primary );
         --bs-btn-hover-bg: transparent;
         --bs-btn-hover-border-color: var( --color-primary );
         --bs-btn-focus-shadow-rgb: 49,132,253;
         --bs-btn-active-color: var( --color-primary );
         --bs-btn-active-bg: rgba( var( --color-primary-rgb ) , .2);
         --bs-btn-active-border-color: rgba( var( --color-primary-rgb ) , .5);
         --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
         --bs-btn-disabled-color: var( --color-primary );
         --bs-btn-disabled-bg: rgba( var( --color-primary-rgb ) , .2);
         --bs-btn-disabled-border-color: rgba( var( --color-primary-rgb ) , .2);
      }
   </style>
<?php
   }
} );

add_action('enqueue_block_editor_assets', function () {
   wp_enqueue_script(
      'block-inserter',
      get_theme_file_uri('/assets/js/block-inserter.js'),
      ['wp-blocks', 'wp-element', 'wp-editor', 'wp-data'],
      false,
      true
   );
});

include_once 'includes/cli-builder.php';