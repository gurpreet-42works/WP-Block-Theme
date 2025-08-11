<?php

function bloxby_header_menu_shortcode($atts)
{
    ob_start();
?>
    <nav class="navbar navbar-expand-lg justify-content-end">
        <button class="header-menu-toggle navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#headerNavbarContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span></span><span></span><span></span></button>
        <?php
        wp_nav_menu([
            'theme_location' => 'header-menu',
            'container' => 'div',
            'container_class' => 'collapse navbar-collapse m-0',
            'container_id' => 'headerNavbarContent',
            'menu_class' => 'header-menu-nav navbar-nav ms-auto gap-4',
        ]);
        ?>
    </nav>
<?php
    return ob_get_clean();
}
add_shortcode('get_header_menu', 'bloxby_header_menu_shortcode');


function bloxby_footer_menu_shortcode($atts)
{
    ob_start();
    wp_nav_menu([
        'theme_location' => 'footer-menu',
        'container' => 'nav',
        'container_class' => 'navbar py-3',
        'menu_class' => 'nav ms-auto gap-4',
    ]);

    return ob_get_clean();
}
add_shortcode('get_footer_menu', 'bloxby_footer_menu_shortcode');

function bloxby_copyright_text_shortcode()
{
    ob_start();
?>
    @ <?php echo date("Y"); ?> <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
<?php
    return ob_get_clean();
}
add_shortcode('copyright_text', 'bloxby_copyright_text_shortcode');

function bloxby_contact_form_shortcode()
{
    ob_start();
?>
    <div class="bloxby-contact-form py-4">
        <?php echo do_shortcode('[contact-form-7 title="Bloxby Contact Form"]'); ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('bloxby_contact_form', 'bloxby_contact_form_shortcode');


function bloxby_social_icons_shortcode()
{
    ob_start();

    $site_data = get_option('sitedata', '');
    if (!empty($site_data)) {
        $data_array = unserialize(base64_decode($site_data));

        $show_icons = false;
        $icons_html = '';
        foreach ($data_array['social_urls'] as $key => $value) {
            if ( !empty($value) ) {
                $show_icons = true;
                switch ($key) {
                    case 'facebook':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        </li>';
                        break;
                    case 'instagram':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        </li>';
                        break;
                    case 'twitter':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                        </li>';
                        break;
                    case 'linkedin':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" aria-label="linkedin"><i class="fa-brands fa-linkedin"></i></a>
                        </li>';
                        break;
                    case 'pinterest':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" aria-label="Pinterest"><i class="fa-brands fa-pinterest"></i></a>
                        </li>';
                        break;
                    default:
                        $icons_html .= '';
                        break;
                }
            } else {
                $show_icons = false;
            }
        }

        if ($show_icons) {
            echo '<ul class="footer-social-nav nav col-md-5 col-12 justify-content-md-end justify-content-center list-unstyled d-flex m-0">';
            echo $icons_html;
            echo '</ul>';
        }
    }

    return ob_get_clean();
}
add_shortcode('bloxby_social_icons', 'bloxby_social_icons_shortcode');
