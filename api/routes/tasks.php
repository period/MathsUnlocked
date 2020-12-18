<?php
    require_once("/var/www/mathsunlocked/api/middleware/auth.php");

    require_once("/var/www/mathsunlocked/api/helpers/Task.php");
    require_once("/var/www/mathsunlocked/api/helpers/TaskQuestion.php");
    require_once("/var/www/mathsunlocked/api/helpers/Student.php");
    require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
    require_once("/var/www/mathsunlocked/api/helpers/School.php");

    if($request["sliced"][2] == "questions") {
        validate([$request["sliced"][3] => "int", $request["input"]["answer"] => "int"]);
        $question = new TaskQuestion($request["sliced"][3]);
        $question->load($conn);

        $task = new Task($question->getTask());
        $task->load($conn);
        if(empty($task->getActivity())) renderError("No such task", 404);
        if(($auth["scope"] == "student" && $task->getStudent() != $auth["user_id"]) || ($auth["scope"] == "teacher" && $task->getTeacher() != $auth["user_id"])) renderError("User not authenticated", 403);
    
        if($task->getCompleted() != null) renderError("Task has already been checked out");

        $data = $question->getData();
        $data["submitted_answer"] = $request["input"]["answer"];
        $question->setData($data);
        $question->save($conn);
        die("{}");
    }

    validate([$request["sliced"][2] => "int"]);
    $task = new Task($request["sliced"][2]);
    $task->load($conn);
    if(empty($task->getActivity())) renderError("No such task", 404);
    if(($auth["scope"] == "student" && $task->getStudent() != $auth["user_id"]) || ($auth["scope"] == "teacher" && $task->getTeacher() != $auth["user_id"])) renderError("User not authenticated", 403);

    $student = new Student($task->getStudent());

    if($request["type"] == "DELETE") {
        if($auth["scope"] != "teacher") renderError("Teacher scope required", 403);
        if($task->getCompleted() != null) renderError("Cannot delete task as is marked as completed");
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?;");
        $stmt->bind_param("i", $task->getId());
        $stmt->execute();
        $stmt->close();
        die("{}");
    }

    if($request["type"] == "GET") {
        $response = $task->export();
        $response["questions"] = [];
        $questions = $task->getQuestions($conn);
        $i = 1;
        foreach($questions as $question) {
            $qdata = $question->export();
            if($response["completed"] == null) $qdata["data"]["answer"] = null; // completed is null, so not checked out, so don't leak answer
            $qdata["position"] = $i;
            $i++;
            $response["questions"][] = $qdata;
        }

        if($auth["scope"] == "student" && $task->getStarted() == null) {
            $task->setStarted(time());
            $task->save($conn);
        }

        die(json_encode($response));
    }
    if($request["type"] == "POST") {
        if($task->getCompleted() != null) renderError("Task has already been checked out");
        $questions = $task->getQuestions($conn);
        $pointsEarned = 0;

        foreach($questions as $question) {
            if(!isset($question->getData()["submitted_answer"]) || $question->getData()["submitted_answer"] == null) renderError("Cannot checkout a task that is missing answers");
            if($question->mark($question->getData()["submitted_answer"]) == true) $pointsEarned += 2;
        }
        $student->addPoints($conn, $pointsEarned, "Completion of task #" .$task->getId());
        $task->setCompleted(time());
        $task->save($conn);

        die(json_encode(["points_earned" => $pointsEarned]));
    }