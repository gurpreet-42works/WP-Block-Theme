<?php
/**
 * Title: Media and Text. Media on Right
 * Slug: bloxbywp/media-text-right-aligned
 * Categories: section
 * Description: A section with content and media with media on right.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"align":"full","className":"content-with-media-section commpn-padding"} -->
<div class="wp-block-group alignfull content-with-media-section common-padding ">
<!-- wp:group {"className":"container"} -->
<div class="wp-block-group container"><!-- wp:group {"align":"wide","className":"row align-items-center"} -->
		<div class="wp-block-group alignwide row align-items-center">
			<!-- wp:media-text {"align":"wide","mediaPosition":"right","mediaType":"image","className":"has-media-on-the-left col-md-12"} -->
			<div
				class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile has-media-on-the-left col-md-12">
				<div class="wp-block-media-text__content"><!-- wp:heading {"className":"mb-3"} -->
					<h2 class="wp-block-heading mb-3">[Heading]</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph {"className":"mb-4"} -->
					<p class="mb-4">Lorem ipsum dolor sit amet consectetur. Porta nulla erat integer fames pellentesque
						lacinia amet integer fringilla. Vel consectetur consectetur id amet pharetra molestie massa
						pharetra mauris.</p>
					<!-- /wp:paragraph -->

					<!-- wp:list {"className":"mb-4"} -->
					<ul class="wp-block-list mb-4"><!-- wp:list-item -->
						<li>Lorem ipsum dolor sit amet consectetur. Elementum cras in enim sem venenatis. Pharetra odio
							in non fringilla posuere massa donec.</li>
						<!-- /wp:list-item -->

						<!-- wp:list-item -->
						<li>Lorem ipsum dolor sit amet consectetur. Elementum cras in enim sem venenatis. Pharetra odio
							in non fringilla posuere massa donec.</li>
						<!-- /wp:list-item -->
					</ul>
					<!-- /wp:list -->
				</div>
				<figure class="wp-block-media-text__media"><img src="https://placehold.co/640x480"
						alt="Placeholder image" /></figure>
			</div>
			<!-- /wp:media-text -->
		</div>
		<!-- /wp:group -->
	</div>
<!-- /wp:group -->
 </div>
<!-- /wp:group -->