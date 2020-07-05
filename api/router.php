<?php
    header("Content-Type: application/json");

    function renderError($error, $code=400) {
        http_response_code($code);
        die(json_encode(array("message" => $error)));
    }

    function validate($params) {
        foreach($params as $key => $value) {
            $validators = explode("|", $value);
            if(is_null($key)) renderError("Field being validated is null");
            foreach($validators as $validator) {
                if($validator == "username") {
                    if(strlen($key) < 2 || strlen($key) > 32 || !ctype_alnum(str_replace("_", "", $key))) renderError("Invalid username (alphanumeric, 2-32 chars)");
                }
                else if($validator == "bool" || $validator == "boolean") {
                    if($key != true && $key != false) renderError("Expecting boolean but got something else");
                }
                else if($validator == "string") {
                    if(empty($key) || !is_string($key) && !is_numeric($key)) renderError("Expecting string but got something else for " . $key);
                }
                else if($validator == "alnum") {
                    if(!ctype_alnum($key)) renderError("Field must be alphanumeric");
                }
                else if($validator == "alnumspace") {
                    if(!ctype_alnum(str_replace(" ", "", $key))) renderError("Field must be alphanumeric");
                }
                else if($validator == "int") {
                    if(!is_int($key)) renderError("Expecting int but got something else");
                }
                else if($validator == "password") {
                    if(strlen($key) < 3 || strlen($key) > 128) renderError("Password too long or too short");
                }
                else if($validator == "email") {
                    if(!filter_var($key, FILTER_VALIDATE_EMAIL)) renderError("Invalid email");
                }
                else renderError("Unknown validation check (" .$validator.")");
            }
        }
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

    if($request["sliced"][1] == "auth") {
        require_once("routes/auth.php");
    }

    renderError("Unable to route request", 404);