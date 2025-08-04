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

    function bloxby_contact_form_shortcode() {
        ob_start();
    ?>
        <form class="my-2">
            <div class="row g-3">
                <div class="col-md-6 m-0">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="inputFirstName" placeholder="First Name" required>
                        <label for="inputFirstName">First Name</label>
                    </div>
                </div>
                <div class="col-md-6 m-0">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="inputLastName" placeholder="Last Name">
                        <label for="inputLastName">Last Name</label>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6 m-0">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="emailInput" placeholder="name@example.com" required>
                        <label for="emailInput">Email address</label>
                    </div>
                </div>
                <div class="col-md-6 m-0">
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="phoneInput" placeholder="+1 9876543210" required>
                        <label for="emailInput">Phone Number</label>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control" placeholder="Message" id="floatingTextarea" style="height: 150px;"></textarea>
                    <label for="floatingTextarea">Message</label>
                </div>
            </div>
            </div>

            <div class="row mt-4">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4">Send</button>
            </div>
            </div>
        </form>
    <?php
        return ob_get_clean();
    }
    add_shortcode('bloxby_contact_form', 'bloxby_contact_form_shortcode');