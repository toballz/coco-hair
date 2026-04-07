<?php
include_once("../config.php");
header('Content-Type: application/json');

require_once __DIR__ . "/api/get_dates_appointments_spec_date.php";
require_once __DIR__ . "/api/get_dates_appointments_more_than_date.php";
require_once __DIR__ . "/api/get_weekly_static.php";
require_once __DIR__ . "/api/updates_weekly.php";
require_once __DIR__ . "/api/receipt_ii_info.php";
require_once __DIR__ . "/api/get_override_dates.php";
require_once __DIR__ . "/api/stats.php";
require_once __DIR__ . "/api/select_time_for_date.php";
require_once __DIR__ . "/api/save_contacts_64e.php";
require_once __DIR__ . "/api/update_overrided.php";
require_once __DIR__ . "/api/delete_appointment.php";
require_once __DIR__ . "/api/get_message_notifiy.php";
require_once __DIR__ . "/api/logine.php";
require_once __DIR__ . "/api/subscribed.php";

$u = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = "";
    if (isset($_POST["action"]) && is_string($_POST["action"]) && trim($_POST["action"]) !== "") {
        $action = trim($_POST["action"]);
    }

    switch ($action) {
        case "select_time_forDate":
            $u = api_select_time_for_date($_POST);
            break;
        case "selectdatetime":
            $u = [];
            break;
            
        case "getDatesAppointmentsSpecDate":
            $u = api_get_dates_appointments_spec_date($_POST);
            break;
        case "getDatesAppointmentsMoreThanDate":
            $u = api_get_dates_appointments_more_than_date($_POST);
            break;
        case "getweeklyStatic":
            $u = api_get_weekly_static($_POST);
            break;
        case "updatesWeekly":
            $u = api_updates_weekly($_POST);
            break;
        case "receiptIIinfo":
            $u = api_receipt_ii_info($_POST);
            break;
        case "getOverrideDates":
            $u = api_get_override_dates($_POST);
            break;
        case "stats":
            $u = api_stats($_POST);
            break;
        case "save_contacts_64e":
            $u = api_save_contacts_64e($_POST);
            break;
        case "updateOverrided":
            $u = api_update_overrided($_POST);
            break;
        case "deleteAppointment":
            $u = api_delete_appointment($_POST);
            break;
        case "get_messageNotifiy":
            $u = api_get_message_notifiy($_POST);
            break;
        case "logine":
            $u = api_logine($_POST);
            break;
        case "subscribed":
            $u = api_subscribed($_POST);
            break;

        default:
            $u = array(1 => "no action specified", 2 => $action);
            break;
    }
}

echo json_encode($u);
?>