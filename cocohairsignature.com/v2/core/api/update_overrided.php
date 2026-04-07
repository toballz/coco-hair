<?php
function api_update_overrided(array $post)
{
    if (!isset($post["cat"]) || $post["cat"] === "") {
        return array();
    }

    $cat4 = trim($post["cat"]);
    db::stmt("UPDATE `availability` SET `description` = '$cat4' WHERE `id` = '3' AND `namer`='override';");

    return array(
        "code" => 200,
        "message" => "ok"
    );
}
?>
