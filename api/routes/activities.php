<?php
    require_once("/var/www/mathsunlocked/api/middleware/auth.php");

    if($request["type"] == "GET") {
        $data = [];
        $categoriesQuery = $conn->query("SELECT id, name FROM activity_categories;");
        while($category = $categoriesQuery->fetch_assoc()) {
            $data[$category["id"]] = ["name" => $category["name"], "activities" => []];
        }
        $activitiesQuery = $conn->query("SELECT id, name, category, (SELECT COUNT(*) FROM activity_questions WHERE activity = activities.id) AS `questions` FROM activities;");
        while($activity = $activitiesQuery->fetch_assoc()) {
            $data[$activity["category"]]["activities"][] = ["id" => $activity["id"], "name" => $activity["name"], "questions" => $activity["questions"]];
        }
        die(json_encode($data));
    }