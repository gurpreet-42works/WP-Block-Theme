<?php

    function bloxby_header_menu_shortcode($atts) {
        ob_start();
        wp_nav_menu([
            'theme_location' => 'header-menu',
            'container' => 'nav',
            'container_class' => 'navbar navbar-expand-lg',
            'menu_class' => 'navbar-nav ms-auto gap-4',
        ]);
        return ob_get_clean();
    }
    add_shortcode('get_header_menu', 'bloxby_header_menu_shortcode');


    function bloxby_footer_menu_shortcode($atts) {
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
    
    function bloxby_copyright_text_shortcode() {
        ob_start();
    ?>
        @ <?php echo date("Y"); ?> <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
    <?php
        return ob_get_clean();
    }
    add_shortcode('copyright_text', 'bloxby_copyright_text_shortcode');