<?php
function api_get_weekly_static(array $post)
{
    $u = array();
    $tg = db::stmt("SELECT `description` FROM `availability` WHERE `namer`='weekly' AND `id`='1';");
    while ($rr = mysqli_fetch_assoc($tg)) {
        $u = json_decode($rr["description"]);
    }

    return $u;
}
?>
