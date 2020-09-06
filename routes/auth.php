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
            $stmt = $conn->prepare("SELECT id FROM " .$request["input"]["type"]."s WHERE username = ?;");
            $stmt->bind_param("s", $request["sliced"][2]);
            $stmt->execute();
            $stmt->bind_result($userID);
            $stmt->fetch();
            $stmt->close();
            if($request["input"]["type"] == "student") $user = new Student($userID);
            else $user = new Teacher($userID);
            $user->load($conn);
            if($user->getId() == null || empty($user->getId())) renderError("Incorrect username/password", 404);
            if(password_verify($request["input"]["password"], $user->getHash()) == false) renderError("Incorrect username/password", 403);
            die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+7 days"), "user" => $user->getUsername(), "name" => $user->getName(), "user_id" => $user->getId(), "scope" => $request["input"]["type"]], $jwtSecret)]));

        }
    }