<?php
require ('../classes/user.class.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set the allowed origins for CORS
    $allowed_origins = array('https://gceqs.icademia.me','http://localhost:80');

    // Get the origin header from the request
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

    // Check if the origin is allowed
    if (in_array($origin, $allowed_origins)) {
        // Set the CORS headers
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Cache-Control: no-cache, no-store, must-revalidate');
    }

    $user = User::login($_POST['email'], $_POST['password']);

    if ($user) {
        $response = array(
            'success' => true,
            'user' => $user,
        );
    } else if ($user === 0) {
        $response = array('success' => false, 'error' => "Wrong Password");
    } else if (!$user) {
        $response = array('success' => false, 'error' => "Invalid email or password");
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}