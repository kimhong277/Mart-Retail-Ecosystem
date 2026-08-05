<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔐 GOOGLE OAUTH SECURITY PROFILE DEFINITION KEYS
define('GOOGLE_CLIENT_ID', '656815209579-nh2ceebs4hv5smcmc83f2nf9hvn7lmpl.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
// define('GOOGLE_REDIRECT_URI', 'http://localhost/mart_pos_system/google-callback.php');
define('GOOGLE_REDIRECT_URI', 'http://localhost/mart-retail-ecosystem/mart_pos_system/google-callback.php');

// Simple helper function to generate the authentication click route URL string
function getGoogleLoginUrl()
{
    $endpoint = "https://accounts.google.com/o/oauth2/v2/auth";

    // 💡 FIXED: Separated scopes clearly using a clean space, and added include_granted_scopes
    $params = [
        'client_id'              => GOOGLE_CLIENT_ID,
        'redirect_uri'           => GOOGLE_REDIRECT_URI,
        'response_type'          => 'code',
        'scope'                  => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
        'access_type'            => 'offline',
        'include_granted_scopes' => 'true', // 🚀 REQUIRED for modern Google safety policies
        'prompt'                 => 'select_account'
    ];

    // We use PHP's raw encoding constants to make sure spaces become %20 instead of standard + signs
    return $endpoint . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}
