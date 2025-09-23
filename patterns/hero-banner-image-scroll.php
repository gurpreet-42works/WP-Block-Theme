<?php

/**
 * Title: Banner Image Scroll Animmation
 * Slug: bloxbywp/hero-banner-image-scroll
 * Categories: banner
 * Description: A hero banner section with title and ddescripption and a Image element with scroll animation.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"className":"banner-image-scroll-main","layout":{"type":"constrained"}} -->
<div class="wp-block-group banner-image-scroll-main">
    <!-- wp:group {"className":"container","layout":{"type":"constrained"}} -->
    <div class="wp-block-group container">
        <!-- wp:group {"className":"banner-image-scroll-row-2 m-0 pb-4","layout":{"type":"constrained"}} -->
        <div class="wp-block-group banner-image-scroll-row-2 m-0 pb-4">
            <!-- wp:heading {"level":1} -->
            <h1 class="wp-block-heading">Lorem is simply dummy text</h1>
            <!-- /wp:heading -->
        </div>
        <!-- /wp:group -->
        <!-- wp:group {"className":"banner-image-scroll-row-1 m-0","layout":{"type":"constrained"}} -->
        <div class="wp-block-group banner-image-scroll-row-1 m-0">
            <!-- wp:group {"className":"banner-image-scroll-text","layout":{"type":"constrained"}} -->
            <div class="wp-block-group banner-image-scroll-text">
                <!-- wp:paragraph {"fontSize":"medium"} -->
                <p class="has-medium-font-size"><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                <!-- /wp:paragraph -->
                <!-- wp:buttons -->
                <div class="wp-block-buttons">
                    <!-- wp:button {"className":"wp-block-button__link btn btn-primary btn-lg"} -->
                    <div class="wp-block-button wp-block-button__link btn btn-primary btn-lg"><a class="wp-block-button__link wp-element-button">CTA Button</a></div>
                    <!-- /wp:button -->
                </div>
                <!-- /wp:buttons -->
            </div>
            <!-- /wp:group -->
            <!-- wp:group {"className":"banner-image-scroll-media","layout":{"type":"constrained"}} -->
            <div class="wp-block-group banner-image-scroll-media">
                <!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
                <figure class="wp-block-image size-large"><img src="https://placehold.co/1600x900/png" alt=""/></figure>
                <!-- /wp:image -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->