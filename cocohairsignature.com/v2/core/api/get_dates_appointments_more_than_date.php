<?php
function api_get_dates_appointments_more_than_date(array $post)
{
    $u = array();
    if (!isset($post["dateTo"])) {
        return $u;
    }

    $tg = db::stmt("SELECT `date` FROM schedulee WHERE `date` >= '" . trim($post["dateTo"]) . "' AND `haspaid`='1' LIMIT 13;");
    $i = 0;
    while ($rr = mysqli_fetch_assoc($tg)) {
        $rd = DateTime::createFromFormat("Ymd", $rr["date"]);
        if ($rd instanceof DateTime) {
            $u[$i]["year"] = $rd->format("Y");
            $u[$i]["month"] = $rd->format("m");
            $u[$i]["day"] = $rd->format("j");
        }
        $i++;
    }

    return $u;
}
?>
