<?php

/**
 * Enabling CLI Scripts 
 * to generate the blocks using WP-CLI  
 */
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('aibuilder generate-pages', 'aibuilder_generate_pages_cli');
}

function aibuilder_generate_pages_cli($args, $assoc_args)
{
    $json_path = get_option('sitedata', '');
    $apiKey = isset($assoc_args['apikey']) ? $assoc_args['apikey'] : '';

    if (empty($apiKey)) {
        WP_CLI::error("API key should not be empty. Add a api key with --apikey");
        return;
    }

    if (empty($json_path)) {
        WP_CLI::error("Site data not found.");
        return;
    }

    $json = unserialize(base64_decode($json_path));

    if (empty($json['pages'])) {
        WP_CLI::error("No pages found in site data.");
        return;
    }

    //Deleting sample data
    wp_delete_post(1, true);
    wp_delete_post(2, true);

    $website_title = $json['website_title'];
    $website_description = $json['website_description'];
    $industry = $json['website_industry'];
    $search_keys = $json['search_query'];
    $user_logo = isset( $json['user_logo'] ) ? $json['user_logo'] : '';
    $site_colors = $json['colors'];



    $images_array = fetch_images_from_unsplash($apiKey, "wEaTTFCyEpJYE8XjPti48CK0ff74g5Hl0-B8hJ5g9Yk", $search_keys, 30);
    
    //Set Global Title and Description for webiste
    update_option('blogname', $website_title);

   
    //Set a sitelogo and set it globally
    generate_website_logo($apiKey, $website_title, $website_description, $user_logo, $site_colors);

    //Generate some blog posts for the website
    generate_website_posts($apiKey, $website_title, $website_description, $images_array);

    //Add CF7 Contact forms
    create_bloxby_contact_form();
    create_bloxby_newsletter_form();

    $allPages = [];
    foreach ($json['pages'] as $page) {
        $allPages[] = [
            'page_title' => $page['page_title'],
            'page_slug' =>  sanitize_title($page['page_title'])
        ];
    }

    // $page = $json['pages'][0];
    // if (!empty($page)) {
        foreach ($json['pages'] as $page) {

        $page_title = $page['page_title'];
        $page_description = $page['page_description'];
        $page_type = $page['page_type'];
        $page_id = wp_insert_post([
            'post_title'   => wp_strip_all_tags($page_title),
            'post_excerpt' => $page_description,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        if (is_wp_error($page_id)) {
            WP_CLI::warning("Failed to create page: $page_title");
            // continue;
            return false;
        }

        if (strpos($page['page_type'], 'home') !== FALSE) {
            // Set 'Front page displays' to 'A static page'
            update_option('show_on_front', 'page');

            // Set the page as the homepage
            update_option('page_on_front', $page_id);
        }

        // if (strpos($page['page_type'], 'blog') !== FALSE) {
        //     update_option('page_for_posts', $page_id);
        // }

        $all_blocks = '';

        $sections_array = array();
        foreach ($page['sections'] as $section) {
            if (isset($section['section_type']) && in_array(strtolower($section['section_type']), ['header', 'footer'])) {
                continue; // Skip header/footer sections
            }
            array_push($sections_array, $section);
        }

        $section_json = json_encode($sections_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $html = handle_openai_pattern_call_generation_cli($apiKey, $website_title, $website_description, $allPages, $page_title, $page_description, $section_json, $images_array); // now returns HTML

        if (!$html) {
            WP_CLI::warning("Failed to create page section: $section");
            WP_CLI::print_value($section);
            // continue;
            return false;
        }

        $html = clean_ai_json_output($html);

        $all_blocks .= "\n" . trim($html);

        wp_update_post([
            'ID' => $page_id,
            'post_content' => $all_blocks
        ]);

        //Add Page to Navigation
        $header_menu = wp_get_nav_menu_object("header-menu"); //Header Menu
        if ($header_menu) {
            $menu_id = $header_menu->term_id;
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => wp_strip_all_tags($page_title),
                'menu-item-object' => 'page',
                'menu-item-object-id' => $page_id,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish'
            ));
        }

        $footer_menu = wp_get_nav_menu_object("footer-menu"); //Header Menu
        if ($footer_menu) {
            $menu_id = $footer_menu->term_id;
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => wp_strip_all_tags($page_title),
                'menu-item-object' => 'page',
                'menu-item-object-id' => $page_id,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish'
            ));
        }

        WP_CLI::success("Generated page: $page_title (ID: $page_id)");
    }
}

