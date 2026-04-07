<?php
function api_get_override_dates(array $post)
{
    $tg = db::stmt("SELECT `description` FROM `availability` WHERE `namer`='override';");
    $row = mysqli_fetch_assoc($tg);
    if (!is_array($row) || !isset($row["description"])) {
        return array();
    }

    $decoded = json_decode($row["description"]);
    return ($decoded !== null) ? $decoded : array();
}
?>
