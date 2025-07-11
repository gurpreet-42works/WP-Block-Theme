<?php

/**
 * Title: Hero Banner Medium Left
 * Slug: bloxbywp/hero-banner-left-aligned
 * Categories: banner
 * Description: A hero banner with image and content contained in container with image on left and content on right.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"align": "full", "className":"alignfull content-with-media-section commpn-padding"} -->
<div class="wp-block-group alignfull hero-banner-content-left">
    <!-- wp:group {"className":"container"} -->
    <div class="wp-block-group container">
        <!-- wp:group {"align":"wide", "className":"row align-items-center"} -->
        <div class="wp-block-group alignwide row align-items-center">
            <!-- wp:media-text {"mediaPosition":"right","mediaType":"image","className":"col-md-12"} -->
            <div class="wp-block-media-text has-media-on-the-right is-stacked-on-mobile alignwide col-md-12">
                <figure class="wp-block-media-text__media">
                    <img src="https://placehold.co/900x900" alt="Placeholder image" />
                </figure>
                <div class="wp-block-media-text__content">
                    <!-- wp:heading {"level":1,"className":"mb-3"} -->
                    <h1 class="wp-block-heading mb-3">Heading Goes Here</h1>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"className":"mb-4"} -->
                    <p class="mb-4">Lorem ipsum dolor sit amet consectetur. Porta nulla erat integer fames pellentesque lacinia amet integer fringilla. Vel consectetur consectetur id amet pharetra molestie massa pharetra mauris.</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:buttons -->
                    <div class="wp-block-buttons">
                        <!-- wp:button {"className":"btn btn-primary btn-lg"} -->
                        <div class="wp-block-button btn btn-primary btn-lg">
                            <a class="wp-block-button__link wp-element-button">Contact Button</a>
                        </div>
                        <!-- /wp:button -->
                    </div>
                    <!-- /wp:buttons -->
                </div>
            </div>
            <!-- /wp:media-text -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->