/**
 * Functions for creating blocks using Structured output
 * 
 */
function handle_openai_pattern_call_generation_cli($api_key, $website_title, $website_description, $allPages, $page_title, $page_description, $section_json, $images_array)
{
    $patterns_string = file_get_contents(get_stylesheet_directory() . '/assets/patterns.json');

    $systemPrompt = 'You are an expert WordPress FSE block builder that generates creative Gutenberg block HTML using Bootstrap 5 classes.';
    $aiPrompt = 'You are a professional WordPress content generator that builds section HTML using block patterns.
    
        Here are the available **WordPress block patterns**:
        ' . $patterns_string . '
        
        Each pattern includes:
        - `section_intent`: It is an important rule to follow before selecting the pattern.
        - `VALID_SLUGS`: a list of valid design slugs to randomly pick for the selected pattern. Only pick a random slug given in this array.
        - `content_needed`: Each field contains the WordPress block format required. Return only a JSON array as described here.
        - You MUST generate valid HTML for gutenberg block editor for each field using these WordPress blocks
        
        
        ---
        
        Here is the input JSON from the client describing desired page sections:
        ' . $section_json . '

        WEBSITE DETAILS:
        WEBSITE URL: ' . site_url() . '
        WEBSITE NAME: ' . $website_title . '
        WEBSITE DESCRIPTION: ' . $website_description . '

        INPUT PROCESSING:
        CURRENT PAGE DETAILS:       
        PAGE TITLE: ' . $page_title . '
        PAGE DESCRIPTION: ' . $page_description . '
        PAGE SLUG: ' . sanitize_title($page_title) . '
        
        ---
        **IMPORTANT FORMAT RULES:**
        - For each field (like heading, description, button), wrap the generated content inside the appropriate WordPress block comment structure **AND include real HTML inside**.
        - For example, if the block is `<!-- wp:heading {{"className":"mb-3"}} -->`, then inside it include an actual `<h2>` or `<h1>` tag like:
        <!-- wp:heading {{"className":"mb-3"}} -->
        <h2 class="wp-block-heading mb-3">Your Title Here</h2>
        <!-- /wp:heading -->
        
        - Do the same for paragraphs, buttons, and lists — use actual `<p>`, `<a>`, `<ul>`, `<li>` etc. inside the comment blocks.
        - Do not generate placeholders. Always return complete HTML code for each field.
        - Use the hero_banner pattern only if page_type is "home" and For all other pages use inner_banner pattern for banner.
        - If the page is intended to display all blog posts (e.g. page type “blog”, “news”, “archive”, or URLs containing /blog, /news, /our-blog, /our-beauty-blog), use the posts_grid_with_pagination pattern to generate a paginated post list else select posts_grid_without_pagination pattern.

         **Processing Rules:**
        Critical: In JSON describing desired page sections Dont copy the description as it is instead generate a description
        - section_type: Use as primary design direction and layout guide
        - section_prompt: Reference only for context - DO NOT copy as literal content
        - Content Generation: Create professional, industry-specific copy based on page title/description  
        - Cohesive Design: Ensure all sections work together harmoniously with consistent styling
        - Industry Adaptation: Tailor content, CTAs, and messaging to match the business type implied by page title
        - For each button you generate, do not use # as the link. Instead, use the actual URL path of the page.
        - A list of available pages, each with a page_title and page_slug: ' . json_encode($allPages) . '
        - Whenever you generate a button or link, follow these rules:
            - Never use # or placeholder links.
            - Instead, choose the most contextually relevant page from the list provided.
            - If the link is linked to website page, use full URL donot use relaive links. WEBSITE URL is given.

        STRICT SLUG SELECTION RULES (READ CAREFULLY):
        1. For each input section, locate the matching pattern object in the provided PATTERNS JSON.
        2. **You MUST choose a random value from that pattern object’s "VALID_SLUGS" list (or object keys)**.
        3. **Do NOT output the pattern objects top-level key** (e.g., do NOT return "posts_grid_without_pagination"). Only return one of the actual design slugs listed in VALID_SLUGS array such as "post-cards-with-image".
        4.  **If the previously used section’s slug belongs to the same design category (e.g., "centered content", "left-aligned content", "full-width image banner", etc.), you must select a slug from a different design category in VALID_SLUGS array.
          - Example: If the last section used "media-text-left-aligned", you cannot pick "media-text-left-aligned" immediately after it, since they share the same "left aligned" design layout.

        **Your task:**
        - For each section in the input, select the best-matching pattern based on section type and intent.
        - Use the corresponding `content_needed` to format each field to generate the WordPress Gutenberg block HTML and generate the data in each field in content_needed according to the section_description.
        - Generate **professional, relevant content** in WordPress HTML (no placeholders, no lorem ipsum).
        - Return only a JSON array as described in content_needed JSON in seleted pattern like:
        
        [
        {{
            "section_name": "Section Title",
            "slug": "chosen slug",
            "content": {{
                "heading": "<!-- wp:heading ... -->Write a unique secton heading<!-- /wp -->",
                "description": "<!-- wp:paragraph ... -->Generate a section desription<!-- /wp -->",
                ...
            }}
        }},
        ]
        Return only valid JSON. Do not explain or comment anything.
    ';

    $data = [
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $aiPrompt],
        ],
        'temperature' => 0.7,
    ];

    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    );
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    } else {
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode >= 200 && $httpcode < 300) {
            $responseData = json_decode($result, true);
            $responseContent = $responseData['choices'][0]['message']['content'] ?? 'Error: No content returned';
            $responseHtml = parse_generated_blocks($api_key, $responseContent, $images_array);
            return $responseHtml;
        } else {
            return false;
        }
    }


    curl_close($ch);
    return false; //Default Fallback
}

