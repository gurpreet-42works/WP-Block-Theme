<?php

/**
 * Title: Inner Banner Medium Left
 * Slug: bloxbywp/inner-banner-left-aligned
 * Categories: banner
 * Description: A hero banner with image and content contained from one left side in a container with image on right and content on left.
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"align": "full", "className":"alignfull content-with-media-section banner-content-left inner-banner-content-left bg-body-tertiary inner-page-hero common-padding px-4"} -->
<div class="wp-block-group alignfull content-with-media-section banner-content-left inner-banner-content-left bg-body-tertiary inner-page-hero common-padding px-4">
    <!-- wp:group {"className":"container"} -->
    <div class="wp-block-group container">
        <!-- wp:group {"align":"wide", "className":"row align-items-center"} -->
        <div class="wp-block-group alignwide row align-items-center">
            <!-- wp:media-text {"mediaPosition":"right","mediaType":"image","className":"col-md-12 media-rounded-lg media-shadow"} -->
            <div class="wp-block-media-text has-media-on-the-right is-stacked-on-mobile alignwide col-md-12 media-rounded-lg media-shadow">
                <figure class="wp-block-media-text__media">
                    <img src="https://placehold.co/900x600" alt="Placeholder image" />
                </figure>
                <div class="wp-block-media-text__content">
                    <!-- wp:heading {"level":1,"className":"mb-3"} -->
                    <h1 class="wp-block-heading mb-3">Heading Goes Here</h1>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"className":"mb-4","fontSize":"medium"} -->
                    <p class="mb-4 has-medium-font-size">Lorem ipsum dolor sit amet consectetur. Porta nulla erat integer fames pellentesque lacinia amet integer fringilla. Vel consectetur consectetur id amet pharetra molestie massa pharetra mauris.</p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
            <!-- /wp:media-text -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->