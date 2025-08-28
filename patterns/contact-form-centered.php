<?php

/**
 * Title: Contact Form Centered
 * Slug: bloxbywp/contact-form-centered
 * Categories: contact
 * Description: A contact form module with heading, subheading in full width layout.
 *
 * @package WordPress
 * @subpackage Bloxby_WP
 * @since 1.0
 */
?>

<!-- wp:group {"align":"full","className":"d-flex align-items-center contact-form-centered px-4 py-5 text-center"} -->
<div class="wp-block-group alignfull d-flex align-items-center contact-form-centered px-4 py-5 text-center"><!-- wp:group {"className":"mx-auto","layout":{"type":"constrained","contentSize":"750px"}} -->
    <div class="wp-block-group mx-auto"><!-- wp:heading {"className":"mb-3"} -->
        <h2 class="wp-block-heading mb-3">Title Goes Here</h2>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"className":"mb-4 mt-0","fontSize":"medium"} -->
        <p class="mb-4 mt-0 has-medium-font-size">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
        <!-- /wp:paragraph -->

        <!-- wp:shortcode -->
        [bloxby_contact_form]
        <!-- /wp:shortcode -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->