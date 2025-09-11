<?php

/**
 * Title: Content With No Media. Description on Right.
 * Slug: bloxbywp/informational-content-right
 * Categories: section
 * Description: A section to show informational content without media. Title on left and description on right.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"metadata":{"categories":["section"],"patternName":"bloxbywp/featured-content-centered","name":"Featured Content Centered"},"align":"full","className":"content-no-media desc-left common-padding bg-dark text-white"} -->
<div class="wp-block-group alignfull content-no-media desc-left common-padding bg-dark text-white">
    <!-- wp:group {"className":"container"} -->
    <div class="wp-block-group container">
        <!-- wp:columns {"className":"content-no-media-columns-heading m-0"} -->
        <div class="wp-block-columns content-no-media-columns-heading m-0">
            <!-- wp:column -->
            <div class="wp-block-column">
                <!-- wp:heading {"fontSize":"x-large"} -->
                <h2 class="wp-block-heading has-x-large-font-size">Heading/Title Here</h2>
                <!-- /wp:heading -->
            </div>
            <!-- /wp:column -->
            <!-- wp:column -->
            <div class="wp-block-column"></div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->
        <!-- wp:columns {"className":"content-no-media-columns-desc m-0"} -->
        <div class="wp-block-columns content-no-media-columns-desc m-0">
            <!-- wp:column -->
            <div class="wp-block-column"></div>
            <!-- /wp:column -->
            <!-- wp:column -->
            <div class="wp-block-column">
                <!-- wp:paragraph {"fontSize":"medium"} -->
                <p class="has-medium-font-size"><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                <!-- /wp:paragraph -->
                <!-- wp:paragraph {"fontSize":"medium"} -->
                <p class="has-medium-font-size">It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
