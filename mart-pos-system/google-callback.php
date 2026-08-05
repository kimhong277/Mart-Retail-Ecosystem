<?php
// google-callback.php
require_once 'config.php'; // 🔐 RESTORED: Vital for initializing session handles dynamically
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
if (!$conn) {
    die("Database engine link down: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_GET['code'])) {
    header("Location: login.php?status=auth_error");
    exit();
}

$code = $_GET['code'];

// 1️⃣ EXCHANGE THE AUTHORIZATION CODE FOR AN ACCESS TOKEN
$token_url = "https://oauth2.googleapis.com/token";
$post_fields = [
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Safe for local development testing
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);

if (!isset($token_data['access_token'])) {
    header("Location: login.php?status=token_error");
    exit();
}

$access_token = $token_data['access_token'];

// 2️⃣ REQUEST THE USER'S GOOGLE PROFILE INFO DATA PAYLOAD
$userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $access_token;
$profile_response = file_get_contents($userinfo_url);
$user_profile = json_decode($profile_response, true);

if (empty($user_profile['email'])) {
    header("Location: login.php?status=profile_error");
    exit();
}

// Extract variables cleanly
$google_id = mysqli_real_escape_string($conn, $user_profile['id']);
$email     = mysqli_real_escape_string($conn, $user_profile['email']);
$fullname  = mysqli_real_escape_string($conn, $user_profile['name']);

// 3️⃣ CROSS-REFERENCE DATABASE TO INITIALIZE TERMINAL SESSION
$check_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");

if (mysqli_num_rows($check_user) > 0) {
    $user = mysqli_fetch_assoc($check_user);

    // Ensure their google_id field is updated if it was missing
    if (empty($user['google_id'])) {
        mysqli_query($conn, "UPDATE users SET google_id = '$google_id' WHERE id = " . $user['id']);
    }
} else {
    // HYBRID HANDLER: Create them instantly if they are a new operator
    $username = mysqli_real_escape_string($conn, explode('@', $email)[0]);

    $insert_sql = "INSERT INTO users (username, fullname, email, google_id, role) 
                   VALUES ('$username', '$fullname', '$email', '$google_id', 'cashier')";

    if (mysqli_query($conn, $insert_sql)) {
        $new_user_id = mysqli_insert_id($conn);
        $get_new = mysqli_query($conn, "SELECT * FROM users WHERE id = $new_user_id LIMIT 1");
        $user = mysqli_fetch_assoc($get_new);
    } else {
        header("Location: login.php?status=db_creation_failed");
        exit();
    }
}

// 4️⃣ COMMIT SNAPSHOT DATA TO TERMINAL SESSION LOCKER
$_SESSION['user_id']   = $user['id'];
$_SESSION['username']  = $user['username'];
$_SESSION['fullname']  = $user['fullname'];
$_SESSION['email']     = $user['email'];
$_SESSION['user_role'] = $user['role'];

// 🚀 FIXED: Dynamic redirect routing parameter matches your SweetAlert engine listeners!
header("Location: index.php?page=dashboard&status=sale_success");
exit();
