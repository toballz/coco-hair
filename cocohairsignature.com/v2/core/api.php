<?php
include_once("../config.php");
header('Content-Type: application/json');

require_once __DIR__ . "/api/select_time_for_date.php";
require_once __DIR__ . "/api/save_contacts_64e.php";

$u = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = "";
    if (isset($_POST["action"]) && is_string($_POST["action"]) && trim($_POST["action"]) !== "") {
        $action = trim($_POST["action"]);
    }

    switch ($action) {
        case "select_time_forDate": //used
            $u = api_select_time_for_date($_POST);
            break;

        case "save_contacts_64e": //used stripe
            $u = api_save_contacts_64e($_POST);
            break;

        default:
            $u = array(1 => "no action specified", 2 => $action);
            break;
    }
}

echo json_encode($u);
?>