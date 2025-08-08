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
        <?php echo do_shortcode( '[contact-form-7 title="Contact form"]' ); ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('bloxby_contact_form', 'bloxby_contact_form_shortcode');
