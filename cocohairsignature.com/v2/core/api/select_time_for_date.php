<?php
function api_select_time_for_date(array $post)
{  
    global $db;

    $u = array(1=>"no date specified");
    if (!isset($post["getDate"]) || $post["getDate"] === "") {
        return $u;
    }

    $thisDAte = trim($post["getDate"]);
    $normalizedDateKey = preg_replace("/[^0-9]/", "", $thisDAte);
    $parsedDate = null;

    if (preg_match("/^\d{8}$/", $normalizedDateKey)) {
        $parsedDate = DateTime::createFromFormat("Ymd", $normalizedDateKey);
    }

    if (!$parsedDate instanceof DateTime) {
        $timestamp = strtotime($thisDAte);
        if ($timestamp === false) {
            return $u;
        }
        $parsedDate = new DateTime();
        $parsedDate->setTimestamp($timestamp);
    }

    $thisDAte_dayInWeek = strtolower($parsedDate->format("l"));

    $tg1 = $db->query("SELECT `description` FROM `availability` WHERE `namer` = 'override' LIMIT 1");
    $overrideRow = mysqli_fetch_assoc($tg1);
    $overrided_fetch_assoc = json_decode(isset($overrideRow["description"]) ? $overrideRow["description"] : "[]");
    if (!is_array($overrided_fetch_assoc)) {
        $overrided_fetch_assoc = array();
    }

    $tg2 = $db->query("SELECT `time_scheduled` FROM `product_purchased` WHERE `date_scheduled` = '" . $thisDAte . "' AND `haspaid`='1';");
    $Persons_AlreadyBookedFot_thisDate = array();
    while ($lo = mysqli_fetch_assoc($tg2)) {
        $Persons_AlreadyBookedFot_thisDate[] = $lo["time_scheduled"];
    }

    $tg3 = $db->query("SELECT `description` FROM `availability` WHERE `namer` = 'weekly' LIMIT 1;");
    $weeklyRow = mysqli_fetch_assoc($tg3);
    $weeklyDesc = isset($weeklyRow["description"]) ? strtolower($weeklyRow["description"]) : "{}";
    $reqgularSchedule_fetch_assoc = json_decode($weeklyDesc);
    if (!is_object($reqgularSchedule_fetch_assoc) || !isset($reqgularSchedule_fetch_assoc->$thisDAte_dayInWeek)) {
        return $u;
    }

    $times_to_show_from_weekly = array_map("trim", explode(",", $reqgularSchedule_fetch_assoc->$thisDAte_dayInWeek));

    foreach ($overrided_fetch_assoc as $ovrrd) {
        $overrideDateKey = isset($ovrrd->date) ? preg_replace("/[^0-9]/", "", (string) $ovrrd->date) : "";
        if ($overrideDateKey !== "" && isset($ovrrd->time) && $overrideDateKey === $normalizedDateKey) {
            $times_to_show_from_weekly = array_map("trim", explode(",", $ovrrd->time));
            break;
        }
    }

    foreach ($times_to_show_from_weekly as $ki => $times) {
        if (empty($times)) {
            unset($times_to_show_from_weekly[$ki]);
            continue;
        }
        if (in_array($times, $Persons_AlreadyBookedFot_thisDate)) {
            foreach ($times_to_show_from_weekly as $k => $a) {
                if ($a == $times) {
                    unset($times_to_show_from_weekly[$k]);
                    break;
                }
            }
        }
    }

    return $times_to_show_from_weekly;
}
?>
