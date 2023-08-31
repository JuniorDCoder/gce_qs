<?php
$allowed_origins = array('https://gceqs.icademia.me', 'http://localhost:8080');

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

require '../classes/user.class.php';

// Register a new donor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $response = array('success' => false, 'error' => 'Password and confirm password do not match');
    } else {
        $user = new User($_POST['name'], $_POST['email'], $_POST['password'], $_POST['school']);
        $register_result = $user->register();

        if ($register_result === true) {
            $response = array('success' => true, 'user' => $user);
            $emailTemplate = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>GCE-QS Registration</title>
            <style>
                /* Add your custom styles here */
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f6f6f6;
                    margin: 0;
                    padding: 0;
                }
                
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                
                .logo img {
                    max-width: 200px;
                }
                
                .message {
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 5px;
                }
                
                .message h2 {
                    color: #333333;
                    font-size: 24px;
                    margin: 0;
                    margin-bottom: 10px;
                }
                
                .message p {
                    color: #555555;
                    font-size: 16px;
                    margin: 0;
                }
                
                .button {
                    display: inline-block;
                    background-color: #4CAF50;
                    border: none;
                    color: white;
                    text-align: center;
                    font-size: 16px;
                    padding: 10px 20px;
                    cursor: pointer;
                    border-radius: 5px;
                    text-decoration: none;
                }
                
                .button:hover {
                    background-color: #45a049;
                }
                
                .contact {
                    margin-top: 20px;
                    padding-top: 20px;
                    border-top: 1px solid #dddddd;
                }
                
                .contact p {
                    margin: 0;
                    padding: 5px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                <img src="https://icademia.me/gce_qs/images/logo.jpg" alt="GCE-QS">
                </div>
                <div class="message">
                    <h2>Welcome</h2>
                    <p>Hello Dear <em>'.$_POST['name'].'</em></p><br>
                    <p>Thank you for Registering With Our Application</p><br>
                    <p>You have taken a bold step towards your success and we will help you Achieve that.</p><br><br>
                    
                </div>
                <div class="contact">
                    <h4>Contact Details:</h4>
                    <p><strong>Phone:</strong> +237 677 802 114</p>
                    <p><strong>Email:</strong> gceqs@icademia.me</p>
                    <p><strong>Address:</strong> Ghana Street, Bamenda, Cameroon</p>
                </div>
            </div>
        </body>
        </html>
    ';
        // Send email using PHP's mail function
        $subject = 'Welcome To GCE-QS';
        $headers = "From: GCE-QS <gceqs@icademia.me>\r\n";
        $headers .= "Reply-To: gceqs@icademia.me\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($_POST['email'], $subject, $emailTemplate, $headers);
        } else if ($register_result === false) {
            $response = array('success' => false, 'error' => 'Some error occurred. Try again!!');
        } else if ($register_result === -1) {
            $response = array('success' => false, 'error' => 'Email Already Exists');
        }
    }

    // Encode the response as a JSON string and remove any unwanted characters
    $json_response = json_encode($response);
    $json_response = trim($json_response);

    // Set the content type header and output the JSON response
    header('Content-Type: application/json');
    echo $json_response;
}