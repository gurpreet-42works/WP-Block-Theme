<?php

/**
 * Title: List of posts, No Pagination
 * Slug: bloxbywp/template-query-loop
 * Categories: query
 * Block Types: core/query
 * Description: A list of posts, with featured image and post date.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */

?>
<!-- wp:query {"queryId":"list-posts-paged-bloxby","query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[],"format":[]},"metadata":{"categories":["posts"]},"className":"alignfull common-padding"} -->
<div class="wp-block-query alignfull common-padding">
	<!-- wp:group {"className":"container"} -->
	<div class="wp-block-group container">
		<!-- wp:group {"className":"section-heading-wrap text-center"} -->
        <div class="wp-block-group section-heading-wrap text-center">
            <!-- wp:heading {"className":"mb-3"} -->
            <h2 class="wp-block-heading mb-3">Title Goes Here</h2>
            <!-- /wp:heading -->

            <!-- wp:paragraph {"className":"mb-4","fontSize":"medium"} -->
            <p class="mb-4 has-medium-font-size">Lorem ipsum dolor sit amet consectetur. Id viverra praesent in tellus lectus fusce dictum. <br>Risus gravida aliquam sed vestibulum nec.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
		<!-- wp:post-template -->
		<!-- wp:group {"className":"card h-100 border-0"} -->
		<div class="wp-block-group card h-100 border-0">

			<!-- wp:post-featured-image {"className":"card-img-top rounded-0", "isLink":true} /-->

			<!-- wp:group {"className":"card-body p-0"} -->
			<div class="wp-block-group card-body p-0">
				
				<!-- wp:post-title {"level":3,"isLink":true,"className":"card-title h3 mt-2"} /-->

				<!-- wp:post-excerpt {"moreText":"Read More", "className":"card-text mt-2"} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
			<!-- wp:paragraph -->
			<p>Sorry, but nothing was found. Please try a search with different keywords.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:query -->