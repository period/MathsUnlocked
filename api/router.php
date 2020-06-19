<?php
    header("Content-Type: application/json");

    function renderError($error, $code=400) {
        http_response_code($code);
        die(json_encode(array("message" => $error)));
    }

    $request["type"] = $_SERVER["REQUEST_METHOD"];
    $request["ip"] = $_SERVER["HTTP_CF_CONNECTING_IP"]; // I'm using Cloudflare and so I need to restore the original IP through this header
    $request["uri"] = $_SERVER["REQUEST_URI"];
    $request["sliced"] = explode("/", $_SERVER["REQUEST_URI"], 10);
    if($request["type"] != "GET") {
        $request["input"] = json_decode(file_get_contents("php://input"), true);
    }
    else $request["input"] = [];

    require_once("database.php");

    // TODO: Add routes here

    renderError("Unable to route request", 404);