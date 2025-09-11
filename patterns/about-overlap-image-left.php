<?php

/**
 * Title: About section with overllaped images on left
 * Slug: bloxbywp/about-overlap-image-left
 * Categories: about
 * Description: An about section with overllaped images on left and content on right.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"metadata":{"categories":["about"],"patternName":"bloxbywp/about-overlap-image-left","name":"About section with overllaped images on left"},"className":"common-padding about-media-pattren bg-light","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group common-padding about-media-pattren bg-light">
    <!-- wp:columns {"className":"container m-auto are-vertically-aligned-center"} -->
    <div class="wp-block-columns container m-auto are-vertically-aligned-center">
        <!-- wp:column {"verticalAlignment":"center","width":"50%","className":"media-about-sec"} -->
        <div class="wp-block-column is-vertically-aligned-center media-about-sec" style="flex-basis:50%">
            <!-- wp:group {"className":"images-grid","layout":{"type":"grid","columnCount":1,"minimumColumnWidth":null}} -->
            <div class="wp-block-group images-grid">
                <!-- wp:group {"style":{"layout":{"columnSpan":1,"rowSpan":1}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group">
                    <!-- wp:image {"sizeSlug":"large","className":"about-left-img media-rounded"} -->
                    <figure class="wp-block-image size-large about-left-img media-rounded"><img src="https://placehold.co/900x600" alt=""/></figure>
                    <!-- /wp:image -->
                </div>
                <!-- /wp:group -->
                <!-- wp:group {"layout":{"type":"constrained","justifyContent":"center","contentSize":"","wideSize":""}} -->
                <div class="wp-block-group">
                    <!-- wp:image {"width":"300px","sizeSlug":"large","linkDestination":"none","className":"about-right-img media-rounded","style":{"spacing":{"margin":{"bottom":"0"}},"border":{"width":"5px"}},"borderColor":"white"} -->
                    <figure class="wp-block-image size-large is-resized has-custom-border about-right-img media-rounded" style="margin-bottom:0"><img src="https://placehold.co/600x400/" alt="" class="has-border-color has-white-border-color" style="border-width:5px;width:300px"/></figure>
                    <!-- /wp:image -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
            <!-- wp:heading -->
            <h2 class="wp-block-heading">Heading Goes Here</h2>
            <!-- /wp:heading -->
            <!-- wp:paragraph -->
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <!-- /wp:paragraph -->
            <!-- wp:paragraph -->
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->