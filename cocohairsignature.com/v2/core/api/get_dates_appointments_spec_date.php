<?php
function api_get_dates_appointments_spec_date(array $post)
{
    $u = array();
    if (!isset($post["dateFrom"])) {
        return $u;
    }

    $tg = db::stmt("SELECT `hairstyle`,`image`,`hairstyle`,`rida`,`date`,`time` FROM schedulee WHERE `date` = '" . $post["dateFrom"] . "' AND `haspaid`='1';");
    $i = 0;
    while ($rr = mysqli_fetch_assoc($tg)) {
        $rd = DateTime::createFromFormat("Ymd", $rr["date"]);
        $formattedDate = ($rd instanceof DateTime) ? $rd->format("Y F, l jS") : $rr["date"];
        $u[$i]["imageUrl"] = site::url_hostdir() . "/img/" . $rr["image"] . ".jpg?93jv";
        $u[$i]["datetime"] = $formattedDate . " " . $rr["time"];
        $u[$i]["hairname"] = $rr["hairstyle"];
        $u[$i]["orderId"] = $rr["rida"];
        $i++;
    }

    return $u;
}
?>
