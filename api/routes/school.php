<?php
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    require_once("/var/www/mathsunlocked/api/helpers/School.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/SchoolClass.php");

    require_once("/var/www/mathsunlocked/api/vendor/autoload.php");
    require_once("/var/www/mathsunlocked/api/jwt-secret.php");
    use \Firebase\JWT\JWT;

    if($request["type"] == "PUT" && !isset($request["sliced"][2])) {
        validate([
            $request["input"]["name"] => "string|alnumspace",
            $request["input"]["administrator"]["name"] => "string|name",
            $request["input"]["administrator"]["username"] => "string|username",
            $request["input"]["administrator"]["password"] => "string|password",
            $request["input"]["administrator"]["email"] => "string|email"
        ]);

        $school = new School(null);
        $school->setName($request["input"]["name"]);
        $school->setOwner(-1); // Not created teacher account yet, so set owner to -1 (impossible to have a user with this id)
        $school->create($conn);

        $teacher = new Teacher(null);
        $teacher->setSchool($school->getId());
        $teacher->setName($request["input"]["administrator"]["name"]);
        $teacher->setUsername($request["input"]["administrator"]["username"]);
        $teacher->setEmail($request["input"]["administrator"]["email"]);
        $teacher->setHash(password_hash($request["input"]["administrator"]["password"], PASSWORD_DEFAULT));
        $teacher->create($conn);

        $school->setOwner($teacher->getId());
        $school->save($conn);
        
        die(json_encode([
            "token" => JWT::encode(["iat" => time(), "nbf" => time(), "exp" => strtotime("+7 days"), "user" => $teacher->getUsername(), "name" => $teacher->getName(), "user_id" => $teacher->getId(), "scope" => "teacher"], $jwtSecret),
            "school" => $school->export()
        ]));
    }
    if(isset($request["sliced"][2])) {
        validate([$request["sliced"][2] => "int"]);
        require_once("/var/www/mathsunlocked/api/middleware/auth.php");
        $school = new School($request["sliced"][2]);
        $school->load($conn);
        if($school->getName() == null) renderError("No such school with id", 404);
        if($auth["scope"] != "teacher") renderError("Teacher scope required", 403);

        $teacher = new Teacher($auth["user_id"]);
        $teacher->load($conn);
        if($teacher->getSchool() != $request["sliced"][2]) renderError("Teacher does not belong to school");

        if(!isset($request["sliced"][3]) && $request["type"] == "GET") die(json_encode($school->export()));
        if(!isset($request["sliced"][3]) && $request["type"] == "PATCH") {
            validate([$request["input"]["name"] => "string|alnumspace"]);
            if($auth["user_id"] != $school->getOwner()) renderError("You need to be the administrator of the school to create students", 403);

            if(isset($request["input"]["name"])) $school->setName($request["input"]["name"]);

            $school->save($conn);
            die(json_encode($school->export()));
        }

        if($request["sliced"][3] == "students") {
            if($request["type"] == "PUT") {
                validate([$request["input"] => "array"]);
                if($auth["user_id"] != $school->getOwner()) renderError("You need to be the administrator of the school to create students", 403);
                $studentsCreated = [];
                foreach($request["input"] as $studentToImport) {
                    validate([
                        $studentToImport["name"] => "string|name",
                        $studentToImport["email"] => "string|email"
                    ]);
                    $student = new Student(null);
                    $student->setSchool($school->getId());
                    $student->setName($studentToImport["name"]);
                    $student->setEmail($studentToImport["email"]);
                    $student->setHash("NOT_SET");
                    $student->setUsername($school->getId().strtoupper($studentToImport["name"])[0].explode(" ",$studentToImport["name"])[1].rand(0,99));
                    $student->create($conn);
                    $studentsCreated[] = $student->export();
                }
                die(json_encode($studentsCreated));
            }
            if($request["type"] == "GET") {
                $students = [];
                $stmt = $conn->prepare("SELECT id, school, username, email, name, (SELECT SUM(amount) FROM points WHERE student = id AND timestamp > UNIX_TIMESTAMP()-(86400*7)) FROM students WHERE school = ?;");
                $stmt->bind_param("i", $school->getId());
                $stmt->execute();
                $stmt->bind_result($studentId, $studentSchool, $studentUsername, $studentEmail, $studentName, $pointsLastWeek);
                while($stmt->fetch()) $students[] = ["id" => $studentId, "school" => $studentSchool, "username" => $studentUsername, "email" => $studentEmail, "name" => $studentName, "points_last_week" => $pointsLastWeek];
                $stmt->close();

                die(json_encode($students));
            }
        }
        if($request["sliced"][3] == "classes") {
            if($request["type"] == "PUT") {
                validate([$request["input"] => "array"]);
                if($auth["user_id"] != $school->getOwner()) renderError("You need to be the administrator of the school to create classes", 403);
                $classesCreated = [];
                $request["input"] = array_unique($request["input"]);
                foreach($request["input"] as $classToImport) {
                    validate([
                        $classToImport => "string",
                    ]);
                    if(strlen($classToImport) > 32) renderError("Cannot add " .$classToImport. " as name exceeds 32 characters");
                    $class = new SchoolClass(null);
                    $class->setName($classToImport);
                    $class->setSchool($school->getId());
                    $class->create($conn);
                    $classesCreated[] = $class->export();
                }
                die(json_encode($classesCreated));
            }
            if($request["type"] == "GET") {
                $classes = [];
                $stmt = $conn->prepare("SELECT id, name FROM classes WHERE school = ?;");
                $stmt->bind_param("i", $school->getId());
                $stmt->execute();
                $stmt->bind_result($classId, $className);
                while($stmt->fetch()) $classes[] = ["id" => $classId, "name" => $className];
                $stmt->close();

                die(json_encode($classes));
            }
        }
        if($request["sliced"][3] == "teachers") {
            if($request["type"] == "PUT") {
                validate([
                    $request["input"]["name"] => "string|name",
                    $request["input"]["username"] => "string|username",
                    $request["input"]["password"] => "string|password",
                    $request["input"]["email"] => "string|email"
                ]);

                if($auth["user_id"] != $school->getOwner()) renderError("You need to be the administrator of the school to create a teacher", 403);

                $teacher = new Teacher(null);
                $teacher->setSchool($school->getId());
                $teacher->setName($request["input"]["name"]);
                $teacher->setUsername($request["input"]["username"]);
                $teacher->setEmail($request["input"]["email"]);
                $teacher->setHash(password_hash($request["input"]["password"], PASSWORD_DEFAULT));
                $teacher->create($conn);

                die(json_encode($teacher->export()));
            }
            if($request["type"] == "GET") {
                $teachers = [];
                $stmt = $conn->prepare("SELECT id, school, username, email, name FROM teachers WHERE school = ?;");
                $stmt->bind_param("i", $school->getId());
                $stmt->execute();
                $stmt->bind_result($teacherId, $teacherSchool, $teacherUsername, $teacherEmail, $teacherName);
                while($stmt->fetch()) $teachers[] = ["id" => $teacherId, "school" => $teacherSchool, "username" => $teacherUsername, "email" => $teacherEmail, "name" => $teacherName];
                $stmt->close();

                die(json_encode($teachers));
            }
        }
    }