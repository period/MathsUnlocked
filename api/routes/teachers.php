<?php
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    require_once("/var/www/mathsunlocked/api/helpers/School.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/SchoolClass.php");

    validate([$request["sliced"][2] => "int"]);
    require_once("/var/www/mathsunlocked/api/middleware/auth.php");
    use \Firebase\JWT\JWT;

    if($auth["scope"] != "teacher") renderError("Teacher scope required", 403);
    if($auth["user_id"] != $request["sliced"][2]) renderError("Tried to access wrong user id", 403);

    $teacher = new Teacher($auth["user_id"]);
    $teacher->load($conn);

    if(!isset($request["sliced"][3]) && $request["type"] == "PATCH") {
        foreach($request["input"] as $key=>$value) {
            if($key == "name") {
                validate([$request["input"]["name"] => "string|name"]);
                $teacher->setName($request["input"]["name"]);
            }
            if($key == "username") {
                validate([$request["input"]["username"] => "string|username"]);
                $teacher->setName($request["input"]["username"]);
            }
            if($key == "password") {
                validate([$request["input"]["password"] => "string|password"]);
                $teacher->setHash(password_hash($request["input"]["password"], PASSWORD_DEFAULT));
            }
        }
        $teacher->save($conn);
        die("{}");
    }

    if(!isset($request["sliced"][3])) renderError("Unable to route request", 404);

    if($request["sliced"][3] == "qr" && $request["type"] == "GET") {
        die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+1 year"), "user_id" => $teacher->getId(), "scope" => "teacher", "not_valid_for" => "auth"], $jwtSecret)]));
    }


    if($request["sliced"][3] == "students" && $request["type"] == "GET") {
        $stmt = $conn->prepare("SELECT id, school, username, email, name FROM students WHERE id IN (SELECT student FROM class_students WHERE class IN (SELECT class FROM class_teachers WHERE teacher = ?));");
        $stmt->bind_param("i", $teacher->getId());
        $stmt->execute();
        $stmt->bind_result($studentID, $studentSchool, $studentUsername, $studentEmail, $studentName);
        $students = [];
        while($stmt->fetch()) {
            $student = new Student($studentID, ["school" => $studentSchool, "username" => $studentUsername, "email" => $studentEmail, "name" => $studentName]);
            $students[] = $student->export();
        }
        die(json_encode($students));
    }