function parse_generated_blocks($api_key, $blocks, $images_array)
{
    $cleaned_output = clean_ai_json_output($blocks);
    $final_html = '';
    if (!empty($cleaned_output)) {
        $output_arr = json_decode($cleaned_output);
        foreach ($output_arr as $output) {
            $pattern_slug = $output->slug;
            $pattern_path = get_stylesheet_directory() . "/patterns/static/{$pattern_slug}.html";
            WP_CLI::print_value($pattern_path);
            if (file_exists($pattern_path)) {
                $pattern_content = file_get_contents($pattern_path);
                
                if (!empty($pattern_content)) {
                    if (isset($output->content->heading)) {
                        $pattern_content = str_replace(
                            '<!--section-heading-->',
                            $output->content->heading,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--section-heading-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->subheading)) {
                        $pattern_content = str_replace(
                            '<!--section-sub-heading-->',
                            $output->content->subheading,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--section-sub-heading-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->description)) {
                        $pattern_content = str_replace(
                            '<!--section-description-->',
                            $output->content->description,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--section-description-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->button)) {
                        $pattern_content = str_replace(
                            '<!--cta-button-->',
                            $output->content->button,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--cta-button-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->bullet_lists)) {
                        $pattern_content = str_replace(
                            '<!--list-group-->',
                            $output->content->bullet_lists,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--list-group-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->stats_html)) {
                        $pattern_content = str_replace(
                            '<!--statistics-bar-html-->',
                            $output->content->stats_html,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--statistics-bar-html-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->testimonials_array)) {
                        $testimonials_array = json_decode($output->content->testimonials_array);
                        $testimonials_pattern_html = '';
                        $icon_url = get_stylesheet_directory_uri() . "/assets/icons/user-alt.svg";

                        foreach ($testimonials_array as $testimonial) {
                            $testimonials_pattern_html .= '<!-- wp:bloxby-blocks/testimonial-grid -->
                                <div class="wp-block-bloxby-blocks-testimonial-grid save-block testimonial-grid-block"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
                                    <div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
                                        <!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
                                            <figure class="wp-block-image size-full"><img src="' . $icon_url . '" alt=""/></figure>
                                        <!-- /wp:image -->

                                        <!-- wp:paragraph {"placeholder":"Enter testimonial here...","fontSize":"small"} -->
                                        <p class="has-small-font-size">' . $testimonial->title . '</p>
                                        <!-- /wp:paragraph -->

                                        <!-- wp:paragraph {"align":"center","placeholder":"Enter description here..."} -->
                                        <p class="has-text-align-center">' . $testimonial->description . '</p>
                                        <!-- /wp:paragraph -->

                                        <!-- wp:paragraph {"placeholder":"Enter designation here...","style":{"spacing":{"padding":{"bottom":"30px"}},"typography":{"fontWeight":"600","fontStyle":"normal"}},"fontSize":"small"} -->
                                        <p class="has-small-font-size" style="padding-bottom:30px;font-style:normal;font-weight:600">' . $testimonial->designation . '</p>
                                        <!-- /wp:paragraph -->
                                    </div>
                                    <!-- /wp:group -->
                                </div>
                                <!-- /wp:bloxby-blocks/testimonial-grid -->';
                        }
                        $pattern_content = str_replace(
                            '<!--all-testimonials-here-->',
                            $testimonials_pattern_html,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--all-testimonials-here-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->social_cards_array)) {
                        $social_cards_array = json_decode($output->content->social_cards_array);
                        $social_cards_pattern_html = '';

                        foreach ($social_cards_array as $social_card) {
                            $social_cards_pattern_html .= '<!-- wp:column {"className":"card social-card p-4 h-100 text-center"} -->
                                <div class="wp-block-column card social-card p-4 h-100 text-center">
                                    <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"rounded-circle bg-body-secondary"} -->
                                    <figure class="wp-block-image size-full rounded-circle bg-body-secondary">
                                        <img src="' .  get_stylesheet_directory_uri() . '/assets/icons/profile-pic-dummy.png" alt="" />
                                    </figure>
                                    <!-- /wp:image -->

                                    <!-- wp:heading {"level":3} -->
                                    <h3 class="wp-block-heading">'. $social_card->name .'</h3>
                                    <!-- /wp:heading -->

                                    <!-- wp:paragraph -->
                                    <p>'. $social_card->description .'</p>
                                    <!-- /wp:paragraph -->

                                    <!-- wp:group {"className":"social-card-icons","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
                                    <div class="wp-block-group social-card-icons">
                                        <!-- wp:image {"lightbox":{"enabled":false},"width":"35px","height":"35px","scale":"cover","sizeSlug":"full","linkDestination":"custom"} -->
                                        <figure class="wp-block-image size-full is-resized">
                                            <a href="https://www.facebook.com/" target="_blank" rel=" noreferrer noopener">
                                                <img src="' .  get_stylesheet_directory_uri() . '/assets/icons/social-fb.png" alt="" style="object-fit:cover;width:35px;height:35px" />
                                            </a>
                                        </figure>
                                        <!-- /wp:image -->

                                        <!-- wp:image {"lightbox":{"enabled":false},"width":"35px","height":"35px","scale":"cover","sizeSlug":"full","linkDestination":"custom"} -->
                                        <figure class="wp-block-image size-full is-resized">
                                            <a href="https://www.instagram.com/" target="_blank" rel=" noreferrer noopener">
                                                <img src="' .  get_stylesheet_directory_uri() . '/assets/icons/social-insta.png" alt="" style="object-fit:cover;width:35px;height:35px" />
                                            </a>
                                        </figure>
                                        <!-- /wp:image -->

                                        <!-- wp:image {"lightbox":{"enabled":false},"width":"35px","height":"35px","scale":"cover","sizeSlug":"full","linkDestination":"custom"} -->
                                        <figure class="wp-block-image size-full is-resized">
                                            <a href="https://www.linkedin.com/" target="_blank" rel=" noreferrer noopener">
                                                <img src="' .  get_stylesheet_directory_uri() . '/assets/icons/social-linkedin.png" alt="" style="object-fit:cover;width:35px;height:35px" />
                                            </a>
                                        </figure>
                                        <!-- /wp:image -->
                                    </div>
                                    <!-- /wp:group -->
                                </div>
                                <!-- /wp:column -->';
                        }
                        $pattern_content = str_replace(
                            '<!--social-cards-content-->',
                            $social_cards_pattern_html,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--social-cards-content-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->faqs_array)) {
                        $faqs_array = json_decode($output->content->faqs_array);
                        $faqs_pattern_html = '';

                        foreach ($faqs_array as $index => $faq) {
                            if( $index == 0 ){
                                $faqs_pattern_html .= '<!-- wp:details {"showContent":true} -->
                                <details class="wp-block-details" open>
                                    <summary>'. $faq->title .'</summary>
                                    <!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
                                    <p>'. $faq->description .'</p>
                                    <!-- /wp:paragraph -->
                                </details>
                                <!-- /wp:details -->';
                            }else{
                                $faqs_pattern_html .= '<!-- wp:details -->
                                <details class="wp-block-details" open>
                                    <summary>'. $faq->title .'</summary>
                                    <!-- wp:paragraph {"placeholder":"Type / to add a hidden block"} -->
                                    <p>'. $faq->description .'</p>
                                    <!-- /wp:paragraph -->
                                </details>
                                <!-- /wp:details -->';
                            }
                            
                        }
                        $pattern_content = str_replace(
                            '<!--all-faqs-content-->',
                            $faqs_pattern_html,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--all-faqs-content-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->features_array)) {
                        $features_array = json_decode($output->content->features_array);
                        $feature_cards_html = '';
                        if (!empty($features_array)) {
                            foreach ($features_array as $feature) {
                                $feature_cards_html .= '
                                    <!-- wp:group {"className":"card feature-card px-4 py-4","layout":{"type":"constrained","justifyContent":"left"}} -->
                                    <div class="wp-block-group card feature-card px-4 py-4">
                                        <!-- wp:heading {"level":3} -->
                                        <h3 class="wp-block-heading">' . $feature->heading . '</h3>
                                        <!-- /wp:heading -->

                                        <!-- wp:paragraph {"className":"mt-2"} -->
                                        <p class="mt-2">' . $feature->description . '</p>
                                        <!-- /wp:paragraph -->
                                    </div>
                                    <!-- /wp:group -->';
                            }
                        }
                        $pattern_content = str_replace(
                            '<!--features-grid-->',
                            $feature_cards_html,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--features-grid-->',
                            '',
                            $pattern_content
                        );
                    }


                    //Check if pattern selected has image URL needed then use a random image
                    $image_required = detect_image_tag($pattern_content);
                    if ($image_required['found']) {
                        if( isset( $output->content->search_terms ) ){
                            $gallery_images_array = fetch_images_from_unsplash("", "wEaTTFCyEpJYE8XjPti48CK0ff74g5Hl0-B8hJ5g9Yk", $output->content->search_terms, 1);
                        } else{
                            $gallery_images_array = $images_array;
                        }
                        $img_width = $image_required['width'];
                        $img_height =  $image_required['height'];
                        $full_tag = $image_required['full_tag'];

                        $randomKey = array_rand($gallery_images_array);
                        $random_image = $gallery_images_array[$randomKey];
                        $image_url = $random_image['url'] . '&w=' . $img_width . '&h=' . $img_height . '&&fit=crop'; //Crop to required size
                        $pattern_content = str_replace(
                            $full_tag,
                            $image_url,
                            $pattern_content
                        );
                    }

                    //Check if pattern selected has gallery URL needed then use a random gallery
                    $gallery_required = detect_gallery_tag($pattern_content);
                    if ($gallery_required['found']) {
                        if( isset( $output->content->search_terms ) ) {
                            $gallery_images_array = fetch_images_from_unsplash("", "wEaTTFCyEpJYE8XjPti48CK0ff74g5Hl0-B8hJ5g9Yk", $output->content->search_terms, 10);
                        }else{
                            $gallery_images_array = $images_array;
                        }
                        
                        $img_width = $gallery_required['width'];
                        $img_height =  $gallery_required['height'];
                        $full_tag = $gallery_required['full_tag'];
                        $images_count = $gallery_required['count'];
                        $gallery_html = '';

                        for ($i = 0; $i < $images_count; $i++) {
                            if( isset( $output->content->search_terms ) ) {
                                $random_image = $gallery_images_array[$i];
                            }else{
                                $randomKey = array_rand($gallery_images_array);
                                $random_image = $gallery_images_array[$randomKey];
                            }
                            
                            $image_url = $random_image['url'] . '&w=' . $img_width . '&h=' . $img_height . '&&fit=crop'; //Crop to required size
                            $gallery_html .= '<!-- wp:image {"className":"overflow-hidden rounded shadow-sm"} -->
                                    <figure class="wp-block-image size-large overflow-hidden rounded shadow-sm"><img src="' . $image_url . '" alt="Gallery Image ' . $i . '" /></figure>
                                    <!-- /wp:image -->';
                        }

                        $pattern_content = str_replace(
                            $full_tag,
                            $gallery_html,
                            $pattern_content
                        );
                    }
                }
                $final_html .= $pattern_content;
            }
        }
    }

    return $final_html;
}

