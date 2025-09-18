<?php
/**
 * Title: Feature Detail - background image with overlay text on left
 * Slug: bloxbywp/feature-detail-block-right
 * Categories: section
 * Description: A featured deatail section with description in a background image block on left and highlight and heading block on right. 
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"className":"feature-details-block-right common-padding","layout":{"type":"constrained"}} -->
<div class="wp-block-group feature-details-block-right common-padding">
    <!-- wp:group {"className":"container","layout":{"type":"constrained"}} -->
    <div class="wp-block-group container">
        <!-- wp:columns {"className":"feature-details-columns","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
        <div class="wp-block-columns feature-details-columns">
            <!-- wp:column {"verticalAlignment":"stretch","width":"70%","className":"h-auto"} -->
            <div class="wp-block-column is-vertically-aligned-stretch h-auto" style="flex-basis:70%">
                <!-- wp:group {"className":"p-5 rounded-1 feature-detail-column__left text-white","style":{"background":{"backgroundImage":{"url":"https://placehold.co/1600x900/555/555/png","title":"feature-bg-image"},"backgroundSize":"cover"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group p-5 rounded-1 feature-detail-column__left text-white">
                    <!-- wp:paragraph {"fontSize":"medium"} -->
                    <p class="has-medium-font-size"><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
                    <!-- /wp:paragraph -->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:column -->
            <!-- wp:column {"verticalAlignment":"stretch","width":"30%"} -->
            <div class="wp-block-column is-vertically-aligned-stretch" style="flex-basis:30%">
                <!-- wp:group {"className":"p-5 rounded-1 shadow-sm feature-detail-column__right","layout":{"type":"flex","orientation":"vertical","justifyContent":"left","verticalAlignment":"space-between"}} -->
                <div class="wp-block-group p-5 rounded-1 shadow-sm feature-detail-column__right">
                    <!-- wp:paragraph -->
                    <p>Lorem Ipsum</p>
                    <!-- /wp:paragraph -->
                    <!-- wp:group {"layout":{"type":"default"}} -->
                    <div class="wp-block-group">
                        <!-- wp:heading -->
                        <h2 class="wp-block-heading"><strong>Lorem Ipsum</strong></h2>
                        <!-- /wp:heading -->
                        <!-- wp:paragraph {"fontSize":"medium"} -->
                        <p class="has-medium-font-size"><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry.</p>
                        <!-- /wp:paragraph -->
                    </div>
                    <!-- /wp:group -->
                    
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:column -->
        </div>
        <!-- /wp:columns -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
