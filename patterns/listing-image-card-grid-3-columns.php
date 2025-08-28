<?php

/**
 * Title: Listing Card Grid with image
 * Slug: bloxbywp/listing-image-card-grid
 * Categories: section
 * Description: A listing section of 3 columns with image cards in grid.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"className":"service-cards common-padding","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-cards common-padding"><!-- wp:group {"className":"container"} -->
    <div class="wp-block-group container"><!-- wp:group {"className":"section-heading-wrap text-center mb-5"} -->
        <div class="wp-block-group section-heading-wrap text-center mb-5"><!-- wp:heading {"className":"mb-3"} -->
            <h2 class="wp-block-heading mb-3">Title Goes Here</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"className":"mb-4","fontSize":"medium"} -->
            <p class="mb-4 has-medium-font-size">Lorem ipsum dolor sit amet consectetur. Id viverra praesent in tellus
                lectus fusce dictum. <br>Risus gravida aliquam sed vestibulum nec.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->

        <!-- wp:group {"className":"listing-cards-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":null}} -->
        <div class="wp-block-group listing-cards-grid">
            <!-- wp:group {"className":"card h-100 border-0"} -->
            <div class="wp-block-group card h-100 border-0">
                <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"card-img-top rounded shadow-sm"} -->
                <figure class="wp-block-image size-full card-img-top rounded shadow-sm"><img
                        src="https://placehold.co/900x600"
                        alt="" />
                </figure>
                <!-- /wp:image -->

                <!-- wp:group {"className":"card-body p-0"} -->
                <div class="wp-block-group card-body p-0"><!-- wp:heading {"level":3,"className":"mb-2"} -->
                    <h3 class="wp-block-heading mb-2">Lorem Ipsum</h3>
                    <!-- /wp:heading -->

                    <!-- wp:paragraph {"className":"mt-0"} -->
                    <p class="mt-0">Lorem ipsum dolor sit amet consectetur. Id viverra praesent in tellus lectus fusce
                        dictum.<br>Risus gravida aliquam sed vestibulum nec.</p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->