function generate_website_logo($apiKey, $siteName, $siteDesc, $userLogo, $siteColors)
{
    $logoUrl = '';
    if( !empty($userLogo) && $userLogo != 'false' ){
        $logoUrl = $userLogo;
    }else{
        $primary_colour = implode(",", $siteColors[0]);
        $secondary_colour = implode(",", $siteColors[1]);
        // Construct a new logo using AI
        $prompt = "Create a bold minimalist perfectly round circular logo for website Name: {$siteName}, Description of website: {$siteDesc} and logo elements in the primary color rgb({$primary_colour}) and secondary color rgb({$secondary_colour}).
        Critical Rules to follow:
        1. The circle must cover the full 1:1 image from edge to edge with no empty space, on a pure white (#ffffff) background.
        2. The design should be fully contained within the circle — no parts of the design should extend outside the circle.
        3. The design must be flat 2D vector style, with no lighting, no shadows, no textures, no bevel, no gradients, no 3D effects, and no mockup.
        ";

        // Prepare CURL
        $ch = curl_init("https://api.openai.com/v1/images/generations");

        $data = [
            "model" => "dall-e-3",
            "prompt" => $prompt,
            "size" => "1024x1024",
            "quality" => "standard",
            "n" => 1
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            exit;
        }

        curl_close($ch);

        // Parse and display the generated logo URL
        $result = json_decode($response, true);
        

        if (isset($result['data'][0]['url'])) {
            $logoUrl = $result['data'][0]['url'];
        } else {
            WP_CLI::error("Error in generation: " . $response . PHP_EOL);
            return false;
        }
    }

    if ($logoUrl) {
        //Handle File Upload
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $tmp = download_url($logoUrl);
        if (is_wp_error($tmp)) {
            return false;
        }

        $filename = sanitize_title($siteName . '-logo');

        $file_array = [
            'name' => $filename . '.png',
            'tmp_name' => $tmp
        ];

        $attachment_id = media_handle_sideload($file_array, 0); //Upload to media gallery

        if (is_wp_error($attachment_id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        // Step 3: Set as site logo
        if (function_exists('set_theme_mod')) {
            set_theme_mod('custom_logo', $attachment_id);
        }
    }

}

function create_bloxby_contact_form()
{
    // Check if the form already exists
    $existing = get_page_by_title('Bloxby Contact Form', OBJECT, 'wpcf7_contact_form');

    if ($existing) return;

    // Create the form post
    $form_post = array(
        'post_title'   => 'Bloxby Contact Form',
        'post_status'  => 'publish',
        'post_type'    => 'wpcf7_contact_form',
    );
    $form_id = wp_insert_post($form_post);

    $form_template = '<div class="row g-3">
            <div class="col-md-6 m-0">
                <div class="form-control-wrap">
                    [text* first-name class:form-control class:p-3 class:rounded-3 minlength:1 maxlength:50 placeholder "First Name"]
                </div>
            </div>
            <div class="col-md-6 m-0">
                <div class="form-control-wrap">
                    [text* last-name class:form-control class:p-3 class:rounded-3 minlength:1 maxlength:50 placeholder "Last Name"]
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6 m-0">
                <div class="form-control-wrap">
                    [email* email-address class:form-control class:p-3 class:rounded-3 placeholder "Email Address"]
                </div>
            </div>
            <div class="col-md-6 m-0">
                <div class="form-control-wrap">
                    [tel* phone-number class:form-control class:p-3 class:rounded-3 minlength:1 maxlength:15 placeholder "Phone Number"]
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="form-control-wrap">
                    [textarea user-message class:form-control class:p-3 class:rounded-3 maxlength:500 placeholder] Message [/textarea]
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-auto">
                <div class="form-submit-wrap">
                    [submit class:btn class:btn-primary class:px-4 "Submit"]
                </div>
            </div>
        </div>
    ';

    update_post_meta($form_id, '_form', $form_template);
}

function create_bloxby_newsletter_form()
{
    // Check if the form already exists
    $existing = get_page_by_title('Bloxby Newsletter Form', OBJECT, 'wpcf7_contact_form');

    if ($existing) return;

    // Create the form post
    $form_post = array(
        'post_title'   => 'Bloxby Newsletter Form',
        'post_status'  => 'publish',
        'post_type'    => 'wpcf7_contact_form',
    );
    $form_id = wp_insert_post($form_post);

    $form_template = '<div class="d-flex flex-sm-row w-100 gap-2 mt-3 position-relative">
            <div>[email* newsletter-email class:form-control class:rounded-3 placeholder "Email Address"]</div>
            <div>[submit class:btn class:btn-primary "Subscribe"] </div>
        </div>';

    update_post_meta($form_id, '_form', $form_template);
}

function generate_website_posts($api_key, $website_title, $website_description, $images_array) {
    $prompt = 'Add 3 posts according to the website_title: '. $website_title .' and website description: '. $website_description .'. Keep content below 50 words. Return an array like this: [{\"title\":\"title1\",\"content\":\"blog_content1\"},{\"title\":\"title2\",\"content\":\"blog_content2\"} ...]';
    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            [
                "role" => "system",
                "content" => "You are a helpful content writer who writes well-formatted, detailed HTML posts ready for WordPress publishing."
            ],
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.5
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer {$api_key}"
    ]);
    
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }

    curl_close($ch);

    // Parse the response
    $result = json_decode($response, true);

    $posts_array = [];
    if (isset($result['choices'][0]['message']['content'])) {
        $generatedPosts = $result['choices'][0]['message']['content'];
        if ($generatedPosts) {
            $ai_posts_str = clean_ai_json_output($generatedPosts);
            $posts_array = json_decode( $ai_posts_str );
        }
    }

    if (!empty($posts_array)) {
        foreach ($posts_array as $post) {
            //Upload a dummy Image
            $randomKey = array_rand($images_array);
            $random_image = $images_array[$randomKey];

            $image_url = $random_image['url'] . '&w=1200&h=800&&fit=crop'; //Crop to required size
            $image_name = $random_image['image_name'];
            $attach_id = upload_media_to_library($image_url, $image_name);

            if ($attach_id) {
                generate_post($api_key, $post->title, $post->content, $attach_id);
            }
        }
    }
}

