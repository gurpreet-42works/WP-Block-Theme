<?php

/**
 * Title: Banner BG Content Centered
 * Slug: bloxbywp/hero-banner-bg-content-center
 * Categories: banner
 * Description: A hero banner section with title, sub heading, description and CTA button with full height and verticle centered content. Supports background images.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"className":"d-flex py-5 align-items-center banner-content-centered banner-height bg-body-secondary text-white","style":{"background":{"backgroundImage":{"url":"https://placehold.co/1600x900/dddddd/dddddd/png","title":"banner-bg-image"},"backgroundSize":"cover"}},"layout":{"type":"constrained","contentSize":""}} -->
<div class="wp-block-group d-flex py-5 align-items-center banner-content-centered banner-height bg-body-secondary text-white">
    <!-- wp:group {"layout":{"type":"constrained"}} -->
    <div class="wp-block-group">
        <!-- wp:group {"className":"container text-center","layout":{"type":"constrained","contentSize":"750px"}} -->
        <div class="wp-block-group container text-center"><!-- wp:heading {"textAlign":"center","level":1} -->
            <h1 class="wp-block-heading has-text-align-center">Heading Goes Here</h1>
            <!-- /wp:heading -->

            <!-- wp:heading {"textAlign":"center","fontSize":"medium"} -->
            <h2 class="wp-block-heading has-text-align-center has-medium-font-size">Lorem ipsum dolor sit amet
                consectetur.</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"align":"center"} -->
            <p class="has-text-align-center">Lorem ipsum dolor sit amet consectetur. Porta nulla erat integer fames
                pellentesque lacinia amet integer fringilla. Vel consectetur consectetur id amet pharetra molestie massa
                pharetra mauris.</p>
            <!-- /wp:paragraph -->

            <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
            <div class="wp-block-buttons"><!-- wp:button {"className":"btn btn-primary btn-lg"} -->
                <div class="wp-block-button btn btn-primary btn-lg"><a
                        class="wp-block-button__link wp-element-button">Button</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->