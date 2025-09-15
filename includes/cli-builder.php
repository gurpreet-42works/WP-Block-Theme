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



    $images_array = fetch_images_from_unsplash($apiKey, "9SoUkm0hdm8hp6hSgyVtmOacsej82Zn7QFlvRvTyRbo", $search_keys, 30);
    
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
    $aiPrompt = 'You are a professional **WordPress content generator** that builds section HTML using **block patterns**.
    
    
    Follow this exact reasoning workflow internally before selecting a pattern and writing content and before producing the final JSON:  

    1. **Read Critical Rules**: Carefully review all Critical Rules and IMPORTANT FORMAT RULES and Output Format.  
    2. **Understand Page Context**: Look at `page_type`, `page_title`, and `page_description` to understand the business/industry and tone.  
    3. **Match Section to Pattern**:  
       - Understand each section by looking at section_type , section_title , section_description , section_prompt
       - Then use `section_intent` to select the correct block pattern according to section need.  
       - From that pattern’s `VALID_SLUGS`, randomly pick **exactly one slug** (never use the pattern name itself).  
       - Ensure no two consecutive sections use the same layout category. If two consecutive sections share the same design category (e.g., both “left-aligned”), choose a different layout category for the next section. 
       
    4. **Generate Content**:  
       - For each field in `content_needed`, write valid Gutenberg HTML inside block comments **with real HTML elements**.
       - For each field, strictly follow the exact WordPress block + HTML format defined in that field’s description. 
            Example: If the description says: "Generate using: <!-- wp:paragraph {\"className\":\"mb-4\",\"fontSize\":\"medium\"} -->". Then the AI must output: <!-- wp:paragraph {\"className\":\"mb-4\",\"fontSize\":\"medium\"} --> <p class="mb-4 has-medium-font-size">{{Content Here}}</p> <!-- /wp:paragraph -->.

       - Follow rules for paragraphs (2–3 × 100 words in media-text), FAQs (5–8 Q&A × 2-3 lines), buttons (link to `' . site_url() . '` pages).  
       - If the description requires an array (e.g., features_array) or search terms, output exactly in the specified format (JSON array, comma-separated values, etc.).
       - Never use placeholders or incomplete copy.  
    5. **Validation Step**:  
       - Re-check that every required field in `content_needed` is included.  
       - Validate if the HTML generated is in correct format as defined in that field’s description.
       - Verify that the slug is one of the provided `VALID_SLUGS`.  
       - Ensure banner/page/blog/archive rules are respected.  
    6. **Final Output**:  
       - Return only the JSON array in the exact format below.  
       - Do not include your reasoning, only the finished JSON.  

    ### Available Block Patterns
    ' . $patterns_string . '
    
    Each pattern includes:
    - **section_intent**: Rule to follow before selecting the pattern. Always match the section intent..
    - **VALID_SLUGS**: List of valid design slugs. Randomly pick **exactly one** slug from this list.
    - **content_needed**: Fields that require Gutenberg block HTML. You must fill these with real content and appropriate HTML block if defined in the field description. Return only a JSON array as described there.
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
    - For each field (like heading, description, button), wrap the generated content inside the appropriate WordPress block comment structure defined in the description of the field **AND include real HTML inside**.
    - For example, if the block is `<!-- wp:heading {{"className":"mb-3"}} -->`, then inside it include an actual `<h2>` or `<h1>` tag like:
      <!-- wp:heading {{"className":"mb-3"}} -->
      <h2 class="wp-block-heading mb-3">Your Title Here</h2>
      <!-- /wp:heading -->
    - Do the same for paragraphs, buttons, and lists — use actual <p>, <a>, <ul>, <li> etc. inside the comment blocks.
    - Do not generate placeholders. Always return complete HTML code for each field.
    
    **Critical Rules:**
    1) Slug Selection:
    - Find the correct pattern by section_intent.
    - For each section, you are given a "VALID_SLUGS" array   
    - Randomly select ONE slug from that pattern’s VALID_SLUGS array. Each must have equal probability.
    - The chosen slug MUST exactly match one of the strings inside VALID_SLUGS.
    - **Do NOT output the pattern object\'s top-level key (e.g., DO NOT return "posts_grid_without_pagination"). Only return one of the actual design slugs such as "post-cards-with-image" from VALID_SLUGS array.**
    - For hero banner sections: Randomly select ONE slug from VALID_SLUGS.Each must have equal probability.
    - If two consecutive sections share the same design category (e.g., both “left-aligned”), choose a different layout category for the next section.

    2) Banner Rules:
    - If `page_type = "home"` → **must** use `hero_banner` and Randomly select ONE slug from VALID_SLUGS.Each must have equal probability.
    - If `page_type ≠ "home"` → **must** use `inner_banner` and Randomly select ONE slug from VALID_SLUGS.Each must have equal probability.
    - Ensure not to repeat the same banner layout consecutively.
    
    3) Blog/Archive Rules
    - If page_type is "blog", "news", "archive", or URL contains /blog, /news, /our-blog, /our-beauty-blog → use posts_grid_with_pagination.
    - Otherwise, use posts_grid_without_pagination.
    
    4) Content Rules:
    - Never copy section_prompt or description literally → rewrite into professional copy.
    - Always return all items in the content_needed array for each selected pattern. Do not skip, omit, or ignore any field.
    - When writing content for listing_grid, always create listing_array descriptions with 80 words each.
    - Use real Gutenberg HTML inside the comment wrappers (no placeholders, no lorem ipsum).
    - When writing project always use real project name avoid project 1, project 2 or project alpa etc.
    - When generating content for any media-and-text pattern, always create 2–3 paragraphs, each around 100 words.
    - When generating content for information_content_without_media pattern, always create description in 2–3 paragraphs.
    - When generating content for faq pattern always include faqs_array with 5-8 different questions and description in 2-3 lines about 150 words.
    - Always generate headings (<h1>, <h2>), paragraphs (<p>), buttons (<a>), and lists (<ul><li>) as required.
    - For buttons and links, never use #. Instead:
    - Pick the most relevant page from ' . json_encode($allPages) . '.
    - Full website URL = ' . site_url() . '.
    
    5) Cohesion & Industry Adaptation:
    - Content must be consistent, professional, and industry-appropriate.
    - Sections must work together with a unified design and voice.
    - Tailor CTAs, tone, and structure to match the business type inferred from page title.
    
    **our Task**
    For each input section:
    - Match to the correct block pattern (based on section_intent, section_type, and section_prompt).
    - Randomly pick one slug from VALID_SLUGS.
    - Generate all required fields (content_needed) in valid Gutenberg HTML with meaningful, professional content.
    - Return only the JSON array described below.
    - Do not explain, comment, or output anything else.
    
    ### Output Format
    You must return only a JSON array in this structure:
    
    [
        {
            "section_name": "Section Title",
            "slug": "chosen slug",
            "content": {
                "heading": "<!-- wp:heading ... -->Write a unique section heading<!-- /wp -->",
                "description": "<!-- wp:paragraph ... -->Generate a section description<!-- /wp -->",
                ...
            }
        }
    ]
    ' ;

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
            $responseHtml = parse_generated_blocks($api_key, $responseContent, $images_array, $website_description);
            return $responseHtml;
        } else {
            return false;
        }
    }


    curl_close($ch);
    return false; //Default Fallback
}

