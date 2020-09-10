<?php
    require_once("/var/www/mathsunlocked/api/vendor/autoload.php");
    require_once("/var/www/mathsunlocked/api/jwt-secret.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    use \Firebase\JWT\JWT;

    if(isset($request["sliced"][2])) {
        if($request["type"] == "POST") {
            if(isset($request["input"]["use_qr"]) && $request["input"]["use_qr"] == true) {
                validate([$request["input"]["qr"] => "string", $request["sliced"][2] => "string|username"]);
                try {
                    $qr = (array) JWT::decode($request["input"]["qr"], $jwtSecret, array("HS256"));
                }
                catch(Exception $exc) {
                    renderError("Invalid QR code JWT", 401);
                }
                if(isset($qr["not_valid_for"]) && $qr["not_valid_for"] == "qr") renderError("Token is not valid for QR authentication", 401);
                $request["input"]["type"] = $qr["scope"];
                $userID = $qr["user_id"];

            }
            else validate([$request["sliced"][2] => "string|username", $request["input"]["type"] => "string", $request["input"]["password"] => "string|password"]);
            if($request["input"]["type"] != "student" && $request["input"]["type"] != "teacher") renderError("Invalid user type");
            $user = null;
            $userID = 0;
            if(empty($userID)) {
                $stmt = $conn->prepare("SELECT id FROM " .$request["input"]["type"]."s WHERE username = ?;");
                $stmt->bind_param("s", $request["sliced"][2]);
                $stmt->execute();
                $stmt->bind_result($userID);
                $stmt->fetch();
                $stmt->close();
            }
            if($request["input"]["type"] == "student") $user = new Student($userID);
            else $user = new Teacher($userID);
            $user->load($conn);
            if($user->getId() == null || empty($user->getId())) renderError("Incorrect username/password", 404);
            if(password_verify($request["input"]["password"], $user->getHash()) == false) renderError("Incorrect username/password", 403);
            die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+7 days"), "user" => $user->getUsername(), "name" => $user->getName(), "user_id" => $user->getId(), "scope" => $request["input"]["type"]], $jwtSecret)]));

        }
    }