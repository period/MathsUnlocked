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

    if(!isset($request["sliced"][3]) && $request["type"] == "PATCH") {
        // todo: delete teacher
    }

    if(!isset($request["sliced"][3])) renderError("Unable to route request", 404);

    if($request["sliced"][3] == "qr" && $request["type"] == "GET") {
        die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+1 year"), "user_id" => $teacher->getId(), "scope" => "teacher", "not_valid_for" => "auth"], $jwtSecret)]));
    }


    if($request["sliced"][3] == "students" && $request["type"] == "GET") {
        $stmt = $conn->prepare("SELECT id, school, username, email, name FROM students WHERE id IN (SELECT student FROM class_students WHERE class IN (SELECT class FROM class_teachers WHERE teacher = ?));");
        $tid = $teacher->getId();
        $stmt->bind_param("i", $tid);
        $stmt->execute();
        $stmt->bind_result($studentID, $studentSchool, $studentUsername, $studentEmail, $studentName);
        $students = [];
        while($stmt->fetch()) {
            $student = new Student($studentID, ["school" => $studentSchool, "username" => $studentUsername, "email" => $studentEmail, "name" => $studentName]);
            $students[] = $student->export();
        }
        die(json_encode($students));
    }
    if($request["sliced"][3] == "classes" && $request["type"] == "GET") {
        $stmt = $conn->prepare("SELECT id, school, name FROM classes WHERE id IN (SELECT class FROM class_teachers WHERE teacher = ?);");
        $tid = $teacher->getId();
        $stmt->bind_param("i", $tid);
        $stmt->execute();
        $stmt->bind_result($classID, $classSchool, $className);
        $classes = [];
        while($stmt->fetch()) {
            $classes[] = ["id" => $classID, "school" => $classSchool, "name" => $className];
        }
        die(json_encode($classes));
    }

    if($request["sliced"][3] == "tasks" && $request["type"] == "GET") {
        $tasks = [];
        $stmt = $conn->prepare("SELECT id, student, (SELECT name FROM students WHERE students.id = student), teacher, (SELECT name FROM teachers WHERE teachers.id = teacher), created, due, started, completed, activity, remarks FROM tasks WHERE teacher IS NOT NULL and teacher = ?;");
        $teacherId = $teacher->getId();
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        $stmt->bind_result($taskId, $taskStudent, $taskStudentName, $taskTeacher, $taskTeacherName, $taskCreated, $taskDue, $taskStarted, $taskCompleted, $taskActivity, $taskRemarks);
        while($stmt->fetch()) {
            $tasks[] = ["id" => $taskId, "student" => ["id" => $taskStudent, "name" => $taskStudentName], "teacher" => ["id" => $taskTeacher, "name" => $taskTeacherName], "created" => $taskCreated, "due" => $taskDue, "started" => $taskStarted, "completed" => $taskCompleted, "activity" => $taskActivity, "remarks" => $taskRemarks];
        }
        $stmt->close();
        die(json_encode($tasks));
    }