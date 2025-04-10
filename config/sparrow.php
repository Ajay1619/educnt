<?php

/*

* SPARROW - A PHP Framework For Web Artisans

*/

// Start session securely
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict', 'secure' => true]);
    session_start();
}

//include globalVariables.php
require_once 'globalVariables.php';

// header("Access-Control-Allow-Origin: " . BASEPATH);


// Security Headers
header('X-Frame-Options: DENY');
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header('X-Content-Type-Options: nosniff');





//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Use file path for error log
ini_set('error_log', ROOT . '/error_log.txt');

//include sqLfunctions.php
require_once 'sqlFunctions.php';



//include globalFunctions.php
require_once 'globalFunctions.php';


$currentLocation = getCurrentLocation();

$getRewrittenUrl = getRewrittenUrl();
if (
    isset($getRewrittenUrl) && $getRewrittenUrl !== "Login" &&
    isset($_GET['action']) &&
    $_GET['action'] !== "forgot_password" &&
    $_GET['action'] !== "send_otp" &&
    $_GET['action'] !== "reset_password"
) {
    checkLogin($logged_login_id);
}