function parse_generated_blocks($api_key, $blocks, $images_array, $website_description)
{
    $cleaned_output = clean_ai_json_output($blocks);
    $final_html = '';
    if (!empty($cleaned_output)) {
        $output_arr = json_decode($cleaned_output);
        WP_CLI::print_value($output_arr);
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

                    if (isset($output->content->feature_title)) {
                        $pattern_content = str_replace(
                            '<!--feature-title-->',
                            $output->content->feature_title,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--feature-title-->',
                            '',
                            $pattern_content
                        );
                    }

                    if (isset($output->content->feature_description)) {
                        $pattern_content = str_replace(
                            '<!--feature-description-->',
                            $output->content->feature_description,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--feature-description-->',
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

                    if (isset($output->content->stats_array)) {
                        $stats_array = json_decode($output->content->stats_array);
                        $stats_array_html = '';

                        if( !empty($stats_array) ) {
                            foreach($stats_array as $stat) {
                                $stats_array_html .= '<!-- wp:bloxby-blocks/statistics-bar-without-arrow-child -->
                                    <div
                                        class="wp-block-bloxby-blocks-statistics-bar-without-arrow-child save-block stats-no-arrow-grid-block">
                                        <!-- wp:group {"align":"wide"} -->
                                        <div class="wp-block-group alignwide">
                                            <!-- wp:heading {"textAlign":"center","placeholder":"Enter text here...","fontSize":"xx-large"} -->
                                            <h2 class="wp-block-heading has-text-align-center has-xx-large-font-size">'. $stat->number .'</h2>
                                            <!-- /wp:heading -->

                                            <!-- wp:paragraph {"placeholder":"Enter text here...","style":{"spacing":{"padding":{"bottom":"10px"}}},"fontSize":"medium"} -->
                                            <p class="has-medium-font-size" style="padding-bottom:10px">'. $stat->label .'</p>
                                            <!-- /wp:paragraph -->
                                        </div>
                                        <!-- /wp:group -->
                                    </div>
                                    <!-- /wp:bloxby-blocks/statistics-bar-without-arrow-child -->';
                            }
                        }

                        $pattern_content = str_replace(
                            '<!--stats-custom-html-->',
                            $stats_array_html,
                            $pattern_content
                        );
                    } else {
                        $pattern_content = str_replace(
                            '<!--stats-custom-html-->',
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

                        if( $pattern_slug == "testimonials-slider" ) {
                            foreach ($testimonials_array as $testimonial) {
                                $testimonials_pattern_html .= '<!-- wp:group {"className":"testimonials-slider__slide","layout":{"type":"flex","orientation":"vertical"}} -->
                                    <div class="wp-block-group testimonials-slider__slide">
                                        <!-- wp:heading {"level":3,"className":"testimonial-slide__title"} -->
                                        <h3 class="wp-block-heading testimonial-slide__title">' . $testimonial->title . '</h3>
                                        <!-- /wp:heading -->
                                        <!-- wp:paragraph {"className":"testimonial-slide__desc","fontSize":"medium"} -->
                                        <p class="testimonial-slide__desc has-medium-font-size">' . $testimonial->description . '</p>
                                        <!-- /wp:paragraph -->
                                        <!-- wp:group {"className":"testimonial-slide__cite","layout":{"type":"flex","flexWrap":"nowrap"}} -->
                                        <div class="wp-block-group testimonial-slide__cite">
                                            <!-- wp:image {"width":"40px","height":"40px","scale":"cover","sizeSlug":"large"} -->
                                            <figure class="wp-block-image size-large is-resized"><img src="' . $icon_url . '" alt="" style="object-fit:cover;width:40px;height:40px"/></figure>
                                            <!-- /wp:image -->
                                            <!-- wp:paragraph -->
                                            <p>' . $testimonial->designation . '</p>
                                            <!-- /wp:paragraph -->
                                        </div>
                                        <!-- /wp:group -->
                                    </div>
                                    <!-- /wp:group -->';
                            }
                        }else{
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

                    if (isset($output->content->faq_array)) {
                        $faq_array = json_decode($output->content->faq_array);
                        $faqs_pattern_html = '';

                        foreach ($faq_array as $index => $faq) {
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
                                <details class="wp-block-details">
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
                            if( $pattern_slug == "featured-cards-stacked-fullwidth" ) {
                                foreach ($features_array as $feature) {
                                    $image_arr = fetch_images_from_unsplash("", "9SoUkm0hdm8hp6hSgyVtmOacsej82Zn7QFlvRvTyRbo", $website_description ." " . $feature->heading, 1);
                                    $image = $image_arr[0];
                                    $image_url = $image['url'] . '&w=900&h=600&&fit=crop';
                                    $feature_cards_html .= '
                                        <!-- wp:columns {"verticalAlignment":"center","className":"featured-cards-full__card rounded-2"} -->
                                        <div class="wp-block-columns are-vertically-aligned-center featured-cards-full__card rounded-2">
                                            <!-- wp:column {"verticalAlignment":"center"} -->
                                            <div class="wp-block-column is-vertically-aligned-center">
                                                <!-- wp:group {"className":"featured-cards-full__card-content","layout":{"type":"constrained"}} -->
                                                <div class="wp-block-group featured-cards-full__card-content">
                                                    <!-- wp:heading -->
                                                    <h2 class="wp-block-heading">' . $feature->heading . '</h2>
                                                    <!-- /wp:heading -->
                                                    <!-- wp:paragraph -->
                                                    <p>' . $feature->description . '</p>
                                                    <!-- /wp:paragraph -->
                                                </div>
                                                <!-- /wp:group -->
                                            </div>
                                            <!-- /wp:column -->
                                            <!-- wp:column {"verticalAlignment":"center"} -->
                                            <div class="wp-block-column is-vertically-aligned-center">
                                                <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"featured-cards-full__card-media media-rounded"} -->
                                                <figure class="wp-block-image size-full featured-cards-full__card-media media-rounded"><img src="'. $image_url .'" alt=""/></figure>
                                                <!-- /wp:image -->
                                            </div>
                                            <!-- /wp:column -->
                                        </div>
                                        <!-- /wp:columns -->';
                                }
                            } else {
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

                    //Listings grid
                    $listing_grid_tag = detect_listings_grid($pattern_content);
                    if( $listing_grid_tag['found'] && isset($output->content->listings_array) ) {
                        $listings_array = json_decode($output->content->listings_array);
                        $type = $listing_grid_tag['type'];
                        $layout = $listing_grid_tag['layout'];
                        $full_tag = $listing_grid_tag['full_tag'];
                        $listings_html = '';

                        if( $type == "image" && $layout == "top" && !empty($listings_array) ) {
                            foreach ($listings_array as $listing) {
                            $image_arr = fetch_images_from_unsplash("", "9SoUkm0hdm8hp6hSgyVtmOacsej82Zn7QFlvRvTyRbo", $website_description ." " . $listing->heading , 1);
                            $image = $image_arr[0];
                            $image_url = $image['url'] . '&w=900&h=600&&fit=crop';
                            $listings_html .= '<!-- wp:group {"className":"card h-100 border-0"} -->
                                <div class="wp-block-group card h-100 border-0">
                                    <!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"card-img-top rounded shadow-sm"} -->
                                    <figure class="wp-block-image size-full card-img-top rounded shadow-sm"><img
                                            src="'.$image_url.'"
                                            alt="" />
                                    </figure>
                                    <!-- /wp:image -->

                                    <!-- wp:group {"className":"card-body p-0"} -->
                                    <div class="wp-block-group card-body p-0">
                                        <!-- wp:heading {"level":3,"className":"mb-2"} -->
                                        <h3 class="wp-block-heading mb-2">'.$listing->heading.'</h3>
                                        <!-- /wp:heading -->

                                        <!-- wp:paragraph {"className":"mt-0"} -->
                                        <p class="mt-0">'.$listing->description.'</p>
                                        <!-- /wp:paragraph -->
                                    </div>
                                    <!-- /wp:group -->
                                </div>
                                <!-- /wp:group -->';
                            }
                        }

                        $pattern_content = str_replace(
                            $full_tag,
                            $listings_html,
                            $pattern_content
                        );
                    }

                    //Check if pattern selected has image URL needed then use a random image
                    $image_required = detect_image_tag($pattern_content);
                    if ($image_required['found']) {
                        if( isset( $output->content->search_terms ) ){
                            $gallery_images_array = fetch_images_from_unsplash("", "9SoUkm0hdm8hp6hSgyVtmOacsej82Zn7QFlvRvTyRbo", $website_description ." " . $output->content->search_terms, 1);
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
                            $gallery_images_array = fetch_images_from_unsplash("", "9SoUkm0hdm8hp6hSgyVtmOacsej82Zn7QFlvRvTyRbo", $website_description ." " . $output->content->search_terms, 10);
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
                                    <figure class="wp-block-image size-large overflow-hidden rounded shadow-sm">
                                        <a href="'. $random_image['url'] .'">
                                            <img src="' . $image_url . '" alt="Gallery Image ' . $i . '" />
                                        </a>
                                    </figure>
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
            $images_array = fetch_images_from_unsplash("", "9SoUkm0hdm8hp6hSgyVtmOacsej82Zn7QFlvRvTyRbo", $post->title, 1);
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

function detect_listings_grid($input) {
    $pattern = '/<!--listings-grid\s*\{type\}(.*?)\{\/type\}\s*\{layout\}(.*?)\{\/layout\}\s*-->/';
    // Regex to capture the whole comment and both parameters
    if (preg_match($pattern, $input, $matches)) {
        return [
            'found'   => true,
            'type'    => $matches[1],
            'layout'  => $matches[2],
            'full_tag'=> $matches[0],
        ];
    }

    return ['found' => false];
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
