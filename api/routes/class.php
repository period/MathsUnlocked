<?php
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    require_once("/var/www/mathsunlocked/api/helpers/School.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/SchoolClass.php");

    validate([$request["sliced"][2] => "int"]);
    require_once("/var/www/mathsunlocked/api/middleware/auth.php");
    if($auth["scope"] != "teacher") renderError("Teacher scope required", 403);
    $user = new Teacher($auth["user_id"]);
    $user->load($conn);

    $schoolClass = new SchoolClass($request["sliced"][2]);
    $schoolClass->load($conn);
    if($schoolClass->getSchool() == null || $schoolClass->getSchool() != $user->getSchool()) renderError("No such class", 404);

    if(!isset($request["sliced"][3])) {
        if($request["type"] == "GET") {
            $response = $schoolClass->export();
            $response["students"] = [];
            foreach($schoolClass->getStudents($conn) as $student) $response["students"][] = $student->export();
            $response["teachers"] = [];
            foreach($schoolClass->getTeachers($conn) as $teacher) $response["teachers"][] = $teacher->export();
            die(json_encode($response));
        }
        else if($request["type"] == "DELETE") {
            $school = new School($schoolClass->getSchool());
            $school->load($conn);
            if($school->getOwner() != $user->getId()) renderError("You need ownership of the school to be able to do this", 403);
            $schoolClass->delete($conn);
            die("{}");
        }
        else if($request["type"] == "PATCH") {
            $school = new School($schoolClass->getSchool());
            $school->load($conn);
            if($school->getOwner() != $user->getId()) renderError("You need ownership of the school to be able to do this", 403);
            validate([$request["input"]["name"] => "string|alnumspace"]);
            $schoolClass->setName($request["input"]["name"]);
            $schoolClass->save($conn);
            die("{}");
        }
    }
    else {
        if($request["type"] == "GET" && $request["sliced"][3] == "tasks") {
            $tasks = [];
            $stmt = $conn->prepare("SELECT id, student, (SELECT name FROM students WHERE students.id = student), teacher, (SELECT name FROM teachers WHERE teachers.id = teacher), created, due, started, completed, activity, remarks FROM tasks WHERE student IN (SELECT student FROM class_students WHERE class = ?);");
            $classId = $schoolClass->getId();
            $stmt->bind_param("i", $classId);
            $stmt->execute();
            $stmt->bind_result($taskId, $taskStudent, $taskStudentName, $taskTeacher, $taskTeacherName, $taskCreated, $taskDue, $taskStarted, $taskCompleted, $taskActivity, $taskRemarks);
            while($stmt->fetch()) {
                $tasks[] = ["id" => $taskId, "student" => ["id" => $taskStudent, "name" => $taskStudentName], "teacher" => ["id" => $taskTeacher, "name" => $taskTeacherName], "created" => $taskCreated, "due" => $taskDue, "started" => $taskStarted, "completed" => $taskCompleted, "activity" => $taskActivity, "remarks" => $taskRemarks];
            }
            $stmt->close();
            die(json_encode($tasks));
        }
        if($request["type"] != "PUT" && $request["type"] != "DELETE") renderError("Unable to route request", 405);
        validate([$request["sliced"][3] => "string", $request["sliced"][4] => "int"]);

        if($request["type"] == "PUT") {
            if($request["sliced"][3] == "student") {
                $members = $schoolClass->getStudents($conn);
                foreach($members as $member) if($member->getId() == $request["sliced"][4]) renderError("Student is already in class");
                $schoolClass->addStudent($conn, $request["sliced"][4]);
                die("{}");
            }
            if($request["sliced"][3] == "teacher") {
                $members = $schoolClass->getTeachers($conn);
                foreach($members as $member) if($member->getId() == $request["sliced"][4]) renderError("Teacher is already in class");
                $schoolClass->addTeacher($conn, $request["sliced"][4]);
                die("{}");
            }
        }
        if($request["type"] == "DELETE") {
            if($request["sliced"][3] == "student") {
                $members = $schoolClass->getStudents($conn);
                foreach($members as $member) if($member->getId() == $request["sliced"][4]) {
                    $schoolClass->deleteStudent($conn, $request["sliced"][4]);
                    die("{}");
                }
                renderError("Nothing to delete");
            }
            if($request["sliced"][3] == "teacher") {
                $members = $schoolClass->getTeachers($conn);
                foreach($members as $member) if($member->getId() == $request["sliced"][4]) {
                    $schoolClass->deleteTeacher($conn, $request["sliced"][4]);
                    die("{}");
                }
                renderError("Nothing to delete");
            }
        }
    }