function generate_post($api_key, $post_title, $post_desc, $attach_id, $post_type = 'post')
{
    WP_CLI::print_value("Generating Post:" . $post_title);
    $prompt = "
        Write a comprehensive blog article with:
        Multiple H2 and H3 subheadings to break up content
        Use bullet points wherever appropriate to improve readability
        Around 800-1000 words with detailed explanations

        REQUIREMENTS:
        Use proper HTML formatting. Include:

        Do not include article, section, div or H1 tags. Just use formatting Rich Text tags like heading tags except H1 tag, paragraph tags, list tags. Use bootstrap classes to format the content.

        Do not include any information about legal assistance resources such as specific legal services programs, clinics, or advocacy groups.

        The blog post title is: {$post_title}

        The blog post should be about: {$post_desc}
    ";

    $ch = curl_init("https://api.openai.com/v1/chat/completions");

    $data = [
        "model" => "gpt-4o-mini",
        "messages" => [
            [
                "role" => "system",
                "content" => "You are a helpful SEO blog writer who writes well-formatted, detailed HTML blog posts ready for WordPress publishing."
            ],
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.5
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer {$api_key}"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return false;
    }

    curl_close($ch);

    // Parse the response
    $result = json_decode($response, true);

    if (isset($result['choices'][0]['message']['content'])) {
        $generatedContent = $result['choices'][0]['message']['content'];
        if ($generatedContent) {
            $post_content = clean_ai_html_output($generatedContent);
            // Insert a new post if 
            $post_id = wp_insert_post([
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_excerpt'  => $post_desc,
                'post_status'  => 'publish',
                'post_type' => $post_type,
                'post_author'  => 1, // ID of the author
            ]);
            if ($attach_id) {
                set_post_thumbnail($post_id, $attach_id);
            }
            return true;
        }
    } else {
        return false;
    }
}

function fetch_images_from_unsplash($api_key, $unsplash_key, $search_keys, $limit)
{
    if ($search_keys) {
        $url = "https://api.unsplash.com/photos/random?" . http_build_query([
            'client_id' => $unsplash_key,
            'query' => $search_keys,
            'count' => $limit,
            'orientation' => "landscape"
        ]);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => [
                'Accept-Version: v1',
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return [];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return [];
        }

        $data = json_decode($response, true);
        $imageUrls = [];

        if (is_array($data)) {
            foreach ($data as $image) {
                if (isset($image['urls']['full'])) {
                    $image_slug = $image['slug'];
                    $image_url = $image['urls']['full'];

                    $imageUrls[] = array(
                        'attachment_id' => 0,
                        'image_name' => $image_slug,
                        'url' => $image_url //URL can be appended like &w=1200&h=900&&fit=crop to get proper resolution
                    );
                }
            }
        }

        return $imageUrls;
    }
}

/**
 * Helpers
 */
function upload_media_to_library($image_url, $image_name)
{
    //Upload the Image
    $upload_file = wp_upload_bits($image_name . '.jpg', null, file_get_contents($image_url));

    $attach_id = 0;

    if (!$upload_file['error']) {
        $wp_filetype = wp_check_filetype($upload_file['file'], null);

        $attachment = [
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name($image_name),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_author'  => 1, // ID of the author
        ];

        $attach_id = wp_insert_attachment($attachment, $upload_file['file']);
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    return $attach_id;
}

function clean_ai_json_output($raw_output)
{
    // Try to extract content within ```json ... ```
    if (preg_match('/```json\s*(.*?)```/is', $raw_output, $matches)) {
        return trim($matches[1]);
    }

    // Fallback: remove any lingering code fences and return raw HTML
    $cleaned = preg_replace('/```(?:json)?/i', '', $raw_output);
    return trim(str_replace('```', '', $cleaned));
}

function clean_ai_html_output($raw_output)
{
    // Try to extract content within ```html ... ```
    if (preg_match('/```html\s*(.*?)```/is', $raw_output, $matches)) {
        return trim($matches[1]);
    }

    // Fallback: remove any lingering code fences and return raw HTML
    $cleaned = preg_replace('/```(?:html)?/i', '', $raw_output);
    return trim(str_replace('```', '', $cleaned));
}

function detect_image_tag($pattern_content)
{
    $pattern = '/<!--image\s*\{width\}(\d+)\{\/width\}\s*\{height\}(\d+)\{\/height\}-->/s';

    if (preg_match($pattern, $pattern_content, $matches)) {
        return [
            'found' => true,
            'width' => (int)$matches[1],
            'height' => (int)$matches[2],
            'full_tag' => isset($matches[0]) ? $matches[0] : ''
        ];
    }

    return ['found' => false];
}

function detect_gallery_tag($pattern_content)
{
    $pattern = '/<!--gallery\s*\{width\}(\d+)\{\/width\}\s*\{height\}(\d+)\{\/height\}\s*\{count\}(\d+)\{\/count\}-->/s';

    if (preg_match($pattern, $pattern_content, $matches)) {
        $width = (int)$matches[1];
        $height = (int)$matches[2];
        $count = (int)$matches[3];

        return [
            'found' => true,
            'width' => $width,
            'height' => $height,
            'count' => $count,
            'full_tag' => isset($matches[0]) ? $matches[0] : ''
        ];
    }

    return ['found' => false];
}
