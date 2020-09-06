<?php
    class SchoolClass {
        private $id = null;
        private $name = null;
        private $school = null;

        public function __construct($id, $data=array()) {
            $this->id = $id;
            foreach($data as $key => $value) {
                $this->$key = $value;
            }
        }

        public function export() {
            return ["id" => $this->getId(), "school" => $this->getSchool(), "name" => $this->getName()];
        }
        
        public function load($conn) {
            $stmt = $conn->prepare("SELECT name, school FROM classes WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $stmt->bind_result($this->name, $this->school);
            $stmt->fetch();
            $stmt->close();
            return true;
        }
        public function save($conn) {
            $stmt = $conn->prepare("UPDATE classes SET name = ?, school = ? WHERE id = ?;");
            $stmt->bind_param("sis", $this->name, $this->school, $this->id);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        public function create($conn) {
            $stmt = $conn->prepare("INSERT INTO classes VALUES (null, ?, ?);");
            $stmt->bind_param("ss",  $this->name, $this->school);
            $res = $stmt->execute();
            $this->id = $stmt->insert_id;
            $stmt->close();
            return $res;
        }

        public function getStudents($conn) {
            $students = [];
            $stmt = $conn->prepare("SELECT id, school, username, email, name FROM students WHERE id IN (SELECT student FROM class_students WHERE class = ?);");
            $stmt->bind_param("i", $this->getId());
            $stmt->execute();
            $stmt->bind_result($tmpId, $tmpSchool, $tmpUsername, $tmpEmail, $tmpName);
            require_once("/var/www/mathsunlocked/api/helpers/Student.php");
            while($stmt->fetch()) $students[] = new Student($tmpUsername, ["id" => $tmpId, "school" => $tmpSchool, "email" => $tmpEmail, "name" => $tmpName]);
            $stmt->close();
            return $students;
        }
        public function addStudent($conn, $studentID) {
            $stmt = $conn->prepare("INSERT INTO class_students VALUES (?, ?);");
            $stmt->bind_param("ii", $this->id, $studentID);
            $stmt->execute();
            $stmt->close();
        }
        public function deleteStudent($conn, $studentID) {
            $stmt = $conn->prepare("DELETE FROM class_students WHERE class = ? AND student = ?;");
            $stmt->bind_param("ii", $this->id, $studentID);
            $stmt->execute();
            $stmt->close();
        }
        public function getTeachers($conn) {
            $teachers = [];
            $stmt = $conn->prepare("SELECT id, school, username, email, name FROM teachers WHERE id IN (SELECT teacher FROM class_teachers WHERE class = ?);");
            $stmt->bind_param("i", $this->getId());
            $stmt->execute();
            $stmt->bind_result($tmpId, $tmpSchool, $tmpUsername, $tmpEmail, $tmpName);
            require_once("/var/www/mathsunlocked/api/helpers/Teacher.php");
            while($stmt->fetch()) $teachers[] = new Teacher($tmpUsername, ["id" => $tmpId, "school" => $tmpSchool, "email" => $tmpEmail, "name" => $tmpName]);
            $stmt->close();
            return $teachers;
        }
        public function addTeacher($conn, $teacherID) {
            $stmt = $conn->prepare("INSERT INTO class_teachers VALUES (?, ?);");
            $stmt->bind_param("ii", $this->id, $teacherID);
            $stmt->execute();
            $stmt->close();
        }
        public function deleteTeacher($conn, $teacherID) {
            $stmt = $conn->prepare("DELETE FROM class_teachers WHERE class = ? AND teacher = ?;");
            $stmt->bind_param("ii", $this->id, $teacherID);
            $stmt->execute();
            $stmt->close();
        }

        public function getId() {
            return $this->id;
        }
        public function setId($id) {
            $this->id = $id;
        }
        public function getSchool() {
            return $this->school;
        }
        public function setSchool($school) {
            $this->school = $school;
        }
        public function getName() {
            return $this->name;
        }
        public function setName($name) {
            $this->name = $name;
        }
    }