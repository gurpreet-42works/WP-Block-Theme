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
    $json_path = get_stylesheet_directory() . '/assets/test.json';
    $apiKey = isset( $assoc_args['apikey'] ) ? $assoc_args['apikey'] : '';

    if ( empty($apiKey) ) {
        WP_CLI::error("API key should not be empty. Add a api key with --apikey");
        return;
    }

    if (!file_exists($json_path)) {
        WP_CLI::error("JSON file not found at: $json_path");
        return;
    }

    $json = json_decode(file_get_contents($json_path), true);
    if (empty($json['pages'])) {
        WP_CLI::error("No pages found in JSON.");
        return;
    }

    foreach ($json['pages'] as $page) {
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
            continue;
        }

        if( $page['page_type'] == 'home' ){
            // Set 'Front page displays' to 'A static page'
            update_option( 'show_on_front', 'page' );

            // Set the page as the homepage
            update_option( 'page_on_front', $page_id );
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
        $html = handle_openai_generation_cli($apiKey, $page_title, $page_description, $section_json); // now returns HTML

        if( !$html ){
            WP_CLI::warning("Failed to page section: $section");
            continue;
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

function handle_openai_generation_cli($api_key, $page_title, $page_description, $promptData)
{   
    $systemPrompt = 'You are an expert WordPress FSE block builder that generates creative Gutenberg block HTML using Bootstrap 5 classes.';
    $userPrompt = 'You are an expert WordPress developer and UI/UX designer tasked with creating professional, modern websites using Gutenberg blocks. Convert the  provided section JSON into stunning WordPress Gutenberg blocks HTML that follows current web design trends and best practices.

        CRITICAL: Output ONLY raw WordPress Gutenberg block HTML. No markdown, explanations, or comments.

        DESIGN PHILOSOPHY:
        Create websites that are:
        - Modern & Trendy: Follow 2024-2025 design trends (glassmorphism, bold typography, vibrant gradients, micro-animations)
        - Professional: Clean, polished layouts that build trust
        - User-Centric: Intuitive navigation and clear conversion paths
        - Mobile-First: Responsive design that works perfectly on all devices
        - Performance-Optimized: Clean code structure for fast loading

        TECHNICAL FRAMEWORK:

        Block Structure Requirements:
        - Main Wrapper: Always use core/group with alignfull and common-padding classes
        - Container System: All content inside Bootstrap 5 container classes
        - Custom Classes: Unique semantic class names (e.g., hero-section-modern, testimonials-grid-premium)
        - Hierarchy: Proper nesting with core/group or core/columns containers
        - Block Comments: Always include proper WordPress block comment syntax

        Allowed Core Blocks:
        core/paragraph, core/heading, core/group, core/columns, core/column, core/media-text, core/image, core/gallery, core/buttons, core/button, core/spacer, core/separator, core/query, core/post-template, core/post-title, core/post-excerpt, core/post-featured-image, core/list, core/cover

        DETAILED BLOCK EXAMPLES:

        HERO SECTION TEMPLATE:
        <!-- wp:group {"align":"full","className":"hero-section-modern common-padding has-background"} -->
        <div class="wp-block-group alignfull hero-section-modern common-padding has-background">
            <div class="container py-5">
                <div class="row align-items-center min-vh-75">
                    <div class="col-lg-6">
                        <!-- wp:heading {"level":2,"className":"display-4 fw-bold mb-4"} -->
                        <h2 class="wp-block-heading display-4 fw-bold mb-4">[Industry-Specific Compelling Headline]</h2>
                        <!-- /wp:heading -->
                        
                        <!-- wp:paragraph {"className":"lead mb-4"} -->
                        <p class="lead mb-4">[Engaging value proposition based on page description]</p>
                        <!-- /wp:paragraph -->
                        
                        <!-- wp:buttons {"className":"mb-4"} -->
                        <div class="wp-block-buttons mb-4">
                            <!-- wp:button {"className":"btn-primary btn-lg me-3"} -->
                            <div class="wp-block-button btn-primary btn-lg me-3"><a class="wp-block-button__link wp-element-button">[Action-Oriented CTA]</a></div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                    <div class="col-lg-6">
                        <!-- wp:image {"className":"img-fluid rounded shadow"} -->
                        <figure class="wp-block-image img-fluid rounded shadow"><img src="https://placehold.co/600x400/4f46e5/ffffff?text=Hero+Image" alt="[Descriptive alt text related to business]"/></figure>
                        <!-- /wp:image -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /wp:group -->

        FEATURES/SERVICES SECTION TEMPLATE:
        <!-- wp:group {"align":"full","className":"features-section-modern common-padding"} -->
        <div class="wp-block-group alignfull features-section-modern common-padding">
            <div class="container py-5">
                <!-- wp:heading {"level":2,"textAlign":"center","className":"mb-5"} -->
                <h2 class="wp-block-heading has-text-align-center mb-5">[Section Title]</h2>
                <!-- /wp:heading -->
                
                <!-- wp:columns {"className":"feature-cards-grid"} -->
                <div class="wp-block-columns feature-cards-grid">
                    <!-- wp:column {"className":"mb-4"} -->
                    <div class="wp-block-column mb-4">
                        <!-- wp:group {"className":"feature-card h-100 p-4 text-center border rounded shadow-sm"} -->
                        <div class="wp-block-group feature-card h-100 p-4 text-center border rounded shadow-sm">
                            <!-- wp:image {"width":80,"height":80,"className":"mx-auto mb-3"} -->
                            <figure class="wp-block-image mx-auto mb-3 is-resized"><img src="https://placehold.co/80x80/007cba/ffffff?text=Icon" alt="Feature icon" width="80" height="80"/></figure>
                            <!-- /wp:image -->
                            
                            <!-- wp:heading {"level":3,"className":"h4 mb-3"} -->
                            <h3 class="wp-block-heading h4 mb-3">[Feature Title]</h3>
                            <!-- /wp:heading -->
                            
                            <!-- wp:paragraph -->
                            <p>[Feature description explaining benefits]</p>
                            <!-- /wp:paragraph -->
                        </div>
                        <!-- /wp:group -->
                    </div>
                    <!-- /wp:column -->
                    
                    [Repeat column pattern for 2-3 more features]
                </div>
                <!-- /wp:columns -->
            </div>
        </div>
        <!-- /wp:group -->

        TESTIMONIALS SECTION TEMPLATE:
        <!-- wp:group {"align":"full","className":"testimonials-section-premium common-padding bg-light"} -->
        <div class="wp-block-group alignfull testimonials-section-premium common-padding bg-light">
            <div class="container py-5">
                <!-- wp:heading {"level":2,"textAlign":"center","className":"mb-5"} -->
                <h2 class="wp-block-heading has-text-align-center mb-5">What Our Clients Say</h2>
                <!-- /wp:heading -->
                
                <!-- wp:columns {"className":"testimonials-grid"} -->
                <div class="wp-block-columns testimonials-grid">
                    <!-- wp:column {"className":"mb-4"} -->
                    <div class="wp-block-column mb-4">
                        <!-- wp:group {"className":"testimonial-card bg-white p-4 rounded shadow"} -->
                        <div class="wp-block-group testimonial-card bg-white p-4 rounded shadow">
                            <!-- wp:paragraph {"className":"mb-3 fst-italic"} -->
                            <p class="mb-3 fst-italic">"[Realistic testimonial content based on industry]"</p>
                            <!-- /wp:paragraph -->
                            
                            <!-- wp:media-text {"mediaPosition":"left","mediaWidth":20,"verticalAlignment":"center","className":"align-items-center"} -->
                            <div class="wp-block-media-text is-vertically-aligned-center align-items-center" style="grid-template-columns:20% auto">
                                <figure class="wp-block-media-text__media">
                                    <img src="https://placehold.co/60x60/cccccc/333333?text=Avatar" alt="Client photo"/>
                                </figure>
                                <div class="wp-block-media-text__content">
                                    <!-- wp:paragraph {"className":"mb-0 fw-bold"} -->
                                    <p class="mb-0 fw-bold">[Client Name]</p>
                                    <!-- /wp:paragraph -->
                                    
                                    <!-- wp:paragraph {"className":"mb-0 text-muted small"} -->
                                    <p class="mb-0 text-muted small">[Title, Company]</p>
                                    <!-- /wp:paragraph -->
                                </div>
                            </div>
                            <!-- /wp:media-text -->
                        </div>
                        <!-- /wp:group -->
                    </div>
                    <!-- /wp:column -->
                    
                    [Repeat for 2-3 more testimonials]
                </div>
                <!-- /wp:columns -->
            </div>
        </div>
        <!-- /wp:group -->

        CTA SECTION TEMPLATE:
        <!-- wp:group {"align":"full","className":"cta-section-modern common-padding bg-primary text-white"} -->
        <div class="wp-block-group alignfull cta-section-modern common-padding bg-primary text-white">
            <div class="container py-5">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <!-- wp:heading {"level":2,"textAlign":"center","className":"mb-4"} -->
                        <h2 class="wp-block-heading has-text-align-center mb-4">[Compelling CTA Headline]</h2>
                        <!-- /wp:heading -->
                        
                        <!-- wp:paragraph {"align":"center","className":"lead mb-4"} -->
                        <p class="has-text-align-center lead mb-4">[Urgency/value proposition]</p>
                        <!-- /wp:paragraph -->
                        
                        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                        <div class="wp-block-buttons">
                            <!-- wp:button {"className":"btn-light btn-lg"} -->
                            <div class="wp-block-button btn-light btn-lg"><a class="wp-block-button__link wp-element-button">[Primary CTA]</a></div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /wp:group -->

        BLOG/POSTS SECTION TEMPLATE:
        <!-- wp:group {"align":"full","className":"blog-section-modern common-padding"} -->
        <div class="wp-block-group alignfull blog-section-modern common-padding">
            <div class="container py-5">
                <!-- wp:heading {"level":2,"textAlign":"center","className":"mb-5"} -->
                <h2 class="wp-block-heading has-text-align-center mb-5">Latest Articles</h2>
                <!-- /wp:heading -->
                
                <!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"blog-grid-modern"} -->
                <div class="wp-block-query blog-grid-modern">
                    <!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
                        <!-- wp:group {"className":"post-card h-100 border rounded shadow-sm overflow-hidden"} -->
                        <div class="wp-block-group post-card h-100 border rounded shadow-sm overflow-hidden">
                            <!-- wp:post-featured-image {"className":"card-img-top"} /-->
                            
                            <div class="card-body p-4">
                                <!-- wp:post-title {"level":3,"className":"h5 card-title"} /-->
                                <!-- wp:post-excerpt {"className":"card-text text-muted"} /-->
                                <!-- wp:post-date {"className":"small text-muted mb-3"} /-->
                                
                                <!-- wp:buttons -->
                                <div class="wp-block-buttons">
                                    <!-- wp:button {"className":"btn-outline-primary"} -->
                                    <div class="wp-block-button btn-outline-primary"><a class="wp-block-button__link wp-element-button">Read More</a></div>
                                    <!-- /wp:button -->
                                </div>
                                <!-- /wp:buttons -->
                            </div>
                        </div>
                        <!-- /wp:group -->
                    <!-- /wp:post-template -->
                </div>
                <!-- /wp:query -->
            </div>
        </div>
        <!-- /wp:group -->

        INDUSTRY-SPECIFIC CONTENT GUIDELINES:

        Healthcare/Medical:
        - Focus: Trust, credentials, patient care, safety
        - Headlines: "Expert Medical Care You Can Trust"
        - CTAs: "Schedule Consultation", "Book Appointment"
        - Content: Emphasize experience, certifications, patient outcomes

        E-commerce/Retail:
        - Focus: Products, deals, customer satisfaction, security
        - Headlines: "Premium Products, Unbeatable Prices"
        - CTAs: "Shop Now", "View Catalog", "Get Discount"
        - Content: Product benefits, customer reviews, guarantees

        SaaS/Technology:
        - Focus: Features, efficiency, scalability, ROI
        - Headlines: "Streamline Your Business Operations"
        - CTAs: "Start Free Trial", "See Demo", "Get Started"
        - Content: Feature benefits, use cases, integrations

        Professional Services:
        - Focus: Expertise, results, client success
        - Headlines: "Professional Solutions That Deliver Results"
        - CTAs: "Get Quote", "Contact Us", "Learn More"
        - Content: Service benefits, case studies, credentials

        Restaurant/Food:
        - Focus: Quality, atmosphere, location, menu
        - Headlines: "Fresh Flavors, Exceptional Experience"
        - CTAs: "Make Reservation", "View Menu", "Order Now"
        - Content: Food quality, chef expertise, atmosphere

        Legal Services:
        - Focus: Expertise, track record, client protection
        - Headlines: "Experienced Legal Representation"
        - CTAs: "Free Consultation", "Contact Attorney"
        - Content: Practice areas, success stories, credentials

        VALIDATION CHECKLIST - Verify each output contains:
        - Proper WordPress block comment syntax (<!-- wp:blockname -->)
        - Closing tags for every block (<!-- /wp:blockname -->)
        - Valid JSON in block attributes where needed
        - Bootstrap classes properly applied and spelled correctly
        - All images have proper dimensions and descriptive alt text
        - Semantic HTML structure (h2 for main sections, h3 for subsections)
        - No broken or incomplete blocks
        - All class names are valid CSS
        - Placeholder URLs are properly formatted
        - Industry-appropriate content (no lorem ipsum)
        - Consistent spacing and alignment
        - Responsive design classes included

        COMMON MISTAKES TO AVOID:
        - Never use core/section (doesn\'t exist)
        - Don\'t forget closing block comments
        - Avoid nested headings of same level
        - Don\'t use invalid Bootstrap classes
        - Never leave content placeholders empty
        - Avoid broken or malformed URLs
        - Don\'t mix inconsistent spacing patterns
        - Never use generic "lorem ipsum" text

        PLACEHOLDER IMAGE REQUIREMENTS:
        MANDATORY: ALL images must use https://placehold.co/ with proper dimensions 
        Image Dimension Standards:
        Hero/Banner Images: 1920x1080 or 1600x900
        Feature Icons: 80x80 or 100x100
        Feature Cards: 400x300
        Gallery Images: 500x500 or 400x400
        Media-Text Images: 600x400
        Team/Avatar Photos: 300x300 or 150x150
        Blog Thumbnails: 400x250
        Logo Placeholders: 200x100
        Product Images: 400x400
        Background Images: 1920x800
        URL Format Examples:
        Basic: https://placehold.co/600x400
        BOOTSTRAP 5 UTILITY CLASSES:
        Layout: .container, .container-fluid, .row, .col-, .col-sm-, .col-md-, .col-lg-, .col-xl-, .d-flex, .justify-content-center, .align-items-center
        Spacing: .p-0 to .p-5, .m-0 to .m-5, .py-, .px-, .my-, .mx-, .mt-, .mb-, .ms-, .me-
        Typography: .text-center, .text-start, .text-end, .fw-bold, .fw-normal, .lead, .display-1 to .display-6, .h1 to .h6
        Colors: .text-primary, .text-secondary, .text-success, .text-danger, .bg-primary, .bg-light, .bg-dark, .bg-white
        Display: .d-none, .d-block, .d-flex, .d-grid, .d-inline, .d-inline-block
        Borders: .border, .border-0, .rounded, .rounded-circle, .shadow, .shadow-sm
        Position: .position-relative, .position-absolute, .top-, .bottom-, .start-, .end-

        INPUT PROCESSING:
        PAGE TITLE: ' . $page_title . '
        PAGE DESCRIPTION: ' . $page_description . '
        SECTION JSON: ' . $promptData . '

        Processing Rules:
        1. section_type: Use as primary design direction and layout guide
        2. section_prompt: Reference only for context - DO NOT copy as literal content
        3. Content Generation: Create professional, industry-specific copy based on page title/description
        4. Visual Elements: Generate all required images with proper dimensions and descriptive alt text
        5. Cohesive Design: Ensure all sections work together harmoniously with consistent styling
        6. Industry Adaptation: Tailor content, CTAs, and messaging to match the business type implied by page title

        FINAL OUTPUT REQUIREMENTS:
        Return ONLY the complete WordPress Gutenberg block HTML structure:
        - Properly formatted and indented HTML
        - All custom classes included and properly named
        - Complete Bootstrap 5 integration
        - Professional, industry-specific content
        - Optimized images with proper dimensions
        - SEO-friendly semantic structure
        - Mobile-responsive design
        - No placeholder or incomplete content

        OUTPUT FORMAT: Raw HTML only - no explanations, no markdown, no additional text.';

    $url = 'https://api.openai.com/v1/chat/completions';
        
    $data = [
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ],
        'temperature' => 0.7,
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $responseContent = false;
    } else {
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode >= 200 && $httpcode < 300) {
            $responseData = json_decode($response, true);
            $responseContent = $responseData['choices'][0]['message']['content'] ?? 'Error: No content returned';
        } else {
            $responseContent = false;
        }
    }

    curl_close($ch);

    return $responseContent;
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
