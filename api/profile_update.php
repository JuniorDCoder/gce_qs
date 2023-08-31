<?php
$allowed_origins = array('https://gceqs.icademia.me','http://localhost:8080');

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


require ('../classes/user.class.php');
// Update user profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User('', '', '', '');
    $result = $user->updateProfile($_POST['id'], $_POST['name'], $_POST['password'], $_POST['school']);

    if ($result != false) {
       
        $response = array('success' => true, 'user' => $user);
        
    } else {
        $response = array('success' => false, 'error' => 'Failed To Update Profile');
    }

    // Encode the response as a JSON string and remove any unwanted characters
    $json_response = json_encode($response);
    $json_response = trim($json_response);

    // Set the content type header and output the JSON response
    header('Content-Type: application/json');
    echo $json_response;
}