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

    if(!isset($request["sliced"][3]) && $request["type"] == "DELETE" && $auth["scope"] == "teacher") {
        // todo: delete student account
    }

    if(!isset($request["sliced"][3]) && $request["type"] == "GET") {
        $res = $student->export();
        $res["points"] = [0,0,0,0,0,0,0];

        $stmt = $conn->prepare("SELECT amount, timestamp FROM points WHERE student = ? AND timestamp > UNIX_TIMESTAMP()-(86400*7);");
        $studentId = $student->getId();
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $stmt->bind_result($points, $timestamp);
        while($stmt->fetch()) {
            $daysAgo = round((time()-$timestamp) / 86400);
            $res["points"][$daysAgo] += $points;
        }
        die(json_encode($res));
    }

    if(!isset($request["sliced"][3])) renderError("Unable to route request", 404);

    if($request["sliced"][3] == "tasks") {
        if($request["type"] == "PUT") {
            validate([$request["input"]["activity"] => "int"]);
            $stmt = $conn->prepare("SELECT name FROM activities WHERE id = ?;");
            $stmt->bind_param("i", $request["input"]["activity"]);
            $stmt->execute();
            $stmt->bind_result($activityName);
            $stmt->fetch();
            $stmt->close();

            if(empty($activityName)) renderError("Activity does not exist");
            require_once("/var/www/mathsunlocked/api/helpers/Task.php");
            $task = new Task(null);
            $task->setStudent($student->getId());
            if($auth["scope"] == "teacher") {
                $task->setTeacher($auth["user_id"]);
                if(isset($request["input"]["remarks"])) {
                    validate([$request["input"]["remarks"] => "string|alnumspace"]);
                    if(strlen($request["input"]["remarks"]) > 2048) renderError("Remarks cannot exceed 2048 characters");
                    $task->setRemarks($request["input"]["remarks"]);
                }
                if(isset($request["input"]["due"])) {
                    validate([$request["input"]["due"] => "int"]);
                    if($request["input"]["due"] <= time()) renderError("Due date cannot be in the past");
                    $task->setDue($request["input"]["due"]);
                }
            }
            $task->setCreated(time());
            $task->setActivity($activityName);

            $task->create($conn);
            $task->initialise($conn, $request["input"]["activity"]);

            die(json_encode(["task" => $task->getId()]));
        }
        if($request["type"] == "GET") {
            $tasks = [];
            $stmt = $conn->prepare("SELECT id, student, (SELECT name FROM students WHERE students.id = student), teacher, (SELECT name FROM teachers WHERE teachers.id = teacher), created, due, started, completed, activity, remarks FROM tasks WHERE student = ?;");
            $studentId = $student->getId();
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $stmt->bind_result($taskId, $taskStudent, $taskStudentName, $taskTeacher, $taskTeacherName, $taskCreated, $taskDue, $taskStarted, $taskCompleted, $taskActivity, $taskRemarks);
            while($stmt->fetch()) {
                $tasks[] = ["id" => $taskId, "student" => ["id" => $taskStudent, "name" => $taskStudentName], "teacher" => ["id" => $taskTeacher, "name" => $taskTeacherName], "created" => $taskCreated, "due" => $taskDue, "started" => $taskStarted, "completed" => $taskCompleted, "activity" => $taskActivity, "remarks" => $taskRemarks];
            }
            $stmt->close();
            die(json_encode($tasks));
        }
    }

    if($request["sliced"][3] == "token" && $request["type"] == "POST") {
        if($auth["scope"] != "teacher") renderError("Teacher scope required", 403);        
        die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+7 days"), "user" => $student->getUsername(), "name" => $student->getName(), "user_id" => $student->getId(), "scope" => "student"], $jwtSecret)]));
    }
    if($request["sliced"][3] == "qr" && $request["type"] == "GET") {
        die(json_encode(["token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+1 year"), "user_id" => $student->getId(), "scope" => "student", "not_valid_for" => "auth"], $jwtSecret)]));
    }