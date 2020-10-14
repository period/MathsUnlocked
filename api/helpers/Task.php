<?php
    require_once("/var/www/mathsunlocked/api/helpers/TaskQuestion.php");
    class Task {
        private $id = null;
        private $student = null;
        private $teacher = null;
        private $created = null;
        private $due = null;
        private $started = null;
        private $completed = null;
        private $activity = null;
        private $remarks = null;

        public function __construct($id, $data=array()) {
            $this->id = $id;
            foreach($data as $key => $value) {
            $this->$key = $value;
            }
        }

        public function export() {
            return ["id" => $this->id, "student" => $this->student, "teacher" => $this->teacher, "created" => $this->created, "due" => $this->due, "started" => $this->started, "completed" => $this->completed, "activity" => $this->activity, "remarks" => $this->remarks];
        }

        public function load($conn) {
            $stmt = $conn->prepare("SELECT student, teacher, created, due, started, completed, activity, remarks FROM tasks WHERE id = ?;");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $stmt->bind_result($this->student, $this->teacher, $this->created, $this->due, $this->started, $this->completed, $this->activity, $this->remarks);
            $stmt->fetch();
            $stmt->close();
        }
        public function save($conn) {
            $stmt = $conn->prepare("UPDATE tasks SET student = ?, teacher = ?, created = ?, due = ?, started = ?, completed = ?, activity = ?, remarks = ? WHERE id = ?;");
            $stmt->bind_param("iiiiiissi", $this->student, $this->teacher, $this->created, $this->due, $this->started, $this->completed, $this->activity, $this->remarks, $this->id);
            $stmt->execute();
            $stmt->close();
        }

        public function create($conn) {
            $stmt = $conn->prepare("INSERT INTO tasks VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("iiiiiiss", $this->student, $this->teacher, $this->created, $this->due, $this->started, $this->completed, $this->activity, $this->remarks);
            $res = $stmt->execute();
            $this->id = $stmt->insert_id;
            $stmt->close();
            return $res;
        }

        public function initialise($conn, $activityID) {
            if(sizeof($this->getQuestions($conn)) > 0) renderError("Task has already been initialised.");

            $stmt = $conn->prepare("SELECT snapshot FROM activity_questions WHERE activity = ?;");
            $stmt->bind_param("i", $activityID);
            $stmt->execute();
            $stmt->bind_result($snapshotRaw);
            while($stmt->fetch()) {
                $snapshot = json_decode($snapshotRaw, 1);
                // Initialise placeholders based upon the designated generator
                for($i = 0; $i < sizeof($snapshot["placeholders"]); $i++) {
                    // Random number generator
                    if($snapshot["placeholders"][$i]["generator"]["type"] == "random_number") {
                        $snapshot["placeholders"][$i]["value"] = rand($snapshot["placeholders"][$i]["generator"]["min"], $snapshot["placeholders"][$i]["generator"]["max"]);
                    }
                    // If other generators come to exist in the future, then add these here

                    // Null the generator for this placeholder as no longer required
                    $snapshot["placeholders"][$i]["generator"] = null;
                }

                // Re-encode the JSON
                $snapshotData = json_encode($snapshot);
                // Replace the placeholders. Doing this on the JSON string in case e.g. a question has more than just a title in the future
                foreach($snapshot["placeholders"] as $placeholder) {
                    $snapshotData = str_replace("[[PLACEHOLDER_" .$placeholder["name"]."]]", $placeholder["value"], $snapshotData);
                }
                // Decode and compute answer such that we only do this once
                $snapshot = json_decode($snapshotData)
                $snapshot["answer"]["value"] = eval($snapshot["answer"]["expression"]);
                // JSON representation of question is now complete with placeholders generated and replaced, insert into the task's questions
                $this->addQuestion($conn, json_encode($snapshot));

            }
            $stmt->close();
        }

        public function addQuestion($conn, $data) {
            $stmt = $conn->prepare("INSERT INTO task_questions VALUES (null, ?, ?, 0);");
            $stmt->bind_param("is", $this->id, $data);
            $stmt->execute();
            $stmt->close();
        }
        public function getQuestions($conn) {
            $questions = [];
            $stmt = $conn->prepare("SELECT id, data, correct FROM task_questions WHERE task = ?;");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $stmt->bind_result($questionId, $questionData, $questionCorrect);
            while($stmt->fetch()) {
                $questions[] = new TaskQuestion($questionId, ["data" => $questionData, "correct" => $questionCorrect)]);
            }
            return $questions;
        }

        public function getId() {
            return $this->id;
        }
        public function setId($id) {
            $this->id = $id;
        }
        public function getStudent() {
            return $this->student;
        }
        public function setStudent($student) {
            $this->student = $student;
        }
        public function getTeacher() {
            return $this->teacher;
        }
        public function setTeacher($teacher) {
            $this->teacher = $teacher;
        }
        public function getCreated() {
            return $this->created;
        }

        public function setCreated($created) {
            $this->created = $created;
        }
        public function getDue() {
            return $this->due;
        }
        public function setDue($due) {
            $this->due = $due;
        }
        public function getStarted() {
            return $this->started;
        }
        public function setStarted($started) {
            $this->started = $started;
        }
        public function getCompleted() {
            return $this->completed;
        }
        public function setCompleted($completed) {
            $this->completed = $completed;
        }
        public function getActivity() {
            return $this->activity;
        }
        public function setActivity($activity) {
            $this->activity = $activity;
        }
        public function getRemarks() {
            return $this->remarks;
        }
        public function setRemarks($remarks) {
            $this->remarks = $remarks;
        }
    }