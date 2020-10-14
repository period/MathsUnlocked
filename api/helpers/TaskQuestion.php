<?php
    class TaskQuestion {
        private $id = null;
        private $task = null;
        private $data = null;
        private $correct = 0;

        public function load($conn) {
            $stmt = $conn->prepare("SELECT task, data, correct FROM task_questions WHERE id = ?;");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $stmt->bind_result($questionTask, $questionData, $questionCorrect);
            $stmt->fetch();
            $stmt->close();

            $this->task = $questionTask;
            $this->data = $questionData;
            $this->correct = $questionCorrect;
        }
        public function save($conn) {
            $stmt = $conn->prepare("UPDATE task_questions SET data = ?, correct = ? WHERE id = ?;");
            $stmt->bind_param("sii", $this->data, $this->correct, $this->id);
            $stmt->execute();
            $stmt->close();
        }
        public function mark($answer) {
            return $this->getData()["answer"]["value"] == $answer;
        }

        public function getId() {
            return $this->id;
        }
        public function getTask() {
            return $this->task;
        }
        public function getData() {
            return json_decode($this->data, true);)
        }
        public function getDataRaw() {
            return $this->data;
        }
        public function getCorrect() {
            return $this->correct == 1;
        }
        public function setData($data) {
            $this->data = json_encode($data);
        }
        public function setDataRaw($data) {
            $this->data = $data;
        }
        public function setCorrect($isCorrect) {
            if($isCorrect == true) $this->correct = 1;
            else $this->correct = 0;
        }
    }