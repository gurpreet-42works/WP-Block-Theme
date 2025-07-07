<?php
/**
 * Title: Hero Banner Medium Image Right
 * Slug: bloxbywp/content-with-media-on-left
 * Categories: section
 * Description: A section with content and media with media on left.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"align":"full","className":"content-with-media-section commpn-padding"} -->
<div class="wp-block-group alignfull content-with-media-section common-padding ">
<!-- wp:group {"className":"container"} -->
<div class="wp-block-group container">
    <!-- wp:group {"align":"wide", "className":"row align-items-center"} -->
    <div class="wp-block-group alignwide row align-items-center">
        <!-- wp:media-text {"mediaPosition":"left","mediaType":"image","className":"col-md-12"} -->
        <div class="wp-block-media-text has-media-on-the-left is-stacked-on-mobile alignwide col-md-12">
            <figure class="wp-block-media-text__media">
                <img src="https://placehold.co/640x480" alt="Placeholder image"/>
            </figure>
            <div class="wp-block-media-text__content">
                <!-- wp:heading {"level":2,"className":"mb-3"} -->
                <h2 class="wp-block-heading mb-3">Heading Goes Here</h2>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"className":"mb-4"} -->
                <p class="mb-4">Lorem ipsum dolor sit amet consectetur. Porta nulla erat integer fames pellentesque lacinia amet integer fringilla. Vel consectetur consectetur id amet pharetra molestie massa pharetra mauris.</p>
                <!-- /wp:paragraph -->

                <!-- wp:list {"className":"mb-4"} -->
                <ul class="mb-4">
                    <li>Lorem ipsum dolor sit amet consectetur. Elementum cras in enim sem venenatis. Pharetra odio in non fringilla posuere massa donec.</li>
                    <li>Lorem ipsum dolor sit amet consectetur. Elementum cras in enim sem venenatis. Pharetra odio in non fringilla posuere massa donec.</li>
                </ul>
                <!-- /wp:list -->

                <!-- wp:buttons -->
                <div class="wp-block-buttons">
                    <!-- wp:button {"className":"btn btn-primary"} -->
                    <div class="wp-block-button btn btn-primary">
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