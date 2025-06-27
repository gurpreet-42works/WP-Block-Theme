<?php

/**
 * Title: List of posts, 1 column
 * Slug: bloxbywp/template-query-loop
 * Categories: query
 * Block Types: core/query
 * Description: A list of posts, 1 column, with featured image and post date.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"taxQuery":null,"parents":[]},"align":"full","layout":{"type":"default"}} -->
<div class="wp-block-query alignfull container">
	<!-- wp:post-template -->

	<!-- wp:group {"className":"card h-100 shadow-sm"} -->
		<div class="wp-block-group card h-100 shadow-sm">

			<!-- wp:post-featured-image {"className":"card-img-top"} /-->

			<!-- wp:group {"className":"card-body"} -->
			<div class="wp-block-group card-body">

				<!-- wp:post-title {"isLink":true,"level":3,"className":"card-title h5"} /-->

				<!-- wp:post-excerpt {"className":"card-text"} /-->

				<!-- wp:post-date {"format":"F j, Y","className":"text-muted small"} /-->

				<!-- wp:post-terms {"term":"category","className":"text-muted small"} /-->

				<!-- wp:post-terms {"term":"post_tag","className":"text-muted small"} /-->

				<!-- wp:post-link {"className":"btn btn-primary mt-3"} -->
				<a class="wp-block-post-link btn btn-primary mt-3">Read More</a>
				<!-- /wp:post-link -->

			</div>
			<!-- /wp:group -->

		</div>
	<!-- /wp:group -->

	<!-- /wp:post-template -->
	<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
		<!-- wp:query-no-results -->
		<!-- wp:paragraph -->
		<p><?php echo esc_html_x('Sorry, but nothing was found. Please try a search with different keywords.', 'Message explaining that there are no results returned from a search.', 'bloxbywp'); ?></p>
		<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
		<!-- /wp:query-pagination -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:query -->