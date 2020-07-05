<?php
    require_once("/var/www/mathsunlocked/api/vendor/autoload.php");
    require_once("/var/www/mathsunlocked/api/jwt-secret.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    use \Firebase\JWT\JWT;

    if(isset($request["sliced"][2])) {
        if($request["type"] == "POST") {
            validate([$request["sliced"][2] => "string|username", $request["input"]["type"] => "string", $request["input"]["password"] => "string|password"]);
            if($request["input"]["type"] != "student" && $request["input"]["type"] != "teacher") renderError("Invalid user type");

            $user = null;
            if($request["input"]["type"] == "student") $user = new Student($request["sliced"][2]);
            else $user = new Teacher($request["sliced"][2]);
            $user->load($conn);
            if($user->getId() == null || empty($user->getId())) renderError("Incorrect username/password", 404);
            if(password_verify($request["input"]["password"], $user->getHash()) == false) renderError("Incorrect username/password", 403);
            die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+7 days"), "user" => $user->getUsername(), "name" => $user->getName(), "user_id" => $user->getId(), "scope" => $request["input"]["type"]], $jwtSecret)]));

        }
    }