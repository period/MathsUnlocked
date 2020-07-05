<?php
    class SchoolClass {
        private $id = null;
        private $name = null;
        private $owner = null;

        public function __construct($id, $data=array()) {
            $this->id = $id;
            foreach($data as $key => $value) {
                $this->$key = $value;
            }
        }

        public function export() {
            return ["id" => $this->getId(), "owner" => $this->getOwner(), "name" => $this->getName()];
        }
        
        public function load($conn) {
            $stmt = $conn->prepare("SELECT name, owner FROM schools WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $stmt->bind_result($this->name, $this->owner);
            $stmt->fetch();
            $stmt->close();
            return true;
        }
        public function save($conn) {
            $stmt = $conn->prepare("UPDATE schools SET name = ?, owner = ? WHERE id = ?;");
            $stmt->bind_param("sis", $this->name, $this->owner, $this->id);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        public function create($conn) {
            $stmt = $conn->prepare("INSERT INTO schools VALUES (null, ?, ?);");
            $stmt->bind_param("ss",  $this->name, $this->owner);
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
        public function getName() {
            return $this->name;
        }
        public function setName($name) {
            $this->name = $name;
        }
        public function getOwner() {
            return $this->owner;
        }
        public function setOwner($owner) {
            $this->owner = $owner;
        }
    }