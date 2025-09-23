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
        'container_class' => 'navbar py-0 mt-3',
        'menu_class' => 'nav flex-column gap-2',
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

function bloxby_newsletter_form_shortcode()
{
    ob_start();
?>
    <div class="bloxby-newsletter-form mt-0"> 
    <?php echo do_shortcode('[contact-form-7 title="Bloxby Newsletter Form"]'); ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('bloxby_newsletter_form', 'bloxby_newsletter_form_shortcode');


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
                            <a class="text-body-secondary" href="' . $value . '" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        </li>';
                        break;
                    case 'instagram':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        </li>';
                        break;
                    case 'twitter':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" target="_blank" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                        </li>';
                        break;
                    case 'linkedin':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" target="_blank" aria-label="linkedin"><i class="fa-brands fa-linkedin"></i></a>
                        </li>';
                        break;
                    case 'pinterest':
                        $icons_html .= '<li class="ms-3">
                            <a class="text-body-secondary" href="' . $value . '" target="_blank" aria-label="Pinterest"><i class="fa-brands fa-pinterest"></i></a>
                        </li>';
                        break;
                    default:
                        $icons_html .= '';
                        break;
                }
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

add_shortcode( 'bloxby_website_description', function() {
    ob_start();

    $site_data = get_option('sitedata', '');


    if (!empty($site_data)) {
        $data_array = unserialize(base64_decode($site_data));
        echo $data_array['website_description'];
    } 

    return ob_get_clean();
} );


add_shortcode( 'bloxby_header_ctas', function() {
    ob_start();
    
    $site_data = get_option('sitedata', '');
    $cta_html = '';

    if (!empty($site_data)) {
        $data_array = unserialize(base64_decode($site_data));
        
        $contact = $data_array['contact_details'];
        $primary_cta = $contact['primary_cta'];

        
        if( !empty( $contact['phone_number'] ) || !empty( $contact['email'] ) ) {
            $cta_html .= '<div class="header-cta cta-single m-0">';

            if( $primary_cta == 'phone' ) {
                $cta_html .= '<a class="cta-link cta-phone m-0" href="tel:'. $contact['phone_number'] .'"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></i><span class="cta-text"><span>Call Now</span> '. $contact['phone_number'] .'</span></a>';
            }
            if( $primary_cta == 'email' ) {
                $cta_html .= '<a class="cta-link cta-email btn btn-primary m-0" href="mailto:'. $contact['email'] .'"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></i><span class="cta-text">Get in Touch</span></a>';
            }

            $cta_html .= '</div>';
        }

    }    
    echo $cta_html;
    return ob_get_clean(); 
} );
