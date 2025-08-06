<?php

/**
 * Title: List of posts, With Pagination
 * Slug: bloxbywp/loop-post-cards-with-image
 * Categories: query
 * Block Types: core/query
 * Description: A list of posts, with featured image and post date with pagination. Can be used in Archive pages.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */

?>

<!-- wp:group {"className":"section-archive common-padding container"} -->
<div class="wp-block-group section-archive common-padding ">
    <!-- wp:query {"queryId":"posts-archive-bloxby","query":{"perPage":9,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[],"format":[]},"metadata":{"categories":["posts"]},"className":"alignfull container"} -->
    <div class="wp-block-query alignfull container">
        <!-- wp:post-template -->
        <!-- wp:group {"className":"card h-100 border-0"} -->
        <div class="wp-block-group card h-100 border-0">

            <!-- wp:post-featured-image {"className":"card-img-top rounded shadow-sm", "isLink":true} /-->

            <!-- wp:group {"className":"card-body p-0"} -->
            <div class="wp-block-group card-body p-0">
                <!-- wp:post-terms {"term":"category","className":"badge rounded-pill text-bg-primary"} /-->

                <!-- wp:post-title {"level":3,"isLink":true,"className":"card-title h3 mt-2"} /-->

                <!-- wp:post-excerpt {"moreText":"Read More", "className":"card-text mt-2"} /-->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->

        <!-- wp:query-no-results -->
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group"
            style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
            <!-- wp:paragraph -->
            <p>Sorry, but nothing was found. Please try a search with different keywords.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
        <!-- /wp:query-no-results -->

        <!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"center"},"className":"archive-pagination d-flex justify-content-center align-items-center gap-2 mt-5"} -->
        <div
            class="wp-block-query-pagination archive-pagination d-flex justify-content-center align-items-center gap-2 mt-5">

            <!-- wp:query-pagination-previous {"label":"Prev","className":"btn btn-outline-primary"} /-->

            <!-- wp:query-pagination-numbers {"className":"pagination pagination-lg mb-0"} /-->

            <!-- wp:query-pagination-next {"label":"Next", "className":"btn btn-outline-primary"} /-->

        </div>
        <!-- /wp:query-pagination -->
    </div>
    <!-- /wp:query -->

</div>
<!-- /wp:group -->