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
    $json_path = get_option( 'sitedata', []);
    $apiKey = isset($assoc_args['apikey']) ? $assoc_args['apikey'] : '';
    
    if (empty($apiKey)) {
        WP_CLI::error("API key should not be empty. Add a api key with --apikey");
        return;
    }

    if ( empty($json_path) ) {
        WP_CLI::error("Site data not found.");
        return;
    }

    $json = unserialize($json_path);

    if (empty($json['pages'])) {
        WP_CLI::error("No pages found in site data.");
        return;
    }

    $page = $json['pages'][0];

    $website_title = $json['website_title'];
    $website_description = $json['website_description'];

    if( !empty($page) ){
    // foreach ($json['pages'] as $page) {
        
        $page_title = $page['page_title'];
        $page_description = $page['page_description'];
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

        if ($page['page_type'] == 'home') {
            // Set 'Front page displays' to 'A static page'
            update_option('show_on_front', 'page');

            // Set the page as the homepage
            update_option('page_on_front', $page_id);
        }

        $all_blocks = '';

        $sections_array = array();
        foreach ($page['sections'] as $section) {
            if (isset($section['section_type']) && in_array(strtolower($section['section_type']), ['header', 'footer'])) {
                continue; // Skip header/footer sections
            }
            array_push($sections_array, $section);
        }

        $section_json = json_encode($sections_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $html = handle_openai_pattern_call_generation_cli($apiKey, $website_title, $website_description,  $page_title, $page_description, $section_json); // now returns HTML

        if (!$html) {
            WP_CLI::warning("Failed to create page section: $section");
            // continue;
            return false;
        }

        $html = clean_ai_html_output($html);

        $all_blocks .= "\n" . trim($html);

        wp_update_post([
            'ID' => $page_id,
            'post_content' => $all_blocks
        ]);

        WP_CLI::success("Generated page: $page_title (ID: $page_id)");
    }
}

/**
 * Functions for creating blocks using Structured output
 * 
 */
function handle_openai_pattern_call_generation_cli($api_key, $website_title, $website_description, $page_title, $page_description, $section_json)
{
    $patterns_string = file_get_contents(  get_stylesheet_directory() . '/assets/patterns.json' );

    $systemPrompt = 'You are an expert WordPress FSE block builder that generates creative Gutenberg block HTML using Bootstrap 5 classes.';
    $aiPrompt = 'You are a professional WordPress content generator that builds section HTML using block patterns.
    
        Here are the available **WordPress block patterns**:
        '.$patterns_string.'
        
        Each pattern includes:
        - `available_patterns`: a list of valid slugs to randomly pick from
        - `content_needed`: Each field contains the WordPress block format required
        - You MUST generate valid HTML for gutenberg block editor for each field using these WordPress blocks
        
        
        ---
        
        Here is the input JSON from the client describing desired page sections:
        '.$section_json.'

        WEBSITE DETAILS:
        WEBSITE NAME: ' . $website_title . '
        WEBSITE DESCRIPTION: ' . $website_description . '

        INPUT PROCESSING:        
        PAGE TITLE: ' . $page_title . '
        PAGE DESCRIPTION: ' . $page_description . '
        
        ---
        **IMPORTANT FORMAT RULES:**
        - For each field (like heading, description, button), wrap the generated content inside the appropriate WordPress block comment structure **AND include real HTML inside**.
        - For example, if the block is `<!-- wp:heading {{"className":"mb-3"}} -->`, then inside it include an actual `<h2>` or `<h1>` tag like:
        <!-- wp:heading {{"className":"mb-3"}} -->
        <h2 class="wp-block-heading mb-3">Your Title Here</h2>
        <!-- /wp:heading -->
        
        - Do the same for paragraphs, buttons, and lists — use actual `<p>`, `<a>`, `<ul>`, `<li>` etc. inside the comment blocks.
        - Do not generate placeholders. Always return complete HTML code for each field.

         **Processing Rules:**
        Critical: In JSON describing desired page sections Dont copy the description as it is instead generate a description
        - section_type: Use as primary design direction and layout guide
        - section_prompt: Reference only for context - DO NOT copy as literal content
        - Content Generation: Create professional, industry-specific copy based on page title/description  
        - Cohesive Design: Ensure all sections work together harmoniously with consistent styling
        - Industry Adaptation: Tailor content, CTAs, and messaging to match the business type implied by page title

        **Your task:**
        - For each section in the input, select the best-matching pattern based on section type and intent.
        - Randomly pick one of the available pattern slugs for that type.
        - Use the corresponding `content_needed` to format each field to generate the WordPress Gutenberg block HTML and generate the data in each field in content_needed according to the section_description.
        - Generate **professional, relevant content** in WordPress HTML (no placeholders, no lorem ipsum).
        - Return only a JSON array like:
        
        [
        {{
            "section_name": "Section Title",
            "slug": "chosen-slug",
            "content": {{
        "heading": "<!-- wp:heading ... -->Write a unique secton heading<!-- /wp -->",
        "description": "<!-- wp:paragraph ... -->Generate a section desription<!-- /wp -->",
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
            $responseHtml = parse_generated_blocks($responseContent);
            return $responseHtml;
        } else {
            return false;
        }
        
    }

    
    curl_close($ch);
    return false; //Default Fallback
}

function parse_generated_blocks($blocks)
{
    $cleaned_output = clean_ai_html_output($blocks);
    $final_html = '';
    if( !empty($cleaned_output)  ) {
        $output_arr = json_decode($cleaned_output);
        
        foreach( $output_arr as $output ) {
            $pattern_slug = $output->slug;
            $pattern_path = get_stylesheet_directory() . "/patterns/static/{$pattern_slug}.html";
            WP_CLI::print_value( $pattern_path );
            if( file_exists( $pattern_path ) ){
                $pattern_content = file_get_contents( $pattern_path );

                if( !empty($pattern_content) ){
                    if( isset( $output->content->heading ) ) {
                        $pattern_content = str_replace(
                            '<!--section-heading-->',
                            $output->content->heading,
                            $pattern_content
                        );
                    }else {
                        $pattern_content = str_replace(
                            '<!--section-heading-->',
                            '',
                            $pattern_content
                        );
                    }

                    if( isset( $output->content->description ) ) {
                        $pattern_content = str_replace(
                            '<!--section-description-->',
                            $output->content->description,
                            $pattern_content
                        );
                    }else {
                        $pattern_content = str_replace(
                            '<!--section-description-->',
                            '',
                            $pattern_content
                        );
                    }
                    
                    if( isset( $output->content->button ) ) {
                        $pattern_content = str_replace(
                            '<!--cta-button-->',
                            $output->content->button,
                            $pattern_content
                        );
                    }else {
                        $pattern_content = str_replace(
                            '<!--cta-button-->',
                            '',
                            $pattern_content
                        );
                    }

                    if( isset( $output->content->bullet_lists ) ) {
                        $pattern_content = str_replace(
                            '<!--list-group-->',
                            $output->content->bullet_lists,
                            $pattern_content
                        );
                    }else {
                        $pattern_content = str_replace(
                            '<!--list-group-->',
                            '',
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

function clean_ai_html_output($raw_output)
{
    // Try to extract content within ```html ... ```
    if (preg_match('/```json\s*(.*?)```/is', $raw_output, $matches)) {
        return trim($matches[1]);
    }

    // Fallback: remove any lingering code fences and return raw HTML
    $cleaned = preg_replace('/```(?:json)?/i', '', $raw_output);
    return trim(str_replace('```', '', $cleaned));
}
