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
         'footer-menu' => __('Footer Menu'),
         'extra-menu' => __('Extra Menu')
      )
   );
}
add_action('init', 'register_bloxbywp_menus');

function register_bloxbywp_supports()
{
   add_theme_support('custom-logo', array(
    'height'      => 512,
    'width'       => 512,
    'flex-height' => true,
    'flex-width'  => true,
    'header-text' => array('site-title', 'site-description'),
));

}
add_action('after_setup_theme', 'register_bloxbywp_supports');


function add_bloxbywp_scripts()
{
   wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array(), get_theme_mod('version'), true);
   wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js', array('jquery'), get_theme_mod('version'), true);
   wp_enqueue_script('slick-slider', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), get_theme_mod('version'), true);
   wp_enqueue_script('gsap-main', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js', array('jquery'), get_theme_mod('version'), true);
   wp_enqueue_script('gsap-ScrollTrigger', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js', array('jquery'), get_theme_mod('version'), true);
   wp_enqueue_script('gsap-SplitText', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/SplitText.min.js', array('jquery'), get_theme_mod('version'), true);

   wp_enqueue_script(
      'site-main',
      get_theme_file_uri('/assets/js/site.js'),
      array('jquery'),
      false,
      true
   );

   wp_enqueue_script(
      'animations',
      get_theme_file_uri('/assets/js/animations.js'),
      array('jquery', 'gsap-main'),
      false,
      true
   );
}
add_action('wp_enqueue_scripts', 'add_bloxbywp_scripts');


function bloxbywp_block_styles()
{
   wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), get_theme_mod('version'));
   wp_enqueue_style('font-awesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.0.0/css/all.min.css', array(), get_theme_mod('version'));
   wp_enqueue_style('style', get_stylesheet_uri(), array('bootstrap'), get_theme_mod('version'));
}
add_action('enqueue_block_assets', 'bloxbywp_block_styles');

function bloxbywp_frontend_styles()
{
   wp_enqueue_style('slick-slider', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), get_theme_mod('version'));
   wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css', array(), get_theme_mod('version'));
   wp_enqueue_style('animations', get_stylesheet_directory_uri() . '/assets/css/animations.css', array(), get_theme_mod('version'));
}
add_action('wp_enqueue_scripts', 'bloxbywp_frontend_styles');


/**
 * Bootstrap Overrides from 
 * Options 
 */
add_action( 'wp_head', function () {
   $site_data = get_option('sitedata', '');
   if( !empty($site_data) ){
      $data_array = unserialize(base64_decode($site_data));
      
      $site_theme = $data_array['theme'];
      $site_font = $data_array['fonts'];
      switch ($site_theme) {
         case 'classic':
            $font_primary = 'https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap';
            $font_primary_family = '"Figtree", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap';
            $font_secondary_family = '"Cormorant Garamond", serif';
            $border_radius = '0px';
            $border_radius_sm = '0px';
            $border_radius_lg = '0px';

            $box_shadow = '0 0 10px rgba(0, 0, 0, 0.2)';
            $box_shadow_sm = '0 0 10px rgba(0, 0, 0, 0.2)';
            $box_shadow_lg = '0 0 10px rgba(0, 0, 0, 0.2)';
            break;
         case 'material':
            $font_primary = 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap';
            $font_primary_family = '"Roboto", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap';
            $font_secondary_family = '"Raleway", sans-serif';
            $border_radius = '10px';
            $border_radius_sm = '5px';
            $border_radius_lg = '10px';
            break;
         case 'minimalistic':
            $font_primary = 'https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap';
            $font_primary_family = '"Work Sans", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap';
            $font_secondary_family = '"Plus Jakarta Sans", sans-serif';
            $border_radius = '0px';
            $border_radius_sm = '0px';
            $border_radius_lg = '0px';
            break;
         case 'flat':
            $font_primary = 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap';
            $font_primary_family = '"Montserrat", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap';
            $font_secondary_family = '"Rubik", sans-serif';
            $border_radius = '5px';
            $border_radius_sm = '5px';
            $border_radius_lg = '5px';
            break;
         case 'soft':
            $font_primary = 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap';
            $font_primary_family = '"DM Sans", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap';
            $font_secondary_family = '"Quicksand", sans-serif';
            $border_radius = '25px';
            $border_radius_sm = '15px';
            $border_radius_lg = '35px';
            break;
         
         default:
            # code...
            $font_primary = 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap';
            $font_primary_family = '"Poppins", sans-serif';
            $font_secondary = 'https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap';
            $font_secondary_family = '"Noto Serif", serif';
            $border_radius = '5px';
            $border_radius_sm = '5px';
            $border_radius_lg = '5px';
            break;
      }
      $site_colors = $data_array['colors'];

?>
   <style>
      @import url('<?php echo $font_primary; ?>');
      @import url('<?php echo $font_secondary; ?>');
      :root{
         --color-primary: rgb(<?php echo implode(",", $site_colors[0]) ?>);
         --color-primary-rgb: <?php echo implode(",", $site_colors[0]) ?>;
         --color-secondary: rgb(<?php echo implode(",", $site_colors[1]) ?>);
         --color-secondary-rgb: <?php echo implode(",", $site_colors[1]) ?>;

         --bs-primary: var( --color-primary );
         --bs-primary-rgb: var( --color-primary-rgb );
         --bs-primary-bg-rgb: var( --color-primary-rgb );
         --bs-secondary: var( --color-secondary );
         --bs-secondary-bg-rgb: var(--color-secondary-rgb);

         --bs-light: #F8F9FA;
         --bs-dark: #343A40;

         /* Btn Link Colors */
         --bs-link-color-rgb: <?php echo implode(",", $site_colors[0]) ?>;
         --bs-link-hover-color-rgb: var(--bs-dark);

         /* Border Radius */
         --bs-border-radius: <?php echo $border_radius ?>;
         --bs-border-radius-sm: <?php echo $border_radius_sm ?>;
         --bs-border-radius-lg: <?php echo $border_radius_lg ?>;
         --bs-border-radius-xl: <?php echo $border_radius ?>;

         /* Font Families  */
         --family-primary: <?php echo $font_primary_family; ?>;
         --family-secondary: <?php echo $font_secondary_family; ?>;

      }
      /* Wordpress Defaults */
      :root :where(.wp-block-button__link.btn) {
         background-color: var(--bs-primary);
         border-width: 1px;
         border-color: var(--bs-primary);
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
         --bs-bg-opacity: 0.3;
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

      .btn-outline-primary {
         --bs-btn-color: var( --color-primary );
         --bs-btn-border-color: var( --color-primary );
         --bs-btn-hover-color: var(  --bs-light );
         --bs-btn-hover-bg: var( --color-primary );
         --bs-btn-hover-border-color: var( --color-primary );
         --bs-btn-active-color: var(  --bs-light );
         --bs-btn-active-bg: rgba( var( --color-primary-rgb ) , .2);
         --bs-btn-active-border-color: rgba( var( --color-primary-rgb ) , .5);
         --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
         --bs-btn-disabled-color: var( --color-primary );
         --bs-btn-disabled-bg: rgba( var( --color-primary-rgb ) , .2);
         --bs-btn-disabled-border-color: rgba( var( --color-primary-rgb ) , .2);
         --bs-gradient: none;
      }
   </style>
<?php
   }
} );

include_once 'includes/cli-builder.php';
include_once 'includes/shortcodes.php';
include_once 'includes/sso.php';