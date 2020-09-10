<?php
    require_once("/var/www/mathsunlocked/api/vendor/autoload.php");
    require_once("/var/www/mathsunlocked/api/jwt-secret.php");
    use \Firebase\JWT\JWT;
    
    if(@empty($_SERVER["HTTP_AUTHORIZATION"])) renderError("Missing Authorization header", 401);

    try {
        $auth = (array) JWT::decode($_SERVER["HTTP_AUTHORIZATION"], $jwtSecret, array("HS256"));
    }
    catch(Exception $exc) {
        renderError("Invalid Authorization header", 401);
    }

    if(isset($auth["not_valid_for"]) && $auth["not_valid_for"] == "auth") renderError("Token is not valid for authentication", 401);