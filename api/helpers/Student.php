<?php
    class Student {
        private $id = null;
        private $username = null;
        private $email = null;
        private $hash = null;
        private $name = null;

        public function __construct($userName, $data=array()) {
            $this->username = $userName;
            foreach($data as $key => $value) {
                $this->$key = $value;
            }
        }

        public function export() {
            return ["id" => $this->getId(), "username" => $this->getUsername(), "email" => $this->getEmail(), "name" => $this->getName()];
        }

        public function load($conn) {
            $stmt = $conn->prepare("SELECT id, username, email, hash, name FROM students WHERE username = ?;");
            $stmt->bind_param("s", $this->username);
            $stmt->execute();
            $stmt->bind_result($this->id, $this->username, $this->email, $this->hash, $this->name);
            $stmt->fetch();
            $stmt->close();
            return true;
        }
        public function save($conn) {
            $stmt = $conn->prepare("UPDATE students SET username = ?, email = ?, hash = ?, name = ? WHERE id = ?;");
            $stmt->bind_param("ssssi", $this->username, $this->email, $this->hash, $this->name, $this->id);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        public function create($conn) {
            $stmt = $conn->prepare("INSERT INTO students VALUES (null, ?, ?, ?, ?);");
            $stmt->bind_param("ssss", $this->username, $this->email, $this->hash, $this->name);
            $res = $stmt->execute();
            $this->id = $stmt->insert_id;
            $stmt->close();
            return $res;
        }

        public function getId() {
            return $this->id;
        }
        public function setId($id) {
            $this->id = $id;
        }
        public function getUsername() {
            return $this->username;
        }
        public function setUsername($username) {
            $this->username = $username;
        }
        public function getEmail() {
            return $this->email;
        }
        public function setEmail($email) {
            $this->email = $email;
        }
        public function getHash() {
            return $this->hash;
        }
        public function setHash($hash) {
            $this->hash = $hash;
        }
        public function getName() {
            return $this->name;
        }
        public function setName($name) {
            $this->name = $name;
        }
    }