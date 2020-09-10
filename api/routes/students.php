<?php
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    require_once("/var/www/mathsunlocked/api/helpers/School.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/SchoolClass.php");

    validate([$request["sliced"][2] => "int"]);
    require_once("/var/www/mathsunlocked/api/middleware/auth.php");
    use \Firebase\JWT\JWT;

    if($auth["scope"] == "student" && $auth["user_id"] != $request["sliced"][2]) renderError("Target student does not match authenticated student");
    $student = new Student($request["sliced"][2]);
    $student->load($conn);
    if($auth["scope"] == "teacher") {
        $user = new Teacher($auth["user_id"]);
        $user->load($conn);
        if($user->getSchool() != $student->getSchool()) renderError("Target user's school does not match teacher's school");
    }

    if(!isset($request["sliced"][3]) && $request["type"] == "PATCH") {
        foreach($request["input"] as $key=>$value) {
            if($key == "name") {
                validate([$request["input"]["name"] => "string|name"]);
                $student->setName($request["input"]["name"]);
            }
            if($key == "username") {
                validate([$request["input"]["username"] => "string|username"]);
                $student->setName($request["input"]["username"]);
            }
            if($key == "password") {
                validate([$request["input"]["password"] => "password"]);
                $student->setHash(password_hash($request["input"]["password"], PASSWORD_DEFAULT));
            }
        }
        $student->save($conn);
        die("{}");
    }

    if(!isset($request["sliced"][3])) renderError("Unable to route request", 404);

    if($request["sliced"][3] == "token" && $request["type"] == "POST") {
        if($auth["scope"] != "teacher") renderError("Teacher scope required", 403);        
        die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+7 days"), "user" => $student->getUsername(), "name" => $student->getName(), "user_id" => $student->getId(), "scope" => "student"], $jwtSecret)]));
    }
    if($request["sliced"][3] == "qr" && $request["type"] == "GET") {
        die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+1 year"), "user_id" => $student->getId(), "scope" => "student", "not_valid_for" => "auth"], $jwtSecret)]));
    }