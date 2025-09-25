<?php

add_action('rest_api_init', function () {
    register_rest_route('wp/v1', '/autologin', [
        'methods' => 'GET',
        'callback' => 'handle_auto_login',
        'permission_callback' => '__return_true', // secured via signature
    ]);
});

function handle_auto_login(WP_REST_Request $request)
{

    $payload = $request->get_param('payload');
    $email = $request->get_param('email');
    $site_id = get_option( 'boxby_siteid', false );


    
    if (!$payload || !$site_id) {
        return new WP_REST_Response(['error' => 'Missing parameters'], 400);
    }

    $secret_key = 'Bloxby_website_#000009';
    $site_key = 'site_id_' . $site_id . '|email_id_' . $email;

    $calculated_signature = hash_hmac('sha256', $site_key, $secret_key);
   
    if ( !hash_equals($calculated_signature, $payload) ) {
        return new WP_REST_Response(['error' => 'Invalid signature'], 403);
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        return new WP_REST_Response(['error' => 'User not found'], 404);
    }

    wp_set_auth_cookie($user->ID, true);
    wp_set_current_user($user->ID);

    $redirect_url = admin_url();
    wp_redirect($redirect_url);
    exit;
}
